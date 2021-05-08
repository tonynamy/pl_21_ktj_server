<?php

namespace App\Controllers;

use App\Models\AttendanceModel;
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

    public function index()
    {

		$username = $_POST['username'] ?? null;
		$password = $_POST['password'] ?? null;

		
		if($this->auth->login($username, $password)) {
			return $this->respond([])->setcookie("jwt_token", $this->auth->createJWT());
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

		$attendances = $AttendanceModel->where('created_at >', $st)->where('user_id', $user_id)->findAll();

		return $this->respond(count($attendances));
		

	}


}