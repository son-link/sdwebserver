<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->set404Override('App\Controllers\Home::error404');
$routes->get('user/(:any)', 'Users::user/$1');
$routes->get('car/(:any)', 'Cars::index/$1');
$routes->get('track/(:any)', 'Tracks::index/$1');
$routes->get('race/(:num)', 'Races::index/$1');
$routes->get('users', 'Users::index');
$routes->get('login', 'Users::login');
$routes->post('webserver', 'Webserver::index');

$routes->group('dashboard', static function ($routes) {
	$routes->get('/', 'Dashboard::index', ['filter' => 'userSession']);
	$routes->get('user', 'Dashboard::user', ['filter' => 'userSession']);
	$routes->get('logout', 'Dashboard::logout');
	$routes->post('login', 'Dashboard::login');
	$routes->post('update_user', 'Dashboard::updateUser', ['filter' => 'userSession']);
	$routes->post('change_passwd', 'Dashboard::changePasswd', ['filter' => 'userSession']);
});

$routes->group('register', static function ($routes) {
	$routes->get('/', 'Register::index');
	$routes->post('newuser', 'Register::newuser');
	$routes->get('new_captcha', 'Register::newCaptcha');
	$routes->get('ok', 'Register::ok');
});

$routes->group('api', static function ($routes) {
	$routes->get('bests_laps', 'Api::getBestsLaps');
	$routes->get('most_active_users', 'Api::getMostActiveUsers');
	$routes->get('most_used_tracks', 'Api::getMostUsedTracks');
	$routes->get('most_used_cars', 'Api::getMostUsedCars');
});

$routes->group('test', static function ($routes) {
	$routes->get('tables', 'Test::tables');
});