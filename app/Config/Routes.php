<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
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
$routes->setDefaultController('Index');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.

//웹서비스
$routes->group('fm', function($routes)
{
   $routes->add('', 'FMWebService::index');
   $routes->add('login', 'FMWebService::login');
   $routes->add('create_user', 'FMWebService::create_user');
   $routes->add('menu', 'FMWebService::menu');
   $routes->add('add_team', 'FMWebService::add_team');
   $routes->add('load_team_excel', 'FMWebService::load_team_excel');
   $routes->add('parse_team_data', 'FMWebService::parse_team_data');
   $routes->add('add_facility', 'FMWebService::add_facility');
   $routes->add('load_facility_excel', 'FMWebService::load_facility_excel');
   $routes->add('parse_facility_data', 'FMWebService::parse_facility_data');

   $routes->add('view_attendance', 'FMWebService::view_attendance');
   $routes->add('view_attendance/(:num)', 'FMWebService::view_attendance/$1', 				['as' => 'view_attendance_team']);
   $routes->add('view_attendance/(:num)/(:num)', 'FMWebService::view_attendance/$1/$2', 	['as' => 'view_attendance']);

   $routes->add('selet_team', 'FMWebService::selet_team');
   $routes->add('save_attendance_button', 'FMWebService::save_attendance_button');
});


// API
$routes->group('api', function($routes)
{
    $routes->add('', 'Home::index');
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
	$routes->add('facility_edit_expired_at', 'Home::facility_edit_expired_at');
	$routes->add('taskplan', 'Home::taskplan');
	$routes->add('taskplan_team', 'Home::taskplan_team');
	$routes->add('taskplan_edit', 'Home::taskplan_edit');
	$routes->add('taskplan_delete', 'Home::taskplan_delete');
	$routes->add('super_manager', 'Home::super_manager');
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
