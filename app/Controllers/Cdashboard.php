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
        $data['pengumuman'] = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi, status, tanggal_selesai, hasil_diumumkan, tanggal_pengumuman')
            ->groupStart()
            ->where('status', 'pendaftaran')
            ->orWhere('hasil_diumumkan', 1)
            ->groupEnd()
            ->orderBy('COALESCE(tanggal_pengumuman, updated_at)', 'DESC', false)
            ->limit(3)
            ->get()->getResultArray();

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
    // ADMIN SEKOLAH (nanti dilengkapi)
    // =====================================================
    private function dashboardSekolah()
    {
        return view('admin/sekolah/dashboard');
    }

    // =====================================================
    // TIM JURI (nanti dilengkapi)
    // =====================================================
    private function dashboardJuri()
    {
        return view('admin/juri/dashboard');
    }

    // =====================================================
    // GURU (nanti dilengkapi)
    // =====================================================
    private function dashboardGuru()
    {
        return view('admin/guru/dashboard');
    }
}
