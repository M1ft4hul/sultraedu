<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */
$routes->get('/', 'Home::index');

// untuk sistem login
$routes->get('/login', 'Auth::index');
$routes->post('add_login', 'Auth::login');
$routes->get('logout', 'Auth::logout');


// Dashboard admin (admin pusat dan admin sekolah)
$routes->get('dashboard', 'Cdashboard::index');
$routes->get('praktik-baik', 'CpraktikBaik::index');

// Data Sekolah (Admin Dinas/Pusat)
$routes->get('sekolah', 'Csekolah::index');
$routes->post('sekolah/simpan', 'Csekolah::simpan');
$routes->post('sekolah/status/(:num)', 'Csekolah::status/$1');
$routes->post('sekolah/hapus/(:num)', 'Csekolah::hapus/$1');
