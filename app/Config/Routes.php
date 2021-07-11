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
$routes->get('blog', 'Blog::index');
$routes->post('blog/upload_file', 'Blog::upload_file');
$routes->post('blog/update_blog', 'Blog::update_blog');
$routes->get('blog/data', 'Blog::data');
$routes->group('auth', ['namespace' => '\App\Controllers\Auth'], function ($routes) {
	$routes->group('login', function ($routes) {
		$routes->post('', 'Login::index');
		$routes->post('with_social', 'Login::loginWithSocial');
	});
	$routes->group('lupa_password', function ($routes) {
		$routes->post('', 'LupaPassword::index');
		$routes->post('cek_kode', 'LupaPassword::cekKode');
		$routes->post('ubah_password', 'LupaPassword::ubahPassword');
	});
	$routes->post('register', 'Register::index');
});
$routes->group('user', ['filter' => 'auth', 'namespace' => '\App\Controllers\User'], function ($routes) {
	$routes->group('profile', function ($routes) {
		$routes->get('', 'Profile::index');
		$routes->get('cek_token', function () {
			$response = service('response');
			$response->setStatusCode(200);
			$response->setBody(json_encode(["status" => 1, "message" => "token_aktif", "data" => []]));
			$response->setHeader('Content-type', 'application/json');
			return $response;
		});
		$routes->put('', 'Profile::ubah');
		$routes->put('ubah_password', 'Profile::ubahPassword');
		$routes->post('fcm', 'Fcm::index');
	});
	$routes->group('notification', function ($routes) {
		$routes->get('', 'Notifications::index');
		$routes->get('baca_semua', 'Notifications::baca_semua');
		$routes->get('baca/(:num)', 'Notifications::baca/$1');
	});
});
$routes->group('setting', ['filter' => 'auth:admin'], function ($routes) {
	$routes->get('', 'Settings::index');
	$routes->put('', 'Settings::update_setting');
});
$routes->group('booking', ['filter' => 'auth'], function ($routes) {
	$routes->get('', 'Booking::index');
	$routes->get('detail/(:any)', 'Booking::detail/$1');
	$routes->post('', 'Booking::create');
	$routes->post('batalkan/(:any)', 'Booking::batalkan/$1');
	$routes->get('hari_ini', 'Booking::bookingHariIni');
	$routes->post('cek_ketersediaan', 'Booking::CekKetersediaan');
	// $routes->put('', 'Booking::update_setting', ['filter' => 'auth:admin']);
});
$routes->group('admin', ['filter' => 'auth:admin', 'namespace' => '\App\Controllers\Admin'], function ($routes) {
	$routes->group('booking', function ($routes) {
		$routes->get('', 'Booking::index');
		$routes->get('detail/(:any)', 'Booking::detail/$1');
		$routes->post('konfirmasi/(:any)', 'Booking::konfirmasi/$1');
		$routes->post('selesai/(:any)', 'Booking::selesai/$1');
		$routes->post('laporan', 'Booking::laporan');
		// $routes->put('', 'Booking::update_setting', ['filter' => 'auth:admin']);
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
