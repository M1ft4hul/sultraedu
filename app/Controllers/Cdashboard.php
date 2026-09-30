<?php

namespace App\Controllers;

class Cdashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        // Pilih dashboard sesuai role
        switch (session()->get('role')) {
            case 'admin_pusat':
                return $this->dashboardDinas();
            case 'admin_sekolah':
                return $this->dashboardSekolah();
            case 'tim_juri':
                return $this->dashboardJuri();
            case 'guru':
                return $this->dashboardGuru();
        }

        // Role tidak dikenal: keluarkan user dan beri pesan di halaman login
        session()->remove(['logged_in', 'user_id', 'user_type', 'nama', 'username', 'role', 'id_sekolah']);

        return redirect()->to('login')
            ->with('error', 'Hak akses akun Anda tidak dikenali. Silakan hubungi Admin Dinas.');
    }

    // =====================================================
    // ADMIN DINAS
    // =====================================================
    private function dashboardDinas()
    {
        $db = \Config\Database::connect();

        // Statistik se-provinsi
        $data['stat'] = [
            'sekolah'   => $db->table('sekolah')->where('status', 'aktif')->countAllResults(),
            'praktik'   => $db->table('praktik_baik')->where('status_verifikasi_dinas', 'disetujui')->countAllResults(),
            'inovasi'   => $db->table('bank_inovasi')->where('status_verifikasi', 'disetujui')->countAllResults(),
            'kompetisi' => $db->table('kompetisi_peserta')->countAllResults(),
            'suara'     => $db->table('suara')->countAllResults(),
        ];

        // Pengumuman: kompetisi yang membuka pendaftaran & yang hasilnya sudah diumumkan
        $data['pengumuman'] = $this->ambilPengumuman($db);

        // Antrean praktik baik yang menunggu validasi Dinas
        $antrean = $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.tanggal_upload, s.nama_sekolah, s.kabupaten_kota, g.nama_guru')
            ->join('sekolah s', 's.id_sekolah = pb.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->where('pb.status_verifikasi_sekolah', 'disetujui')
            ->where('pb.status_verifikasi_dinas', 'menunggu');

        $data['totalAntrean'] = $antrean->countAllResults(false);
        $data['antrean']      = $antrean->orderBy('pb.tanggal_upload', 'ASC')->limit(5)->get()->getResultArray();

        return view('admin/dinas/dashboard', $data);
    }

    // =====================================================
    // PENGUMUMAN (dipakai bersama semua dashboard)
    // =====================================================
    private function ambilPengumuman($db): array
    {
        return $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, status, tanggal_selesai, hasil_diumumkan, tanggal_pengumuman')
            ->groupStart()
            ->where('status', 'pendaftaran')
            ->orWhere('hasil_diumumkan', 1)
            ->groupEnd()
            ->orderBy('COALESCE(tanggal_pengumuman, updated_at)', 'DESC', false)
            ->limit(3)
            ->get()->getResultArray();
    }

    // =====================================================
    // ADMIN SEKOLAH
    // =====================================================
    private function dashboardSekolah()
    {
        $db        = \Config\Database::connect();
        $idSekolah = session()->get('id_sekolah');

        // Identitas sekolah
        $data['sekolah'] = $db->table('sekolah')
            ->select('nama_sekolah, npsn, jenjang, kabupaten_kota')
            ->where('id_sekolah', $idSekolah)
            ->get()->getRowArray();

        // Statistik khusus sekolah ini
        $data['stat'] = [
            'guru'           => $db->table('guru')->where('id_sekolah', $idSekolah)->where('status', 'aktif')->countAllResults(),
            'tungguSekolah'  => $db->table('praktik_baik')->where('id_sekolah', $idSekolah)->where('status_verifikasi_sekolah', 'menunggu')->countAllResults(),
            'praktik'        => $db->table('praktik_baik')->where('id_sekolah', $idSekolah)->where('status_verifikasi_dinas', 'disetujui')->countAllResults(),
            'inovasi'        => $db->table('bank_inovasi')->where('id_sekolah', $idSekolah)->where('status_verifikasi', 'disetujui')->countAllResults(),
            'juara'          => $db->table('apresiasi')->where('id_sekolah', $idSekolah)->countAllResults(),
        ];

        // Antrean verifikasi tahap 1 (paling lama menunggu di atas)
        $data['antrean'] = $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.kategori, pb.tanggal_upload, g.nama_guru')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->where('pb.id_sekolah', $idSekolah)
            ->where('pb.status_verifikasi_sekolah', 'menunggu')
            ->orderBy('pb.tanggal_upload', 'ASC')
            ->limit(5)
            ->get()->getResultArray();

        // Prestasi sekolah dari kompetisi
        $data['juara'] = $db->table('apresiasi a')
            ->select('a.jenis_apresiasi, a.bukti_file, p.judul_karya, g.nama_guru, k.nama_kompetisi')
            ->join('kompetisi_peserta p', 'p.id_peserta = a.id_peserta')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->join('guru g', 'g.id_guru = p.id_guru', 'left')
            ->where('a.id_sekolah', $idSekolah)
            ->orderBy('k.tanggal_selesai', 'DESC')
            ->orderBy('p.peringkat', 'ASC')
            ->limit(5)
            ->get()->getResultArray();

        $data['pengumuman'] = $this->ambilPengumuman($db);

        return view('admin/sekolah/dashboard', $data);
    }

    // =====================================================
    // TIM JURI
    // Ganti fungsi dashboardJuri() lama di Cdashboard.php dengan fungsi ini
    // =====================================================
    private function dashboardJuri()
    {
        $db     = \Config\Database::connect();
        $idJuri = (int) session()->get('user_id');

        // ---------- Kategori yang ditugaskan kepada juri ini ----------
        $kategoriTugas = array_map('intval', array_column(
            $db->table('juri_penugasan')->select('id_kategori')->where('id_juri', $idJuri)->get()->getResultArray(),
            'id_kategori'
        ));

        // ---------- Lomba penjurian yang punya kategori tugas juri ini ----------
        $kompetisi = [];
        if ($kategoriTugas) {
            $kompetisi = $db->table('kompetisi k')
                ->select('k.id_kompetisi, k.nama_kompetisi, k.tanggal_mulai, k.tanggal_selesai')
                ->join('kompetisi_kategori kk', 'kk.id_kompetisi = k.id_kompetisi')
                ->where('k.status', 'berlangsung')
                ->whereIn('kk.id_kategori', $kategoriTugas)
                ->groupBy('k.id_kompetisi, k.nama_kompetisi, k.tanggal_mulai, k.tanggal_selesai')
                ->orderBy('k.tanggal_selesai', 'ASC')
                ->get()->getResultArray();
        }
        $data['adaTugas'] = ! empty($kategoriTugas);

        $ids      = array_column($kompetisi, 'id_kompetisi');
        $peserta  = [];
        $nilaiku  = [];
        $kriteria = [];
        $kategori = [];

        if ($ids) {
            // Karya yang dinilai: karya di kategori tugas juri ini yang tidak ditolak
            $peserta = $db->table('kompetisi_peserta p')
                ->select('p.id_peserta, p.id_kompetisi, p.id_kategori, p.judul_karya, kk.nama_kategori, kk.urutan')
                ->join('kompetisi_kategori kk', 'kk.id_kategori = p.id_kategori', 'left')
                ->whereIn('p.id_kompetisi', $ids)
                ->whereIn('p.id_kategori', $kategoriTugas)
                ->where('p.status_validasi !=', 'ditolak')
                ->orderBy('p.created_at', 'ASC')
                ->get()->getResultArray();

            // Jumlah kriteria per lomba (syarat penilaian lengkap)
            foreach (
                $db->table('kompetisi_kriteria')->select('id_kompetisi, COUNT(*) AS n')
                    ->whereIn('id_kompetisi', $ids)->groupBy('id_kompetisi')->get()->getResultArray() as $r
            ) {
                $kriteria[$r['id_kompetisi']] = (int) $r['n'];
            }

            foreach (
                $db->table('kompetisi_kategori')->select('id_kategori, id_kompetisi, nama_kategori')
                    ->whereIn('id_kompetisi', $ids)->whereIn('id_kategori', $kategoriTugas)
                    ->orderBy('urutan', 'ASC')->get()->getResultArray() as $r
            ) {
                $kategori[$r['id_kompetisi']][] = $r;
            }

            // Skor yang sudah diberikan juri ini
            $idPeserta = array_column($peserta, 'id_peserta');
            if ($idPeserta) {
                foreach (
                    $db->table('kompetisi_nilai')
                        ->select('id_peserta, COUNT(*) AS n, SUM(skor) AS total')
                        ->where('id_juri', $idJuri)->whereIn('id_peserta', $idPeserta)
                        ->groupBy('id_peserta')->get()->getResultArray() as $r
                ) {
                    $nilaiku[$r['id_peserta']] = $r;
                }
            }
        }

        // Tandai karya yang sudah lengkap dinilai juri ini
        foreach ($peserta as &$p) {
            $butuh           = $kriteria[$p['id_kompetisi']] ?? 0;
            $sudah           = (int) ($nilaiku[$p['id_peserta']]['n'] ?? 0);
            $p['selesai']    = $butuh > 0 && $sudah >= $butuh;
            $p['sebagian']   = $sudah > 0 && ! $p['selesai'];
        }
        unset($p);

        // Progres per lomba & per kategori
        foreach ($kompetisi as &$k) {
            $milik          = array_filter($peserta, fn($p) => (int) $p['id_kompetisi'] === (int) $k['id_kompetisi']);
            $k['total']     = count($milik);
            $k['dinilai']   = count(array_filter($milik, fn($p) => $p['selesai']));
            $k['kriteria']  = $kriteria[$k['id_kompetisi']] ?? 0;
            $k['kategori']  = [];
            foreach ($kategori[$k['id_kompetisi']] ?? [] as $kat) {
                $diKat = array_filter($milik, fn($p) => (int) $p['id_kategori'] === (int) $kat['id_kategori']);
                $k['kategori'][] = [
                    'nama'    => $kat['nama_kategori'],
                    'total'   => count($diKat),
                    'dinilai' => count(array_filter($diKat, fn($p) => $p['selesai'])),
                ];
            }
        }
        unset($k);

        $namaLomba = array_column($kompetisi, 'nama_kompetisi', 'id_kompetisi');

        // Antrean: karya yang belum lengkap dinilai (yang sudah dimulai didahulukan)
        $antrean = array_values(array_filter($peserta, fn($p) => ! $p['selesai']));
        usort($antrean, fn($a, $b) => (int) $b['sebagian'] - (int) $a['sebagian']);
        foreach ($antrean as &$a) {
            $a['nama_kompetisi'] = $namaLomba[$a['id_kompetisi']] ?? '-';
        }
        unset($a);

        $data['kompetisi'] = $kompetisi;
        $data['antrean']   = array_slice($antrean, 0, 5);
        $data['stat']      = [
            'lomba'   => count($kompetisi),
            'karya'   => count($peserta),
            'dinilai' => count(array_filter($peserta, fn($p) => $p['selesai'])),
            'belum'   => count($antrean),
        ];

        // ---------- Riwayat penilaian terakhir (semua lomba) ----------
        $data['riwayat'] = $db->table('kompetisi_nilai n')
            ->select('p.judul_karya, k.nama_kompetisi, kk.nama_kategori,
                      SUM(n.skor * kr.skor_maks) / 100 AS total, MAX(n.updated_at) AS terakhir')
            ->join('kompetisi_kriteria kr', 'kr.id_kriteria = n.id_kriteria')
            ->join('kompetisi_peserta p', 'p.id_peserta = n.id_peserta')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->join('kompetisi_kategori kk', 'kk.id_kategori = p.id_kategori', 'left')
            ->where('n.id_juri', $idJuri)
            ->groupBy('n.id_peserta, p.judul_karya, k.nama_kompetisi, kk.nama_kategori')
            ->orderBy('terakhir', 'DESC')
            ->limit(5)
            ->get()->getResultArray();

        $data['pengumuman'] = $this->ambilPengumuman($db);

        return view('admin/juri/dashboard', $data);
    }

    // =====================================================
    // GURU
    // =====================================================
    private function dashboardGuru()
    {
        $db     = \Config\Database::connect();
        $idGuru = (int) session()->get('user_id');

        // Identitas guru & sekolahnya
        $data['guru'] = $db->table('guru g')
            ->select('g.nama_guru, g.nip, g.mapel, s.nama_sekolah, s.npsn, s.kabupaten_kota')
            ->join('sekolah s', 's.id_sekolah = g.id_sekolah', 'left')
            ->where('g.id_guru', $idGuru)
            ->get()->getRowArray();

        // ---------- Pengajuan: praktik baik + inovasi milik guru ini ----------
        $praktik = $db->table('praktik_baik')
            ->select("id_praktik_baik AS id, judul, tanggal_upload AS tanggal, updated_at,
                      status_verifikasi_sekolah AS st_sekolah, status_verifikasi_dinas AS st_dinas,
                      catatan_admin_sekolah AS catatan_sekolah, catatan_petugas AS catatan_dinas,
                      'praktik' AS jenis", false)
            ->where('id_guru', $idGuru)
            ->get()->getResultArray();

        $inovasi = $db->table('bank_inovasi')
            ->select("id_inovasi AS id, judul_inovasi AS judul, created_at AS tanggal, updated_at,
                      status_verifikasi_sekolah AS st_sekolah, status_verifikasi AS st_dinas,
                      catatan_sekolah, catatan_verifikasi AS catatan_dinas,
                      'inovasi' AS jenis", false)
            ->where('id_guru', $idGuru)
            ->get()->getResultArray();

        $pengajuan = array_merge($praktik, $inovasi);

        // Tentukan posisi setiap pengajuan dalam alur dua tahap
        foreach ($pengajuan as &$p) {
            if ($p['st_sekolah'] === 'menunggu') {
                $p['tahap'] = 'tunggu_sekolah';
            } elseif ($p['st_sekolah'] === 'ditolak') {
                $p['tahap'] = 'tolak_sekolah';
            } elseif ($p['st_dinas'] === 'menunggu') {
                $p['tahap'] = 'tunggu_dinas';
            } elseif ($p['st_dinas'] === 'ditolak') {
                $p['tahap'] = 'tolak_dinas';
            } else {
                $p['tahap'] = 'disetujui';
            }
        }
        unset($p);

        // Terbaru diperbarui di atas
        usort($pengajuan, fn($a, $b) => strcmp((string) $b['updated_at'], (string) $a['updated_at']));

        $hitung = fn(array $tahap) => count(array_filter($pengajuan, fn($p) => in_array($p['tahap'], $tahap, true)));

        $data['perbaikan'] = array_values(array_filter($pengajuan, fn($p) => in_array($p['tahap'], ['tolak_sekolah', 'tolak_dinas'], true)));
        $data['pengajuan'] = array_slice($pengajuan, 0, 5);

        // ---------- Kompetisi ----------
        $diikuti = $db->table('kompetisi_peserta')->select('id_kompetisi')
            ->where('id_guru', $idGuru)->get()->getResultArray();
        $idDiikuti = array_map('intval', array_column($diikuti, 'id_kompetisi'));

        $data['kompetisiBuka'] = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, tanggal_mulai, tanggal_selesai')
            ->where('status', 'pendaftaran')
            ->orderBy('tanggal_selesai', 'ASC')
            ->limit(3)
            ->get()->getResultArray();
        foreach ($data['kompetisiBuka'] as &$k) {
            $k['terdaftar'] = in_array((int) $k['id_kompetisi'], $idDiikuti, true);
        }
        unset($k);

        // ---------- Prestasi (juara) ----------
        $data['prestasi'] = $db->table('apresiasi a')
            ->select('a.bukti_file, p.judul_karya, p.peringkat, p.nilai, k.nama_kompetisi, kk.nama_kategori')
            ->join('kompetisi_peserta p', 'p.id_peserta = a.id_peserta')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->join('kompetisi_kategori kk', 'kk.id_kategori = p.id_kategori', 'left')
            ->where('p.id_guru', $idGuru)
            ->orderBy('k.tanggal_pengumuman', 'DESC')
            ->limit(3)
            ->get()->getResultArray();

        // ---------- Jadwal Monev untuk karya guru ini ----------
        $data['monev'] = $db->table('monev m')
            ->select('m.tanggal_monev, m.aspek_monev, m.status, p.judul_karya')
            ->join('kompetisi_peserta p', 'p.id_peserta = m.id_peserta')
            ->where('p.id_guru', $idGuru)
            ->where('m.status', 'dijadwalkan')
            ->orderBy('m.tanggal_monev', 'ASC')
            ->limit(3)
            ->get()->getResultArray();

        // ---------- Statistik ----------
        $data['stat'] = [
            'disetujui' => $hitung(['disetujui']),
            'diproses'  => $hitung(['tunggu_sekolah', 'tunggu_dinas']),
            'perbaikan' => count($data['perbaikan']),
            'kompetisi' => count($idDiikuti),
            'juara'     => $db->table('apresiasi a')
                ->join('kompetisi_peserta p', 'p.id_peserta = a.id_peserta')
                ->where('p.id_guru', $idGuru)->countAllResults(),
        ];

        $data['pengumuman'] = $this->ambilPengumuman($db);

        return view('admin/guru/dashboard', $data);
    }
}
