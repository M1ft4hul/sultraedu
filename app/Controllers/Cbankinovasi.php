<?php

namespace App\Controllers;

class CbankInovasi extends BaseController
{
    // Label & warna status (dipakai juga di view)
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
    // DAFTAR INOVASI + TAB STATUS
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

        $builder = $db->table('bank_inovasi bi')
            ->select('bi.id_inovasi, bi.id_praktik_baik, bi.judul_inovasi, bi.deskripsi, bi.status_verifikasi,
                      bi.catatan_verifikasi, bi.tanggal_verifikasi, bi.tanggal_publish, bi.created_at,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota,
                      g.nama_guru, g.nip, g.mapel,
                      pb.judul AS judul_praktik,
                      a.nama_admin AS nama_verifikator')
            ->join('sekolah s', 's.id_sekolah = bi.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = bi.id_guru', 'left')
            ->join('praktik_baik pb', 'pb.id_praktik_baik = bi.id_praktik_baik', 'left')
            ->join('admin a', 'a.id_admin = bi.id_verifikator', 'left');

        if ($status !== '') {
            $builder->where('bi.status_verifikasi', $status);
        }

        // Yang menunggu tampil paling atas, lalu terbaru
        $inovasi = $builder
            ->orderBy("bi.status_verifikasi = 'menunggu'", 'DESC', false)
            ->orderBy('bi.created_at', 'DESC')
            ->get()->getResultArray();

        // Ambil lampiran dokumen dari praktik baik asal (sekali query untuk semua baris)
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

        // Jumlah per status untuk tab
        $jumlah = ['' => $db->table('bank_inovasi')->countAllResults()];
        foreach (array_keys(self::STATUS) as $st) {
            $jumlah[$st] = $db->table('bank_inovasi')->where('status_verifikasi', $st)->countAllResults();
        }

        return view('admin/dinas/bank_inovasi', [
            'inovasi' => $inovasi,
            'status'  => $status,
            'jumlah'  => $jumlah,
            'label'   => self::STATUS,
        ]);
    }

    // =====================================================
    // SETUJUI / TOLAK
    // =====================================================
    public function verifikasi($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db      = \Config\Database::connect();
        $inovasi = $db->table('bank_inovasi')->where('id_inovasi', $id)->get()->getRowArray();

        if (! $inovasi) {
            return redirect()->to('bank-inovasi')->with('gagal', 'Data inovasi tidak ditemukan.');
        }
        if ($inovasi['status_verifikasi'] !== 'menunggu') {
            return redirect()->to('bank-inovasi')->with('gagal', 'Inovasi ini sudah divalidasi sebelumnya.');
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

        $db->table('bank_inovasi')->where('id_inovasi', $id)->update([
            'status_verifikasi'  => $aksi,
            'id_verifikator'     => session()->get('user_id'),
            'catatan_verifikasi' => $catatan !== '' ? $catatan : null,
            'tanggal_verifikasi' => $sekarang,
            'tanggal_publish'    => $aksi === 'disetujui' ? $sekarang : null,
            'updated_at'         => $sekarang,
        ]);

        $pesan = $aksi === 'disetujui'
            ? 'Inovasi "' . esc($inovasi['judul_inovasi']) . '" disetujui dan resmi masuk Bank Inovasi.'
            : 'Inovasi "' . esc($inovasi['judul_inovasi']) . '" ditolak. Catatan sudah dikirim ke pengusul.';

        return redirect()->to('bank-inovasi')->with('sukses', $pesan);
    }
}
