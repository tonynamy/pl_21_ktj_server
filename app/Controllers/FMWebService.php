<?php namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\PlaceModel;
use App\Models\FacilityModel;
use App\Models\TaskModel;
use App\Models\TaskPlanModel;
use App\Models\TeamModel;
use App\Models\TeamMateModel;
use App\Models\UserModel;
use CodeIgniter\Database\BaseBuilder;
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

        $FacilityModel = new FacilityModel();
        $FacilityModel->where('place_id', $place_id);

		if($this->auth->login($place_id, $username, $birthday)) {
            
            if($this->auth->user()['level'] != 1 && $this->auth->user()['level'] != 0) {
                return redirect()->to('/fm/menu')->setcookie("jwt_token", $this->auth->createJWT(), 86500);
            } else if($this->auth->user()['level'] == 1) {
                return $this->alert('팀장님은 로그인하실 수 없습니다.');
            } else {
                return $this->alert('관리자의 승인을 기다리고 있습니다.');
            }
            
		} else if(!is_null($FacilityModel->like('super_manager', $username)->first())) {
            return redirect()->to('/fm/menu')->setcookie("jwt_token", $this->auth->createJWT(true, $username), 86500);

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

		if(!$this->auth->is_logged_in(true) && !$this->auth->is_logged_in(false)) {

			return $this->login_fail();

        } else{

            $PlaceModel = new PlaceModel();
            $placename = $PlaceModel->where('id', $this->auth->login_place_id())->first()['name'];

            if($this->auth->is_logged_in(true)){
                $id = null;
                $username = $this->auth->supermanager();
                $level = null;
            } else {
                $id = $this->auth->user()['id'];
                $username = $this->auth->user()['username'];
                $level = $this->auth->level();
            }
            
            $data = [
                'id' => $id,
                'placename' => $placename,
                'username' => $username,
                'level' => $level,
            ];

            return view('menu.php', $data);
        }
    }

    public function change_password() {

        $id = $_POST['id'] ?? null;
        $new_birthday = $_POST['new_birthday'] ?? null;

        if($id == null || $id == 0 || $id == "") {
            return $this->alert('잘못된 요청입니다.');

        }else if($new_birthday == null || $new_birthday == "") {
            return $this->alert('변경할 비밀번호가 입력되지 않았습니다.');

        }else if(!preg_match("/[0-9]{8}+/", $new_birthday)) {
            return $this->alert('비밀번호는 8자리 이상의 숫자만 가능합니다.');
        }

        $UserModel = new UserModel();
        $UserModel->set('birthday', $new_birthday)->where('id', $id)->update();

        return $this->alert('비밀번호가 변경되었습니다.');

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
            $TaskPlanModel = new TaskPlanModel();

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

                    $serial_arr = explode("-", $serial);

                    if(count($serial_arr) > 1 && is_numeric(end($serial_arr)) && intval(substr(end($serial_arr), 0, 1)) != 0 && is_numeric($serial_arr[count($serial_arr)-2])) {
                        $r_num = array_pop($serial_arr);
                        $o_serial = implode("-", $serial_arr);
                    } else {
                        $r_num = 0;
                        $o_serial = $serial;
                    }

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
                    $danger_data = $row_data[13] ?? "";
                    $danger_result = $row_data[14] ?? "";
                    $created_at = $row_data[15] ?? "";
                    $started_at = $row_data[16] ?? "";
                    $finished_at = $row_data[17] ?? "";
                    $edit_started_at = $row_data[18] ?? "";
                    $edit_finished_at = $row_data[19] ?? "";
                    $dis_started_at = $row_data[20] ?? "";
                    $dis_finished_at = $row_data[21] ?? "";
                    $memo = $row_data[22] ?? "";

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
                    $danger_data = trim($danger_data);
                    $danger_result = trim($danger_result);
                    $created_at = trim($created_at);
                    $finished_at = trim($finished_at);
                    $edit_started_at = trim($edit_started_at);
                    $edit_finished_at = trim($edit_finished_at);
                    $dis_started_at = trim($dis_started_at);
                    $dis_finished_at = trim($dis_finished_at);
                    $memo = trim($memo);

                    //null값허용항목 처리
                    $created_at = $created_at == "" ? null : $created_at;
                    $started_at = $started_at == "" ? null : $started_at;
                    $finished_at = $finished_at == "" ? null : $finished_at;
                    $edit_started_at = $edit_started_at == "" ? null : $edit_started_at;
                    $edit_finished_at = $edit_finished_at == "" ? null : $edit_finished_at;
                    $dis_started_at = $dis_started_at == "" ? null : $dis_started_at;
                    $dis_finished_at = $dis_finished_at == "" ? null : $dis_finished_at;
                    $expired_at = null;
                    $memo = $memo == "" ? null : $memo;

                    //필수정보 없을시 통과
                    if($serial === "" || $type === 0 || $subcontractor === "" || $building === "" || $floor === "" || $spot === "") {
                        continue;
                    }
                    
                    $state_column = [

                        'created_at',
                        'started_at',
                        'finished_at',
                        'edit_started_at',
                        'edit_finished_at',
                        'dis_started_at',
                        'dis_finished_at',
                        'expired_at',
            
                    ];

                    //해당 현장에 동일 시리얼번호가 있는지 확인
                    $same_facility = $FacilityModel->where('place_id', $this->auth->login_place_id())
                                                    ->where('serial', $serial)
                                                    ->first();

                    //원도면의 작업계획 모두 찾기
                    $taskplans = $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $o_serial)->findAll();

                    //현도면의 설치단계
                    //for문으로 하고싶은데 $created_at 같은 변수를 어떻게 배열에 넣는지 모르겠네요
                    if(!is_null($dis_finished_at)) {
                        $this_facility_state = 6;
                    } else if(!is_null($dis_started_at)) {
                        $this_facility_state = 5;
                    } else if(!is_null($edit_finished_at)) {
                        $this_facility_state = 4;
                    } else if(!is_null($edit_started_at)) {
                        $this_facility_state = 3;
                    } else if(!is_null($finished_at)) {
                        $this_facility_state = 2;
                    } else if(!is_null($started_at)) {
                        $this_facility_state = 1;
                    } else {
                        $this_facility_state = 0;
                    }

                    //신규도면의 진행단계가 더 높을때는 작업계획이 삭제된다
                    foreach($taskplans as $taskplan) {
                        if($taskplan['type'] == 1 && $this_facility_state >= 2) {
                            $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $o_serial)->where('type', 1)->delete(null, true);
                        } else if($taskplan['type'] == 2 && $this_facility_state >= 4) {
                            $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $o_serial)->where('type', 2)->delete(null, true);
                        } else if($this_facility_state == 6) {
                            $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $o_serial)->delete(null, true);
                        }
                    }


                    //같은 원도면을 공유하는 도면 전부찾기
                    $previous_facilities = $FacilityModel->where('place_id', $this->auth->login_place_id())->where('o_serial', $o_serial)->findAll();

                    
                    //원도면에 값이 있고 신규등록하는 도면에 값이 없으면 원도면의 값을 대입
                    //for문으로 돌리고 싶은데 $started_at, $finished_at 같은 변수들은 어떻게 $i에 대입해야할지 모르겠네요
                    for($i = count($previous_facilities)-1; $i >= 0; $i--) {

                        $previous_facility = $previous_facilities[$i];

                        if(!is_null($previous_facility['started_at']) && is_null($started_at)) {
                            $started_at = $previous_facility['started_at'];
                        }
                        if(!is_null($previous_facility['finished_at']) && is_null($finished_at)) {
                            $finished_at = $previous_facility['finished_at'];
                        }
                        if(!is_null($previous_facility['edit_started_at']) && is_null($edit_started_at)) {
                            $edit_started_at = $previous_facility['edit_started_at'];
                        }
                        if(!is_null($previous_facility['edit_finished_at']) && is_null($edit_finished_at)) {
                            $edit_finished_at = $previous_facility['edit_finished_at'];
                        }
                        if(!is_null($previous_facility['dis_started_at']) && is_null($dis_started_at)) {
                            $dis_started_at = $previous_facility['dis_started_at'];
                        }
                        if(!is_null($previous_facility['dis_finished_at']) && is_null($dis_finished_at)) {
                            $dis_finished_at = $previous_facility['dis_finished_at'];
                        }
                        if(!is_null($previous_facility['expired_at']) && is_null($expired_at)) {
                            $expired_at = $previous_facility['expired_at'];
                        }
                    }
                    
                    //원도면에 값이 없고 신규등록하는 도면에 값이 있으면 신규도면의 값을 대입
                    //IS NULL 이 아니라 예를들어 기존 $started_at이 새로 들어온 $started_at과 다를때 대입하게 바꾸기
                    if(!is_null($started_at) || !is_null($finished_at) || !is_null($edit_started_at) || !is_null($edit_finished_at) || !is_null($dis_started_at) || !is_null($dis_finished_at)) {

                        $FacilityModel->where('place_id', $this->auth->login_place_id())->where('o_serial', $o_serial);
                        if(!is_null($started_at)) {
                            $FacilityModel->set('started_at', "IF(started_at IS NULL, '" . $started_at . "', started_at)", false);
                        }
                        if(!is_null($finished_at)) {
                            $FacilityModel->set('finished_at', "IF(finished_at IS NULL, '" . $finished_at . "', finished_at)", false);
                        }
                        if(!is_null($edit_started_at)) {
                            $FacilityModel->set('edit_started_at', "IF(edit_started_at IS NULL, '" . $edit_started_at . "', edit_started_at)", false);
                        }
                        if(!is_null($edit_finished_at)) {
                            $FacilityModel->set('edit_finished_at', "IF(edit_finished_at IS NULL, '" . $edit_finished_at . "', edit_finished_at)", false);
                        }
                        if(!is_null($dis_started_at)) {
                            $FacilityModel->set('dis_started_at', "IF(dis_started_at IS NULL, '" . $dis_started_at . "', dis_started_at)", false);
                        }
                        if(!is_null($dis_finished_at)) {
                            $FacilityModel->set('dis_finished_at', "IF(dis_finished_at IS NULL, '" . $dis_finished_at . "', dis_finished_at)", false);
                        }
                        $FacilityModel->update();
                    }
                    
                    if(is_null($same_facility)) {

                        $data = [
                            'place_id' => $this->auth->login_place_id(),
                            'serial' => $serial,
                            'o_serial' => $o_serial,
                            'r_num' => $r_num,
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
                            'danger_data' => $danger_data,
                            'danger_result' => $danger_result,
                            'started_at' => $started_at,
                            'finished_at' => $finished_at,
                            'edit_started_at' => $edit_started_at,
                            'edit_finished_at' => $edit_finished_at,
                            'dis_started_at' => $dis_started_at,
                            'dis_finished_at' => $dis_finished_at,
                            'expired_at' => $expired_at,
                            'memo' => $memo,
                        ];

                        if(!is_null($created_at)) {
                            $data['created_at'] = $created_at;
                        } else {
                            $data['created_at'] = Time::now();
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

    public function change_teammate_name() {

        $teammate_id = $_POST['teammate_id'] ?? null;
        $new_name = $_POST['new_name'] ?? null;

        if($teammate_id == null || $teammate_id == 0 || $teammate_id =="") {
            return $this->alert('잘못된 요청입니다.');
            
        } else if($new_name == null || $new_name == "") {
            return $this->alert('바꿀 이름이 입력되지 않았습니다.');

        } else if(mb_strlen($new_name) < 2) {
            return $this->alert('이름은 최소 두글자 이상이어야합니다.');
        }

        $TeamMateModel = new TeamMateModel();
        $TeamMateModel->set('name', $new_name)->where('id', $teammate_id)->update();

        return redirect()->back()->with('alert', $new_name . '(으)로 이름이 변경되었습니다.');

    }

    public function change_teammate_birthday() {

        $teammate_id = $_POST['teammate_id'] ?? null;
        $new_birthday = $_POST['new_birthday'] ?? null;

        if($teammate_id == null || $teammate_id == 0 || $teammate_id =="") {
            return $this->alert('잘못된 요청입니다.');
            
        } else if($new_birthday == null || $new_birthday == "") {
            return $this->alert('바꿀 생년월일이 입력되지 않았습니다.');

        } else if(!preg_match("/[0-9]{6}/", $new_birthday)) {
            return $this->alert('생년월일 양식이 잘못되었습니다.');

        } else {
            $b_month = substr($new_birthday, 2, 2);
            $b_day = substr($new_birthday, 4, 2);
            $short_month = [2, 4, 6, 9, 11];

            if($b_month > 12 || $b_day > 31) {
                return $this->alert('생년월일 양식이 잘못되었습니다.');

            } else if (in_array($b_month, $short_month) && $b_day > 30) {
                return $this->alert('생년월일 양식이 잘못되었습니다.');

            } else if($b_month == 2 && $b_day > 29) {
                return $this->alert('생년월일 양식이 잘못되었습니다.');
            } 
        }

        $TeamMateModel = new TeamMateModel();
        $TeamMateModel->set('birthday', $new_birthday)->where('id', $teammate_id)->update();

        return redirect()->back()->with('alert', $new_birthday . '(으)로 생년월일이 변경되었습니다.');

    }

    public function save_attendance_button() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            //$selected_team = $_POST['team'] ?? null;

            return $this->alert('아직 기능이 구현되지 않았습니다.');
            
        }
    }

    /*-------------------------------------------작업조회-------------------------------------------*/

    public function view_facility($state = null) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $state_array = [];

            if(!is_null($state)) {
                $state_array = str_split($state); 
                $state_num = implode(",", $state_array);
            }
            
            $search_serial = $_POST['search_serial'] ?? null;

            $FacilityModel = new FacilityModel();
            $FacilityModel2 = new FacilityModel();

            $FacilityModel->select('facility.*')->where('facility.place_id', $this->auth->login_place_id());
            $FacilityModel2->select('facility.*')->where('facility.place_id', $this->auth->login_place_id());

            if(in_array("o", $state_array) || in_array("r", $state_array)) {
                $FacilityModel->groupStart();
            }

            if(in_array("o", $state_array)) {
                $FacilityModel->orWhere('r_num', 0);
            }  if(in_array("r", $state_array)) {
                $FacilityModel->orWhere('r_num !=', 0);
            }  
            
            if(in_array("o", $state_array) || in_array("r", $state_array)) {
                $FacilityModel->groupEnd();
            }

            if(!is_null($search_serial)) {

                $FacilityModel->like('serial', $search_serial);
            }

            
            //공종별
            $types = [1, 2, 3, 4];
            $facilities_type = [];
            $is_type = false;
            
            foreach($types as $type) {

                if(in_array(strval($type), $state_array)) {

                    if($is_type == false) {
                        $FacilityModel->groupStart();
                        $FacilityModel2->groupStart();
                    }

                    $FacilityModel->orWhere('type', $type);
                    $FacilityModel2->orWhere('type', $type);

                    $is_type = true;
                }
                
            }

            if($is_type == true) {
                $FacilityModel->groupEnd();
                $FacilityModel2->groupEnd();
            }

            if(in_array("l", $state_array)) {

                $FacilityModel2->join('(SELECT MAX(r_num) as rr_num, o_serial as oo_serial from facility group by o_serial) ff', '(facility.o_serial = ff.oo_serial AND facility.r_num = ff.rr_num)', 'inner', false);

                if(in_array("o", $state_array) || in_array("r", $state_array)) {

                    $select2 = $FacilityModel2->getCompiledSelect();
                    $select1 = $FacilityModel->getCompiledSelect();

                    $query = '('.$select1.') UNION ('.$select2.')';
                    //var_dump($query);exit;

                    $facilities_type = db_connect()->query($query)->getResult('array');

                } else {
                    $facilities_type = $FacilityModel2->findAll();
                }

            } else {
                $facilities_type = $FacilityModel->findAll();
            }


            //진행상황별
            $states = ["a", "b", "c", "d", "e", "f", "g"];
            $facilities_result = [];
            $is_state = false;
            
            foreach($states as $state) {

                if(in_array($state, $state_array)) {

                    $facilities = array_values(array_filter($facilities_type, function($facility) use($state) {
    
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
                $facilities_result = $facilities_type;
            }

            //승인번호순으로 정렬
            if(count($facilities_result) > 0) {

                foreach((array)$facilities_result as $key => $value) {
                    $sort[$key] = $value['serial'];
                }
                array_multisort($sort, SORT_ASC, $facilities_result);

            }
            
            /*
            array_sort_by_multiple_keys($facilities, [
                'serial' => SORT_ASC,
            ]);
            */
            $subcontractors = [];
            foreach($facilities_result as $facility) {

                array_push($subcontractors, $facility['subcontractor']);
            }

            $subcontractors = array_unique($subcontractors);
            asort($subcontractors);
            
            $data = [

                'facilities' => $facilities_result,

                'state' => $state_num ?? '',

                'subcontractors' => $subcontractors,

                'search_serial' => $search_serial,
                
            ];

            return view('view_facility.php', $data);
            
        }
    }

    public function view_facility_info($id) {

        $FacilityModel = new FacilityModel();

        $facility = $FacilityModel->where('id', $id)->first();

        $TaskModel = new TaskModel();
        $tasks = $TaskModel->select('task.*, team.name as team_name')->where('facility_serial', $facility['o_serial'])->join('team', 'team.id = task.team_id')->findAll();

        $data = [
            'facility' => $facility,
            'tasks' => $tasks,            
        ];

        return view('view_facility_info.php', $data);
    }

    public function edit_facility_info() {

        $id = $_POST['id'] ?? null;
        $data = $_POST['data'] ?? null;
        $type = $_POST['type'] ?? null;

        $FacilityModel = new FacilityModel();
        $FacilityModel->where('id', $id);

        if($type == 1) {

            $FacilityModel->set('type', getTypeInt($data));
        }

        $FacilityModel->update();       


        return $this->alert('설비 정보가 변경되었습니다.');

    }

    /*-------------------------------------------현장관리-------------------------------------------*/

    public function set_place() {
        
        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $PlaceModel = new PlaceModel();

            $places = $PlaceModel->findAll();
    
            $data = [
                'places' => $places,  
            ];

            return view('set_place.php', $data);

        }
    }

    public function change_place_name() {

        $id = $_POST['id'] ?? null;
        $new_name = $_POST['new_name'] ?? null;

        $PlaceModel = new PlaceModel();

        if($id == null) {
            
            if($new_name == null || $new_name == "") {
                return $this->alert('현장명이 입력되지 않았습니다.');
            }

            $new_place = [ 'name' => $new_name, ];

            try {
                $PlaceModel->insert($new_place);
                return redirect()->back()->with('alert', $new_name . ' 현장이 추가되었습니다.');

            } catch (\Exception $e) {
                return redirect()->back()->with('alert', ' 현장 추가에 실패했습니다.');
            }

            
        } else {
            
            if($new_name == null || $new_name == "") {
                return $this->alert('바꿀 이름이 입력되지 않았습니다.');
            }
            
            try {
                $PlaceModel->set('name', $new_name)->where('id', $id)->update();
                return redirect()->back()->with('alert', $new_name . '(으)로 이름이 변경되었습니다.');

            } catch (\Exception $e) {
                return redirect()->back()->with('alert', ' 현장명 변경에 실패했습니다.');
            }

        }
    }

    /*-----------------------------------------직원등급관리-----------------------------------------*/

    public function set_user() {
        
        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $UserModel = new UserModel();

            $users = $UserModel->orderBy('level', 'DESC')->orderBy('username', 'ASC')->where('place_id', $this->auth->login_place_id())->findAll();
    
            $data = [
                'users' => $users,  
            ];

            return view('set_user.php', $data);

        }
    }
    
    /*-----------------------------------------생산성조회-----------------------------------------*/

    
    public function view_productivity($team_id = null, $_target_time = null) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            if($_target_time == null || !is_numeric($_target_time)) {
                $target_time = Time::now();
            } else {
                $target_time = Time::createFromTimestamp($_target_time);
            }

            $start_time = $target_time;
            $end_time = $target_time;

            $year = $target_time->getYear();
            $month = $target_time->getMonth();

            $start_time = $start_time->setMonth($month)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            if($month == 12) {

                $end_time = $end_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            } else {

                $end_time = $end_time->setMonth($month+1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            }
            

            $TeamModel = new TeamModel();
            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->findAll();

            $TaskModel = new TaskModel();

            $tasks = $TaskModel->select('ttt.size_current as size_current, ttt.is_square_current as is_square_current, task.facility_serial, t.manday_max as manday_max, type')
                        ->join('(SELECT MAX(manday) as manday_max, facility_serial from task where type = 1 group by facility_serial) t', '(t.facility_serial = task.facility_serial)', 'inner', false)
                        ->join('(SELECT MAX(created_at) as max_created_at, facility_serial from task where type = 1 group by facility_serial) as tt', '(tt.facility_serial = task.facility_serial)', 'inner', false)
                        ->join('(SELECT size as size_current, is_square as is_square_current, created_at, facility_serial from task where type = 1) as ttt', "(ttt.created_at = tt.max_created_at AND ttt.facility_serial = tt.facility_serial)", 'inner', false)
                        ->where('task.type', 1)
                        ->where('task.team_id', $team_id)
                        ->where('task.created_at >= ', $start_time)
                        ->where('task.created_at < ', $end_time)
                        ->groupBy('task.facility_serial, ttt.size_current, ttt.is_square_current')
                        ->findAll();

            $tasks_manday = $TaskModel->select("ANY_VALUE(task.id) as id, ANY_VALUE(t.type) as type, ANY_VALUE(t.facility_serial) as facility_serial, MAX(task.manday) as manday_max, date_format(task.created_at, '%Y-%m-%d') as s_created_at")
                                    ->join("( SELECT id, type, facility_serial from task ) t", '(t.id = task.id)', 'inner', false)
                                    ->where('task.team_id', $team_id)
                                    ->where('task.created_at >= ', $start_time)
                                    ->where('task.created_at < ', $end_time)
                                    ->groupBy("s_created_at")
                                    ->orderBy('s_created_at', 'asc')
                                    ->findAll();

            $data = [              
                'this_team' => $team_id,

                'teams'=> $teams,
                'tasks' => $tasks,
                'tasks_manday' => $tasks_manday,

                'target_time' => $target_time, 
            ];

            return view('view_productivity.php', $data);
            
        }
    }



}
