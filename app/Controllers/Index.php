<?php namespace App\Controllers;

use App\Models\FacilityModel;
use App\Models\PlaceModel;
use App\Models\TeamMateModel;
use App\Models\TeamModel;

use function PHPUnit\Framework\isEmpty;

class Index extends BaseController
{

    protected $auth;

	public function __construct()
	{
		$this->auth = service('Authentication');
	}

    public function index() {

        $PlaceModel = new PlaceModel();

        $places = $PlaceModel->findAll();

        $data = [
            'places' => $places,
        ];

        return view('index', $data);

    }

    public function login()
    {
		$place_id = $_POST['place'] ?? null;
		$username = $_POST['name'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

		if(is_null($username) || $username == '' || is_null($birthday)) {
			return redirect()->back()->withInput()->with('error', '로그인 실패');;
		}

        if($place_id<0) {
            $place_id = null;
        }

		if($this->auth->login($place_id, $username, $birthday)) {
            
            return redirect()->to('/menu')->setcookie("jwt_token", $this->auth->createJWT(), 86500);
		
		} else {
			return redirect()->back()->withInput()->with('error', '로그인 실패');
		}
    }

    public function menu() {

        if(!$this->auth->is_logged_in()) {
            return redirect()->to('/login')->withInput()->with('error', '로그인이 필요합니다');
        }

        return view('menu');

    }

    public function register_team() {

        if(!$this->auth->is_logged_in()) {
            return redirect()->to('/login')->withInput()->with('error', '로그인이 필요합니다');
        }

        if($this->request->getMethod() == 'post') {

            $excel_string = $_POST['excel_string'] ?? null;

            if(is_null($excel_string) || $excel_string === "") {
                return redirect()->back()->with('error', '문자열이 비었습니다.');
            }

            $team_names = [];

            $string_by_row = explode(PHP_EOL, $excel_string);

            $info = [];

            $error_data_count = 0;

            foreach($string_by_row as $row) {

                try {

                    $row_data = explode("\t", $row);

                    if(count($row_data) < 3) {
                        $error_data_count++;
                        continue;
                    }

                    $team_name = $row_data[0];
                    $name = $row_data[1];
                    $registration_number = $row_data[2];

                    if($team_name !== "") {
                        array_push($team_names, $team_name);
                    }

                    $birthday = explode('-', $registration_number)[0];

                    array_push($info, [
                        'team_name' => $team_name,
                        'name' => $name,
                        'birthday' => $birthday,
                    ]);
                    
                } catch (\Exception $e) {
                    $error_data_count ++ ;
                    continue;
                }

            }

            $team_names = array_values(array_unique($team_names));

            if(count($team_names) == 0) {
                return redirect()->back()->with('error', "팀 이름 분석 오류입니다.");
            }

            $TeamModel = new TeamModel();
            $TeamMateModel = new TeamMateModel();

            $teams = $TeamModel->whereIn('name', $team_names)->findAll();

            $teammate_insert_data = [];

            $error_insert_count = 0;

            $last_team_name = "";

            foreach($info as $element) {

                try {

                    $current_team_name = $element['team_name'] === "" ? $last_team_name : $element['team_name'];

                    if($element['team_name']!=="") {
                        $last_team_name = $element['team_name'];
                    }

                    $team = array_filter($teams, function($team) use ($current_team_name) {

                        return $team['name'] == $current_team_name;

                    });

                    if(count($team) == 0) {
                        $error_insert_count++;
                        continue;
                    } 

                    $team = $team[0];
                    
                    array_push($teammate_insert_data, [
                        'team_id' => $team['id'],
                        'name' => $element['name'],
                        'birthday' => $element['birthday'],
                    ]);

                } catch(\Exception $e) {
                    $error_insert_count++;
                    continue;
                }

            }

            try {
                $TeamMateModel->insertBatch($teammate_insert_data);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', "데이터 삽입 과정 중 오류가 발생하였습니다.");
            }

            $message = '데이터 분석 과정 중 '.$error_data_count.'행 오류, 데이터 가공 과정 중 '.$error_insert_count.'행 오류로 총 '.($error_data_count+$error_insert_count).'행 누락되어 삽입되었습니다.';

            return redirect()->back()->with('error', $message);


        } else {

            return view('register_team');

        }

    }

}