<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\TaskPlanModel;
use App\Models\FacilityModel;
use App\Models\PlaceModel;
use App\Models\TeamMateModel;
use App\Models\TeamModel;
use App\Models\UserModel;
use CodeIgniter\I18n\Time;
use CodeIgniter\RESTful\ResourceController;

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
			//return $this->failValidationError(); <- 출시후에는 다시 살림
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
			$teams = $TeamModel->where('id !=', $team_id)->findAll();
		} else {
			$teams = $TeamModel->findAll();
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
                            	  		->distinct()
                          		  		->from('teammate as tm')
                         	     		->join('attendance as a', '(a.teammate_id = tm.id and a.created_at >"'.$this->setTime()->toDateTimeString().'")', 'left outer')
                          	    		->where('tm.team_id', $team_id)
                              			->findAll();

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
			return $this->failForbidden();
		}

		$AttendanceModel = new AttendanceModel();

		$teammate_id = $_POST['teammate_id'] ?? null;
		$type = $_POST['type'] ?? null;
		$date = $_POST['date'] ?? null;

		if(is_null($teammate_id) || is_null($type)) {
			return $this->failValidationError();
		}
		if(is_null($type) || is_null($date)) {
			return $this->failValidationError();
		}
		if($type < 0 || $type > 1) {
			return $this->failValidationError();
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
		$TeamMateModel = new TeamMateModel();
		$AttendanceModel = new AttendanceModel();
		$TaskPlanModel = new TaskPlanModel();

		/*
		if($this->auth->is_logged_in(true)) {
			$FacilityModel->like('super_manager', $this->auth->supermanager());
		}
		*/

		if($team_id != null) {

			$teammate = $TeamMateModel->where('team_id', $team_id)->countAllResults();

			$attendance = $AttendanceModel->select('teammate.name as name')
											->join('teammate', 'attendance.teammate_id = teammate.id and teammate.team_id = "' . $team_id . '"')
											->where('attendance.type', '0')
											->where('attendance.created_at >', $this->setTime()->toDateTimeString())
											->countAllResults();
		
			$facility = $FacilityModel->select('facility.*, ' .$teammate. ' as teammate, ' .$attendance.' as attendance')
									->where('facility.id', $facility_id)
									->first();

		} else if(!is_null($TaskPlanModel->where('facility_id', $facility_id)->first())) {

			$facility = $FacilityModel->select('facility.*, taskplan.type as taskplan_type, taskplan.team_id as taskplan_team_id')
									->join('taskplan', 'facility.id = taskplan.facility_id', 'left outer')
									->where('facility.id', $facility_id)
									->first();

		} else {

			$facility = $FacilityModel->where('facility.id', $facility_id)->first();

		}
		/*
		if(is_null($TaskPlanModel->where('facility_id', $facility_id)->first())){
			$facility = $FacilityModel->where('id', $facility_id)->first();
		} else {
			$facility = $FacilityModel->select('facility.*, taskplan.type as taskplan_type, taskplan.team_id as taskplan_team_id')
									->join('taskplan', 'facility.id = taskplan.facility_id', 'left outer')
									->where('facility.id', $facility_id)
									->first();
		}
		*/
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

		$FacilityModel = new FacilityModel();
		
		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		}

		if(is_null($FacilityModel->first())) {
			return $this->failNotFound();
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
		$subcontractor = $_POST['subcontractor'] ?? null;
		$building = $_POST['building'] ?? null;
		$floor = $_POST['floor'] ?? null;
		$spot = $_POST['spot'] ?? null;
		$super_manager = $_POST['super_manager'] ?? null;
		
		$FacilityModel = new FacilityModel();
		
		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}

		if($serial != null) {
			$FacilityModel->like('serial', $serial);
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

		if($spot != null) {
			$FacilityModel->where('spot', $spot);
		}

		if($super_manager != null) {
			$FacilityModel->like('super_manager', $super_manager);
		}
		
		if($this->auth->is_logged_in(true)) {
			$FacilityModel->like('super_manager', $this->auth->supermanager());
		}

		return $this->respond($FacilityModel->findAll());

	}

	public function facility_edit_state() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$id = $_POST['id'] ?? null;
		$state_type = $_POST['state_type'] ?? null;

		if($state_type == null) {
			return $this->failValidationError();
		}

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

		if($this->auth->user()['place_id'] != null) {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$FacilityModel->where('id', $id);

		$FacilityModel->set($state_column[$state_type], "IF(".$state_column[$state_type]." IS NULL, '".Time::now()->toDateString()."', ".$state_column[$state_type].")", false);

		for($i = $state_type+1; $i<count($state_column); $i++) {
			$FacilityModel->set($state_column[$i], null);
		}

		if($state_type < 2) {
			$FacilityModel->set('expired_at', null);
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

		if($this->auth->user()['place_id'] != null) {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$FacilityModel->where('id', $id);

		$FacilityModel->set('expired_at', $expired_at);

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

	/*-----------------------------------------작업계획관련-----------------------------------------*/

	public function taskplan() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}
		
		$place_id = $_POST['place_id'] ?? null;

		if($this->auth->user()['place_id'] != null && $this->auth->user()['place_id'] != $place_id) {
			return $this->failForbidden();
		}
		
		$FacilityModel = new FacilityModel();
		
		if($this->auth->user()['place_id'] == null) {
			$FacilityModel->where('place_id', $place_id);
		} else {
			$FacilityModel->where('place_id', $this->auth->user()['place_id']);
		}

		$target_time = Time::now()->addDays(14);

		$expire_facilities = $FacilityModel->where('expired_at < ', $target_time)->findAll();

		if($this->auth->user()['place_id'] == null) {
			$FacilityModel->where('facility.place_id', $place_id);
		} else {
			$FacilityModel->where('facility.place_id', $this->auth->user()['place_id']);
		}

		$facility_with_taskplan = $FacilityModel->select('facility.*, taskplan.type as taskplan, team.name as team_name')
												->join('taskplan', 'facility.id = taskplan.facility_id')
												->join('team', 'team.id = taskplan.team_id')
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

		$data = [

			'expire' => $expire_facilities,
			'construct' => $construct_planned_facilities,
			'edit' => $edit_planned_facilities,
			'destruct' => $destruct_planned_facilities,

		];

		return $this->respond($data);
	}

	public function taskplan_team() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$team_id = $_POST['team_id'] ?? null;

		$TaskPlanModel = new TaskPlanModel();
		$TaskPlanModel->where('team_id', $team_id);

		$taskplan = $TaskPlanModel->select('facility.*, taskplan.type as taskplan')
									->join('facility', 'facility.id = taskplan.facility_id')
									->findAll();
		
		return $this->respond($taskplan);
	}
	
	public function taskplan_edit() {

		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$facility_id = $_POST['facility_id'] ?? null;
		$team_id = $_POST['team_id'] ?? null;
		$type = $_POST['type'] ?? null;

		if($facility_id == null || $team_id == null || $type == null) {
			return $this->failValidationError();
		}

		$TeamModel = new TeamModel();
		$TaskPlanModel = new TaskPlanModel();

		if($this->auth->user()['place_id'] != null && is_null($TeamModel->where('place_id', $this->auth->user()['place_id'])->where('id', $team_id)->first()) ) {
			return $this->failForbidden();
		}
		
		$taskplan = $TaskPlanModel->where('facility_id', $facility_id)->first();

		$success = true;

		try {
			if($taskplan == null) {

				$success = $TaskPlanModel->insert([
					'facility_id' => $facility_id,
					'team_id' => $team_id,
					'type' => $type,
				]);

			} else {
				$success = $TaskPlanModel->where('facility_id', $facility_id)->set('team_id', $team_id)->set('type', $type)->update();
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
		$TaskPlanModel = new TaskPlanModel();

		if(!is_null($facility_id)) {
			$taskplan = $TaskPlanModel->where('facility_id', $facility_id)->first();
		} else {
			return $this->failValidationError();
		}

		$success = true;


		try {

			$success = $TaskPlanModel->delete($taskplan['id'], true);

		} catch(\Exception $e) {

			$success = false;

		}

		if($success) return $this->respondUpdated();
		else return $this->failServerError();
	}

	/*-----------------------------------------담당자관련-----------------------------------------*/

	public function super_manager() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failUnauthorized();
		}

		$place_id = $_POST['place_id'] ?? null;

		$FacilityModel = new FacilityModel();

		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		} else {
			return $this->failValidationError();
		}
		
		if(is_null($FacilityModel->where('super_manager !=', '')->first())) {
			return $this->failNotFound();
		}
		
		$info = $FacilityModel->where('super_manager !=', '')
								->select('GROUP_CONCAT(DISTINCT super_manager) as super_manager')
								->first();
				
		return $this->respond($info);
	}

	/*-------------------------------------------테스트-------------------------------------------*/

	public function test() {
	
		$facility_id = 1;
		$team_id = 1;

		$FacilityModel = new FacilityModel();
		$TeamMateModel = new TeamMateModel();
		$AttendanceModel = new AttendanceModel();

		$teammate = $TeamMateModel->where('team_id', $team_id)->countAllResults();

		$attendance = $AttendanceModel->select('teammate.name as name')
										->join('teammate', 'attendance.teammate_id = teammate.id and teammate.team_id = "' . $team_id . '"')
										->where('attendance.type', '0')
										->where('attendance.created_at >', $this->setTime()->toDateTimeString())
										->countAllResults();
		
		$result = $FacilityModel->select('facility.*, ' .$teammate. ' as teammate, ' .$attendance.' as attendance')
								->where('facility.id', $facility_id)
								->first();

		return $this->respond($result);

	}

}