<?php

namespace App\Controllers;

class CpraktikBaik extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }

        $db = \Config\Database::connect();

        // Admin Dinas hanya melihat praktik baik yang sudah lolos verifikasi sekolah
        $data['praktik'] = $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.kategori, pb.tanggal_upload, pb.status_verifikasi_dinas, s.nama_sekolah, s.kabupaten_kota, g.nama_guru')
            ->join('sekolah s', 's.id_sekolah = pb.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->where('pb.status_verifikasi_sekolah', 'disetujui')
            ->orderBy('pb.tanggal_upload', 'DESC')
            ->get()->getResultArray();

        return view('admin/dinas/praktek_baik', $data);
    }
}
