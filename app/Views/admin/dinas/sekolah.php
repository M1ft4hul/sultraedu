<?php
/** @var array $sekolah */
/** @var array $filter */
/** @var array $kabKota */
/** @var array $ringkas */
/** @var array $npsnAda */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$badgeJenjang = [
    'SMA' => 'bg-blue-50 text-blue-700',
    'SMK' => 'bg-indigo-50 text-indigo-700',
    'SLB' => 'bg-purple-50 text-purple-700',
];
$errors  = session()->getFlashdata('errors') ?? [];
$laporan = session()->getFlashdata('laporanImport');
?>
<section class="space-y-6">

    <!-- Judul & tombol tambah -->
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Data Sekolah</h2>
            <p class="text-xs text-slate-500">Data master satuan pendidikan SMA, SMK, dan SLB se-Provinsi Sulawesi Tenggara.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <button type="button" onclick="bukaImport()"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
                <i class="fa-solid fa-file-excel mr-2"></i>Import Excel
            </button>
            <button type="button" onclick="bukaFormSekolah()"
                class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
                <i class="fa-solid fa-plus mr-2"></i>Tambah Sekolah
            </button>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Total Sekolah</div>
            <div class="text-xl font-bold text-slate-800"><?= number_format($ringkas['total'], 0, ',', '.') ?></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Aktif</div>
            <div class="text-xl font-bold text-emerald-600"><?= number_format($ringkas['aktif'], 0, ',', '.') ?></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Nonaktif</div>
            <div class="text-xl font-bold text-slate-400"><?= number_format($ringkas['nonaktif'], 0, ',', '.') ?></div>
        </div>
    </div>

    <!-- Laporan hasil import -->
    <?php if ($laporan) : ?>
        <div class="bg-white border-2 border-emerald-200 rounded-2xl p-5 text-xs space-y-3">
            <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-file-circle-check text-emerald-600 mr-2"></i>Hasil Import Excel</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                <div class="bg-emerald-50 rounded-xl p-3"><div class="text-[10px] uppercase text-emerald-700">Ditambahkan</div><div class="text-xl font-bold text-emerald-700"><?= $laporan['tambah'] ?></div></div>
                <div class="bg-blue-50 rounded-xl p-3"><div class="text-[10px] uppercase text-blue-700">Diperbarui</div><div class="text-xl font-bold text-blue-700"><?= $laporan['ubah'] ?></div></div>
                <div class="bg-slate-50 rounded-xl p-3"><div class="text-[10px] uppercase text-slate-500">Dilewati (sudah ada)</div><div class="text-xl font-bold text-slate-600"><?= $laporan['dilewati'] ?></div></div>
                <div class="<?= $laporan['gagal'] ? 'bg-red-50' : 'bg-slate-50' ?> rounded-xl p-3"><div class="text-[10px] uppercase <?= $laporan['gagal'] ? 'text-red-700' : 'text-slate-500' ?>">Gagal</div><div class="text-xl font-bold <?= $laporan['gagal'] ? 'text-red-700' : 'text-slate-600' ?>"><?= count($laporan['gagal']) ?></div></div>
            </div>
            <?php if ($laporan['gagal']) : ?>
                <details class="bg-red-50 border border-red-200 rounded-xl p-3">
                    <summary class="cursor-pointer font-semibold text-red-700">Lihat baris yang gagal</summary>
                    <ul class="list-disc list-inside mt-2 space-y-0.5 text-red-700 max-h-48 overflow-y-auto">
                        <?php foreach ($laporan['gagal'] as $g) : ?><li><?= esc($g) ?></li><?php endforeach; ?>
                    </ul>
                </details>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- Pesan sukses / gagal -->
    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('gagal')) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2"></i><?= session()->getFlashdata('gagal') ?>
        </div>
    <?php endif; ?>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

        <!-- Pencarian & filter -->
        <form method="get" action="<?= site_url('sekolah') ?>" class="grid grid-cols-1 md:grid-cols-5 gap-3 text-xs">
            <input type="text" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari nama sekolah atau NPSN..."
                class="md:col-span-2 border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <select name="kab" class="border border-slate-300 rounded-lg p-2.5">
                <option value="">Semua Kab/Kota</option>
                <?php foreach ($kabKota as $k) : ?>
                    <option value="<?= esc($k) ?>" <?= $filter['kab'] === $k ? 'selected' : '' ?>><?= esc($k) ?></option>
                <?php endforeach; ?>
            </select>
            <select name="jenjang" class="border border-slate-300 rounded-lg p-2.5">
                <option value="">Semua Jenjang</option>
                <?php foreach (['SMA', 'SMK', 'SLB'] as $j) : ?>
                    <option value="<?= $j ?>" <?= $filter['jenjang'] === $j ? 'selected' : '' ?>><?= $j ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-2">
                <select name="status" class="flex-1 border border-slate-300 rounded-lg p-2.5">
                    <option value="">Semua Status</option>
                    <option value="aktif" <?= $filter['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $filter['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
                <button type="submit" title="Terapkan filter" class="bg-slate-800 hover:bg-slate-700 text-white px-3 rounded-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="<?= site_url('sekolah') ?>" title="Reset filter" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-3 rounded-lg flex items-center">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Tabel -->
        <?php if (empty($sekolah)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-school text-2xl text-brand-500"></i>
                    </div>
                </div>
                <?php if (array_filter($filter)) : ?>
                    <p class="text-sm font-semibold text-slate-700">Tidak ada sekolah yang cocok</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                <?php else : ?>
                    <p class="text-sm font-semibold text-slate-700">Belum ada data sekolah</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Tambahkan sekolah pertama dengan tombol "Tambah Sekolah" di atas.</p>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 rounded-l-lg">NPSN</th>
                            <th class="p-3.5">Nama Sekolah</th>
                            <th class="p-3.5">Jenjang</th>
                            <th class="p-3.5">Kab/Kota</th>
                            <th class="p-3.5">Kontak</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($sekolah as $s) : ?>
                            <tr class="hover:bg-slate-50 transition <?= $s['status'] === 'nonaktif' ? 'opacity-60' : '' ?>">
                                <td class="p-3.5 font-mono"><?= esc($s['npsn']) ?></td>
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800"><?= esc($s['nama_sekolah']) ?></div>
                                    <div class="text-[11px] text-slate-400">
                                        <?= esc($s['kecamatan'] ? 'Kec. ' . $s['kecamatan'] : '-') ?>
                                    </div>
                                </td>
                                <td class="p-3.5">
                                    <span class="<?= $badgeJenjang[$s['jenjang']] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2.5 py-1 rounded-full">
                                        <?= esc($s['jenjang']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 whitespace-nowrap"><?= esc($s['kabupaten_kota']) ?></td>
                                <td class="p-3.5 text-[11px]">
                                    <div><?= esc($s['telepon'] ?: '-') ?></div>
                                    <div class="text-slate-400"><?= esc($s['email'] ?: '') ?></div>
                                </td>
                                <td class="p-3.5">
                                    <?php if ($s['status'] === 'aktif') : ?>
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full">Aktif</span>
                                    <?php else : ?>
                                        <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5">
                                    <div class="flex justify-center gap-1.5">
                                        <button type="button" title="Edit"
                                            data-sekolah="<?= esc(json_encode($s), 'attr') ?>"
                                            onclick="bukaFormSekolah(JSON.parse(this.dataset.sekolah))"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                        <button type="button" title="<?= $s['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                            onclick="konfirmasiAksi(
                                                '<?= site_url('sekolah/status/' . $s['id_sekolah']) ?>',
                                                '<?= $s['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> sekolah?',
                                                <?= esc(json_encode($s['nama_sekolah']), 'attr') ?>,
                                                'amber')"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center">
                                            <i class="fa-solid <?= $s['status'] === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?> text-[12px]"></i>
                                        </button>
                                        <button type="button" title="Hapus"
                                            onclick="konfirmasiAksi(
                                                '<?= site_url('sekolah/hapus/' . $s['id_sekolah']) ?>',
                                                'Hapus sekolah?',
                                                <?= esc(json_encode($s['nama_sekolah']), 'attr') ?>,
                                                'red')"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-red-50 hover:text-red-600 flex items-center justify-center">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =====================================================
     MODAL FORM TAMBAH / EDIT SEKOLAH
     ===================================================== -->
<div id="modalSekolah" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="font-bold text-slate-800 text-base">
                <i class="fa-solid fa-school text-brand-600 mr-2"></i><span id="judulFormSekolah">Tambah Sekolah</span>
            </h3>
            <button type="button" onclick="closeModal('modalSekolah')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <?php if ($errors) : ?>
            <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl mb-4">
                <p class="font-bold mb-1">Data belum bisa disimpan:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    <?php foreach ($errors as $e) : ?>
                        <li><?= esc($e) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="<?= site_url('sekolah/simpan') ?>" method="post" class="space-y-4 text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="id_sekolah" id="f_id_sekolah">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">NPSN <span class="text-red-500">*</span></label>
                    <input type="text" name="npsn" id="f_npsn" maxlength="8" required placeholder="8 karakter"
                        class="w-full border border-slate-300 rounded-lg p-2.5 font-mono uppercase">
                </div>
                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-slate-700">Nama Sekolah <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_sekolah" id="f_nama_sekolah" required placeholder="Contoh: SMAN 1 Kendari"
                        class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Jenjang <span class="text-red-500">*</span></label>
                    <select name="jenjang" id="f_jenjang" required class="w-full border border-slate-300 rounded-lg p-2.5">
                        <option value="">Pilih jenjang</option>
                        <option value="SMA">SMA</option>
                        <option value="SMK">SMK</option>
                        <option value="SLB">SLB</option>
                    </select>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Kabupaten/Kota <span class="text-red-500">*</span></label>
                    <select name="kabupaten_kota" id="f_kabupaten_kota" required class="w-full border border-slate-300 rounded-lg p-2.5">
                        <option value="">Pilih kab/kota</option>
                        <?php foreach ($kabKota as $k) : ?>
                            <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Kecamatan</label>
                    <input type="text" name="kecamatan" id="f_kecamatan" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
            </div>

            <div>
                <label class="font-semibold block mb-1 text-slate-700">Alamat</label>
                <textarea name="alamat" id="f_alamat" rows="2" class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Telepon</label>
                    <input type="text" name="telepon" id="f_telepon" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Email</label>
                    <input type="email" name="email" id="f_email" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="f_status" class="w-full border border-slate-300 rounded-lg p-2.5">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeModal('modalSekolah')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- =====================================================
     MODAL KONFIRMASI (nonaktifkan / aktifkan / hapus)
     ===================================================== -->
<div id="modalKonfirmasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div id="konfirmasiIkon" class="mx-auto w-14 h-14 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-triangle-exclamation text-2xl"></i>
        </div>
        <div>
            <h3 id="konfirmasiJudul" class="font-bold text-slate-800 text-base">Yakin?</h3>
            <p id="konfirmasiPesan" class="text-xs text-slate-500 mt-1 leading-relaxed"></p>
        </div>
        <form id="konfirmasiForm" method="post" class="flex justify-center space-x-2 pt-2">
            <?= csrf_field() ?>
            <button type="button" onclick="closeModal('modalKonfirmasi')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
            <button type="submit" id="konfirmasiTombol" class="px-4 py-2 text-white rounded-lg text-xs font-semibold">Ya, Lanjutkan</button>
        </form>
    </div>
</div>

<script>
    // Buka form: tanpa data = tambah baru, dengan data = edit
    function bukaFormSekolah(data = null) {
        const kolom = ['id_sekolah', 'npsn', 'nama_sekolah', 'jenjang', 'kabupaten_kota',
                       'kecamatan', 'alamat', 'telepon', 'email', 'status'];

        kolom.forEach(k => {
            const el = document.getElementById('f_' + k);
            if (el) el.value = data && data[k] ? data[k] : (k === 'status' ? 'aktif' : '');
        });

        document.getElementById('judulFormSekolah').innerText =
            data && data.id_sekolah ? 'Edit Sekolah' : 'Tambah Sekolah';

        openModal('modalSekolah');
    }

    // Modal konfirmasi untuk aksi status & hapus
    function konfirmasiAksi(url, judul, namaSekolah, warna) {
        document.getElementById('konfirmasiForm').action = url;
        document.getElementById('konfirmasiJudul').innerText = judul;
        document.getElementById('konfirmasiPesan').innerText = namaSekolah;

        const ikon = document.getElementById('konfirmasiIkon');
        const tombol = document.getElementById('konfirmasiTombol');
        const gaya = {
            red:   ['bg-red-100 text-red-600',     'bg-red-600 hover:bg-red-700'],
            amber: ['bg-amber-100 text-amber-600', 'bg-amber-500 hover:bg-amber-600'],
        }[warna];

        ikon.className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center ' + gaya[0];
        tombol.className = 'px-4 py-2 text-white rounded-lg text-xs font-semibold ' + gaya[1];

        openModal('modalKonfirmasi');
    }

    // Kalau simpan gagal validasi: buka lagi form dengan isian sebelumnya
    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', function() {
            bukaFormSekolah(<?= json_encode([
                'id_sekolah'     => old('id_sekolah'),
                'npsn'           => old('npsn'),
                'nama_sekolah'   => old('nama_sekolah'),
                'jenjang'        => old('jenjang'),
                'kabupaten_kota' => old('kabupaten_kota'),
                'kecamatan'      => old('kecamatan'),
                'alamat'         => old('alamat'),
                'telepon'        => old('telepon'),
                'email'          => old('email'),
                'status'         => old('status'),
            ]) ?>);
        });
    <?php endif; ?>
