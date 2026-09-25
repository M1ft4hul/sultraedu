<?php

namespace App\Controllers;

class Cdashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        return view('admin/componen_be/layout');
    }
}
