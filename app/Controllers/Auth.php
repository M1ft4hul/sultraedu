<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\GuruModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('dashboard');
        }

        return view('login'); 
    }

    /**
     * Proses form login.
     */
    public function login()
    {
        $username = trim((string) $this->request->getPost('username'));
        $password = (string) $this->request->getPost('password');

        // 1. Pastikan form tidak kosong
        if ($username === '' || $password === '') {
            return redirect()->back()->withInput()
                ->with('error', 'Username dan kata sandi wajib diisi.');
        }

        $pesanGagal     = 'Username atau kata sandi salah.';
        $pesanNonaktif  = 'Akun Anda sedang dinonaktifkan. Silakan hubungi Admin Pusat.';

        // 2. Cek di tabel admin
        $admin = (new AdminModel())->findByUsername($username);

        if ($admin) {
            if (! password_verify($password, $admin['password'])) {
                return redirect()->back()->withInput()->with('error', $pesanGagal);
            }

            if ($admin['status'] !== 'aktif') {
                return redirect()->back()->withInput()->with('error', $pesanNonaktif);
            }

            $this->simpanSession([
                'user_id'    => $admin['id_admin'],
                'user_type'  => 'admin',
                'nama'       => $admin['nama_admin'],
                'username'   => $admin['username'],
                'role'       => $admin['role'], // admin_pusat / admin_sekolah / penanggung_jawab
                'id_sekolah' => $admin['id_sekolah'],
            ]);

            return redirect()->to('dashboard');
        }

        // 3. Kalau bukan admin, cek di tabel guru
        $guru = (new GuruModel())->findByUsername($username);

        if ($guru) {
            if (! password_verify($password, $guru['password'])) {
                return redirect()->back()->withInput()->with('error', $pesanGagal);
            }

            if ($guru['status'] !== 'aktif') {
                return redirect()->back()->withInput()->with('error', $pesanNonaktif);
            }

            $this->simpanSession([
                'user_id'    => $guru['id_guru'],
                'user_type'  => 'guru',
                'nama'       => $guru['nama_guru'],
                'username'   => $guru['username'], 
                'role'       => 'guru',
                'id_sekolah' => $guru['id_sekolah'],
            ]);

            return redirect()->to('dashboard');
        }

        return redirect()->back()->withInput()->with('error', $pesanGagal);
    }

    /**
     * Keluar dari sistem.
     */
    public function logout()
    {
        session()->destroy();

        return redirect()->to('login');
    }

    /**
     * Simpan data user ke session.
     */
    private function simpanSession(array $data): void
    {
        session()->regenerate();
        session()->set($data + ['logged_in' => true]);
    }
}