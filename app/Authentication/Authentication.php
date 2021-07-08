<?php namespace App\Authentication;

use App\Models\UserModel;
use Firebase\JWT\JWT;

class Authentication
{

    private $is_logged_in = false;
    private $user_id = null;

    private $JWT_KEY = "WOd9iOaBVa_fj7NQrpUw4j9y4ycZ3SmUHwH-5ItyvF8";

    private $model;

    private $user;

    public function init(\CodeIgniter\HTTP\IncomingRequest $request) {

        $this->model = new UserModel();

        $cookie = $request->getCookie("jwt_token");

        if(!is_null($cookie)) {
            
            try {

                $decoded = JWT::decode($cookie, $this->JWT_KEY, ['HS256']);

                $this->user_id = $decoded->user_id;
                $this->is_logged_in = true;

                $this->user = $this->model->where('id', $this->user_id)->first();

            } catch (\Exception $e) {

                $this->user_id = null;
                $this->is_logged_in = false;

            }

        }


    }

    public function login($place_id, $username, $birthday) {

		$row = $this->model->groupStart()
                                ->orWhere('place_id', null)
                                ->orWhere('place_id', $place_id)
                            ->groupEnd()
                            ->where('username', $username)->where('birthday', $birthday)->first();

		if(is_null($row)) {
            $this->onLoginFailed();
			return false;
		}

		$this->onLoginSuccess($row['id']);

        $this->user = $row;
        		
		return true;

    }

    public function checkMinimumLevel($level) {
        return ($this->user['level'] ?? -1) >= $level;
    }

    private function onLoginFailed() {
        $this->user_id = null;
        $this->is_logged_in = false;
        $this->level = null;
    }

    private function onLoginSuccess($user_id) {
        $this->user_id = $user_id;
        $this->is_logged_in = true;
    }

    public function user() {
        return $this->user;
    }

    public function level() {
        return $this->user['level'];
    }

    public function createJWT() {

        if($this->is_logged_in) {
            $token_info = [
                'user_id' => $this->user_id
            ];
    
            $jwt = JWT::encode($token_info, $this->JWT_KEY);

            return $jwt;
        }
        else {
            return "";
        }

        
    }

    public function is_logged_in() {
        return $this->is_logged_in;
    }

    public function user_id() {
        return $this->user_id;
    }

}