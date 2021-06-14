<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
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

		$username = $_POST['username'] ?? null;

		
		if($this->auth->login($username)) {
			return $this->respond([])->setCookie("jwt_token", $this->auth->createJWT(), 86500);
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
		
		$username = $_POST['username'] ?? null;
		$birthday = $_POST['birthday'] ?? null;
		
		if(is_null($username) || is_null($birthday)) {
			return $this->failValidationError();
		}
		
		$UserModel = new UserModel();
		
		if(!is_null($UserModel->where('username', $username)->first())) {
			return $this->failResourceExists();
		}
		
		try {
			
			$birthday_t = Time::createFromFormat('Y-m-j', $birthday);
			
			$insert_id = $UserModel->insert([
				'username' => $username,
				'name' => $username,
				'birthday' => $birthday_t
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

		$AttendanceModel = new AttendanceModel();

		$now = Time::now();

		$st = null;

		if( $now <= Time::createFromTime(5, 0, 0)) {

			$st = Time::now()->yesterday()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		} else {

			$st = Time::now()->setHour(5, 0, 0)->setMinute(0)->setSecond(0);

		}

		$user_id = $this->auth->user_id();

		$team_id = 1;

		$TeamModel = new TeamModel();

		$team = $TeamModel->where('id', $team_id)->first();

		if(is_null($team)) {
			return $this->failNotFound();
		}

		$TeamMateModel = new TeamMateModel();

		$teammates = $TeamMateModel->where('team_id', $team_id)->findAll();

		$teammate_ids = array_map(function($element) {
			return $element['user_id'];
		}, $teammates);

		$user_ids = [];

		array_push($user_ids, $team['leader_id']);

		$user_ids = array_merge($user_ids, $teammate_ids);

		$UserModel = new UserModel();

		$attendances = $UserModel->select('a.id as id, u.id as user_id, u.name as user_name, u.birthday as user_birthday, a.created_at as date, a.type as type')
									   ->distinct()
									   ->from('user as u')
									   ->join('attendance as a', '(a.user_id = u.id AND a.created_at >"'.$st->toDateTimeString().'")', 'left outer')
									   ->whereIn('u.id', $user_ids)
									   ->findAll();

		return $this->respond($attendances);
		

	}
	
	public function attendance_add() {
		
		if(!$this->auth->is_logged_in()) {
			return $this->failForbidden();
		}
		
		$UserModel = new UserModel();
		$AttendanceModel = new AttendanceModel();
		
		$user_id = $_POST['user_id'] ?? null;
		$type = $_POST['type'] ?? null;
		
		if(is_null($user_id) || is_null($type)) {
			return $this->failValidationError();
		}
		
		if(is_null($UserModel->where('id', $user_id)->first())) {
			return $this->failValidationError();
		}
		
		if($type < 0 || $type > 1) {
			return $this->failValidationError();
		}
		
		$insert_id = $AttendanceModel->insert([
			'user_id' => $user_id,
			'type' => $type
		]);
		
		if(is_null($insert_id)) {
			return $this->failServerError();
		} else {
			return $this->respondCreated();
		}
		
		
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

		$teams = $TeamModel->findAll();

		return $this->respond($teams);

		

	}


}