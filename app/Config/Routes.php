<?php

namespace Config;

// RouteCollection 클래스의 새 인스턴스를 만듭니다.
$routes = Services::routes();

// 먼저 시스템의 라우팅 파일을 로드해서 앱과 ENVIRONMENT가 필요에 따라 재정의할 수 있도록 합니다.
if (file_exists(SYSTEMPATH . 'Config/Routes.php'))
{
	require SYSTEMPATH . 'Config/Routes.php';
}

/**
 * --------------------------------------------------------------------
 * Router Setup
 * --------------------------------------------------------------------
 */
$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('FMWebService');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// 디렉토리를 스캔할 필요가 없기 때문에 기본 경로를 지정하여 성능을 향상시킵니다.

//대문
//$routes->add('/', 'Home::index');

//웹서비스
$routes->group('fm', function($routes)
{
	$routes->add('', 'FMWebService::index');
	$routes->add('login', 'FMWebService::login');
	$routes->add('logout', 'FMWebService::logout');
	$routes->add('create_user', 'FMWebService::create_user');
	$routes->add('generate_user', 'FMWebService::generate_user');
	$routes->add('menu', 'FMWebService::menu');
	$routes->add('change_password', 'FMWebService::change_password');
	$routes->add('add_team', 'FMWebService::add_team');
	$routes->add('load_team_excel', 'FMWebService::load_team_excel');
	$routes->add('parse_team_data', 'FMWebService::parse_team_data');
	$routes->add('add_facility', 'FMWebService::add_facility');
	$routes->add('load_facility_excel', 'FMWebService::load_facility_excel');
	$routes->add('parse_facility_data', 'FMWebService::parse_facility_data');

	$routes->add('view_attendance', 'FMWebService::view_attendance');
	$routes->add('view_attendance/(:num)', 'FMWebService::view_attendance/$1', 					['as' => 'view_attendance_team']);
	$routes->add('view_attendance/(:num)/(:num)', 'FMWebService::view_attendance/$1/$2', 		['as' => 'view_attendance']);

	$routes->add('change_teammate_name', 'FMWebService::change_teammate_name');
	$routes->add('change_teammate_birthday', 'FMWebService::change_teammate_birthday');

	$routes->add('save_attendance_button', 'FMWebService::save_attendance_button');

	$routes->add('view_facility', 'FMWebService::view_facility');
	$routes->add('view_facility/(:any)', 'FMWebService::view_facility/$1');
	$routes->add('view_facility_info/(:num)', 'FMWebService::view_facility_info/$1');
	$routes->add('edit_facility_info', 'FMWebService::edit_facility_info');

	$routes->add('view_productivity', 'FMWebService::view_productivity/$1');
	$routes->add('view_productivity/(:segment)', 'FMWebService::view_productivity/$1');
	$routes->add('view_productivity/(:segment)/(:any)', 'FMWebService::view_productivity/$1/$2', ['as' => 'view_productivity']);

	$routes->add('view_safe_point', 'FMWebService::view_safe_point/$1');
	$routes->add('view_safe_point/(:segment)', 'FMWebService::view_safe_point/$1');
	$routes->add('view_safe_point/(:segment)/(:any)', 'FMWebService::view_safe_point/$1/$2', ['as' => 'view_safe_point']);

	$routes->add('set_place', 'FMWebService::set_place');
	$routes->add('change_place_name', 'FMWebService::change_place_name');

	$routes->add('set_user', 'FMWebService::set_user');

});

//앱
$routes->group('api', function($routes)
{
	$routes->add('login', 'Home::index');
	$routes->add('auth_check', 'Home::auth_check');
	$routes->add('place', 'Home::place');
	$routes->add('place_add', 'Home::place_add');
	$routes->add('place_edit', 'Home::place_edit');
	$routes->add('user', 'Home::user');
	$routes->add('user_add', 'Home::user_add');
	$routes->add('user_edit_level', 'Home::user_edit_level');
	$routes->add('user_edit_password', 'Home::user_edit_password');
	$routes->add('user_delete', 'Home::user_delete');
	$routes->add('team', 'Home::team');
	$routes->add('attendance', 'Home::attendance');
	$routes->add('attendance_add', 'Home::attendance_add');
	$routes->add('attendance_edit', 'Home::attendance_edit');
	$routes->add('attendance_edit_team', 'Home::attendance_edit_team');
	$routes->add('facility', 'Home::facility');
	$routes->add('facility_search_info', 'Home::facility_search_info');
	$routes->add('facility_search', 'Home::facility_search');
	$routes->add('facility_edit_state', 'Home::facility_edit_state');
	$routes->add('facility_edit_super_manager', 'Home::facility_edit_super_manager');
	$routes->add('facility_edit_purpose', 'Home::facility_edit_purpose');
	$routes->add('facility_edit_size', 'Home::facility_edit_size');
	$routes->add('facility_edit_expired_at', 'Home::facility_edit_expired_at');
	$routes->add('task_add', 'Home::task_add');
	$routes->add('taskplan', 'Home::taskplan');
	$routes->add('taskplan_team', 'Home::taskplan_team');
	$routes->add('taskplan_edit', 'Home::taskplan_edit');
	$routes->add('taskplan_delete', 'Home::taskplan_delete');
	$routes->add('super_manager', 'Home::super_manager');

	//테스트
	$routes->add('test', 'Home::test');
});

/*기존앱 호환
$routes->add('/', 'Home::index');
$routes->add('/places', 'Home::place');
$routes->add('/add_place', 'Home::place_add');
$routes->add('/edit_place', 'Home::place_edit');
$routes->add('/user_info', 'Home::user');
$routes->add('/add_user', 'Home::user_add');
$routes->add('/user_edit_level', 'Home::user_edit_level');
$routes->add('/teams', 'Home::team');
$routes->add('/attendance', 'Home::attendance');
$routes->add('/attendance_add', 'Home::attendance_add');
$routes->add('/attendance_edit', 'Home::attendance_edit');
$routes->add('/team_edit', 'Home::attendance_edit_team');
$routes->add('/facility', 'Home::facility');
$routes->add('/facility_search_info', 'Home::facility_search_info');
$routes->add('/facility_search', 'Home::facility_search');
$routes->add('/facility_edit_state', 'Home::facility_edit_state');
$routes->add('/facility_edit_super_manager', 'Home::facility_edit_super_manager');
$routes->add('/facility_info', 'Home::taskplan');
$routes->add('/facility_team_taskplan', 'Home::taskplan_team');
$routes->add('/facility_edit_taskplan', 'Home::taskplan_edit');
$routes->add('/super_manager_info', 'Home::super_manager');
//안쓰는거
$routes->add('/attendance_on', 'Home::attendance_on');
$routes->add('/attendance_off', 'Home::attendance_off');
$routes->add('/teammates', 'Home::teammates');
*/


/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php'))
{
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
