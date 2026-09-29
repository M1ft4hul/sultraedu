<?php

namespace App\Controllers;

class Capresiasi extends BaseController
{
    // Jumlah juara per kategori
    public const JUMLAH_JUARA = 3;

    // Folder penyimpanan piagam (di dalam folder public/)
    private const FOLDER_PIAGAM = 'uploads/piagam';

    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // Peringkat karya tervalidasi berdasarkan rata-rata nilai semua juri
    private function hitungPeringkat(int $idKompetisi): array
    {
        $sql = "SELECT p.id_peserta, p.id_kategori, p.id_sekolah, p.id_guru, p.judul_karya,
                       s.nama_sekolah, g.nama_guru, k.nama_kategori, k.urutan,
                       ROUND(AVG(t.total), 2) AS nilai_akhir,
                       COUNT(t.id_juri)        AS jumlah_juri
                FROM kompetisi_peserta p
                LEFT JOIN (
                    SELECT id_peserta, id_juri, SUM(skor) AS total
                    FROM kompetisi_nilai
                    GROUP BY id_peserta, id_juri
                ) t ON t.id_peserta = p.id_peserta
                LEFT JOIN sekolah s            ON s.id_sekolah  = p.id_sekolah
                LEFT JOIN guru g               ON g.id_guru     = p.id_guru
                LEFT JOIN kompetisi_kategori k ON k.id_kategori = p.id_kategori
                WHERE p.id_kompetisi = ? AND p.status_validasi = 'tervalidasi'
                GROUP BY p.id_peserta, p.id_kategori, p.id_sekolah, p.id_guru, p.judul_karya,
                         s.nama_sekolah, g.nama_guru, k.nama_kategori, k.urutan
                ORDER BY k.urutan ASC, nilai_akhir DESC";

        $baris = \Config\Database::connect()->query($sql, [$idKompetisi])->getResultArray();

        // Kelompokkan per kategori & tandai calon juara
        $hasil = [];
        foreach ($baris as $b) {
            $kunci = $b['id_kategori'] ?? 0;
            $hasil[$kunci]['nama']      = $b['nama_kategori'] ?? 'Tanpa kategori';
            $hasil[$kunci]['peserta'][] = $b;
        }
        foreach ($hasil as &$kat) {
            $urut = 0;
            foreach ($kat['peserta'] as &$p) {
                // Hanya karya yang sudah dinilai yang bisa jadi juara
                $p['calon_juara'] = ($p['jumlah_juri'] > 0 && $urut < self::JUMLAH_JUARA) ? ++$urut : null;
            }
            unset($p);
        }
        unset($kat);

        return $hasil;
    }

