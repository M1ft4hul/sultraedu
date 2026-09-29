<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\GuruModel;

class Cprofil extends BaseController
{
    public const LABEL_ROLE = [
        'admin_pusat'   => 'Admin Dinas',
        'admin_sekolah' => 'Admin Sekolah',
        'tim_juri'      => 'Tim Juri',
        'guru'          => 'Guru',
    ];

    private function isGuru(): bool
    {
        return session()->get('user_type') === 'guru';
    }

    private function model()
    {
        return $this->isGuru() ? new GuruModel() : new AdminModel();
    }

    // Ambil data akun yang sedang login
    private function akunSaya(): ?array
    {
        return $this->model()->find(session()->get('user_id'));
    }

    // =====================================================
    // HALAMAN PROFIL
    // =====================================================
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $akun = $this->akunSaya();
        if (! $akun) {
            return redirect()->to('logout');
        }

        $sekolah = null;
        if (! empty($akun['id_sekolah'])) {
            $sekolah = \Config\Database::connect()->table('sekolah')
                ->select('nama_sekolah, npsn, kabupaten_kota')
                ->where('id_sekolah', $akun['id_sekolah'])
                ->get()->getRowArray();
        }

        unset($akun['password']); // hash password tidak dikirim ke halaman

        return view('admin/profil', [
            'akun'      => $akun,
            'isGuru'    => $this->isGuru(),
            'nama'      => $this->isGuru() ? $akun['nama_guru'] : $akun['nama_admin'],
            'labelRole' => self::LABEL_ROLE[session()->get('role')] ?? session()->get('role'),
            'sekolah'   => $sekolah,
        ]);
    }

    // =====================================================
    // SIMPAN DATA DIRI
    // =====================================================
    public function simpan()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        if ($this->isGuru()) {
            $input = [
                'nama_guru' => trim((string) $this->request->getPost('nama')),
                'nip'       => trim((string) $this->request->getPost('nip')),
                'mapel'     => trim((string) $this->request->getPost('mapel')),
            ];
            $rules = [
                'nama_guru' => 'required|max_length[100]',
                'nip'       => 'permit_empty|max_length[30]',
                'mapel'     => 'permit_empty|max_length[100]',
            ];
            $pesan = ['nama_guru' => ['required' => 'Nama lengkap wajib diisi.']];
        } else {
            $input = [
                'nama_admin' => trim((string) $this->request->getPost('nama')),
                'email'      => trim((string) $this->request->getPost('email')),
            ];
            $rules = [
                'nama_admin' => 'required|max_length[100]',
                'email'      => 'permit_empty|valid_email|max_length[100]',
            ];
            $pesan = [
                'nama_admin' => ['required' => 'Nama lengkap wajib diisi.'],
                'email'      => ['valid_email' => 'Format email tidak valid.'],
            ];
        }

        if (! $this->validateData($input, $rules, $pesan)) {
            return redirect()->back()->withInput()->with('errorsProfil', $this->validator->getErrors());
        }

        foreach ($input as $k => $v) {
            if ($v === '' && $k !== 'nama_guru' && $k !== 'nama_admin') {
                $input[$k] = null;
            }
        }

        $this->model()->update(session()->get('user_id'), $input);

        // Perbarui nama di session supaya header langsung ikut berubah
        session()->set('nama', $input['nama_guru'] ?? $input['nama_admin']);

        return redirect()->to('profil')->with('sukses', 'Data diri berhasil diperbarui.');
    }

    // =====================================================
    // UBAH PASSWORD
    // =====================================================
    public function password()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $input = [
            'password_lama'       => (string) $this->request->getPost('password_lama'),
            'password_baru'       => (string) $this->request->getPost('password_baru'),
            'konfirmasi_password' => (string) $this->request->getPost('konfirmasi_password'),
        ];

        $valid = $this->validateData($input, [
            'password_lama'       => 'required',
            'password_baru'       => 'required|min_length[8]',
            'konfirmasi_password' => 'required|matches[password_baru]',
        ], [
            'password_lama'       => ['required' => 'Password lama wajib diisi.'],
            'password_baru'       => ['required' => 'Password baru wajib diisi.', 'min_length' => 'Password baru minimal 8 karakter.'],
            'konfirmasi_password' => ['required' => 'Konfirmasi password wajib diisi.', 'matches' => 'Konfirmasi password tidak sama dengan password baru.'],
        ]);

        if (! $valid) {
            return redirect()->to('profil')->with('errorsPassword', $this->validator->getErrors());
        }

        $akun = $this->model()->find(session()->get('user_id'));

        if (! $akun || ! password_verify($input['password_lama'], $akun['password'])) {
            return redirect()->to('profil')->with('errorsPassword', ['Password lama salah.']);
        }
        if (password_verify($input['password_baru'], $akun['password'])) {
            return redirect()->to('profil')->with('errorsPassword', ['Password baru tidak boleh sama dengan password lama.']);
        }

        // Di-hash otomatis oleh model (callback hashPassword)
        $this->model()->update(session()->get('user_id'), ['password' => $input['password_baru']]);

        return redirect()->to('profil')->with('sukses', 'Password berhasil diubah. Gunakan password baru saat login berikutnya.');
    }
}
