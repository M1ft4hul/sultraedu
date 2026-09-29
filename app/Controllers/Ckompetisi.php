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
        }

        return redirect()->to('dashboard');
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
                          s.nama_sekolah, s.kabupaten_kota, k.nama_kategori, k.urutan')
                ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
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
            $daftar = $peserta[$k['id_kompetisi']] ?? [];

            // Nilai & peringkat disembunyikan sebelum hasil diumumkan
            if (! $k['hasil_diumumkan']) {
                foreach ($daftar as &$d) {
                    $d['peringkat'] = null;
                    $d['nilai']     = null;
                }
                unset($d);
            }
            foreach ($daftar as &$d) {
                $d['milik_sendiri'] = (int) $d['id_sekolah'] === $idSekolah;
                unset($d['id_sekolah']);
            }
            unset($d);

            $k['peserta']        = $daftar;
            $k['kategori']       = $kategori[$k['id_kompetisi']] ?? [];
            $k['jumlah_sekolah'] = count(array_unique(array_column($daftar, 'nama_sekolah')));
            $k['karya_sendiri']  = count(array_filter($daftar, fn($d) => $d['milik_sendiri']));
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

        $kompetisi = $db->table('kompetisi k')
            ->select('k.*, a.nama_admin AS pembuat')
            ->select('(SELECT COUNT(*) FROM kompetisi_peserta p WHERE p.id_kompetisi = k.id_kompetisi) AS jumlah_peserta', false)
            ->join('admin a', 'a.id_admin = k.id_admin_pembuat', 'left')
            ->orderBy('k.tanggal_mulai', 'DESC')
            ->get()->getResultArray();

        // Ambil kategori & kriteria semua kompetisi sekaligus
        $ids      = array_column($kompetisi, 'id_kompetisi');
        $kategori = [];
        $kriteria = [];

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
        }

        foreach ($kompetisi as &$k) {
            $k['jumlah_peserta'] = (int) $k['jumlah_peserta'];
            $k['kategori']       = $kategori[$k['id_kompetisi']] ?? [];
            $k['kriteria']       = $kriteria[$k['id_kompetisi']] ?? [];
        }
        unset($k);

        return view('admin/dinas/kompetisi', [
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
            return $this->gagalForm('Terjadi kesalahan saat menyimpan. Coba lagi.');
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

        return redirect()->to('kompetisi')->with(
            'sukses',
            'Tahap "' . esc($kompetisi['nama_kompetisi']) . '" sekarang: ' . self::STATUS[$target] . '.'
        );
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

        return redirect()->to('kompetisi')->with('sukses', 'Kompetisi "' . esc($kompetisi['nama_kompetisi']) . '" berhasil dihapus.');
    }
}