    // =====================================================
    // HALAMAN APRESIASI
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
    // ADMIN SEKOLAH: lihat juara & status piagam
    // =====================================================
    private function indexSekolah()
    {
        $db        = \Config\Database::connect();
        $idSekolah = (int) session()->get('id_sekolah');

        // Semua karya sekolah ini di seluruh kompetisi (kecuali yang masih draft)
        $rows = $db->table('kompetisi_peserta p')
            ->select('p.id_peserta, p.judul_karya, p.deskripsi_karya, p.link_video, p.peringkat, p.nilai, p.status_validasi,
                      k.id_kompetisi, k.nama_kompetisi, k.tanggal_mulai, k.status AS status_kompetisi,
                      k.hasil_diumumkan, k.tanggal_pengumuman,
                      kk.nama_kategori, g.nama_guru, g.nip, g.mapel,
                      a.id_apresiasi, a.bukti_file, a.updated_at AS tanggal_piagam')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->join('kompetisi_kategori kk', 'kk.id_kategori = p.id_kategori', 'left')
            ->join('guru g', 'g.id_guru = p.id_guru', 'left')
            ->join('apresiasi a', 'a.id_peserta = p.id_peserta', 'left')
            ->where('p.id_sekolah', $idSekolah)
            ->where('k.status !=', 'draft')
            ->orderBy('k.tanggal_mulai', 'DESC')
            ->orderBy('p.peringkat IS NULL', 'ASC', false)
            ->orderBy('p.peringkat', 'ASC')
            ->get()->getResultArray();

        // Rincian nilai per kriteria (rata-rata semua juri), hanya untuk hasil yang sudah diumumkan
        $idDiumumkan = array_column(array_filter($rows, fn ($r) => $r['hasil_diumumkan']), 'id_peserta');
        $rincian     = [];

        if ($idDiumumkan) {
            $nilaiKriteria = $db->table('kompetisi_nilai n')
                ->select('n.id_peserta, kr.nama_kriteria, kr.skor_maks, kr.urutan, ROUND(AVG(n.skor), 2) AS skor')
                ->join('kompetisi_kriteria kr', 'kr.id_kriteria = n.id_kriteria')
                ->whereIn('n.id_peserta', $idDiumumkan)
                ->groupBy('n.id_peserta, kr.id_kriteria, kr.nama_kriteria, kr.skor_maks, kr.urutan')
                ->orderBy('kr.urutan', 'ASC')
                ->get()->getResultArray();

            foreach ($nilaiKriteria as $n) {
                $rincian[$n['id_peserta']][] = [
                    'kriteria'  => $n['nama_kriteria'],
                    'skor'      => (float) $n['skor'],
                    'skor_maks' => (int) $n['skor_maks'],
                ];
            }
        }

        // Nilai & peringkat disembunyikan sebelum hasil diumumkan
        foreach ($rows as &$r) {
            if (! $r['hasil_diumumkan']) {
                $r['peringkat'] = null;
                $r['nilai']     = null;
            }
            $r['rincian'] = $rincian[$r['id_peserta']] ?? [];
        }
        unset($r);

        // ---------- Filter tahun & kompetisi ----------
        foreach ($rows as &$r) {
            $r['tahun'] = date('Y', strtotime($r['tanggal_mulai']));
        }
        unset($r);

        $daftarTahun = array_values(array_unique(array_column($rows, 'tahun')));
        rsort($daftarTahun);

        // Default: tahun terbaru yang punya data
        $tahun = (string) ($this->request->getGet('tahun') ?? ($daftarTahun[0] ?? 'semua'));
        if ($tahun !== 'semua' && ! in_array($tahun, $daftarTahun, true)) {
            $tahun = $daftarTahun[0] ?? 'semua';
        }

        // Pilihan kompetisi menyesuaikan tahun
        $kompetisi = [];
        foreach ($rows as $r) {
            if ($tahun === 'semua' || $r['tahun'] === $tahun) {
                $kompetisi[$r['id_kompetisi']] = $r['nama_kompetisi'];
            }
        }

        $idKompetisi = (string) $this->request->getGet('kompetisi');
        if (! isset($kompetisi[$idKompetisi])) {
            $idKompetisi = '';
        }

        $rows = array_values(array_filter($rows, fn ($r) =>
            ($tahun === 'semua' || $r['tahun'] === $tahun)
            && ($idKompetisi === '' || (string) $r['id_kompetisi'] === $idKompetisi)
        ));

        // Kartu prestasi = karya yang menjadi juara
        $prestasi = array_values(array_filter($rows, fn ($r) => $r['id_apresiasi'] !== null));

        return view('admin/sekolah/apresiasi', [
            'prestasi'    => $prestasi,
            'riwayat'     => $rows,
            'kompetisi'   => $kompetisi,
            'daftarTahun' => $daftarTahun,
            'filter'      => ['tahun' => $tahun, 'kompetisi' => $idKompetisi],
            'ringkas'     => [
                'ikut'     => count($rows),
                'juara'    => count($prestasi),
                'tersedia' => count(array_filter($prestasi, fn ($p) => ! empty($p['bukti_file']))),
            ],
        ]);
    }

    // =====================================================
    // ADMIN DINAS: umumkan juara & unggah piagam
    // =====================================================
    private function indexDinas()
    {
        $db = \Config\Database::connect();

        // Hanya kompetisi yang sudah selesai (penjurian tuntas)
        $daftar = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, tanggal_mulai, tanggal_selesai, hasil_diumumkan, tanggal_pengumuman')
            ->where('status', 'selesai')
            ->orderBy('tanggal_selesai', 'DESC')
            ->get()->getResultArray();

        $idDipilih = (int) ($this->request->getGet('kompetisi') ?: ($daftar[0]['id_kompetisi'] ?? 0));
        $dipilih   = null;
        foreach ($daftar as $d) {
            if ((int) $d['id_kompetisi'] === $idDipilih) {
                $dipilih = $d;
            }
        }

        $data = [
            'daftar'   => $daftar,
            'dipilih'  => $dipilih,
            'kategori' => [],
            'pemenang' => [],
        ];

        if ($dipilih) {
            if ($dipilih['hasil_diumumkan']) {
                // Sudah diumumkan: ambil pemenang beserta piagamnya
                $pemenang = $db->table('apresiasi a')
                    ->select('a.id_apresiasi, a.jenis_apresiasi, a.bukti_file, a.tanggal_verifikasi,
                              p.judul_karya, p.peringkat, p.nilai,
                              s.nama_sekolah, s.kabupaten_kota, g.nama_guru,
                              k.nama_kategori, k.urutan')
                    ->join('kompetisi_peserta p', 'p.id_peserta = a.id_peserta')
                    ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
                    ->join('guru g', 'g.id_guru = p.id_guru', 'left')
                    ->join('kompetisi_kategori k', 'k.id_kategori = p.id_kategori', 'left')
                    ->where('p.id_kompetisi', $idDipilih)
                    ->orderBy('k.urutan', 'ASC')
                    ->orderBy('p.peringkat', 'ASC')
                    ->get()->getResultArray();

                foreach ($pemenang as $w) {
                    $data['pemenang'][$w['nama_kategori'] ?? 'Tanpa kategori'][] = $w;
                }
            } else {
                // Belum diumumkan: tampilkan pratinjau peringkat
                $data['kategori'] = $this->hitungPeringkat($idDipilih);
            }
        }

        return view('admin/dinas/apresiasi', $data);
    }

    // =====================================================
    // UMUMKAN HASIL (tetapkan juara & buat data apresiasi)
    // =====================================================
    public function umumkan($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $id)->get()->getRowArray();
        $kembali   = site_url('apresiasi') . '?kompetisi=' . (int) $id;

        if (! $kompetisi || $kompetisi['status'] !== 'selesai') {
            return redirect()->to($kembali)->with('gagal', 'Hasil hanya bisa diumumkan untuk kompetisi yang sudah selesai.');
        }
        if ($kompetisi['hasil_diumumkan']) {
            return redirect()->to($kembali)->with('gagal', 'Hasil kompetisi ini sudah diumumkan.');
        }

        $kategori = $this->hitungPeringkat((int) $id);
        $juara    = [];
        foreach ($kategori as $kat) {
            foreach ($kat['peserta'] as $p) {
                if ($p['calon_juara']) {
                    $juara[] = $p + ['kategori' => $kat['nama']];
                }
            }
        }

        if (empty($juara)) {
            return redirect()->to($kembali)->with('gagal', 'Belum ada karya yang dinilai juri, jadi belum ada juara yang bisa diumumkan.');
        }

        $sekarang = date('Y-m-d H:i:s');
        $db->transStart();

        // Simpan nilai akhir semua peserta & kosongkan peringkat lama
        foreach ($kategori as $kat) {
            foreach ($kat['peserta'] as $p) {
                $db->table('kompetisi_peserta')->where('id_peserta', $p['id_peserta'])->update([
                    'nilai'      => $p['nilai_akhir'],
                    'peringkat'  => null,
                    'updated_at' => $sekarang,
                ]);
            }
        }

        // Tetapkan juara & buat data apresiasinya
        foreach ($juara as $j) {
            $db->table('kompetisi_peserta')->where('id_peserta', $j['id_peserta'])
                ->update(['peringkat' => $j['calon_juara']]);

            $db->table('apresiasi')->insert([
                'id_peserta'         => $j['id_peserta'],
                'id_sekolah'         => $j['id_sekolah'],
                'id_guru'            => $j['id_guru'],
                'jenis_apresiasi'    => mb_substr('Juara ' . $j['calon_juara'] . ' – ' . $j['kategori'], 0, 150),
                'deskripsi'          => $j['judul_karya'],
                'status_verifikasi'  => 'diverifikasi',
                'id_verifikator'     => session()->get('user_id'),
                'tanggal_verifikasi' => $sekarang,
                'created_at'         => $sekarang,
                'updated_at'         => $sekarang,
            ]);
        }

        $db->table('kompetisi')->where('id_kompetisi', $id)->update([
            'hasil_diumumkan'    => 1,
            'tanggal_pengumuman' => $sekarang,
            'updated_at'         => $sekarang,
        ]);

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to($kembali)->with('gagal', 'Terjadi kesalahan. Hasil belum diumumkan.');
        }

        return redirect()->to($kembali)->with(
            'sukses',
            'Hasil "' . esc($kompetisi['nama_kompetisi']) . '" berhasil diumumkan. ' . count($juara) . ' juara tercatat. Silakan unggah piagamnya.'
        );
    }

