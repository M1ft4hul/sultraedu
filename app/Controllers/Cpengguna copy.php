<?php

namespace App\Controllers;

use App\Models\AdminModel;
use App\Models\SekolahModel;

class Cpengguna extends BaseController
{
    protected AdminModel $admin;

    public function __construct()
    {
        $this->admin = new AdminModel();
    }

    // Hanya Admin Dinas yang boleh mengelola akun
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // Ambil akun admin sekolah berdasarkan id (akun role lain tidak bisa disentuh)
    private function cariAdminSekolah($id): ?array
    {
        return $this->admin->where('id_admin', $id)
            ->where('role', 'admin_sekolah')
            ->first();
    }

    // Ambil nilai filter dari URL
    private function ambilFilter(): array
    {
        return [
            'q'       => trim((string) $this->request->getGet('q')),
            'sekolah' => (string) $this->request->getGet('sekolah'),
            'status'  => (string) $this->request->getGet('status'),
        ];
    }

    // Query daftar akun admin sekolah (dipakai di tabel dan export)
    private function queryPengguna(array $filter): array
    {
        // Kolom dipilih satu per satu supaya hash password tidak ikut terambil
        $builder = $this->admin
            ->select('admin.id_admin, admin.nama_admin, admin.username, admin.id_sekolah, admin.email, admin.status, admin.created_at, s.nama_sekolah, s.npsn, s.kabupaten_kota, s.status AS status_sekolah')
            ->join('sekolah s', 's.id_sekolah = admin.id_sekolah', 'left')
            ->where('admin.role', 'admin_sekolah');

        if ($filter['q'] !== '') {
            $builder->groupStart()
                ->like('admin.nama_admin', $filter['q'])
                ->orLike('admin.username', $filter['q'])
                ->orLike('s.nama_sekolah', $filter['q'])
                ->groupEnd();
        }
        if ($filter['sekolah'] !== '') {
            $builder->where('admin.id_sekolah', $filter['sekolah']);
        }
        if ($filter['status'] !== '') {
            $builder->where('admin.status', $filter['status']);
        }

        return $builder->orderBy('s.nama_sekolah', 'ASC')->findAll();
    }

    // =====================================================
    // DAFTAR AKUN ADMIN SEKOLAH
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $filter = $this->ambilFilter();

        $data['pengguna'] = $this->queryPengguna($filter);
        $data['filter']   = $filter;

        // Daftar sekolah untuk dropdown form & filter
        $data['daftarSekolah'] = (new SekolahModel())
            ->select('id_sekolah, npsn, nama_sekolah, kabupaten_kota, status')
            ->orderBy('nama_sekolah', 'ASC')
            ->findAll();

        // Ringkasan
        $db = \Config\Database::connect();
        $data['ringkas'] = [
            'total' => $this->admin->where('role', 'admin_sekolah')->countAllResults(),
            'aktif' => $this->admin->where('role', 'admin_sekolah')->where('status', 'aktif')->countAllResults(),
            // Sekolah aktif yang belum punya akun admin sekolah sama sekali
            'tanpaAdmin' => $db->table('sekolah')
                ->where('status', 'aktif')
                ->whereNotIn('id_sekolah', function ($sub) {
                    return $sub->select('id_sekolah')->from('admin')
                        ->where('role', 'admin_sekolah')
                        ->where('id_sekolah IS NOT NULL');
                })
                ->countAllResults(),
        ];

