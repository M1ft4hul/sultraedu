<?php

namespace App\Controllers;

class Cmonev extends BaseController
{
    // Usulan aspek pemantauan (tetap bisa diketik bebas)
    public const ASPEK = [
        'Implementasi Praktik Baik',
        'Keberlanjutan Inovasi',
        'Dampak bagi Peserta Didik',
        'Pemanfaatan Platform EDUVATION',
        'Kesiapan Replikasi',
        'Tindak Lanjut Hasil Kompetisi',
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
            'partisipasi' => $sekolahAktif ? round($sekolahBerpartisipasi / $sekolahAktif * 100, 1) : 0,
            'sekolahAktif'  => $sekolahAktif,
            'sekolahIkut'   => $sekolahBerpartisipasi,
            'praktik'       => $db->table('praktik_baik')->where('status_verifikasi_dinas', 'disetujui')->countAllResults(),
            'praktikTunggu' => $db->table('praktik_baik')->where('status_verifikasi_sekolah', 'disetujui')->where('status_verifikasi_dinas', 'menunggu')->countAllResults(),
            'inovasi'       => $db->table('bank_inovasi')->where('status_verifikasi', 'disetujui')->countAllResults(),
            'apresiasi'     => $db->table('apresiasi')->countAllResults(),
            'peserta'       => $db->table('kompetisi_peserta')->countAllResults(),
            'suara'         => $db->table('suara')->countAllResults(),
            'monev'         => $db->table('monev')->countAllResults(),
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

        $rows = $db->query(
            "SELECT s.kabupaten_kota, COUNT(*) AS n
             FROM praktik_baik pb JOIN sekolah s ON s.id_sekolah = pb.id_sekolah
             WHERE pb.status_verifikasi_dinas = 'disetujui'
             GROUP BY s.kabupaten_kota"
        )->getResultArray();
        foreach ($rows as $r) {
            $perKab[$r['kabupaten_kota']]['praktik'] = (int) $r['n'];
        }

        $rows = $db->query(
            "SELECT s.kabupaten_kota, COUNT(*) AS n
             FROM bank_inovasi bi JOIN sekolah s ON s.id_sekolah = bi.id_sekolah
             WHERE bi.status_verifikasi = 'disetujui'
             GROUP BY s.kabupaten_kota"
        )->getResultArray();
        foreach ($rows as $r) {
            $perKab[$r['kabupaten_kota']]['inovasi'] = (int) $r['n'];
        }

        // ---------- Catatan monev lapangan ----------
        $filterSekolah = (string) $this->request->getGet('sekolah');

        $builder = $db->table('monev m')
            ->select('m.*, s.nama_sekolah, s.kabupaten_kota, a.nama_admin AS pencatat')
            ->join('sekolah s', 's.id_sekolah = m.id_sekolah', 'left')
            ->join('admin a', 'a.id_admin = m.id_penanggung_jawab', 'left');
        if ($filterSekolah !== '') {
            $builder->where('m.id_sekolah', $filterSekolah);
        }
        $catatan = $builder->orderBy('m.tanggal_monev', 'DESC')->get()->getResultArray();

        return view('admin/dinas/monev', [
            'indikator'     => $indikator,
            'perKab'        => $perKab,
            'catatan'       => $catatan,
            'filterSekolah' => $filterSekolah,
            'aspek'         => self::ASPEK,
            'sekolah'       => $db->table('sekolah')->select('id_sekolah, nama_sekolah, kabupaten_kota')
                ->orderBy('nama_sekolah', 'ASC')->get()->getResultArray(),
        ]);
    }

    // =====================================================
    // SIMPAN CATATAN MONEV (tambah / edit)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $id    = (int) $this->request->getPost('id_monev');
        $input = [
            'id_sekolah'    => (string) $this->request->getPost('id_sekolah'),
            'tanggal_monev' => (string) $this->request->getPost('tanggal_monev'),
            'aspek_monev'   => trim((string) $this->request->getPost('aspek_monev')),
            'deskripsi'     => trim((string) $this->request->getPost('deskripsi')),
            'hasil_temuan'  => trim((string) $this->request->getPost('hasil_temuan')),
            'rekomendasi'   => trim((string) $this->request->getPost('rekomendasi')),
        ];

        $rules = [
            'id_sekolah'    => 'required|is_not_unique[sekolah.id_sekolah]',
            'tanggal_monev' => 'required|valid_date[Y-m-d]',
            'aspek_monev'   => 'required|max_length[150]',
            'hasil_temuan'  => 'required',
        ];
        $pesan = [
            'id_sekolah'    => ['required' => 'Sekolah wajib dipilih.', 'is_not_unique' => 'Sekolah tidak ditemukan.'],
            'tanggal_monev' => ['required' => 'Tanggal monev wajib diisi.', 'valid_date' => 'Format tanggal tidak valid.'],
            'aspek_monev'   => ['required' => 'Aspek yang dipantau wajib diisi.'],
            'hasil_temuan'  => ['required' => 'Hasil temuan wajib diisi.'],
        ];

        if (! $this->validateData($input, $rules, $pesan)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $db       = \Config\Database::connect();
        $sekarang = date('Y-m-d H:i:s');
        $data     = $input + [
            'deskripsi'   => $input['deskripsi'] ?: null,
            'rekomendasi' => $input['rekomendasi'] ?: null,
            'updated_at'  => $sekarang,
        ];

        if ($id > 0) {
            $db->table('monev')->where('id_monev', $id)->update($data);
            $pesanSukses = 'Catatan monev berhasil diperbarui.';
        } else {
            $db->table('monev')->insert($data + [
                'id_penanggung_jawab' => session()->get('user_id'),
                'created_at'          => $sekarang,
            ]);
            $pesanSukses = 'Catatan monev berhasil ditambahkan.';
        }

        return redirect()->to(site_url('monev') . '#catatan')->with('sukses', $pesanSukses);
    }

    // =====================================================
    // HAPUS CATATAN MONEV
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        \Config\Database::connect()->table('monev')->where('id_monev', $id)->delete();

        return redirect()->to(site_url('monev') . '#catatan')->with('sukses', 'Catatan monev berhasil dihapus.');
    }
}
