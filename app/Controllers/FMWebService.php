<?php namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\PlaceModel;
use App\Models\FacilityModel;
use App\Models\TeamModel;
use App\Models\TeamMateModel;
use App\Models\UserModel;
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

        if($this->auth->is_logged_in()) {

			return redirect()->to('/fm/menu');

        } else {

            $PlaceModel = new PlaceModel();
            $places = $PlaceModel->findAll();
            $data = [
                'places' => $places,
            ];

            return view('index.php', $data);
        }
    }

    public function login() {
        
        $place_id = $_POST['place'] ?? null;
		$username = $_POST['name'] ?? null;
		$birthday = $_POST['birthday'] ?? null;

        if($place_id == '') {
            return $this->alert('현장명을 선택해주세요.');
        }else if($username == '') {
            return $this->alert('아이디를 입력해주세요.');
		} else if ($birthday == '') {
            return $this->alert('비밀번호를 입력해주세요.');
        }

		if($this->auth->login($place_id, $username, $birthday)) {
            
            if($this->auth->user()['level'] != 1 && $this->auth->user()['level'] != 0) {
                return redirect()->to('/fm/menu')->setcookie("jwt_token", $this->auth->createJWT(), 86500);
            } else if($this->auth->user()['level'] == 1) {
                return $this->alert('팀장님은 로그인하실 수 없습니다.');
            } else {
                return $this->alert('관리자의 승인을 기다리고 있습니다.');
            }
            
		} else {
            return $this->alert('로그인에 실패했습니다.');
		}
    }

    public function logout() {
        
        if($this->auth->is_logged_in()) {

			return redirect()->to('/fm')->deleteCookie("jwt_token");

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

    public function generate_user() {

        $place_id = $_POST['place'] ?? null;
		$username = $_POST['name'] ?? null;
		$birthday = $_POST['birthday_calender'] ?? null;

        if(preg_match('/[0-9]{4}-[0-9]{2}-[0-9]{2}/', $birthday, $birthday_preg)) {
            $birthday = $birthday_preg[0];
        }

        if($place_id == '') {
            return redirect()->back()->withInput()->with('alert', '현장명을 선택해주세요.')->with('birthday', $birthday);
        }else if($username == '') {
            return redirect()->back()->withInput()->with('alert', '이름을 입력해주세요.')->with('birthday', $birthday);
		} else if ($birthday == '') {
            return $this->alert('생년월일을 선택해주세요.');
        }


        //생년월일 손질
        $birthday_arr = explode("-", $birthday);
        $year = substr($birthday_arr[0], -2);
        $birthday_data = $year . $birthday_arr[1] . $birthday_arr[2];

        $new_user_data = [
            'place_id' => $place_id,
            'username' => $username,
            'birthday' => $birthday_data,
        ];

        $UserModel = new UserModel();

        if(!is_null($UserModel->where('place_id', $place_id)->where('username', $username)->where('birthday', $birthday_data)->first())) {
            return redirect()->back()->withInput()->with('alert', '같은 사용자가 이미 있습니다.')->with('birthday', $birthday);
        }
           
        try {
            $UserModel->insert($new_user_data);
        } catch (\Exception $e) {
            return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.\n사유: 유저생성실패');
        }

        return redirect()->to('/fm')->withInput()->with('alert', "사용자가 생성되었습니다.");
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
                    $section = $row_data[7];

                    $purpose = $row_data[8] ?? "";
                    $cube_data = $row_data[9] ?? "";
                    $cube_result = $row_data[10] ?? "";
                    $area_data = $row_data[11] ?? "";
                    $area_result = $row_data[12] ?? "";
                    $created_at = $row_data[13] ?? "";
                    $started_at = $row_data[14] ?? "";
                    $finished_at = $row_data[15] ?? "";
                    $edit_started_at = $row_data[16] ?? "";
                    $edit_finished_at = $row_data[17] ?? "";
                    $dis_started_at = $row_data[18] ?? "";
                    $dis_finished_at = $row_data[19] ?? "";
                    $memo = $row_data[20] ?? "";

                    //TRIM
                    $serial = trim($serial);
                    $super_manager = trim($super_manager);
                    $subcontractor = str_replace(', ' , ',', trim($subcontractor));
                    $building = trim($building);
                    $floor = trim($floor);
                    $spot = trim($spot);
                    $section = trim($section);
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
                    $cube_data = $cube_data == "" ? null : $cube_data;
                    $cube_result = $cube_result == "" ? null : $cube_result;
                    $area_data = $area_data == "" ? null : $area_data;
                    $area_result = $area_result == "" ? null : $area_result;
                    $area_result = $area_result == "" ? null : $area_result;
                    $created_at = $created_at == "" ? null : $created_at;
                    $started_at = $started_at == "" ? null : $started_at;
                    $finished_at = $finished_at == "" ? null : $finished_at;
                    $edit_started_at = $edit_started_at == "" ? null : $edit_started_at;
                    $edit_finished_at = $edit_finished_at == "" ? null : $edit_finished_at;
                    $dis_started_at = $dis_started_at == "" ? null : $dis_started_at;
                    $dis_finished_at = $dis_finished_at == "" ? null : $dis_finished_at;
                    $memo = $memo == "" ? null : $memo;

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

                        if(!is_null($created_at)) {
                            $data['created_at'] = $created_at;
                        } else {
                            $data['created_at'] = Time::now()->toDateString();
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

    public function view_attendance($team_id = null, $_target_time=null) {

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

                $start_time = $target_time->subDays($day_of_week-1)->setHour(5)->setMinute(0)->setSecond(0);
                $end_time = $start_time->addDays(7);
                
                $AttendanceModel = new AttendanceModel();

                $attendanceRecords = $AttendanceModel->select('attendance.*, tm.name as teammate_name, tm.birthday as teammate_birthday')
                                                    ->join('teammate as tm', 'attendance.teammate_id = tm.id')
                                                    ->join('team as t', 'tm.team_id = t.id')
                                                    ->where('t.id', $team_id)
                                                    ->where('attendance.created_at >=', $start_time)
                                                    ->where('attendance.created_at <', $end_time)
                                                    ->findAll();

                $TeamMateModel = new TeamMateModel();
                $attendance_teammates = $TeamMateModel->where('team_id', $team_id)->orderBy('name', 'ASC')->findAll();

                for($i=0; $i<7; $i++) {

                    $index_date = $start_time->addDays($i);

                    array_push($attendance_dates, $index_date);

                    $current_attendance_records = array_values(array_filter($attendanceRecords, function($record) use ($index_date) {

                        $created_at = Time::createFromFormat('Y-m-d H:i:s', $record['created_at']);
                        return $created_at >= $index_date && $created_at < $index_date->addDays(1);

                    }));

                    array_push($attendance_data, $current_attendance_records);

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

    public function change_name() {

        $teammate_id = $_POST['teammate_id'] ?? null;
        $new_name = $_POST['new_name'] ?? null;

        $TeamMateModel = new TeamMateModel();

        $TeamMateModel->set('name', $new_name)->where('id', $teammate_id)->update();

        return redirect()->back()->with('alert', $new_name.'으로 이름이 변경되었습니다.');

    }

    public function save_attendance_button() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            //$selected_team = $_POST['team'] ?? null;

            return $this->alert('아직 기능이 구현되지 않았습니다.');
            
        }
    }

    /*-------------------------------------------시설물조회-------------------------------------------*/

    public function view_facility($state = null) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $state_array = [];

            if(!is_null($state)) {
                $state_array = str_split($state); 
                $state_num = implode(",", $state_array);
            }

            $FacilityModel = new FacilityModel();

            $search_serial = $_POST['search_serial'] ?? null;

            if(!is_null($search_serial)) {
                $FacilityModel->like('serial', $search_serial);
            }

            $facilities_1 = [];
            $facilities_2 = [];
            $facilities_3 = [];
            $facilities_4 = [];

            $facilities_a = [];
            $facilities_b = [];
            $facilities_c = [];
            $facilities_d = [];
            $facilities_e = [];
            $facilities_f = [];
            $facilities_g = [];

            $facilities_type = [];
            $is_type = false;

            $types = [1, 2, 3, 4];

            foreach($types as $type) {

                if(in_array(strval($type), $state_array)) {

                    $facilities = $FacilityModel->where('place_id', $this->auth->login_place_id())->where('type', $type)->findAll();

                    foreach($facilities as $facility) {

                        array_push($facilities_type, $facility);

                    }

                    $is_type = true;


                }
            }

            if(!$is_type) {
                $facilities_type = $FacilityModel->where('place_id', $this->auth->login_place_id())->findAll();
            }
            
            //진행상황별
            if(in_array("a", $state_array) || in_array("b", $state_array) || in_array("c", $state_array) || in_array("d", $state_array) || in_array("e", $state_array) || in_array("f", $state_array) || in_array("g", $state_array)) {

                if(in_array("a", $state_array)) {

                    $facilities_a = array_values(array_filter($facilities_type, function($facility) {
    
                        $started_at = $facility['started_at'];
                        $finished_at = $facility['finished_at'];
                        $edit_started_at = $facility['edit_started_at'];
                        $edit_finished_at = $facility['edit_finished_at'];
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return is_null($started_at) && is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("b", $state_array)) {

                    $facilities_b = array_values(array_filter($facilities_type, function($facility) {
    
                        $started_at = $facility['started_at'];
                        $finished_at = $facility['finished_at'];
                        $edit_started_at = $facility['edit_started_at'];
                        $edit_finished_at = $facility['edit_finished_at'];
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($started_at) && is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("c", $state_array)) {

                    $facilities_c = array_values(array_filter($facilities_type, function($facility) {
    
                        $finished_at = $facility['finished_at'];
                        $edit_started_at = $facility['edit_started_at'];
                        $edit_finished_at = $facility['edit_finished_at'];
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($finished_at) && is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("d", $state_array)) {

                    $facilities_d = array_values(array_filter($facilities_type, function($facility) {
    
                        $edit_started_at = $facility['edit_started_at'];
                        $edit_finished_at = $facility['edit_finished_at'];
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($edit_started_at) && is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("e", $state_array)) {

                    $facilities_e = array_values(array_filter($facilities_type, function($facility) {
    
                        $edit_finished_at = $facility['edit_finished_at'];
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($edit_finished_at) && is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("f", $state_array)) {

                    $facilities_f = array_values(array_filter($facilities_type, function($facility) {
    
                        $dis_started_at = $facility['dis_started_at'];
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($dis_started_at) && is_null($dis_finished_at);
    
                    }));
                }
                if(in_array("g", $state_array)) {

                    $facilities_g = array_values(array_filter($facilities_type, function($facility) {
    
                        $dis_finished_at = $facility['dis_finished_at'];
    
                        return !is_null($dis_finished_at);
    
                    }));
                }

                $facilities_data = array_merge($facilities_a, $facilities_b, $facilities_c, $facilities_d, $facilities_e, $facilities_f, $facilities_g);
                $facilities = array_unique($facilities_data, SORT_REGULAR);

            } else {

                $facilities = array_unique($facilities_type, SORT_REGULAR);

            }

            //승인번호순으로 정렬
            if(count($facilities) > 0) {

                foreach((array)$facilities as $key => $value) {
                    $sort[$key] = $value['serial'];
                }
                array_multisort($sort, SORT_ASC, $facilities);

            }
            
            /*
            array_sort_by_multiple_keys($facilities, [
                'serial' => SORT_ASC,
            ]);
            */

            $subcontractors = [];

            foreach($facilities as $facility) {

                array_push($subcontractors,  $facility['subcontractor']);
            }

            $subcontractors = array_unique($subcontractors);
            asort($subcontractors);
            
            $data = [

                'facilities' => $facilities,

                'state' => $state_num ?? '',

                'subcontractors' => $subcontractors,

                'search_serial' => $search_serial,
                
            ];

            return view('view_facility', $data);
            
        }
    }

}
