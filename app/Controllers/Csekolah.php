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

        // Pagination: 10 data per halaman (bisa 25 / 50)
        $perHalaman = (int) $this->request->getGet('per');
        if (! in_array($perHalaman, [10, 25, 50], true)) {
            $perHalaman = 10;
        }

        $data['sekolah'] = $builder->orderBy('kabupaten_kota', 'ASC')
            ->orderBy('nama_sekolah', 'ASC')
            ->paginate($perHalaman, 'sekolah');
        $data['pager']      = $this->sekolah->pager;
        $data['perHalaman'] = $perHalaman;

        $data['filter']  = $filter;
        $data['kabKota'] = self::KAB_KOTA;

        // Ringkasan jumlah untuk kartu kecil di atas tabel
        $data['ringkas'] = [
            'total'    => $this->sekolah->countAllResults(),
            'aktif'    => $this->sekolah->where('status', 'aktif')->countAllResults(),
            'nonaktif' => $this->sekolah->where('status', 'nonaktif')->countAllResults(),
        ];

        // Daftar NPSN yang sudah ada (untuk pratinjau import)
        $data['npsnAda'] = array_column(
            \Config\Database::connect()->table('sekolah')->select('npsn')->get()->getResultArray(),
            'npsn'
        );

        return view('admin/dinas/sekolah', $data);
    }

    // =====================================================
    // IMPORT DARI EXCEL
    // =====================================================

    // Samakan penulisan kab/kota dengan daftar resmi (Kab. Konawe Selatan, KONAWE SELATAN, Kota Bau-Bau, ...)
    public static function normalisasiKab(string $teks): ?string
    {
        $inti = function (string $t): string {
            $t = strtolower(trim($t));
            $t = preg_replace('/^(kabupaten|kab\.?|kota)\s*/', '', $t);

            return preg_replace('/[^a-z]/', '', $t);
        };

        $cari    = $inti($teks);
        $adaKota = (bool) preg_match('/^\s*kota\b/i', $teks);
        $cocok   = [];
        foreach (self::KAB_KOTA as $resmi) {
            if ($inti($resmi) === $cari) {
                $cocok[] = $resmi;
            }
        }
        if (count($cocok) > 1) {
            // Nama sama untuk kab & kota: pilih sesuai awalan yang ditulis
            foreach ($cocok as $resmi) {
                if (str_starts_with($resmi, 'Kota') === $adaKota) {
                    return $resmi;
                }
            }
        }

        return $cocok[0] ?? null;
    }

    // Petakan bentuk pendidikan ke jenjang aplikasi
    public static function normalisasiJenjang(string $teks): ?string
    {
        $t = strtoupper(preg_replace('/[^A-Za-z]/', '', $teks));
        if ($t === '') {
            return null;
        }
        if (str_contains($t, 'SLB') || str_contains($t, 'LB') || str_contains($t, 'LUARBIASA')) {
            return 'SLB';
        }
        if (str_starts_with($t, 'SMK')) {
            return 'SMK';
        }
        if (str_starts_with($t, 'SMA')) {
            return 'SMA';
        }

        return null;
    }

    public function import()
    {
        if (! $this->bolehAkses()) {
            return redirect()->to('dashboard');
        }

        $baris    = json_decode((string) $this->request->getPost('data'), true);
        $perbarui = (bool) $this->request->getPost('perbarui');

        if (! is_array($baris) || ! $baris) {
            return redirect()->to('sekolah')->with('gagal', 'Tidak ada data yang bisa diimport. Pilih file Excel terlebih dulu.');
        }
        if (count($baris) > 5000) {
            return redirect()->to('sekolah')->with('gagal', 'Maksimal 5.000 baris sekali import.');
        }

        $db  = \Config\Database::connect();
        $ada = array_column($db->table('sekolah')->select('id_sekolah, npsn')->get()->getResultArray(), 'id_sekolah', 'npsn');

        $tambah = [];
        $ubah   = [];
        $gagal  = [];
        $dilewati = 0;
        $dilihat  = [];
        $sekarang = date('Y-m-d H:i:s');

        foreach ($baris as $i => $r) {
            $no   = (int) ($r['baris'] ?? ($i + 2));
            $npsn = strtoupper(preg_replace('/\s+/', '', (string) ($r['npsn'] ?? '')));
            $nama = trim((string) ($r['nama_sekolah'] ?? ''));

            $jenjang = self::normalisasiJenjang((string) ($r['jenjang'] ?? ''));
            $kab     = self::normalisasiKab((string) ($r['kabupaten_kota'] ?? ''));
            $status  = strtolower(trim((string) ($r['status'] ?? '')));
            $status  = in_array($status, ['nonaktif', 'tidak aktif', 'non aktif'], true) ? 'nonaktif' : 'aktif';
            $email   = trim((string) ($r['email'] ?? ''));

            $alasan = [];
            if (! preg_match('/^[0-9A-Z]{8}$/', $npsn)) {
                $alasan[] = 'NPSN harus 8 karakter';
            }
            if ($nama === '') {
                $alasan[] = 'nama sekolah kosong';
            }
            if (! $jenjang) {
                $alasan[] = 'jenjang harus SMA/SMK/SLB';
            }
            if (! $kab) {
                $alasan[] = 'kab/kota tidak dikenali';
            }
            if ($email !== '' && ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = ''; // email tidak valid diabaikan, data lain tetap masuk
            }
            if (isset($dilihat[$npsn])) {
                $alasan[] = 'NPSN ganda di file (sama dengan baris ' . $dilihat[$npsn] . ')';
            }

            if ($alasan) {
                $gagal[] = 'Baris ' . $no . ($nama ? ' (' . $nama . ')' : '') . ': ' . implode(', ', $alasan) . '.';
                continue;
            }
            $dilihat[$npsn] = $no;

            $data = [
                'npsn'           => $npsn,
                'nama_sekolah'   => mb_substr($nama, 0, 150),
                'jenjang'        => $jenjang,
                'kabupaten_kota' => $kab,
                'kecamatan'      => mb_substr(trim((string) ($r['kecamatan'] ?? '')), 0, 100) ?: null,
                'alamat'         => trim((string) ($r['alamat'] ?? '')) ?: null,
                'telepon'        => mb_substr(trim((string) ($r['telepon'] ?? '')), 0, 30) ?: null,
                'email'          => $email ? mb_substr($email, 0, 100) : null,
                'status'         => $status,
                'updated_at'     => $sekarang,
            ];

            if (isset($ada[$npsn])) {
                if ($perbarui) {
                    $ubah[(int) $ada[$npsn]] = $data;
                } else {
                    $dilewati++;
                }
            } else {
                $tambah[] = $data + ['created_at' => $sekarang];
            }
        }

        if ($tambah || $ubah) {
            $db->transStart();
            foreach (array_chunk($tambah, 200) as $potong) {
                $db->table('sekolah')->insertBatch($potong);
            }
            foreach ($ubah as $idSekolah => $data) {
                $db->table('sekolah')->where('id_sekolah', $idSekolah)->update($data);
            }
            $db->transComplete();

            if (! $db->transStatus()) {
                return redirect()->to('sekolah')->with('gagal', 'Import gagal disimpan. Tidak ada data yang berubah.');
            }
        }

        return redirect()->to('sekolah')->with('laporanImport', [
            'tambah'   => count($tambah),
            'ubah'     => count($ubah),
            'dilewati' => $dilewati,
            'gagal'    => $gagal,
        ]);
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
            'id_sekolah', 'npsn', 'nama_sekolah', 'jenjang', 'alamat',
            'kecamatan', 'kabupaten_kota', 'telepon', 'email', 'status',
        ]);

        $input = array_map(fn ($v) => is_string($v) ? trim($v) : $v, $input);
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