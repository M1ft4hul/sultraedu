<?php

namespace App\Controllers;

class Ckompetisi extends BaseController
{
    // Urutan tahapan kompetisi (urutan array = urutan tahap)
    public const STATUS = [
        'draft'       => 'Draft',
        'pendaftaran' => 'Pendaftaran Dibuka',
        'berlangsung' => 'Penjurian',
        'selesai'     => 'Selesai',
    ];

    // Kategori bawaan saat membuat kompetisi baru
    public const KATEGORI_BAWAAN = [
        'Transformasi Digital Pembelajaran & Manajemen Sekolah',
        'Pemerataan Akses & Inklusi Pendidikan',
        'Penguatan Karakter & Gizi Anak Sekolah',
        'Inovasi Pembelajaran Efektif & Potensi Peserta Didik',
    ];

    // Kriteria penilaian bawaan (total skor maksimal = 100)
    public const KRITERIA_BAWAAN = [
        ['nama_kriteria' => 'Relevansi Permasalahan', 'keterangan' => 'Kesesuaian solusi dengan masalah nyata sekolah',       'skor_maks' => 15],
        ['nama_kriteria' => 'Kebaruan / Inovasi',     'keterangan' => 'Unsur kebaruan dan nilai keunikan strategi',           'skor_maks' => 20],
        ['nama_kriteria' => 'Efektivitas & Hasil',    'keterangan' => 'Capaian target dan efektivitas implementasi',          'skor_maks' => 20],
        ['nama_kriteria' => 'Manfaat & Dampak',       'keterangan' => 'Dampak bagi peserta didik & satuan pendidikan',        'skor_maks' => 15],
        ['nama_kriteria' => 'Keberlanjutan',          'keterangan' => 'Potensi inovasi terus berjalan jangka panjang',        'skor_maks' => 10],
        ['nama_kriteria' => 'Potensi Replikasi',      'keterangan' => 'Kemudahan diadopsi oleh sekolah lain',                 'skor_maks' => 10],
        ['nama_kriteria' => 'Dokumentasi & Video',    'keterangan' => 'Kejelasan penyajian video 3 menit #sultraeduvation',   'skor_maks' => 10],
    ];

    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    private function jumlahPeserta(int $idKompetisi): int
    {
        return \Config\Database::connect()->table('kompetisi_peserta')
            ->where('id_kompetisi', $idKompetisi)
            ->countAllResults();
    }

    // Kembali ke form dengan pesan error (form terbuka lagi dengan isian sebelumnya)
    private function gagalForm($pesan)
    {
        return redirect()->back()->withInput()->with('errors', (array) $pesan);
    }

    // =====================================================
    // DAFTAR KOMPETISI
    // =====================================================
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        switch (session()->get('role')) {
            case 'admin_pusat':
                return $this->indexDinas();
            case 'admin_sekolah':
                return $this->indexSekolah();
            case 'guru':
                return $this->indexGuru();
        }

