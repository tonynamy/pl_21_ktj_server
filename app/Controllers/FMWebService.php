<?php namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\PlaceModel;
use App\Models\FacilityModel;
use App\Models\TeamModel;
use App\Models\TeamMateModel;
use CodeIgniter\I18n\Time;

class FMWebService extends BaseController
{
    protected $auth;

    public function __construct()
    {
        $this->auth = service('Authentication');
    }

    /*-------------------------------------------경고메세지-------------------------------------------*/    

    public function login_fail() {
        //메인화면 ROUTE
        $default_route = '/fm';
        return redirect()->to($default_route)->withInput()->with('alert', "로그인 후에 접속이 가능합니다.");
    }

    public function alert($message) {
        return redirect()->back()->withInput()->with('alert', $message);
    }

    /*-------------------------------------------로그인-------------------------------------------*/    

    public function index() {

        $PlaceModel = new PlaceModel();
        $places = $PlaceModel->findAll();
        $data = [
            'places' => $places,
        ];

        return view('index.php', $data);
    }

    public function login() {
        
        $place_id = $_POST['place'] ?? null;
		$username = $_POST['name'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

        if($place_id == -1) {
            return $this->alert('현장명을 선택해주세요.');
        }else if($username == '') {
            return $this->alert('아이디를 입력해주세요.');
		} else if ($birthday == '') {
            return $this->alert('비밀번호를 입력해주세요.');
        }

		if($this->auth->login($place_id, $username, $birthday)) {
            
            if($this->auth->user()['level'] == 1) {
                return $this->alert('팀장님은 로그인하실수 없습니다.');
            } else {
                return redirect()->to('/fm/menu')->setcookie("jwt_token", $this->auth->createJWT(), 86500);
            }
            
		} else {
            return $this->alert('로그인에 실패했습니다.');
		}
    }

    /*-------------------------------------------사용자생성-------------------------------------------*/

    public function create_user() {

        $PlaceModel = new PlaceModel();
        $places = $PlaceModel->findAll();
        $data = [
            'places' => $places,
        ];

        return view('create_user.php', $data);
    }

    /*--------------------------------------------메뉴--------------------------------------------*/

    public function menu() {

		if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $PlaceModel = new PlaceModel();
            $login_place = $PlaceModel->where('id', $this->auth->login_place_id())->first();
            $username = $this->auth->user()['username'];
            $role_name = $this->auth->level() == 2? '관리자' : '최고관리자';
            
            $login_info = $login_place['name'] . ' ' . $username . ' ' . $role_name;
            $data = [
                'login_info' =>  $login_info,
            ];

            return view('menu.php', $data);
        }
    }

    /*-------------------------------------------팀등록-------------------------------------------*/

