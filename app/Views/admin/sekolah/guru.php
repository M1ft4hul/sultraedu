<?php

/** @var array $guru */
/** @var array $filter */
/** @var array $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors   = session()->getFlashdata('errors') ?? [];
$akunBaru = session()->getFlashdata('akunBaru');
$kartu = [
    ['Total Guru', $ringkas['total'], 'fa-chalkboard-user', 'bg-blue-50 text-blue-600'],
    ['Guru Aktif', $ringkas['aktif'], 'fa-user-check', 'bg-emerald-50 text-emerald-600'],
    ['Sudah Punya Akun', $ringkas['punyaAkun'], 'fa-key', 'bg-indigo-50 text-indigo-600'],
    ['Belum Punya Akun', $ringkas['belumAkun'], 'fa-user-lock', 'bg-amber-50 text-amber-600'],
];
?>
<section class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Data Guru</h2>
            <p class="text-xs text-slate-500">Kelola data guru di sekolah Anda dan buatkan akun supaya guru bisa masuk ke dashboard guru.</p>
        </div>
        <button type="button" onclick="bukaFormGuru()"
            class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-user-plus mr-2"></i>Tambah Guru
        </button>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($kartu as [$judul, $angka, $ikon, $warna]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $angka ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

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

    <!-- Info akun (password tampil sekali) -->
    <?php if ($akunBaru) : ?>
        <div class="bg-white border-2 border-emerald-300 rounded-2xl p-5 shadow-sm text-xs space-y-3">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-key text-emerald-600 mr-2"></i><?= $akunBaru['jenis'] === 'reset' ? 'Password Berhasil Direset' : 'Akun Guru Berhasil Dibuat' ?></h3>
                    <p class="text-[11px] text-amber-700 mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Password hanya ditampilkan sekali. Salin dan berikan kepada guru yang bersangkutan.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <button type="button" id="tombolBagikan" onclick="bagikanPdf(this)" class="hidden bg-green-600 hover:bg-green-700 text-white font-semibold px-4 py-2 rounded-lg">
                        <i class="fa-brands fa-whatsapp mr-2"></i>Bagikan
                    </button>
                    <button type="button" onclick="unduhPdf(this)" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg">
                        <i class="fa-solid fa-file-pdf mr-2"></i>Unduh PDF
                    </button>
                    <button type="button" onclick="salinAkun(this)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg">
                        <i class="fa-regular fa-copy mr-2"></i>Salin Teks
                    </button>
                </div>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase">Nama</div>
                    <div class="font-semibold text-slate-800"><?= esc($akunBaru['nama']) ?></div>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase">Username</div>
                    <div class="font-mono font-semibold text-slate-800"><?= esc($akunBaru['username']) ?></div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                    <div class="text-[10px] text-emerald-600 uppercase">Password</div>
                    <div class="font-mono font-bold text-emerald-800"><?= esc($akunBaru['password']) ?></div>
                </div>
            </div>
            <textarea id="teksAkun" class="hidden"><?= esc(
                                                        "Akun EDUVATION - Guru\n" .
                                                            "Nama     : {$akunBaru['nama']}\n" .
                                                            "Username : {$akunBaru['username']}\n" .
                                                            "Password : {$akunBaru['password']}\n" .
                                                            "Login di : " . site_url('login') . "\n\n" .
                                                            "Segera ganti password setelah login pertama melalui menu Profil Saya."
                                                    ) ?></textarea>
        </div>
    <?php endif; ?>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

        <!-- Filter -->
        <form method="get" action="<?= site_url('guru') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
            <input type="text" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari nama, NIP, atau mata pelajaran..."
                class="md:col-span-2 border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <select name="akun" class="border border-slate-300 rounded-lg p-2.5">
                <option value="">Semua akun</option>
                <option value="ada" <?= $filter['akun'] === 'ada' ? 'selected' : '' ?>>Sudah punya akun</option>
                <option value="belum" <?= $filter['akun'] === 'belum' ? 'selected' : '' ?>>Belum punya akun</option>
            </select>
            <div class="flex gap-2">
                <select name="status" class="flex-1 border border-slate-300 rounded-lg p-2.5">
                    <option value="">Semua status</option>
                    <option value="aktif" <?= $filter['status'] === 'aktif' ? 'selected' : '' ?>>Aktif</option>
                    <option value="nonaktif" <?= $filter['status'] === 'nonaktif' ? 'selected' : '' ?>>Nonaktif</option>
                </select>
                <button type="submit" title="Terapkan" class="bg-slate-800 hover:bg-slate-700 text-white px-3 rounded-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
                <a href="<?= site_url('guru') ?>" title="Reset" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-3 rounded-lg flex items-center"><i class="fa-solid fa-rotate-left"></i></a>
            </div>
        </form>

        <?php if (empty($guru)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-blue-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-chalkboard-user text-2xl text-blue-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700"><?= array_filter($filter) ? 'Tidak ada guru yang cocok' : 'Belum ada data guru' ?></p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">
                    <?= array_filter($filter) ? 'Coba ubah kata kunci atau filter.' : 'Tambahkan guru pertama dengan tombol "Tambah Guru" di atas.' ?>
                </p>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 rounded-l-lg">Nama Guru</th>
                            <th class="p-3.5">NIP</th>
                            <th class="p-3.5">Mata Pelajaran</th>
                            <th class="p-3.5">Akun Login</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($guru as $g) : ?>
                            <?php $punyaAkun = $g['username'] !== null; ?>
                            <tr class="hover:bg-slate-50 transition <?= $g['status'] === 'nonaktif' ? 'opacity-60' : '' ?>">
                                <td class="p-3.5 font-bold text-slate-800"><?= esc($g['nama_guru']) ?></td>
                                <td class="p-3.5 font-mono"><?= esc($g['nip'] ?: '-') ?></td>
                                <td class="p-3.5"><?= esc($g['mapel'] ?: '-') ?></td>
                                <td class="p-3.5">
                                    <?php if ($punyaAkun) : ?>
                                        <span class="font-mono text-slate-700">@<?= esc($g['username']) ?></span>
                                    <?php else : ?>
                                        <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-solid fa-user-lock mr-1"></i>Belum punya akun</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5">
                                    <?php if ($g['status'] === 'aktif') : ?>
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full">Aktif</span>
                                    <?php else : ?>
                                        <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5">
                                    <div class="flex justify-center gap-1.5">
                                        <button type="button" title="<?= $punyaAkun ? 'Reset password' : 'Buat akun' ?>"
                                            data-guru="<?= esc(json_encode($g), 'attr') ?>"
                                            onclick="bukaFormAkun(JSON.parse(this.dataset.guru))"
                                            class="h-8 px-2.5 rounded-lg text-[11px] font-semibold flex items-center whitespace-nowrap
                                                   <?= $punyaAkun ? 'border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600' : 'bg-amber-500 hover:bg-amber-600 text-white' ?>">
                                            <i class="fa-solid <?= $punyaAkun ? 'fa-key' : 'fa-user-plus' ?> mr-1.5"></i><?= $punyaAkun ? 'Reset' : 'Buat Akun' ?>
                                        </button>
                                        <button type="button" title="Edit data" data-guru="<?= esc(json_encode($g), 'attr') ?>"
                                            onclick="bukaFormGuru(JSON.parse(this.dataset.guru))"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                        <button type="button" title="<?= $g['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                            onclick="konfirmasi('<?= site_url('guru/status/' . $g['id_guru']) ?>', '<?= $g['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> guru?', <?= esc(json_encode($g['nama_guru']), 'attr') ?>, 'amber')"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center">
                                            <i class="fa-solid <?= $g['status'] === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?> text-[12px]"></i>
                                        </button>
                                        <button type="button" title="Hapus"
                                            onclick="konfirmasi('<?= site_url('guru/hapus/' . $g['id_guru']) ?>', 'Hapus data guru?', <?= esc(json_encode($g['nama_guru']), 'attr') ?>, 'red')"
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
     MODAL TAMBAH / EDIT DATA GURU
     ===================================================== -->
<div id="modalGuru" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('guru/simpan') ?>" method="post" autocomplete="off"
        class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto space-y-4 text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_guru" id="g_id">

        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-chalkboard-user text-brand-600 mr-2"></i><span id="g_judul">Tambah Guru</span></h3>
            <button type="button" onclick="closeModal('modalGuru')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <?php if ($errors) : ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside"><?php foreach ($errors as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <div>
            <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
            <input type="text" name="nama_guru" id="g_nama_guru" required placeholder="Beserta gelar, misalnya: Siti Rahma, S.Pd." class="w-full border border-slate-300 rounded-lg p-2.5">
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">NIP</label>
                <input type="text" name="nip" id="g_nip" inputmode="numeric" placeholder="Kosongkan jika belum ada" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Mata Pelajaran</label>
                <input type="text" name="mapel" id="g_mapel" class="w-full border border-slate-300 rounded-lg p-2.5">
            </div>
        </div>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Status</label>
            <select name="status" id="g_status" class="w-full border border-slate-300 rounded-lg p-2.5">
                <option value="aktif">Aktif</option>
                <option value="nonaktif">Nonaktif</option>
            </select>
        </div>

        <!-- Sekalian buat akun (hanya saat tambah baru) -->
        <div id="g_blokAkun" class="border border-dashed border-indigo-300 rounded-xl p-4 space-y-3 bg-indigo-50/40">
            <label class="flex items-center gap-2 font-semibold text-slate-700 cursor-pointer">
                <input type="checkbox" name="buat_akun" value="1" id="g_buatAkun" onchange="toggleAkun()" class="w-4 h-4 accent-brand-600">
                Sekalian buatkan akun login
            </label>
            <div id="g_isianAkun" class="hidden space-y-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Username</label>
                    <input type="text" name="username" id="g_username" placeholder="contoh: siti.rahma" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono lowercase">
                    <p class="text-[10px] text-slate-400 mt-1">Huruf kecil, angka, titik, garis bawah, atau tanda hubung.</p>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-semibold text-slate-700">Password</label>
                        <button type="button" onclick="buatSandi('g_password')" class="text-[10px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis</button>
                    </div>
                    <input type="password" name="password" id="g_password" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2 border-t pt-3">
            <button type="button" onclick="closeModal('modalGuru')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL BUAT AKUN / RESET PASSWORD
     ===================================================== -->
<div id="modalAkun" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form id="formAkun" method="post" autocomplete="off" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <?= csrf_field() ?>
        <div class="flex justify-between items-start border-b pb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-key text-indigo-500 mr-2"></i><span id="a_judul">Buat Akun Guru</span></h3>
                <p id="a_nama" class="text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalAkun')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div id="a_blokUsername">
            <label class="font-semibold block mb-1 text-slate-700">Username</label>
            <input type="text" name="username" id="a_username" placeholder="contoh: siti.rahma" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono lowercase">
            <p class="text-[10px] text-slate-400 mt-1">Huruf kecil, angka, titik, garis bawah, atau tanda hubung.</p>
        </div>
        <div id="a_infoUsername" class="hidden bg-slate-50 rounded-lg p-3">
            <div class="text-[10px] text-slate-400 uppercase">Username</div>
            <div id="a_usernameLama" class="font-mono font-semibold text-slate-800"></div>
        </div>
        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="font-semibold text-slate-700">Password <span id="a_labelBaru">Baru</span></label>
                <button type="button" onclick="buatSandi('a_password')" class="text-[10px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis</button>
            </div>
            <input type="password" name="password" id="a_password" required minlength="8" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
            <p class="text-[10px] text-slate-400 mt-1">Minimal 8 karakter.</p>
        </div>
        <div class="flex justify-end space-x-2 border-t pt-3">
            <button type="button" onclick="closeModal('modalAkun')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> <span id="a_tombol">Buat Akun</span></button>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL KONFIRMASI (status / hapus)
     ===================================================== -->
<div id="modalKonfirmasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div id="konfirmasiIkon" class="mx-auto w-14 h-14 rounded-full flex items-center justify-center"><i class="fa-solid fa-triangle-exclamation text-2xl"></i></div>
        <div>
            <h3 id="konfirmasiJudul" class="font-bold text-slate-800 text-base"></h3>
            <p id="konfirmasiPesan" class="text-xs text-slate-500 mt-1"></p>
        </div>
        <form id="konfirmasiForm" method="post" class="flex justify-center space-x-2 pt-2">
            <?= csrf_field() ?>
            <button type="button" onclick="closeModal('modalKonfirmasi')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
            <button type="submit" id="konfirmasiTombol" class="px-4 py-2 text-white rounded-lg text-xs font-semibold">Ya, Lanjutkan</button>
        </form>
    </div>
</div>

<script>
    // ---------- Form data guru ----------
    function bukaFormGuru(d = null) {
        const edit = d && d.id_guru;
        document.getElementById('g_id').value = edit ? d.id_guru : '';
        ['nama_guru', 'nip', 'mapel'].forEach(k => document.getElementById('g_' + k).value = d?.[k] || '');
        document.getElementById('g_status').value = d?.status || 'aktif';
        document.getElementById('g_judul').innerText = edit ? 'Edit Data Guru' : 'Tambah Guru';

        // Pilihan buat akun hanya saat menambah guru baru
        document.getElementById('g_blokAkun').classList.toggle('hidden', !!edit);
        document.getElementById('g_buatAkun').checked = !!d?.buat_akun;
        document.getElementById('g_username').value = d?.username_baru || '';
        document.getElementById('g_password').value = '';
        toggleAkun();

        openModal('modalGuru');
    }

    function toggleAkun() {
        const aktif = document.getElementById('g_buatAkun').checked;
        document.getElementById('g_isianAkun').classList.toggle('hidden', !aktif);
        document.getElementById('g_username').required = aktif;
        document.getElementById('g_password').required = aktif;
    }

    // ---------- Form buat akun / reset password ----------
    function bukaFormAkun(g) {
        const punya = !!g.username;
        document.getElementById('formAkun').action = '<?= site_url('guru/akun') ?>/' + g.id_guru;
        document.getElementById('a_judul').innerText = punya ? 'Reset Password Guru' : 'Buat Akun Guru';
        document.getElementById('a_tombol').innerText = punya ? 'Reset Password' : 'Buat Akun';
        document.getElementById('a_labelBaru').classList.toggle('hidden', !punya);
        document.getElementById('a_nama').textContent = g.nama_guru;

        document.getElementById('a_blokUsername').classList.toggle('hidden', punya);
        document.getElementById('a_infoUsername').classList.toggle('hidden', !punya);
        document.getElementById('a_username').required = !punya;
        document.getElementById('a_username').value = '';
        document.getElementById('a_usernameLama').textContent = punya ? '@' + g.username : '';

        const pass = document.getElementById('a_password');
        pass.value = '';
        pass.type = 'password';

        openModal('modalAkun');
    }

    // Password acak 10 karakter tanpa huruf yang mudah tertukar
    function buatSandi(id) {
        const huruf = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        const acak = new Uint32Array(10);
        crypto.getRandomValues(acak);
        const input = document.getElementById(id);
        input.value = Array.from(acak, n => huruf[n % huruf.length]).join('');
        input.type = 'text';
    }

    function salinAkun(tombol) {
        navigator.clipboard.writeText(document.getElementById('teksAkun').value).then(() => {
            tombol.innerHTML = '<i class="fa-solid fa-check mr-2"></i>Tersalin!';
            setTimeout(() => tombol.innerHTML = '<i class="fa-regular fa-copy mr-2"></i>Salin Info Akun', 2000);
        });
    }

    // ---------- Konfirmasi ----------
    function konfirmasi(url, judul, nama, warna) {
        const gaya = {
            red: ['bg-red-100 text-red-600', 'bg-red-600 hover:bg-red-700'],
            amber: ['bg-amber-100 text-amber-600', 'bg-amber-500 hover:bg-amber-600'],
        } [warna];
        document.getElementById('konfirmasiForm').action = url;
        document.getElementById('konfirmasiJudul').innerText = judul;
        document.getElementById('konfirmasiPesan').innerText = nama;
        document.getElementById('konfirmasiIkon').className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center ' + gaya[0];
        document.getElementById('konfirmasiTombol').className = 'px-4 py-2 text-white rounded-lg text-xs font-semibold ' + gaya[1];
        openModal('modalKonfirmasi');
    }

    // Kalau simpan gagal: buka lagi form dengan isian sebelumnya (tanpa password)
    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', () => bukaFormGuru(<?= json_encode([
                                                                                'id_guru'       => old('id_guru'),
                                                                                'nama_guru'     => old('nama_guru'),
                                                                                'nip'           => old('nip'),
                                                                                'mapel'         => old('mapel'),
                                                                                'status'        => old('status'),
                                                                                'buat_akun'     => (bool) old('buat_akun'),
                                                                                'username_baru' => old('username'),
                                                                            ]) ?>));
    <?php endif; ?>
</script>

<?php if ($akunBaru) : ?>
    <!-- jsPDF: membuat PDF langsung di browser -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script>
        const AKUN = <?= json_encode([
                            'jenis'    => $akunBaru['jenis'],
                            'nama'     => $akunBaru['nama'],
                            'nip'      => $akunBaru['nip'] ?? '',
                            'sekolah'  => $akunBaru['sekolah'] ?? '-',
                            'username' => $akunBaru['username'],
                            'password' => $akunBaru['password'],
                            'url'      => site_url('login'),
                            'tanggal'  => date('d/m/Y H:i'),
                            'logo'     => base_url('dashboard/Pemprov Sultra.png'),
                        ]) ?>;
        const NAMA_FILE = 'akun-eduvation-' + AKUN.username + '.pdf';

        // Tombol "Bagikan" hanya muncul di perangkat yang bisa membagikan file (umumnya HP)
        document.addEventListener('DOMContentLoaded', () => {
            try {
                const uji = new File([''], 'uji.pdf', {
                    type: 'application/pdf'
                });
                if (navigator.canShare && navigator.canShare({
                        files: [uji]
                    })) {
                    document.getElementById('tombolBagikan').classList.remove('hidden');
                }
            } catch (e) {}
        });

        // Ubah logo menjadi data gambar supaya bisa dimasukkan ke PDF
        function muatLogo(src) {
            return new Promise(resolve => {
                const img = new Image();
                img.onload = () => {
                    const c = document.createElement('canvas');
                    c.width = img.naturalWidth;
                    c.height = img.naturalHeight;
                    c.getContext('2d').drawImage(img, 0, 0);
                    resolve(c.toDataURL('image/png'));
                };
                img.onerror = () => resolve(null);
                img.src = src;
            });
        }

        // Susun kartu akun berbentuk struk (lebar 80 mm)
        async function buatPdf() {
            const {
                jsPDF
            } = window.jspdf;
            const doc = new jsPDF({
                unit: 'mm',
                format: [80, 165]
            });
            const tengah = 40,
                kiri = 7,
                lebar = 66;
            let y = 8;

            const garis = () => {
                doc.setDrawColor(160);
                doc.setLineDashPattern([1, 1], 0);
                doc.line(kiri, y, kiri + lebar, y);
                doc.setLineDashPattern([], 0);
                y += 5;
            };
            const baris = (label, nilai) => {
                doc.setFont('helvetica', 'normal').setFontSize(7).setTextColor(120);
                doc.text(label, kiri, y);
                y += 4;
                doc.setFont('helvetica', 'bold').setFontSize(9).setTextColor(20);
                const teks = doc.splitTextToSize(nilai || '-', lebar);
                doc.text(teks, kiri, y);
                y += teks.length * 4 + 2;
            };

            // Kop
            const logo = await muatLogo(AKUN.logo);
            if (logo) {
                doc.addImage(logo, 'PNG', tengah - 8, y, 16, 16);
                y += 19;
            }
            doc.setFont('helvetica', 'bold').setFontSize(14).setTextColor(20);
            doc.text('EDUVATION', tengah, y, {
                align: 'center'
            });
            y += 4.5;
            doc.setFont('helvetica', 'normal').setFontSize(7).setTextColor(90);
            doc.text('Dinas Pendidikan & Kebudayaan', tengah, y, {
                align: 'center'
            });
            y += 3.5;
            doc.text('Provinsi Sulawesi Tenggara', tengah, y, {
                align: 'center'
            });
            y += 5;
            garis();

            doc.setFont('helvetica', 'bold').setFontSize(10).setTextColor(20);
            doc.text(AKUN.jenis === 'reset' ? 'RESET PASSWORD AKUN GURU' : 'KARTU AKUN GURU', tengah, y, {
                align: 'center'
            });
            y += 7;

            baris('Nama', AKUN.nama);
            if (AKUN.nip) baris('NIP', AKUN.nip);
            baris('Sekolah', AKUN.sekolah);
            baris('Username', AKUN.username);

            // Password dalam kotak
            doc.setFont('helvetica', 'normal').setFontSize(7).setTextColor(120);
            doc.text('Password', kiri, y);
            y += 2;
            doc.setFillColor(236, 253, 245).setDrawColor(110, 231, 183);
            doc.roundedRect(kiri, y, lebar, 13, 2, 2, 'FD');
            doc.setFont('courier', 'bold').setFontSize(15).setTextColor(6, 95, 70);
            doc.text(AKUN.password, tengah, y + 8.5, {
                align: 'center'
            });
            y += 19;

            garis();

            doc.setFont('helvetica', 'normal').setFontSize(6.5).setTextColor(100);
            const pesan = doc.splitTextToSize(
                'Jaga kerahasiaan password ini. Segera ganti password setelah login pertama melalui menu Profil Saya.', lebar);
            doc.text(pesan, tengah, y, {
                align: 'center'
            });
            y += pesan.length * 3 + 3;
            doc.text('Dibuat: ' + AKUN.tanggal, tengah, y, {
                align: 'center'
            });

            return doc;
        }

        async function unduhPdf(tombol) {
            const awal = tombol.innerHTML;
            tombol.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Menyiapkan...';
            try {
                (await buatPdf()).save(NAMA_FILE);
            } catch (e) {
                showToast('PDF gagal dibuat', 'Periksa koneksi internet, lalu coba lagi.', 'error');
            }
            tombol.innerHTML = awal;
        }

        async function bagikanPdf(tombol) {
            const awal = tombol.innerHTML;
            tombol.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Menyiapkan...';
            try {
                const blob = (await buatPdf()).output('blob');
                const file = new File([blob], NAMA_FILE, {
                    type: 'application/pdf'
                });
                await navigator.share({
                    files: [file],
                    title: 'Akun EDUVATION ' + AKUN.nama
                });
            } catch (e) {
                // Dibatalkan pengguna atau tidak didukung: tidak perlu pesan
            }
            tombol.innerHTML = awal;
        }
    </script>
<?php endif; ?>
<?= $this->endSection() ?>