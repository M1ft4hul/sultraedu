<?php

namespace App\Controllers;

class Cdashboard extends BaseController
{
    public function index()
    {
        if (! session()->get('logged_in')) {
            return redirect()->to('login');
        }
        $db = \Config\Database::connect();
        $data['stat'] = [
            'sekolah'   => $db->table('sekolah')->where('status', 'aktif')->countAllResults(),
            'praktik'   => $db->table('praktik_baik')->where('status_verifikasi_dinas', 'disetujui')->countAllResults(),
            'inovasi'   => $db->table('bank_inovasi')->where('status_verifikasi', 'disetujui')->countAllResults(),
            'kompetisi' => $db->table('kompetisi_peserta')->countAllResults(),
            'suara'     => $db->table('suara')->countAllResults(),
        ];

        $data['pengumuman'] = [];

        // Antrean praktik baik yang menunggu validasi Dinas
        $antrean = $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.tanggal_upload, s.nama_sekolah, s.kabupaten_kota, g.nama_guru')
            ->join('sekolah s', 's.id_sekolah = pb.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->where('pb.status_verifikasi_sekolah', 'disetujui')
            ->where('pb.status_verifikasi_dinas', 'menunggu');

        $data['totalAntrean'] = $antrean->countAllResults(false);
        $data['antrean']      = $antrean->orderBy('pb.tanggal_upload', 'ASC')->limit(5)->get()->getResultArray();

        $role = session()->get('role');

        $halaman = [
            'admin_pusat'   => 'admin/dinas/dashboard',
            'admin_sekolah' => 'admin/sekolah/dashboard',
            'tim_juri'      => 'admin/juri/dashboard',
            'guru'          => 'admin/guru/dashboard',
        ];

        // Role tidak dikenal: keluarkan user dan beri pesan di halaman login
        if (! isset($halaman[$role])) {
            session()->remove(['logged_in', 'user_id', 'user_type', 'nama', 'username', 'role', 'id_sekolah']);

            return redirect()->to('login')
                ->with('error', 'Hak akses akun Anda tidak dikenali. Silakan hubungi Admin Dinas.');
        }

        return view($halaman[$role], $data);
    }
}
