<?php

/** @var array|null $guru */
/** @var array $stat */
/** @var array $perbaikan */
/** @var array $pengajuan */
/** @var array $kompetisiBuka */
/** @var array $prestasi */
/** @var array $monev */
/** @var array $pengumuman */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';

// Posisi pengajuan dalam alur: [label, warna badge, ikon]
$tahap = [
    'tunggu_sekolah' => ['Menunggu verifikasi sekolah', 'bg-amber-100 text-amber-800', 'fa-clock'],
    'tunggu_dinas'   => ['Menunggu validasi Dinas', 'bg-blue-100 text-blue-700', 'fa-hourglass-half'],
    'disetujui'      => ['Disetujui Dinas', 'bg-emerald-100 text-emerald-800', 'fa-circle-check'],
    'tolak_sekolah'  => ['Ditolak sekolah', 'bg-red-100 text-red-700', 'fa-rotate-left'],
    'tolak_dinas'    => ['Ditolak Dinas', 'bg-red-100 text-red-700', 'fa-rotate-left'],
];
$jenis = [
    'praktik' => ['Praktik Baik', 'fa-book-open', 'text-brand-600 bg-brand-50', 'praktik-baik'],
    'inovasi' => ['Inovasi', 'fa-lightbulb', 'text-indigo-600 bg-indigo-50', 'bank-inovasi'],
];
$medali = [
    1 => 'bg-amber-100 text-amber-700 border-amber-300',
    2 => 'bg-slate-200 text-slate-700 border-slate-300',
    3 => 'bg-orange-100 text-orange-700 border-orange-300',
];

$jam   = (int) date('H');
$salam = $jam < 11 ? 'Selamat pagi' : ($jam < 15 ? 'Selamat siang' : ($jam < 18 ? 'Selamat sore' : 'Selamat malam'));

