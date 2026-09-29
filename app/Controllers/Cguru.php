<?php

namespace App\Controllers;

use App\Models\GuruModel;

class Cguru extends BaseController
{
    protected GuruModel $guru;

    public function __construct()
    {
        $this->guru = new GuruModel();
    }

    // Hanya Admin Sekolah, dan hanya untuk guru di sekolahnya sendiri
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_sekolah';
    }

    private function idSekolah(): int
    {
        return (int) session()->get('id_sekolah');
    }

    private function cariGuru($id): ?array
    {
        return $this->guru->where('id_guru', $id)->where('id_sekolah', $this->idSekolah())->first();
    }

    // Username harus unik di tabel admin dan guru sekaligus
    private function usernameTersedia(string $username, ?int $kecualiGuru = null): bool
    {
        $db = \Config\Database::connect();

        if ($db->table('admin')->where('username', $username)->countAllResults() > 0) {
            return false;
        }

        $q = $db->table('guru')->where('username', $username);
        if ($kecualiGuru) {
            $q->where('id_guru !=', $kecualiGuru);
        }

        return $q->countAllResults() === 0;
    }

    // Validasi username & password akun guru, kembalikan daftar error
    private function cekAkun(string $username, string $password, bool $wajibUsername, ?int $idGuru = null): array
    {
        $error = [];

        if ($wajibUsername) {
            if (strlen($username) < 4 || strlen($username) > 50) {
                $error[] = 'Username harus 4 sampai 50 karakter.';
            } elseif (! preg_match('/^[a-z0-9_.-]+$/', $username)) {
                $error[] = 'Username hanya boleh huruf kecil, angka, titik, garis bawah, dan tanda hubung.';
            } elseif (! $this->usernameTersedia($username, $idGuru)) {
                $error[] = 'Username "' . $username . '" sudah dipakai akun lain.';
            }
        }

        if (strlen($password) < 8) {
            $error[] = 'Password minimal 8 karakter.';
        }

        return $error;
    }

    // =====================================================
    // DAFTAR GURU
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $filter = [
            'q'      => trim((string) $this->request->getGet('q')),
            'akun'   => (string) $this->request->getGet('akun'),
            'status' => (string) $this->request->getGet('status'),
        ];

        $builder = $this->guru
            ->select('id_guru, nip, nama_guru, username, mapel, status, created_at')
            ->where('id_sekolah', $this->idSekolah());

        if ($filter['q'] !== '') {
            $builder->groupStart()
                ->like('nama_guru', $filter['q'])
                ->orLike('nip', $filter['q'])
                ->orLike('mapel', $filter['q'])
                ->groupEnd();
        }
        if ($filter['akun'] === 'ada') {
            $builder->where('username IS NOT NULL');
        } elseif ($filter['akun'] === 'belum') {
            $builder->where('username IS NULL');
        }
        if (in_array($filter['status'], ['aktif', 'nonaktif'], true)) {
            $builder->where('status', $filter['status']);
        }

        $data['guru']   = $builder->orderBy('nama_guru', 'ASC')->findAll();
        $data['filter'] = $filter;

        $semua = $this->guru->select('username, status')->where('id_sekolah', $this->idSekolah())->findAll();
        $data['ringkas'] = [
            'total'     => count($semua),
            'aktif'     => count(array_filter($semua, fn($g) => $g['status'] === 'aktif')),
            'punyaAkun' => count(array_filter($semua, fn($g) => $g['username'] !== null)),
        ];
        $data['ringkas']['belumAkun'] = $data['ringkas']['total'] - $data['ringkas']['punyaAkun'];

        return view('admin/sekolah/guru', $data);
    }

    // =====================================================
    // SIMPAN DATA GURU (tambah / edit), opsional sekalian buat akun
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $id    = (int) $this->request->getPost('id_guru');
        $input = [
            'nama_guru' => trim((string) $this->request->getPost('nama_guru')),
            'nip'       => trim((string) $this->request->getPost('nip')),
            'mapel'     => trim((string) $this->request->getPost('mapel')),
            'status'    => (string) $this->request->getPost('status'),
        ];

        $error = [];
        if ($input['nama_guru'] === '' || mb_strlen($input['nama_guru']) > 100) {
            $error[] = 'Nama guru wajib diisi (maksimal 100 karakter).';
        }
        if ($input['nip'] !== '' && ! preg_match('/^[0-9]{8,30}$/', $input['nip'])) {
            $error[] = 'NIP hanya boleh berisi angka (8 sampai 30 digit).';
        }
        if (! in_array($input['status'], ['aktif', 'nonaktif'], true)) {
            $input['status'] = 'aktif';
        }

        // NIP tidak boleh kembar (kalau diisi)
        if ($input['nip'] !== '') {
            $q = $this->guru->where('nip', $input['nip']);
            if ($id) {
                $q->where('id_guru !=', $id);
            }
            if ($q->countAllResults() > 0) {
                $error[] = 'NIP ' . $input['nip'] . ' sudah terdaftar.';
            }
        }

        // Edit: pastikan guru milik sekolah ini
        if ($id && ! $this->cariGuru($id)) {
            return redirect()->to('guru')->with('gagal', 'Data guru tidak ditemukan.');
        }

        // Tambah baru + sekalian buat akun
        $buatAkun = ! $id && $this->request->getPost('buat_akun');
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');

        if ($buatAkun) {
            $error = array_merge($error, $this->cekAkun($username, $password, true));
        }

        if ($error) {
            return redirect()->back()->withInput()->with('errors', $error);
        }

        $input['nip']   = $input['nip'] ?: null;
        $input['mapel'] = $input['mapel'] ?: null;

        if ($id) {
            $this->guru->update($id, $input);

            return redirect()->to('guru')->with('sukses', 'Data ' . esc($input['nama_guru']) . ' berhasil diperbarui.');
        }

        $input['id_sekolah'] = $this->idSekolah();
        if ($buatAkun) {
            $input['username'] = $username;
            $input['password'] = $password; // di-hash otomatis oleh GuruModel
        }
        $this->guru->insert($input);

        $redirect = redirect()->to('guru')->with('sukses', 'Guru ' . esc($input['nama_guru']) . ' berhasil ditambahkan.');

        if ($buatAkun) {
            $redirect->with('akunBaru', ['jenis' => 'baru', 'nama' => $input['nama_guru'], 'username' => $username, 'password' => $password]);
        }

        return $redirect;
    }

    // =====================================================
    // BUAT AKUN / RESET PASSWORD
    // =====================================================
    public function akun($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $guru = $this->cariGuru($id);
        if (! $guru) {
            return redirect()->to('guru')->with('gagal', 'Data guru tidak ditemukan.');
        }

        $punyaAkun = $guru['username'] !== null;
        $username  = $punyaAkun ? $guru['username'] : strtolower(trim((string) $this->request->getPost('username')));
        $password  = (string) $this->request->getPost('password');

        $error = $this->cekAkun($username, $password, ! $punyaAkun, (int) $id);
        if ($error) {
            return redirect()->to('guru')->with('gagal', implode(' ', $error));
        }

        $this->guru->update($id, ['username' => $username, 'password' => $password]);

        return redirect()->to('guru')
            ->with('sukses', $punyaAkun ? 'Password ' . esc($guru['nama_guru']) . ' berhasil direset.' : 'Akun untuk ' . esc($guru['nama_guru']) . ' berhasil dibuat.')
            ->with('akunBaru', [
                'jenis'    => $punyaAkun ? 'reset' : 'baru',
                'nama'     => $guru['nama_guru'],
                'username' => $username,
                'password' => $password,
            ]);
    }

    // =====================================================
    // AKTIFKAN / NONAKTIFKAN
    // =====================================================
    public function status($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $guru = $this->cariGuru($id);
        if (! $guru) {
            return redirect()->to('guru')->with('gagal', 'Data guru tidak ditemukan.');
        }

        $baru = $guru['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->guru->update($id, ['status' => $baru]);

        return redirect()->to('guru')->with('sukses', esc($guru['nama_guru']) . ' sekarang berstatus ' . $baru . '.');
    }

    // =====================================================
    // HAPUS (hanya kalau guru belum punya karya apa pun)
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $guru = $this->cariGuru($id);
        if (! $guru) {
            return redirect()->to('guru')->with('gagal', 'Data guru tidak ditemukan.');
        }

        $db     = \Config\Database::connect();
        $karya  = 0;
        foreach (['praktik_baik', 'bank_inovasi', 'kompetisi_peserta', 'suara'] as $tabel) {
            $karya += $db->table($tabel)->where('id_guru', $id)->countAllResults();
        }

        if ($karya > 0) {
            return redirect()->to('guru')->with('gagal', esc($guru['nama_guru']) . ' tidak bisa dihapus karena sudah memiliki karya atau SUARA. Nonaktifkan saja.');
        }

        $this->guru->delete($id);

        return redirect()->to('guru')->with('sukses', esc($guru['nama_guru']) . ' berhasil dihapus.');
    }
}
