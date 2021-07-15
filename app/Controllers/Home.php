<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
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
		if($status == null || $status == 200 || $status == 201){
			
			$resp = [
				"status" => $status == null ? 200 : $status,
				"results" => $data
			];
			return parent::respond($resp, $status, $message);

		} else return parent::respond($data, $status, $message);
	}

    public function index()
    {
		$place_id = $_POST['place_id'] ?? null;
		$username = $_POST['username'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

		if (is_null($username) || is_null($birthday) ) {
			return $this->failValidationError();
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
			])->setCookie("jwt_token", $this->auth->createJWT(), 86500);
		} else {
			return $this->failForbidden();
		}
    }

	public function check() {

		if($this->auth->is_logged_in()) {
			return $this->respond(true);
		} else {
			return $this->respond(false);
		}


	}
	
	public function add_user() {
		
		$place_id = $_POST['place_id'] ?? null;
		$username = $_POST['username'] ?? null;
		$birthday = $_POST['birthday'] ?? null;
		
		if(is_null($username) || is_null($birthday)) {
			return $this->failValidationError();
		}

		$PlaceModel = new PlaceModel();		
		$UserModel = new UserModel();

		if(is_null($PlaceModel->where('id', $place_id)->first())) {
			return $this->failValidationError();
		}
		
		if(!is_null($UserModel->where('username', $username)->first())) {
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

    public function attendance_on() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$AttendanceModel = new AttendanceModel();

		$now = Time::now();

		$st = null;

		if( $now <= Time::createFromTime(5, 0, 0)) {

			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		} else {

			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		}

		$user_id = $this->auth->user_id();

		$attendances = $AttendanceModel->where('created_at >', $st)->where('user_id', $user_id)->findAll();

		if(count($attendances) > 0) {

			return $this->failValidationError();

		}

		$AttendanceModel->insert(['user_id' => $user_id, 'type' => TYPE_ATTENDANCE_ON]);

		return $this->respondCreated();

	}

	public function attendance_off() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$AttendanceModel = new AttendanceModel();

		$now = Time::now();

		$st = null;

		if( $now <= Time::createFromTime(5, 0, 0)) {

			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		} else {

			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		}

		$user_id = $this->auth->user_id();

		$attendances = $AttendanceModel->where('created_at >', $st)->where('user_id', $user_id)->findAll();

		if(count($attendances) == 0) {

			return $this->failValidationError();

		}

		$AttendanceModel->insert(['user_id' => $user_id, 'type' => TYPE_ATTENDANCE_OFF]);

		return $this->respondCreated();



	}

	public function attendance() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$TeamModel = new TeamModel();
		$TeamMateModel = new TeamMateModel();

		$team_id = $_POST['team_id'];

		if(is_null($team_id) || is_null($TeamModel->where('id', $team_id)->first())) {
			return $this->failValidationError();
		}

		$now = Time::now();

		$st = null;

		if( $now->getTimestamp() <= Time::createFromTime(5, 0, 0)->getTimestamp()) {

			$st = Time::now()->yesterday()->setHour(5)->setMinute(0)->setSecond(0);

		} else {

			$st = Time::now()->setHour(5)->setMinute(0)->setSecond(0);

		}

		$attendances = $TeamMateModel->select('tm.id as id, tm.name as name, tm.birthday as birthday, a.created_at as date, a.type as type')
									   ->distinct()
									   ->from('teammate as tm')
									   ->join('attendance as a', '(a.teammate_id = tm.id and a.created_at >"'.$st->toDateTimeString().'")', 'left outer')
									   ->where('tm.team_id', $team_id)
									   ->findAll();

		return $this->respond($attendances);
		

	}
	
	public function attendance_add() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
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

		if(is_null($teammate_id) || is_null($teammate_id)) {
			return $this->failValidationError();
		}

		if(is_null($type) || is_null($date)) {
			return $this->failValidationError();
		}

		if($type < 0 || $type > 1) {
			return $this->failValidationError();
		}

		$st = null;

		if( Time::now() <= Time::createFromTime(5, 0, 0)) {

			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		} else {

			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		}

		$AttendanceModel->where('created_at >', $st)
						->where('teammate_id', $teammate_id)
						->where('type', $type)
						->set('created_at', $date)
						->update();


		return $this->respondUpdated();


		
	}

	public function teammates() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$TeamMateModel = new TeamMateModel();

		$team_id =1;

		$teammates = $TeamMateModel->where('team_id', $team_id)
		
		
									->findAll();

		return $this->respond($teammates);

		

	}

	public function teams() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}
		
		$TeamModel = new TeamModel();

		$user = $this->auth->user();

		if($user['place_id'] == null) {

			$teams = $TeamModel->findAll();

		} else {

			$teams = $TeamModel->where('place_id', $user['place_id'])
								->findAll();

		}		

		return $this->respond($teams);

		

	}

	public function team_edit() {

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
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

	public function places() {

		$PlaceModel = new PlaceModel();

		$places = $PlaceModel->findAll();

		return $this->respond($places);

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

	public function facility_info() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$FacilityModel = new FacilityModel();

		$info = $FacilityModel->select('GROUP_CONCAT(DISTINCT type) as type, GROUP_CONCAT(DISTINCT subcontractor) as subcontractor, GROUP_CONCAT(DISTINCT building) as building, GROUP_CONCAT(DISTINCT floor) as floor, GROUP_CONCAT(DISTINCT spot) as spot')
								->first();


		

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

		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}

		$place_id = $_POST['place_id'] ?? null;
		$serial = $_POST['serial'] ?? null;
		$type = $_POST['type'] ?? null;
		$subcontractor = $_POST['subcontractor'] ?? null;
		$building = $_POST['building'] ?? null;
		$floor = $_POST['floor'] ?? null;
		$spot = $_POST['spot'] ?? null;

		$FacilityModel = new FacilityModel();

		if($place_id != $this->auth->user()['place_id'] ) {
			return $this->failUnauthorized();
		}

		if($place_id != null) {
			$FacilityModel->where('place_id', $place_id);
		}

		if($serial != null) {
			$FacilityModel->like('serial', $serial);
		}

		if($type != null) {
			$FacilityModel->where('type', $type);
		}

		if($subcontractor != null) {
			$FacilityModel->where('subcontractor', $subcontractor);
		}

		if($building != null) {
			$FacilityModel->where('building', $building);
		}

		if($floor != null) {
			$FacilityModel->where('floor', $floor);
		}

		//die($floor);

		if($spot != null) {
			$FacilityModel->where('spot', $spot);
		}

		return $this->respond($FacilityModel->findAll());

	}

}