<?php

namespace App\Controllers;

use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;
use App\Models\PlaceModel;
use App\Models\UserModel;
use App\Models\TeamModel;
use App\Models\TeamMateModel;
use App\Models\AttendanceModel;
use App\Models\FacilityModel;
use App\Models\TaskModel;
use App\Models\TaskPlanModel;
use App\Models\TeamSafePointModel;

class Home extends ResourceController
{
    protected $modelName = 'App\Models\UserModel';
    protected $format    = 'json';

	protected $auth;

	public function __construct()
	{
		$this->auth = service('Authentication');
	}

	function respond($data = null, ?int $status = null, string $message = '')
	{
		if($status == null || $status == 200 || $status == 201) {

			$resp = [
				"status" => $status == null ? 200 : $status,
				"results" => $data
			];
			return parent::respond($resp, $status, $message);

		} else return parent::respond($data, $status, $message);
	}

	/*----------------------------------------출퇴근기준시간----------------------------------------*/

	function setTime() {

		$HOUR = 5;		//기준시
		$MINUTE = 0;	//기준분

		//$now = Time::now();
		if(Time::now() <= Time::createFromTime($HOUR, $MINUTE, 0)) {

			return Time::now()->yesterday()->setHour($HOUR)->setMinute($MINUTE)->setSecond(0);

		} else {

			return Time::now()->setHour($HOUR)->setMinute($MINUTE)->setSecond(0);
			
		}
	}

	/*-----------------------------------------로그인관련-----------------------------------------*/

    public function index()
    {
		$place_id = $_POST['place_id'] ?? null;
		$username = $_POST['username'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

		if(is_null($username) || $username == '' || is_null($birthday)) {
			return $this->failValidationError();
		}
		
		$FacilityModel = new FacilityModel();

		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		}
		
		if($this->auth->login($place_id, $username, $birthday)) {

			$PlaceModel = new PlaceModel();
			$place = $PlaceModel->where('id', $this->auth->user()['place_id'])->first();

			$TeamMateModel = new TeamMateModel();
			$teammate = $TeamMateModel->where('name', $this->auth->user()['username'])->where('birthday', $this->auth->user()['birthday'])->first();

			return $this->respond([
				'place_id' => $place['id'] ?? "",
				'place_name' => $place['name'] ?? "",
				'team_id' => $teammate['team_id'] ?? "",
				'user_name' => $this->auth->user()['username'],
				'level' => intval($this->auth->level())
			])->setcookie("jwt_token", $this->auth->createJWT(), 86500);
		
		} else if($place_id != '' && !is_null($FacilityModel->like('super_manager', $username)->first())) {

			$PlaceModel = new PlaceModel();
			$place = $PlaceModel->where('id', $place_id)->first();
			
			return $this->respond([
				'place_id' => $place['id'] ?? "",
				'place_name' => $place['name'] ?? "",
				'team_id' => null,
				'user_name' => $username,
				'level' => -1
			])->setcookie("jwt_token", $this->auth->createJWT(true, $username), 86500);

		} else {
			return $this->failForbidden();
		}
    }

	public function auth_check() {

		if($this->auth->is_logged_in()) {
			return $this->respond(true);
		} else {
			return $this->failUnauthorized();
		}

	}

	/*------------------------------------------현장관련------------------------------------------*/

	public function place() {

		$PlaceModel = new PlaceModel();
		
		$_auth_check = $_POST['auth_check'] ?? 'false';
		$auth_check = $_auth_check == 'true'? true: false;

		if($auth_check && !$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		if(is_null($PlaceModel->first())) {
			return $this->failNotFound();
		}
		
		$places = $PlaceModel->findAll();

		return $this->respond($places);
	}

	public function place_add() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_name = $_POST['place_name'] ?? null;

		if(is_null($place_name)) {
			return $this->failValidationError();
		}

		$PlaceModel = new PlaceModel();

		try {
			$insert_name = $PlaceModel->insert([
				'name' => $place_name
			]);

			if(is_null($insert_name)) {
				return $this->failServerError();
			} else {
				return $this->respondCreated();
			}

		} catch(\Exception $e) {
			return $this->failValidationError($e->getMessage());
		}
	}

	public function place_edit() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$place_name = $_POST['place_name'] ?? null;

		if(is_null($place_name)) {
			return $this->failValidationError();
		}

		$PlaceModel = new PlaceModel();

		if(!is_null($place_id)) {
			$PlaceModel->where('id', $place_id);
		} else {
			return $this->failForbidden();
		}

		$PlaceModel->set('name', $place_name);

		$success = true;

