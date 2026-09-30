<?php

namespace App\Controllers;

use App\Models\AdminModel;

class Cjuri extends BaseController
{
    // Hanya Admin Dinas
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // Username unik di tabel admin & guru
    private function usernameTersedia(string $username, ?int $kecuali = null): bool
    {
        $db = \Config\Database::connect();
        $q  = $db->table('admin')->where('username', $username);
        if ($kecuali) {
            $q->where('id_admin !=', $kecuali);
        }

        return $q->countAllResults() === 0
            && $db->table('guru')->where('username', $username)->countAllResults() === 0;
    }

    private function cariJuri($id): ?array
    {
        return (new AdminModel())->where('id_admin', $id)->where('role', 'tim_juri')->first();
    }

    // =====================================================
    // DAFTAR JURI & PENUGASAN
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db = \Config\Database::connect();
        $q  = trim((string) $this->request->getGet('q'));

        $builder = $db->table('admin')
            ->select('id_admin, nama_admin, username, email, keterangan, status, created_at')
            ->where('role', 'tim_juri');
        if ($q !== '') {
            $builder->groupStart()->like('nama_admin', $q)->orLike('username', $q)->orLike('keterangan', $q)->groupEnd();
        }
        $juri = $builder->orderBy('nama_admin', 'ASC')->get()->getResultArray();

