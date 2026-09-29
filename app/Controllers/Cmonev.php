<?php

namespace App\Controllers;

class Cmonev extends BaseController
{
    // Usulan fokus monitoring (tetap bisa diketik bebas)
    public const ASPEK = [
        'Keberlanjutan Inovasi',
        'Implementasi di Sekolah',
        'Dampak bagi Peserta Didik',
        'Kesiapan Replikasi ke Sekolah Lain',
        'Tindak Lanjut Rekomendasi Juri',
    ];

    public const STATUS = [
        'dijadwalkan' => 'Dijadwalkan',
        'selesai'     => 'Selesai',
    ];

    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // =====================================================
    // HALAMAN MONEV
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db = \Config\Database::connect();

        // ---------- Indikator utama ----------
        $sekolahAktif = $db->table('sekolah')->where('status', 'aktif')->countAllResults();

        $sekolahBerpartisipasi = (int) $db->query(
            "SELECT COUNT(DISTINCT pb.id_sekolah) AS n
             FROM praktik_baik pb
             JOIN sekolah s ON s.id_sekolah = pb.id_sekolah
             WHERE s.status = 'aktif'"
        )->getRow()->n;

        $indikator = [
            'partisipasi'   => $sekolahAktif ? round($sekolahBerpartisipasi / $sekolahAktif * 100, 1) : 0,
            'sekolahAktif'  => $sekolahAktif,
            'sekolahIkut'   => $sekolahBerpartisipasi,
            'praktik'       => $db->table('praktik_baik')->where('status_verifikasi_dinas', 'disetujui')->countAllResults(),
            'praktikTunggu' => $db->table('praktik_baik')->where('status_verifikasi_sekolah', 'disetujui')->where('status_verifikasi_dinas', 'menunggu')->countAllResults(),
            'inovasi'       => $db->table('bank_inovasi')->where('status_verifikasi', 'disetujui')->countAllResults(),
            'apresiasi'     => $db->table('apresiasi')->countAllResults(),
            'peserta'       => $db->table('kompetisi_peserta')->countAllResults(),
            'suara'         => $db->table('suara')->countAllResults(),
        ];

        // ---------- Sebaran per kabupaten/kota ----------
        $perKab = [];
        foreach (Csekolah::KAB_KOTA as $kab) {
            $perKab[$kab] = ['sekolah' => 0, 'ikut' => 0, 'praktik' => 0, 'inovasi' => 0];
        }

        $rows = $db->query(
            "SELECT s.kabupaten_kota, COUNT(*) AS sekolah,
                    SUM(EXISTS (SELECT 1 FROM praktik_baik pb WHERE pb.id_sekolah = s.id_sekolah)) AS ikut
             FROM sekolah s
             WHERE s.status = 'aktif'
             GROUP BY s.kabupaten_kota"
        )->getResultArray();
        foreach ($rows as $r) {
            $perKab[$r['kabupaten_kota']]['sekolah'] = (int) $r['sekolah'];
            $perKab[$r['kabupaten_kota']]['ikut']    = (int) $r['ikut'];
        }

        foreach (
            [
                'praktik' => "SELECT s.kabupaten_kota, COUNT(*) AS n FROM praktik_baik pb JOIN sekolah s ON s.id_sekolah = pb.id_sekolah
                          WHERE pb.status_verifikasi_dinas = 'disetujui' GROUP BY s.kabupaten_kota",
                'inovasi' => "SELECT s.kabupaten_kota, COUNT(*) AS n FROM bank_inovasi bi JOIN sekolah s ON s.id_sekolah = bi.id_sekolah
                          WHERE bi.status_verifikasi = 'disetujui' GROUP BY s.kabupaten_kota",
            ] as $kunci => $sql
        ) {
            foreach ($db->query($sql)->getResultArray() as $r) {
                $perKab[$r['kabupaten_kota']][$kunci] = (int) $r['n'];
            }
        }

        // ---------- Jadwal monitoring ----------
        $filterKompetisi = (string) $this->request->getGet('kompetisi');

        $builder = $db->table('monev m')
            ->select('m.id_monev, m.id_peserta, m.tanggal_monev, m.aspek_monev, m.deskripsi, m.status,
                      m.hasil_temuan, m.rekomendasi, m.file_hasil, m.tanggal_hasil,
                      p.judul_karya, p.peringkat, k.id_kompetisi, k.nama_kompetisi,
                      s.nama_sekolah, s.kabupaten_kota, g.nama_guru')
            ->join('kompetisi_peserta p', 'p.id_peserta = m.id_peserta')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
            ->join('guru g', 'g.id_guru = p.id_guru', 'left');
        if ($filterKompetisi !== '') {
            $builder->where('k.id_kompetisi', $filterKompetisi);
        }
        // Yang masih dijadwalkan di atas, lalu tanggal terdekat
        $jadwal = $builder->orderBy("m.status = 'dijadwalkan'", 'DESC', false)
            ->orderBy('m.tanggal_monev', 'ASC')
            ->get()->getResultArray();

        // Kompetisi yang sudah diumumkan beserta pesertanya (untuk form)
        $kompetisi = $db->table('kompetisi')
            ->select('id_kompetisi, nama_kompetisi')
            ->where('hasil_diumumkan', 1)
            ->orderBy('tanggal_pengumuman', 'DESC')
            ->get()->getResultArray();

        $peserta = [];
        if ($kompetisi) {
            $rows = $db->table('kompetisi_peserta p')
                ->select('p.id_peserta, p.id_kompetisi, p.judul_karya, p.peringkat, p.nilai, s.nama_sekolah')
                ->join('sekolah s', 's.id_sekolah = p.id_sekolah', 'left')
                ->whereIn('p.id_kompetisi', array_column($kompetisi, 'id_kompetisi'))
                ->where('p.status_validasi', 'tervalidasi')
                ->orderBy('p.peringkat IS NULL', 'ASC', false)
                ->orderBy('p.peringkat', 'ASC')
                ->orderBy('p.nilai', 'DESC')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $peserta[$r['id_kompetisi']][] = $r;
            }
        }