		try {

			$success = $PlaceModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	/*----------------------------------------유저정보관련----------------------------------------*/

	public function user() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$place_id = $_POST['place_id'] ?? null;

		$UserModel = new UserModel();
		$UserModel->where('level !=', 4);
				
		if($place_id != null) {
			$UserModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		$user = $UserModel->findAll();
		
		return $this->respond($user);
	}

	public function user_add() {

		$place_id = $_POST['place_id'] ?? null;
		$username = $_POST['username'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

		if(is_null($place_id) || is_null($username) || is_null($birthday)) {
			return $this->failValidationError();
		}

		$PlaceModel = new PlaceModel();
		$UserModel = new UserModel();

		if(is_null($PlaceModel->where('id', $place_id)->first())) {
			return $this->failValidationError();
		}

		if(!is_null($UserModel->where('place_id', $place_id)->where('username', $username)->where('birthday', $birthday)->first())) {
			return $this->failResourceExists();
		}

		try {

			$insert_id = $UserModel->insert([
				'place_id' => $place_id,
				'username' => $username,
				'birthday' => $birthday
			]);

			if(is_null($insert_id)) {
				return $this->failServerError();
			} else {
				return $this->respondCreated();
			}

		} catch(\Exception $e) {
			return $this->failValidationError($e->getMessage());
		}
	}

	public function user_edit_level() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$id = $_POST['id'] ?? null;
		$level = $_POST['level'] ?? null;
		
		if($id == null) {
			return $this->failValidationError();
		}
		
		$UserModel = new UserModel();

		if($place_id != null) {
			$UserModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		$UserModel->where('id', $id);
		$UserModel->set('level', $level);

		$success = true;

		try {

			$success = $UserModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}
	
	public function user_edit_password() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		$password = $_POST['password'] ?? null;

		if($password == null) {
			return $this->failValidationError();
		}

		$UserModel = new UserModel();


		$UserModel->where('id', $this->auth->user()['id']);
		$UserModel->set('birthday', $password);

		$success = true;

		try {

			$success = $UserModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function user_delete() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$id = $_POST['id'] ?? null;

		if($id == null) {
			return $this->failValidationError();
		}

		$UserModel = new UserModel();

		if($place_id != null) {
			$UserModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		$success = true;

		try {

			$success = $UserModel->delete($id, true);

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	/*-------------------------------------------팀관련-------------------------------------------*/

	public function team() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$team_id = $_POST['team_id'] ?? null;

		$TeamModel = new TeamModel();

		if(!is_null($place_id)) {
			$TeamModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		if(!is_null($team_id) && $team_id != '') {
			$teams = $TeamModel->orderBy('name', 'ASC')->where('id !=', $team_id)->findAll();
		} else {
			$teams = $TeamModel->orderBy('name', 'ASC')->findAll();
		}

		if($teams == null) {
			return $this->failNotFound();
		}

		return $this->respond($teams);
	}

	/*-----------------------------------------출퇴근관련-----------------------------------------*/

	/*
    public function attendance_on() {
		
		if(!$this->auth->is_logged_in()){
			return $this->failForbidden();
		}

		$AttendanceModel = new AttendanceModel();

		$now = Time::now();

		$st = null;

		if($now <= Time::createFromTime(5, 0, 0)) {
			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);
		} else {
			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);
		}

		$user_id = $this->auth->user_id();

		$attendances = $AttendanceModel->where('created_at >', $st)->where('user_id', $user_id)->findAll();

		if(count($attendances) > 0) {
			return $this->failValidationError();
		}

		$AttendanceModel->insert(['user_id' => $this->auth->user_id(), 'type' => TYPE_ATTENDANCE_ON]);

		return $this->respondCreated();
	}
	
    public function attendance_off() {
		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$AttendanceModel = new AttendanceModel();

		$now = Time::now();

		$st = null;

		if($now <= Time::createFromTime(5, 0, 0)) {
			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);
		} else {
			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);
		}

		$user_id = $this->auth->user_id();

		$attendances = $AttendanceModel->where('created_at >', $st)->where('user_id', $user_id)->findAll();

		if(count($attendances) > 0) {
			return $this->failValidationError();
		}

		$AttendanceModel->insert(['user_id' => $this->auth->user_id(), 'type' => TYPE_ATTENDANCE_OFF]);

		return $this->respondCreated();
	}
	*/

	public function attendance() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$TeamModel = new TeamModel();
		$TeamMateModel = new TeamMateModel();

		$team_id = $_POST['team_id'];

		if(is_null($team_id) || is_null($TeamModel->where('id', $team_id)->first())) {
			return $this->failValidationError();
		}

		$attendances = $TeamMateModel->select('tm.id as id, tm.name as name, tm.birthday as birthday, a.created_at as date, a.type as type')
									->orderBy('tm.name', 'ASC')
                            	  	->distinct()
                          		  	->from('teammate as tm')
                         	     	->join('attendance as a', '(a.teammate_id = tm.id and a.created_at >"'.$this->setTime()->toDateTimeString().'")', 'left outer')
                          	    	->where('tm.team_id', $team_id)
                              		->findAll();
		
		/*
		$sortArr = array();
		foreach($attendances as $value) {
			$sortArr[] = $value['name'];
		}
		array_multisort($attendances, SORT_ASC, $sortArr);
		*/
		return $this->respond($attendances);

	}

	public function attendance_add() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$TeamMateModel = new TeamMateModel();
		$AttendanceModel = new AttendanceModel();

		$teammate_id = $_POST['teammate_id'] ?? null;
		$type = $_POST['type'] ?? null;

		if(is_null($teammate_id) || is_null($type)) {
			return $this->failValidationError();
		}
		if(is_null($TeamMateModel->where('id', $teammate_id)->first())) {
			return $this->failValidationError();
		}
		if($type < 0 || $type > 1) {
			return $this->failValidationError();
		}

		$insert_id = $AttendanceModel->insert([
			'teammate_id' => $teammate_id,
			'type' => $type
		]);

		if(is_null($insert_id)) {
			return $this->failServerError();
		} else {
			return $this->respondCreated();
		}

	}

	public function attendance_edit() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$AttendanceModel = new AttendanceModel();

		$teammate_id = $_POST['teammate_id'] ?? null;
		$type = $_POST['type'] ?? null;
		$date = $_POST['date'] ?? null;

		if(is_null($teammate_id) || is_null($type) || is_null($date)) {
			return $this->failValidationError();
		}
		if($type < 0 || $type > 1) {
			return $this->failForbidden();
		}
		
		$AttendanceModel->where('created_at >', $this->setTime())
						->where('teammate_id', $teammate_id)
						->where('type', $type)
						->set('created_at', $date)
						->update();

		return $this->respondUpdated();

	}

	public function attendance_edit_team() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$user_id = $_POST['user_id'] ?? null;
		$team_id = $_POST['team_id'] ?? null;

		$UserModel = new UserModel();
		$TeamMateModel = new TeamMateModel();
		$TeamModel = new TeamModel();

		$teammate = $TeamMateModel->where('id', $user_id)->first();
		$new_team = $TeamModel->where('id', $team_id)->first();
		$user = $UserModel->where('username', $teammate['name'])->first();

		if(is_null($new_team)) {
			return $this->failValidationError();
		}

		if(!is_null($user) && $user['place_id'] != $new_team['place_id']) {

			$UserModel->where('username', $teammate['name'])
						->set('place_id', $new_team['place_id'])
						->update();
		}

		$TeamMateModel->where('id', $user_id)
					->set('team_id', $team_id)
					->update();
		
		return $this->respondUpdated();

	}
	/*
	public function teammates() {
		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$TeamMateModel = new TeamMateModel();

		$team_id = $_POST['team_id'] ?? null;

		$teammates = $TeamMateModel->where('team_id', $team_id)
									->findAll();

		return $this->respond($teammates);
	}
	*/

	/*-----------------------------------------시설물관련-----------------------------------------*/
	
	public function facility() {

		if(!$this->auth->is_logged_in(true) && !$this->auth->is_logged_in(false)) {
			return $this->failUnauthorized();
		}

		$facility_id = $_POST['facility_id'] ?? null;
		$team_id = $_POST['team_id'] ?? null;

		if($facility_id == null) {
			return $this->failValidationError();
		}

		$FacilityModel = new FacilityModel();
		$AttendanceModel = new AttendanceModel();
		$TaskPlanModel = new TaskPlanModel();

		$facility_o_serial = $FacilityModel->where('id', $facility_id)->first()['o_serial'] ?? null;

		/*
		if($this->auth->is_logged_in(true)) {
			$FacilityModel->like('super_manager', $this->auth->supermanager());
		}
		*/
		if($facility_o_serial == null) {
			return $this->failForbidden();
		}

		//팀장님
		if($team_id != null) {
			//$teammate = $TeamMateModel->where('team_id', $team_id)->countAllResults();

			$attendance = $AttendanceModel->select('teammate.name as name')
											->join('teammate', 'attendance.teammate_id = teammate.id and teammate.team_id = "' . $team_id . '"')
											->where('attendance.type', '0')
											->where('attendance.created_at >', $this->setTime()->toDateTimeString())
											->countAllResults();
		
			$facility = $FacilityModel->select('facility.*, taskplan.type as taskplan_type, taskplan.team_id as taskplan_team_id')
									->join('taskplan', 'facility.o_serial = taskplan.facility_serial', 'left outer')
									->where('facility.id', $facility_id)
									->first();

			$facility['attendance'] = $attendance;

		//관리자
		} else if(!is_null($TaskPlanModel->where('facility_serial', $facility_o_serial)->first())) {

			$facility = $FacilityModel->select('facility.*, taskplan.type as taskplan_type, taskplan.team_id as taskplan_team_id')
									->join('taskplan', 'facility.o_serial = taskplan.facility_serial', 'left outer')
									->where('facility.id', $facility_id)
									->first();

		} else {

			$facility = $FacilityModel->where('facility.id', $facility_id)->first();

		}

		if($facility == null) {
			return $this->failNotFound();
		}

		return $this->respond($facility);

	}
	
	private function sort_array($array) {
		sort($array);
		return $array;
	}

	private function sort_floor_array($array) {
		usort($array, function($a, $b) {

			$aa = intval(rtrim($a, 'F'));
			$bb = intval(rtrim($b, 'F'));

			if($aa == $bb) {
				return 0;
			}
			return ($aa < $bb) ? -1 : 1;
		});

		return $array;
	}
	
	public function facility_search_info() {

		if(!$this->auth->is_logged_in(true) && !$this->auth->is_logged_in(false)) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$super_manager = $_POST['super_manager'] ?? null;

		$type = $_POST['type'] ?? null;
		$subcontractor = $_POST['subcontractor'] ?? null;
		$building = $_POST['building'] ?? null;
		$floor = $_POST['floor'] ?? null;
		$sopt = $_POST['sopt'] ?? null;

		$FacilityModel = new FacilityModel();
		
		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		}

		if(is_null($FacilityModel->first())) {
			return $this->failNotFound();
		}

		if($type != null) {
			$FacilityModel->where('type', $type);
		}

		if($subcontractor != null) {
			$FacilityModel->like('subcontractor', $subcontractor);
		}

		if($building != null) {
			$FacilityModel->where('building', $building);
		}

		if($floor != null) {
			$FacilityModel->where('floor', $floor);
		}

		if($sopt != null) {
			$FacilityModel->where('sopt', $sopt);
		}

		$FacilityModel->select('GROUP_CONCAT(DISTINCT type) as type, GROUP_CONCAT(DISTINCT subcontractor) as subcontractor, GROUP_CONCAT(DISTINCT building) as building, GROUP_CONCAT(DISTINCT floor) as floor, GROUP_CONCAT(DISTINCT spot) as spot')
						->where('place_id', $place_id);

		if($this->auth->is_logged_in(true)) {

			$FacilityModel->like('super_manager', $this->auth->supermanager());

		} else if($super_manager != null) {
			$FacilityModel->like('super_manager', $super_manager);
		}

		$info = $FacilityModel->first();

		$data = [

			'type' => implode(',', $this->sort_array(explode(',', $info['type']))),
			'subcontractor' => implode(',', array_unique(explode(',', $info['subcontractor']))),
			'building' => implode(',', $this->sort_array(explode(',', $info['building']))),
			'floor' => implode(',', $this->sort_floor_array(explode(',', $info['floor']))),
			'spot' => implode(',', $this->sort_array(explode(',', $info['spot']))),

		];

		return $this->respond($data);
	}

	public function facility_search() {
		
		if(!$this->auth->is_logged_in(true) && !$this->auth->is_logged_in(false)) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;
		$serial = $_POST['serial'] ?? null;
		$type = $_POST['type'] ?? null;
		$super_manager = $_POST['super_manager'] ?? null;
		$subcontractor = $_POST['subcontractor'] ?? null;
		$building = $_POST['building'] ?? null;
		$floor = $_POST['floor'] ?? null;
		$spot = $_POST['spot'] ?? null;
		$button_right = $_POST['button_right'] ?? null;
		$filter = $_POST['filter'] ?? null;

		$state_array = [];
		
		$FacilityModel = new FacilityModel();

		//관리자
		if(intval($button_right) == 2) {
			$FacilityModel->select('facility.*, taskplan.type as taskplan_type')->join('taskplan', 'facility.o_serial = taskplan.facility_serial', 'left outer');
		}
		
		if($place_id != null) {
			$FacilityModel->where('facility.place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		if($serial != null) {
			$FacilityModel->like('facility.serial', $serial);
		}

		if($type != null) {
			$FacilityModel->where('facility.type', $type);
		}

		if($super_manager != null) {
			$FacilityModel->like('facility.super_manager', $super_manager);
		}

		if($subcontractor != null) {
			$FacilityModel->like('facility.subcontractor', $subcontractor);
		}

		if($building != null) {
			$FacilityModel->where('facility.building', $building);
		}
		
		if($floor != null) {
			$FacilityModel->where('facility.floor', $floor);
		}

		if($spot != null) {
			$FacilityModel->where('facility.spot', $spot);
		}

		if($filter != null) {
			$state_array = str_split($filter); 
		}

		if($this->auth->is_logged_in(true)) {
			$FacilityModel->like('facility.super_manager', $this->auth->supermanager());
		}

		$searched_facilities = $FacilityModel->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false)
											->findAll();

		//필터적용
		$states = ["a", "b", "c", "d", "e", "f", "g"];
        $facilities_result = [];
        $is_state = false;
        
        foreach($states as $state) {

            if(in_array($state, $state_array)) {

                $facilities = array_values(array_filter($searched_facilities, function($facility) use($state) {
    
                    $started_at = $facility['started_at'];
                    $finished_at = $facility['finished_at'];
                    $edit_started_at = $facility['edit_started_at'];
                    $edit_finished_at = $facility['edit_finished_at'];
                    $dis_started_at = $facility['dis_started_at'];
                    $dis_finished_at = $facility['dis_finished_at'];
                    
                    if($state == "a") {
                        return is_null($started_at) && is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "b") {
                        return !is_null($started_at) && is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "c") {
                        return !is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "d") {
                        return !is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "e") {
                        return !is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "f") {
                        return !is_null($dis_started_at) && is_null($dis_finished_at);
                    } else if($state == "g") {
                        return !is_null($dis_finished_at);
                    }
                
                }));
                
                foreach($facilities as $facility) {

                    array_push($facilities_result, $facility);

                }

                $is_state = true;
            }
        }
        if(!$is_state) {
            $facilities_result = $searched_facilities;
        }

        //승인번호순으로 정렬
        if(count($facilities_result) > 0) {

            foreach((array)$facilities_result as $key => $value) {
                $sort[$key] = $value['serial'];
            }
            array_multisort($sort, SORT_ASC, $facilities_result);

        }

		return $this->respond($facilities_result);

	}

	public function facility_edit_state() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$state_type = $_POST['state_type'] ?? null;

		$state_column = [

			'created_at',
			'started_at',
			'finished_at',
			'edit_started_at',
			'edit_finished_at',
			'dis_started_at',
			'dis_finished_at',

		];

		if($id == null || $state_type == null || !is_numeric($state_type) || intval($state_type) < 0 || intval($state_type) >= count($state_column)) {
			return $this->failValidationError();
		}

		$FacilityModel = new FacilityModel();
		$TaskPlanModel = new TaskPlanModel();
		$this_facility = $FacilityModel->where('id', $id)->first();

		if($this_facility == null) {
			return $this->failForbidden();
		}

		$this_facility_state = 0;
		for($i = count($state_column)-1; $i >= 0; $i--) {
			if(!is_null($this_facility[$state_column[$i]])) {
				$this_facility_state = $i;
				break;
			}
		}

		//선택한 것이 지금 진행상황과 같으면 아무 작업도 하지 않는다.
		if($state_type == $this_facility_state) {
			return $this->respond();
		}

		$place_id = $this_facility['place_id'];
		$o_serial = $this_facility['o_serial'];

		if($place_id == 0 || $o_serial == "") {
			return $this->failForbidden();
		}

		$FacilityModel->where('place_id', $place_id)->where('o_serial', $o_serial);

		//선택된 진행상황 이전 진행상황중 null이 있으면 채워넣기
		for($i = $state_type; $i > 0; $i--) {
			$FacilityModel->set($state_column[$i], "IF(".$state_column[$i]." IS NULL, '" . Time::now() . "', " . $state_column[$i] . ")", false);
		}
		//선택된 진행상황 이후 진행상황은 모두 삭제
		for($i = $state_type+1; $i < count($state_column); $i++) {
			$FacilityModel->set($state_column[$i], null);
		}
		//설치전, 설치중 상황이면 만료일 삭제
		if($state_type < 2) {
			$FacilityModel->set('expired_at', null);
		}
		//이전 진행상황으로 되돌리는 상황이면 설치계획 삭제
		$TaskPlanModel->where('place_id', $place_id)->where('facility_serial', $o_serial);
		if($state_type < $this_facility_state) {
			/*
			if($state_type == 0) {
				$TaskPlanModel->delete(null, true);
			} else */ if($state_type < 2) {
				$TaskPlanModel->where('type', 2)->orwhere('type', 3)->delete(null, true);
			}

		//순차적으로 진행되었을 떄
		} else {
			//승인완료되면 설치예정 작업계획 삭제
			if($state_type == 2) {
				$TaskPlanModel->where('type', 1)->delete(null, true);
			//수정중이되면 해체예정 작업계획 삭제
			} else if($state_type == 3) {
				$TaskPlanModel->where('type', 3)->delete(null, true);
			//수정완료되면 설치예정, 수정예정 작업계획 삭제
			} else if($state_type == 4) {
				$TaskPlanModel->where('type', 1)->orwhere('type', 2)->delete(null, true);
			//해체중이되면 수정예정 작업계획 삭제
			} else if($state_type == 5) {
				$TaskPlanModel->where('type', 2)->delete(null, true);
			//해체완료되면 모든 작업계획 삭제
			} else if($state_type == 6) {
				$TaskPlanModel->delete(null, true);
			}
		}

		$success = true;

		try {

			$success = $FacilityModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function facility_edit_super_manager() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$super_manager = $_POST['super_manager'] ?? null;

		if($id == null) {
			return $this->failValidationError();
		}

		$FacilityModel = new FacilityModel();

		if($this->auth->user()['place_id'] != null) {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$FacilityModel->where('id', $id);

		$FacilityModel->set('super_manager', $super_manager);

		$success = true;

		try {

			$success = $FacilityModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function facility_edit_purpose() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$purpose = $_POST['purpose'] ?? null;

		if($id == null) {
			return $this->failValidationError();
		}

		$FacilityModel = new FacilityModel();

		if($this->auth->user()['place_id'] != null) {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$FacilityModel->where('id', $id);

		$FacilityModel->set('purpose', $purpose);

		$success = true;

		try {

			$success = $FacilityModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function facility_edit_size() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$size = $_POST['size'] ?? null;
		$is_danger = $_POST['is_danger'] ?? 'false';
		$is_danger = $is_danger == 'true'? true: false;

		if($id == null || $size == null) {
			return $this->failValidationError();
		}
		if($this->auth->user()['place_id'] == null) {
			return $this->failForbidden();
		}

		$FacilityModel = new FacilityModel();
		
		$FacilityModel->where('place_id', $this->auth->user()['place_id'])->where('id', $id);

		if($is_danger) {
			$FacilityModel->set('danger_result', $size);
		} else {
			$FacilityModel->set('cube_result', $size);
		}

		$success = true;

		try {

			$success = $FacilityModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}
	
	public function facility_edit_expired_at() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$_expired_at = $_POST['expired_at'] ?? null;

		if($id == null || $_expired_at == null) {
			return $this->failValidationError();
		}


		$expired_at = null;

		try {

			$expired_at = Time::parse($_expired_at);

		} catch(\Exception $e) {
			return $this->failValidationError();
		}

		if($id == null || $expired_at == null) {
			return $this->failValidationError();
		}

		$FacilityModel = new FacilityModel();

		$this_facility = $FacilityModel->where('place_id', $this->auth->user()['place_id'])->where('id', $id)->first();

		$FacilityModel->where('place_id', $this_facility['place_id'])->where('o_serial', $this_facility['o_serial']);

		$FacilityModel->set('expired_at', "IF(expired_at IS NULL, '" . $expired_at . "', IF(id = '" . $id . "', '" . $expired_at . "', expired_at))", false);

		$success = true;

		try {

			$success = $FacilityModel->update();

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();

	}

	/*-------------------------------------------작업관련-------------------------------------------*/

	public function task_add() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$team_id = $_POST['team_id'] ?? null;
		$facility_id = $_POST['facility_id'] ?? null; // 이전버전을 위해 남겨둠
		$place_id = $_POST['place_id'] ?? null;
		$facility_serial = $_POST['facility_serial'] ?? null;
		$manday = $_POST['manday'] ?? null;
		$type = $_POST['type'] ?? null;

		$state_column = [

			'created_at',			//0
			'started_at',			//1
			'finished_at',			//2
			'edit_started_at',		//3
			'edit_finished_at',		//4
			'dis_started_at',		//5
			'dis_finished_at',		//6

		];

		if($team_id == null || $manday == null || $type == null) {
			return $this->failValidationError();
		}

		$TeamModel = new TeamModel();
		$FacilityModel = new FacilityModel();
		$TaskModel = new TaskModel();
		$TaskPlanModel = new TaskPlanModel();

		//이전버전을 위한 것
		if($place_id == null) {
			$place_id = $TeamModel->where('id', $team_id)->first()['place_id'] ?? 0;
		}
		if($facility_id != null && $facility_serial == null) {
			$this_facility = $FacilityModel->where('place_id', $place_id)->where('id', $facility_id)->first();
			$facility_serial = $this_facility['o_serial'];
			
		//최신버전
		} else {
			$this_facility = $FacilityModel->where('place_id', $place_id)->where('o_serial', $facility_serial)->first();
		}

		if($place_id == 0 || $facility_serial == "") {
			return $this->failForbidden();
		}
		
		if($this_facility != null) {
			if($this_facility['danger_result'] != 0) {
				$size = $this_facility['danger_result'];
				$is_square = 1;
			} else {
				$size = $this_facility['cube_result'];
				$is_square = 0;
			}

			$taskplan_type = $TaskPlanModel->where('place_id', $place_id)->where('facility_serial', $facility_serial)->first()['type'] ?? null;
		
			$this_facility_state = 0;
			for($i = count($state_column)-1; $i >= 0; $i--) {
				if(!is_null($this_facility[$state_column[$i]])) {
					$this_facility_state = $i;
					break;
				}
			}
			
			$state_num = 0;
			if($type == 1) {
				$state_num = 1;
			} else if($type == 2) {
				$state_num = 3;
			} else if($type == 3) {
				$state_num = 5;
			}
	
			//진행중인 작업인지 확인
			$same_facility = $FacilityModel->where('place_id', $place_id)->where('o_serial', $facility_serial)->where($state_column[$state_num] . ' !=', null)->first();
	
			if(is_null($same_facility) || ($taskplan_type == 2 && $this_facility_state == 4 && $type == 2)) {
	
				$FacilityModel->where('place_id', $place_id)->where('o_serial', $facility_serial);
				//처음 진행하는 작업이라면
				if(is_null($same_facility)) {
					//이전작업의 빈날짜를 모두 오늘로 채워넣기
					for($i = $state_num; $i > 0; $i--) {
						$FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . Time::now() . "', " . $state_column[$i] . ")", false);
					}
				}
	
				//수정작업계획($type == 2)이 있고 현재진행상태는 수정완료($this_facility_state == 4)일때에 한해 수정버튼($type == 2)을 눌렸을시 수정완료일이 삭제된다
				if($taskplan_type == 2 && $this_facility_state == 4 && $type == 2) {
					$FacilityModel->set('edit_finished_at', null);
				}
				$FacilityModel->update();
			}

		} else {
			$size = 0;
			$is_square = 0;
		}
		

		$success = true;

		try {
			
			$success = $TaskModel->insert([
				'type' => $type,
				'place_id' => $place_id,
				'facility_serial' => $facility_serial,
				'team_id' => $team_id,
				'size' => $size,
				'is_square' => $is_square,
				'manday' => $manday,
			]);

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	/*-----------------------------------------작업계획관련-----------------------------------------*/

	public function taskplan() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$place_id = $_POST['place_id'] ?? null;

		if($place_id == null) {
			return $this->failValidationError();
		}
		
		$FacilityModel = new FacilityModel();
		$TaskPlanModel = new TaskPlanModel();
		
		if($this->auth->user()['place_id'] == null) {
			$FacilityModel->where('place_id', $place_id);
		} else {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$target_time = Time::now()->addDays(14);

		$expire_facilities = $FacilityModel->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false)
											->where('place_id', $place_id)->where('expired_at < ', $target_time)
											->findAll();

		$facility_with_taskplan = $FacilityModel->select('facility.*, taskplan.type as taskplan, team.name as team_name')
								->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false)
								->join('taskplan', 'facility.place_id = taskplan.place_id and facility.o_serial = taskplan.facility_serial')
								->join('team', 'team.id = taskplan.team_id')
								->where('facility.place_id', $place_id)
								->findAll();


		$construct_planned_facilities = array_values(array_filter($facility_with_taskplan, function($facility) {
			return $facility['taskplan'] == 1;
		}));

		$edit_planned_facilities = array_values(array_filter($facility_with_taskplan, function($facility) {
			return $facility['taskplan'] == 2;
		}));

		$destruct_planned_facilities = array_values(array_filter($facility_with_taskplan, function($facility) {
			return $facility['taskplan'] == 3;
		}));

		$etc_taskplan = $TaskPlanModel->select('taskplan.*, team.name as team_name')
									->join('team', 'team.id = taskplan.team_id')
									->where('taskplan.place_id', $place_id)
									->where('taskplan.type', 4)
									->findAll();


		$data = [

			'expire' => $expire_facilities,
			'construct' => $construct_planned_facilities,
			'edit' => $edit_planned_facilities,
			'destruct' => $destruct_planned_facilities,
			'etc' => $etc_taskplan,

		];

		return $this->respond($data);
	}

	public function taskplan_team() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$team_id = $_POST['team_id'] ?? null;

		if($team_id == null) {
			return $this->failValidationError();
		}

		$TeamModel = new TeamModel();
		$FacilityModel = new FacilityModel();
		$TaskPlanModel = new TaskPlanModel();
		
		$place_id = $TeamModel->where('id', $team_id)->first()['place_id'];

		$plan_task = $FacilityModel->select('facility.*, taskplan.type as taskplan')
									->orderBy('facility.serial', 'ASC')
									->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false)
									->join('taskplan', 'taskplan.team_id = ' . $team_id . ' and taskplan.place_id = facility.place_id and taskplan.facility_serial = facility.o_serial')
									->where('facility.place_id', $place_id)
									->findAll();
		
		$etc_plan = $TaskPlanModel->where('team_id', $team_id)->where('type', 4)->findAll();

		$target_time = Time::now()->subDays(3);

		$recent_task = $FacilityModel->select('facility.*')
									->orderBy('facility.serial', 'ASC')
									->distinct()
									->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false)
									->join('task', 'task.team_id = "' . $team_id . '" and task.created_at > "' . $target_time . '" and task.place_id = facility.place_id and task.facility_serial = facility.o_serial', 'inner', false)
									//->join('taskplan', 'taskplan.place_id = facility.place_id and taskplan.facility_serial = facility.o_serial', 'left outer')
									->where('facility.place_id', $place_id)
									->findAll();

		$data = [

			'plan' => $plan_task,
			'etc_plan' => $etc_plan,
			'recent' => $recent_task,
		
		];

		return $this->respond($data);
	}
	
	public function taskplan_edit() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$facility_id = $_POST['facility_id'] ?? null; // 이전버전을 위해 남겨둠

		$place_id = $_POST['place_id'] ?? null;
		$facility_serial = $_POST['facility_serial'] ?? null;
		$team_id = $_POST['team_id'] ?? null;
		$type = $_POST['type'] ?? null;

		if($team_id == null || $type == null) {
			return $this->failValidationError();
		}

		$TeamModel = new TeamModel();
		$TaskPlanModel = new TaskPlanModel();

		//이전버전을 위한 것
		if($place_id == null) {
			$place_id = $TeamModel->where('id', $team_id)->first()['place_id'] ?? null;
		}
		if($facility_id != null && $facility_serial == null) {
			$FacilityModel = new FacilityModel();
			$facility_serial = $FacilityModel->where('place_id', $place_id)->where('id', $facility_id)->first()['o_serial'] ?? null;
		}

		if($place_id == 0 || $facility_serial == "") {
			return $this->failForbidden();
		}
		
		$taskplan = $TaskPlanModel->where('place_id', $place_id)->where('facility_serial', $facility_serial)->first();

		$success = true;

		try {
			if($taskplan == null) {

				$success = $TaskPlanModel->insert([
					'place_id' => $place_id,
					'facility_serial' => $facility_serial,
					'team_id' => $team_id,
					'type' => $type,
				]);

			} else {
				$success = $TaskPlanModel->where('place_id', $place_id)->where('facility_serial', $facility_serial)->set('team_id', $team_id)->set('type', $type)->update();
			}

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function taskplan_delete() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$facility_id = $_POST['facility_id'] ?? null;

		$place_id = $_POST['place_id'] ?? null;
		$facility_serial = $_POST['facility_serial'] ?? null;

		$TaskPlanModel = new TaskPlanModel();

		//이전버전을 위한 것
		if($facility_id != null && $place_id == null && $facility_serial == null) {
			$FacilityModel = new FacilityModel();
			$this_facility = $FacilityModel->where('id', $facility_id)->first();
			$place_id = $this_facility['place_id'];
			$facility_serial = $this_facility['o_serial'];
		}

		if($place_id == 0 || $facility_serial == "") {
			return $this->failForbidden();
		}

		$success = true;

		try {

			$success = $TaskPlanModel->where('place_id', $place_id)->where('facility_serial', $facility_serial)->delete(null, true);

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	public function taskplan_etc() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$taskplan_id = $_POST['taskplan_id'] ?? null;

		$TaskPlanModel = new TaskPlanModel();
		$TaskModel = new TaskModel();

		$etc_taskplan = $TaskPlanModel->where('id', $taskplan_id)->first();




		$etc_taskplan['in_task'] = true;

		return $this->respond($etc_taskplan);

	}

	/*-----------------------------------------담당자관련-----------------------------------------*/

	public function super_manager() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;

		$FacilityModel = new FacilityModel();

		if($place_id == null) {
			return $this->failValidationError();
		}

		if(is_null($FacilityModel->where('place_id', $place_id)->where('super_manager !=', '')->first())) {
			return $this->failNotFound();
		}
		
		$FacilityModel->where('place_id', $place_id);

		$info = $FacilityModel->select('GROUP_CONCAT(DISTINCT super_manager) as super_manager')
								->where('super_manager !=', '')
								->first();
				
		return $this->respond($info);
	}

	/*-------------------------------------------생산성-------------------------------------------*/

	public function get_productivity_task($team_id, $start_time, $end_time) {

        $TaskModel = new TaskModel();

        $tasks = $TaskModel->select('ttt.size_current as size_current, ttt.is_square_current as is_square_current, task.facility_serial, t.manday_max as manday_max, task.type, task.place_id')
                            ->join('facility as f', '(f.o_serial = task.facility_serial AND f.place_id = task.place_id AND f.finished_at >= "' . $start_time->toDateTimeString() . '" AND f.finished_at < "' . $end_time->toDateTimeString() . '")', 'inner', false)
                            ->join('(SELECT MAX(manday) as manday_max, facility_serial from task where type = 1 group by facility_serial) t', 't.facility_serial = task.facility_serial', 'inner', false)
                            ->join('(SELECT MAX(created_at) as max_created_at, facility_serial from task where type = 1 group by facility_serial) as tt', 'tt.facility_serial = task.facility_serial', 'inner', false)
                            ->join('(SELECT size as size_current, is_square as is_square_current, created_at, facility_serial from task where type = 1) as ttt', "(ttt.created_at = tt.max_created_at AND ttt.facility_serial = tt.facility_serial)", 'inner', false)
                            ->where('task.type', 1)
                            ->where('task.team_id', $team_id)
                            ->groupBy('task.facility_serial, ttt.size_current, ttt.is_square_current, task.place_id')
                            ->findAll();

        return $tasks;
    }

    public function get_productivity_manday($team_id, $start_time, $end_time) {

        $TaskModel = new TaskModel();

        $tasks_manday = $TaskModel->select("ANY_VALUE(task.id) as id, ANY_VALUE(t.type) as type, ANY_VALUE(t.facility_serial) as facility_serial, MAX(task.manday) as manday_max, date_format(task.created_at, '%Y-%m-%d') as s_created_at")
                                    ->join("( SELECT id, type, facility_serial from task ) t", '(t.id = task.id)', 'inner', false)
                                    ->groupStart()->where('task.type', 2)->orWhere('task.type', 3)->groupEnd()
                                    ->where('task.team_id', $team_id)
                                    ->where('task.created_at >= ', $start_time)
                                    ->where('task.created_at < ', $end_time)
                                    ->groupBy('s_created_at')
                                    ->orderBy('s_created_at', 'ASC')
                                    ->findAll();

        return $tasks_manday;

    }

	public function productivity() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;

		if($place_id == null) {
			return $this->failValidationError();
		}

		$_target_time = $_POST['target_time'] ?? null;
		
		if($_target_time == null || !is_numeric($_target_time)) {
			$target_time = Time::now();
		} else {
			$target_time = Time::createFromTimestamp($_target_time);
		}
		
		$year = $target_time->getYear();
		$month = $target_time->getMonth();

		$start_time = $target_time->setMonth($month)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		if($month == 12) {

			$end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		} else {

			$end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		}
		
		$TeamModel = new TeamModel();
		$teams = $TeamModel->where('place_id', $place_id)->orderBy('name', 'ASC')->findAll();

		if(count($teams) == 0) {
			return $this->failNotFound();
		} 

		$team_ids = array_map(function($team) {
			return $team['id'];
		}, $teams);

		foreach($team_ids as $team_id) {

			$tasks = $this->get_productivity_task($team_id, $start_time, $end_time);
			$mandays = $this->get_productivity_manday($team_id, $start_time, $end_time);

			$total_cube = 0; //수평비계
			$total_square = 0; //달대비계
			$total_manday = 0; //맨데이
			
			foreach($tasks as $task) {

				if($task['is_square_current'] == 0) { //수평비계

					$total_cube += $task['size_current'] / $task['manday_max'];

				} else if($task['is_square_current'] == 1) { //달대비계
				
					$total_square += $task['size_current'] / $task['manday_max'];

				}
			}

			foreach($mandays as $manday) {

				$total_manday += $manday['manday_max'];

			}

			$totals_cube[$team_id] = $total_cube;
			$totals_square[$team_id] = $total_square;
			$totals_manday[$team_id] = $total_manday;

		}
		
		$data = [

			'teams' => $teams,
			'totals_cube' => $totals_cube,
			'totals_square' => $totals_square,
			'totals_manday' => $totals_manday,

		];


		return $this->respond($data);

	}

	
	public function dashboard() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$place_id = $_POST['place_id'] ?? null;
		$team_id = $_POST['team_id'] ?? null;
		$_target_time = $_POST['target_time'] ?? null;

