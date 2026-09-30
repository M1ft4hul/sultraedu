<?php

namespace App\Controllers;

class Cpenilaian extends BaseController
{
    // Nilai juri untuk setiap kriteria berskala 1–100
    public const SKOR_MIN = 1;
    public const SKOR_MAKS = 100;

    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'tim_juri';
    }

    private function idJuri(): int
    {
        return (int) session()->get('user_id');
    }

    // Kategori yang ditugaskan kepada juri ini
    private function kategoriTugas($db): array
    {
        return array_map('intval', array_column(
            $db->table('juri_penugasan')->select('id_kategori')->where('id_juri', $this->idJuri())->get()->getResultArray(),
            'id_kategori'
        ));
    }

    // Karya yang boleh dinilai juri ini (lomba sedang penjurian + kategori tugasnya)
    private function karyaTugas($db, $idPeserta): ?array
    {
        $kategori = $this->kategoriTugas($db);
        if (! $kategori) {
            return null;
        }

        return $db->table('kompetisi_peserta p')
            ->select('p.*, k.status AS status_kompetisi, k.nama_kompetisi')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->where('p.id_peserta', $idPeserta)
            ->where('k.status', 'berlangsung')
            ->whereIn('p.id_kategori', $kategori)
            ->get()->getRowArray();
    }

    // =====================================================
    // HALAMAN PENILAIAN
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db       = \Config\Database::connect();
        $idJuri   = $this->idJuri();
        $kategori = $this->kategoriTugas($db);

        // Lomba penjurian yang memuat kategori tugas juri ini
        $kompetisi = [];
        if ($kategori) {
            $kompetisi = $db->table('kompetisi k')
                ->select('k.id_kompetisi, k.nama_kompetisi, k.deskripsi')
                ->join('kompetisi_kategori kk', 'kk.id_kompetisi = k.id_kompetisi')
                ->where('k.status', 'berlangsung')
                ->whereIn('kk.id_kategori', $kategori)
                ->groupBy('k.id_kompetisi, k.nama_kompetisi, k.deskripsi')
                ->orderBy('k.tanggal_selesai', 'ASC')
                ->get()->getResultArray();
        }

        $data = ['kompetisi' => $kompetisi, 'adaTugas' => ! empty($kategori), 'aktif' => null];
        if (! $kompetisi) {
            return view('admin/juri/penilaian', $data);
        }

        // Lomba yang dipilih (default: yang pertama)
        $idKompetisi = (int) $this->request->getGet('kompetisi');
        $aktif       = null;
        foreach ($kompetisi as $k) {
            if ((int) $k['id_kompetisi'] === $idKompetisi) {
                $aktif = $k;
            }
        }
        $aktif ??= $kompetisi[0];
        $idKompetisi = (int) $aktif['id_kompetisi'];

        // Kriteria (bobot = skor maksimal)
        $kriteria = $db->table('kompetisi_kriteria')
            ->select('id_kriteria, nama_kriteria, keterangan, skor_maks')
            ->where('id_kompetisi', $idKompetisi)->orderBy('urutan', 'ASC')
            ->get()->getResultArray();

        // Kategori tugas di lomba ini
        $kategoriLomba = $db->table('kompetisi_kategori')->select('id_kategori, nama_kategori')
            ->where('id_kompetisi', $idKompetisi)->whereIn('id_kategori', $kategori)
            ->orderBy('urutan', 'ASC')->get()->getResultArray();

        // Karya di kategori tugas (identitas sekolah & guru tidak ditampilkan ke juri)
        $karya = $db->table('kompetisi_peserta')
            ->select('id_peserta, id_kategori, judul_karya, deskripsi_karya, link_video, status_validasi, id_juri_validator, catatan_juri, created_at')
            ->where('id_kompetisi', $idKompetisi)
            ->whereIn('id_kategori', array_column($kategoriLomba, 'id_kategori') ?: [0])
            ->orderBy('created_at', 'ASC')
            ->get()->getResultArray();

        // Nilai yang sudah diberikan juri ini
        $nilaiku = [];
        $ids     = array_column($karya, 'id_peserta');
        if ($ids) {
            foreach (
                $db->table('kompetisi_nilai')->select('id_peserta, id_kriteria, skor')
                    ->where('id_juri', $idJuri)->whereIn('id_peserta', $ids)->get()->getResultArray() as $n
            ) {
                $nilaiku[$n['id_peserta']][$n['id_kriteria']] = (float) $n['skor'];
            }
        }

        $jumlahKriteria = count($kriteria);
        $namaKategori   = array_column($kategoriLomba, 'nama_kategori', 'id_kategori');
        foreach ($karya as $i => &$k) {
            $k['kode']          = 'K-' . str_pad((string) ($i + 1), 3, '0', STR_PAD_LEFT); // kode anonim
            $k['nama_kategori'] = $namaKategori[$k['id_kategori']] ?? '-';
            $k['nilai']         = $nilaiku[$k['id_peserta']] ?? [];
            $terisi             = count($k['nilai']);
            $k['tolak_saya']    = $k['status_validasi'] === 'ditolak' && (int) $k['id_juri_validator'] === $idJuri;

            if ($k['status_validasi'] === 'ditolak') {
                $k['keadaan'] = 'ditolak';
            } elseif ($terisi >= $jumlahKriteria && $jumlahKriteria > 0) {
                $k['keadaan'] = 'lengkap';
            } elseif ($terisi > 0) {
                $k['keadaan'] = 'sebagian';
            } else {
                $k['keadaan'] = 'belum';
            }

            // Nilai terbobot versi juri ini (hanya gambaran; nilai akhir resmi dihitung SPK)
            $k['nilai_bobot'] = null;
            if ($k['keadaan'] === 'lengkap') {
                $total = 0;
                foreach ($kriteria as $kr) {
                    $total += ($k['nilai'][$kr['id_kriteria']] ?? 0) * $kr['skor_maks'] / 100;
                }
                $k['nilai_bobot'] = round($total, 2);
            }

            // Juri tidak perlu melihat siapa validatornya
            unset($k['id_juri_validator']);
        }
        unset($k);

        // Karya yang dibuka (?karya=)
        $idKarya = (int) $this->request->getGet('karya');
        $dipilih = null;
        foreach ($karya as $k) {
            if ((int) $k['id_peserta'] === $idKarya) {
                $dipilih = $k;
            }
        }

        $hitung = fn($keadaan) => count(array_filter($karya, fn($k) => $k['keadaan'] === $keadaan));

        return view('admin/juri/penilaian', array_merge($data, [
            'aktif'         => $aktif,
            'kriteria'      => $kriteria,
            'kategoriLomba' => $kategoriLomba,
            'karya'         => $karya,
            'dipilih'       => $dipilih,
            'ringkas'       => [
                'total'    => count($karya),
                'lengkap'  => $hitung('lengkap'),
                'sebagian' => $hitung('sebagian'),
                'belum'    => $hitung('belum'),
                'ditolak'  => $hitung('ditolak'),
            ],
        ]));
    }

    // Alamat kembali ke karya yang sama
    private function kembali($karya)
    {
        return redirect()->to(site_url('penilaian') . '?kompetisi=' . $karya['id_kompetisi'] . '&karya=' . $karya['id_peserta']);
    }

    // =====================================================
    // SIMPAN NILAI (boleh sebagian, lengkap = tervalidasi)
    // =====================================================
    public function simpan($idPeserta)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $karya = $this->karyaTugas($db, $idPeserta);
        if (! $karya) {
            return redirect()->to('penilaian')->with('gagal', 'Karya ini tidak termasuk tugas penilaian Anda, atau tahap penjurian sudah ditutup.');
        }
        if ($karya['status_validasi'] === 'ditolak') {
            return $this->kembali($karya)->with('gagal', 'Karya ini ditandai tidak memenuhi syarat, sehingga tidak bisa dinilai.');
        }

        $kriteria = array_map('intval', array_column(
            $db->table('kompetisi_kriteria')->select('id_kriteria')->where('id_kompetisi', $karya['id_kompetisi'])->get()->getResultArray(),
            'id_kriteria'
        ));

        $masukan = (array) $this->request->getPost('skor');
        $simpan  = [];
        foreach ($kriteria as $idKriteria) {
            $nilai = trim((string) ($masukan[$idKriteria] ?? ''));
            if ($nilai === '') {
                continue; // boleh dikosongkan dulu (simpan sebagian)
            }
            $nilai = (float) str_replace(',', '.', $nilai);
            if ($nilai < self::SKOR_MIN || $nilai > self::SKOR_MAKS) {
                return $this->kembali($karya)->with('gagal', 'Nilai setiap kriteria harus antara ' . self::SKOR_MIN . ' dan ' . self::SKOR_MAKS . '.');
            }
            $simpan[$idKriteria] = round($nilai, 2);
        }

        if (! $simpan) {
            return $this->kembali($karya)->with('gagal', 'Isi minimal satu nilai kriteria.');
        }

        $idJuri   = $this->idJuri();
        $sekarang = date('Y-m-d H:i:s');

        $db->transStart();
        foreach ($simpan as $idKriteria => $skor) {
            $ada = $db->table('kompetisi_nilai')->where([
                'id_peserta' => $karya['id_peserta'],
                'id_kriteria' => $idKriteria,
                'id_juri' => $idJuri,
            ])->countAllResults();

            if ($ada) {
                $db->table('kompetisi_nilai')
                    ->where(['id_peserta' => $karya['id_peserta'], 'id_kriteria' => $idKriteria, 'id_juri' => $idJuri])
                    ->update(['skor' => $skor, 'updated_at' => $sekarang]);
            } else {
                $db->table('kompetisi_nilai')->insert([
                    'id_peserta'  => $karya['id_peserta'],
                    'id_kriteria' => $idKriteria,
                    'id_juri'     => $idJuri,
                    'skor'        => $skor,
                    'created_at'  => $sekarang,
                    'updated_at'  => $sekarang,
                ]);
            }
        }

        // Lengkap di semua kriteria → karya tervalidasi (memenuhi syarat)
        $terisi  = $db->table('kompetisi_nilai')->where('id_peserta', $karya['id_peserta'])->where('id_juri', $idJuri)->countAllResults();
        $lengkap = $terisi >= count($kriteria);
        if ($lengkap && $karya['status_validasi'] !== 'tervalidasi') {
            $db->table('kompetisi_peserta')->where('id_peserta', $karya['id_peserta'])->update([
                'status_validasi'   => 'tervalidasi',
                'id_juri_validator' => $idJuri,
                'updated_at'        => $sekarang,
            ]);
        }
        $db->transComplete();

        return $this->kembali($karya)->with('sukses', $lengkap
            ? 'Penilaian "' . esc($karya['judul_karya']) . '" lengkap dan tersimpan. Karya dinyatakan memenuhi syarat.'
            : 'Nilai sementara tersimpan. Lengkapi semua kriteria untuk menyelesaikan penilaian.');
    }

    // =====================================================
    // TANDAI TIDAK MEMENUHI SYARAT
    // =====================================================
    public function tolak($idPeserta)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $karya = $this->karyaTugas($db, $idPeserta);
        if (! $karya) {
            return redirect()->to('penilaian')->with('gagal', 'Karya ini tidak termasuk tugas penilaian Anda, atau tahap penjurian sudah ditutup.');
        }

        $catatan = trim((string) $this->request->getPost('catatan_juri'));
        if (mb_strlen($catatan) < 10) {
            return $this->kembali($karya)->with('gagal', 'Tuliskan alasan karya tidak memenuhi syarat (minimal 10 karakter).');
        }

        $db->table('kompetisi_peserta')->where('id_peserta', $karya['id_peserta'])->update([
            'status_validasi'   => 'ditolak',
            'id_juri_validator' => $this->idJuri(),
            'catatan_juri'      => $catatan,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        return $this->kembali($karya)->with('sukses', 'Karya "' . esc($karya['judul_karya']) . '" ditandai tidak memenuhi syarat dan tidak ikut perhitungan juara.');
    }

    // =====================================================
    // BATALKAN PENOLAKAN (hanya oleh juri yang menolak)
    // =====================================================
    public function batalTolak($idPeserta)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $karya = $this->karyaTugas($db, $idPeserta);
        if (! $karya || $karya['status_validasi'] !== 'ditolak') {
            return redirect()->to('penilaian')->with('gagal', 'Karya tidak ditemukan atau tidak sedang ditolak.');
        }
        if ((int) $karya['id_juri_validator'] !== $this->idJuri()) {
            return $this->kembali($karya)->with('gagal', 'Penolakan hanya bisa dibatalkan oleh juri yang menolaknya.');
        }

        // Kembali tervalidasi kalau sudah ada juri yang menilai lengkap, selain itu menunggu
        $jumlahKriteria = $db->table('kompetisi_kriteria')->where('id_kompetisi', $karya['id_kompetisi'])->countAllResults();
        $adaLengkap = $db->query(
            'SELECT 1 FROM kompetisi_nilai WHERE id_peserta = ? GROUP BY id_juri HAVING COUNT(*) >= ? LIMIT 1',
            [$karya['id_peserta'], $jumlahKriteria]
        )->getRowArray();

        $db->table('kompetisi_peserta')->where('id_peserta', $karya['id_peserta'])->update([
            'status_validasi'   => $adaLengkap ? 'tervalidasi' : 'menunggu',
            'id_juri_validator' => null,
            'catatan_juri'      => null,
            'updated_at'        => date('Y-m-d H:i:s'),
        ]);

        return $this->kembali($karya)->with('sukses', 'Penolakan dibatalkan. Karya bisa dinilai kembali.');
    }
}