$kartu = [
    ['Disetujui Dinas', $stat['disetujui'], 'fa-circle-check', 'bg-emerald-50 text-emerald-600', 'Praktik baik & inovasi'],
    ['Sedang Diproses', $stat['diproses'], 'fa-hourglass-half', 'bg-blue-50 text-blue-600', 'Menunggu sekolah / Dinas'],
    ['Perlu Perbaikan', $stat['perbaikan'], 'fa-rotate-left', $stat['perbaikan'] > 0 ? 'bg-red-50 text-red-600' : 'bg-slate-50 text-slate-400', 'Ditolak dengan catatan'],
    ['Kompetisi Diikuti', $stat['kompetisi'], 'fa-trophy', 'bg-amber-50 text-amber-600', $stat['juara'] . ' kali juara'],
];
?>
<section class="space-y-6">

    <!-- Banner -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-brand-900 text-white p-6 rounded-2xl shadow-md border border-slate-800 flex flex-wrap justify-between items-center gap-5">
        <div class="space-y-1.5">
            <p class="text-xs text-amber-400 font-semibold"><?= $salam ?>,</p>
            <h2 class="text-2xl font-bold"><?= esc($guru['nama_guru'] ?? session()->get('nama')) ?></h2>
            <p class="text-xs text-slate-300">
                <?php if (! empty($guru['nip'])) : ?><span class="font-mono">NIP <?= esc($guru['nip']) ?></span> • <?php endif; ?>
                <?= esc($guru['mapel'] ?? 'Guru') ?>
            </p>
            <p class="text-xs text-slate-300"><i class="fa-solid fa-school mr-1.5 text-amber-400"></i><?= esc($guru['nama_sekolah'] ?? '-') ?><?= ! empty($guru['kabupaten_kota']) ? ' • ' . esc($guru['kabupaten_kota']) : '' ?></p>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="<?= site_url('praktik-baik') ?>" class="bg-white text-slate-900 hover:bg-slate-100 text-xs font-bold px-4 py-2.5 rounded-xl flex items-center">
                <i class="fa-solid fa-book-open mr-2 text-brand-600"></i>Unggah Praktik Baik
            </a>
            <a href="<?= site_url('bank-inovasi') ?>" class="bg-amber-400 hover:bg-amber-500 text-slate-900 text-xs font-bold px-4 py-2.5 rounded-xl flex items-center">
                <i class="fa-solid fa-lightbulb mr-2"></i>Usulkan Inovasi
            </a>
        </div>
    </div>

    <!-- Statistik -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <?php foreach ($kartu as [$judul, $angka, $ikon, $warna, $sub]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $angka ?></div>
                    <div class="text-[10px] text-slate-400"><?= esc($sub) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ================= KOLOM KIRI ================= -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Perlu perbaikan -->
            <?php if (! empty($perbaikan)) : ?>
                <div class="bg-white p-5 rounded-2xl border-2 border-red-200 shadow-sm space-y-3">
                    <h3 class="font-bold text-red-700 text-sm"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Perlu Perbaikan (<?= count($perbaikan) ?>)</h3>
                    <p class="text-[11px] text-slate-500">Pengajuan berikut dikembalikan. Perbaiki sesuai catatan, lalu kirim ulang.</p>
                    <div class="space-y-2">
                        <?php foreach ($perbaikan as $p) : ?>
                            <?php $catatan = $p['tahap'] === 'tolak_sekolah' ? $p['catatan_sekolah'] : $p['catatan_dinas']; ?>
                            <a href="<?= site_url($jenis[$p['jenis']][3]) ?>" class="block border border-red-100 bg-red-50/40 hover:bg-red-50 rounded-xl p-3.5 text-xs transition">
                                <div class="flex flex-wrap justify-between items-start gap-2">
                                    <div class="min-w-0">
                                        <span class="text-[10px] font-semibold text-slate-500"><i class="fa-solid <?= $jenis[$p['jenis']][1] ?> mr-1"></i><?= $jenis[$p['jenis']][0] ?></span>
                                        <div class="font-bold text-slate-800 truncate"><?= esc($p['judul']) ?></div>
                                    </div>
                                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap <?= $tahap[$p['tahap']][1] ?>"><?= $tahap[$p['tahap']][0] ?></span>
                                </div>
                                <?php if ($catatan) : ?>
                                    <div class="mt-2 text-slate-600 bg-white border border-red-100 rounded-lg p-2.5 line-clamp-2">
                                        <i class="fa-solid fa-quote-left text-red-300 mr-1"></i><?= esc($catatan) ?>
                                    </div>
                                <?php endif; ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Status pengajuan terbaru -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-list-check text-brand-600 mr-2"></i>Status Pengajuan Terbaru</h3>

                <?php if (empty($pengajuan)) : ?>
                    <div class="text-center py-8 text-xs text-slate-400">
                        <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                        Anda belum mengajukan praktik baik atau inovasi.<br>
                        Mulai dengan tombol <b>Ajukan Praktik Baik</b> di atas.
                    </div>
                <?php else : ?>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($pengajuan as $p) : ?>
                            <?php [$labelJenis, $ikonJenis, $warnaJenis] = $jenis[$p['jenis']]; ?>
                            <div class="flex items-center gap-3 py-3 text-xs">
                                <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 <?= $warnaJenis ?>"><i class="fa-solid <?= $ikonJenis ?>"></i></div>
                                <div class="flex-1 min-w-0">
                                    <div class="font-semibold text-slate-800 truncate"><?= esc($p['judul']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= $labelJenis ?> • diajukan <?= $tgl($p['tanggal']) ?></div>
                                </div>
                                <span class="text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap <?= $tahap[$p['tahap']][1] ?>">
                                    <i class="fa-solid <?= $tahap[$p['tahap']][2] ?> mr-1"></i><?= $tahap[$p['tahap']][0] ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Prestasi -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-medal text-amber-500 mr-2"></i>Prestasi Saya</h3>
                <?php if (empty($prestasi)) : ?>
                    <p class="text-xs text-slate-400 text-center py-6">Belum ada prestasi. Ikuti kompetisi inovasi yang sedang dibuka!</p>
                <?php else : ?>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <?php foreach ($prestasi as $j) : ?>
                            <div class="border border-slate-200 rounded-xl p-3.5 text-xs space-y-2">
                                <span class="<?= $medali[$j['peringkat']] ?? 'bg-slate-100 text-slate-600' ?> border text-[10px] font-bold px-2.5 py-1 rounded-full">
                                    <i class="fa-solid fa-medal mr-1"></i>Juara <?= $j['peringkat'] ?>
                                </span>
                                <div class="font-bold text-slate-800"><?= esc($j['judul_karya']) ?></div>
                                <div class="text-[11px] text-slate-400"><?= esc($j['nama_kompetisi']) ?></div>
                                <?php if (! empty($j['bukti_file'])) : ?>
                                    <a href="<?= base_url($j['bukti_file']) ?>" target="_blank" class="inline-flex items-center text-emerald-700 font-semibold hover:underline">
                                        <i class="fa-solid fa-download mr-1"></i>Unduh Piagam
                                    </a>
                                <?php else : ?>
                                    <span class="text-[11px] text-blue-600"><i class="fa-solid fa-hourglass-half mr-1"></i>Piagam sedang disiapkan Dinas</span>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ================= KOLOM KANAN ================= -->
        <div class="space-y-6">

            <!-- Kompetisi dibuka -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-trophy text-amber-500 mr-2"></i>Kompetisi Dibuka</h3>
                <?php if (empty($kompetisiBuka)) : ?>
                    <p class="text-xs text-slate-400 text-center py-4">Belum ada kompetisi yang membuka pendaftaran.</p>
                <?php else : ?>
                    <?php foreach ($kompetisiBuka as $k) : ?>
                        <a href="<?= site_url('kompetisi') ?>" class="block border border-slate-200 hover:border-amber-300 hover:bg-amber-50/40 rounded-xl p-3 text-xs transition">
                            <div class="font-bold text-slate-800"><?= esc($k['nama_kompetisi']) ?></div>
                            <div class="text-[11px] text-slate-500 mt-0.5"><i class="fa-regular fa-calendar mr-1"></i>Tutup <?= $tgl($k['tanggal_selesai']) ?></div>
                            <?php if ($k['terdaftar']) : ?>
                                <span class="inline-block mt-2 bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full"><i class="fa-solid fa-check mr-1"></i>Sudah terdaftar</span>
                            <?php else : ?>
                                <span class="inline-block mt-2 text-amber-700 text-[11px] font-semibold">Daftar sekarang <i class="fa-solid fa-arrow-right ml-1"></i></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Jadwal monev -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-3">
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-clipboard-check text-brand-600 mr-2"></i>Jadwal Monitoring</h3>
                <?php if (empty($monev)) : ?>
                    <p class="text-xs text-slate-400 text-center py-4">Tidak ada jadwal monitoring untuk karya Anda.</p>
                <?php else : ?>
                    <?php foreach ($monev as $m) : ?>
                        <?php $lewat = $m['tanggal_monev'] < date('Y-m-d');
                        $hariIni = $m['tanggal_monev'] === date('Y-m-d'); ?>
                        <div class="flex gap-3 text-xs">
                            <div class="w-12 shrink-0 text-center rounded-lg py-1.5 <?= $hariIni ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700' ?>">
                                <div class="text-base font-bold leading-none"><?= date('j', strtotime($m['tanggal_monev'])) ?></div>
                                <div class="text-[9px] uppercase"><?= $bulan[(int) date('n', strtotime($m['tanggal_monev']))] ?></div>
                            </div>
                            <div class="min-w-0">
                                <div class="font-semibold text-slate-800 truncate"><?= esc($m['aspek_monev']) ?></div>
                                <div class="text-[11px] text-slate-400 truncate"><?= esc($m['judul_karya']) ?></div>
                                <?php if ($hariIni) : ?><span class="text-[10px] font-bold text-brand-600">Hari ini</span><?php endif; ?>
                                <?php if ($lewat) : ?><span class="text-[10px] font-bold text-red-600">Terlewat</span><?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
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