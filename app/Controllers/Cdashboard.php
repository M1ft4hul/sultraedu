<?php

namespace App\Controllers;

class Cdashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        return 'Login berhasil. Halo, ' . esc(session()->get('nama'))
            . ' (role: ' . esc(session()->get('role')) . '). '
            . '<a href="' . base_url('logout') . '">Keluar</a>';
    }
}
