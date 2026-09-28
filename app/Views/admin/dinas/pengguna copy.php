<?php

/** @var array $pengguna */
/** @var array $filter */
/** @var array $daftarSekolah */
/** @var array $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php $errors = session()->getFlashdata('errors') ?? []; ?>
<section class="space-y-6">

    <!-- Judul & tombol tambah -->
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Akun Admin Sekolah</h2>
            <p class="text-xs text-slate-500">Kelola akun Admin Sekolah yang bertugas memverifikasi praktik baik dan mengelola akun guru di sekolahnya.</p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('pengguna/export') . '?' . http_build_query($filter) ?>"
                class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
                <i class="fa-solid fa-file-excel mr-2"></i>Unduh Excel
            </a>
            <button type="button" onclick="bukaFormPengguna()"
                class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
                <i class="fa-solid fa-user-plus mr-2"></i>Tambah Admin Sekolah
            </button>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Total Akun</div>
            <div class="text-xl font-bold text-slate-800"><?= number_format($ringkas['total'], 0, ',', '.') ?></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Akun Aktif</div>
            <div class="text-xl font-bold text-emerald-600"><?= number_format($ringkas['aktif'], 0, ',', '.') ?></div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm">
            <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Sekolah Belum Punya Admin</div>
            <div class="text-xl font-bold <?= $ringkas['tanpaAdmin'] > 0 ? 'text-amber-600' : 'text-slate-400' ?>">
                <?= number_format($ringkas['tanpaAdmin'], 0, ',', '.') ?>
            </div>
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


    <!-- Info akun baru / password direset (tampil SEKALI) -->
    <?php $akunBaru = session()->getFlashdata('akunBaru'); ?>
    <?php if ($akunBaru) : ?>
        <div class="bg-white border-2 border-emerald-300 rounded-2xl p-5 shadow-sm">
            <div class="flex flex-wrap justify-between items-start gap-3 mb-3">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm flex items-center">
                        <i class="fa-solid fa-key text-emerald-600 mr-2"></i>
                        <?= $akunBaru['jenis'] === 'reset' ? 'Password Berhasil Direset' : 'Akun Berhasil Dibuat' ?>
                    </h3>
                    <p class="text-[11px] text-amber-700 mt-1">
                        <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                        Password hanya ditampilkan sekali. Salin dan kirimkan ke pihak sekolah sekarang.
                    </p>
                </div>
                <button type="button" onclick="salinAkun(this)"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2 rounded-lg flex items-center">
                    <i class="fa-regular fa-copy mr-2"></i>Salin Info Akun
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Nama</div>
                    <div class="font-semibold text-slate-800"><?= esc($akunBaru['nama']) ?></div>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Sekolah</div>
                    <div class="font-semibold text-slate-800"><?= esc($akunBaru['sekolah']) ?></div>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase tracking-wider">Username</div>
                    <div class="font-mono font-semibold text-slate-800"><?= esc($akunBaru['username']) ?></div>
                </div>
                <div class="bg-emerald-50 rounded-lg p-3 border border-emerald-200">
                    <div class="text-[10px] text-emerald-600 uppercase tracking-wider">Password</div>
                    <div class="font-mono font-bold text-emerald-800"><?= esc($akunBaru['password']) ?></div>
                </div>
            </div>
            <!-- Teks yang disalin ke clipboard -->
            <textarea id="teksAkun" class="hidden"><?= esc(
                                                        "Akun EDUVATION - Admin Sekolah\n" .
                                                            "Nama     : {$akunBaru['nama']}\n" .
                                                            "Sekolah  : {$akunBaru['sekolah']}\n" .
                                                            "Username : {$akunBaru['username']}\n" .
                                                            "Password : {$akunBaru['password']}\n" .
                                                            "Login di : {$akunBaru['url']}\n\n" .
                                                            "Mohon jaga kerahasiaan password ini."
                                                    ) ?></textarea>
        </div>
    <?php endif; ?>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

        <!-- Pencarian & filter -->
        <form method="get" action="<?= site_url('pengguna') ?>" class="grid grid-cols-1 md:grid-cols-4 gap-3 text-xs">
            <input type="text" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari nama, username, atau sekolah..."
                class="md:col-span-2 border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <select name="sekolah" class="border border-slate-300 rounded-lg p-2.5">
                <option value="">Semua Sekolah</option>
                <?php foreach ($daftarSekolah as $s) : ?>
                    <option value="<?= $s['id_sekolah'] ?>" <?= $filter['sekolah'] == $s['id_sekolah'] ? 'selected' : '' ?>>
                        <?= esc($s['nama_sekolah']) ?>
                    </option>
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
                <a href="<?= site_url('pengguna') ?>" title="Reset filter" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-3 rounded-lg flex items-center">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>

        <!-- Tabel -->
        <?php if (empty($pengguna)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-users-gear text-2xl text-brand-500"></i>
                    </div>
                </div>
                <?php if (array_filter($filter)) : ?>
                    <p class="text-sm font-semibold text-slate-700">Tidak ada akun yang cocok</p>
                    <p class="text-xs text-slate-400 mt-1">Coba ubah kata kunci atau filter pencarian.</p>
                <?php else : ?>
                    <p class="text-sm font-semibold text-slate-700">Belum ada akun Admin Sekolah</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Buat akun pertama dengan tombol "Tambah Admin Sekolah" di atas.</p>
                <?php endif; ?>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 rounded-l-lg">Nama</th>
                            <th class="p-3.5">Sekolah</th>
                            <th class="p-3.5">Email</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5">Dibuat</th>
                            <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($pengguna as $p) : ?>
                            <tr class="hover:bg-slate-50 transition <?= $p['status'] === 'nonaktif' ? 'opacity-60' : '' ?>">
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800"><?= esc($p['nama_admin']) ?></div>
                                    <div class="text-[11px] text-slate-400 font-mono">@<?= esc($p['username']) ?></div>
                                </td>
                                <td class="p-3.5">
                                    <?php if ($p['nama_sekolah']) : ?>
                                        <div class="font-semibold text-slate-700"><?= esc($p['nama_sekolah']) ?></div>
                                        <div class="text-[11px] text-slate-400">
                                            NPSN <?= esc($p['npsn']) ?> • <?= esc($p['kabupaten_kota']) ?>
                                            <?php if ($p['status_sekolah'] === 'nonaktif') : ?>
                                                <span class="text-red-500 font-semibold">(sekolah nonaktif)</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else : ?>
                                        <span class="text-red-500 text-[11px] font-semibold">Belum terhubung ke sekolah</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5 text-[11px]"><?= esc($p['email'] ?: '-') ?></td>
                                <td class="p-3.5">
                                    <?php if ($p['status'] === 'aktif') : ?>
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full">Aktif</span>
                                    <?php else : ?>
                                        <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full">Nonaktif</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3.5 whitespace-nowrap text-[11px]"><?= date('d/m/Y', strtotime($p['created_at'])) ?></td>
                                <td class="p-3.5">
                                    <div class="flex justify-center gap-1.5">
                                        <button type="button" title="Edit / Reset password"
                                            data-pengguna="<?= esc(json_encode($p), 'attr') ?>"
                                            onclick="bukaFormPengguna(JSON.parse(this.dataset.pengguna))"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                            <i class="fa-solid fa-pen text-[11px]"></i>
                                        </button>
                                        <button type="button" title="<?= $p['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                            onclick="konfirmasiAksi(
                                                '<?= site_url('pengguna/status/' . $p['id_admin']) ?>',
                                                '<?= $p['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> akun?',
                                                <?= esc(json_encode($p['nama_admin'] . ' (@' . $p['username'] . ')'), 'attr') ?>,
                                                'amber')"
                                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center">
                                            <i class="fa-solid <?= $p['status'] === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?> text-[12px]"></i>
                                        </button>
                                        <button type="button" title="Hapus"
                                            onclick="konfirmasiAksi(
                                                '<?= site_url('pengguna/hapus/' . $p['id_admin']) ?>',
                                                'Hapus akun?',
                                                <?= esc(json_encode($p['nama_admin'] . ' (@' . $p['username'] . ')'), 'attr') ?>,
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
     MODAL FORM TAMBAH / EDIT AKUN
     ===================================================== -->
<div id="modalPengguna" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center border-b pb-3 mb-4">
            <h3 class="font-bold text-slate-800 text-base">
                <i class="fa-solid fa-user-shield text-brand-600 mr-2"></i><span id="judulFormPengguna">Tambah Admin Sekolah</span>
            </h3>
            <button type="button" onclick="closeModal('modalPengguna')" class="text-slate-400 hover:text-slate-600">
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

        <form action="<?= site_url('pengguna/simpan') ?>" method="post" class="space-y-4 text-xs" autocomplete="off">
            <?= csrf_field() ?>
            <input type="hidden" name="id_admin" id="p_id_admin">

            <div>
                <label class="font-semibold block mb-1 text-slate-700">Sekolah <span class="text-red-500">*</span></label>
                <select name="id_sekolah" id="p_id_sekolah" required class="w-full border border-slate-300 rounded-lg p-2.5">
                    <option value="">Pilih sekolah</option>
                    <?php foreach ($daftarSekolah as $s) : ?>
                        <option value="<?= $s['id_sekolah'] ?>">
                            <?= esc($s['nama_sekolah']) ?> — <?= esc($s['kabupaten_kota']) ?><?= $s['status'] === 'nonaktif' ? ' (nonaktif)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                <input type="text" name="nama_admin" id="p_nama_admin" required placeholder="Nama petugas admin sekolah"
                    class="w-full border border-slate-300 rounded-lg p-2.5">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Username <span class="text-red-500">*</span></label>
                    <input type="text" name="username" id="p_username" required placeholder="contoh: sman1kendari"
                        class="w-full border border-slate-300 rounded-lg p-2.5 font-mono lowercase">
                </div>
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="font-semibold text-slate-700">
                            Password <span id="p_password_wajib" class="text-red-500">*</span>
                        </label>
                        <button type="button" onclick="buatPassword()" class="text-[10px] text-brand-600 font-semibold hover:underline">
                            <i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis
                        </button>
                    </div>
                    <div class="relative">
                        <input type="password" name="password" id="p_password" autocomplete="new-password"
                            class="w-full border border-slate-300 rounded-lg p-2.5 pr-9">
                        <button type="button" onclick="lihatPassword()" class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i id="p_password_ikon" class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                    <p id="p_password_info" class="text-[10px] text-slate-400 mt-1">Minimal 8 karakter.</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Email</label>
                    <input type="email" name="email" id="p_email" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Status <span class="text-red-500">*</span></label>
                    <select name="status" id="p_status" class="w-full border border-slate-300 rounded-lg p-2.5">
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                </div>
            </div>

            <div class="flex justify-end space-x-2 border-t pt-3">
                <button type="button" onclick="closeModal('modalPengguna')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
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
    // Buka form: tanpa data = akun baru, dengan data = edit
    function bukaFormPengguna(data = null) {
        const kolom = ['id_admin', 'id_sekolah', 'nama_admin', 'username', 'email', 'status'];
        kolom.forEach(k => {
            const el = document.getElementById('p_' + k);
            if (el) el.value = data && data[k] ? data[k] : (k === 'status' ? 'aktif' : '');
        });

        // Password tidak pernah diisi ulang
        const pass = document.getElementById('p_password');
        pass.value = '';

        const edit = data && data.id_admin;
        document.getElementById('judulFormPengguna').innerText = edit ? 'Edit Admin Sekolah' : 'Tambah Admin Sekolah';
        pass.required = !edit;
        document.getElementById('p_password_wajib').classList.toggle('hidden', !!edit);
        document.getElementById('p_password_info').innerText = edit ?
            'Kosongkan jika password tidak diubah. Isi untuk mereset password (min. 8 karakter).' :
            'Minimal 8 karakter.';

        openModal('modalPengguna');
    }


    // Buat password acak 10 karakter (tanpa huruf yang mirip seperti l, 1, O, 0)
    function buatPassword() {
        const huruf = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        const acak = new Uint32Array(10);
        crypto.getRandomValues(acak);
        const hasil = Array.from(acak, n => huruf[n % huruf.length]).join('');

        const pass = document.getElementById('p_password');
        pass.value = hasil;
        pass.type = 'text';
        document.getElementById('p_password_ikon').className = 'fa-solid fa-eye-slash';
    }

    // Salin info akun ke clipboard
    function salinAkun(tombol) {
        const teks = document.getElementById('teksAkun').value;
        navigator.clipboard.writeText(teks).then(() => {
            tombol.innerHTML = '<i class="fa-solid fa-check mr-2"></i>Tersalin!';
            setTimeout(() => {
                tombol.innerHTML = '<i class="fa-regular fa-copy mr-2"></i>Salin Info Akun';
            }, 2000);
        });
    }

    // Tampilkan / sembunyikan password
    function lihatPassword() {
        const pass = document.getElementById('p_password');
        const ikon = document.getElementById('p_password_ikon');
        const tampil = pass.type === 'password';
        pass.type = tampil ? 'text' : 'password';
        ikon.className = tampil ? 'fa-solid fa-eye-slash' : 'fa-solid fa-eye';
    }

    // Modal konfirmasi untuk aksi status & hapus
    function konfirmasiAksi(url, judul, nama, warna) {
        document.getElementById('konfirmasiForm').action = url;
        document.getElementById('konfirmasiJudul').innerText = judul;
        document.getElementById('konfirmasiPesan').innerText = nama;

        const gaya = {
            red: ['bg-red-100 text-red-600', 'bg-red-600 hover:bg-red-700'],
            amber: ['bg-amber-100 text-amber-600', 'bg-amber-500 hover:bg-amber-600'],
        } [warna];

        document.getElementById('konfirmasiIkon').className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center ' + gaya[0];
        document.getElementById('konfirmasiTombol').className = 'px-4 py-2 text-white rounded-lg text-xs font-semibold ' + gaya[1];

        openModal('modalKonfirmasi');
    }

    // Kalau simpan gagal validasi: buka lagi form dengan isian sebelumnya (tanpa password)
    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', function() {
            bukaFormPengguna(<?= json_encode([
                                    'id_admin'   => old('id_admin'),
                                    'id_sekolah' => old('id_sekolah'),
                                    'nama_admin' => old('nama_admin'),
                                    'username'   => old('username'),
                                    'email'      => old('email'),
                                    'status'     => old('status'),
                                ]) ?>);
        });
    <?php endif; ?>
</script>
<?= $this->endSection() ?>