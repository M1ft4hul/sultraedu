<?php

namespace App\Controllers;

class CbankInovasi extends BaseController
{
    public const STATUS = [
        'menunggu'  => 'Menunggu Validasi',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
    ];

    // =====================================================
    // PINTU MASUK: pilih halaman sesuai role
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

    private function statusDariUrl(): string
    {
        $status = (string) $this->request->getGet('status');

        return array_key_exists($status, self::STATUS) ? $status : '';
    }

    // Query dasar inovasi lengkap dengan sekolah, guru, praktik asal & verifikator
    private function queryDasar($db)
    {
        return $db->table('bank_inovasi bi')
            ->select('bi.id_inovasi, bi.id_praktik_baik, bi.judul_inovasi, bi.deskripsi, bi.created_at,
                      bi.status_verifikasi_sekolah, bi.catatan_sekolah, bi.tanggal_verifikasi_sekolah,
                      bi.status_verifikasi, bi.catatan_verifikasi, bi.tanggal_verifikasi, bi.tanggal_publish,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota,
                      g.nama_guru, g.nip, g.mapel,
                      pb.judul AS judul_praktik,
                      vs.nama_admin AS verifikator_sekolah,
                      a.nama_admin  AS nama_verifikator')
            ->join('sekolah s', 's.id_sekolah = bi.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = bi.id_guru', 'left')
            ->join('praktik_baik pb', 'pb.id_praktik_baik = bi.id_praktik_baik', 'left')
            ->join('admin vs', 'vs.id_admin = bi.id_verifikator_sekolah', 'left')
            ->join('admin a', 'a.id_admin = bi.id_verifikator', 'left');
    }

    // Lampiran dari praktik baik asal (sekali query untuk semua baris)
    private function tempelLampiran($db, array $inovasi): array
    {
        $idPraktik = array_filter(array_unique(array_column($inovasi, 'id_praktik_baik')));
        $lampiran  = [];

        if ($idPraktik) {
            $dokumen = $db->table('praktik_baik_dokumen')
                ->select('id_praktik_baik, jenis_dokumen, nama_file, path_file, keterangan')
                ->whereIn('id_praktik_baik', $idPraktik)
                ->get()->getResultArray();

            foreach ($dokumen as $d) {
                $d['url'] = $d['path_file'] ? base_url($d['path_file']) : null;
                $lampiran[$d['id_praktik_baik']][] = $d;
            }
        }

        foreach ($inovasi as &$i) {
            $i['lampiran'] = $lampiran[$i['id_praktik_baik']] ?? [];
        }
        unset($i);

        return $inovasi;
    }

    // =====================================================
    // ADMIN DINAS: validasi (hanya yang sudah lolos sekolah)
    // =====================================================
    private function indexDinas()
    {
        $db     = \Config\Database::connect();
        $status = $this->statusDariUrl();

        $builder = $this->queryDasar($db)->where('bi.status_verifikasi_sekolah', 'disetujui');
        if ($status !== '') {
            $builder->where('bi.status_verifikasi', $status);
        }

        $inovasi = $builder
            ->orderBy("bi.status_verifikasi = 'menunggu'", 'DESC', false)
            ->orderBy('bi.created_at', 'DESC')
            ->get()->getResultArray();

        $hitung = function ($st = null) use ($db) {
            $q = $db->table('bank_inovasi')->where('status_verifikasi_sekolah', 'disetujui');
            if ($st) {
                $q->where('status_verifikasi', $st);
            }

            return $q->countAllResults();
        };

        $jumlah = ['' => $hitung()];
        foreach (array_keys(self::STATUS) as $st) {
            $jumlah[$st] = $hitung($st);
        }

        return view('admin/dinas/bank_inovasi', [
            'inovasi' => $this->tempelLampiran($db, $inovasi),
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => self::STATUS,
        ]);
    }

    // =====================================================
    // ADMIN SEKOLAH: verifikasi (hanya sekolahnya sendiri)
    // =====================================================
    private function indexSekolah()
    {
        $db        = \Config\Database::connect();
        $idSekolah = session()->get('id_sekolah');
        $status    = $this->statusDariUrl();

        $builder = $this->queryDasar($db)->where('bi.id_sekolah', $idSekolah);
        if ($status !== '') {
            $builder->where('bi.status_verifikasi_sekolah', $status);
        }

        $inovasi = $builder
            ->orderBy("bi.status_verifikasi_sekolah = 'menunggu'", 'DESC', false)
            ->orderBy('bi.created_at', 'ASC')
            ->get()->getResultArray();

        $hitung = function ($st = null) use ($db, $idSekolah) {
            $q = $db->table('bank_inovasi')->where('id_sekolah', $idSekolah);
            if ($st) {
                $q->where('status_verifikasi_sekolah', $st);
            }

            return $q->countAllResults();
        };

        $jumlah = ['' => $hitung()];
        foreach (array_keys(self::STATUS) as $st) {
            $jumlah[$st] = $hitung($st);
        }

        return view('admin/sekolah/bank_inovasi', [
            'inovasi' => $this->tempelLampiran($db, $inovasi),
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => ['menunggu' => 'Menunggu Verifikasi'] + self::STATUS,
        ]);
    }

    // =====================================================
    // SETUJUI / TOLAK (tahap sesuai role)
    // =====================================================
    public function verifikasi($id)
    {
        $role = session()->get('role');
        if (! session()->get('logged_in') || ! in_array($role, ['admin_pusat', 'admin_sekolah'], true)) {
            return redirect()->to('dashboard');
        }

        $db      = \Config\Database::connect();
        $inovasi = $db->table('bank_inovasi')->where('id_inovasi', $id)->get()->getRowArray();

        if (! $inovasi) {
            return redirect()->to('bank-inovasi')->with('gagal', 'Data inovasi tidak ditemukan.');
        }

        $aksi    = $this->request->getPost('aksi');
        $catatan = trim((string) $this->request->getPost('catatan'));

        if (! in_array($aksi, ['disetujui', 'ditolak'], true)) {
            return redirect()->to('bank-inovasi')->with('gagal', 'Aksi tidak dikenali.');
        }
        if ($aksi === 'ditolak' && $catatan === '') {
            return redirect()->to('bank-inovasi')->with('gagal', 'Alasan penolakan wajib diisi supaya guru tahu apa yang perlu diperbaiki.');
        }

        $sekarang = date('Y-m-d H:i:s');
        $judul    = esc($inovasi['judul_inovasi']);

        // ---------- Tahap 1: Admin Sekolah ----------
        if ($role === 'admin_sekolah') {
            if ((int) $inovasi['id_sekolah'] !== (int) session()->get('id_sekolah')) {
                return redirect()->to('bank-inovasi')->with('gagal', 'Inovasi ini bukan dari sekolah Anda.');
            }
            if ($inovasi['status_verifikasi_sekolah'] !== 'menunggu') {
                return redirect()->to('bank-inovasi')->with('gagal', 'Inovasi ini sudah diverifikasi sebelumnya.');
            }

            $data = [
                'status_verifikasi_sekolah'  => $aksi,
                'id_verifikator_sekolah'     => session()->get('user_id'),
                'catatan_sekolah'            => $catatan !== '' ? $catatan : null,
                'tanggal_verifikasi_sekolah' => $sekarang,
                'updated_at'                 => $sekarang,
            ];

            // Diteruskan ke Dinas sebagai antrean baru (termasuk hasil perbaikan)
            if ($aksi === 'disetujui') {
                $data += [
                    'status_verifikasi'  => 'menunggu',
                    'id_verifikator'     => null,
                    'catatan_verifikasi' => null,
                    'tanggal_verifikasi' => null,
                    'tanggal_publish'    => null,
                ];
            }

            $db->table('bank_inovasi')->where('id_inovasi', $id)->update($data);

            $pesan = $aksi === 'disetujui'
                ? 'Inovasi "' . $judul . '" disetujui dan diteruskan ke Dinas untuk divalidasi.'
                : 'Inovasi "' . $judul . '" ditolak. Guru dapat memperbaiki lalu mengirim ulang.';

            return redirect()->to('bank-inovasi')->with('sukses', $pesan);
        }

        // ---------- Tahap 2: Admin Dinas ----------
        if ($inovasi['status_verifikasi_sekolah'] !== 'disetujui') {
            return redirect()->to('bank-inovasi')->with('gagal', 'Inovasi ini belum diverifikasi Admin Sekolah.');
        }
        if ($inovasi['status_verifikasi'] !== 'menunggu') {
            return redirect()->to('bank-inovasi')->with('gagal', 'Inovasi ini sudah divalidasi sebelumnya.');
        }

        $db->table('bank_inovasi')->where('id_inovasi', $id)->update([
            'status_verifikasi'  => $aksi,
            'id_verifikator'     => session()->get('user_id'),
            'catatan_verifikasi' => $catatan !== '' ? $catatan : null,
            'tanggal_verifikasi' => $sekarang,
            'tanggal_publish'    => $aksi === 'disetujui' ? $sekarang : null,
            'updated_at'         => $sekarang,
        ]);

        $pesan = $aksi === 'disetujui'
            ? 'Inovasi "' . $judul . '" disetujui dan resmi masuk Bank Inovasi.'
            : 'Inovasi "' . $judul . '" ditolak. Catatan sudah dikirim ke pengusul.';

        return redirect()->to('bank-inovasi')->with('sukses', $pesan);
    }
}
