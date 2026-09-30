<?php

namespace App\Libraries;

/**
 * Sistem Pendukung Keputusan untuk menentukan peringkat karya kompetisi.
 *
 * Semua perhitungan peringkat memanggil Spk::hitung(). Kalau nanti metodenya
 * diganti (misalnya TOPSIS), cukup ubah METODE dan tambahkan fungsinya di sini;
 * halaman juri, data nilai, dan halaman Apresiasi tidak perlu diubah.
 */
class Spk
{
    public const METODE = 'SAW';

    public const NAMA_METODE = [
        'SAW' => 'Simple Additive Weighting (SAW)',
    ];

    /**
     * @param array $matriks [id_alternatif => [id_kriteria => nilai]]  (nilai juri 1–100, sudah dirata-rata)
     * @param array $bobot   [id_kriteria => bobot]                      (skor maksimal kriteria, total 100)
     *
     * @return array ['nilai' => [id_alternatif => 0..1], 'langkah' => rincian perhitungan]
     */
    public static function hitung(array $matriks, array $bobot): array
    {
        switch (self::METODE) {
            case 'SAW':
            default:
                return self::saw($matriks, $bobot);
        }
    }

    /**
     * Simple Additive Weighting.
     * Semua kriteria bersifat "benefit" (semakin tinggi semakin baik):
     *   r_ij = x_ij / max_i(x_ij)
     *   V_i  = Σ_j (w_j × r_ij),  dengan w_j = bobot_j / Σ bobot
     */
    public static function saw(array $matriks, array $bobot): array
    {
        $totalBobot = array_sum($bobot) ?: 1;
        $w = [];
        foreach ($bobot as $j => $b) {
            $w[$j] = $b / $totalBobot;
        }

        // Nilai maksimal setiap kriteria
        $maks = [];
        foreach ($bobot as $j => $_) {
            $kolom    = array_map(fn($baris) => (float) ($baris[$j] ?? 0), $matriks);
            $maks[$j] = $kolom ? max($kolom) : 0;
        }

        $normal = [];
        $nilai  = [];
        foreach ($matriks as $i => $baris) {
            $v = 0.0;
            foreach ($w as $j => $bj) {
                $r              = $maks[$j] > 0 ? ((float) ($baris[$j] ?? 0)) / $maks[$j] : 0;
                $normal[$i][$j] = round($r, 4);
                $v             += $bj * $r;
            }
            $nilai[$i] = round($v, 4);
        }

        return [
            'nilai'   => $nilai,
            'langkah' => [
                'metode'      => self::NAMA_METODE['SAW'],
                'bobot'       => array_map(fn($x) => round($x, 4), $w),
                'maks'        => $maks,
                'matriks'     => $matriks,
                'normalisasi' => $normal,
            ],
        ];
    }
}
