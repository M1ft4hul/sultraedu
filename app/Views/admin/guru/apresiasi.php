<?php

/** @var array $prestasi */
/** @var array $medali */
/** @var int $tersedia */
/** @var int $totalSemua */
/** @var array $daftarTahun */
/** @var array $filter */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$angka = fn($n) => number_format((float) $n, 2, ',', '.');

// Gaya per peringkat
$gaya = [
    1 => [
        'nama'   => 'Emas',
        'kepala' => 'bg-gradient-to-br from-amber-300 via-yellow-400 to-amber-500',
        'teks'   => 'text-amber-950',
        'piala'  => 'text-white drop-shadow-lg',
        'garis'  => 'border-amber-300',
        'tombol' => 'bg-amber-500 hover:bg-amber-600',
        'lencana' => 'bg-amber-400 text-white',
    ],
    2 => [
        'nama'   => 'Perak',
        'kepala' => 'bg-gradient-to-br from-slate-200 via-slate-300 to-slate-400',
        'teks'   => 'text-slate-800',
        'piala'  => 'text-white drop-shadow-lg',
        'garis'  => 'border-slate-300',
        'tombol' => 'bg-slate-600 hover:bg-slate-700',
        'lencana' => 'bg-slate-400 text-white',
    ],
    3 => [
        'nama'   => 'Perunggu',
        'kepala' => 'bg-gradient-to-br from-orange-300 via-orange-400 to-amber-700',
        'teks'   => 'text-orange-950',
        'piala'  => 'text-white drop-shadow-lg',
        'garis'  => 'border-orange-300',
        'tombol' => 'bg-orange-600 hover:bg-orange-700',
        'lencana' => 'bg-orange-500 text-white',
    ],
];
$total = array_sum($medali);

// Susun ulang filter untuk tautan (ubah satu nilai, pertahankan sisanya)
$tautan = fn(array $ubah) => site_url('apresiasi') . '?' . http_build_query(array_filter($ubah + $filter, fn($x) => $x !== ''));
$adaFilter = array_filter($filter, fn($x) => $x !== '');

