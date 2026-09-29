<?php

/** @var array|null $sekolah */
/** @var array $stat */
/** @var array $antrean */
/** @var array $juara */
/** @var array $pengumuman */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$n     = fn($x) => number_format((int) $x, 0, ',', '.');

$kartu = [
    ['Guru Aktif',            $stat['guru'],          'fa-chalkboard-user', 'bg-blue-50 text-blue-600'],
    ['Menunggu Verifikasi',   $stat['tungguSekolah'], 'fa-hourglass-half',  'bg-amber-50 text-amber-600'],
    ['Praktik Baik Tervalidasi', $stat['praktik'],    'fa-book-open',       'bg-emerald-50 text-emerald-600'],
    ['Bank Inovasi', $stat['inovasi'],     'fa-lightbulb',       'bg-indigo-50 text-indigo-600'],
    ['Juara Kompetisi',       $stat['juara'],         'fa-award',           'bg-orange-50 text-orange-600'],
];
?>
<section class="space-y-6">

    <?php if (! $sekolah) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i>
            Akun Anda belum terhubung ke sekolah mana pun. Silakan hubungi Admin Dinas.
        </div>
    <?php endif; ?>

    <!-- Banner -->
    <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="bg-brand-500/30 text-brand-200 text-xs px-3 py-1 rounded-full border border-brand-400/30 font-semibold uppercase tracking-wider mb-3 inline-block">Panel Admin Sekolah</span>
            <h2 class="text-2xl sm:text-3xl font-extrabold mb-1 leading-tight"><?= esc($sekolah['nama_sekolah'] ?? 'Sekolah') ?></h2>
            <p class="text-slate-300 text-xs mb-4">
                <?php if ($sekolah) : ?>
                    NPSN <?= esc($sekolah['npsn']) ?> • <?= esc($sekolah['jenjang']) ?> • <?= esc($sekolah['kabupaten_kota']) ?>
                <?php endif; ?>
            </p>
            <p class="text-slate-300 text-sm mb-6 leading-relaxed">
                Selamat datang, <?= esc(session()->get('nama')) ?>. Verifikasi praktik baik dari guru, kelola akun guru, dan daftarkan inovasi terbaik sekolah Anda ke kompetisi.
            </p>
            <div class="flex flex-wrap gap-3">
                <a href="<?= site_url('praktik-baik') ?>" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow-md transition">
                    <i class="fa-solid fa-clipboard-check mr-2"></i>Verifikasi Praktik Baik
                    <?php if ($stat['tungguSekolah'] > 0) : ?>
                        <span class="ml-1 bg-amber-400 text-slate-900 text-[10px] font-bold px-1.5 py-0.5 rounded-full"><?= $stat['tungguSekolah'] ?></span>
                    <?php endif; ?>
                </a>
                <a href="<?= site_url('guru') ?>" class="bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow transition">
                    <i class="fa-solid fa-chalkboard-user mr-2"></i>Kelola Data Guru
                </a>
            </div>
        </div>
        <div class="absolute right-0 bottom-0 top-0 opacity-20 pointer-events-none flex items-center pr-10">
            <img src="<?= base_url('dashboard/Tut Wuri Handayani.png') ?>" alt="" width="250">
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <?php foreach ($kartu as [$judul, $angka, $ikon, $warna]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $n($angka) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Antrean verifikasi -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="font-bold text-slate-800 flex items-center text-base">
                    <i class="fa-solid fa-clipboard-list text-amber-500 mr-2"></i>Menunggu Verifikasi Sekolah
                    <?php if ($stat['tungguSekolah'] > 0) : ?>
                        <span class="ml-2 bg-amber-100 text-amber-700 text-[11px] font-bold px-2 py-0.5 rounded-full"><?= $stat['tungguSekolah'] ?></span>
                    <?php endif; ?>
                </h3>
                <a href="<?= site_url('praktik-baik') ?>" class="text-xs text-brand-600 font-semibold hover:underline">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>

            <?php if (empty($antrean)) : ?>
                <div class="flex flex-col items-center justify-center text-center py-8 px-4">
                    <div class="relative w-16 h-16 mb-4">
                        <div class="absolute inset-0 rounded-2xl bg-emerald-100 rotate-12"></div>
                        <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-check text-2xl text-emerald-500"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Tidak ada antrean verifikasi</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[260px] leading-relaxed">Semua praktik baik dari guru sudah diverifikasi.</p>
                </div>
            <?php else : ?>
                <div class="space-y-3">
                    <?php foreach ($antrean as $a) : ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                            <div>
                                <div class="text-xs font-semibold text-slate-800"><?= esc($a['judul']) ?></div>
                                <div class="text-[11px] text-slate-500">
                                    <i class="fa-regular fa-user mr-1"></i><?= esc($a['nama_guru'] ?? '-') ?>
                                    <span class="mx-1">•</span>
                                    <i class="fa-regular fa-calendar mr-1"></i><?= $tgl($a['tanggal_upload']) ?>
                                    <?php if ($a['kategori']) : ?><span class="mx-1">•</span><?= esc($a['kategori']) ?><?php endif; ?>
                                </div>
                            </div>
                            <span class="text-[10px] bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full font-bold whitespace-nowrap self-start sm:self-center">Menunggu Verifikasi</span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <div class="space-y-6">
            <!-- Pengumuman -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-3 flex items-center text-sm"><i class="fa-solid fa-bullhorn text-brand-600 mr-2"></i>Pengumuman Dinas</h3>
                <?php if (empty($pengumuman)) : ?>
                    <div class="text-center py-4">
                        <i class="fa-regular fa-bell-slash text-2xl text-slate-300"></i>
                        <p class="text-xs text-slate-400 mt-2">Belum ada pengumuman.</p>
                    </div>
                <?php else : ?>
                    <div class="space-y-3">
                        <?php foreach ($pengumuman as $p) : ?>
                            <?php if ($p['hasil_diumumkan']) : ?>
                                <a href="<?= site_url('apresiasi') ?>" class="block bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-r-lg text-xs space-y-1 hover:bg-emerald-100 transition">
                                    <p class="font-bold text-emerald-800"><i class="fa-solid fa-trophy mr-1"></i>Hasil diumumkan</p>
                                    <p class="font-semibold text-slate-800"><?= esc($p['nama_kompetisi']) ?></p>
                                    <p class="text-slate-600">Diumumkan <?= $tgl($p['tanggal_pengumuman']) ?>. Klik untuk melihat pemenang.</p>
                                </a>
                            <?php else : ?>
                                <a href="<?= site_url('kompetisi') ?>" class="block bg-slate-50 border-l-4 border-brand-500 p-3.5 rounded-r-lg text-xs space-y-1 hover:bg-slate-100 transition">
                                    <p class="font-bold text-brand-700"><i class="fa-solid fa-door-open mr-1"></i>Pendaftaran dibuka</p>
                                    <p class="font-semibold text-slate-800"><?= esc($p['nama_kompetisi']) ?></p>
                                    <p class="text-slate-600">Ditutup <?= $tgl($p['tanggal_selesai']) ?>. Daftarkan inovasi terbaik sekolah Anda.</p>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Prestasi sekolah -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 rounded-2xl shadow-md border border-slate-800">
                <h3 class="font-bold mb-3 flex items-center text-amber-400 text-sm"><i class="fa-solid fa-award mr-2"></i>Prestasi Sekolah</h3>
                <?php if (empty($juara)) : ?>
                    <p class="text-xs text-slate-300 leading-relaxed">Belum ada juara dari sekolah Anda. Ikuti kompetisi inovasi yang sedang dibuka!</p>
                <?php else : ?>
                    <div class="space-y-2.5">
                        <?php foreach ($juara as $j) : ?>
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 text-xs">
                                <div class="font-bold text-amber-300"><?= esc($j['jenis_apresiasi']) ?></div>
                                <div class="text-white"><?= esc($j['judul_karya']) ?></div>
                                <div class="text-[11px] text-slate-400"><?= esc($j['nama_guru'] ?? '-') ?> • <?= esc($j['nama_kompetisi']) ?></div>
                                <?php if ($j['bukti_file']) : ?>
                                    <a href="<?= base_url($j['bukti_file']) ?>" target="_blank" class="inline-block mt-2 text-[11px] bg-amber-400 text-slate-900 font-semibold px-2.5 py-1 rounded-lg hover:bg-amber-300">
                                        <i class="fa-solid fa-download mr-1"></i>Unduh Piagam
                                    </a>
                                <?php else : ?>
                                    <div class="mt-2 text-[11px] text-slate-400 italic">Piagam sedang disiapkan Dinas</div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>