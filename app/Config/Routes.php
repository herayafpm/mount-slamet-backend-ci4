<?php

namespace Config;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
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
$routes->setAutoRoute(false);

/*
 * --------------------------------------------------------------------
 * Route Definitions
 * --------------------------------------------------------------------
 */

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
$routes->get('/', 'Home::index');
$routes->group('auth', ['namespace' => '\App\Controllers\Auth'], function ($routes) {
	$routes->post('', 'Login::index');
	$routes->get('profile', 'Profile::index', ['filter' => 'auth']);
	$routes->put('profile', 'Profile::ubah', ['filter' => 'auth']);
	$routes->put('ubah_password', 'UbahPassword::index', ['filter' => 'auth']);
	$routes->put('ubah_fcm_token', 'UbahFcmToken::index', ['filter' => 'auth']);
	$routes->put('ubah_auth_key', 'UbahAuthKey::index', ['filter' => 'auth']);
	$routes->group('lupa_password', function ($routes) {
		$routes->post('', 'LupaPassword::index');
		$routes->post('cek_kode', 'LupaPassword::cek_kode');
		$routes->post('ubah_password', 'LupaPassword::ubah_password');
	});
	$routes->group('notifications', ['namespace' => '\App\Controllers\Auth', 'filter' => 'auth'], function ($routes) {
		$routes->get('', 'Notifications::index');
		$routes->get('baca_semua', 'Notifications::baca_semua');
		$routes->get('baca/(:num)', 'Notifications::baca/$1');
	});
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
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
	require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
