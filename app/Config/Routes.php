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
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->add('/', 'Home::index');
$routes->add('/check', 'Home::check');
$routes->add('/attendance_on', 'Home::attendance_on');
$routes->add('/attendance_off', 'Home::attendance_off');
$routes->add('/attendance', 'Home::attendance');
$routes->add('/attendance_add', 'Home::attendance_add');
$routes->add('/attendance_edit', 'Home::attendance_edit');
$routes->add('/teams', 'Home::teams');
$routes->add('/team_edit', 'Home::team_edit');
$routes->add('/teammates', 'Home::teammates');
$routes->add('/add_user', 'Home::add_user');
$routes->add('/places', 'Home::places');
$routes->add('/facility_info', 'Home::facility_info');
$routes->add('/facility_search', 'Home::facility_search');
$routes->add('/facility', 'Home::facility');
$routes->add('/facility_edit_state', 'Home::facility_edit_state');
$routes->add('/facility_edit_expired_at', 'Home::facility_edit_expired_at');
$routes->add('/facility_edit_super_manager', 'Home::facility_edit_super_manager');
$routes->add('/facility_edit_purpose', 'Home::facility_edit_purpose');
$routes->add('/facility_edit_taskplan', 'Home::facility_edit_taskplan');

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
