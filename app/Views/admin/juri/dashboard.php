<?php

/** @var array $kompetisi */
/** @var array $antrean */
/** @var array $stat */
/** @var array $riwayat */
/** @var array $pengumuman */
/** @var bool $adaTugas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$angka = fn($n) => number_format((float) $n, 2, ',', '.');
$persen = fn($a, $b) => $b > 0 ? (int) round($a / $b * 100) : 0;

$jam   = (int) date('H');
$salam = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));

$progresTotal = $persen($stat['dinilai'], $stat['karya']);

$kartu = [
    ['Lomba Dinilai', $stat['lomba'], 'fa-gavel', 'bg-amber-50 text-amber-600'],
    ['Total Karya', $stat['karya'], 'fa-lightbulb', 'bg-indigo-50 text-indigo-600'],
    ['Sudah Saya Nilai', $stat['dinilai'], 'fa-circle-check', 'bg-emerald-50 text-emerald-600'],
    ['Belum Dinilai', $stat['belum'], 'fa-hourglass-half', $stat['belum'] > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400'],
];
?>
<section class="space-y-6">

    <!-- Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-amber-950 to-slate-900 text-white p-6 rounded-2xl shadow-md border border-slate-800">
        <div class="flex flex-wrap justify-between items-center gap-6">
            <div class="space-y-1.5">
                <p class="text-xs text-amber-400 font-semibold"><?= $salam ?>,</p>
                <h2 class="text-2xl font-bold"><?= esc(session()->get('nama')) ?></h2>
                <p class="text-xs text-slate-300"><i class="fa-solid fa-gavel text-amber-400 mr-1.5"></i>Tim Juri Kompetisi Inovasi Pendidikan</p>
            </div>

            <div class="flex items-center gap-5">
                <!-- Lingkaran progres keseluruhan -->
                <div class="relative w-20 h-20">
                    <svg viewBox="0 0 36 36" class="w-20 h-20 -rotate-90">
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="rgba(255,255,255,.15)" stroke-width="3"></circle>
                        <circle cx="18" cy="18" r="15.9" fill="none" stroke="#fbbf24" stroke-width="3" stroke-linecap="round"
                            stroke-dasharray="<?= $progresTotal ?> 100"></circle>
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-lg font-extrabold leading-none"><?= $progresTotal ?>%</span>
                        <span class="text-[9px] text-slate-300">dinilai</span>
                    </div>
                </div>
                <?php if ($stat['belum'] > 0) : ?>
                    <a href="<?= site_url('penilaian') ?>" class="bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs font-bold px-5 py-3 rounded-xl flex items-center shadow">
                        <i class="fa-solid fa-pen-to-square mr-2"></i>Mulai Menilai
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($kartu as [$judul, $angkaKartu, $ikon, $warna]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $angkaKartu ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ================= KOLOM KIRI ================= -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Progres per lomba -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-gavel text-amber-500 mr-2"></i>Lomba dalam Tahap Penjurian</h3>

                <?php if (empty($kompetisi)) : ?>
                    <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                        <div class="relative w-16 h-16 mb-4">
                            <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                            <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                                <i class="fa-solid fa-mug-hot text-2xl text-amber-500"></i>
                            </div>
                        </div>
                        <?php if (! $adaTugas) : ?>
                            <p class="text-sm font-semibold text-slate-700">Anda belum ditugaskan menilai</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Dinas akan menentukan kategori lomba yang Anda nilai. Hubungi Admin Dinas bila tugas Anda belum muncul.</p>
                        <?php else : ?>
                            <p class="text-sm font-semibold text-slate-700">Belum ada lomba yang perlu dinilai</p>
                            <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Kategori tugas Anda akan muncul di sini setelah Dinas menutup pendaftaran dan memulai tahap penjurian.</p>
                        <?php endif; ?>
                    </div>
                <?php else : ?>
                    <?php foreach ($kompetisi as $k) : ?>
                        <?php $p = $persen($k['dinilai'], $k['total']);
                        $tuntas = $k['total'] > 0 && $k['dinilai'] >= $k['total']; ?>
                        <div class="border <?= $tuntas ? 'border-emerald-200 bg-emerald-50/30' : 'border-slate-200' ?> rounded-2xl p-4 space-y-3 text-xs">
                            <div class="flex flex-wrap justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <div class="font-bold text-slate-800 text-sm"><?= esc($k['nama_kompetisi']) ?></div>
                                    <div class="text-[11px] text-slate-500"><?= $k['total'] ?> karya • <?= count($k['kategori']) ?> kategori tugas Anda • <?= $k['kriteria'] ?> kriteria penilaian</div>
                                </div>
                                <?php if ($tuntas) : ?>
                                    <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Semua karya sudah dinilai</span>
                                <?php else : ?>
                                    <a href="<?= site_url('penilaian?kompetisi=' . $k['id_kompetisi']) ?>"
                                        class="bg-amber-500 hover:bg-amber-600 text-white font-semibold px-3.5 py-2 rounded-lg flex items-center">
                                        <i class="fa-solid fa-pen-to-square mr-1.5"></i>Nilai Karya
                                    </a>
                                <?php endif; ?>
                            </div>

                            <!-- Bar progres -->
                            <div>
                                <div class="flex justify-between text-[11px] mb-1">
                                    <span class="text-slate-500">Progres penilaian Anda</span>
                                    <span class="font-bold text-slate-700"><?= $k['dinilai'] ?> / <?= $k['total'] ?> (<?= $p ?>%)</span>
                                </div>
                                <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full <?= $tuntas ? 'bg-emerald-500' : 'bg-amber-400' ?>" style="width: <?= $p ?>%"></div>
                                </div>
                            </div>

                            <!-- Per kategori -->
                            <?php if (count($k['kategori']) > 1) : ?>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    <?php foreach ($k['kategori'] as $kat) : ?>
                                        <div class="flex justify-between items-center bg-slate-50 rounded-lg px-3 py-2">
                                            <span class="truncate text-slate-600"><i class="fa-solid fa-tag text-indigo-400 mr-1"></i><?= esc($kat['nama']) ?></span>
                                            <span class="font-semibold shrink-0 ml-2 <?= $kat['total'] > 0 && $kat['dinilai'] >= $kat['total'] ? 'text-emerald-600' : 'text-slate-700' ?>"><?= $kat['dinilai'] ?>/<?= $kat['total'] ?></span>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>

                            <p class="text-[10px] text-slate-400"><i class="fa-regular fa-calendar mr-1"></i>Periode lomba <?= $tgl($k['tanggal_mulai']) ?> – <?= $tgl($k['tanggal_selesai']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Antrean karya -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <div class="flex justify-between items-center">
                    <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-list-ol text-brand-600 mr-2"></i>Antrean Karya Belum Dinilai</h3>
                    <?php if ($stat['belum'] > count($antrean)) : ?>
                        <a href="<?= site_url('penilaian') ?>" class="text-[11px] text-brand-600 font-semibold hover:underline">Lihat semua (<?= $stat['belum'] ?>)</a>
                    <?php endif; ?>
                </div>

                <?php if (empty($antrean)) : ?>
                    <p class="text-xs text-slate-400 text-center py-6"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Tidak ada karya yang menunggu penilaian Anda.</p>
                <?php else : ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($antrean as $i => $a) : ?>
                            <a href="<?= site_url('penilaian?kompetisi=' . $a['id_kompetisi'] . '&karya=' . $a['id_peserta']) ?>"
                                class="flex items-center gap-3 py-3 text-xs hover:bg-slate-50 rounded-lg px-2 -mx-2 transition">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 text-[11px] font-bold flex items-center justify-center shrink-0"><?= $i + 1 ?></span>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate"><?= esc($a['judul_karya']) ?></div>
                                    <div class="text-[11px] text-slate-400 truncate"><?= esc($a['nama_kategori'] ?? '-') ?> • <?= esc($a['nama_kompetisi']) ?></div>
                                </div>
                                <?php if ($a['sebagian']) : ?>
                                    <span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Belum lengkap</span>
                                <?php endif; ?>
                                <i class="fa-solid fa-chevron-right text-slate-300"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ================= KOLOM KANAN ================= -->
        <div class="space-y-6">

            <!-- Riwayat penilaian -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-clock-rotate-left text-slate-500 mr-2"></i>Penilaian Terakhir</h3>
                <?php if (empty($riwayat)) : ?>
                    <p class="text-xs text-slate-400 text-center py-4">Anda belum memberi penilaian.</p>
                <?php else : ?>
                    <?php foreach ($riwayat as $r) : ?>
                        <div class="flex items-center gap-3 text-xs">
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-800 truncate"><?= esc($r['judul_karya']) ?></div>
                                <div class="text-[10px] text-slate-400 truncate"><?= esc($r['nama_kategori'] ?? '-') ?><?= $r['terakhir'] ? ' • ' . $tgl($r['terakhir']) : '' ?></div>
                            </div>
                            <span class="font-extrabold text-slate-800 shrink-0"><?= $angka($r['total']) ?></span>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Panduan singkat -->
            <div class="bg-amber-50 border border-amber-200 p-5 rounded-2xl text-xs space-y-2">
                <h3 class="font-bold text-amber-900 text-sm"><i class="fa-solid fa-scale-balanced mr-2"></i>Pedoman Penilaian</h3>
                <ul class="space-y-1.5 text-amber-900/80">
                    <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5"></i>Nilai setiap karya pada semua kriteria sesuai skor maksimalnya.</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5"></i>Tonton video dan baca deskripsi karya sebelum memberi skor.</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5"></i>Nilai akhir adalah rata-rata dari seluruh juri.</li>
                    <li class="flex gap-2"><i class="fa-solid fa-check mt-0.5"></i>Skor masih bisa diubah selama tahap penjurian berlangsung.</li>
                </ul>
            </div>

            <!-- Pengumuman -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-bullhorn text-purple-500 mr-2"></i>Pengumuman</h3>
                <?php if (empty($pengumuman)) : ?>
                    <p class="text-xs text-slate-400 text-center py-4"><i class="fa-regular fa-bell mr-1"></i>Belum ada pengumuman.</p>
                <?php else : ?>
                    <?php foreach ($pengumuman as $n) : ?>
                        <div class="border-l-4 <?= $n['hasil_diumumkan'] ? 'border-emerald-400' : 'border-blue-400' ?> pl-3 py-1 text-xs">
                            <div class="font-semibold text-slate-800">
                                <?= $n['hasil_diumumkan'] ? 'Hasil diumumkan: ' : 'Pendaftaran dibuka: ' ?><?= esc($n['nama_kompetisi']) ?>
                            </div>
                            <div class="text-[11px] text-slate-400">
                                <?= $n['hasil_diumumkan'] ? $tgl($n['tanggal_pengumuman']) : 'Tutup ' . $tgl($n['tanggal_selesai']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>