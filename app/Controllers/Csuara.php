<?php

namespace App\Controllers;

class Csuara extends BaseController
{
    public const KATEGORI = [
        'saran'      => 'Saran',
        'keluhan'    => 'Keluhan',
        'pertanyaan' => 'Pertanyaan',
        'lainnya'    => 'Lainnya',
    ];

    public const STATUS = [
        'belum_ditindak' => 'Belum Dibalas',
        'diproses'       => 'Sedang Diproses',
        'selesai'        => 'Selesai',
    ];

    // Rekap, balas, dan unduh khusus Admin Dinas
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    private function ambilFilter(): array
    {
        return [
            'q'        => trim((string) $this->request->getGet('q')),
            'kategori' => (string) $this->request->getGet('kategori'),
            'kab'      => (string) $this->request->getGet('kab'),
            'sekolah'  => (string) $this->request->getGet('sekolah'),
            'status'   => (string) $this->request->getGet('status'),
        ];
    }

    // Query daftar SUARA lengkap dengan identitas pengirim
    private function querySuara(array $filter): array
    {
        $builder = \Config\Database::connect()->table('suara sr')
            ->select('sr.id_suara, sr.kategori, sr.isi_suara, sr.tanggal_kirim,
                      sr.nama_pengirim, sr.email_pengirim,
                      sr.status_tindak_lanjut, sr.tanggapan, sr.tanggal_tindak_lanjut,
                      g.nama_guru, g.nip, ap.nama_admin AS nama_admin_pengirim,
                      pd.nama_admin AS nama_penindak,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota')
            ->join('guru g', 'g.id_guru = sr.id_guru', 'left')
            ->join('admin ap', 'ap.id_admin = sr.id_admin_pengirim', 'left')
            ->join('admin pd', 'pd.id_admin = sr.id_admin_penindak', 'left')
            ->join('sekolah s', 's.id_sekolah = sr.id_sekolah', 'left');

        if ($filter['q'] !== '') {
            $builder->groupStart()
                ->like('sr.isi_suara', $filter['q'])
                ->orLike('sr.nama_pengirim', $filter['q'])
                ->orLike('g.nama_guru', $filter['q'])
                ->orLike('s.nama_sekolah', $filter['q'])
                ->groupEnd();
        }
        if (array_key_exists($filter['kategori'], self::KATEGORI)) {
            $builder->where('sr.kategori', $filter['kategori']);
        }
        if ($filter['kab'] !== '') {
            $builder->where('s.kabupaten_kota', $filter['kab']);
        }
        if ($filter['sekolah'] !== '') {
            $builder->where('sr.id_sekolah', $filter['sekolah']);
        }
        if (array_key_exists($filter['status'], self::STATUS)) {
            $builder->where('sr.status_tindak_lanjut', $filter['status']);
        }

        $data = $builder->orderBy('sr.tanggal_kirim', 'DESC')->get()->getResultArray();

        // Nama tampilan: pakai nama guru kalau pengirimnya guru terdaftar
        foreach ($data as &$d) {
            if ($d['nama_guru']) {
                $d['pengirim']       = $d['nama_guru'];
                $d['jenis_pengirim'] = 'Guru';
            } elseif ($d['nama_admin_pengirim']) {
                $d['pengirim']       = $d['nama_admin_pengirim'];
                $d['jenis_pengirim'] = 'Admin Sekolah';
            } else {
                $d['pengirim']       = $d['nama_pengirim'] ?: 'Tanpa nama';
                $d['jenis_pengirim'] = 'Umum';
            }
        }
        unset($d);

        return $data;
    }

    // =====================================================
    // DAFTAR SUARA
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
    // ADMIN SEKOLAH: kirim SUARA & lihat riwayat kiriman sekolah
    // =====================================================
    private function indexSekolah()
    {
        $riwayat = \Config\Database::connect()->table('suara sr')
            ->select('sr.id_suara, sr.kategori, sr.isi_suara, sr.tanggal_kirim, a.nama_admin AS pengirim,
                      sr.status_tindak_lanjut, sr.tanggapan, sr.tanggal_tindak_lanjut')
            ->join('admin a', 'a.id_admin = sr.id_admin_pengirim')
            ->where('sr.id_sekolah', session()->get('id_sekolah'))
            ->where('sr.id_admin_pengirim IS NOT NULL')
            ->orderBy('sr.tanggal_kirim', 'DESC')
            ->get()->getResultArray();

        return view('admin/sekolah/suara', [
            'riwayat'  => $riwayat,
            'kategori' => self::KATEGORI,
            'status'   => self::STATUS,
        ]);
    }

    // =====================================================
    // GURU: kirim SUARA & lihat riwayat kiriman sendiri
    // =====================================================
    private function indexGuru()
    {
        $riwayat = \Config\Database::connect()->table('suara')
            ->select('id_suara, kategori, isi_suara, tanggal_kirim, status_tindak_lanjut, tanggapan, tanggal_tindak_lanjut')
            ->where('id_guru', session()->get('user_id'))
            ->orderBy('tanggal_kirim', 'DESC')
            ->get()->getResultArray();

        return view('admin/guru/suara', [
            'riwayat'  => $riwayat,
            'kategori' => self::KATEGORI,
            'status'   => self::STATUS,
        ]);
    }

    // =====================================================
    // KIRIM SUARA (Admin Sekolah & Guru)
    // =====================================================
    public function kirim()
    {
        $role = session()->get('role');
        if (! session()->get('logged_in') || ! in_array($role, ['admin_sekolah', 'guru'], true)) {
            return redirect()->to('dashboard');
        }

        $input = [
            'kategori'  => (string) $this->request->getPost('kategori'),
            'isi_suara' => trim((string) $this->request->getPost('isi_suara')),
        ];

        $valid = $this->validateData($input, [
            'kategori'  => 'required|in_list[' . implode(',', array_keys(self::KATEGORI)) . ']',
            'isi_suara' => 'required|min_length[10]|max_length[2000]',
        ], [
            'kategori'  => ['required' => 'Pilih kategori SUARA.', 'in_list' => 'Kategori tidak valid.'],
            'isi_suara' => [
                'required'   => 'Isi SUARA wajib diisi.',
                'min_length' => 'Isi SUARA minimal 10 karakter supaya jelas maksudnya.',
                'max_length' => 'Isi SUARA maksimal 2000 karakter.',
            ],
        ]);

        if (! $valid) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'id_sekolah'    => session()->get('id_sekolah'),
            'kategori'      => $input['kategori'],
            'isi_suara'     => $input['isi_suara'],
            'tanggal_kirim' => date('Y-m-d H:i:s'),
        ];

        // Identitas pengirim sesuai jenis akun
        if ($role === 'guru') {
            $guru = (new \App\Models\GuruModel())->find(session()->get('user_id'));
            $data += [
                'id_guru'       => session()->get('user_id'),
                'nama_pengirim' => $guru['nama_guru'] ?? session()->get('nama'),
            ];
        } else {
            $akun = (new \App\Models\AdminModel())->find(session()->get('user_id'));
            $data += [
                'id_admin_pengirim' => session()->get('user_id'),
                'nama_pengirim'     => $akun['nama_admin'] ?? session()->get('nama'),
                'email_pengirim'    => $akun['email'] ?? null,
            ];
        }

        \Config\Database::connect()->table('suara')->insert($data);

        return redirect()->to('suara')->with('sukses', 'Terima kasih! SUARA Anda sudah terkirim ke Dinas.');
    }

    // =====================================================
    // ADMIN DINAS: rekap SUARA
    // =====================================================
    private function indexDinas()
    {
        $db     = \Config\Database::connect();
        $filter = $this->ambilFilter();

        // Ringkasan per kategori (tidak terpengaruh filter)
        $ringkas = ['total' => $db->table('suara')->countAllResults()];
        foreach (array_keys(self::KATEGORI) as $k) {
            $ringkas[$k] = $db->table('suara')->where('kategori', $k)->countAllResults();
        }
        $ringkas['belum'] = $db->table('suara')->where('status_tindak_lanjut', 'belum_ditindak')->countAllResults();

        return view('admin/dinas/suara', [
            'suara'    => $this->querySuara($filter),
            'filter'   => $filter,
            'ringkas'  => $ringkas,
            'kategori' => self::KATEGORI,
            'status'   => self::STATUS,
            'kabKota'  => Csekolah::KAB_KOTA,
            'sekolah'  => $db->table('sekolah')->select('id_sekolah, nama_sekolah')
                ->orderBy('nama_sekolah', 'ASC')->get()->getResultArray(),
        ]);
    }

    // =====================================================
    // BALAS / PERBARUI TANGGAPAN (Admin Dinas)
    // =====================================================
    public function balas($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $suara = $db->table('suara')->where('id_suara', $id)->get()->getRowArray();
        if (! $suara) {
            return redirect()->to('suara')->with('gagal', 'SUARA tidak ditemukan.');
        }

        $tanggapan = trim((string) $this->request->getPost('tanggapan'));
        $status    = (string) $this->request->getPost('status');

        if (! in_array($status, ['diproses', 'selesai'], true)) {
            $status = 'selesai';
        }
        if (mb_strlen($tanggapan) < 5 || mb_strlen($tanggapan) > 2000) {
            return redirect()->to(site_url('suara') . '?' . http_build_query($this->request->getGet()))
                ->with('gagal', 'Tanggapan wajib diisi (5 sampai 2000 karakter).');
        }

        $db->table('suara')->where('id_suara', $id)->update([
            'tanggapan'             => $tanggapan,
            'status_tindak_lanjut'  => $status,
            'id_admin_penindak'     => session()->get('user_id'),
            'tanggal_tindak_lanjut' => date('Y-m-d H:i:s'),
        ]);

        // Kembali ke halaman dengan filter yang sama
        return redirect()->to(site_url('suara') . '?' . http_build_query($this->request->getGet()))
            ->with('sukses', 'Tanggapan berhasil ' . ($suara['tanggapan'] ? 'diperbarui' : 'dikirim') . '.');
    }

    // =====================================================
    // UNDUH REKAP (CSV, bisa dibuka di Excel)
    // =====================================================
    public function export()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $data = $this->querySuara($this->ambilFilter());

        $file = fopen('php://temp', 'r+');
        fwrite($file, "\xEF\xBB\xBF");
        fputcsv($file, ['No', 'Tanggal', 'Kategori', 'Pengirim', 'Jenis Pengirim', 'Email', 'Sekolah', 'Kabupaten/Kota', 'Isi SUARA', 'Status', 'Tanggapan Dinas', 'Dibalas Oleh', 'Tanggal Dibalas'], ';');

        foreach ($data as $i => $d) {
            fputcsv($file, [
                $i + 1,
                date('d/m/Y H:i', strtotime($d['tanggal_kirim'])),
                self::KATEGORI[$d['kategori']] ?? $d['kategori'],
                $d['pengirim'],
                $d['jenis_pengirim'],
                $d['email_pengirim'] ?: '-',
                $d['nama_sekolah'] ?? '-',
                $d['kabupaten_kota'] ?? '-',
                $d['isi_suara'],
                self::STATUS[$d['status_tindak_lanjut']] ?? '-',
                $d['tanggapan'] ?: '-',
                $d['nama_penindak'] ?: '-',
                $d['tanggal_tindak_lanjut'] ? date('d/m/Y H:i', strtotime($d['tanggal_tindak_lanjut'])) : '-',
            ], ';');
        }

        rewind($file);
        $isi = stream_get_contents($file);
        fclose($file);

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition', 'attachment; filename="rekap-suara-' . date('Ymd-His') . '.csv"')
            ->setBody($isi);
    }
}
