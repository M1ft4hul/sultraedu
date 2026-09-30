<?php

namespace App\Controllers;

class CpraktikBaik extends BaseController
{
    public const STATUS = [
        'menunggu'  => 'Menunggu Verifikasi',
        'disetujui' => 'Disetujui',
        'ditolak'   => 'Ditolak',
    ];

    // Pilihan kategori praktik baik (silakan disesuaikan dengan Juknis)
    public const KATEGORI = [
        'Pembelajaran',
        'Pembelajaran Vokasi',
        'Literasi & Numerasi',
        'Karakter',
        'Kesejahteraan Siswa',
        'Pendidikan Inklusi',
        'Lingkungan',
        'Manajemen Sekolah',
        'Lainnya',
    ];

    // Jenis lampiran: berupa file unggahan atau tautan
    public const JENIS_FILE = [
        'foto'            => 'Foto Kegiatan',
        'dokumen_program' => 'Dokumen Program',
        'data_hasil'      => 'Data Hasil',
        'bukti_perubahan' => 'Bukti Perubahan',
        'penghargaan'     => 'Penghargaan',
        'dokumen_lain'    => 'Dokumen Lain',
    ];
    public const JENIS_TAUTAN = [
        'video'            => 'Video',
        'tautan_publikasi' => 'Tautan Publikasi',
    ];

    private const EKSTENSI   = ['jpg', 'jpeg', 'png', 'webp', 'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx'];
    private const MAKS_KB    = 5120; // 5 MB per file
    private const FOLDER     = 'uploads/praktik_baik/';

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
            case 'guru':
                return $this->indexGuru();
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
            ->select('id_dokumen, id_praktik_baik, jenis_dokumen, nama_file, path_file, keterangan')
            ->whereIn('id_praktik_baik', $ids)
            ->get()->getResultArray();