        // Penugasan setiap juri (kategori + lomba)
        $tugas = [];
        $ids   = array_column($juri, 'id_admin');
        if ($ids) {
            $rows = $db->table('juri_penugasan jp')
                ->select('jp.id_juri, kk.id_kategori, kk.nama_kategori, k.id_kompetisi, k.nama_kompetisi, k.status')
                ->join('kompetisi_kategori kk', 'kk.id_kategori = jp.id_kategori')
                ->join('kompetisi k', 'k.id_kompetisi = kk.id_kompetisi')
                ->whereIn('jp.id_juri', $ids)
                ->orderBy('k.tanggal_mulai', 'DESC')->orderBy('kk.urutan', 'ASC')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $tugas[$r['id_juri']][$r['id_kompetisi']]['nama']       = $r['nama_kompetisi'];
                $tugas[$r['id_juri']][$r['id_kompetisi']]['status']     = $r['status'];
                $tugas[$r['id_juri']][$r['id_kompetisi']]['kategori'][] = ['id' => (int) $r['id_kategori'], 'nama' => $r['nama_kategori']];
            }
        }

        // Jumlah karya yang sudah dinilai setiap juri
        $dinilai = [];
        if ($ids) {
            foreach (
                $db->table('kompetisi_nilai')->select('id_juri, COUNT(DISTINCT id_peserta) AS n')
                    ->whereIn('id_juri', $ids)->groupBy('id_juri')->get()->getResultArray() as $r
            ) {
                $dinilai[$r['id_juri']] = (int) $r['n'];
            }
        }

        foreach ($juri as &$j) {
            $j['tugas']   = $tugas[$j['id_admin']] ?? [];
            $j['dinilai'] = $dinilai[$j['id_admin']] ?? 0;
        }
        unset($j);

        // Lomba yang masih bisa ditugaskan (belum selesai) beserta kategorinya
        $kompetisi = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, status')
            ->whereIn('status', ['draft', 'pendaftaran', 'berlangsung'])
            ->orderBy('tanggal_selesai', 'ASC')
            ->get()->getResultArray();
        $kategori = [];
        $idKomp   = array_column($kompetisi, 'id_kompetisi');
        if ($idKomp) {
            // Jumlah juri & karya per kategori, untuk membantu Dinas membagi tugas
            foreach (
                $db->query(
                    'SELECT kk.id_kategori, kk.id_kompetisi, kk.nama_kategori,
                        (SELECT COUNT(*) FROM juri_penugasan jp JOIN admin a ON a.id_admin = jp.id_juri AND a.status = "aktif"
                          WHERE jp.id_kategori = kk.id_kategori) AS jumlah_juri,
                        (SELECT COUNT(*) FROM kompetisi_peserta p WHERE p.id_kategori = kk.id_kategori AND p.status_validasi != "ditolak") AS jumlah_karya
                 FROM kompetisi_kategori kk
                 WHERE kk.id_kompetisi IN (' . implode(',', array_map('intval', $idKomp)) . ')
                 ORDER BY kk.urutan'
                )->getResultArray() as $r
            ) {
                $kategori[$r['id_kompetisi']][] = $r;
            }
        }
        foreach ($kompetisi as &$k) {
            $k['kategori'] = $kategori[$k['id_kompetisi']] ?? [];
        }
        unset($k);

        // Peringatan: kategori lomba yang sedang dinilai tapi belum punya juri
        $tanpaJuri = [];
        foreach ($kompetisi as $k) {
            foreach ($k['kategori'] as $kat) {
                if ((int) $kat['jumlah_juri'] === 0 && in_array($k['status'], ['pendaftaran', 'berlangsung'], true)) {
                    $tanpaJuri[] = $kat['nama_kategori'] . ' (' . $k['nama_kompetisi'] . ')';
                }
            }
        }

        return view('admin/dinas/juri', [
            'juri'      => $juri,
            'kompetisi' => $kompetisi,
            'tanpaJuri' => $tanpaJuri,
            'q'         => $q,
            'ringkas'   => [
                'total'    => count($juri),
                'aktif'    => count(array_filter($juri, fn($j) => $j['status'] === 'aktif')),
                'bertugas' => count(array_filter($juri, fn($j) => ! empty($j['tugas']))),
            ],
        ]);
    }

    // =====================================================
    // SIMPAN AKUN JURI (tambah / edit)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $model = new AdminModel();
        $id    = (int) $this->request->getPost('id_admin');
        $input = [
            'nama_admin' => trim((string) $this->request->getPost('nama_admin')),
            'email'      => trim((string) $this->request->getPost('email')),
            'keterangan' => trim((string) $this->request->getPost('keterangan')),
        ];
        $username = strtolower(trim((string) $this->request->getPost('username')));
        $password = (string) $this->request->getPost('password');

        $error = [];
        if ($input['nama_admin'] === '' || mb_strlen($input['nama_admin']) > 100) {
            $error[] = 'Nama juri wajib diisi (maksimal 100 karakter).';
        }
        if ($input['email'] !== '' && ! filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
            $error[] = 'Format email tidak valid.';
        }
        if (mb_strlen($input['keterangan']) > 150) {
            $error[] = 'Instansi/keahlian maksimal 150 karakter.';
        }

        if ($id) {
            if (! $this->cariJuri($id)) {
                return redirect()->to('juri')->with('gagal', 'Data juri tidak ditemukan.');
            }
        } else {
            if (! preg_match('/^[a-z0-9_-]{4,50}$/', $username)) {
                $error[] = 'Username 4–50 karakter: huruf kecil, angka, garis bawah, atau tanda hubung.';
            } elseif (! $this->usernameTersedia($username)) {
                $error[] = 'Username "' . $username . '" sudah dipakai akun lain.';
            }
            if (strlen($password) < 8) {
                $error[] = 'Password minimal 8 karakter.';
            }
        }

        if ($error) {
            return redirect()->to('juri')->withInput()->with('errors', $error);
        }

        $input['email']      = $input['email'] ?: null;
        $input['keterangan'] = $input['keterangan'] ?: null;

        if ($id) {
            $model->update($id, $input);

            return redirect()->to('juri')->with('sukses', 'Data ' . esc($input['nama_admin']) . ' berhasil diperbarui.');
        }

        $model->insert($input + [
            'username'   => $username,
            'password'   => $password, // di-hash otomatis oleh AdminModel
            'role'       => 'tim_juri',
            'id_sekolah' => null,
            'status'     => 'aktif',
        ]);

        return redirect()->to('juri')
            ->with('sukses', 'Akun juri ' . esc($input['nama_admin']) . ' berhasil dibuat. Jangan lupa tugaskan kategori yang dinilainya.')
            ->with('akunBaru', ['jenis' => 'baru', 'nama' => $input['nama_admin'], 'username' => $username, 'password' => $password]);
    }

    // =====================================================
    // RESET PASSWORD
    // =====================================================
    public function reset($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $juri = $this->cariJuri($id);
        if (! $juri) {
            return redirect()->to('juri')->with('gagal', 'Data juri tidak ditemukan.');
        }

        $password = (string) $this->request->getPost('password');
        if (strlen($password) < 8) {
            return redirect()->to('juri')->with('gagal', 'Password minimal 8 karakter.');
        }

        (new AdminModel())->update($id, ['password' => $password]);

        return redirect()->to('juri')
            ->with('sukses', 'Password ' . esc($juri['nama_admin']) . ' berhasil direset.')
            ->with('akunBaru', ['jenis' => 'reset', 'nama' => $juri['nama_admin'], 'username' => $juri['username'], 'password' => $password]);
    }

    // =====================================================
    // AKTIFKAN / NONAKTIFKAN
    // =====================================================
    public function status($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $juri = $this->cariJuri($id);
        if (! $juri) {
            return redirect()->to('juri')->with('gagal', 'Data juri tidak ditemukan.');
        }

        $baru = $juri['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        (new AdminModel())->update($id, ['status' => $baru]);

        return redirect()->to('juri')->with('sukses', esc($juri['nama_admin']) . ' sekarang ' . $baru . '.');
    }

    // =====================================================
    // SIMPAN PENUGASAN (per lomba: kategori yang dicentang)
    // =====================================================
    public function tugas($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $juri = $this->cariJuri($id);
        if (! $juri) {
            return redirect()->to('juri')->with('gagal', 'Data juri tidak ditemukan.');
        }

        $db          = \Config\Database::connect();
        $idKompetisi = (int) $this->request->getPost('id_kompetisi');
        $kompetisi   = $db->table('kompetisi')->where('id_kompetisi', $idKompetisi)->get()->getRowArray();
        if (! $kompetisi || $kompetisi['status'] === 'selesai') {
            return redirect()->to('juri')->with('gagal', 'Penugasan hanya bisa diatur untuk lomba yang belum selesai.');
        }

        // Hanya kategori milik lomba ini yang diterima
        $kategoriLomba = array_map('intval', array_column(
            $db->table('kompetisi_kategori')->select('id_kategori')->where('id_kompetisi', $idKompetisi)->get()->getResultArray(),
            'id_kategori'
        ));
        $dipilih = array_values(array_intersect(array_map('intval', (array) $this->request->getPost('kategori')), $kategoriLomba));

        $db->transStart();
        // Ganti penugasan juri ini di lomba tersebut
        if ($kategoriLomba) {
            $db->table('juri_penugasan')->where('id_juri', $id)->whereIn('id_kategori', $kategoriLomba)->delete();
        }
        $sekarang = date('Y-m-d H:i:s');
        foreach ($dipilih as $idKategori) {
            $db->table('juri_penugasan')->insert(['id_juri' => $id, 'id_kategori' => $idKategori, 'created_at' => $sekarang]);
        }
        $db->transComplete();

        $pesan = $dipilih
            ? esc($juri['nama_admin']) . ' ditugaskan menilai ' . count($dipilih) . ' kategori di ' . esc($kompetisi['nama_kompetisi']) . '.'
            : 'Penugasan ' . esc($juri['nama_admin']) . ' di ' . esc($kompetisi['nama_kompetisi']) . ' dikosongkan.';

        return redirect()->to('juri')->with('sukses', $pesan);
    }
}
