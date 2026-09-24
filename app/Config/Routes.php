<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// untuk sistem login
$routes->get('/login', 'Auth::index');
$routes->post('add_login', 'Auth::login');
$routes->get('logout', 'Auth::logout');


// Dashboard admin (nanti dikunci dengan filter di langkah 5)
$routes->get('dashboard', 'Cdashboard::index');
