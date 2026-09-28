<?php

namespace App\Controllers;

use App\Models\SekolahModel;

class Csekolah extends BaseController
{
    // Daftar kabupaten/kota di Sulawesi Tenggara
    public const KAB_KOTA = [
        'Kota Kendari',
        'Kota Baubau',
        'Kab. Bombana',
        'Kab. Buton',
        'Kab. Buton Selatan',
        'Kab. Buton Tengah',
        'Kab. Buton Utara',
        'Kab. Kolaka',
        'Kab. Kolaka Timur',
        'Kab. Kolaka Utara',
        'Kab. Konawe',
        'Kab. Konawe Kepulauan',
        'Kab. Konawe Selatan',
        'Kab. Konawe Utara',
        'Kab. Muna',
        'Kab. Muna Barat',
        'Kab. Wakatobi',
    ];

    protected SekolahModel $sekolah;

    public function __construct()
    {
        $this->sekolah = new SekolahModel();
    }

    // Hanya Admin Dinas yang boleh mengelola data sekolah
    private function bolehAkses(): bool
    {
        return session()->get('logged_in') && session()->get('role') === 'admin_pusat';
    }

    // =====================================================
    // DAFTAR SEKOLAH (+ pencarian & filter)
    // =====================================================
    public function index()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $filter = [
            'q'       => trim((string) $this->request->getGet('q')),
            'kab'     => (string) $this->request->getGet('kab'),
            'jenjang' => (string) $this->request->getGet('jenjang'),
            'status'  => (string) $this->request->getGet('status'),
        ];

        $builder = $this->sekolah;

        if ($filter['q'] !== '') {
            $builder->groupStart()
                ->like('nama_sekolah', $filter['q'])
                ->orLike('npsn', $filter['q'])
                ->groupEnd();
        }
        if ($filter['kab'] !== '') {
            $builder->where('kabupaten_kota', $filter['kab']);
        }
        if ($filter['jenjang'] !== '') {
            $builder->where('jenjang', $filter['jenjang']);
        }
        if ($filter['status'] !== '') {
            $builder->where('status', $filter['status']);
        }

        $data['sekolah'] = $builder->orderBy('kabupaten_kota', 'ASC')
            ->orderBy('nama_sekolah', 'ASC')
            ->findAll();

        $data['filter']  = $filter;
        $data['kabKota'] = self::KAB_KOTA;

        // Ringkasan jumlah untuk kartu kecil di atas tabel
        $data['ringkas'] = [
            'total'    => $this->sekolah->countAllResults(),
            'aktif'    => $this->sekolah->where('status', 'aktif')->countAllResults(),
            'nonaktif' => $this->sekolah->where('status', 'nonaktif')->countAllResults(),
        ];

        return view('admin/dinas/sekolah', $data);
    }

    // =====================================================
    // SIMPAN (tambah baru atau edit, tergantung ada id_sekolah)
    // =====================================================
    public function simpan()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $input = $this->request->getPost([
            'id_sekolah',
            'npsn',
            'nama_sekolah',
            'jenjang',
            'alamat',
            'kecamatan',
            'kabupaten_kota',
            'telepon',
            'email',
            'status',
        ]);

        $input = array_map(fn($v) => is_string($v) ? trim($v) : $v, $input);
        $input['npsn'] = strtoupper($input['npsn'] ?? '');

        $edit = ! empty($input['id_sekolah']);
        if (! $edit) {
            unset($input['id_sekolah']);
        }

        // Pastikan kab/kota berasal dari daftar resmi
        if (! in_array($input['kabupaten_kota'] ?? '', self::KAB_KOTA, true)) {
            return redirect()->back()->withInput()
                ->with('errors', ['kabupaten_kota' => 'Kabupaten/Kota tidak valid.']);
        }

        if (! $this->sekolah->save($input)) {
            return redirect()->back()->withInput()->with('errors', $this->sekolah->errors());
        }

        $pesan = $edit ? 'Data sekolah berhasil diperbarui.' : 'Sekolah baru berhasil ditambahkan.';

        return redirect()->to('sekolah')->with('sukses', $pesan);
    }

    // =====================================================
    // AKTIFKAN / NONAKTIFKAN
    // =====================================================
    public function status($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $sekolah = $this->sekolah->find($id);
        if (! $sekolah) {
            return redirect()->to('sekolah')->with('gagal', 'Data sekolah tidak ditemukan.');
        }

        $statusBaru = $sekolah['status'] === 'aktif' ? 'nonaktif' : 'aktif';
        $this->sekolah->update($id, ['status' => $statusBaru]);

        return redirect()->to('sekolah')
            ->with('sukses', esc($sekolah['nama_sekolah']) . ' sekarang berstatus ' . $statusBaru . '.');
    }

    // =====================================================
    // HAPUS (hanya jika belum dipakai data lain)
    // =====================================================
    public function hapus($id)
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $sekolah = $this->sekolah->find($id);
        if (! $sekolah) {
            return redirect()->to('sekolah')->with('gagal', 'Data sekolah tidak ditemukan.');
        }

        // Cek apakah sekolah sudah terhubung ke akun atau karya
        $db     = \Config\Database::connect();
        $terpakai = $db->table('admin')->where('id_sekolah', $id)->countAllResults()
            + $db->table('guru')->where('id_sekolah', $id)->countAllResults()
            + $db->table('praktik_baik')->where('id_sekolah', $id)->countAllResults();

        if ($terpakai > 0) {
            return redirect()->to('sekolah')->with(
                'gagal',
                esc($sekolah['nama_sekolah']) . ' tidak bisa dihapus karena sudah memiliki akun atau data praktik baik. Nonaktifkan saja.'
            );
        }

        $this->sekolah->delete($id);

        return redirect()->to('sekolah')->with('sukses', esc($sekolah['nama_sekolah']) . ' berhasil dihapus.');
    }
}