</script>

<!-- =====================================================
     MODAL IMPORT EXCEL
     ===================================================== -->
<div id="modalImport" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('sekolah/import') ?>" method="post" onsubmit="return kirimImport()"
        class="bg-white rounded-2xl max-w-5xl w-full shadow-2xl max-h-[92vh] flex flex-col text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="data" id="i_data">

        <div class="flex justify-between items-center border-b p-5">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-file-excel text-emerald-600 mr-2"></i>Import Data Sekolah dari Excel</h3>
            <button type="button" onclick="closeModal('modalImport')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            <!-- Langkah 1 & 2 -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-xl p-4 space-y-2">
                    <div class="font-bold text-slate-800"><span class="bg-emerald-600 text-white w-5 h-5 inline-flex items-center justify-center rounded-full mr-1.5 text-[10px]">1</span>Unduh template</div>
                    <p class="text-slate-500">Salin data sekolah ke template ini. Kolomnya sama dengan form Tambah Sekolah.</p>
                    <button type="button" onclick="unduhTemplate()" class="border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold px-3 py-2 rounded-lg">
                        <i class="fa-solid fa-download mr-1.5"></i>Unduh Template Excel
                    </button>
                </div>
                <div class="border rounded-xl p-4 space-y-2">
                    <div class="font-bold text-slate-800"><span class="bg-emerald-600 text-white w-5 h-5 inline-flex items-center justify-center rounded-full mr-1.5 text-[10px]">2</span>Pilih file yang sudah diisi</div>
                    <input type="file" id="i_file" accept=".xlsx,.xls,.csv" onchange="bacaExcel(this)"
                        class="w-full border border-slate-300 rounded-lg p-1.5 file:mr-2 file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:rounded">
                    <p class="text-slate-400 text-[10px]">Format .xlsx, .xls, atau .csv. Data dibaca dari lembar (sheet) pertama.</p>
                </div>
            </div>

            <!-- Pratinjau -->
            <div id="i_pratinjau" class="hidden space-y-3">
                <div class="flex flex-wrap items-center gap-2">
                    <span class="font-bold text-slate-800"><span class="bg-emerald-600 text-white w-5 h-5 inline-flex items-center justify-center rounded-full mr-1.5 text-[10px]">3</span>Pratinjau</span>
                    <span id="i_jmlBaru" class="bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full"></span>
                    <span id="i_jmlUbah" class="bg-blue-100 text-blue-700 font-bold px-2.5 py-1 rounded-full"></span>
                    <span id="i_jmlError" class="bg-red-100 text-red-700 font-bold px-2.5 py-1 rounded-full"></span>
                </div>
                <label class="flex items-center gap-2 cursor-pointer bg-blue-50 border border-blue-200 rounded-lg p-2.5">
                    <input type="checkbox" name="perbarui" value="1" id="i_perbarui" onchange="hitungRingkas()" class="accent-blue-600">
                    <span>Perbarui data sekolah yang <b>NPSN-nya sudah terdaftar</b> (kalau tidak dicentang, sekolah tersebut dilewati)</span>
                </label>
                <div class="border rounded-xl overflow-auto max-h-[45vh]">
                    <table class="w-full text-left text-[11px]">
                        <thead class="bg-slate-100 text-slate-600 uppercase text-[10px] sticky top-0">
                            <tr>
                                <th class="p-2">Baris</th><th class="p-2">Status</th><th class="p-2">NPSN</th><th class="p-2">Nama Sekolah</th>
                                <th class="p-2">Jenjang</th><th class="p-2">Kab/Kota</th><th class="p-2">Kecamatan</th><th class="p-2">Telepon</th>
                            </tr>
                        </thead>
                        <tbody id="i_isi" class="divide-y divide-slate-100"></tbody>
                    </table>
                </div>
                <p id="i_catatan" class="text-[10px] text-slate-400"></p>
            </div>
        </div>

        <div class="flex justify-between items-center gap-2 border-t p-5 bg-slate-50 rounded-b-2xl">
            <span class="text-[10px] text-slate-400">Baris yang error tidak ikut disimpan.</span>
            <div class="flex gap-2">
                <button type="button" onclick="closeModal('modalImport')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
                <button type="submit" id="i_tombol" disabled class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 disabled:opacity-40 disabled:cursor-not-allowed text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-file-import mr-1"></i> <span id="i_tombolTeks">Import</span>
                </button>
            </div>
        </div>
    </form>