        foreach ($dokumen as $d) {
            $d['url'] = ! $d['path_file'] ? null
                : (preg_match('#^https?://#i', $d['path_file']) ? $d['path_file'] : base_url($d['path_file']));
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
                      pb.catatan_revisi, pb.jumlah_revisi, pb.tanggal_revisi,
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
    // GURU: daftar pengajuan milik sendiri
    // =====================================================

    // Posisi pengajuan dalam alur dua tahap
    public static function tahap(array $p): string
    {
        if ($p['status_verifikasi_sekolah'] === 'menunggu') {
            return 'tunggu_sekolah';
        }
        if ($p['status_verifikasi_sekolah'] === 'ditolak') {
            return 'tolak_sekolah';
        }
        if ($p['status_verifikasi_dinas'] === 'menunggu') {
            return 'tunggu_dinas';
        }

        return $p['status_verifikasi_dinas'] === 'ditolak' ? 'tolak_dinas' : 'disetujui';
    }

    private function indexGuru()
    {
        $db  = \Config\Database::connect();
        $tab = (string) $this->request->getGet('tab');

        $praktik = $this->queryDasar($db)
            ->where('pb.id_guru', session()->get('user_id'))
            ->orderBy('pb.updated_at', 'DESC')
            ->get()->getResultArray();

        $kelompok = [
            'diproses'  => ['tunggu_sekolah', 'tunggu_dinas'],
            'perbaikan' => ['tolak_sekolah', 'tolak_dinas'],
            'disetujui' => ['disetujui'],
        ];

        $jumlah = ['' => count($praktik), 'diproses' => 0, 'perbaikan' => 0, 'disetujui' => 0];
        foreach ($praktik as &$p) {
            $p['tahap']   = self::tahap($p);
            $p['bisa_ubah'] = in_array($p['tahap'], ['tunggu_sekolah', 'tolak_sekolah', 'tolak_dinas'], true);
            foreach ($kelompok as $k => $daftar) {
                if (in_array($p['tahap'], $daftar, true)) {
                    $jumlah[$k]++;
                }
            }
        }
        unset($p);

        if (isset($kelompok[$tab])) {
            $praktik = array_values(array_filter($praktik, fn($p) => in_array($p['tahap'], $kelompok[$tab], true)));
        } else {
            $tab = '';
        }

        return view('admin/guru/praktik_baik', [
            'praktik'     => $this->tempelLampiran($db, $praktik),
            'tab'         => $tab,
            'jumlah'      => $jumlah,
            'kategori'    => self::KATEGORI,
            'jenisFile'   => self::JENIS_FILE,
            'jenisTautan' => self::JENIS_TAUTAN,
            'ekstensi'    => self::EKSTENSI,
        ]);
    }

    // Ambil praktik baik milik guru yang login
    private function milikGuru($db, $id): ?array
    {
        return $db->table('praktik_baik')
            ->where('id_praktik_baik', $id)
            ->where('id_guru', session()->get('user_id'))
            ->get()->getRowArray();
    }

    // =====================================================
    // GURU: ajukan / edit / perbaiki & kirim ulang
    // =====================================================
    public function simpan()
    {
        if (! session()->get('logged_in') || session()->get('role') !== 'guru') {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $id    = (int) $this->request->getPost('id_praktik_baik');
        $lama  = null;
        $tahap = null;

        if ($id) {
            $lama = $this->milikGuru($db, $id);
            if (! $lama) {
                return redirect()->to('praktik-baik')->with('gagal', 'Data praktik baik tidak ditemukan.');
            }
            $tahap = self::tahap($lama);
            if (! in_array($tahap, ['tunggu_sekolah', 'tolak_sekolah', 'tolak_dinas'], true)) {
                return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini sedang divalidasi atau sudah disetujui, sehingga tidak bisa diubah.');
            }
        }
        $revisi = in_array($tahap, ['tolak_sekolah', 'tolak_dinas'], true);

        $input = [
            'judul'     => trim((string) $this->request->getPost('judul')),
            'kategori'  => trim((string) $this->request->getPost('kategori')),
            'deskripsi' => trim((string) $this->request->getPost('deskripsi')),
        ];
        $catatanRevisi = trim((string) $this->request->getPost('catatan_revisi'));

        // ---------- Validasi isian ----------
        $error = [];
        if ($input['judul'] === '' || mb_strlen($input['judul']) > 200) {
            $error[] = 'Judul wajib diisi (maksimal 200 karakter).';
        }
        if ($input['kategori'] === '' || mb_strlen($input['kategori']) > 100) {
            $error[] = 'Pilih kategori praktik baik.';
        }
        if (mb_strlen($input['deskripsi']) < 30) {
            $error[] = 'Deskripsi minimal 30 karakter supaya pemeriksa memahami praktik baik Anda.';
        }
        if ($revisi && mb_strlen($catatanRevisi) < 10) {
            $error[] = 'Jelaskan perbaikan yang Anda lakukan (minimal 10 karakter).';
        }

        // ---------- Validasi lampiran file ----------
        $semuaFile  = $this->request->getFiles()['file_lampiran'] ?? [];
        $jenisFile  = (array) $this->request->getPost('jenis_file');
        $ketFile    = (array) $this->request->getPost('keterangan_file');
        $fileBaru   = [];

        foreach ($semuaFile as $i => $file) {
            if (! $file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                continue;
            }
            $nama = $file->getClientName();
            if (! $file->isValid()) {
                $error[] = 'File "' . $nama . '" gagal diunggah.';
                continue;
            }
            if (! in_array(strtolower($file->getClientExtension()), self::EKSTENSI, true)) {
                $error[] = 'Format file "' . $nama . '" tidak didukung.';
                continue;
            }
            if ($file->getSizeByUnit('kb') > self::MAKS_KB) {
                $error[] = 'File "' . $nama . '" melebihi 5 MB.';
                continue;
            }
            $fileBaru[] = [
                'file'  => $file,
                'jenis' => array_key_exists($jenisFile[$i] ?? '', self::JENIS_FILE) ? $jenisFile[$i] : 'dokumen_lain',
                'ket'   => mb_substr(trim((string) ($ketFile[$i] ?? '')), 0, 255),
            ];
        }

        // ---------- Validasi tautan ----------
        $tautanBaru = [];
        $urlTautan  = (array) $this->request->getPost('url_tautan');
        $jenisTaut  = (array) $this->request->getPost('jenis_tautan');
        $ketTaut    = (array) $this->request->getPost('keterangan_tautan');
        foreach ($urlTautan as $i => $url) {
            $url = trim((string) $url);
            if ($url === '') {
                continue;
            }
            if (! preg_match('#^https?://#i', $url) || ! filter_var($url, FILTER_VALIDATE_URL)) {
                $error[] = 'Tautan "' . $url . '" tidak valid (harus diawali http:// atau https://).';
                continue;
            }
            $tautanBaru[] = [
                'url'   => mb_substr($url, 0, 255),
                'jenis' => array_key_exists($jenisTaut[$i] ?? '', self::JENIS_TAUTAN) ? $jenisTaut[$i] : 'tautan_publikasi',
                'ket'   => mb_substr(trim((string) ($ketTaut[$i] ?? '')), 0, 255),
            ];
        }

        if ($error) {
            return redirect()->to('praktik-baik')->with('gagal', implode(' ', $error));
        }

        // ---------- Simpan ----------
        $sekarang = date('Y-m-d H:i:s');
        $db->transStart();

        if ($id) {
            $data = $input + ['updated_at' => $sekarang];

            // Perbaikan setelah ditolak: kembali ke verifikasi sekolah (tahap 1)
            if ($revisi) {
                $data += [
                    'status_verifikasi_sekolah' => 'menunggu',
                    'status_verifikasi_dinas'   => 'menunggu',
                    'id_verifikator_dinas'      => null,
                    'tanggal_verifikasi_dinas'  => null,
                    'catatan_revisi'            => $catatanRevisi,
                    'jumlah_revisi'             => (int) $lama['jumlah_revisi'] + 1,
                    'tanggal_revisi'            => $sekarang,
                ];
            }
            $db->table('praktik_baik')->where('id_praktik_baik', $id)->update($data);

            // Lampiran lama yang dicentang untuk dihapus
            $hapus = array_map('intval', (array) $this->request->getPost('hapus_lampiran'));
            if ($hapus) {
                $this->hapusDokumen($db, $id, $hapus);
            }
        } else {
            $db->table('praktik_baik')->insert($input + [
                'id_guru'                   => session()->get('user_id'),
                'id_sekolah'                => session()->get('id_sekolah'),
                'status_verifikasi_sekolah' => 'menunggu',
                'status_verifikasi_dinas'   => 'menunggu',
                'tanggal_upload'            => $sekarang,
                'created_at'                => $sekarang,
                'updated_at'                => $sekarang,
            ]);
            $id = (int) $db->insertID();
        }

        // Pindahkan file & catat lampiran
        if (! is_dir(FCPATH . self::FOLDER)) {
            mkdir(FCPATH . self::FOLDER, 0755, true);
        }
        foreach ($fileBaru as $f) {
            $namaAcak = $f['file']->getRandomName();
            $f['file']->move(FCPATH . self::FOLDER, $namaAcak);
            $db->table('praktik_baik_dokumen')->insert([
                'id_praktik_baik' => $id,
                'jenis_dokumen'   => $f['jenis'],
                'nama_file'       => mb_substr($f['file']->getClientName(), 0, 255),
                'path_file'       => self::FOLDER . $namaAcak,
                'keterangan'      => $f['ket'] ?: null,
                'created_at'      => $sekarang,
            ]);
        }
        foreach ($tautanBaru as $t) {
            $db->table('praktik_baik_dokumen')->insert([
                'id_praktik_baik' => $id,
                'jenis_dokumen'   => $t['jenis'],
                'nama_file'       => $t['url'],
                'path_file'       => $t['url'],
                'keterangan'      => $t['ket'] ?: null,
                'created_at'      => $sekarang,
            ]);
        }

        $db->transComplete();

        if (! $db->transStatus()) {
            return redirect()->to('praktik-baik')->with('gagal', 'Terjadi kesalahan saat menyimpan. Silakan coba lagi.');
        }

        $pesan = match (true) {
            $revisi       => 'Perbaikan "' . esc($input['judul']) . '" berhasil dikirim ulang ke Admin Sekolah.',
            $lama !== null => 'Perubahan "' . esc($input['judul']) . '" berhasil disimpan.',
            default       => 'Praktik baik "' . esc($input['judul']) . '" berhasil diajukan dan menunggu verifikasi sekolah.',
        };

        return redirect()->to('praktik-baik')->with('sukses', $pesan);
    }

    // Hapus baris dokumen beserta file fisiknya
    private function hapusDokumen($db, int $idPraktik, ?array $idDokumen = null): void
    {
        $q = $db->table('praktik_baik_dokumen')->where('id_praktik_baik', $idPraktik);
        if ($idDokumen !== null) {
            $q->whereIn('id_dokumen', $idDokumen);
        }
        $dokumen = $q->get()->getResultArray();

        foreach ($dokumen as $d) {
            if ($d['path_file'] && ! preg_match('#^https?://#i', $d['path_file']) && is_file(FCPATH . $d['path_file'])) {
                @unlink(FCPATH . $d['path_file']);
            }
            $db->table('praktik_baik_dokumen')->where('id_dokumen', $d['id_dokumen'])->delete();
        }
    }

    // =====================================================
    // GURU: hapus pengajuan
    // =====================================================
    public function hapus($id)
    {
        if (! session()->get('logged_in') || session()->get('role') !== 'guru') {
            return redirect()->to('dashboard');
        }

        $db      = \Config\Database::connect();
        $praktik = $this->milikGuru($db, $id);
        if (! $praktik) {
            return redirect()->to('praktik-baik')->with('gagal', 'Data praktik baik tidak ditemukan.');
        }
        if (! in_array(self::tahap($praktik), ['tunggu_sekolah', 'tolak_sekolah', 'tolak_dinas'], true)) {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik yang sedang divalidasi atau sudah disetujui tidak bisa dihapus.');
        }

        // Tidak bisa dihapus kalau sudah dipakai sebagai dasar inovasi
        if ($db->table('bank_inovasi')->where('id_praktik_baik', $id)->countAllResults() > 0) {
            return redirect()->to('praktik-baik')->with('gagal', 'Praktik baik ini sudah menjadi dasar usulan inovasi, sehingga tidak bisa dihapus.');
        }

        $db->transStart();
        $this->hapusDokumen($db, (int) $id);
        $db->table('praktik_baik')->where('id_praktik_baik', $id)->delete();
        $db->transComplete();

        return redirect()->to('praktik-baik')->with('sukses', 'Praktik baik "' . esc($praktik['judul']) . '" berhasil dihapus.');
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
