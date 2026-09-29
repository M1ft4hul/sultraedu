<?php

namespace App\Controllers;

class CpraktikBaik extends BaseController
{
    public const STATUS = [
        'menunggu'  => 'Menunggu Validasi',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
    ];

    // Sementara halaman ini khusus Admin Dinas.
    // Nanti role lain (guru, admin sekolah) ditambahkan seperti pola di Cdashboard.
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // =====================================================
    // DAFTAR PRAKTIK BAIK (yang sudah lolos verifikasi sekolah)
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db     = \Config\Database::connect();
        $status = (string) $this->request->getGet('status');
        if (! array_key_exists($status, self::STATUS)) {
            $status = '';
        }

        $builder = $db->table('praktik_baik pb')
            ->select('pb.id_praktik_baik, pb.judul, pb.deskripsi, pb.kategori, pb.tanggal_upload,
                      pb.catatan_admin_sekolah, pb.tanggal_verifikasi_sekolah,
                      pb.status_verifikasi_dinas, pb.catatan_petugas, pb.tanggal_verifikasi_dinas,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota,
                      g.nama_guru, g.nip, g.mapel,
                      vs.nama_admin AS verifikator_sekolah,
                      vd.nama_admin AS verifikator_dinas')
            ->join('sekolah s', 's.id_sekolah = pb.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = pb.id_guru', 'left')
            ->join('admin vs', 'vs.id_admin = pb.id_verifikator_sekolah', 'left')
            ->join('admin vd', 'vd.id_admin = pb.id_verifikator_dinas', 'left')
            // Dinas hanya menangani yang sudah disetujui Admin Sekolah
            ->where('pb.status_verifikasi_sekolah', 'disetujui');

        if ($status !== '') {
            $builder->where('pb.status_verifikasi_dinas', $status);
        }

        // Yang menunggu paling atas, lalu yang terlama diajukan (supaya tidak terlupa)
        $praktik = $builder
            ->orderBy("pb.status_verifikasi_dinas = 'menunggu'", 'DESC', false)
            ->orderBy('pb.tanggal_upload', 'ASC')
            ->get()->getResultArray();

        // Lampiran dokumen (sekali query untuk semua baris)
        $ids      = array_column($praktik, 'id_praktik_baik');
        $lampiran = [];

        if ($ids) {
            $dokumen = $db->table('praktik_baik_dokumen')
                ->select('id_praktik_baik, jenis_dokumen, nama_file, path_file, keterangan')
                ->whereIn('id_praktik_baik', $ids)
                ->get()->getResultArray();

            foreach ($dokumen as $d) {
                $d['url'] = $d['path_file'] ? base_url($d['path_file']) : null;
                $lampiran[$d['id_praktik_baik']][] = $d;
            }
        }

        foreach ($praktik as &$p) {
            $p['lampiran'] = $lampiran[$p['id_praktik_baik']] ?? [];
        }
        unset($p);

        // Jumlah per status untuk tab
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
            'praktik' => $praktik,
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => self::STATUS,
        ]);
    }

    // =====================================================
    // SETUJUI / TOLAK (validasi Dinas)
    // =====================================================
    public function verifikasi($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db      = \Config\Database::connect();
        $praktik = $db->table('praktik_baik')->where('id_praktik_baik', $id)->get()->getRowArray();

        if (! $praktik) {
            return redirect()->to('praktik-baik')->with('gagal', 'Data praktik baik tidak ditemukan.');
        }
        if ($praktik['status_verifikasi_sekolah'] !== 'disetujui') {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini belum diverifikasi Admin Sekolah.');
        }
        if ($praktik['status_verifikasi_dinas'] !== 'menunggu') {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini sudah divalidasi sebelumnya.');
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

        $db->table('praktik_baik')->where('id_praktik_baik', $id)->update([
            'status_verifikasi_dinas'  => $aksi,
            'id_verifikator_dinas'     => session()->get('user_id'),
            'catatan_petugas'          => $catatan !== '' ? $catatan : null,
            'tanggal_verifikasi_dinas' => $sekarang,
            'updated_at'               => $sekarang,
        ]);

        $pesan = $aksi === 'disetujui'
            ? 'Praktik baik "' . esc($praktik['judul']) . '" disetujui dan resmi terdokumentasi.'
            : 'Praktik baik "' . esc($praktik['judul']) . '" ditolak. Catatan sudah dikirim ke pengusul.';

        return redirect()->to('praktik-baik')->with('sukses', $pesan);
    }
}
