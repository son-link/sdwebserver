<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('user/(:any)', 'Users::user/$1');
$routes->get('car/(:any)', 'Cars::index/$1');
$routes->get('track/(:any)', 'Tracks::index/$1');
$routes->get('race/(:num)', 'Races::index/$1');
$routes->get('users', 'Users::index');