        return view('admin/dinas/monev', [
            'indikator'       => $indikator,
            'perKab'          => $perKab,
            'jadwal'          => $jadwal,
            'filterKompetisi' => $filterKompetisi,
            'kompetisi'       => $kompetisi,
            'peserta'         => $peserta,
            'aspek'           => self::ASPEK,
            'labelStatus'     => self::STATUS,
        ]);
    }

    // =====================================================
    // SIMPAN JADWAL (buat banyak sekaligus, atau edit satu)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db    = \Config\Database::connect();
        $id    = (int) $this->request->getPost('id_monev');
        $input = [
            'tanggal_monev' => (string) $this->request->getPost('tanggal_monev'),
            'aspek_monev'   => trim((string) $this->request->getPost('aspek_monev')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
        ];

        $valid = $this->validateData($input, [
            'tanggal_monev' => 'required|valid_date[Y-m-d]',
            'aspek_monev'   => 'required|max_length[150]',
        ], [
            'tanggal_monev' => ['required' => 'Tanggal monitoring wajib diisi.', 'valid_date' => 'Format tanggal tidak valid.'],
            'aspek_monev'   => ['required' => 'Fokus monitoring wajib diisi.'],
        ]);

        if (! $valid) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $sekarang = date('Y-m-d H:i:s');
        $data     = $input + [
            'deskripsi'  => $input['deskripsi'] ?: null,
            'updated_at' => $sekarang,
        ];
        $data['deskripsi'] = $input['deskripsi'] ?: null;

        // ---------- Edit satu jadwal ----------
        if ($id > 0) {
            $lama = $db->table('monev')->where('id_monev', $id)->get()->getRowArray();
            if (! $lama || $lama['status'] !== 'dijadwalkan') {
                return redirect()->to('monev')->with('gagal', 'Jadwal yang sudah selesai tidak bisa diubah.');
            }
            $db->table('monev')->where('id_monev', $id)->update($data);

            return redirect()->to(site_url('monev') . '#jadwal')->with('sukses', 'Jadwal monitoring berhasil diperbarui.');
        }

        // ---------- Buat jadwal untuk beberapa karya sekaligus ----------
        $dipilih = array_map('intval', (array) $this->request->getPost('peserta'));
        if (empty($dipilih)) {
            return redirect()->back()->withInput()->with('errors', ['Pilih minimal satu karya yang akan dimonitoring.']);
        }

        // Hanya karya tervalidasi dari kompetisi yang sudah diumumkan
        $peserta = $db->table('kompetisi_peserta p')
            ->select('p.id_peserta, p.id_sekolah')
            ->join('kompetisi k', 'k.id_kompetisi = p.id_kompetisi')
            ->where('k.hasil_diumumkan', 1)
            ->where('p.status_validasi', 'tervalidasi')
            ->whereIn('p.id_peserta', $dipilih)
            ->get()->getResultArray();

        if (empty($peserta)) {
            return redirect()->back()->withInput()->with('errors', ['Karya yang dipilih tidak valid.']);
        }

        $baris = [];
        foreach ($peserta as $p) {
            $baris[] = $data + [
                'id_peserta'       => $p['id_peserta'],
                'id_sekolah'       => $p['id_sekolah'],
                'id_admin_pembuat' => session()->get('user_id'),
                'status'           => 'dijadwalkan',
                'created_at'       => $sekarang,
            ];
        }
        $db->table('monev')->insertBatch($baris);

        return redirect()->to(site_url('monev') . '#jadwal')
            ->with('sukses', count($baris) . ' jadwal monitoring berhasil dibuat.');
    }

    // =====================================================
    // HAPUS JADWAL (hanya yang belum selesai)
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db   = \Config\Database::connect();
        $lama = $db->table('monev')->where('id_monev', $id)->get()->getRowArray();

        if (! $lama || $lama['status'] !== 'dijadwalkan') {
            return redirect()->to('monev')->with('gagal', 'Jadwal yang sudah selesai tidak bisa dihapus.');
        }

        $db->table('monev')->where('id_monev', $id)->delete();

        return redirect()->to(site_url('monev') . '#jadwal')->with('sukses', 'Jadwal monitoring berhasil dihapus.');
    }
}
