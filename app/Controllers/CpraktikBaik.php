<?php

namespace App\Controllers;

class CpraktikBaik extends BaseController
{
    public const STATUS = [
        'menunggu'  => 'Menunggu Verifikasi',
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

    // Ambil status dari URL (?status=...)
    private function statusDariUrl(): string
    {
        $status = (string) $this->request->getGet('status');

        return array_key_exists($status, self::STATUS) ? $status : '';
    }

    // Lampiran dokumen untuk banyak praktik baik sekaligus
    private function tempelLampiran($db, array $praktik): array
    {
        $ids = array_column($praktik, 'id_praktik_baik');
        if (! $ids) {
            return $praktik;
        }

        $lampiran = [];
        $dokumen  = $db->table('praktik_baik_dokumen')
            ->select('id_praktik_baik, jenis_dokumen, nama_file, path_file, keterangan')
            ->whereIn('id_praktik_baik', $ids)
            ->get()->getResultArray();

        foreach ($dokumen as $d) {
            $d['url'] = $d['path_file'] ? base_url($d['path_file']) : null;
            $lampiran[$d['id_praktik_baik']][] = $d;
        }

        foreach ($praktik as &$p) {
            $p['lampiran'] = $lampiran[$p['id_praktik_baik']] ?? [];
        }
        unset($p);

        return $praktik;
    }

    // Query dasar praktik baik lengkap dengan guru, sekolah, dan verifikator
    private function queryDasar($db)
    {
        return $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.deskripsi, pb.kategori, pb.tanggal_upload,
                      pb.status_verifikasi_sekolah, pb.catatan_admin_sekolah, pb.tanggal_verifikasi_sekolah,
                      pb.status_verifikasi_dinas, pb.catatan_petugas, pb.tanggal_verifikasi_dinas,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota,
                      g.nama_guru, g.nip, g.mapel,
                      vs.nama_admin AS verifikator_sekolah,
                      vd.nama_admin AS verifikator_dinas')
            ->join('sekolah s', 's.id_sekolah = pb.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->join('admin vs', 'vs.id_admin = pb.id_verifikator_sekolah', 'left')
            ->join('admin vd', 'vd.id_admin = pb.id_verifikator_dinas', 'left');
    }

    // =====================================================
    // ADMIN DINAS: validasi tahap 2
    // =====================================================
    private function indexDinas()
    {
        $db     = \Config\Database::connect();
        $status = $this->statusDariUrl();

        $builder = $this->queryDasar($db)->where('pb.status_verifikasi_sekolah', 'disetujui');
        if ($status !== '') {
            $builder->where('pb.status_verifikasi_dinas', $status);
        }

        $praktik = $builder
            ->orderBy("pb.status_verifikasi_dinas = 'menunggu'", 'DESC', false)
            ->orderBy('pb.tanggal_upload', 'ASC')
            ->get()->getResultArray();

        $hitung = function ($st = null) use ($db) {
            $q = $db->table('praktik_baik')->where('status_verifikasi_sekolah', 'disetujui');
            if ($st) {
                $q->where('status_verifikasi_dinas', $st);
            }

            return $q->countAllResults();
        };

        $jumlah = ['' => $hitung()];
        foreach (array_keys(self::STATUS) as $st) {
            $jumlah[$st] = $hitung($st);
        }

        return view('admin/dinas/praktik_baik', [
            'praktik' => $this->tempelLampiran($db, $praktik),
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => ['menunggu' => 'Menunggu Validasi'] + self::STATUS,
        ]);
    }

    // =====================================================
    // ADMIN SEKOLAH: verifikasi tahap 1 (hanya sekolahnya sendiri)
    // =====================================================
    private function indexSekolah()
    {
        $db        = \Config\Database::connect();
        $idSekolah = session()->get('id_sekolah');
        $status    = $this->statusDariUrl();

        $builder = $this->queryDasar($db)->where('pb.id_sekolah', $idSekolah);
        if ($status !== '') {
            $builder->where('pb.status_verifikasi_sekolah', $status);
        }

        $praktik = $builder
            ->orderBy("pb.status_verifikasi_sekolah = 'menunggu'", 'DESC', false)
            ->orderBy('pb.tanggal_upload', 'ASC')
            ->get()->getResultArray();

        $hitung = function ($st = null) use ($db, $idSekolah) {
            $q = $db->table('praktik_baik')->where('id_sekolah', $idSekolah);
            if ($st) {
                $q->where('status_verifikasi_sekolah', $st);
            }

            return $q->countAllResults();
        };

        $jumlah = ['' => $hitung()];
        foreach (array_keys(self::STATUS) as $st) {
            $jumlah[$st] = $hitung($st);
        }

        return view('admin/sekolah/praktik_baik', [
            'praktik' => $this->tempelLampiran($db, $praktik),
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => self::STATUS,
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
        $praktik = $db->table('praktik_baik')->where('id_praktik_baik', $id)->get()->getRowArray();

        if (! $praktik) {
            return redirect()->to('praktik-baik')->with('gagal', 'Data praktik baik tidak ditemukan.');
        }

        $aksi    = $this->request->getPost('aksi');
        $catatan = trim((string) $this->request->getPost('catatan'));

        if (! in_array($aksi, ['disetujui', 'ditolak'], true)) {
            return redirect()->to('praktik-baik')->with('gagal', 'Aksi tidak dikenali.');
        }
        if ($aksi === 'ditolak' && $catatan === '') {
            return redirect()->to('praktik-baik')->with('gagal', 'Alasan penolakan wajib diisi supaya guru tahu apa yang perlu diperbaiki.');
        }

        $sekarang = date('Y-m-d H:i:s');
        $judul    = esc($praktik['judul']);

        // ---------- Tahap 1: Admin Sekolah ----------
        if ($role === 'admin_sekolah') {
            if ((int) $praktik['id_sekolah'] !== (int) session()->get('id_sekolah')) {
                return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini bukan dari sekolah Anda.');
            }
            if ($praktik['status_verifikasi_sekolah'] !== 'menunggu') {
                return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini sudah diverifikasi sebelumnya.');
            }

            $data = [
                'status_verifikasi_sekolah'  => $aksi,
                'id_verifikator_sekolah'     => session()->get('user_id'),
                'catatan_admin_sekolah'      => $catatan !== '' ? $catatan : null,
                'tanggal_verifikasi_sekolah' => $sekarang,
                'updated_at'                 => $sekarang,
            ];

            // Diteruskan ke Dinas sebagai antrean baru (termasuk hasil perbaikan)
            if ($aksi === 'disetujui') {
                $data += [
                    'status_verifikasi_dinas'  => 'menunggu',
                    'id_verifikator_dinas'     => null,
                    'catatan_petugas'          => null,
                    'tanggal_verifikasi_dinas' => null,
                ];
            }

            $db->table('praktik_baik')->where('id_praktik_baik', $id)->update($data);

            $pesan = $aksi === 'disetujui'
                ? 'Praktik baik "' . $judul . '" disetujui dan diteruskan ke Dinas untuk divalidasi.'
                : 'Praktik baik "' . $judul . '" ditolak. Guru dapat memperbaiki lalu mengirim ulang.';

            return redirect()->to('praktik-baik')->with('sukses', $pesan);
        }

        // ---------- Tahap 2: Admin Dinas ----------
        if ($praktik['status_verifikasi_sekolah'] !== 'disetujui') {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini belum diverifikasi Admin Sekolah.');
        }
        if ($praktik['status_verifikasi_dinas'] !== 'menunggu') {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini sudah divalidasi sebelumnya.');
        }

        $db->table('praktik_baik')->where('id_praktik_baik', $id)->update([
            'status_verifikasi_dinas'  => $aksi,
            'id_verifikator_dinas'     => session()->get('user_id'),
            'catatan_petugas'          => $catatan !== '' ? $catatan : null,
            'tanggal_verifikasi_dinas' => $sekarang,
            'updated_at'               => $sekarang,
        ]);

        $pesan = $aksi === 'disetujui'
            ? 'Praktik baik "' . $judul . '" disetujui dan resmi terdokumentasi.'
            : 'Praktik baik "' . $judul . '" ditolak. Catatan sudah dikirim ke pengusul.';

        return redirect()->to('praktik-baik')->with('sukses', $pesan);
    }
}