</div>

<!-- SheetJS: membaca & membuat file Excel langsung di browser -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    const KAB_KOTA = <?= json_encode($kabKota) ?>;
    const NPSN_ADA = new Set(<?= json_encode(array_map('strval', $npsnAda)) ?>);
    const KOLOM = [
        ['npsn', 'NPSN'], ['nama_sekolah', 'Nama Sekolah'], ['jenjang', 'Jenjang'], ['kabupaten_kota', 'Kabupaten/Kota'],
        ['kecamatan', 'Kecamatan'], ['alamat', 'Alamat'], ['telepon', 'Telepon'], ['email', 'Email'], ['status', 'Status'],
    ];
    // Judul kolom lain yang juga dikenali (misalnya dari file Dinas)
    const ALIAS = {
        npsn: ['npsn'],
        nama_sekolah: ['namasekolah', 'namasatuanpendidikan', 'nama'],
        jenjang: ['jenjang', 'bentukpendidikan', 'bentuk'],
        kabupaten_kota: ['kabupatenkota', 'kabkota', 'kabupaten', 'kota'],
        kecamatan: ['kecamatan'],
        alamat: ['alamat'],
        telepon: ['telepon', 'nomortelepon', 'notelepon', 'telp'],
        email: ['email', 'emailsekolah'],
        status: ['status', 'statusaktif'],
    };
    let hasilBaca = [];

    function bukaImport() {
        document.getElementById('i_file').value = '';
        document.getElementById('i_pratinjau').classList.add('hidden');
        document.getElementById('i_tombol').disabled = true;
        document.getElementById('i_tombolTeks').textContent = 'Import';
        hasilBaca = [];
        openModal('modalImport');
    }

    // ---------- Template ----------
    function unduhTemplate() {
        const isi = [
            KOLOM.map(k => k[1]),
            ['40400123', 'SMAN 1 Kendari', 'SMA', 'Kota Kendari', 'Mandonga', 'Jl. Contoh No. 1', '0401-123456', 'sman1kendari@contoh.sch.id', 'Aktif'],
            ['40400456', 'SMKN 2 Kendari', 'SMK', 'Kota Kendari', 'Kadia', 'Jl. Contoh No. 2', '', '', 'Aktif'],
        ];
        const lembar = XLSX.utils.aoa_to_sheet(isi);
        lembar['!cols'] = [12, 32, 9, 22, 18, 36, 16, 28, 10].map(w => ({ wch: w }));

        const petunjuk = [
            ['PETUNJUK PENGISIAN'],
            ['• Hapus 2 baris contoh, lalu isi satu sekolah per baris mulai baris ke-2.'],
            ['• NPSN wajib 8 karakter dan tidak boleh ganda.'],
            ['• Kolom wajib: NPSN, Nama Sekolah, Jenjang, Kabupaten/Kota.'],
            ['• Status: Aktif atau Nonaktif (kosong = Aktif).'],
            [''],
            ['Jenjang yang diterima'], ['SMA'], ['SMK'], ['SLB (SMALB/SMKLB juga dikenali)'],
            [''],
            ['Kabupaten/Kota yang diterima'], ...KAB_KOTA.map(k => [k]),
        ];
        const lembarPetunjuk = XLSX.utils.aoa_to_sheet(petunjuk);
        lembarPetunjuk['!cols'] = [{ wch: 70 }];

        const buku = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(buku, lembar, 'Data Sekolah');
        XLSX.utils.book_append_sheet(buku, lembarPetunjuk, 'Petunjuk');
        XLSX.writeFile(buku, 'template-data-sekolah.xlsx');
    }

    // ---------- Normalisasi (sama dengan di server) ----------
    const inti = t => String(t || '').toLowerCase().trim().replace(/^(kabupaten|kab\.?|kota)\s*/, '').replace(/[^a-z]/g, '');
    function normalisasiKab(teks) {
        const cocok = KAB_KOTA.filter(k => inti(k) === inti(teks));
        if (cocok.length > 1) {
            const kota = /^\s*kota\b/i.test(teks);
            return cocok.find(k => k.startsWith('Kota') === kota) || cocok[0];
        }
        return cocok[0] || null;
    }
    function normalisasiJenjang(teks) {
        const t = String(teks || '').toUpperCase().replace(/[^A-Z]/g, '');
        if (!t) return null;
        if (t.includes('SLB') || t.includes('LB') || t.includes('LUARBIASA')) return 'SLB';
        if (t.startsWith('SMK')) return 'SMK';
        if (t.startsWith('SMA')) return 'SMA';
        return null;
    }

    // ---------- Baca file ----------
    function bacaExcel(input) {
        const file = input.files[0];
        if (!file) return;
        const pembaca = new FileReader();
        pembaca.onload = e => {
            try {
                const buku = XLSX.read(new Uint8Array(e.target.result), { type: 'array' });
                const baris = XLSX.utils.sheet_to_json(buku.Sheets[buku.SheetNames[0]], { header: 1, raw: false, defval: '' });
                olahBaris(baris);
            } catch (err) {
                showToast('File tidak bisa dibaca', 'Pastikan file berformat Excel (.xlsx/.xls) atau CSV.', 'error');
            }
        };
        pembaca.readAsArrayBuffer(file);
    }

    function olahBaris(baris) {
        // Cari baris judul (yang memuat kolom NPSN)
        const idxJudul = baris.findIndex(b => b.some(sel => String(sel).toLowerCase().replace(/[^a-z]/g, '') === 'npsn'));
        if (idxJudul < 0) {
            showToast('Kolom NPSN tidak ditemukan', 'Gunakan template Excel dari tombol Unduh Template.', 'error');
            return;
        }
        const judul = baris[idxJudul].map(sel => String(sel).toLowerCase().replace(/[^a-z]/g, ''));
        const posisi = {};
        Object.entries(ALIAS).forEach(([kunci, daftar]) => {
            const i = judul.findIndex(j => daftar.includes(j));
            if (i >= 0) posisi[kunci] = i;
        });

        const dilihat = {};
        hasilBaca = [];
        baris.slice(idxJudul + 1).forEach((b, i) => {
            if (!b.some(sel => String(sel).trim() !== '')) return; // lewati baris kosong
            const nomor = idxJudul + i + 2;
            const r = { baris: nomor };
            KOLOM.forEach(([kunci]) => r[kunci] = posisi[kunci] !== undefined ? String(b[posisi[kunci]] ?? '').trim() : '');
            r.npsn = r.npsn.replace(/\s+/g, '').toUpperCase();

            const alasan = [];
            if (!/^[0-9A-Z]{8}$/.test(r.npsn)) alasan.push('NPSN harus 8 karakter');
            if (!r.nama_sekolah) alasan.push('nama kosong');
            const jenjang = normalisasiJenjang(r.jenjang);
            if (!jenjang) alasan.push('jenjang tidak dikenali');
            const kab = normalisasiKab(r.kabupaten_kota);
            if (!kab) alasan.push('kab/kota tidak dikenali');
            if (dilihat[r.npsn]) alasan.push('NPSN ganda (baris ' + dilihat[r.npsn] + ')');
            if (!alasan.length) dilihat[r.npsn] = nomor;

            hasilBaca.push({ ...r, jenjang_ok: jenjang, kab_ok: kab, alasan, ada: NPSN_ADA.has(r.npsn) });
        });

        tampilPratinjau();
    }

    function tampilPratinjau() {
        const isi = document.getElementById('i_isi');
        isi.innerHTML = '';
        const sel = (teks, kelas = '') => {
            const td = document.createElement('td');
            td.className = 'p-2 ' + kelas;
            td.textContent = teks || '–';
            return td;
        };

        hasilBaca.slice(0, 500).forEach(r => {
            const tr = document.createElement('tr');
            tr.className = r.alasan.length ? 'bg-red-50/60' : '';
            const status = document.createElement('td');
            status.className = 'p-2';
            const badge = document.createElement('span');
            badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap ' +
                (r.alasan.length ? 'bg-red-100 text-red-700' : (r.ada ? 'bg-blue-100 text-blue-700' : 'bg-emerald-100 text-emerald-800'));
            badge.textContent = r.alasan.length ? 'Error' : (r.ada ? 'Perbarui' : 'Baru');
            status.appendChild(badge);
            if (r.alasan.length) {
                const ket = document.createElement('div');
                ket.className = 'text-[10px] text-red-600 mt-0.5';
                ket.textContent = r.alasan.join(', ');
                status.appendChild(ket);
            }
            tr.append(
                sel(String(r.baris), 'text-slate-400'), status, sel(r.npsn, 'font-mono'), sel(r.nama_sekolah, 'font-semibold text-slate-800'),
                sel(r.jenjang_ok || r.jenjang, r.jenjang_ok ? '' : 'text-red-600'),
                sel(r.kab_ok || r.kabupaten_kota, r.kab_ok ? '' : 'text-red-600'),
                sel(r.kecamatan), sel(r.telepon)
            );
            isi.appendChild(tr);
        });

        document.getElementById('i_catatan').textContent = hasilBaca.length > 500
            ? 'Menampilkan 500 baris pertama dari ' + hasilBaca.length + ' baris. Semua baris tetap diproses saat import.'
            : hasilBaca.length + ' baris terbaca.';
        document.getElementById('i_pratinjau').classList.remove('hidden');
        hitungRingkas();
    }

    function hitungRingkas() {
        const valid = hasilBaca.filter(r => !r.alasan.length);
        const baru = valid.filter(r => !r.ada).length;
        const ubah = valid.filter(r => r.ada).length;
        const error = hasilBaca.length - valid.length;
        const perbarui = document.getElementById('i_perbarui').checked;

        document.getElementById('i_jmlBaru').textContent = baru + ' baru';
        document.getElementById('i_jmlUbah').textContent = ubah + (perbarui ? ' diperbarui' : ' sudah ada (dilewati)');
        document.getElementById('i_jmlError').textContent = error + ' error';

        const jumlah = baru + (perbarui ? ubah : 0);
        document.getElementById('i_tombol').disabled = jumlah === 0;
        document.getElementById('i_tombolTeks').textContent = 'Import ' + jumlah + ' sekolah';
    }

    function kirimImport() {
        const valid = hasilBaca.filter(r => !r.alasan.length);
        if (!valid.length) return false;
        // Kirim data mentah; server memeriksa & menormalkan ulang
        document.getElementById('i_data').value = JSON.stringify(valid.map(r => ({
            baris: r.baris, npsn: r.npsn, nama_sekolah: r.nama_sekolah, jenjang: r.jenjang, kabupaten_kota: r.kabupaten_kota,
            kecamatan: r.kecamatan, alamat: r.alamat, telepon: r.telepon, email: r.email, status: r.status,
        })));
        document.getElementById('i_tombol').disabled = true;
        document.getElementById('i_tombolTeks').textContent = 'Menyimpan...';
        return true;
    }
</script>
<?= $this->endSection() ?>