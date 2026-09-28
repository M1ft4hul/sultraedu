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

    // Sementara halaman ini khusus Admin Dinas (lihat data saja).
    // Versi Admin Sekolah (tanpa identitas pengirim) ditambahkan nanti.
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
        ];
    }

    // Query daftar SUARA lengkap dengan identitas pengirim
    private function querySuara(array $filter): array
    {
        $builder = \Config\Database::connect()->table('suara sr')
            ->select('sr.id_suara, sr.kategori, sr.isi_suara, sr.tanggal_kirim,
                      sr.nama_pengirim, sr.email_pengirim,
                      g.nama_guru, g.nip,
                      s.nama_sekolah, s.npsn, s.kabupaten_kota')
            ->join('guru g', 'g.id_guru = sr.id_guru', 'left')
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

        $data = $builder->orderBy('sr.tanggal_kirim', 'DESC')->get()->getResultArray();

        // Nama tampilan: pakai nama guru kalau pengirimnya guru terdaftar
        foreach ($data as &$d) {
            $d['pengirim'] = $d['nama_guru'] ?: ($d['nama_pengirim'] ?: 'Tanpa nama');
            $d['jenis_pengirim'] = $d['nama_guru'] ? 'Guru' : 'Umum';
        }
        unset($d);

        return $data;
    }

    // =====================================================
    // DAFTAR SUARA
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $db     = \Config\Database::connect();
        $filter = $this->ambilFilter();

        // Ringkasan per kategori (tidak terpengaruh filter)
        $ringkas = ['total' => $db->table('suara')->countAllResults()];
        foreach (array_keys(self::KATEGORI) as $k) {
            $ringkas[$k] = $db->table('suara')->where('kategori', $k)->countAllResults();
        }

        return view('admin/dinas/suara', [
            'suara'    => $this->querySuara($filter),
            'filter'   => $filter,
            'ringkas'  => $ringkas,
            'kategori' => self::KATEGORI,
            'kabKota'  => Csekolah::KAB_KOTA,
            'sekolah'  => $db->table('sekolah')->select('id_sekolah, nama_sekolah')
                ->orderBy('nama_sekolah', 'ASC')->get()->getResultArray(),
        ]);
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
        fputcsv($file, ['No', 'Tanggal', 'Kategori', 'Pengirim', 'Jenis Pengirim', 'Email', 'Sekolah', 'Kabupaten/Kota', 'Isi SUARA'], ';');

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