    public function add_team() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            return view('add_team.php');
        }
    }

    public function load_team_excel() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            return $this->alert('아직 기능이 구현되지 않았습니다.');
        }
    }

    public function parse_team_data() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $excel_string = $_POST['excel_string'] ?? null;

            if(is_null($excel_string) || $excel_string === "") {

                return $this->alert('문자열이 비었습니다.');

            }

            $team_names = [];

            $string_by_row = explode(PHP_EOL, $excel_string);

            $info = [];

            $error_data_count = 0;

            foreach($string_by_row as $row) {

                try {

                    $row_data = explode("\t", $row);

                    if(count($row_data) != 3) {
                        $error_data_count++;
                        continue;
                    }

                    $team_name = $row_data[0];
                    $name = $row_data[1];
                    $registration_number = $row_data[2];

                    if($team_name !== "") {
                        array_push($team_names, $team_name);
                    }

                    $birthday = preg_replace('/(?<=[0-9]{6}).+/', '', $registration_number);

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
                return $this->alert('팀정보가 없습니다.');
            }

            $TeamModel = new TeamModel();
            $TeamMateModel = new TeamMateModel();


            //팀이 없을시 팀생성
            foreach($team_names as $team_name_string){

                if(is_null($TeamModel->where('place_id', $this->auth->login_place_id())->where('name', $team_name_string)->first())) {
                    
                    $team_insert_data = [];
                    
                    array_push($team_insert_data, [
                        'place_id' => $this->auth->login_place_id(),
                        'name' => $team_name_string
                    ]);
                    
                    try {
                        $TeamModel->insertBatch($team_insert_data);
                    } catch (\Exception $e) {
                        //return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.\n사유: 팀생성 중 실패');
                    }
                }
            }
            
            
            $teammate_insert_data = [];

            $error_insert_count = 0;

            $last_team_name = "";

            foreach($info as $element) {

                try {

                    //팀이름이 비었을 때 이전 팀이름 가져오기
                    if($element['team_name']!=="") {
                        $current_team_name = $element['team_name'];
                        $last_team_name = $element['team_name'];
                    } else {
                        $current_team_name = $last_team_name;
                    }

                    //팀이름으로 해당현장에서 팀검색
                    $match_team = $TeamModel->where('place_id', $this->auth->login_place_id())->where('name', $current_team_name)->first();

                    //동일팀메이트 있는지 검사
                    $same_teammate = $TeamMateModel->select('teammate.*')
                                                        ->join('team', 'team.id = teammate.team_id and team.place_id = "'. $this->auth->login_place_id() .'"')
                                                        ->where('teammate.name', $element['name'])
                                                        ->where('teammate.birthday', $element['birthday'])
                                                        ->first();
                    

                    if(!is_null($match_team) && is_null($same_teammate)) {

                        array_push($teammate_insert_data, [
                            'team_id' => $match_team['id'],
                            'name' => $element['name'],
                            'birthday' => $element['birthday'],
                        ]);

                    }
                    
                    
                } catch(\Exception $e) {
                    $error_insert_count++;
                    continue;
                }

            }

            try {
                $TeamMateModel->insertBatch($teammate_insert_data);
            } catch (\Exception $e) {
                return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.');
            }

            $message = '데이터 분석 과정 중 '.$error_data_count.'행 오류, 데이터 가공 과정 중 '.$error_insert_count.'행 오류로 총 '.($error_data_count+$error_insert_count).'행 누락되어 삽입되었습니다.';

            return $this->alert($message);

        }
    }

    /*-------------------------------------------도면등록-------------------------------------------*/

    public function add_facility() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            return view('add_facility');
            
        }
    }

    public function load_facility_excel() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            return $this->alert('아직 기능이 구현되지 않았습니다.');
        }
    }

    public function parse_facility_data() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $FacilityModel = new FacilityModel();

            $excel_string = $_POST['excel_string'] ?? null;

            if(is_null($excel_string) || $excel_string === "") {

                return $this->alert('문자열이 비었습니다.');

            }
            
            $string_by_row = explode(PHP_EOL, $excel_string);

            $info = [];

            foreach($string_by_row as $row) {
                
                try {

                    $row_data = explode("\t", $row);

                    if(count($row_data) < 7 || count($row_data) > 21) {
                        continue;
                    }

                    $serial = $row_data[0];

                    $type = 4;
                    switch($row_data[1]) {
                        case "설비": 
                            $type = 1;
                            break;
                        case "전기":
                            $type = 2;
                            break;
                        case "건축":
                            $type = 3;
                            break;
                        default:
                            $type = 4;
                            break;
                    }
                    $super_manager = $row_data[2];
                    $subcontractor = $row_data[3];
                    $building = $row_data[4];
                    $floor = $row_data[5];
                    $spot = $row_data[6];

                    $section = 0;
                    switch($row_data[7]) {
                        case "사내":
                            $section = 1;
                            break;
                        case "사외":
                            $section = 2;
                            break;
                        default:
                            $section = 0;
                            break;
                    }

                    $purpose = $row_data[8] ?? '';
                    $cube_data = $row_data[9] ?? null;
                    $cube_result = $row_data[10] ?? null;
                    $area_data = $row_data[11] ?? null;
                    $area_result = $row_data[12] ?? null;
                    $created_at = $row_data[13] ?? null;
                    $started_at = $row_data[14] ?? null;
                    $finished_at = $row_data[15] ?? null;
                    $edit_started_at = $row_data[16] ?? null;
                    $edit_finished_at = $row_data[17] ?? null;
                    $dis_started_at = $row_data[18] ?? null;
                    $dis_finished_at = $row_data[19] ?? null;
                    $memo = $row_data[20] ?? null;

                    //TRIM
                    $serial = trim($serial);
                    $super_manager = trim($super_manager);
                    $subcontractor = str_replace(', ' , ',', trim($subcontractor));
                    $building = trim($building);
                    $floor = trim($floor);
                    $spot = trim($spot);
                    $purpose = trim($purpose);
                    $cube_data = trim($cube_data);
                    $cube_result = trim($cube_result);
                    $area_data = trim($area_data);
                    $area_result = trim($area_result);
                    $created_at = trim($created_at);
                    $finished_at = trim($finished_at);
                    $edit_started_at = trim($edit_started_at);
                    $edit_finished_at = trim($edit_finished_at);
                    $dis_started_at = trim($dis_started_at);
                    $dis_finished_at = trim($dis_finished_at);
                    $memo = trim($memo);

                    //null값허용항목 처리
                    $cube_data = $cube_data == '' ? null : $cube_data;
                    $cube_result = $cube_result == '' ? null : $cube_result;
                    $area_data = $area_data == '' ? null : $area_data;
                    $area_result = $area_result == '' ? null : $area_result;
                    $area_result = $area_result == '' ? null : $area_result;
                    $created_at = $created_at == '' ? null : $created_at;
                    $started_at = $started_at == '' ? null : $started_at;
                    $finished_at = $finished_at == '' ? null : $finished_at;
                    $edit_started_at = $edit_started_at == '' ? null : $edit_started_at;
                    $edit_finished_at = $edit_finished_at == '' ? null : $edit_finished_at;
                    $dis_started_at = $dis_started_at == '' ? null : $dis_started_at;
                    $dis_finished_at = $dis_finished_at == '' ? null : $dis_finished_at;
                    $memo = $memo == '' ? null : $memo;

                    //필수정보 없을시 통과
                    if($serial === "" || $type === 0 || $subcontractor === "" || $building === "" || $floor === "" || $spot === "") {
                        continue;
                    }
                    
                    //해당 현장에 동일 시리얼번호가 있는지 확인
                    $same_facility = $FacilityModel->where('place_id', $this->auth->login_place_id())
                                                    ->where('serial', $serial)
                                                    ->first();

                    if(is_null($same_facility)) {

                        $data = [
                            'place_id' => $this->auth->login_place_id(),
                            'serial' => $serial,
                            'type' => $type,
                            'super_manager' => $super_manager,
                            'subcontractor' => $subcontractor,
                            'building' => $building,
                            'floor' => $floor,
                            'spot' => $spot,
                            'section' => $section,
                            'purpose' => $purpose,
                            'cube_data' => $cube_data,
                            'cube_result' => $cube_result,
                            'area_data' => $area_data,
                            'area_result' => $area_result,
                            'started_at' => $started_at,
                            'finished_at' => $finished_at,
                            'edit_started_at' => $edit_started_at,
                            'edit_finished_at' => $edit_finished_at,
                            'dis_started_at' => $dis_started_at,
                            'dis_finished_at' => $dis_finished_at,
                            'memo' => $memo,
                        ];

                        if($created_at != null) {
                            $data['created_at'] = $created_at;
                        }

                        array_push($info, $data);
                    }


                } catch (\Exception $e) {
                    continue;
                }

            }

            /*
            foreach($info as $dump_data){
                var_dump($dump_data);
                echo '<br>';
            }
            exit;
            */

            try {
                $FacilityModel->insertBatch($info);
            } catch (\Exception $e) {
                return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.');
            }

            return $this->alert('업로드 성공');

        }
    }

    /*-------------------------------------------출퇴근조회-------------------------------------------*/

    public function view_attendance($team_id=null, $_target_time=null) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $attendance_dates = [];
            $attendance_data = [];
            $attendance_teammates = [];

            if($team_id != null) {

                if($_target_time == null || !is_numeric($_target_time)) {
                    $target_time = Time::now();
                } else {
                    $target_time = Time::createFromTimestamp($_target_time);
                }

                // 일 : 1
                // 월 : 2
                // 화 : 3
                // 수 : 4
                // 목 : 5
                // 금 : 6
                // 토 : 7

                $day_of_week = $target_time->getDayOfWeek();

                $start_time = $target_time->subDays($day_of_week-1);
                $end_time = $target_time->addDays(7-$day_of_week+1);

                $start_time = $start_time->setHour(0)->setMinute(0)->setSecond(0);
                $end_time = $end_time->setHour(23)->setMinute(59)->setSecond(59);

                $AttendanceModel = new AttendanceModel();

                $attendanceRecords = $AttendanceModel->select('attendance.*, tm.name as teammate_name, tm.birthday as teammate_birthday')
                                                    ->join('teammate as tm', 'attendance.teammate_id = tm.id')
                                                    ->join('team as t', 'tm.team_id = t.id')
                                                    ->where('t.id', $team_id)
                                                    ->where('attendance.created_at >= ', $start_time)
                                                    ->where('attendance.created_at <= ', $end_time)
                                                    ->orderBy('tm.name', 'asc')
                                                    ->findAll();

                $TeamMateModel = new TeamMateModel();
                $attendance_teammates = $TeamMateModel->where('team_id', $team_id)->findAll();
                
                for($i=0;$i<7;$i++) {

                    $index_date = $start_time->addDays($i);

                    array_push($attendance_dates, $index_date);

                    $current_attnedance_records = array_values(array_filter($attendanceRecords, function($record) use ($index_date) {

                        $created_at = Time::createFromFormat('Y-m-d H:i:s', $record['created_at']);

                        return $created_at->isAfter($index_date) && $created_at->isBefore($index_date->setHour(23)->setMinute(59)->setSecond(59));

                    }));
                    
                    array_push($attendance_data, $current_attnedance_records);

                }


            }

            $TeamModel = new TeamModel();

            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();
            $data = [
                'this_team' => $team_id, 
                
                'teams'=> $teams,

                'attendance_dates' => $attendance_dates,
                'attendance_data' => $attendance_data,
                'attendance_teammates' => $attendance_teammates,
            ];

            return view('view_attendance.php', $data);
            
        }
    }

    public function selet_team() {

    }

    public function save_attendance_button() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            //$selected_team = $_POST['team'] ?? null;

            return $this->alert('아직 기능이 구현되지 않았습니다.');
            
        }
    }






}
