<?php

/** @var array $akun */
/** @var bool $isGuru */
/** @var string $nama */
/** @var string $labelRole */
/** @var array|null $sekolah */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errorsProfil   = session()->getFlashdata('errorsProfil') ?? [];
$errorsPassword = session()->getFlashdata('errorsPassword') ?? [];

// Inisial untuk avatar, misalnya "Muh Syamdudin" -> "MS"
$kata    = preg_split('/\s+/', trim($nama));
$inisial = strtoupper(mb_substr($kata[0] ?? '', 0, 1) . mb_substr($kata[1] ?? '', 0, 1));

$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$sejak = ! empty($akun['created_at'])
    ? $bulan[(int) date('n', strtotime($akun['created_at']))] . ' ' . date('Y', strtotime($akun['created_at']))
    : '-';
?>
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- Kartu identitas -->
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
        <div class="h-20 bg-gradient-to-r from-brand-800 to-slate-900"></div>
        <div class="px-5 pb-5 -mt-10 text-center space-y-3">
            <div class="mx-auto w-20 h-20 rounded-full bg-white p-1 shadow">
                <div class="w-full h-full rounded-full bg-brand-600 text-white flex items-center justify-center text-2xl font-bold">
                    <?= esc($inisial) ?>
                </div>
            </div>
            <div>
                <h2 class="font-bold text-slate-800 text-base"><?= esc($nama) ?></h2>
                <p class="text-xs text-slate-400 font-mono">@<?= esc($akun['username']) ?></p>
            </div>
            <span class="inline-block bg-brand-50 text-brand-700 text-[11px] font-bold px-3 py-1 rounded-full"><?= esc($labelRole) ?></span>

            <div class="text-left text-xs border-t pt-4 space-y-2.5">
                <?php if ($sekolah) : ?>
                    <div class="flex gap-3">
                        <i class="fa-solid fa-school text-slate-400 w-4 mt-0.5"></i>
                        <div>
                            <div class="font-semibold text-slate-700"><?= esc($sekolah['nama_sekolah']) ?></div>
                            <div class="text-[11px] text-slate-400">NPSN <?= esc($sekolah['npsn']) ?> • <?= esc($sekolah['kabupaten_kota']) ?></div>
                        </div>
                    </div>
                <?php endif; ?>
                <?php if (! $isGuru && ! empty($akun['email'])) : ?>
                    <div class="flex gap-3">
                        <i class="fa-solid fa-envelope text-slate-400 w-4 mt-0.5"></i>
                        <span class="text-slate-600 break-all"><?= esc($akun['email']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="flex gap-3">
                    <i class="fa-solid fa-calendar text-slate-400 w-4 mt-0.5"></i>
                    <span class="text-slate-600">Bergabung sejak <?= $sejak ?></span>
                </div>
                <div class="flex gap-3">
                    <i class="fa-solid fa-circle-check text-emerald-500 w-4 mt-0.5"></i>
                    <span class="text-slate-600">Akun <?= esc($akun['status'] ?? 'aktif') ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="lg:col-span-2 space-y-6">

        <?php if (session()->getFlashdata('sukses')) : ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
            </div>
        <?php endif; ?>

        <!-- Data diri -->
        <form action="<?= site_url('profil/simpan') ?>" method="post" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <?= csrf_field() ?>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-user-pen text-brand-600 mr-2"></i>Data Diri</h3>
                <p class="text-slate-400 mt-0.5">Perbarui nama dan informasi kontak Anda.</p>
            </div>

            <?php if ($errorsProfil) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errorsProfil as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required value="<?= esc(old('nama', $nama)) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>

                <?php if ($isGuru) : ?>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">NIP</label>
                        <input type="text" name="nip" value="<?= esc(old('nip', $akun['nip'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Mata Pelajaran</label>
                        <input type="text" name="mapel" value="<?= esc(old('mapel', $akun['mapel'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                <?php else : ?>
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1 text-slate-700">Email</label>
                        <input type="email" name="email" value="<?= esc(old('email', $akun['email'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                <?php endif; ?>

                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Username</label>
                    <input type="text" value="<?= esc($akun['username']) ?>" disabled class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-lg p-2.5 font-mono">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Hak Akses</label>
                    <input type="text" value="<?= esc($labelRole) ?>" disabled class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-lg p-2.5">
                </div>
            </div>
            <p class="text-[11px] text-slate-400"><i class="fa-solid fa-circle-info mr-1"></i>Username, hak akses, dan sekolah hanya dapat diubah oleh Admin Dinas.</p>

            <div class="flex justify-end border-t pt-4">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Data Diri
                </button>
            </div>
        </form>

        <!-- Ubah password -->
        <form action="<?= site_url('profil/password') ?>" method="post" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs" autocomplete="off">
            <?= csrf_field() ?>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-lock text-amber-500 mr-2"></i>Ubah Password</h3>
                <p class="text-slate-400 mt-0.5">Gunakan minimal 8 karakter. Password tidak ditampilkan kepada siapa pun, termasuk Admin Dinas.</p>
            </div>

            <?php if ($errorsPassword) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errorsPassword as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php foreach (
                [
                    ['password_lama', 'Password Lama', 'current-password'],
                    ['password_baru', 'Password Baru', 'new-password'],
                    ['konfirmasi_password', 'Ulangi Password Baru', 'new-password'],
                ] as [$nm, $label, $auto]
            ) : ?>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700"><?= $label ?></label>
                    <div class="relative">
                        <input type="password" name="<?= $nm ?>" id="<?= $nm ?>" required autocomplete="<?= $auto ?>"
                            class="w-full border border-slate-300 rounded-lg p-2.5 pr-9">
                        <button type="button" onclick="lihatSandi('<?= $nm ?>', this)"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-end border-t pt-4">
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-key mr-1"></i> Ubah Password
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    function lihatSandi(id, tombol) {
        const input = document.getElementById(id);
        const tampil = input.type === 'password';
        input.type = tampil ? 'text' : 'password';
        tombol.innerHTML = tampil ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
    }
</script>
<?= $this->endSection() ?>