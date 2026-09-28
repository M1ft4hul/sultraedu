<?php

/** @var array $sekolah */
/** @var array $filter */
/** @var array $kabKota */
/** @var array $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$badgeJenjang = [
    'SMA' => 'bg-blue-50 text-blue-700',
    'SMK' => 'bg-indigo-50 text-indigo-700',
    'SLB' => 'bg-purple-50 text-purple-700',
];
$errors = session()->getFlashdata('errors') ?? [];
?>
<section class="space-y-6">

    <!-- Judul & tombol tambah -->
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Data Sekolah</h2>
            <p class="text-xs text-slate-500">Data master satuan pendidikan SMA, SMK, dan SLB se-Provinsi Sulawesi Tenggara.</p>
        </div>
        <button type="button" onclick="bukaFormSekolah()"
            class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-plus mr-2"></i>Tambah Sekolah
        </button>
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
            'kecamatan', 'alamat', 'telepon', 'email', 'status'
        ];

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
            red: ['bg-red-100 text-red-600', 'bg-red-600 hover:bg-red-700'],
            amber: ['bg-amber-100 text-amber-600', 'bg-amber-500 hover:bg-amber-600'],
        } [warna];

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
<?= $this->endSection() ?>