<?php

namespace App\Controllers;

/**
 * Halaman publik informasi lomba (tanpa login).
 * Dipakai sebagai tautan yang dibagikan ke WhatsApp; meta Open Graph
 * membuat banner tampil sebagai pratinjau tautan.
 */
class Clomba extends BaseController
{
    public function detail($id)
    {
        $db        = \Config\Database::connect();
        $kompetisi = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, deskripsi, banner, tanggal_mulai, tanggal_selesai, status, hasil_diumumkan')
            ->where('id_kompetisi', $id)
            ->where('status !=', 'draft') // lomba yang masih disiapkan tidak ditampilkan
            ->get()->getRowArray();

        if (! $kompetisi) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Lomba tidak ditemukan.');
        }

        $kategori = $db->table('kompetisi_kategori')->select('nama_kategori')
            ->where('id_kompetisi', $id)->orderBy('urutan', 'ASC')->get()->getResultArray();

        $kriteria = $db->table('kompetisi_kriteria')->select('nama_kriteria, skor_maks')
            ->where('id_kompetisi', $id)->orderBy('urutan', 'ASC')->get()->getResultArray();

        return view('lomba', [
            'k'        => $kompetisi,
            'kategori' => array_column($kategori, 'nama_kategori'),
            'kriteria' => $kriteria,
            'peserta'  => $db->table('kompetisi_peserta')->where('id_kompetisi', $id)->countAllResults(),
        ]);
    }
}