        return view('admin/dinas/pengguna', $data);
    }

    // =====================================================
    // SIMPAN (tambah baru atau edit)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $input = $this->request->getPost([
            'id_admin',
            'nama_admin',
            'username',
            'password',
            'id_sekolah',
            'email',
            'status',
        ]);
        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, $input);
        $input['username'] = strtolower($input['username'] ?? '');

        $edit = ! empty($input['id_admin']);

        // Kalau edit, pastikan akun yang diedit memang admin sekolah
        if ($edit && ! $this->cariAdminSekolah($input['id_admin'])) {
            return redirect()->to('pengguna')->with('gagal', 'Akun tidak ditemukan.');
        }

        $rules = [
            'id_admin'   => 'permit_empty|is_natural_no_zero',
            'nama_admin' => 'required|max_length[100]',
            'username'   => 'required|min_length[4]|max_length[50]|alpha_dash|is_unique[admin.username,id_admin,{id_admin}]',
            'password'   => $edit ? 'permit_empty|min_length[8]' : 'required|min_length[8]',
            'id_sekolah' => 'required|is_not_unique[sekolah.id_sekolah]',
            'email'      => 'permit_empty|valid_email|max_length[100]',
            'status'     => 'required|in_list[aktif,nonaktif]',
        ];

        $pesan = [
            'nama_admin' => ['required' => 'Nama lengkap wajib diisi.'],
            'username'   => [
                'required'   => 'Username wajib diisi.',
                'min_length' => 'Username minimal 4 karakter.',
                'alpha_dash' => 'Username hanya boleh huruf, angka, garis bawah (_), dan tanda hubung (-).',
                'is_unique'  => 'Username ini sudah dipakai akun lain.',
            ],
            'password' => [
                'required'   => 'Password wajib diisi untuk akun baru.',
                'min_length' => 'Password minimal 8 karakter.',
            ],
            'id_sekolah' => [
                'required'       => 'Sekolah wajib dipilih.',
                'is_not_unique'  => 'Sekolah yang dipilih tidak ditemukan.',
            ],
            'email' => ['valid_email' => 'Format email tidak valid.'],
        ];

        if (! $this->validateData($input, $rules, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Username juga tidak boleh sama dengan username guru
        $dipakaiGuru = \Config\Database::connect()->table('guru')
            ->where('username', $input['username'])
            ->countAllResults();

        if ($dipakaiGuru > 0) {
            return redirect()->back()->withInput()
                ->with('errors', ['username' => 'Username ini sudah dipakai oleh akun guru.']);
        }

        $simpan = [
            'nama_admin' => $input['nama_admin'],
            'username'   => $input['username'],
            'id_sekolah' => $input['id_sekolah'],
            'email'      => $input['email'] ?: null,
            'status'     => $input['status'],
            'role'       => 'admin_sekolah',
        ];

        // Password hanya disimpan kalau diisi (di-hash otomatis oleh AdminModel)
        if (! empty($input['password'])) {
            $simpan['password'] = $input['password'];
        }

        if ($edit) {
            $this->admin->update($input['id_admin'], $simpan);
            $pesanSukses = 'Akun ' . esc($input['nama_admin']) . ' berhasil diperbarui.';
        } else {
            $this->admin->insert($simpan);
            $pesanSukses = 'Akun admin sekolah untuk ' . esc($input['nama_admin']) . ' berhasil dibuat.';
        }

        $redirect = redirect()->to('pengguna')->with('sukses', $pesanSukses);

        // Kalau password dibuat/direset, tampilkan SEKALI di halaman berikutnya
        if (! empty($input['password'])) {
            $sekolah = (new SekolahModel())->find($input['id_sekolah']);

            $redirect->with('akunBaru', [
                'jenis'    => $edit ? 'reset' : 'baru',
                'nama'     => $input['nama_admin'],
                'sekolah'  => $sekolah['nama_sekolah'] ?? '-',
                'username' => $input['username'],
                'password' => $input['password'],
                'url'      => site_url('login'),
            ]);
        }

        return $redirect;
    }

    // =====================================================
    // UNDUH REKAP AKUN (CSV, bisa dibuka di Excel)
    // =====================================================
    public function export()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $data = $this->queryPengguna($this->ambilFilter());

        $file = fopen('php://temp', 'r+');

        // BOM supaya huruf dan simbol terbaca benar di Excel
        fwrite($file, "\xEF\xBB\xBF");

        // Pemisah titik koma (;) agar langsung terbagi per kolom di Excel berbahasa Indonesia
        fputcsv($file, ['No', 'Nama Lengkap', 'Username', 'Sekolah', 'NPSN', 'Kabupaten/Kota', 'Email', 'Status Akun', 'Tanggal Dibuat'], ';');

        foreach ($data as $i => $p) {
            fputcsv($file, [
                $i + 1,
                $p['nama_admin'],
                $p['username'],
                $p['nama_sekolah'] ?? '-',
                // Tanda kutip tunggal di depan agar NPSN tidak diubah Excel menjadi angka
                $p['npsn'] ? "'" . $p['npsn'] : '-',
                $p['kabupaten_kota'] ?? '-',
                $p['email'] ?: '-',
                ucfirst($p['status']),
                date('d/m/Y', strtotime($p['created_at'])),
            ], ';');
        }

        rewind($file);
        $isi = stream_get_contents($file);
        fclose($file);

        $namaFile = 'rekap-akun-admin-sekolah-' . date('Ymd-His') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $namaFile . '"')
            ->setBody($isi);
    }

    // =====================================================
    // AKTIFKAN / NONAKTIFKAN
    // =====================================================
    public function status($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $akun = $this->cariAdminSekolah($id);
        if (! $akun) {
            return redirect()->to('pengguna')->with('gagal', 'Akun tidak ditemukan.');
        }

        $statusBaru = $akun['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->admin->update($id, ['status' => $statusBaru]);

        return redirect()->to('pengguna')
            ->with('sukses', 'Akun ' . esc($akun['nama_admin']) . ' sekarang berstatus ' . $statusBaru . '.');
    }

    // =====================================================
    // HAPUS (hanya jika belum pernah memverifikasi apa pun)
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $akun = $this->cariAdminSekolah($id);
        if (! $akun) {
            return redirect()->to('pengguna')->with('gagal', 'Akun tidak ditemukan.');
        }

        $pernahVerifikasi = \Config\Database::connect()->table('praktik_baik')
            ->where('id_verifikator_sekolah', $id)
            ->countAllResults();

        if ($pernahVerifikasi > 0) {
            return redirect()->to('pengguna')->with(
                'gagal',
                'Akun ' . esc($akun['nama_admin']) . ' tidak bisa dihapus karena sudah pernah memverifikasi praktik baik. Nonaktifkan saja.'
            );
        }

        $this->admin->delete($id);

        return redirect()->to('pengguna')->with('sukses', 'Akun ' . esc($akun['nama_admin']) . ' berhasil dihapus.');
    }
}
