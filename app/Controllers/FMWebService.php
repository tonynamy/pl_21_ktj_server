<?php namespace App\Controllers;

use App\Models\AttendanceModel;
use App\Models\PlaceModel;
use App\Models\FacilityModel;
use App\Models\TaskModel;
use App\Models\TaskPlanModel;
use App\Models\TeamModel;
use App\Models\TeamMateModel;
use App\Models\UserModel;
use App\Models\SafePointModel;
use App\Models\TeamSafePointModel;
use CodeIgniter\Database\BaseBuilder;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
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

    public function alert_redirect($message, $url) {
        return redirect()->to(urldecode($url))->withInput()->with('alert', $message);
    }

    /*-------------------------------------------로그인-------------------------------------------*/    

    public function index() {

        if($this->auth->is_logged_in()) {

			return redirect()->to('/fm/menu');

        } else {

            $PlaceModel = new PlaceModel();
            $places = $PlaceModel->where('is_hide', 0)->findAll();
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
            
		} else if(!is_null($FacilityModel->like('super_manager', $username)->first()) && mb_strlen($username, 'utf-8') > 1) {
            return redirect()->to('/fm/menu')->setcookie("jwt_token", $this->auth->createJWT(true, $username), 86500);

		} else {
            return $this->alert('로그인에 실패했습니다.');
		}
    }

    public function logout() {
        
        if($this->auth->is_logged_in() || $this->auth->is_logged_in(true)) {

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
		$birthday = $_POST['birthday_calendar'] ?? null;

        if(preg_match('/^[0-9]{4}(0[1-9]|1[0-2])(0[1-9]|[1,2][0-9]|3[0,1])$/', $birthday, $birthday_preg)) {
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
                $id = $this->auth->user_id();
                $level = -1;
                $username = $this->auth->supermanager();
                $birthday = null;
            } else {
                $id = $this->auth->user_id();
                $level = $this->auth->level();
                $username = $this->auth->username();
                $birthday = $this->auth->birthday();
            }
            
            $data = [
                'id' => $id,
                'placename' => $placename,
                'level' => $level,
                'username' => $username,
                'birthday' => $birthday,
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

        try {

            $UserModel->update($id, [
                'birthday' => $new_birthday,
            ]);
    
            return $this->alert('비밀번호가 변경되었습니다.');

        } catch (\Exception $e) {

            return redirect()->back()->with('alert', '비밀번호 변경에 실패했습니다.');

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

    public function parse_team_data() {

        if(!$this->auth->is_logged_in()) {

         return $this->login_fail();

        } else {

            $excel_string = $_POST['excel_string'] ?? null;

            if(is_null($excel_string) || trim($excel_string) === "") {

                return $this->alert('문자열이 비었습니다.');

            }
            $string_by_row = explode(PHP_EOL, $excel_string);

            $TeamModel = new TeamModel();
            $TeamMateModel = new TeamMateModel();

            $upload_error_data = [];
            $pre_exist_data = [];
            $upload_success_data = [];

            $teammate_insert_data = []; //최종 insert 데이터

            $last_team_name = "";

            foreach($string_by_row as $row) {

                try {

                    if(trim($row) == "") continue;  //continue는 for문의 처음으로 돌아간다는 뜻

                    $row_data = explode("\t", $row);
                    $row_data_count = count($row_data);

                    $error_types = [];
                    
                    //값받기
                    $team_name = $row_data[0] ?? "";
                    $name = $row_data[1] ?? "";
                    $registration_number = $row_data[2] ?? "";

                    //TRIM
                    $team_name = trim($team_name);
                    $name = trim($name);
                    $registration_number = trim($registration_number);


                    if($row_data_count < 3) {
                        array_push($error_types, 0);
                    }

                    //팀이름이 비었을 때 이전 팀이름 가져오기
                    if($team_name != "") {
                        $current_team_name = $team_name;
                        $last_team_name = $team_name;
                    } else {
                        $current_team_name = $last_team_name;
                    }

                    if($current_team_name == "") {
                        array_push($error_types, 11);

                    } else if(preg_match("/[<>]/", $current_team_name)) {
                        array_push($error_types, 12);
                    }
                    if($name == "") {
                        array_push($error_types, 21);

                    } else if(preg_match("/[<>]/", $name)) {
                        array_push($error_types, 22);

                    }
                    if($registration_number == "") {
                        array_push($error_types, 31);

                    }

                    $matches = [];
                    $birthday = preg_match('/^[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[1,2][0-9]|3[0,1])/', $registration_number, $matches);

                    if($birthday && !preg_match("/[ㄱ-ㅎ|ㅏ-ㅣ|가-힣]/", $registration_number)) {

                        $birthday = $matches[0];

                    //생년월일 양식이 잘못된 경우
                    } else {
                        array_push($error_types, 32);
                    }

                    if($row_data_count > 3) {
                        array_push($error_types, 33);
                    }

                    //$error_types = array_values(array_unique($error_types));

                    //에러데이터 전송
                    if(count($error_types) > 0) {
                        
                        array_push($upload_error_data, [
                            'error_types' => $error_types,
                            'team_name' => $current_team_name,
                            'name' => $name,
                            'birthday' => $birthday,
                        ]);

                        continue;

                    }

                    //팀이 없을시 팀생성
                    if(is_null($TeamModel->where('place_id', $this->auth->login_place_id())->where('name', $current_team_name)->first())) {
                        
                        try {
                            $TeamModel->insert([
                                'place_id' => $this->auth->login_place_id(),
                                'name' => $current_team_name,
                            ]);
                        } catch (\Exception $e) {

                            return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.\n사유: 팀생성 중 실패');

                        }
                    }

                    //팀이름으로 해당현장에서 팀검색
                    $match_team = $TeamModel->where('place_id', $this->auth->login_place_id())->where('name', $current_team_name)->first();
                    
                    //동일팀메이트 있는지 검사
                    $same_teammate = $TeamMateModel->select('teammate.*')
                                                    ->where('teammate.team_id', $match_team['id'])
                                                    ->where('teammate.name', $name)
                                                    ->where('teammate.birthday', $birthday)
                                                    ->first();
                    
                    $is_pre_exist_data = false;

                    if(is_null($same_teammate)) {

                        $unique_info = [
                            'team_id' => $match_team['id'],
                            'name' => $name,
                            'birthday' => $birthday,
                        ];

                        if(in_array($unique_info, $teammate_insert_data)) {

                            $is_pre_exist_data = true;

                        } else {

                            array_push($teammate_insert_data, $unique_info);
                            
                            //성공데이터 전송
                            array_push($upload_success_data, [
                                'team_id' => $match_team['id'],
                                'team_name' => $match_team['name'],
                                'name' => $name,
                                'birthday' => $birthday,
                            ]);

                        }

                    } else {
                        $is_pre_exist_data = true;

                    }

                    if($is_pre_exist_data) {

                        //이미 있는 데이터 + 중복 데이터 전송
                        array_push($pre_exist_data, [
                            'team_id' => $match_team['id'],
                            'team_name' => $match_team['name'],
                            'name' =>  $name,
                            'birthday' => $birthday,
                        ]);

                        continue;
                    }

                } catch (\Exception $e) {
                    continue;
                }
            }

            try {

                $TeamMateModel->insertBatch($teammate_insert_data);

            } catch (\Exception $e) {

                return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.');

            }

            return view('add_team_result', [

                'upload_success_data' => $upload_success_data,
                'pre_exist_data' => $pre_exist_data,
                'upload_error_data' => $upload_error_data,

            ]);

        }
    }

    public function add_team_result() {
        
        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            return view('add_team_result.php');
        }

    }

    /*-------------------------------------------도면등록-------------------------------------------*/

    public function add_facility() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $file = $this->request->getFile('file') ?? null;

            //var_dump($file);exit;

            if($file==null) return view('add_facility');

            $path = WRITEPATH.'uploads/'. $file->store();

            $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($path);
            $worksheet = $spreadsheet->getActiveSheet();
            $rows = $worksheet->toArray();

            unlink($path);

            return $this->alert('추가되었습니다.');
            
        }
    }

    public function parse_facility_data() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $excel_string = $_POST['excel_string'] ?? null;

            if(is_null($excel_string) || trim($excel_string) === "") {

                return $this->alert('문자열이 비었습니다.');

            }
            $string_by_row = explode(PHP_EOL, $excel_string);


            //serial의 중복값 찾기
            $duplicate_serials = [];

            foreach($string_by_row as $row) {

                if(trim($row) == "") continue;  //continue는 for문의 처음으로 돌아간다는 뜻

                $row_data = explode("\t", $row);
                $row_data_count = count($row_data);

                $serial = $row_data[0] ?? "";
                $serial = trim($serial);

                if($serial != "") {
                    array_push($duplicate_serials, $serial);
                }
            }
            $duplicate_serials = array_unique(array_diff_assoc($duplicate_serials, array_unique($duplicate_serials))); //중복된 serial을 배열에 담았다
        
            $FacilityModel = new FacilityModel();
            $TaskPlanModel = new TaskPlanModel();

            $upload_error_data = [];
            $pre_exist_data = [];
            $upload_success_data = [];

            $facility_insert_data = []; //최종 insert 데이터

            foreach($string_by_row as $row) {
                
                try {

                    if(trim($row) == "") continue;  //continue는 for문의 처음으로 돌아간다는 뜻

                    $row_data = explode("\t", $row);
                    $row_data_count = count($row_data);

                    $error_types = [];

                    //값받기
                    $serial = $row_data[0] ?? "";
                    $serial_arr = explode("-", $serial);

                    //원도면번호와 리비전넘버 찾기 (최소한 -가 한번이상 있고, 마지막 -뒤의 번호가 숫자여야하고, 마지막 -뒤의 번호는 0으로 시작해선 안되고, 마지막 -앞의 번호도 숫자일시)
                    if(count($serial_arr) > 1 && is_numeric(end($serial_arr)) && intval(substr(end($serial_arr), 0, 1)) != 0 && is_numeric($serial_arr[count($serial_arr)-2])) {
                        $r_num = array_pop($serial_arr);
                        $o_serial = implode("-", $serial_arr);
                    } else {
                        $r_num = 0;
                        $o_serial = $serial;
                    }

                    switch(trim($row_data[1] ?? "")) {
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

                    $super_manager = $row_data[2] ?? "";
                    $subcontractor = $row_data[3] ?? "";
                    $building = $row_data[4] ?? "";
                    $floor = $row_data[5] ?? "";
                    $spot = $row_data[6] ?? "";
                    $section = $row_data[7] ?? "";
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
                    $expired_at = $row_data[22] ?? "";
                    $memo = $row_data[23] ?? "";

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
                    $started_at = trim($started_at);
                    $finished_at = trim($finished_at);
                    $edit_started_at = trim($edit_started_at);
                    $edit_finished_at = trim($edit_finished_at);
                    $dis_started_at = trim($dis_started_at);
                    $dis_finished_at = trim($dis_finished_at);
                    $expired_at = trim($expired_at);
                    $memo = trim($memo);

                    //null값 허용항목 처리
                    $created_at = $created_at == "" ? null : $created_at;
                    $started_at = $started_at == "" ? null : $started_at;
                    $finished_at = $finished_at == "" ? null : $finished_at;
                    $edit_started_at = $edit_started_at == "" ? null : $edit_started_at;
                    $edit_finished_at = $edit_finished_at == "" ? null : $edit_finished_at;
                    $dis_started_at = $dis_started_at == "" ? null : $dis_started_at;
                    $dis_finished_at = $dis_finished_at == "" ? null : $dis_finished_at;
                    $expired_at = $expired_at == "" ? null : $expired_at;
                    $memo = $memo == "" ? null : $memo; //조금 더 생각해보자


                    //설치위치(spot 7번)까지 필수정보임, 비고(memo 24번)보다 많아도 안됨
                    if($row_data_count < 7) {
                        array_push($error_types, -100); // -100: 정보수 미달
                    }
                    if($row_data_count > 24) {
                        array_push($error_types, 100); // 100: 정보수 초과
                    }
                    if($serial == "") {
                        array_push($error_types, -1); // -1: 승인번호가 비어있음

                    } else if(preg_match("/[<>]/", $serial)) {
                        array_push($error_types, 1); // 1: 승인번호에 <>있음

                    } else if(in_array($serial, $duplicate_serials)) {
                        array_push($error_types, 2); // 2: 함께 들어온 데이터중에 승인번호가 중복된 데이터가 있음
                    }
                    if(preg_match("/[<>]/", $super_manager)) {
                        array_push($error_types, 3); // 3: 담당자에 <>있음
                    }
                    if(preg_match("/[<>]/", $subcontractor)) {
                        array_push($error_types, 4); // 4: 업체에 <>있음
                    }
                    if(preg_match("/[<>]/", $building)) {
                        array_push($error_types, 5); // 5: 설치동에 <>있음
                    }
                    if(preg_match("/[<>]/", $floor)) {
                        array_push($error_types, 6); // 6: 층에 <>있음
                    }
                    if(preg_match("/[<>]/", $spot)) {
                        array_push($error_types, 7); // 7: 설치위치에 <>있음
                    }
                    if($building == "" && $floor == "" && $spot == "") {
                        array_push($error_types, -567); // -567: 설치위치 정보가 전혀 없음
                    }
                    if($created_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $created_at)) {
                        array_push($error_types, 15); // 15: 도면등록일이 날짜형식이 아님
                    }
                    if($started_at == null && ($finished_at != null || $edit_started_at != null || $edit_finished_at != null || $dis_started_at != null || $dis_finished_at != null) ) {
                        array_push($error_types, -16); // -16: 설치시작일 누락
                    }
                    if($started_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $started_at)) {
                        array_push($error_types, 16); // 16: 설치시작일이 날짜형식이 아님
                    }
                    if($finished_at == null && ($edit_started_at != null || $edit_finished_at != null || $dis_started_at != null || $dis_finished_at != null) ) {
                        array_push($error_types, -17); // -17: 승인완료일 누락
                    }
                    if($finished_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $finished_at)) {
                        array_push($error_types, 17); // 17: 승인완료일이 날짜형식이 아님
                    }
                    if($edit_started_at == null && $edit_finished_at != null) {
                        array_push($error_types, -18); // -18: 수정시작일 누락
                    }
                    if($edit_started_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $edit_started_at)) {
                        array_push($error_types, 18); // 18: 수정시작일이 날짜형식이 아님
                    }
                    if($edit_finished_at == null && $edit_started_at != null && ($dis_started_at != null || $dis_finished_at != null)) {
                        array_push($error_types, -19); // -19: 수정완료일 누락
                    }
                    if($edit_finished_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $edit_finished_at)) {
                        array_push($error_types, 19); // 19: 수정완료일이 날짜형식이 아님
                    }
                    if($dis_started_at == null && $dis_finished_at != null) {
                        array_push($error_types, -20); // -20: 해체시작일 누락
                    }
                    if($dis_started_at != null && !preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $dis_started_at)) {
                        array_push($error_types, 20); // 20: 해체시작일이 날짜형식이 아님
                    }
                    if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $dis_finished_at) && $dis_finished_at != null) {
                        array_push($error_types, 21); // 21: 해체완료일이 날짜형식이 아님
                    }
                    if(!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/", $expired_at) && $expired_at != null) {
                        array_push($error_types, 22); // 22: 만료일이 날짜형식이 아님
                    }

                    //필수정보 없을시 에러데이터에 넣기
                    if(count($error_types) > 0) {

                        array_push($upload_error_data, [
                            
                            'error_types' => $error_types,
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
                            'danger_data' => $danger_data,
                            'danger_result' => $danger_result,
                            'created_at' => $created_at,
                            'started_at' => $started_at,
                            'finished_at' => $finished_at,
                            'edit_started_at' => $edit_started_at,
                            'edit_finished_at' => $edit_finished_at,
                            'dis_started_at' => $dis_started_at,
                            'dis_finished_at' => $dis_finished_at,
                            'expired_at' => $expired_at,
                            'memo' => $memo,

                        ]);

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
                    $same_serial_facility = $FacilityModel->where('place_id', $this->auth->login_place_id())
                                                    ->where('serial', $serial)
                                                    ->first();

                    //해당 현장에 동일 원도면번호에 리비전번호가 있는지 확인
                    $same_rnum_facility = $FacilityModel->where('place_id', $this->auth->login_place_id())
                                                    ->where('o_serial', $o_serial)
                                                    ->where('r_num', $r_num)
                                                    ->first();

                    //해당 현장에 동일 시리얼번호가 없을시
                    if(is_null($same_serial_facility) && is_null($same_rnum_facility)) {

                        $unique_info = [
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
                            'created_at' => $created_at,
                            'started_at' => $started_at,
                            'finished_at' => $finished_at,
                            'edit_started_at' => $edit_started_at,
                            'edit_finished_at' => $edit_finished_at,
                            'dis_started_at' => $dis_started_at,
                            'dis_finished_at' => $dis_finished_at,
                            'expired_at' => $expired_at,
                            'memo' => $memo,
                        ];

                        //우선 업로드성공데이터에 넣기
                        array_push($upload_success_data, $unique_info);

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

                        //같은 원도면을 공유하는 도면 전부찾기
                        $previous_facilities = $FacilityModel->where('place_id', $this->auth->login_place_id())->where('o_serial', $o_serial)->orderBy('r_num', 'ASC')->findAll();

                        //원도면에 값이 있고 신규등록하는 도면에 값이 없으면 신규도면에 원도면의 값을 대입
                        //for문으로 돌리고 싶은데 $started_at, $finished_at 같은 변수들은 어떻게 $i에 대입해야할지 모르겠네요
                        for($i = (count($previous_facilities)-1); $i >= 0; $i--) {

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
                        //도면등록일이 비어있다면
                        if($created_at == null) {
                            $unique_info['created_at'] = Time::now();
                        }
                        //날짜정보가 바뀐게 있다면
                        if($started_at != null) {
                            $unique_info['started_at'] = $started_at;
                        }
                        if($finished_at != null) {
                            $unique_info['finished_at'] = $finished_at;
                        }
                        if($edit_started_at != null) {
                            $unique_info['edit_started_at'] = $edit_started_at;
                        }
                        if($edit_finished_at != null) {
                            $unique_info['edit_finished_at'] = $edit_finished_at;
                        }
                        if($dis_started_at != null) {
                            $unique_info['dis_started_at'] = $dis_started_at;
                        }
                        if($dis_finished_at != null) {
                            $unique_info['dis_finished_at'] = $dis_finished_at;
                        }
                        if($expired_at != null) {
                            $unique_info['expired_at'] = $expired_at;
                        }
                        array_push($facility_insert_data, $unique_info);

                    //데이터베이스에 이미 같은 serial의 정보가 있을시
                    } else {

                        $exist_type = 0;
                        $exist_serial = "";

                        if(!is_null($same_serial_facility)){
                            $exist_type = 1;
                            $exist_serial = $same_serial_facility['serial'];

                        } else if(!is_null($same_rnum_facility)) {
                            $exist_type = 2;
                            $exist_serial = $same_rnum_facility['serial'];
                        }

                        array_push($pre_exist_data, [
                            'exist_type' => $exist_type,
                            'exist_serial' => $exist_serial,
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
                            'created_at' => $created_at,
                            'started_at' => $started_at,
                            'finished_at' => $finished_at,
                            'edit_started_at' => $edit_started_at,
                            'edit_finished_at' => $edit_finished_at,
                            'dis_started_at' => $dis_started_at,
                            'dis_finished_at' => $dis_finished_at,
                            'expired_at' => $expired_at,
                            'memo' => $memo,
                        ]);

                    }

                } catch (\Exception $e) {
                    continue;
                }
            }
            /*
            var_dump($upload_success_data);
            echo('<br>');
            var_dump($pre_exist_data);
            echo('<br>');
            var_dump($upload_error_data);
            */
            
            try {

                $FacilityModel->insertBatch($facility_insert_data);

            } catch (\Exception $e) {

                return $this->alert('데이터 삽입 과정 중 오류가 발생하였습니다.');

            }

            //return $this->alert('업로드 성공');

            return view('add_facility_result.php', [

                'upload_success_data' => $upload_success_data,
                'pre_exist_data' => $pre_exist_data,
                'upload_error_data' => $upload_error_data,

            ]);
        }
    }

    public function add_facility_result() {
        
        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            return view('add_facility_result.php');
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

            $is_first_week = true;
            $is_after = false;

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

                $is_after = Time::now()->isBefore($end_time);
                
                $AttendanceModel = new AttendanceModel();

                $firstAttendance = $AttendanceModel->select('attendance.*, tm.name as teammate_name, tm.birthday as teammate_birthday')
                                                    ->join('teammate as tm', 'attendance.teammate_id = tm.id')
                                                    ->join('team as t', 'tm.team_id = t.id')
                                                    ->where('t.id', $team_id)
                                                    ->orderBy('attendance.created_at', 'ASC')
                                                    ->first();

                if($firstAttendance != null) {
                    $is_first_week = Time::createFromFormat('Y-m-d H:i:s', $firstAttendance['created_at'])->isAfter($start_time);
                }

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

                'is_after' => $is_after,
                'is_first_week' => $is_first_week,
            ];

            return view('view_attendance.php', $data);
            
        }
    }

    //출석기록 엑셀로 저장
    public function download_attendance($old_team_id = null) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else if($this->request->getMethod() == 'post') {

            $team_id = $_POST['team_id'] ?? null;
            $weeks = $_POST['weeks'] ?? null;

            if($team_id == null || $weeks == null) {
                return $this->alert('값이 올바르지 않습니다.');
            }

            $PlaceModel = new PlaceModel();
            $place_name = $PlaceModel->where('id', $this->auth->login_place_id())->first()['name'];

            $now_timestamp = date("Ymd");

            $target_time = Time::now();
            $weeks = $weeks - 1;

            // 일 : 1
            // 월 : 2
            // 화 : 3
            // 수 : 4
            // 목 : 5
            // 금 : 6
            // 토 : 7

            $day_of_week = $target_time->getDayOfWeek();

            $start_time = $target_time->subDays($day_of_week-1 + $weeks*7)->setHour(5)->setMinute(0)->setSecond(0);
            $end_time = $start_time->addDays(7 + $weeks*7);
            //$end_time2 = $target_time->addDays(8-$day_of_week)->setHour(5)->setMinute(0)->setSecond(0);

            $TeamMateModel = new TeamMateModel();
            $teammates = $TeamMateModel->where('team_id', $team_id)->orderBy('name', 'ASC')->findAll();

            $AttendanceModel = new AttendanceModel();

            $attendanceRecords = $AttendanceModel->select('attendance.*, tm.name as teammate_name, tm.birthday as teammate_birthday')
                                                ->join('teammate as tm', 'attendance.teammate_id = tm.id')
                                                ->join('team as t', 'tm.team_id = t.id')
                                                ->where('t.id', $team_id)
                                                ->where('attendance.created_at >=', $start_time)
                                                ->where('attendance.created_at <', $end_time)
                                                ->orderBy('attendance.created_at', 'ASC')
                                                ->orderBy('tm.name', 'ASC')
                                                ->findAll();

            $excel_array = [];

            $ptr_time = $start_time;

            $ptr_idx = 0;

        
            for($i=0; $i<=$weeks; $i++) {

                $time_row = [$ptr_time->getYear(). '년'];

                for($j=0; $j<7; $j++) {
                    
                    $d = $ptr_time->addDays($j);
                    array_push($time_row, $d->getMonth() . '/' . $d->getDay() . '출근');
                    array_push($time_row, $d->getMonth() . '/' . $d->getDay() . '퇴근');
                }

                array_push($excel_array, $time_row);

                $records = [];

                while( $ptr_idx < count($attendanceRecords) && Time::createFromFormat('Y-m-d H:i:s', $attendanceRecords[$ptr_idx]['created_at'])->isBefore($ptr_time->addDays(7)) ) {

                    $r = $attendanceRecords[$ptr_idx];
                    $t = Time::createFromFormat('Y-m-d H:i:s', $r['created_at']);
                    $k = $r['teammate_name'] . '(' . $r['teammate_birthday'] . ')';

                    if(!array_key_exists($k, $records)) {

                        $records[$k] = [];
                    }

                    $d = $ptr_time->difference($t);

                    $records[$k][ $d->getDays()*2 + $r['type'] ] = sprintf('%02d', $t->getHour()) . ':' . sprintf('%02d', $t->getMinute());

                    $ptr_idx++;
                }

                foreach($teammates as $teammate) {

                    $name = $teammate['name'] . '(' . $teammate['birthday'] . ')';

                    $record = [];

                    if(array_key_exists($name, $records)) {
                        $record = $records[$name];
                    }
    
                    $row = [$name];

                    for($j=0; $j<14; $j++) {

                        if(array_key_exists($j, $record)) {
                            array_push($row, $record[$j]);
                        } else {
                            array_push($row, '');
                        }

                    }

                    array_push($excel_array, $row);
                }

                array_push($excel_array, []);
                
                $ptr_time = $ptr_time->addDays(7);
            }
            
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($excel_array, NULL, 'A1', true);
            
            $file_path = tempnam(sys_get_temp_dir(), 'xl_');
            
            $writer = new Xlsx($spreadsheet);
            $writer->save($file_path);
            
            return $this->response->download($file_path, null)->setFileName($place_name . '_출퇴근기록_' . $now_timestamp . '.xlsx');


        } else {

            $TeamModel = new TeamModel();

            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();

            return view('download_attendance.php', [

                'old_team_id' => $old_team_id,
                'teams' => $teams,

            ]);

        }
    }

    //출퇴근기록추가
    public function edit_attendance() {

        $teammate_id = $_POST['teammate_id'] ?? null;
        $attendance_id = $_POST['attendance_id'] ?? null;
        $attendance_id2 = $_POST['attendance_id2'] ?? null;
        $attendance_type = $_POST['attendance_type'] ?? null;
        $attendance_date = $_POST['attendance_date'] ?? null;
        $attendance_time = $_POST['attendance_time'] ?? null;
        $is_delete = $_POST['is_delete'] == "true" ? true : false;

        if($teammate_id == null || $attendance_type == null || $attendance_date == null || !preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1,2][0-9]|3[0,1])$/', $attendance_date)) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $AttendanceModel = new AttendanceModel();

        //삭제상황
        if($is_delete) {

            //출퇴근기록 모두 삭제
            if($attendance_type == 0 && $attendance_id2 != null) {

                $AttendanceModel->delete($attendance_id);
                $AttendanceModel->delete($attendance_id2);

                return $this->alert('출퇴근기록을 삭제했습니다.');

            //출근기록만 삭제
            } else if($attendance_type == 0 && $attendance_id2 == null) {

                $AttendanceModel->delete($attendance_id);

                return $this->alert('출근기록을 삭제했습니다.');

            //퇴근기록만 삭제
            } else if($attendance_type == 1) {

                $AttendanceModel->delete($attendance_id, true);

                return $this->alert('퇴근기록을 삭제했습니다.');
            }

        //추가, 수정상황
        } else if($attendance_time != null && preg_match('/^[0-9]{2}:[0-9]{2}$/', $attendance_time) && explode(":", $attendance_time)[0] < 24 && explode(":", $attendance_time)[1] < 60 ) {

            $hour = explode(":", $attendance_time)[0];
            $min = explode(":", $attendance_time)[1];
            $total_min = ($hour*60) + $min;

            //$attendance_time이 05:00보다 작을시 그 다음날로 입력이 되어야 한다.
            if($total_min < 300) {
                $datetime = Time::createFromFormat('Y-m-d', $attendance_date)->addDays(1)->setHour($hour)->setMinute($min)->setSecond(0);

            } else {
                $datetime = Time::createFromFormat('Y-m-d', $attendance_date)->setHour($hour)->setMinute($min)->setSecond(0);
            }

            //새 출석기록 추가
            if($attendance_id == null) {

                $AttendanceModel->insert([
                    'teammate_id' => $teammate_id,
                    'type' => $attendance_type,
                    'created_at' => $datetime,
                ]);
                
                return $this->alert('새 출석기록을 추가했습니다.');
            
            //기존 출석기록 수정
            } else {

                $AttendanceModel->update($attendance_id , [
                    'created_at' => $datetime,
                ]);

                return $this->alert('출석기록을 수정했습니다.');
            }

        //오류메세지
        } else {

            if($attendance_time == null) {
                return $this->alert('시간이 입력되지 않았습니다.');
            
            } else {
                return $this->alert('시간형식이 잘못되었습니다.');
            }
        }
    }

    //팀삭제
    public function delete_team() {

        $id = $_POST['team_id'] ?? null;

        $TeamModel = new TeamModel();

        $TeamModel->delete($id, true);

        /*
        if(is_null($url)) return $this->alert('팀이 삭제되었습니다.');
        else return $this->alert_redirect('삭제되었습니다', $url);
        */
        return redirect()->to('/fm/view_attendance')->withInput()->with('alert', '팀이 삭제되었습니다.');

    }

    //팀원정보 수정 및 삭제
    public function edit_teammate_info() {

        $id = $_POST['teammate_id'] ?? null;
        $new_birthday = $_POST['teammate_new_birthday'] ?? null;
        $teammate_delete = $_POST['teammate_delete'] == "true"? true : false;

        if($id == null || $id == 0) {
            return $this->alert('잘못된 요청입니다.');
        }

        $TeamMateModel = new TeamMateModel();

        //팀메이트 삭제 상황
        if($teammate_delete) {

            $TeamMateModel->delete($id, true);

            return $this->alert('팀원이 삭제되었습니다.');

        //팀메이트 수정 상황
        } else {
            
            if($new_birthday == null) {
                return $this->alert('바꿀 생년월일이 입력되지 않았습니다.');

            } else if(!preg_match("/^[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[1,2][0-9]|3[0,1])$/", $new_birthday)) {
                return $this->alert('생년월일 양식이 잘못되었습니다.');

            } else {
                $b_month = substr($new_birthday, 2, 2);
                $b_day = substr($new_birthday, 4, 2);
                $short_month = [2, 4, 6, 9, 11];

                if((in_array($b_month, $short_month) && $b_day > 30) || ($b_month == 2 && $b_day > 29)) {
                    return $this->alert('생년월일 양식이 잘못되었습니다.');
                }
            }

            $TeamMateModel->update($id, [ 'birthday' => $new_birthday, ]);

            return $this->alert($new_birthday . '(으)로 생년월일이 변경되었습니다.');
        }
    }

    /*-------------------------------------------작업조회-------------------------------------------*/

    public function view_facility($filter_key = null) {

        if(!($this->auth->is_logged_in() || $this->auth->is_logged_in(true))) {

			return $this->login_fail();

        } else {
            
            if($this->auth->is_logged_in(true)){
                $user_level = -1;
            } else {
                $user_level = $this->auth->level();
            }

            $state_array = [];

            if(!is_null($filter_key)) {
                $state_array = str_split($filter_key); 
                $state_key = implode(",", $state_array);
            }
            
            $search_serial = $_POST['search_serial'] ?? null;

            $FacilityModel = new FacilityModel();
            $FacilityModel2 = new FacilityModel();

            $FacilityModel->select('facility.*')->where('facility.place_id', $this->auth->login_place_id());
            $FacilityModel2->select('facility.*')->where('facility.place_id', $this->auth->login_place_id());
            
            $is_guest = $this->auth->is_logged_in(true);
            if($is_guest) {

                $FacilityModel->like('super_manager', $this->auth->supermanager());
                $FacilityModel2->like('super_manager', $this->auth->supermanager());

            }

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
            $subcontractors = [];
            foreach($facilities_result as $facility) {

                array_push($subcontractors, $facility['subcontractor']);
            }

            $subcontractors = array_unique($subcontractors);
            asort($subcontractors);
            */

            $data = [
                'user_level' => $user_level,
                'facilities' => $facilities_result,
                'state' => $state_key ?? '',
                //'subcontractors' => $subcontractors,
                'search_serial' => $search_serial,
            ];

            return view('view_facility.php', $data);
            
        }
    }

    public function download_facility() {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $place_id = $this->auth->login_place_id();

            $PlaceModel = new PlaceModel();
            $place_name = $PlaceModel->where('id', $place_id)->first()['name'];

            $now_timestamp = date("Ymd");

            $FacilityModel = new FacilityModel();

            $facilities = $FacilityModel->where('place_id', $place_id)->orderBy('serial', 'ASC')->findAll();

            $facility_array = [];
            
            array_push($facility_array, [
                "승인번호", "공종", "담당자", "사용업체", "설치동", "층", "설치위치", "설치구간", "설치목적",
                "강관비계 산출식", "물량(㎥)", "안전발판 산출식", "물량(㎡)", "달대비계 산출식", "물량(㎥)",
                "도면등록일", "설치시작일", "승인완료일", "수정시작일", "수정완료일", "해체시작일", "해체완료일",
                "만료일", "비고"
            ]);

            foreach($facilities as $facility) {

                array_push($facility_array, [
                    $facility['serial'],
                    getTypeText($facility['type']),
                    $facility['super_manager'],
                    $facility['subcontractor'],
                    $facility['building'],
                    $facility['floor'],
                    $facility['spot'],
                    $facility['section'],
                    $facility['purpose'],
                    $facility['cube_data'],
                    $facility['cube_result'],
                    $facility['area_data'],
                    $facility['area_result'],
                    $facility['danger_data'],
                    $facility['danger_result'],
                    explode(' ', $facility['created_at'])[0],
                    explode(' ', $facility['started_at'])[0],
                    explode(' ', $facility['finished_at'])[0],
                    explode(' ', $facility['edit_started_at'])[0],
                    explode(' ', $facility['edit_finished_at'])[0],
                    explode(' ', $facility['dis_started_at'])[0],
                    explode(' ', $facility['dis_finished_at'])[0],
                    explode(' ', $facility['expired_at'])[0], 
                    $facility['memo']
                ]);
            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($facility_array, NULL, 'A1', true);

            $file_path = tempnam(sys_get_temp_dir(), 'xl_');

            $writer = new Xlsx($spreadsheet);
            $writer->save($file_path);

            return $this->response->download($file_path, null)->setFileName($place_name . '_비계관리대장_' . $now_timestamp .'.xlsx');
        }
    }

    public function view_facility_info($serial, $filter_key = null) {

        if(!($this->auth->is_logged_in() || $this->auth->is_logged_in(true))) {

			return $this->login_fail();

        } else {
            
            if($this->auth->is_logged_in(true)){
                $user_level = -1;
            } else {
                $user_level = $this->auth->level();
            }

            $FacilityModel = new FacilityModel();

            $facility = $FacilityModel->where('place_id', $this->auth->login_place_id())->where('serial', $serial)->first();

            if($facility == null) {
                return $this->alert('해당되는 도면이 없습니다.');
            }

            $TaskPlanModel = new TaskPlanModel();
            $taskplans = $TaskPlanModel->select('taskplan.*, team.name as team_name')
                                        ->join('team', 'team.id = taskplan.team_id')
                                        ->where('taskplan.facility_serial', $facility['o_serial'])
                                        ->findAll();

            if(count($taskplans) == 1) {
                $taskplan = $taskplans[0];

                if(Time::now() <= Time::createFromTime(5, 0, 0)) {
                    $today_start_time = Time::now()->yesterday()->setHour(5)->setMinute(0)->setSecond(0);

                } else {
                    $today_start_time = Time::now()->setHour(5)->setMinute(0)->setSecond(0);
                }

                $AttendanceModel = new AttendanceModel();
                $attendance = $AttendanceModel->select('teammate.name as name')
                                                ->join('teammate', 'attendance.teammate_id = teammate.id and teammate.team_id = "' . $taskplan['team_id'] . '"')
                                                ->where('attendance.type', '0')
                                                ->where('attendance.created_at >', $today_start_time)
                                                ->countAllResults();

                $taskplan['attendance'] = $attendance;

            } else if((count($taskplans) > 1)) {
                $taskplan = 'VALID';
            } else {
                $taskplan = null;
            }

            $TaskModel = new TaskModel();
            $tasks = $TaskModel->select('task.*, team.name as team_name')
                                ->join('team', 'team.id = task.team_id')
                                ->where('task.facility_serial', $facility['o_serial'])
                                ->orderBy('created_at', 'ASC')
                                ->findAll();
            
            $TeamModel = new TeamModel();
            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();

            $data = [
                'user_level' => $user_level,
                'facility' => $facility,
                'taskplan' => $taskplan,
                'tasks' => $tasks,
                'teams' => $teams,
                'filter_key' => $filter_key,
            ];

            return view('view_facility_info.php', $data);
        }
    }

    public function delete_facility() {

        $id = $_POST['facility_id'] ?? null;

        $FacilityModel = new FacilityModel();

        $FacilityModel->delete($id, true);

        return redirect()->to('/fm/view_facility')->withInput()->with('alert', '도면이 삭제되었습니다.');

    }

    public function edit_facility_info() {

        $id = $_POST['facility_id'] ?? null;
        $data_type = $_POST['data_type'] ?? null;
        $data = $_POST['data'] ?? null;
        $is_edit = $_POST['is_edit'] == "true" ? true : false;

        $work_message = !$is_edit ? "생성" : ($data != null ? "수정" : "삭제");

        if($id == null || $data_type == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $FacilityModel = new FacilityModel();

        if($data_type == 0) {

            $r_num = $_POST['r_num'] ?? null;
            
            if($r_num == null) {
                return $this->alert('값이 올바르지 않습니다.');
            }

            if($data == null) {
                return $this->alert('원도면번호는 비워둘 수 없습니다.');

            } else {
                $same_rnum_facility = $FacilityModel->where('o_serial', $data)->where('r_num', $r_num)->first();

                if(!is_null($same_rnum_facility)) {
                    return $this->alert('같은 리비전번호의 도면이 있습니다.');

                } else {
                    $FacilityModel->update($id, [ 'o_serial' => $data ]);
                    return $this->alert('원도면번호가 수정되었습니다.');
                }
            }

        } else if($data_type == 1) {

            $o_serial = $_POST['o_serial'] ?? null;

            if($o_serial == null) {
                return $this->alert('값이 올바르지 않습니다.');
            }

            $same_rnum_facility = $FacilityModel->where('o_serial', $o_serial)->where('r_num', $data)->first();
            if(!is_null($same_rnum_facility)) {
                return $this->alert('같은 리비전번호의 도면이 있습니다.');

            } else {
                $FacilityModel->update($id, [ 'r_num' => $data ]);
                return $this->alert('리비전번호가 수정되었습니다.');
            }

        } else if($data_type == 2) {

            $FacilityModel->update($id, [ 'type' => $data ]);
            return $this->alert('공종이 수정되었습니다.');

        } else if($data_type == 3) {
            
            $FacilityModel->update($id, [ 'super_manager' => $data ]);
            return $this->alert('담당자가 ' . $work_message . '되었습니다.');

        } else if($data_type == 4) {
            
            $FacilityModel->update($id, [ 'subcontractor' => $data ]);
            return $this->alert('사용업체가 ' . $work_message . '되었습니다.');

        } else if($data_type == 5 || $data_type == 6 || $data_type == 7) {
            
            $this_facility = $FacilityModel->where('id', $id)->first();
            //var_dump($this_facility); exit;

            if($data_type == 5) {

                if($data == null && $this_facility['floor'] == "" && $this_facility['spot'] == "") {
                    return $this->alert('설치동, 설치층, 설치위치 중 최소한 한가지의 정보는 있어야합니다.');

                } else {
                    $FacilityModel->update($id, [ 'building' => $data ]);
                    return $this->alert('설치동이 ' . $work_message . '되었습니다.');
                }

            } else if($data_type == 6) {
            
                if($data == null && $this_facility['building'] == "" && $this_facility['spot'] == "") {
                    return $this->alert('설치동, 설치층, 설치위치 중 최소한 한가지의 정보는 있어야합니다.');

                } else {
                    $FacilityModel->update($id, [ 'floor' => $data ]);
                    return $this->alert('설치층이 ' . $work_message . '되었습니다.');
                }
    
            } else if($data_type == 7) {
                
                if($data == null && $this_facility['floor'] == "" && $this_facility['building'] == "") {
                    return $this->alert('설치동, 설치층, 설치위치 중 최소한 한가지의 정보는 있어야합니다.');

                } else {
                    $FacilityModel->update($id, [ 'spot' => $data ]);
                    return $this->alert('설치위치가 ' . $work_message . '되었습니다.');
                }
            }

        } else if($data_type == 8) {
            
            $FacilityModel->update($id, [ 'section' => $data ]);
            return $this->alert('설치구간이 ' . $work_message . '되었습니다.');

        } else if($data_type == 9) {
            
            $FacilityModel->update($id, [ 'purpose' => $data ]);
            return $this->alert('설치목적이 ' . $work_message . '되었습니다.');

        } else if($data_type == 10) {
            
            $FacilityModel->update($id, [ 'cube_data' => $data ]);
            return $this->alert('강관비계 산출식이 ' . $work_message . '되었습니다.');

        } else if($data_type == 11) {
            
            $FacilityModel->update($id, [ 'cube_result' => $data ]);
            return $this->alert('강관비계 물량이 ' . $work_message . '되었습니다.');

        } else if($data_type == 12) {
            
            $FacilityModel->update($id, [ 'area_data' => $data ]);
            return $this->alert('안전발판 산출식이 ' . $work_message . '되었습니다.');

        }else if($data_type == 13) {
            
            $FacilityModel->update($id, [ 'area_result' => $data ]);
            return $this->alert('안전발판 물량이 ' . $work_message . '되었습니다.');

        } else if($data_type == 14) {
            
            $FacilityModel->update($id, [ 'danger_data' => $data ]);
            return $this->alert('달대비계 산출식이 ' . $work_message . '되었습니다.');

        } else if($data_type == 15) {
            
            $FacilityModel->update($id, [ 'danger_result' => $data ]);
            return $this->alert('달대비계 물량이 ' . $work_message . '되었습니다.');

        } else if($data_type == 16) {
            //삭제부분
            if($data == null) {
                return $this->alert('도면등록일은 삭제할 수 없습니다.');

            //수정부분
            } else {
                $FacilityModel->update($id, [ 'created_at' => $data ]);
                return $this->alert('도면등록일이 수정되었습니다.');
            }

        } else if($data_type > 16 && $data_type <= 23) {
            
            $o_serial = $_POST['o_serial'] ?? null;
            $r_num = $_POST['r_num'] ?? null;

            $FacilityModel->where('place_id', $this->auth->login_place_id())->where('o_serial', $o_serial);

            $TaskPlanModel = new TaskPlanModel();
            $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $o_serial);

            $data = $data == "" ? null : $data;
            
            $state_column = [

                'created_at',       //0
                'started_at',       //1
                'finished_at',      //2
                'edit_started_at',  //3
                'edit_finished_at', //4
                'dis_started_at',   //5
                'dis_finished_at',  //6
                'expired_at',       //7

            ];

            //설치시작일
            if($data_type == 17) {
                $this_state_column = 'started_at'; //1
                //삭제부분
                if($data == null) {
                    for($i=2; $i<7; $i++) {
                        $FacilityModel->set($state_column[$i], null);
                    }
                }
                //수정 및 추가부분
                $FacilityModel->set($this_state_column, $data)->update();
                return $this->alert('설치시작일이 ' . $work_message . '되었습니다.');

            //승인완료일
            } else if($data_type == 18) {
                $this_state_column = 'finished_at'; //2
                
                //삭제부분
                if($data == null) {
                    for($i=3; $i<7; $i++) {
                        $FacilityModel->set($state_column[$i], null);
                    }
                    $FacilityModel->set('expired_at', null); //승인완료일을 삭제하면 만료일도 함께 삭제된다.
                    //해당 o_serial의 수정계획(type=2), 해체계획(type=3)을 삭제한다
                    $TaskPlanModel->groupStart()->where('type', 2)->orwhere('type', 3)->groupEnd()->delete(null, true);

                //수정 및 추가부분
                } else {
                    for($i=1; $i>0; $i--) {
                        $FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                    }
                }
                $FacilityModel->set($this_state_column, $data)->update();

                //생성될때(is_edit이 false일때)는 설치계획(type=1)을 삭제하고 작업기록의 size와 is_square를 최신값으로 업데이트한다.
                if($data != null && !$is_edit) {

                    $TaskPlanModel->where('type', 1)->delete(null, true);

                    $max_rnum_facility = $FacilityModel->select('serial, place_id, facility.o_serial, r_num, cube_result, danger_result')
                                                        ->join('(SELECT MAX(r_num) as r_num_max, o_serial from facility group by o_serial) f', '(f.r_num_max = r_num AND facility.o_serial = f.o_serial)', 'inner', false)
                                                        ->where('facility.o_serial', $o_serial)
                                                        ->where('place_id', $this->auth->login_place_id())
                                                        ->first();

                    if($max_rnum_facility['danger_result'] != 0) {
                        $size = $max_rnum_facility['danger_result'];
                        $is_square = 1;
                    } else {
                        $size = $max_rnum_facility['cube_result'];
                        $is_square = 0;
                    }

                    $TaskModel = new TaskModel();
                    $TaskModel->where('place_id', $this->auth->login_place_id())
                                ->where('facility_serial', $o_serial)
                                ->set('size', $size)
                                ->set('is_square', $is_square)
                                ->update();
                }

                return $this->alert('승인완료일이 ' . $work_message . '되었습니다.');

            //수정시작일
            } else if($data_type == 19) {
                $this_state_column = 'edit_started_at'; //3
                //삭제부분
                if($data == null) {
                    $FacilityModel->set('edit_finished_at', null);

                //수정 및 추가부분
                } else {
                    for($i=2; $i>0; $i--) {
                        $FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                    }
                    if(!$is_edit) {
                        for($i=5; $i<7; $i++) {
                            $FacilityModel->set($state_column[$i], null);
                        }
                    }
                }
                $FacilityModel->set($this_state_column, $data)->update();

                //생성될때(is_edit이 false일때)는 설치계획(type=1), 해체계획(type=3)을 삭제한다.
                if($data != null && !$is_edit) {
                    $TaskPlanModel->groupStart()->where('type', 1)->orWhere('type', 3)->groupEnd()->delete(null, true);
                }

                return $this->alert('수정시작일이 ' . $work_message . '되었습니다.');

            //수정완료일
            } else if($data_type == 20) {
                $this_state_column = 'edit_finished_at'; //4
                //삭제부분
                if($data == null) {
                    for($i=5; $i<7; $i++) {
                        $FacilityModel->set($state_column[$i], null);
                    }
                    //해당 o_serial의 설치계획(type=1), 해체계획(type=3)을 삭제한다
                    $TaskPlanModel->groupStart()->where('type', 1)->orwhere('type', 3)->groupEnd()->delete(null, true);

                //수정 및 추가부분
                } else {
                    for($i=3; $i>0; $i--) {
                        $FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                    }
                }
                $FacilityModel->set($this_state_column, $data)->update();

                //생성될때(is_edit이 false일때)는 설치계획(type=1), 수정계획(type=2)을 삭제한다.
                if($data != null && !$is_edit) {
                    $TaskPlanModel->groupStart()->where('type', 1)->orWhere('type', 2)->groupEnd()->delete(null, true);
                }

                return $this->alert('수정완료일이 ' . $work_message . '되었습니다.');
    
            //해체시작일
            } else if($data_type == 21) {
                $this_state_column = 'dis_started_at'; //5
                //삭제부분
                if($data == null) {
                    for($i=6; $i<7; $i++) {
                        $FacilityModel->set($state_column[$i], null);
                    }

                //수정 및 추가부분
                } else {
                    for($i=4; $i>0; $i--) {
                        if($i==3 || $i==4) {
                            $FacilityModel->set($state_column[$i], "IF(edit_started_at IS NOT NULL and " . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                        } else {
                            $FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                        }
                    }
                }
                $FacilityModel->set($this_state_column, $data)->update();

                //생성될때(is_edit이 false일때)는 설치계획(type=1), 수정계획(type=2)을 삭제한다.
                if($data != null && !$is_edit) {
                    $TaskPlanModel->groupStart()->where('type', 1)->orWhere('type', 2)->groupEnd()->delete(null, true);
                }

                return $this->alert('해체시작일이 ' . $work_message . '되었습니다.');
    
            //해체완료일
            } else if($data_type == 22) {
                $this_state_column = 'dis_finished_at'; //6
                //수정 및 추가부분
                for($i=5; $i>0; $i--) {
                    if($i==3 || $i==4) {
                        $FacilityModel->set($state_column[$i], "IF(edit_started_at IS NOT NULL and " . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                    } else {
                        $FacilityModel->set($state_column[$i], "IF(" . $state_column[$i] . " IS NULL, '" . $data . "', " . $state_column[$i] . ")", false);
                    }
                }
                $FacilityModel->set($this_state_column, $data)->update();

                //생성될때(is_edit이 false일때)는 해당 o_serial의 작업계획을 모두 삭제한다.
                if(!$is_edit) {
                    $TaskPlanModel->delete(null, true);
                }

                return $this->alert('해체완료일이 ' . $work_message . '되었습니다.');
    
            //만료일
            } else if($data_type == 23) {
                
                //$FacilityModel->set('expired_at', "IF(r_num > " . $r_num . " and expired_at IS NULL, '" . $data . "', expired_at)", false);
                //$FacilityModel->set('expired_at', "IF(r_num = " . $r_num . ", '" . $data . "', expired_at)", false)->update();
                $FacilityModel->set('expired_at', "IF(r_num >= " . $r_num . ", '" . $data . "', expired_at)", false)->update();
                return $this->alert('만료일이 ' . $work_message . '되었습니다.');
            }

        } else if($data_type == 24) {

            $data = $data == "" ? null : $data;
            
            $FacilityModel->update($id, [ 'memo' => $data ]);
            return $this->alert('비고가 ' . $work_message . '되었습니다.');
            
        } else {
            
            return $this->alert('오류가 있습니다.');
        }
    }

    public function edit_taskplan() {

        $taskplan_id = $_POST['taskplan_id'] ?? null;
        $facility_serial = $_POST['o_serial'] ?? null;
        $facility_state = $_POST['facility_state'] ?? null;
        $taskplan_type = $_POST['taskplan_type'] ?? null;
        $team_id = $_POST['team_id'] ?? null;
        
        if($facility_serial == null || $team_id == null || $facility_state == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }
        if($taskplan_type == null) {
            return $this->alert('작업내용이 선택되지 않았습니다.');
        }

        $TaskPlanModel = new TaskPlanModel();

        //삭제, 수정상황
        if($taskplan_id != null) {

            //삭제상황
            if($taskplan_type == -1) {

                $TaskPlanModel->delete($taskplan_id, true);
    
                return $this->alert('작업계획을 삭제했습니다.');
    
            //수정상황
            } else {
                $TaskPlanModel->update($taskplan_id, [
                    'type' => $taskplan_type,
                    'team_id' => $team_id,
                ]);
                return $this->alert('작업계획을 수정했습니다.');
            }

        //추가상황
        } else {
            $taskplan = $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $facility_serial)->first();

            if($taskplan == null) {
    
                $TaskPlanModel->insert([
                    'place_id' => $this->auth->login_place_id(),
                    'facility_serial' => $facility_serial,
                    'type' => $taskplan_type,
                    'team_id' => $team_id,
                ]);
                return $this->alert('작업계획을 추가했습니다.');

            //오류상황
            } else {
                return $this->alert('작업계획을 수정할 수 없습니다.');
            }
        }
    }

    public function edit_task() {

        $task_id = $_POST['task_id'] ?? null;
        $task_date = $_POST['task_date'] ?? Time::now();
        $task_type = $_POST['task_type'] ?? null;
        $team_id = $_POST['team_id'] ?? null;
        $manday = $_POST['manday'] ?? null;
        $facility_serial = $_POST['o_serial'] ?? null;
        $size = $_POST['size'] ?? null;
        $is_square = $_POST['is_square'] ?? null;

        if($task_type == null || $team_id == null || $manday == null || $facility_serial == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskModel = new TaskModel();

        //추가상황
        if($task_id == null) {

            $TaskModel->insert([

                'type' => $task_type,
                'place_id' => $this->auth->login_place_id(),
                'facility_serial' => $facility_serial,
                'team_id' => $team_id,
                'size' => $size,
                'is_square' => $is_square,
                'manday' => $manday,
                'created_at' => $task_date,

            ]);

            return $this->alert('작업내역을 추가했습니다.');

        } else {
            //삭제상황
            if($manday == -1) {

                $TaskModel->delete($task_id, true);

                return $this->alert('작업내역을 삭제했습니다.');

            //수정상황
            } else {
                $TaskModel->update($task_id, [
                    'type' => $task_type,
                    'place_id' => $this->auth->login_place_id(),
                    'facility_serial' => $facility_serial,
                    'team_id' => $team_id,
                    'manday' => $manday,
                ]);
        
                return $this->alert('작업내역을 수정했습니다.');
            }
        }
    }

    //작업내역 추가 기능


    public function view_etc_task() {

        if(!$this->auth->is_logged_in()) {

         return $this->login_fail();

        }

        $query = "(SELECT task.place_id as place_id, task.type as type, task.facility_serial as task_name, t.id as team_id, t.name as team_name, task.manday as manday, task.created_at as created_at from task join team as t on t.id = task.team_id where task.place_id = '".$this->auth->login_place_id()."' and task.type = '4')"
        ." UNION (SELECT taskplan.place_id as place_id, taskplan.type as type, taskplan.facility_serial as task_name, tt.id as team_id, tt.name as team_name, NULL as manday, NULL as created_at from taskplan join team as tt on tt.id = taskplan.team_id where taskplan.place_id = '".$this->auth->login_place_id()."' and taskplan.type = '4')";
        //." order by created_at is null ASC, created_at ASC;";

        $tasks = db_connect()->query($query)->getResult('array');

        $etc_tasks = [];

        foreach($tasks as $task) {

            if(!array_key_exists($task['task_name'], $etc_tasks)) {

                $etc_tasks[$task['task_name']] = [

                    $task['team_id'].'__'.$task['team_name'] => [

                        'total_manday' => 0,
                        'total_task' => 0,
                        'started_at' => null,
                        'finished_at' => null,

                    ],
                ];

            } else if(!array_key_exists($task['team_id'].'__'.$task['team_name'], $etc_tasks[$task['task_name']])) {
                
                $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']] = [

                    'total_manday' => 0,
                    'total_task' => 0,
                    'started_at' => null,
                    'finished_at' => null,

                ];
            }

            if($task['manday'] == null) { // taskplan

                $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['finished_at'] = null;
                

            } else { // task

                $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['total_manday'] += $task['manday'];
                $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['total_task']++;

                $created_at = Time::createFromFormat('Y-m-d H:i:s', $task['created_at']);
                
                if($etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['started_at'] == null || $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['started_at']->isBefore($created_at))
                    $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['started_at'] = $created_at;
                
                if($etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['finished_at'] == null || $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['finished_at']->isAfter($created_at)) 
                    $etc_tasks[$task['task_name']][$task['team_id'].'__'.$task['team_name']]['finished_at'] = $created_at;

            }
        }

        $TeamModel = new TeamModel();
        $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();
        
        $data = [

            'etc_tasks' => $etc_tasks,
            'teams' => $teams,

        ];

        return view('view_etc_task', $data);

    }

    public function download_etc_task() {

        $place_id = $this->auth->login_place_id();

        $PlaceModel = new PlaceModel();
        $place_name = $PlaceModel->where('id', $place_id)->first()['name'];

        $now_timestamp = date("Ymd");

        $query = "(SELECT task.place_id as place_id, task.type as type, task.facility_serial as task_name, t.id as team_id, t.name as team_name, task.manday as manday, task.created_at as created_at from task join team as t on t.id = task.team_id where task.place_id = '". $place_id ."' and task.type = '4')"
        ." UNION (SELECT taskplan.place_id as place_id, taskplan.type as type, taskplan.facility_serial as task_name, tt.id as team_id, tt.name as team_name, NULL as manday, NULL as created_at from taskplan join team as tt on tt.id = taskplan.team_id where taskplan.place_id = '". $place_id ."' and taskplan.type = '4')";
        //." order by created_at is null ASC, created_at ASC;";

        $tasks = db_connect()->query($query)->getResult('array');

        $etc_tasks = [];

        foreach($tasks as $task) {

            if(!array_key_exists($task['task_name'], $etc_tasks)) {

                $etc_tasks[$task['task_name']] = [

                    $task['team_name'] => [

                        'total_manday' => 0,
                        'total_task' => 0,
                        'started_at' => null,
                        'finished_at' => null,

                    ],
                ];

            } else if(!array_key_exists($task['team_name'], $etc_tasks[$task['task_name']])) {
                
                $etc_tasks[$task['task_name']][$task['team_name']] = [

                    'total_manday' => 0,
                    'total_task' => 0,
                    'started_at' => null,
                    'finished_at' => null,

                ];
            }

            if($task['manday'] == null) { // taskplan

                $etc_tasks[$task['task_name']][$task['team_name']]['finished_at'] = null;
                

            } else { // task

                $etc_tasks[$task['task_name']][$task['team_name']]['total_manday'] += $task['manday'];
                $etc_tasks[$task['task_name']][$task['team_name']]['total_task']++;

                $created_at = Time::createFromFormat('Y-m-d H:i:s', $task['created_at']);
                
                if($etc_tasks[$task['task_name']][$task['team_name']]['started_at'] == null || $etc_tasks[$task['task_name']][$task['team_name']]['started_at']->isBefore($created_at))
                    $etc_tasks[$task['task_name']][$task['team_name']]['started_at'] = $created_at;
                
                if($etc_tasks[$task['task_name']][$task['team_name']]['finished_at'] == null || $etc_tasks[$task['task_name']][$task['team_name']]['finished_at']->isAfter($created_at)) 
                    $etc_tasks[$task['task_name']][$task['team_name']]['finished_at'] = $created_at;

            }
        }

        $excel_array = [];

        array_push($excel_array, ["작업명", "작업팀", "총투입인원", "작업시작일", "작업완료일"]);
        
        foreach($etc_tasks as $task_name => $etc_task_teams) {

            foreach($etc_task_teams as $team_name => $etc_task) {

                array_push($excel_array, [$task_name, $team_name, $etc_task['total_manday'], $etc_task['started_at'] != null ? $etc_task['started_at']->toDateString() : "", $etc_task['finished_at'] != null ? $etc_task['finished_at']->toDateString() : ""]);
            }

        }

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray($excel_array, NULL, 'A1', true);

        $file_path = tempnam(sys_get_temp_dir(), 'xl_');

        $writer = new Xlsx($spreadsheet);
        $writer->save($file_path);

        return $this->response->download($file_path, null)->setFileName($place_name . '_기타작업_' . $now_timestamp .'.xlsx');

    }

    public function add_etc_taskplan() {

        $task_name = $_POST['task_name'] ?? null;
        $team_id = $_POST['team_id'] ?? null;

        if($task_name == null || $team_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskPlanModel = new TaskPlanModel();

        $this_taskplan = $TaskPlanModel->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('type', 4)
        ->where('team_id', $team_id)
        ->first();

        if($this_taskplan != null){
            return $this->alert('같은 작업계획이 있습니다.');
        }

        $TaskPlanModel->insert([
            'place_id' => $this->auth->login_place_id(),
            'facility_serial' => $task_name,
            'type' => 4,
            'team_id' => $team_id,
        ]);

        return $this->alert('작업계획이 추가되었습니다.');
    }


    public function view_etc_task_info($team_id, $task_name) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $TeamModel = new TeamModel();
            $this_team = $TeamModel->where('id', $team_id)->first();
            
            if(Time::now() <= Time::createFromTime(5, 0, 0)) {
                $today_start_time = Time::now()->yesterday()->setHour(5)->setMinute(0)->setSecond(0);

            } else {
                $today_start_time = Time::now()->setHour(5)->setMinute(0)->setSecond(0);
            }
            
            $AttendanceModel = new AttendanceModel();

            $attendance = $AttendanceModel->select('teammate.name as name')
                                            ->join('teammate', 'attendance.teammate_id = teammate.id and teammate.team_id = "' . $team_id . '"')
                                            ->where('attendance.type', '0')
                                            ->where('attendance.created_at >', $today_start_time)
                                            ->countAllResults();

            $this_team['attendance'] = $attendance;

            $TaskPlanModel = new TaskPlanModel();
            $this_taskplan = $TaskPlanModel->where('place_id', $this->auth->login_place_id())->where('facility_serial', $task_name)->where('type', 4)->where('team_id', $team_id)->first();

            $TaskModel = new TaskModel();
            $etc_tasks = $TaskModel->select('task.*, team.name as team_name')
                                ->join('team', 'team.id = task.team_id')
                                ->where('task.type =', 4)
                                ->where('task.place_id', $this->auth->login_place_id())
                                ->where('task.facility_serial', $task_name)
                                ->where('task.team_id', $team_id)
                                ->orderBy('created_at', 'ASC')
                                ->findAll();

            $progress = 0;

            if(count($etc_tasks) > 0) {
                if($this_taskplan != null) {
                    $progress = 1;
                } else {
                    $progress = 2;
                }
            }

            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();

            $data = [

                'task_name' => $task_name,
                'this_team' => $this_team,
                'etc_tasks' => $etc_tasks,
                'progress' => $progress,
                'teams' => $teams,

            ];

            return view('view_etc_task_info.php', $data);
        }
    }
    
    public function change_etc_task_team() {

        $task_name = $_POST['task_name'] ?? null;
        $old_team_id = $_POST['old_team_id'] ?? null;
        $new_team_id = $_POST['new_team_id'] ?? null;

        if($task_name == null || $old_team_id == null || $new_team_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskPlanModel = new TaskPlanModel();
        $TaskModel = new TaskModel();

        //같은 작업 계획이 있다면 작업계획 삭제, 없으면 팀을 변경
        $new_taskplan = $TaskPlanModel->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('type', 4)
        ->where('team_id', $new_team_id)
        ->first();
        
        if($new_taskplan == null) {

            $TaskPlanModel->where('place_id', $this->auth->login_place_id())
            ->where('facility_serial', $task_name)
            ->where('type', 4)
            ->where('team_id', $old_team_id)
            ->set('team_id', $new_team_id)
            ->update();

        } else {

            $TaskPlanModel->where('place_id', $this->auth->login_place_id())
            ->where('facility_serial', $task_name)
            ->where('type', 4)
            ->where('team_id', $old_team_id)
            ->delete(null, true);

        }

        //해당 작업들이 없다면 아무 작업 안함, 있으면 작업의 팀을 변경
        $TaskModel->where('type', 4)
        ->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('team_id', $old_team_id)
        ->set('team_id', $new_team_id)
        ->update();

        return $this->view_etc_task_info($new_team_id, $task_name);
    }

    public function add_etc_task() {

        $task_name = $_POST['task_name'] ?? null;
        $team_id = $_POST['team_id'] ?? null;
        $manday = $_POST['manday'] ?? null;
        $task_calendar = $_POST['task_calendar'] ?? null;

        if($task_name == null || $team_id == null || $manday == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        if($task_calendar == "0"){
            $task_calendar = Time::now()->setHour(7)->setMinute(0)->setSecond(0);
        }

        $TaskModel = new TaskModel();

        $TaskModel->insert([
            'type' => 4,
            'place_id' => $this->auth->login_place_id(),
            'facility_serial' => $task_name,
            'team_id' => $team_id,
            'manday' => $manday,
            'created_at' => $task_calendar,
        ]);

        return $this->alert('작업기록이 추가되었습니다.');
    }

    public function finish_etc_taskplan() {

        $task_name = $_POST['task_name'] ?? null;
        $team_id = $_POST['team_id'] ?? null;

        if($task_name == null || $team_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskPlanModel = new TaskPlanModel();

        $TaskPlanModel->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('type', 4)
        ->where('team_id', $team_id)
        ->delete(null, true);

        return $this->alert('작업이 완료되었습니다.');
    }

    public function delete_etc_taskplan() {

        $task_name = $_POST['task_name'] ?? null;
        $team_id = $_POST['team_id'] ?? null;

        if($task_name == null || $team_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskPlanModel = new TaskPlanModel();
        $TaskModel = new TaskModel();

        $TaskPlanModel->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('type', 4)
        ->where('team_id', $team_id)
        ->delete(null, true);

        $TaskModel->where('type', 4)
        ->where('place_id', $this->auth->login_place_id())
        ->where('facility_serial', $task_name)
        ->where('team_id', $team_id)
        ->delete(null, true);

        return $this->view_etc_task();
    }

    public function edit_etc_task() {

        $task_id = $_POST['task_id'] ?? null;
        $manday = $_POST['manday'] ?? null;

        if($task_id == null || $manday == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskModel = new TaskModel();

        //삭제상황
        if($manday < 0) {

            $TaskModel->delete($task_id, true);
    
            return $this->alert('작업기록이 삭제되었습니다.');

        //수정상황
        } else {

            try {

                $TaskModel->update($task_id, [
                    'manday' => $manday
                ]);
        
                return $this->alert('작업기록이 수정되었습니다.');

            } catch (\Exception $e) {

                return redirect()->back()->with('alert', ' 작업기록 수정에 실패했습니다.');

            }
        }
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

    public function edit_place() {

        $place_id = $_POST['place_id'] ?? null;
        $data = $_POST['data'] ?? null;
        $is_hide = $_POST['is_hide'] ?? null;
        $is_hide = $is_hide != null ? 1 : 0;
        $is_delete = $_POST['is_delete'] ?? null;
        $is_delete = $is_delete == "true" ? true : false;

        $PlaceModel = new PlaceModel();

        //현장삭제상황
        if($is_delete) {

            $FacilityModel = new FacilityModel();
            $TaskModel = new TaskModel();
            $TeamModel = new TeamModel();
            $UserModel = new UserModel();

            $exist_data = [];

            $facility = $FacilityModel->where('place_id', $place_id)->first();
            if($facility != null) {
                array_push($exist_data, "facility");
            }

            $task = $TaskModel->where('place_id', $place_id)->first();
            if($task != null) {
                array_push($exist_data, "task");
            }

            $team = $TeamModel->where('place_id', $place_id)->first();
            if($team != null) {
                array_push($exist_data, "team");
            }

            $user =$UserModel->where('place_id', $place_id)->first();
            if($user != null) {
                array_push($exist_data, "user");
            }

            if(count($exist_data) > 0) {

                return $this->confirm_delete_place($place_id, $exist_data);

            } 

            try {
                $PlaceModel->delete($place_id, true);
                return $this->alert('현장이 삭제되었습니다.');

            } catch (\Exception $e) {

                return $this->alert('현장이 삭제에 실패했습니다.');
            }
            

        } else {

            //현장추가상황
            if($place_id == null) {
                
                if($data == null || $data == "") {
                    return $this->alert('현장명이 입력되지 않았습니다.');
                }

                $new_place = [ 'name' => $data, ];

                try {
                    $PlaceModel->insert($new_place);
                    return $this->alert('현장이 추가되었습니다.');

                } catch (\Exception $e) {
                    return $this->alert('현장 추가에 실패했습니다.');
                }

            //현장명 수정 상황
            } else {
                
                if($data == null || $data == "") {
                    return $this->alert('바꿀 이름이 입력되지 않았습니다.');
                }
                
                try {

                    $PlaceModel->update($place_id, [
                        'name' => $data,
                        'is_hide' => $is_hide,
                    ]);
                    return $this->alert('현장정보가 수정되었습니다.');

                } catch (\Exception $e) {

                    return $this->alert('현장정보가 수정에 실패했습니다.');

                }
            }
        }        
    }

    public function confirm_delete_place($place_id, $exist_data) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            if($place_id == null) {

                return $this->alert('값이 올바르지 않습니다.');

            }

            $PlaceModel = new PlaceModel();

            $place = $PlaceModel->where('id', $place_id)->first();

            $data = [
                'place' => $place,
                'exist_data' => $exist_data,
            ];

            return view('delete_place.php', $data);
        }
    }

    public function delete_place() {

        $place_id = $_POST['place_id'] ?? null;
        $login_place_id = $this->auth->login_place_id();


        if($place_id == null) {

            return $this->alert('값이 올바르지 않습니다.');

        }

        $PlaceModel = new PlaceModel();
        $PlaceModel->delete($place_id, true);

        //지금 로그인한 현장을 삭제했을시
        if($place_id == $login_place_id) {

            return redirect()->to('/fm/logout')->withInput()->with('alert', '현장이 삭제되었습니다.');


        } else {

            return redirect()->to('/fm/set_place')->withInput()->with('alert', '현장이 삭제되었습니다.');

        }
    }


    /*-----------------------------------------직원등급관리-----------------------------------------*/

    public function set_user() {
        
        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $UserModel = new UserModel();

            $users = $UserModel->where('place_id', $this->auth->login_place_id())->orderBy('level', 'DESC')->orderBy('username', 'ASC')->findAll();
    
            $data = [
            
                'level' => $this->auth->level(),
                'users' => $users,  
            ];

            return view('set_user.php', $data);

        }
    }

    public function edit_user_info() {

        $id = $_POST['user_id'] ?? null;
        $_new_birthday = $_POST['new_birthday'] ?? null;
        $new_level = $_POST['new_level'] ?? null;
        $user_delete = $_POST['user_delete'] == "true"? true : false;


        if($id == null || $_new_birthday == null || $new_level == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $UserModel = new UserModel();

        //사용자 삭제 상황
        if($user_delete) {

            $UserModel->delete($id, true);

            return $this->alert('사용자가 삭제되었습니다.');

        //사용자 정보 수정 상황
        } else {

            $preg_match_birthday = false;

            if(is_numeric($_new_birthday) && strlen($_new_birthday) == 6){
    
                $matches = [];
    
                $preg_match_birthday = preg_match('/^[0-9]{2}(0[1-9]|1[0-2])(0[1-9]|[1,2][0-9]|3[0,1])$/', $_new_birthday, $matches);
        
            }

            //패스워드가 생년월일 양식일시
            if($preg_match_birthday) {
    
                $new_birthday = $matches[0];

            //패스워드가 8자리이상 숫자일시
            }else if(is_numeric($_new_birthday) && strlen($_new_birthday) >= 8) {
    
                $new_birthday = $_new_birthday;
            
            //그외
            } else {
    
                return $this->alert('패스워드는 생년월일 혹은 8자리 이상의 숫자로 지정해주세요.');
    
            }

            try{

                $UserModel->update($id, [
                    'birthday' => $new_birthday,
                    'level' => $new_level,
                ]);
    
                return $this->alert('사용자정보가 수정되었습니다.');

            } catch (\Exception $e) {

                return redirect()->back()->with('alert', '사용자정보 수정에 실패했습니다.');
                
            }
        }
    }

    /*-----------------------------------------생산성조회-----------------------------------------*/

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

    public function get_first_productivity_task($place_id) {
        $TaskModel = new TaskModel();

        $tasks = $TaskModel->select('ttt.size_current as size_current, ttt.is_square_current as is_square_current, task.facility_serial, t.manday_max as manday_max, task.type, task.place_id, task.created_at as created_at')
                            ->join('facility as f', '(f.o_serial = task.facility_serial AND f.place_id = task.place_id)', 'inner', false)
                            ->join('(SELECT MAX(manday) as manday_max, facility_serial from task where type = 1 group by facility_serial) t', 't.facility_serial = task.facility_serial', 'inner', false)
                            ->join('(SELECT MAX(created_at) as max_created_at, facility_serial from task where type = 1 group by facility_serial) as tt', 'tt.facility_serial = task.facility_serial', 'inner', false)
                            ->join('(SELECT size as size_current, is_square as is_square_current, created_at, facility_serial from task where type = 1) as ttt', "(ttt.created_at = tt.max_created_at AND ttt.facility_serial = tt.facility_serial)", 'inner', false)
                            ->join('place as p', 'p.id = task.place_id')
                            ->where('task.type', 1)
                            ->where('p.id', $place_id)
                            ->orderBy('task.created_at', 'ASC')
                            ->groupBy('task.facility_serial, ttt.size_current, ttt.is_square_current, task.place_id, task.created_at')
                            ->first();

        return $tasks;
    }

    public function get_productivity_manday($team_id, $start_time, $end_time) {

        $TaskModel = new TaskModel();

        /*선생님과 함께 수정한것
        $query = "SELECT tt.id as id, tt.type as type, tt.facility_serial as facility_serial, tt.manday as manday_max, t.s_created_at as s_created_at"
                ." FROM (select MAX(manday) as manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task where team_id = '".$team_id."' group by s_created_at) td"
                ." INNER JOIN (select max(id) as id, manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task group by manday, s_created_at) t ON t.manday = td.manday and t.s_created_at = td.s_created_at"
                ." JOIN task as tt ON t.id = tt.id"
                ." WHERE tt.type != 1 and t.s_created_at >= '".$start_time->toDateString()."' and t.s_created_at < '".$end_time->toDateString()."'"
                ." and tt.deleted_at IS NULL"
                ." order by t.s_created_at asc";

        $tasks_manday = db_connect()->query($query)->getResult('array');
        */
        /*기존것
        $tasks_manday = $TaskModel->select("ANY_VALUE(task.id) as id, ANY_VALUE(t.type) as type, ANY_VALUE(t.facility_serial) as facility_serial, MAX(task.manday) as manday_max, date_format(task.created_at, '%Y-%m-%d') as s_created_at")
                                    ->join("( SELECT id, type, facility_serial from task ) t", '(t.id = task.id)', 'inner', false)
                                    ->groupStart()->where('task.type', 2)->orWhere('task.type', 3)->orWhere('task.type', 4)->groupEnd()
                                    ->where('task.team_id', $team_id)
                                    ->where('task.created_at >= ', $start_time)
                                    ->where('task.created_at < ', $end_time)
                                    ->groupBy('s_created_at')
                                    ->orderBy('s_created_at', 'ASC')
                                    ->findAll();
        */

        $tasks_manday = $TaskModel->select("task.*, tm.manday as manday_max, tm.s_created_at as s_created_at")
                                    ->from("(SELECT MAX(manday) as manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task where team_id = '".$team_id."' AND type != 1 group by s_created_at) tm")
                                    ->join("(SELECT MAX(id) as id, manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task where team_id = '".$team_id."' AND type != 1 group by manday, s_created_at) ti", "(task.id = ti.id AND ti.manday = tm.manday AND ti.s_created_at = tm.s_created_at)", 'inner', false)
                                    ->where('created_at >= ', $start_time)
                                    ->where('created_at < ', $end_time)
                                    ->orderBy('created_at', 'ASC')
                                    ->findAll();

        return $tasks_manday;

    }

    public function get_first_productivity_manday($place_id) {

        $TaskModel = new TaskModel();

        $tasks_manday = $TaskModel->select("task.*, tm.manday as manday_max, tm.s_created_at as s_created_at")
                                    ->from("(SELECT MAX(manday) as manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task where type != 1 group by s_created_at) tm")
                                    ->join("(SELECT MAX(id) as id, manday, date_format(created_at, '%Y-%m-%d') as s_created_at from task where type != 1 group by manday, s_created_at) ti", "(task.id = ti.id AND ti.manday = tm.manday AND ti.s_created_at = tm.s_created_at)", 'inner', false)
                                    ->join('place as p', 'p.id = task.place_id')
                                    ->where('p.id', $place_id)
                                    ->orderBy('created_at', 'ASC')
                                    ->first();

        return $tasks_manday;

    }
    
    public function view_productivity($_target_time = 0) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {
            
            if($_target_time == null || !is_numeric($_target_time)) {
                $target_time = Time::now();
            } else {
                $target_time = Time::createFromTimestamp($_target_time);
            }

            $is_after = false;

            $year = $target_time->getYear();
            $month = $target_time->getMonth();

            $start_time = $target_time->setMonth($month)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            if($month == 12) {

                $end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            } else {

                $end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            }

            $TeamModel = new TeamModel();
            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();

            $team_ids = array_map(function($team) {
                return $team['id'];
            }, $teams);

            $totals_cube = [];
            $totals_square = [];
            $totals_manday = [];

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

            $is_after = $end_time->isAfter(Time::now());

            $first_task = $this->get_first_productivity_task($this->auth->login_place_id());
            $first_manday = $this->get_first_productivity_manday($this->auth->login_place_id());
            
            $first_task_date = $first_task['created_at'] ?? null;
            $first_manday_date = $first_manday['created_at'] ?? null;

            $before_exists = false;           

            if($first_task_date != null )
                $before_exists |= Time::createFromFormat("Y-m-d H:i:s", $first_task_date)->isBefore($start_time);
            
            if($first_manday_date != null)
                $before_exists |= Time::createFromFormat("Y-m-d H:i:s", $first_manday_date)->isBefore($start_time);

            $data = [      
                'teams' => $teams,
                        
                'target_time' => $target_time,

                'totals_cube' => $totals_cube,
                'totals_square' => $totals_square,
                'totals_manday' => $totals_manday,

                'is_after' => $is_after,
                'before_exists' => $before_exists,

            ];

            return view('view_productivity.php', $data);
        }
    }

    public function view_productivity_team($team_id = 0, $_target_time = 0) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            if($_target_time == null || !is_numeric($_target_time)) {
                $target_time = Time::now();
            } else {
                $target_time = Time::createFromTimestamp($_target_time);
            }
            
            $is_after = false;

            $year = $target_time->getYear();
            $month = $target_time->getMonth();

            $start_time = $target_time->setMonth($month)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            if($month == 12) {

                $end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            } else {

                $end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            }

            $TeamModel = new TeamModel();
            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();
            $this_team = $TeamModel->where('id', $team_id)->first();

            $tasks = $this->get_productivity_task($team_id, $start_time, $end_time);
            $tasks_manday = $this->get_productivity_manday($team_id, $start_time, $end_time);

            $is_after = $end_time->isAfter(Time::now());

            $data = [      
                'teams' => $teams,
                        
                'this_team' => $this_team,
                'target_time' => $target_time,

                'tasks' => $tasks,
                'tasks_manday' => $tasks_manday,
                'is_after' => $is_after,
            ];

            return view('view_productivity_team.php', $data);
            
        }
    }

    public function view_facility_max_rnum($facility_serial) {

        $FacilityModel = new FacilityModel();

        $place_id = $this->auth->login_place_id();

        $facility = $FacilityModel->select('serial, r_num, facility.o_serial, place_id')
                                ->join('(SELECT MAX(r_num) as r_num_max, o_serial from facility group by o_serial) f', '(f.r_num_max = r_num AND facility.o_serial = f.o_serial)', 'inner', false)
                                ->where('facility.o_serial', $facility_serial)
                                ->where('place_id', $place_id)
                                ->first();
        
        if($facility == null) return;

        //return $this->view_facility_info($facility['serial']);
        return redirect()->to('/fm/view_facility_info/' . $facility['serial']);
    }


    public function view_manday_team($team_id, $target_time) {

        if(!$this->auth->is_logged_in()) {

			return $this->login_fail();

        } else {

            $TaskModel = new TaskModel();
            $TeamModel = new TeamModel();
    
            $target_time = Time::createFromTimestamp($target_time);
    
            $start_time = $target_time->setHour(0)->setMinute(0)->setSecond(0);
            $end_time = $start_time->addDays(1)->setHour(0)->setMinute(0)->setSecond(0);
    
            $tasks = $TaskModel->where('created_at >=', $start_time)
                                ->where('created_at <', $end_time)
                                ->where('team_id', $team_id)
                                ->where('type !=' , 1)
                                ->orderBy('created_at', 'ASC')
                                ->findAll();

            $this_team = $TeamModel->where('id', $team_id)->first();
    
            $data = [
                'tasks' => $tasks,
                'this_team' => $this_team,
                'target_time' => $target_time,
            ];
    
            return view('view_manday_team.php', $data);
        }
    }

    public function edit_etc_task_manday() {

        $team_id = $_POST['team_id'] ?? null;
        $task_id = $_POST['task_id'] ?? null;
        $manday = $_POST['manday'] ?? null;
        $target_time = $_POST['target_time'] ?? null;

        var_dump($task_id);
        var_dump($manday);

        if($task_id == null || $manday == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $TaskModel = new TaskModel();

        try {

            $TaskModel->update($task_id, [
                'manday' => $manday
            ]);
    
            //return $this->alert('작업기록이 수정되었습니다.');
            return redirect()->to('/fm/view_manday_team/' . $team_id . '/' . $target_time)->withInput()->with('alert', '작업기록이 수정되었습니다.');

        } catch (\Exception $e) {

            return redirect()->back()->with('alert', ' 작업기록 수정에 실패했습니다.');

        }

    }

    /*-----------------------------------------안전점수조회-----------------------------------------*/

    public function view_safe_point($_target_time = 0) {

        if(!$this->auth->is_logged_in()) {

         return $this->login_fail();

        } else {

            $is_first_month = true;
            $is_after = false;
            
            if($_target_time == null || !is_numeric($_target_time)) {
                $target_time = Time::now();
            } else {
                $target_time = Time::createFromTimestamp($_target_time);
            }

            $year = $target_time->getYear();
            $month = $target_time->getMonth();

            $start_time = $target_time->setMonth($month)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            if($month == 12) {

                $end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            } else {

                $end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            }

            $TeamModel = new TeamModel();
            $SafePointModel = new SafePointModel();
            $TeamSafePointModel = new TeamSafePointModel();

            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();

            $safe_points = $SafePointModel->findAll();

            $first_safe_point = $TeamSafePointModel->select('team_safe_point.*')
                                ->join('team', 'team_safe_point.team_id = team.id AND team.place_id = "' . $this->auth->login_place_id() . '"', 'inner', false)
                                ->orderBy('team_safe_point.created_at', 'ASC')
                                ->first();

            if($first_safe_point != null) {
                $is_first_month = Time::createFromFormat('Y-m-d H:i:s', $first_safe_point['created_at'])->isAfter($start_time);
            }

            $is_after = $end_time->isAfter(Time::now());

            $team_ids = array_map(function($team) {
                return $team['id'];
            }, $teams);

            $all_team_safe_points = $TeamSafePointModel
                            ->whereIn('team_id', $team_ids)
                            ->where('team_safe_point.created_at >= ', $start_time)
                            ->where('team_safe_point.created_at < ', $end_time)
                            ->findAll();

            $team_safe_points = [];

            foreach($all_team_safe_points as $team_safe_point) {

                if(!array_key_exists($team_safe_point['team_id'], $team_safe_points))
                    $team_safe_points[$team_safe_point['team_id']] = 0;
                    
                $team_safe_points[$team_safe_point['team_id']] += $team_safe_point['point'];

            }

            $data = [
                'target_time' => $target_time,
                'teams' => $teams,
                'safe_points' => $safe_points,
                'team_safe_points' => $team_safe_points,
                'is_first_month' => $is_first_month,
                'is_after' => $is_after,
            ];

            return view('view_safe_point.php', $data);
            
        }
    }

    public function download_report() {

        if(!$this->auth->is_logged_in()) {

            return $this->login_fail();

        } else if($this->request->getMethod() == 'post') {

            $place_id = $this->auth->login_place_id();

            $PlaceModel = new PlaceModel();
            $place_name = $PlaceModel->where('id', $place_id)->first()['name'];
            
            $now_timestamp = date("Ymd");

            $period = $_POST['period'] ?? null;

            $st_time = Time::now()->subMonths($period-1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            $TeamModel = new TeamModel();
            $TeamSafePointModel = new TeamSafePointModel();
            $teams = $TeamModel->where('place_id', $place_id)->orderBy('name', 'ASC')->findAll();

            $excel_array = [];

            for($i=0; $i<$period; $i++) {

                array_push($excel_array, [$st_time->getYear().'-'.$st_time->getMonth(), '1인당 수평비계 생산성(루베)', '1인당 달대비계 생산성(헤베)', '멘데이합계(공수)', '안전점수']);

                $team_array = [];
                
                foreach($teams as $team) {

                    $ed_time = $st_time->addMonths(1);

                    $productivity = $this->get_productivity_task($team['id'], $st_time, $ed_time);
                    $manday = $this->get_productivity_manday($team['id'], $st_time, $ed_time);
                    
                    $safe_points = $TeamSafePointModel
                            ->where('team_id', $team['id'])
                            ->where('team_safe_point.created_at >= ', $st_time)
                            ->where('team_safe_point.created_at < ', $ed_time)
                            ->findAll();

                    $safe_point = 100;
                    foreach($safe_points as $sp) $safe_point += $sp['point'];

                    $r=0; $h=0; $m=0;

                    foreach($productivity as $p) {

                        if($p['is_square_current'] == 0) {
                            $r = round($p['size_current'] / $p['manday_max'] , 1);
                        } else {
                            $h = round($p['size_current'] / $p['manday_max'] , 1);
                        }
                    }

                    foreach($manday as $mm) {

                        $m += $mm['manday_max'];

                    }

                    $team_array = [
                        $team['name'],
                        $r, $h, $m, $safe_point
                    ];

                    array_push($excel_array, $team_array);
                }
                array_push($excel_array, []);
                $st_time = $st_time->addMonths(1);

            }

            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->fromArray($excel_array, NULL, 'A1', true);

            $file_path = tempnam(sys_get_temp_dir(), 'xl_');

            $writer = new Xlsx($spreadsheet);
            $writer->save($file_path);

            return $this->response->download($file_path, null)->setFileName($place_name . '_종합보고서_' . $now_timestamp . '.xlsx');
            
        } else {

            $PlaceModel = new PlaceModel();

            $place_name = $PlaceModel->where('id', $this->auth->login_place_id())->first()['name'];

            return view('download_report.php', ['place_name' => $place_name]);

        }
    }

    public function add_safe_point() {

        $sp_name = $_POST['sp_name'] ?? null;
        $sp_point = $_POST['sp_point'] ?? null;

        if($sp_name == null || $sp_point == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $SafePointModel = new SafePointModel();

        $SafePointModel->insert([
            'name' => $sp_name,
            'point' => $sp_point,
        ]);

        return $this->alert('안전점수기준이 추가되었습니다.');
    }

    public function edit_safe_point() {

        $sp_id = $_POST['sp_id'] ?? null;
        $sp_name = $_POST['sp_name'] ?? null;
        $sp_point = $_POST['sp_point'] ?? null;
        $is_delete = $_POST['is_delete'] == "true" ? true : false;

        if($sp_id == null || $sp_name == null || $sp_point == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $SafePointModel = new SafePointModel();

        if($is_delete) {

            if($sp_id > 4) {

                $SafePointModel->delete($sp_id, true);
    
                return $this->alert('안전점수기준이 삭제되었습니다.');

            } else {
                return $this->alert('기본 안전점수기준은 삭제할 수 없습니다.');
            }

        } else {

            $SafePointModel->update($sp_id, [
                'name' => $sp_name,
                'point' => $sp_point,
            ]);

            return $this->alert('안전점수기준이 수정되었습니다.');
        }
    }

    public function view_safe_point_team($team_id = 0, $_target_time = 0) {

        if(!$this->auth->is_logged_in()) {

         return $this->login_fail();

        } else {

            $is_first_month = true;
            $is_after = false;

            if($_target_time == null || !is_numeric($_target_time)) {
                $target_time = Time::now();
            } else {
                $target_time = Time::createFromTimestamp($_target_time);
            }

            $year = $target_time->getYear();
            $month = $target_time->getMonth();

            $start_time = $target_time->setMonth($month)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            if($month == 12) {

                $end_time = $start_time->setYear($year+1)->setMonth(1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            } else {

                $end_time = $start_time->setMonth($month+1)->setDay(1)->setHour(0)->setMinute(0)->setSecond(0);

            }

            $TeamModel = new TeamModel();
            $TeamSafePointModel = new TeamSafePointModel();
            $SafePointModel = new SafePointModel();


            $teams = $TeamModel->where('place_id', $this->auth->login_place_id())->orderBy('name', 'ASC')->findAll();
            $this_team = $TeamModel->where('id', $team_id)->first();

            $team_safe_points = $TeamSafePointModel
                                ->where('team_id', $team_id)
                                ->where('team_safe_point.created_at >= ', $start_time)
                                ->where('team_safe_point.created_at < ', $end_time)
                                ->findAll();

            $safe_points = $SafePointModel->findAll();

            $first_safe_point = $TeamSafePointModel->select('team_safe_point.*')
                                ->join('team', 'team_safe_point.team_id = team.id AND team.place_id = "' . $this->auth->login_place_id() . '"', 'inner', false)
                                ->orderBy('team_safe_point.created_at', 'ASC')
                                ->first();

            if($first_safe_point != null) {
                $is_first_month = Time::createFromFormat('Y-m-d H:i:s', $first_safe_point['created_at'])->isAfter($start_time);
            }

            $is_after = $end_time->isAfter(Time::now());

            $data = [
                'target_time' => $target_time,
                'teams' => $teams,
                'this_team' => $this_team,
                'team_safe_points' => $team_safe_points,
                'safe_points' => $safe_points,
                'is_first_month' => $is_first_month,
                'is_after' => $is_after,
            ];

            return view('view_safe_point_team.php', $data);
        }
    }

    public function add_team_safe_point() {

        $team_id = $_POST['team_id'] ?? null;
        $team_sp_date = $_POST['team_sp_date'] ?? null;
        $sp_id = $_POST['sp_id'] ?? null;
        
        if($team_id == null || $sp_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $SafePointModel = new SafePointModel();
        $TeamSafePointModel = new TeamSafePointModel();

        $this_safe_point = $SafePointModel->where('id', $sp_id)->first();

        $TeamSafePointModel->insert([
            'team_id' => $team_id,
            'name' => $this_safe_point['name'],
            'point' => $this_safe_point['point'],
            'created_at' => $team_sp_date,
        ]);

        return $this->alert('안전점수가 부여되었습니다.');

    }

    public function edit_team_safe_point() {

        $team_sp_id = $_POST['team_sp_id'] ?? null;
        $sp_id = $_POST['sp_id'] ?? null;

        if($team_sp_id == null || $sp_id == null) {
            return $this->alert('값이 올바르지 않습니다.');
        }

        $SafePointModel = new SafePointModel();
        $TeamSafePointModel = new TeamSafePointModel();

        $this_safe_point = $SafePointModel->where('id', $sp_id)->first();

        //삭제인 경우
        if($sp_id < 0) {
            
            $TeamSafePointModel->delete($team_sp_id, true);
            return $this->alert('안전점수가 삭제되었습니다.');

        //무효인 경우
        } else if($sp_id == 0) {
            
            $TeamSafePointModel->update($team_sp_id, [
                'point' => 0,
            ]);

        //수정인 경우
        } else {

            $TeamSafePointModel->update($team_sp_id, [
                'name' => $this_safe_point['name'],
                'point' => $this_safe_point['point'],
            ]);

        }
        return $this->alert('안전점수가 수정되었습니다.');
    }



}
