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
$routes->post('praktik-baik/verifikasi/(:num)', 'CpraktikBaik::verifikasi/$1');
// Bank Inovasi
$routes->get('bank-inovasi', 'CbankInovasi::index');
$routes->post('bank-inovasi/verifikasi/(:num)', 'CbankInovasi::verifikasi/$1');

// Data Sekolah (Admin Dinas/Pusat)
$routes->get('sekolah', 'Csekolah::index');
$routes->post('sekolah/simpan', 'Csekolah::simpan');
$routes->post('sekolah/status/(:num)', 'Csekolah::status/$1');
$routes->post('sekolah/hapus/(:num)', 'Csekolah::hapus/$1');

// Akun Admin Sekolah (Admin Dinas/Pusat)
$routes->get('pengguna', 'Cpengguna::index');
$routes->post('pengguna/simpan', 'Cpengguna::simpan');
$routes->post('pengguna/status/(:num)', 'Cpengguna::status/$1');
$routes->post('pengguna/hapus/(:num)', 'Cpengguna::hapus/$1');
$routes->get('pengguna/export', 'Cpengguna::export');
$routes->post('pengguna/buat-massal', 'Cpengguna::buatMassal');
$routes->post('pengguna/reset-massal', 'Cpengguna::resetMassal');

// Kompetisi (Admin Dinas)
$routes->get('kompetisi', 'Ckompetisi::index');
$routes->post('kompetisi/simpan', 'Ckompetisi::simpan');
$routes->post('kompetisi/status/(:num)', 'Ckompetisi::status/$1');
$routes->post('kompetisi/hapus/(:num)', 'Ckompetisi::hapus/$1');

// Apresiasi (Admin Dinas)
$routes->get('apresiasi', 'Capresiasi::index');
$routes->post('apresiasi/umumkan/(:num)', 'Capresiasi::umumkan/$1');
$routes->post('apresiasi/piagam/(:num)', 'Capresiasi::piagam/$1');

// Monev (Admin Dinas)
$routes->get('monev', 'Cmonev::index');
$routes->post('monev/simpan', 'Cmonev::simpan');
$routes->post('monev/hapus/(:num)', 'Cmonev::hapus/$1');

// SUARA
$routes->get('suara', 'Csuara::index');
$routes->get('suara/export', 'Csuara::export');

// Profil (semua role)
$routes->get('profil', 'Cprofil::index');
$routes->post('profil/simpan', 'Cprofil::simpan');
$routes->post('profil/password', 'Cprofil::password');

$routes->post('profil/tim', 'Cprofil::tambahAdmin');
$routes->post('profil/tim/status/(:num)', 'Cprofil::statusAdmin/$1');
$routes->post('suara/kirim', 'Csuara::kirim');

// Data Guru (Admin Sekolah)
$routes->get('guru', 'Cguru::index');
$routes->post('guru/simpan', 'Cguru::simpan');
$routes->post('guru/akun/(:num)', 'Cguru::akun/$1');
$routes->post('guru/status/(:num)', 'Cguru::status/$1');
$routes->post('guru/hapus/(:num)', 'Cguru::hapus/$1');
