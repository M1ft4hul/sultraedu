<?php

namespace App\Controllers;

use App\Libraries\Spk;

class Crekap extends BaseController
{
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // Lomba yang punya peserta dan sudah/ sedang dinilai
    private function daftarKompetisi($db): array
    {
        return $db->table('kompetisi k')
            ->select('k.id_kompetisi, k.nama_kompetisi, k.status, k.hasil_diumumkan, k.tanggal_mulai')
            ->whereIn('k.status', ['berlangsung', 'selesai'])
            ->where('EXISTS (SELECT 1 FROM kompetisi_peserta p WHERE p.id_kompetisi = k.id_kompetisi)', null, false)
            ->orderBy("k.status = 'berlangsung'", 'DESC', false)
            ->orderBy('k.tanggal_mulai', 'DESC')
            ->get()->getResultArray();
    }

    // =====================================================
    // HITUNG REKAP SATU LOMBA
    // =====================================================
    private function rekap($db, int $idKompetisi): array
    {
        $kriteria = $db->table('kompetisi_kriteria')->select('id_kriteria, nama_kriteria, skor_maks')
            ->where('id_kompetisi', $idKompetisi)->orderBy('urutan', 'ASC')->get()->getResultArray();
        $bobot = [];
        foreach ($kriteria as $kr) {
            $bobot[(int) $kr['id_kriteria']] = (float) $kr['skor_maks'];
        }
        $jumlahKriteria = count($kriteria);

        $kategori = $db->table('kompetisi_kategori')->select('id_kategori, nama_kategori')
            ->where('id_kompetisi', $idKompetisi)->orderBy('urutan', 'ASC')->get()->getResultArray();

        $peserta = $db->table('kompetisi_peserta p')
            ->select('p.id_peserta, p.id_kategori, p.judul_karya, p.status_validasi, p.catatan_juri, p.peringkat,
                      s.nama_sekolah, g.nama_guru, v.nama_admin AS juri_penolak')
            ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = p.id_guru', 'left')
            ->join('admin v', 'v.id_admin = p.id_juri_validator', 'left')
            ->where('p.id_kompetisi', $idKompetisi)
            ->get()->getResultArray();

        // Semua nilai: [id_peserta][id_juri][id_kriteria] = skor
        $nilai = [];
        $ids   = array_column($peserta, 'id_peserta');
        if ($ids) {
            foreach (
                $db->table('kompetisi_nilai')->select('id_peserta, id_juri, id_kriteria, skor')
                    ->whereIn('id_peserta', $ids)->get()->getResultArray() as $n
            ) {
                $nilai[(int) $n['id_peserta']][(int) $n['id_juri']][(int) $n['id_kriteria']] = (float) $n['skor'];
            }
        }

        // Juri: yang ditugaskan di kategori lomba ini + yang pernah memberi nilai
        $idKategori = array_map('intval', array_column($kategori, 'id_kategori'));
        $tugas      = [];
        if ($idKategori) {
            foreach (
                $db->table('juri_penugasan')->select('id_juri, id_kategori')
                    ->whereIn('id_kategori', $idKategori)->get()->getResultArray() as $t
            ) {
                $tugas[(int) $t['id_kategori']][] = (int) $t['id_juri'];
            }
        }
        $idJuriSemua = [];
        foreach ($tugas as $daftar) {
            $idJuriSemua = array_merge($idJuriSemua, $daftar);
        }
        foreach ($nilai as $perJuri) {
            $idJuriSemua = array_merge($idJuriSemua, array_keys($perJuri));
        }
        $idJuriSemua = array_values(array_unique($idJuriSemua));
        $namaJuri    = $idJuriSemua
            ? array_column($db->table('admin')->select('id_admin, nama_admin')->whereIn('id_admin', $idJuriSemua)->get()->getResultArray(), 'nama_admin', 'id_admin')
            : [];

        $hasil = [];
        foreach ($kategori as $kat) {
            $idKat = (int) $kat['id_kategori'];
            $karya = array_values(array_filter($peserta, fn($p) => (int) $p['id_kategori'] === $idKat));

            // Juri kategori ini
            $juriKat = $tugas[$idKat] ?? [];
            foreach ($karya as $p) {
                $juriKat = array_merge($juriKat, array_keys($nilai[(int) $p['id_peserta']] ?? []));
            }
            $juriKat = array_values(array_unique($juriKat));
            sort($juriKat);

            $dinilai = array_values(array_filter($karya, fn($p) => $p['status_validasi'] !== 'ditolak'));
            $ditolak = array_values(array_filter($karya, fn($p) => $p['status_validasi'] === 'ditolak'));

            // Nilai terbobot per juri & rata-rata per kriteria
            $matriks = [];
            foreach ($dinilai as &$p) {
                $idP = (int) $p['id_peserta'];
                $p['per_juri'] = [];
                foreach ($juriKat as $idJ) {
                    $skor   = $nilai[$idP][$idJ] ?? [];
                    $total  = 0;
                    foreach ($kriteria as $kr) {
                        $total += ($skor[(int) $kr['id_kriteria']] ?? 0) * $kr['skor_maks'] / 100;
                    }
                    $p['per_juri'][$idJ] = [
                        'lengkap' => count($skor) >= $jumlahKriteria && $jumlahKriteria > 0,
                        'terisi'  => count($skor),
                        'total'   => $skor ? round($total, 2) : null,
                        'skor'    => $skor,
                    ];
                }

                // Rata-rata juri per kriteria (dasar perhitungan SAW)
                $rata = [];
                foreach ($kriteria as $kr) {
                    $kolom = [];
                    foreach ($nilai[$idP] ?? [] as $skor) {
                        if (isset($skor[(int) $kr['id_kriteria']])) {
                            $kolom[] = $skor[(int) $kr['id_kriteria']];
                        }
                    }
                    if ($kolom) {
                        $rata[(int) $kr['id_kriteria']] = round(array_sum($kolom) / count($kolom), 2);
                    }
                }
                $p['rata'] = $rata;
                if ($rata && $p['status_validasi'] === 'tervalidasi') {
                    $matriks[$idP] = $rata;
                }
            }
            unset($p);

            // SPK
            $spk = $matriks ? Spk::hitung($matriks, $bobot) : ['nilai' => [], 'langkah' => null];
            foreach ($dinilai as &$p) {
                $v              = $spk['nilai'][(int) $p['id_peserta']] ?? null;
                $p['nilai_spk'] = $v === null ? null : round($v * 100, 2);
            }
            unset($p);

            usort($dinilai, fn($a, $b) => ($b['nilai_spk'] ?? -1) <=> ($a['nilai_spk'] ?? -1));
            $urut = 0;
            foreach ($dinilai as &$p) {
                $p['calon_juara'] = ($p['nilai_spk'] !== null && $urut < Capresiasi::JUMLAH_JUARA) ? ++$urut : null;
            }
            unset($p);

            // Progres setiap juri di kategori ini
            $progres = [];
            foreach ($juriKat as $idJ) {
                $selesai = count(array_filter($dinilai, fn($p) => $p['per_juri'][$idJ]['lengkap'] ?? false));
                $progres[$idJ] = [
                    'nama'       => $namaJuri[$idJ] ?? 'Juri #' . $idJ,
                    'selesai'    => $selesai,
                    'total'      => count($dinilai),
                    'ditugaskan' => in_array($idJ, $tugas[$idKat] ?? [], true),
                ];
            }

            $hasil[] = [
                'id'      => $idKat,
                'nama'    => $kat['nama_kategori'],
                'juri'    => $juriKat,
                'progres' => $progres,
                'karya'   => $dinilai,
                'ditolak' => $ditolak,
                'spk'     => $spk['langkah'],
            ];
        }

        return ['kriteria' => $kriteria, 'kategori' => $hasil, 'namaJuri' => $namaJuri];
    }

    // =====================================================
    // HALAMAN REKAP
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $this->daftarKompetisi($db);
        if (! $kompetisi) {
            return view('admin/dinas/rekap_penilaian', ['kompetisi' => [], 'aktif' => null]);
        }

        $idPilih = (int) $this->request->getGet('kompetisi');
        $aktif   = null;
        foreach ($kompetisi as $k) {
            if ((int) $k['id_kompetisi'] === $idPilih) {
                $aktif = $k;
            }
        }
        $aktif ??= $kompetisi[0];

        return view('admin/dinas/rekap_penilaian', [
            'kompetisi' => $kompetisi,
            'aktif'     => $aktif,
            'metode'    => Spk::NAMA_METODE[Spk::METODE] ?? Spk::METODE,
        ] + $this->rekap($db, (int) $aktif['id_kompetisi']));
    }