        return redirect()->to('dashboard');
    }

    // =====================================================
    // GURU: lomba yang dibuka & lomba yang diikuti
    // =====================================================
    private function indexGuru()
    {
        $db     = \Config\Database::connect();
        $idGuru = (int) session()->get('user_id');
        $tab    = $this->request->getGet('tab') === 'saya' ? 'saya' : 'buka';

        $data = ['tab' => $tab, 'label' => self::STATUS];

        // Jumlah untuk kedua tab
        $data['jumlahTab'] = [
            'buka' => $db->table('kompetisi')->where('status', 'pendaftaran')->countAllResults(),
            'saya' => $db->table('kompetisi_peserta p')
                ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
                ->where('p.id_guru', $idGuru)->where('k.status !=', 'draft')->countAllResults(),
        ];

        if ($tab === 'buka') {
            $kompetisi = $db->table('kompetisi')
                ->select('id_kompetisi, nama_kompetisi, deskripsi, banner, tanggal_mulai, tanggal_selesai')
                ->where('status', 'pendaftaran')
                ->orderBy('tanggal_selesai', 'ASC')
                ->get()->getResultArray();

            $ids = array_column($kompetisi, 'id_kompetisi');
            $kategori = $kriteria = $milikSaya = $jumlahPeserta = [];

            if ($ids) {
                foreach ($db->table('kompetisi_kategori')->select('id_kategori, id_kompetisi, nama_kategori, deskripsi')
                    ->whereIn('id_kompetisi', $ids)->orderBy('urutan', 'ASC')->get()->getResultArray() as $r) {
                    $kategori[$r['id_kompetisi']][] = $r;
                }
                foreach ($db->table('kompetisi_kriteria')->select('id_kompetisi, nama_kriteria, keterangan, skor_maks')
                    ->whereIn('id_kompetisi', $ids)->orderBy('urutan', 'ASC')->get()->getResultArray() as $r) {
                    $kriteria[$r['id_kompetisi']][] = $r;
                }
                foreach ($db->table('kompetisi_peserta')
                    ->select('id_peserta, id_kompetisi, id_kategori, id_praktik_baik, judul_karya, deskripsi_karya, link_video, status_validasi, created_at')
                    ->where('id_guru', $idGuru)->whereIn('id_kompetisi', $ids)->get()->getResultArray() as $r) {
                    $milikSaya[$r['id_kompetisi']] = $r;
                }
                foreach ($db->table('kompetisi_peserta')->select('id_kompetisi, COUNT(*) AS jumlah')
                    ->whereIn('id_kompetisi', $ids)->groupBy('id_kompetisi')->get()->getResultArray() as $r) {
                    $jumlahPeserta[$r['id_kompetisi']] = (int) $r['jumlah'];
                }
            }

            foreach ($kompetisi as &$k) {
                $k['kategori'] = $kategori[$k['id_kompetisi']] ?? [];
                $k['kriteria'] = $kriteria[$k['id_kompetisi']] ?? [];
                $k['karya']    = $milikSaya[$k['id_kompetisi']] ?? null;
                $k['jumlah']   = $jumlahPeserta[$k['id_kompetisi']] ?? 0;
            }
            unset($k);

            $data['kompetisi'] = $kompetisi;

            // Praktik baik milik guru yang sudah disetujui Dinas (opsional ditautkan ke karya)
            $data['praktik'] = $db->table('praktik_baik')->select('id_praktik_baik, judul')
                ->where('id_guru', $idGuru)
                ->where('status_verifikasi_sekolah', 'disetujui')
                ->where('status_verifikasi_dinas', 'disetujui')
                ->orderBy('judul', 'ASC')->get()->getResultArray();
        } else {
            $riwayat = $db->table('kompetisi_peserta p')
                ->select('p.id_peserta, p.judul_karya, p.deskripsi_karya, p.link_video, p.peringkat, p.nilai, p.status_validasi,
                          k.nama_kompetisi, k.tanggal_mulai, k.tanggal_selesai, k.status, k.hasil_diumumkan, k.tanggal_pengumuman,
                          kk.nama_kategori')
                ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
                ->join('kompetisi_kategori kk', 'kk.id_kategori = p.id_kategori', 'left')
                ->where('p.id_guru', $idGuru)
                ->where('k.status !=', 'draft')
                ->orderBy('k.tanggal_mulai', 'DESC')
                ->get()->getResultArray();

            // Rincian nilai per kriteria (rata-rata semua juri), hanya untuk hasil yang sudah diumumkan
            $idDiumumkan = array_column(array_filter($riwayat, fn ($r) => $r['hasil_diumumkan']), 'id_peserta');
            $rincian     = [];
            if ($idDiumumkan) {
                $baris = $db->table('kompetisi_nilai n')
                    ->select('n.id_peserta, kr.nama_kriteria, kr.skor_maks, kr.urutan, ROUND(AVG(n.skor), 2) AS skor, COUNT(DISTINCT n.id_juri) AS juri')
                    ->join('kompetisi_kriteria kr', 'kr.id_kriteria = n.id_kriteria')
                    ->whereIn('n.id_peserta', $idDiumumkan)
                    ->groupBy('n.id_peserta, kr.id_kriteria, kr.nama_kriteria, kr.skor_maks, kr.urutan')
                    ->orderBy('kr.urutan', 'ASC')
                    ->get()->getResultArray();
                foreach ($baris as $b) {
                    $rincian[$b['id_peserta']][] = [
                        'kriteria'  => $b['nama_kriteria'] . ' (bobot ' . (int) $b['skor_maks'] . '%)',
                        'skor'      => (float) $b['skor'],
                        'skor_maks' => 100, // nilai juri berskala 1–100
                        'juri'      => (int) $b['juri'],
                    ];
                }
            }

            // Nilai & peringkat disembunyikan sebelum hasil diumumkan
            foreach ($riwayat as &$r) {
                if (! $r['hasil_diumumkan']) {
                    $r['peringkat'] = null;
                    $r['nilai']     = null;
                }
                $r['rincian'] = $rincian[$r['id_peserta']] ?? [];
            }
            unset($r);

            $data['riwayat'] = $riwayat;
        }

        return view('admin/guru/kompetisi', $data);
    }

    // =====================================================
    // GURU: daftarkan / ubah karya
    // =====================================================
    public function daftar()
    {
        if (! session()->get('logged_in') || session()->get('role') !== 'guru') {
            return redirect()->to('dashboard');
        }

        $db          = \Config\Database::connect();
        $idGuru      = (int) session()->get('user_id');
        $idKompetisi = (int) $this->request->getPost('id_kompetisi');

        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $idKompetisi)->get()->getRowArray();
        if (! $kompetisi || $kompetisi['status'] !== 'pendaftaran') {
            return redirect()->to('kompetisi')->with('gagal', 'Pendaftaran kompetisi ini sudah ditutup.');
        }

        $input = [
            'id_kategori'     => (int) $this->request->getPost('id_kategori'),
            'judul_karya'     => trim((string) $this->request->getPost('judul_karya')),
            'deskripsi_karya' => trim((string) $this->request->getPost('deskripsi_karya')),
            'link_video'      => trim((string) $this->request->getPost('link_video')),
            'id_praktik_baik' => (int) $this->request->getPost('id_praktik_baik') ?: null,
        ];

        $error = [];
        $kategoriSah = $db->table('kompetisi_kategori')->where('id_kategori', $input['id_kategori'])
            ->where('id_kompetisi', $idKompetisi)->countAllResults();
        if (! $kategoriSah) {
            $error[] = 'Pilih kategori lomba.';
        }
        if ($input['judul_karya'] === '' || mb_strlen($input['judul_karya']) > 200) {
            $error[] = 'Judul karya wajib diisi (maksimal 200 karakter).';
        }
        if (mb_strlen($input['deskripsi_karya']) < 30) {
            $error[] = 'Deskripsi karya minimal 30 karakter.';
        }
        if (! preg_match('#^https?://#i', $input['link_video']) || ! filter_var($input['link_video'], FILTER_VALIDATE_URL) || mb_strlen($input['link_video']) > 255) {
            $error[] = 'Link video wajib diisi dengan alamat yang valid (diawali http:// atau https://).';
        }
        if ($input['id_praktik_baik']) {
            $punya = $db->table('praktik_baik')->where('id_praktik_baik', $input['id_praktik_baik'])
                ->where('id_guru', $idGuru)->countAllResults();
            if (! $punya) {
                $error[] = 'Praktik baik terkait tidak valid.';
            }
        }

        if ($error) {
            return redirect()->to('kompetisi')->with('gagal', implode(' ', $error));
        }

        $sekarang = date('Y-m-d H:i:s');
        $lama     = $db->table('kompetisi_peserta')->where('id_kompetisi', $idKompetisi)
            ->where('id_guru', $idGuru)->get()->getRowArray();

        // Satu guru satu karya per kompetisi: kalau sudah ada, perbarui
        if ($lama) {
            if ($lama['status_validasi'] === 'tervalidasi') {
                return redirect()->to('kompetisi')->with('gagal', 'Karya Anda sudah divalidasi sehingga tidak bisa diubah lagi.');
            }
            $db->table('kompetisi_peserta')->where('id_peserta', $lama['id_peserta'])
                ->update($input + ['status_validasi' => 'menunggu', 'updated_at' => $sekarang]);

            return redirect()->to('kompetisi')->with('sukses', 'Karya "' . esc($input['judul_karya']) . '" berhasil diperbarui.');
        }

        $db->table('kompetisi_peserta')->insert($input + [
            'id_kompetisi'    => $idKompetisi,
            'id_sekolah'      => session()->get('id_sekolah'),
            'id_guru'         => $idGuru,
            'status_validasi' => 'menunggu',
            'created_at'      => $sekarang,
            'updated_at'      => $sekarang,
        ]);

        return redirect()->to('kompetisi')->with('sukses', 'Selamat! Karya "' . esc($input['judul_karya']) . '" berhasil didaftarkan di ' . esc($kompetisi['nama_kompetisi']) . '.');
    }

    // =====================================================
    // GURU: batalkan pendaftaran
    // =====================================================
    public function batal($idKompetisi)
    {
        if (! session()->get('logged_in') || session()->get('role') !== 'guru') {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $idKompetisi)->get()->getRowArray();
        if (! $kompetisi || $kompetisi['status'] !== 'pendaftaran') {
            return redirect()->to('kompetisi')->with('gagal', 'Pendaftaran sudah ditutup, sehingga tidak bisa dibatalkan.');
        }

        $karya = $db->table('kompetisi_peserta')->where('id_kompetisi', $idKompetisi)
            ->where('id_guru', session()->get('user_id'))->get()->getRowArray();
        if (! $karya) {
            return redirect()->to('kompetisi')->with('gagal', 'Anda belum terdaftar di kompetisi ini.');
        }
        if ($karya['status_validasi'] === 'tervalidasi') {
            return redirect()->to('kompetisi')->with('gagal', 'Karya yang sudah divalidasi tidak bisa dibatalkan.');
        }

        $db->table('kompetisi_peserta')->where('id_peserta', $karya['id_peserta'])->delete();

        return redirect()->to('kompetisi')->with('sukses', 'Pendaftaran di ' . esc($kompetisi['nama_kompetisi']) . ' berhasil dibatalkan.');
    }

    // =====================================================
    // ADMIN SEKOLAH: lihat jadwal, tahapan, dan peserta
    // =====================================================
    private function indexSekolah()
    {
        $db        = \Config\Database::connect();
        $idSekolah = (int) session()->get('id_sekolah');

        // Tab: "berjalan" (pendaftaran & penjurian) atau "riwayat" (sudah selesai)
        $tab       = $this->request->getGet('tab') === 'riwayat' ? 'riwayat' : 'berjalan';
        $statusTab = [
            'berjalan' => ['pendaftaran', 'berlangsung'],
            'riwayat'  => ['selesai'],
        ];

        $builder = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, deskripsi, tanggal_mulai, tanggal_selesai, status, hasil_diumumkan, tanggal_pengumuman')
            ->whereIn('status', $statusTab[$tab]);

        // Berjalan: tenggat terdekat di atas. Riwayat: yang terbaru selesai di atas.
        $kompetisi = $tab === 'berjalan'
            ? $builder->orderBy('tanggal_selesai', 'ASC')->get()->getResultArray()
            : $builder->orderBy('tanggal_selesai', 'DESC')->get()->getResultArray();

        $jumlahTab = [];
        foreach ($statusTab as $kunci => $daftarStatus) {
            $jumlahTab[$kunci] = $db->table('kompetisi')->whereIn('status', $daftarStatus)->countAllResults();
        }

        $ids      = array_column($kompetisi, 'id_kompetisi');
        $peserta  = [];
        $kategori = [];

        if ($ids) {
            // Peserta (yang ditolak Dinas tidak ditampilkan)
            $rows = $db->table('kompetisi_peserta p')
                ->select('p.id_kompetisi, p.id_sekolah, p.judul_karya, p.peringkat, p.nilai, p.status_validasi,
                          s.nama_sekolah, k.nama_kategori, k.urutan, g.nama_guru, g.mapel')
                ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
                ->join('guru g', 'g.id_guru = p.id_guru', 'left')
                ->join('kompetisi_kategori k', 'k.id_kategori = p.id_kategori', 'left')
                ->whereIn('p.id_kompetisi', $ids)
                ->where('p.status_validasi !=', 'ditolak')
                ->orderBy('k.urutan', 'ASC')
                ->orderBy('s.nama_sekolah', 'ASC')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $peserta[$r['id_kompetisi']][] = $r;
            }

            $rows = $db->table('kompetisi_kategori')->select('id_kompetisi, nama_kategori')
                ->whereIn('id_kompetisi', $ids)->orderBy('urutan', 'ASC')->get()->getResultArray();
            foreach ($rows as $r) {
                $kategori[$r['id_kompetisi']][] = $r['nama_kategori'];
            }
        }

        foreach ($kompetisi as &$k) {
            $semua = $peserta[$k['id_kompetisi']] ?? [];

            // Hanya karya sekolah sendiri yang dikirim ke halaman
            $milikSendiri = array_values(array_filter($semua, fn ($d) => (int) $d['id_sekolah'] === $idSekolah));

            foreach ($milikSendiri as &$d) {
                unset($d['id_sekolah'], $d['nama_sekolah']);
                // Nilai & peringkat disembunyikan sebelum hasil diumumkan
                if (! $k['hasil_diumumkan']) {
                    $d['peringkat'] = null;
                    $d['nilai']     = null;
                }
            }
            unset($d);

            $k['peserta']        = $milikSendiri;
            $k['kategori']       = $kategori[$k['id_kompetisi']] ?? [];
            $k['total_karya']    = count($semua);
            $k['jumlah_sekolah'] = count(array_unique(array_column($semua, 'id_sekolah')));
            $k['karya_sendiri']  = count($milikSendiri);
        }
        unset($k);

        return view('admin/sekolah/kompetisi', [
            'kompetisi' => $kompetisi,
            'label'     => self::STATUS,
            'tab'       => $tab,
            'jumlahTab' => $jumlahTab,
        ]);
    }

    // =====================================================
    // ADMIN DINAS: kelola kompetisi
    // =====================================================
    private function indexDinas()
    {
        $db = \Config\Database::connect();

        // Tab: "aktif" (belum selesai) atau "selesai"
        $tab       = $this->request->getGet('tab') === 'selesai' ? 'selesai' : 'aktif';
        $statusTab = [
            'aktif'   => ['draft', 'pendaftaran', 'berlangsung'],
            'selesai' => ['selesai'],
        ];

        $builder = $db->table('kompetisi k')
            ->select('k.*, a.nama_admin AS pembuat')
            ->select('(SELECT COUNT(*) FROM kompetisi_peserta p WHERE p.id_kompetisi = k.id_kompetisi) AS jumlah_peserta', false)
            ->join('admin a', 'a.id_admin = k.id_admin_pembuat', 'left')
            ->whereIn('k.status', $statusTab[$tab]);

        // Belum selesai: tenggat terdekat di atas. Selesai: yang terbaru selesai di atas.
        $kompetisi = $tab === 'aktif'
            ? $builder->orderBy('k.tanggal_selesai', 'ASC')->get()->getResultArray()
            : $builder->orderBy('k.tanggal_selesai', 'DESC')->get()->getResultArray();

        $jumlahTab = [];
        foreach ($statusTab as $kunci => $daftarStatus) {
            $jumlahTab[$kunci] = $db->table('kompetisi')->whereIn('status', $daftarStatus)->countAllResults();
        }

        // Ambil kategori & kriteria semua kompetisi sekaligus
        $ids      = array_column($kompetisi, 'id_kompetisi');
        $kategori = [];
        $kriteria = [];
        $peserta  = [];

        if ($ids) {
            $rows = $db->table('kompetisi_kategori')->whereIn('id_kompetisi', $ids)
                ->orderBy('urutan', 'ASC')->get()->getResultArray();
            foreach ($rows as $r) {
                $kategori[$r['id_kompetisi']][] = $r;
            }

            $rows = $db->table('kompetisi_kriteria')->whereIn('id_kompetisi', $ids)
                ->orderBy('urutan', 'ASC')->get()->getResultArray();
            foreach ($rows as $r) {
                $kriteria[$r['id_kompetisi']][] = $r;
            }

            // Peserta lengkap: sekolah, guru, karya, nilai
            $rows = $db->table('kompetisi_peserta p')
                ->select('p.id_peserta, p.id_kompetisi, p.id_kategori, p.judul_karya, p.deskripsi_karya, p.link_video,
                          p.nilai, p.peringkat, p.status_validasi,
                          s.nama_sekolah, s.kabupaten_kota, g.nama_guru, g.nip, g.mapel')
                ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
                ->join('guru g', 'g.id_guru = p.id_guru', 'left')
                ->whereIn('p.id_kompetisi', $ids)
                ->orderBy('p.peringkat IS NULL', 'ASC', false)
                ->orderBy('p.peringkat', 'ASC')
                ->orderBy('p.nilai', 'DESC')
                ->orderBy('s.nama_sekolah', 'ASC')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $peserta[$r['id_kompetisi']][] = $r;
            }
        }

        foreach ($kompetisi as &$k) {
            $k['jumlah_peserta'] = (int) $k['jumlah_peserta'];
            $k['kategori']       = $kategori[$k['id_kompetisi']] ?? [];
            $k['kriteria']       = $kriteria[$k['id_kompetisi']] ?? [];
            $k['peserta']        = $peserta[$k['id_kompetisi']] ?? [];
        }
        unset($k);

        return view('admin/dinas/kompetisi', [
            'tab'            => $tab,
            'jumlahTab'      => $jumlahTab,
            'kompetisi'      => $kompetisi,
            'label'          => self::STATUS,
            'kategoriBawaan' => self::KATEGORI_BAWAAN,
            'kriteriaBawaan' => self::KRITERIA_BAWAAN,
        ]);
    }

    // =====================================================
    // SIMPAN (buat baru atau edit)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db   = \Config\Database::connect();
        $id   = (int) $this->request->getPost('id_kompetisi');
        $edit = $id > 0;

        $input = [
            'nama_kompetisi'  => trim((string) $this->request->getPost('nama_kompetisi')),
            'deskripsi'       => trim((string) $this->request->getPost('deskripsi')),
            'tanggal_mulai'   => (string) $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => (string) $this->request->getPost('tanggal_selesai'),
        ];

        $rules = [
            'nama_kompetisi'  => 'required|max_length[200]',
            'tanggal_mulai'   => 'required|valid_date[Y-m-d]',
            'tanggal_selesai' => 'required|valid_date[Y-m-d]',
        ];
        $pesan = [
            'nama_kompetisi'  => ['required' => 'Nama kompetisi wajib diisi.'],
            'tanggal_mulai'   => ['required' => 'Tanggal mulai wajib diisi.', 'valid_date' => 'Format tanggal mulai tidak valid.'],
            'tanggal_selesai' => ['required' => 'Tanggal selesai wajib diisi.', 'valid_date' => 'Format tanggal selesai tidak valid.'],
        ];

        if (! $this->validateData($input, $rules, $pesan)) {
            return $this->gagalForm($this->validator->getErrors());
        }
        if ($input['tanggal_selesai'] < $input['tanggal_mulai']) {
            return $this->gagalForm('Tanggal selesai tidak boleh sebelum tanggal mulai.');
        }

        if ($edit && ! $db->table('kompetisi')->where('id_kompetisi', $id)->countAllResults()) {
            return redirect()->to('kompetisi')->with('gagal', 'Kompetisi tidak ditemukan.');
        }

        // Kategori & kriteria hanya boleh diubah kalau belum ada peserta
        $bolehUbahRubrik = ! $edit || $this->jumlahPeserta($id) === 0;
        $kategori        = [];
        $kriteria        = [];

        if ($bolehUbahRubrik) {
            // Kategori
            foreach ((array) $this->request->getPost('kategori') as $nama) {
                $nama = trim((string) $nama);
                if ($nama !== '') {
                    $kategori[] = mb_substr($nama, 0, 150);
                }
            }
            if (empty($kategori)) {
                return $this->gagalForm('Minimal harus ada satu kategori lomba.');
            }

            // Kriteria
            $namaK = (array) $this->request->getPost('kriteria_nama');
            $ketK  = (array) $this->request->getPost('kriteria_keterangan');
            $skorK = (array) $this->request->getPost('kriteria_skor');

            foreach ($namaK as $i => $nama) {
                $nama = trim((string) $nama);
                if ($nama === '') {
                    continue;
                }
                $skor = (int) ($skorK[$i] ?? 0);
                if ($skor < 1) {
                    return $this->gagalForm('Skor maksimal kriteria "' . $nama . '" minimal 1.');
                }
                $kriteria[] = [
                    'nama_kriteria' => mb_substr($nama, 0, 150),
                    'keterangan'    => mb_substr(trim((string) ($ketK[$i] ?? '')), 0, 255) ?: null,
                    'skor_maks'     => $skor,
                ];
            }
            if (empty($kriteria)) {
                return $this->gagalForm('Minimal harus ada satu kriteria penilaian.');
            }

            $total = array_sum(array_column($kriteria, 'skor_maks'));
            if ($total !== 100) {
                return $this->gagalForm('Total skor maksimal semua kriteria harus 100 (saat ini ' . $total . ').');
            }
        }

        // ---------- Banner lomba (opsional) ----------
        $bannerLama = $edit ? $db->table('kompetisi')->select('banner')->where('id_kompetisi', $id)->get()->getRow('banner') : null;
        $bannerBaru = null;
        $file       = $this->request->getFile('banner');

        if ($file && $file->getError() !== UPLOAD_ERR_NO_FILE) {
            if (! $file->isValid()) {
                return $this->gagalForm('Banner gagal diunggah. Coba lagi.');
            }
            if (! in_array(strtolower($file->getClientExtension()), ['jpg', 'jpeg', 'png', 'webp'], true)
                || ! str_starts_with((string) $file->getMimeType(), 'image/')) {
                return $this->gagalForm('Banner harus berupa gambar JPG, PNG, atau WEBP.');
            }
            if ($file->getSizeByUnit('kb') > 2048) {
                return $this->gagalForm('Ukuran banner maksimal 2 MB.');
            }
            if (! is_dir(FCPATH . 'uploads/banner')) {
                mkdir(FCPATH . 'uploads/banner', 0755, true);
            }
            $namaAcak = $file->getRandomName();
            $file->move(FCPATH . 'uploads/banner', $namaAcak);
            $bannerBaru      = 'uploads/banner/' . $namaAcak;
            $input['banner'] = $bannerBaru;
        } elseif ($edit && $this->request->getPost('hapus_banner')) {
            $input['banner'] = null;
        }

        $sekarang = date('Y-m-d H:i:s');
        $db->transStart();

        if ($edit) {
            $db->table('kompetisi')->where('id_kompetisi', $id)->update($input + ['updated_at' => $sekarang]);
        } else {
            $db->table('kompetisi')->insert($input + [
                'status'           => 'draft',
                'id_admin_pembuat' => session()->get('user_id'),
                'created_at'       => $sekarang,
                'updated_at'       => $sekarang,
            ]);
            $id = (int) $db->insertID();
        }

        if ($bolehUbahRubrik) {
            // Ganti seluruh kategori & kriteria dengan yang baru
            $db->table('kompetisi_kategori')->where('id_kompetisi', $id)->delete();
            $db->table('kompetisi_kriteria')->where('id_kompetisi', $id)->delete();

            $barisKategori = [];
            foreach ($kategori as $i => $nama) {
                $barisKategori[] = ['id_kompetisi' => $id, 'nama_kategori' => $nama, 'urutan' => $i + 1];
            }
            $db->table('kompetisi_kategori')->insertBatch($barisKategori);

            $barisKriteria = [];
            foreach ($kriteria as $i => $k) {
                $barisKriteria[] = $k + ['id_kompetisi' => $id, 'urutan' => $i + 1];
            }
            $db->table('kompetisi_kriteria')->insertBatch($barisKriteria);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            // Batalkan file banner yang baru diunggah
            if ($bannerBaru && is_file(FCPATH . $bannerBaru)) {
                @unlink(FCPATH . $bannerBaru);
            }

            return $this->gagalForm('Terjadi kesalahan saat menyimpan. Coba lagi.');
        }

        // Banner lama dihapus kalau diganti atau dihapus
        if ($bannerLama && array_key_exists('banner', $input) && is_file(FCPATH . $bannerLama)) {
            @unlink(FCPATH . $bannerLama);
        }

        $pesanSukses = $edit
            ? 'Kompetisi "' . esc($input['nama_kompetisi']) . '" berhasil diperbarui.'
            : 'Kompetisi "' . esc($input['nama_kompetisi']) . '" berhasil dibuat dengan status Draft.';

        if ($edit && ! $bolehUbahRubrik) {
            $pesanSukses .= ' Kategori dan kriteria tidak diubah karena sudah ada peserta.';
        }

        return redirect()->to('kompetisi')->with('sukses', $pesanSukses);
    }

    // =====================================================
    // PINDAH TAHAP (maju / mundur satu langkah)
    // =====================================================
    public function status($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $id)->get()->getRowArray();

        if (! $kompetisi) {
            return redirect()->to('kompetisi')->with('gagal', 'Kompetisi tidak ditemukan.');
        }

        $urutan  = array_keys(self::STATUS);
        $target  = (string) $this->request->getPost('status');
        $posLama = array_search($kompetisi['status'], $urutan, true);
        $posBaru = array_search($target, $urutan, true);

        if ($posBaru === false || $posLama === false || abs($posBaru - $posLama) !== 1) {
            return redirect()->to('kompetisi')->with('gagal', 'Perubahan tahap tidak valid.');
        }

        // Sebelum membuka pendaftaran, rubrik harus lengkap
        if ($target === 'pendaftaran') {
            $adaKategori = $db->table('kompetisi_kategori')->where('id_kompetisi', $id)->countAllResults();
            $adaKriteria = $db->table('kompetisi_kriteria')->where('id_kompetisi', $id)->countAllResults();
            if (! $adaKategori || ! $adaKriteria) {
                return redirect()->to('kompetisi')->with('gagal', 'Lengkapi kategori dan kriteria penilaian sebelum membuka pendaftaran.');
            }
        }

        $db->table('kompetisi')->where('id_kompetisi', $id)->update([
            'status'     => $target,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $redirect = redirect()->to('kompetisi')->with(
            'sukses',
            'Tahap "' . esc($kompetisi['nama_kompetisi']) . '" sekarang: ' . self::STATUS[$target] . '.'
        );

        // Saat penjurian dimulai: ingatkan kategori yang punya karya tapi belum punya juri aktif
        if ($target === 'berlangsung') {
            $tanpaJuri = $db->query(
                "SELECT kk.nama_kategori
                 FROM kompetisi_kategori kk
                 WHERE kk.id_kompetisi = ?
                   AND EXISTS (SELECT 1 FROM kompetisi_peserta p WHERE p.id_kategori = kk.id_kategori AND p.status_validasi != 'ditolak')
                   AND NOT EXISTS (
                       SELECT 1 FROM juri_penugasan jp
                       JOIN admin a ON a.id_admin = jp.id_juri AND a.status = 'aktif'
                       WHERE jp.id_kategori = kk.id_kategori)
                 ORDER BY kk.urutan",
                [$id]
            )->getResultArray();

            if ($tanpaJuri) {
                $redirect->with('peringatan', 'Kategori berikut belum memiliki juri: '
                    . implode(', ', array_map(fn ($r) => $r['nama_kategori'], $tanpaJuri))
                    . '. Tugaskan juri melalui menu Kelola Data → Tim Juri supaya karyanya bisa dinilai.');
            }
        }

        return $redirect;
    }

    // =====================================================
    // HAPUS (hanya kalau belum ada peserta)
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $id)->get()->getRowArray();

        if (! $kompetisi) {
            return redirect()->to('kompetisi')->with('gagal', 'Kompetisi tidak ditemukan.');
        }
        if ($this->jumlahPeserta((int) $id) > 0) {
            return redirect()->to('kompetisi')->with('gagal', 'Kompetisi yang sudah memiliki peserta tidak bisa dihapus.');
        }

        // Kategori & kriteria ikut terhapus otomatis (ON DELETE CASCADE)
        $db->table('kompetisi')->where('id_kompetisi', $id)->delete();
        if (! empty($kompetisi['banner']) && is_file(FCPATH . $kompetisi['banner'])) {
            @unlink(FCPATH . $kompetisi['banner']);
        }

        return redirect()->to('kompetisi')->with('sukses', 'Kompetisi "' . esc($kompetisi['nama_kompetisi']) . '" berhasil dihapus.');
    }
}