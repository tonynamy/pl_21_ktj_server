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
	$routes->add('parse_team_data', 'FMWebService::parse_team_data');
	$routes->add('add_team_result', 'FMWebService::add_team_result');

	$routes->add('add_facility', 'FMWebService::add_facility');
	$routes->add('parse_facility_data', 'FMWebService::parse_facility_data');
	$routes->add('add_facility_result', 'FMWebService::add_facility_result');

	$routes->add('view_attendance', 'FMWebService::view_attendance');
	$routes->add('view_attendance/(:num)', 'FMWebService::view_attendance/$1');
	$routes->add('view_attendance/(:num)/(:num)', 'FMWebService::view_attendance/$1/$2', 		['as' => 'view_attendance']);
	$routes->add('download_attendance', 'FMWebService::download_attendance');
	$routes->add('download_attendance/(:num)', 'FMWebService::download_attendance/$1');
	$routes->add('delete_team', 'FMWebService::delete_team');
	$routes->add('edit_teammate_info', 'FMWebService::edit_teammate_info');
	$routes->add('edit_attendance', 'FMWebService::edit_attendance');

	$routes->add('view_facility', 'FMWebService::view_facility');
	$routes->add('view_facility/(:segment)', 'FMWebService::view_facility/$1');
	$routes->add('download_facility', 'FMWebService::download_facility');
	$routes->add('view_facility_info/(:segment)', 'FMWebService::view_facility_info/$1');
	$routes->add('view_facility_info/(:segment)/(:segment)', 'FMWebService::view_facility_info/$1/$2');
	$routes->add('delete_facility', 'FMWebService::delete_facility');
	$routes->add('edit_facility_info', 'FMWebService::edit_facility_info');
	$routes->add('edit_taskplan', 'FMWebService::edit_taskplan');
	$routes->add('edit_task', 'FMWebService::edit_task');
	$routes->add('view_etc_task', 'FMWebService::view_etc_task');
	$routes->add('add_etc_task', 'FMWebService::add_etc_task');
	$routes->add('download_etc_task', 'FMWebService::download_etc_task');
	$routes->add('view_etc_task_info/(:num)/(:segment)', 'FMWebService::view_etc_task_info/$1/$2', ['as' => 'view_etc_task_info']);
	$routes->add('change_etc_task_team', 'FMWebService::change_etc_task_team');
	$routes->add('add_etc_taskplan', 'FMWebService::add_etc_taskplan');
	$routes->add('finish_etc_taskplan', 'FMWebService::finish_etc_taskplan');
	$routes->add('delete_etc_taskplan', 'FMWebService::delete_etc_taskplan');
	$routes->add('edit_etc_task', 'FMWebService::edit_etc_task');

	$routes->add('view_productivity', 'FMWebService::view_productivity');
	$routes->add('view_productivity/(:num)', 'FMWebService::view_productivity/$1',						['as' => 'view_productivity']);
	$routes->add('view_productivity_team', 'FMWebService::view_productivity_team');
	$routes->add('view_productivity_team/(:num)', 'FMWebService::view_productivity_team/$1');
	$routes->add('view_productivity_team/(:num)/(:num)', 'FMWebService::view_productivity_team/$1/$2',	['as' => 'view_productivity_team']);
	$routes->add('view_manday_team/(:num)/(:num)', 'FMWebService::view_manday_team/$1/$2', 		['as' => 'view_manday_team']);
	$routes->add('edit_etc_task_manday', 'FMWebService::edit_etc_task_manday');

	$routes->add('view_facility_max_rnum/(:segment)', 'FMWebService::view_facility_max_rnum/$1', ['as' => 'view_facility_max_rnum']);

	$routes->add('view_safe_point', 'FMWebService::view_safe_point');
	$routes->add('view_safe_point/(:num)', 'FMWebService::view_safe_point/$1',						['as' => 'view_safe_point']);
	$routes->add('add_safe_point', 'FMWebService::add_safe_point');
	$routes->add('edit_safe_point', 'FMWebService::edit_safe_point');

	$routes->add('view_safe_point_team', 'FMWebService::view_safe_point_team');
	$routes->add('view_safe_point_team/(:num)', 'FMWebService::view_safe_point_team/$1');
	$routes->add('view_safe_point_team/(:num)/(:num)', 'FMWebService::view_safe_point_team/$1/$2',	['as' => 'view_safe_point_team']);
	$routes->add('add_team_safe_point', 'FMWebService::add_team_safe_point');
	$routes->add('edit_team_safe_point', 'FMWebService::edit_team_safe_point');

	$routes->add('download_report', 'FMWebService::download_report');

	$routes->add('set_place', 'FMWebService::set_place');
	$routes->add('edit_place', 'FMWebService::edit_place');
	$routes->add('delete_place', 'FMWebService::delete_place');
	$routes->add('change_place_name', 'FMWebService::change_place_name');

	$routes->add('set_user', 'FMWebService::set_user');
	$routes->add('edit_user_info', 'FMWebService::edit_user_info');

});

//앱
$routes->group('api', function($routes)
{
	$routes->add('login', 'Home::index');
	$routes->add('auth_check', 'Home::auth_check');
	$routes->add('place', 'Home::place');
	$routes->add('place_hide', 'Home::place_hide');
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
	$routes->add('attendance_delete', 'Home::attendance_delete');
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
	$routes->add('taskplan_etc', 'Home::taskplan_etc');
	$routes->add('taskplan_etc_edit', 'Home::taskplan_etc_edit');
	$routes->add('taskplan_etc_delete', 'Home::taskplan_etc_delete');
	$routes->add('super_manager', 'Home::super_manager');
	$routes->add('productivity', 'Home::productivity');
	$routes->add('productivity_team', 'Home::productivity_team');
	$routes->add('safe_point', 'Home::safe_point');
	$routes->add('safe_point_team', 'Home::safe_point_team');
	$routes->add('dashboard', 'Home::dashboard');


	//테스트
	$routes->add('test', 'Home::test');
});


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