    // =====================================================
    // UNDUH REKAP (CSV, bisa dibuka di Excel)
    // =====================================================
    public function export($idKompetisi)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')->where('id_kompetisi', $idKompetisi)->get()->getRowArray();
        if (! $kompetisi) {
            return redirect()->to('rekap-penilaian')->with('gagal', 'Kompetisi tidak ditemukan.');
        }

        $data = $this->rekap($db, (int) $idKompetisi);
        $file = fopen('php://temp', 'r+');
        fwrite($file, "\xEF\xBB\xBF");
        fputcsv($file, ['Rekap Penilaian: ' . $kompetisi['nama_kompetisi']], ';');
        fputcsv($file, ['Metode: ' . (Spk::NAMA_METODE[Spk::METODE] ?? Spk::METODE) . ' — nilai juri skala 1–100, bobot = skor maksimal kriteria'], ';');
        fputcsv($file, [], ';');

        foreach ($data['kategori'] as $kat) {
            fputcsv($file, ['Kategori: ' . $kat['nama']], ';');

            $kepala = ['Peringkat', 'Judul Karya', 'Sekolah', 'Guru'];
            foreach ($kat['juri'] as $idJ) {
                $kepala[] = 'Nilai ' . ($data['namaJuri'][$idJ] ?? 'Juri #' . $idJ);
            }
            foreach ($data['kriteria'] as $kr) {
                $kepala[] = 'Rata-rata ' . $kr['nama_kriteria'] . ' (' . (int) $kr['skor_maks'] . '%)';
            }
            $kepala[] = 'Nilai Akhir SPK';
            $kepala[] = 'Keterangan';
            fputcsv($file, $kepala, ';');

            $no = 0;
            foreach ($kat['karya'] as $p) {
                $baris = [$p['nilai_spk'] !== null ? ++$no : '-', $p['judul_karya'], $p['nama_sekolah'] ?? '-', $p['nama_guru'] ?? '-'];
                foreach ($kat['juri'] as $idJ) {
                    $pj      = $p['per_juri'][$idJ] ?? null;
                    $baris[] = $pj && $pj['total'] !== null ? number_format($pj['total'], 2, ',', '') . ($pj['lengkap'] ? '' : ' (belum lengkap)') : '-';
                }
                foreach ($data['kriteria'] as $kr) {
                    $r       = $p['rata'][(int) $kr['id_kriteria']] ?? null;
                    $baris[] = $r === null ? '-' : number_format($r, 2, ',', '');
                }
                $baris[] = $p['nilai_spk'] === null ? '-' : number_format($p['nilai_spk'], 2, ',', '');
                $baris[] = $p['calon_juara'] ? 'Juara ' . $p['calon_juara'] : ($p['status_validasi'] === 'tervalidasi' ? '' : 'Belum dinilai lengkap');
                fputcsv($file, $baris, ';');
            }
            foreach ($kat['ditolak'] as $p) {
                fputcsv($file, ['-', $p['judul_karya'], $p['nama_sekolah'] ?? '-', $p['nama_guru'] ?? '-', 'Tidak memenuhi syarat: ' . ($p['catatan_juri'] ?? '-')], ';');
            }
            fputcsv($file, [], ';');
        }

        rewind($file);
        $isi = stream_get_contents($file);
        fclose($file);

        $nama = 'rekap-penilaian-' . preg_replace('/[^a-z0-9]+/', '-', strtolower($kompetisi['nama_kompetisi'])) . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $nama . '"')
            ->setBody($isi);
    }
}