    // =====================================================
    // UNGGAH / GANTI PIAGAM
    // =====================================================
    public function piagam($idApresiasi)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $apresiasi = $db->table('apresiasi a')
            ->select('a.id_apresiasi, a.bukti_file, a.jenis_apresiasi, p.id_kompetisi')
            ->join('kompetisi_peserta p', 'p.id_peserta = a.id_peserta')
            ->where('a.id_apresiasi', $idApresiasi)
            ->get()->getRowArray();

        if (! $apresiasi) {
            return redirect()->to('apresiasi')->with('gagal', 'Data pemenang tidak ditemukan.');
        }

        $kembali = site_url('apresiasi') . '?kompetisi=' . $apresiasi['id_kompetisi'];

        $valid = $this->validate([
            'piagam' => 'uploaded[piagam]|max_size[piagam,2048]|ext_in[piagam,pdf,jpg,jpeg,png]|mime_in[piagam,application/pdf,image/jpeg,image/png]',
        ], [
            'piagam' => [
                'uploaded' => 'Pilih file piagam terlebih dahulu.',
                'max_size' => 'Ukuran file piagam maksimal 2 MB.',
                'ext_in'   => 'Format piagam harus PDF, JPG, atau PNG.',
                'mime_in'  => 'Format piagam harus PDF, JPG, atau PNG.',
            ],
        ]);

        if (! $valid) {
            return redirect()->to($kembali)->with('gagal', implode(' ', $this->validator->getErrors()));
        }

        $file   = $this->request->getFile('piagam');
        $folder = FCPATH . self::FOLDER_PIAGAM;
        if (! is_dir($folder)) {
            mkdir($folder, 0755, true);
        }

        $namaBaru = $file->getRandomName();
        $file->move($folder, $namaBaru);

        // Hapus file lama kalau piagam diganti
        if ($apresiasi['bukti_file'] && is_file(FCPATH . $apresiasi['bukti_file'])) {
            unlink(FCPATH . $apresiasi['bukti_file']);
        }

        $db->table('apresiasi')->where('id_apresiasi', $idApresiasi)->update([
            'bukti_file' => self::FOLDER_PIAGAM . '/' . $namaBaru,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        return redirect()->to($kembali)->with('sukses', 'Piagam "' . esc($apresiasi['jenis_apresiasi']) . '" berhasil diunggah.');
    }
}