// Kelompokkan per bulan pengumuman
$perBulan = [];
foreach ($prestasi as $p) {
    $waktu = strtotime($p['tanggal_pengumuman'] ?? $p['tanggal_mulai']);
    $perBulan[$bulan[(int) date('n', $waktu)] . ' ' . date('Y', $waktu)][] = $p;
}
?>
<style>
    /* Kilau lembut di kepala kartu piala */
    .kilau {
        position: relative;
        overflow: hidden;
    }

    .kilau::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -60%;
        width: 40%;
        height: 200%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, .45), transparent);
        transform: rotate(20deg);
        animation: kilau 4s ease-in-out infinite;
    }

    @keyframes kilau {

        0%,
        60% {
            left: -60%;
        }

        100% {
            left: 130%;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .kilau::after {
            animation: none;
            display: none;
        }
    }
</style>

<section class="space-y-6">

    <!-- Banner lemari piala -->
    <div class="bg-gradient-to-r from-slate-900 via-indigo-950 to-slate-900 text-white p-6 rounded-2xl shadow-md border border-slate-800">
        <div class="flex flex-wrap justify-between items-center gap-6">
            <div>
                <p class="text-xs text-amber-400 font-semibold uppercase tracking-wider"><i class="fa-solid fa-award mr-1"></i>Lemari Piala</p>
                <h2 class="text-2xl font-bold mt-1"><?= $total > 0 ? 'Selamat atas prestasi Anda!' : 'Raih piala pertama Anda' ?></h2>
                <p class="text-xs text-slate-300 mt-1">
                    <?= $total > 0
                        ? $total . ' penghargaan dari kompetisi inovasi • ' . $tersedia . ' piagam siap diunduh'
                        : 'Ikuti kompetisi inovasi dan tunjukkan karya terbaik Anda.' ?>
                </p>
            </div>
            <div class="flex gap-3">
                <?php foreach ([1 => 'text-amber-400', 2 => 'text-slate-300', 3 => 'text-orange-400'] as $juara => $warna) : ?>
                    <?php $aktif = $filter['juara'] === (string) $juara; ?>
                    <a href="<?= $aktif ? $tautan(['juara' => '']) : $tautan(['juara' => (string) $juara]) ?>"
                        title="<?= $aktif ? 'Tampilkan semua juara' : 'Tampilkan hanya Juara ' . $juara ?>"
                        class="rounded-2xl px-4 py-3 text-center min-w-[80px] border transition
                               <?= $aktif ? 'bg-white/25 border-white/60 ring-2 ring-white/40' : 'bg-white/10 border-white/10 hover:bg-white/20' ?>">
                        <i class="fa-solid fa-trophy text-2xl <?= $medali[$juara] > 0 ? $warna : 'text-white/20' ?>"></i>
                        <div class="text-xl font-extrabold mt-1"><?= $medali[$juara] ?></div>
                        <div class="text-[10px] text-slate-300">Juara <?= $juara ?></div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <?php if ($totalSemua > 0) : ?>
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3 text-xs">
            <form method="get" action="<?= site_url('apresiasi') ?>" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="juara" value="<?= esc($filter['juara']) ?>">
                <span class="font-semibold text-slate-600"><i class="fa-solid fa-filter text-brand-600 mr-1"></i>Periode:</span>
                <select name="tahun" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                    <option value="">Semua tahun</option>
                    <?php foreach ($daftarTahun as $th) : ?>
                        <option value="<?= $th ?>" <?= $filter['tahun'] === $th ? 'selected' : '' ?>><?= $th ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="bulan" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                    <option value="">Semua bulan</option>
                    <?php foreach ($bulan as $no => $nama) : ?>
                        <option value="<?= $no ?>" <?= $filter['bulan'] === (string) $no ? 'selected' : '' ?>><?= $nama ?></option>
                    <?php endforeach; ?>
                </select>
            </form>

            <div class="flex flex-wrap items-center gap-1.5 md:ml-auto">
                <span class="font-semibold text-slate-600 mr-1">Juara:</span>
                <?php foreach (['' => 'Semua', '1' => 'Juara 1', '2' => 'Juara 2', '3' => 'Juara 3'] as $kunci => $teks) : ?>
                    <a href="<?= $tautan(['juara' => (string) $kunci]) ?>"
                        class="px-3 py-1.5 rounded-full border font-semibold transition
                               <?= $filter['juara'] === (string) $kunci ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50' ?>">
                        <?php if ($kunci !== '') : ?><i class="fa-solid fa-medal mr-1 <?= $filter['juara'] === (string) $kunci ? '' : ['1' => 'text-amber-500', '2' => 'text-slate-400', '3' => 'text-orange-500'][$kunci] ?>"></i><?php endif; ?><?= $teks ?>
                    </a>
                <?php endforeach; ?>
                <?php if ($adaFilter) : ?>
                    <a href="<?= site_url('apresiasi') ?>" title="Hapus semua filter" class="ml-1 border border-slate-300 hover:bg-slate-50 text-slate-600 px-2.5 py-1.5 rounded-full">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if (empty($prestasi) && $totalSemua > 0) : ?>
        <!-- Filter tidak cocok -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm text-center py-12 px-4 text-xs text-slate-400">
            <i class="fa-regular fa-calendar-xmark text-3xl mb-2 block"></i>
            Tidak ada penghargaan pada periode atau juara yang dipilih.
            <a href="<?= site_url('apresiasi') ?>" class="block mt-3 text-brand-600 font-semibold hover:underline">Tampilkan semua penghargaan</a>
        </div>
    <?php elseif (empty($prestasi)) : ?>
        <!-- Belum pernah juara -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-14 px-4">
            <div class="relative w-20 h-20 mb-4">
                <div class="absolute inset-0 rounded-3xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-3xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-3xl text-slate-300"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700">Lemari piala Anda masih kosong</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[320px] leading-relaxed">Piagam penghargaan akan tersedia di sini setelah karya Anda meraih juara pada kompetisi inovasi.</p>
            <a href="<?= site_url('kompetisi') ?>" class="mt-5 bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow flex items-center">
                <i class="fa-solid fa-paper-plane mr-2"></i>Lihat Kompetisi yang Dibuka
            </a>
        </div>
    <?php else : ?>
        <!-- Kartu penghargaan, dikelompokkan per bulan -->
        <?php foreach ($perBulan as $namaBulan => $isiBulan) : ?>
            <div class="space-y-3">
                <div class="flex items-center gap-3">
                    <h3 class="font-bold text-slate-700 text-sm"><i class="fa-regular fa-calendar text-brand-600 mr-2"></i><?= $namaBulan ?></h3>
                    <span class="text-[11px] text-slate-400"><?= count($isiBulan) ?> penghargaan</span>
                    <div class="flex-1 h-px bg-slate-200"></div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    <?php foreach ($isiBulan as $p) : ?>
                        <?php
                        $j   = (int) $p['peringkat'];
                        $g   = $gaya[$j] ?? $gaya[3];
                        $ada = ! empty($p['bukti_file']);
                        ?>
                        <div class="bg-white rounded-3xl border-2 <?= $g['garis'] ?> shadow-md overflow-hidden flex flex-col hover:-translate-y-1 hover:shadow-xl transition duration-300">

                            <!-- Kepala: piala besar -->
                            <div class="kilau <?= $g['kepala'] ?> px-5 pt-6 pb-5 text-center">
                                <i class="fa-solid fa-trophy text-6xl <?= $g['piala'] ?>"></i>
                                <div class="mt-3 text-[11px] font-bold uppercase tracking-[0.2em] <?= $g['teks'] ?> opacity-80">Medali <?= $g['nama'] ?></div>
                                <div class="text-3xl font-black <?= $g['teks'] ?> leading-tight">JUARA <?= $j ?></div>
                            </div>

                            <!-- Isi -->
                            <div class="p-5 space-y-3 flex-1 text-xs">
                                <div>
                                    <div class="text-[10px] text-slate-400 uppercase font-bold tracking-wider">Karya</div>
                                    <div class="font-bold text-slate-800 text-sm leading-snug"><?= esc($p['judul_karya']) ?></div>
                                </div>
                                <div class="space-y-1.5 text-slate-600">
                                    <div class="flex gap-2"><i class="fa-solid fa-trophy w-4 text-slate-400 mt-0.5"></i><span><?= esc($p['nama_kompetisi']) ?></span></div>
                                    <?php if ($p['nama_kategori']) : ?>
                                        <div class="flex gap-2"><i class="fa-solid fa-tag w-4 text-slate-400 mt-0.5"></i><span><?= esc($p['nama_kategori']) ?></span></div>
                                    <?php endif; ?>
                                    <div class="flex gap-2"><i class="fa-regular fa-calendar w-4 text-slate-400 mt-0.5"></i><span>Diumumkan <?= $tgl($p['tanggal_pengumuman']) ?></span></div>
                                </div>
                                <div class="flex justify-between items-center bg-slate-50 rounded-xl px-3.5 py-2.5">
                                    <span class="text-slate-500 font-semibold">Nilai Akhir</span>
                                    <span><b class="text-lg font-extrabold text-slate-800"><?= $angka($p['nilai']) ?></b><span class="text-slate-400"> / 100</span></span>
                                </div>
                            </div>

                            <!-- Piagam -->
                            <div class="border-t p-4 bg-slate-50/60">
                                <?php if ($ada) : ?>
                                    <div class="flex gap-2">
                                        <a href="<?= base_url($p['bukti_file']) ?>" download
                                            class="flex-1 <?= $g['tombol'] ?> text-white text-xs font-bold px-4 py-2.5 rounded-xl flex items-center justify-center shadow">
                                            <i class="fa-solid fa-download mr-2"></i>Unduh Piagam
                                        </a>
                                        <a href="<?= base_url($p['bukti_file']) ?>" target="_blank" title="Lihat piagam"
                                            class="w-11 border border-slate-300 hover:bg-white text-slate-600 rounded-xl flex items-center justify-center">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    </div>
                                    <p class="text-[10px] text-slate-400 text-center mt-2"><i class="fa-solid fa-circle-check text-emerald-500 mr-1"></i>Piagam tersedia sejak <?= $tgl($p['tanggal_piagam']) ?></p>
                                <?php else : ?>
                                    <div class="flex items-center justify-center gap-2 text-blue-700 bg-blue-50 border border-blue-200 rounded-xl px-4 py-2.5 text-xs font-semibold">
                                        <i class="fa-solid fa-hourglass-half"></i>Piagam sedang disiapkan Dinas
                                    </div>
                                    <p class="text-[10px] text-slate-400 text-center mt-2">Tombol unduh akan muncul setelah Dinas mengunggah piagam.</p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>