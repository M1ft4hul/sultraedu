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

    // Role yang boleh menambah rekan setim
    private function bolehKelolaTim(): bool
    {
        return session()->get('logged_in')
            && in_array(session()->get('role'), ['admin_pusat', 'admin_sekolah'], true);
    }

    // Query akun setim: role sama, dan untuk Admin Sekolah juga sekolah yang sama
    private function queryTim()
    {
        $model = (new AdminModel())->where('role', session()->get('role'));
        if (session()->get('role') === 'admin_sekolah') {
            $model->where('id_sekolah', session()->get('id_sekolah'));
        }

        return $model;
    }

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

        // Admin Dinas & Admin Sekolah: daftar rekan setim
        $timDinas = null;
        if ($this->bolehKelolaTim()) {
            $timDinas = $this->queryTim()
                ->select('id_admin, nama_admin, username, email, status, created_at')
                ->orderBy('created_at', 'ASC')
                ->findAll();
        }

        return view('admin/profil', [
            'timDinas'  => $timDinas,
            'labelTim'  => session()->get('role') === 'admin_sekolah' ? 'Admin Sekolah' : 'Admin Dinas',
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

    // =====================================================
    // TIM ADMIN DINAS: tambah akun
    // =====================================================
    public function tambahAdmin()
    {
        if (! $this->bolehKelolaTim()) {
            return redirect()->to('dashboard');
        }

        $input = [
            'nama_admin' => trim((string) $this->request->getPost('nama_admin')),
            'username'   => strtolower(trim((string) $this->request->getPost('username'))),
            'email'      => trim((string) $this->request->getPost('email')),
            'password'   => (string) $this->request->getPost('password'),
        ];

        $valid = $this->validateData($input, [
            'nama_admin' => 'required|max_length[100]',
            'username'   => 'required|min_length[4]|max_length[50]|alpha_dash|is_unique[admin.username]',
            'email'      => 'permit_empty|valid_email|max_length[100]',
            'password'   => 'required|min_length[8]',
        ], [
            'nama_admin' => ['required' => 'Nama lengkap wajib diisi.'],
            'username'   => [
                'required'   => 'Username wajib diisi.',
                'min_length' => 'Username minimal 4 karakter.',
                'alpha_dash' => 'Username hanya boleh huruf, angka, garis bawah (_), dan tanda hubung (-).',
                'is_unique'  => 'Username ini sudah dipakai akun lain.',
            ],
            'email'    => ['valid_email' => 'Format email tidak valid.'],
            'password' => ['required' => 'Password wajib diisi.', 'min_length' => 'Password minimal 8 karakter.'],
        ]);

        if (! $valid) {
            return redirect()->to(site_url('profil') . '#tim')->withInput()->with('errorsTim', $this->validator->getErrors());
        }

        // Username juga tidak boleh sama dengan username guru
        $dipakaiGuru = \Config\Database::connect()->table('guru')->where('username', $input['username'])->countAllResults();
        if ($dipakaiGuru) {
            return redirect()->to(site_url('profil') . '#tim')->withInput()
                ->with('errorsTim', ['Username ini sudah dipakai oleh akun guru.']);
        }

        // Password di-hash otomatis oleh AdminModel
        // Akun baru ikut role & sekolah pembuatnya
        $role    = session()->get('role');
        $sekolah = $role === 'admin_sekolah' ? session()->get('id_sekolah') : null;
        $label   = $role === 'admin_sekolah' ? 'Admin Sekolah' : 'Admin Dinas';

        (new AdminModel())->insert([
            'nama_admin' => $input['nama_admin'],
            'username'   => $input['username'],
            'email'      => $input['email'] ?: null,
            'password'   => $input['password'],
            'role'       => $role,
            'id_sekolah' => $sekolah,
            'status'     => 'aktif',
        ]);

        return redirect()->to(site_url('profil') . '#tim')
            ->with('sukses', 'Akun ' . $label . ' untuk ' . esc($input['nama_admin']) . ' berhasil dibuat.')
            ->with('akunBaru', [
                'nama'     => $input['nama_admin'],
                'username' => $input['username'],
                'password' => $input['password'],
                'url'      => site_url('login'),
            ]);
    }

    // =====================================================
    // TIM ADMIN DINAS: aktifkan / nonaktifkan
    // =====================================================
    public function statusAdmin($id)
    {
        if (! $this->bolehKelolaTim()) {
            return redirect()->to('dashboard');
        }

        $kembali = site_url('profil') . '#tim';

        if ((int) $id === (int) session()->get('user_id')) {
            return redirect()->to($kembali)->with('gagalTim', 'Anda tidak bisa menonaktifkan akun Anda sendiri.');
        }

        // Hanya akun setim (role sama, dan sekolah sama untuk Admin Sekolah)
        $akun = $this->queryTim()->where('id_admin', $id)->first();
        if (! $akun) {
            return redirect()->to($kembali)->with('gagalTim', 'Akun tidak ditemukan.');
        }

        $statusBaru = $akun['status'] === 'aktif' ? 'nonaktif' : 'aktif';

        // Pastikan selalu tersisa minimal satu akun aktif di tim
        if ($statusBaru === 'nonaktif') {
            $aktif = $this->queryTim()->where('status', 'aktif')->countAllResults();
            if ($aktif <= 1) {
                $label = session()->get('role') === 'admin_sekolah' ? 'Admin Sekolah di sekolah ini' : 'Admin Dinas';

                return redirect()->to($kembali)->with('gagalTim', 'Minimal harus ada satu ' . $label . ' yang aktif.');
            }
        }

        (new AdminModel())->update($id, ['status' => $statusBaru]);

        return redirect()->to($kembali)->with('sukses', 'Akun ' . esc($akun['nama_admin']) . ' sekarang ' . $statusBaru . '.');
    }
}