		if($place_id == null) {
			return $this->failValidationError();
		}
		
		$TeamModel = new TeamModel();
		
		if($this->auth->user()['place_id'] == null) {
			$TeamModel->where('place_id', $place_id);
		} else {
			$TeamModel->where('place_id', $this->auth->user()['place_id']);
		}

		if($_target_time == null || !is_numeric($_target_time)) {
			$target_time = Time::now();
		} else {
			$target_time = Time::createFromTimestamp($_target_time);
		}
		
		$year = $target_time->getYear();
		$month = $target_time->getMonth();

		$start_time = $target_time->setMonth($month)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		if($month == 12) {

			$end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		} else {

			$end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(5)->setMinute(0)->setSecond(0);

		}

		$TeamModel = new TeamModel();
		$team = $TeamModel->where('id', $team_id)->first();

		$tasks = $this->get_productivity_task($team_id, $start_time, $end_time);
		$mandays = $this->get_productivity_manday($team_id, $start_time, $end_time);

		$total_cube = 0; //수평비계
		$total_square = 0; //달대비계
		$total_manday = 0; //맨데이
		
		foreach($tasks as $task) {

			if($task['is_square_current'] == 0) { //수평비계

				$total_cube += $task['size_current'] / $task['manday_max'];

			} else if($task['is_square_current'] == 1) { //달대비계
			
				$total_square += $task['size_current'] / $task['manday_max'];

			}
		}

		foreach($mandays as $manday) {

			$total_manday += $manday['manday_max'];

		}

		// 안전점수
		$TeamSafePointModel = new TeamSafePointModel();
		//$SafePointModel = new SafePointModel();

		$team_safe_points = $TeamSafePointModel
						->select('team_safe_point.id as id,  sp.name as name, sp.point as point, team_safe_point.created_at as created_at')
						->join('safe_point as sp', 'sp.id = team_safe_point.safe_point_id')
						->where('team_id', $team_id)
						->where('team_safe_point.created_at >= ', $start_time)
						->where('team_safe_point.created_at < ', $end_time)
						->findAll();

		$safe_points = 0;

		foreach($team_safe_points as $team_safe_point) {

			$safe_points += $team_safe_point['point'];

		}

		$data = [

			'total_cube' => $total_cube,
			'total_square' => $total_square,
			'total_manday' => $total_manday,
			'safe_points' => $safe_points

		];

		return $this->respond($data);


		
	}


	/*-------------------------------------------안전점수-------------------------------------------*/

	public function safe_point() {

	}

	public function safe_point_team() {

	}

	/*-------------------------------------------테스트-------------------------------------------*/

	public function test() {
	

	}

}