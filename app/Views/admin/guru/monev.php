<?php

/** @var array $monev */
/** @var int $totalSemua */
/** @var array $ringkas */
/** @var array $filter */
/** @var array $daftarTahun */
/** @var array $daftarKarya */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$hari  = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

$tautan    = fn(array $ubah) => site_url('monev') . '?' . http_build_query(array_filter($ubah + $filter, fn($x) => $x !== ''));
$adaFilter = array_filter($filter, fn($x) => $x !== '');
$terdekat  = $ringkas['terdekat'];
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Monitoring & Evaluasi</h2>
        <p class="text-xs text-slate-500">Jadwal monitoring Dinas untuk karya Anda, beserta hasil dan rekomendasinya.</p>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600"><i class="fa-solid fa-calendar-days text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Jadwal Monitoring</div>
                <div class="text-xl font-bold text-slate-800"><?= $ringkas['jadwal'] ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-file-circle-check text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Hasil Tersedia</div>
                <div class="text-xl font-bold text-emerald-600"><?= $ringkas['selesai'] ?></div>
            </div>
        </div>
        <div class="p-4 rounded-xl border shadow-sm flex items-center gap-3 <?= $terdekat ? 'bg-brand-600 border-brand-600 text-white' : 'bg-white border-slate-200' ?>">
            <div class="p-3 rounded-xl <?= $terdekat ? 'bg-white/20' : 'bg-slate-50 text-slate-400' ?>"><i class="fa-solid fa-bell text-xl"></i></div>
            <div class="min-w-0">
                <div class="text-[11px] font-medium uppercase tracking-wider <?= $terdekat ? 'text-white/80' : 'text-slate-500' ?>">Jadwal Terdekat</div>
                <?php if ($terdekat) : ?>
                    <div class="text-base font-bold"><?= $hari[(int) date('w', strtotime($terdekat['tanggal_monev']))] ?>, <?= $tgl($terdekat['tanggal_monev']) ?></div>
                    <div class="text-[11px] text-white/80 truncate"><?= esc($terdekat['aspek_monev']) ?></div>
                <?php else : ?>
                    <div class="text-sm font-semibold text-slate-400">Tidak ada jadwal</div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php if ($totalSemua > 0) : ?>
        <!-- Filter -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3 text-xs">
            <div class="flex flex-wrap gap-1.5">
                <?php foreach (['' => ['Semua', 'fa-layer-group'], 'jadwal' => ['Jadwal', 'fa-calendar-days'], 'selesai' => ['Hasil Tersedia', 'fa-file-circle-check']] as $kunci => [$teks, $ikon]) : ?>
                    <a href="<?= $tautan(['status' => $kunci]) ?>"
                        class="px-3 py-1.5 rounded-full border font-semibold transition flex items-center gap-1.5
                               <?= $filter['status'] === $kunci ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50' ?>">
                        <i class="fa-solid <?= $ikon ?>"></i><?= $teks ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <form method="get" action="<?= site_url('monev') ?>" class="flex flex-wrap items-center gap-2 md:ml-auto">
                <input type="hidden" name="status" value="<?= esc($filter['status']) ?>">
                <select name="tahun" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                    <option value="">Semua tahun</option>
                    <?php foreach ($daftarTahun as $th) : ?>
                        <option value="<?= $th ?>" <?= $filter['tahun'] === $th ? 'selected' : '' ?>><?= $th ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (count($daftarKarya) > 1) : ?>
                    <select name="karya" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2 max-w-[220px]">
                        <option value="">Semua karya</option>
                        <?php foreach ($daftarKarya as $id => $judul) : ?>
                            <option value="<?= $id ?>" <?= $filter['karya'] === (string) $id ? 'selected' : '' ?>><?= esc($judul) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>
                <?php if ($adaFilter) : ?>
                    <a href="<?= site_url('monev') ?>" title="Hapus filter" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-2.5 py-2 rounded-lg">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>
    <?php endif; ?>

    <?php if ($totalSemua === 0) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-14 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-blue-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-clipboard-check text-2xl text-blue-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700">Belum ada jadwal monitoring</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[320px] leading-relaxed">Dinas akan menjadwalkan monitoring untuk karya kompetisi terpilih. Jadwal dan hasilnya akan muncul di sini.</p>
        </div>
    <?php elseif (empty($monev)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm text-center py-12 px-4 text-xs text-slate-400">
            <i class="fa-regular fa-calendar-xmark text-3xl mb-2 block"></i>
            Tidak ada data monitoring untuk filter yang dipilih.
            <a href="<?= site_url('monev') ?>" class="block mt-3 text-brand-600 font-semibold hover:underline">Tampilkan semua</a>
        </div>
    <?php else : ?>
        <div class="space-y-4">
            <?php foreach ($monev as $m) : ?>
                <?php $selesai = $m['status'] === 'selesai'; ?>
                <div class="bg-white rounded-2xl border shadow-sm p-5 flex flex-col md:flex-row gap-5 text-xs
                            <?= $selesai ? 'border-emerald-200' : ($m['hari_ini'] ? 'border-brand-400 ring-1 ring-brand-300' : ($m['terlewat'] ? 'border-red-200' : 'border-slate-200')) ?>">

                    <!-- Kotak tanggal -->
                    <div class="w-20 shrink-0 text-center rounded-2xl py-3 self-start
                                <?= $selesai ? 'bg-emerald-50 text-emerald-700' : ($m['hari_ini'] ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-700') ?>">
                        <div class="text-[10px] font-semibold uppercase"><?= $hari[(int) date('w', strtotime($m['tanggal_monev']))] ?></div>
                        <div class="text-3xl font-extrabold leading-none mt-0.5"><?= date('j', strtotime($m['tanggal_monev'])) ?></div>
                        <div class="text-[10px] font-semibold uppercase mt-0.5"><?= $bulan[(int) date('n', strtotime($m['tanggal_monev']))] ?> <?= date('Y', strtotime($m['tanggal_monev'])) ?></div>
                    </div>

                    <!-- Isi -->
                    <div class="flex-1 min-w-0 space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <?php if ($selesai) : ?>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Selesai</span>
                            <?php elseif ($m['hari_ini']) : ?>
                                <span class="bg-brand-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-bell mr-1"></i>Hari ini</span>
                            <?php elseif ($m['terlewat']) : ?>
                                <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-clock-rotate-left mr-1"></i>Menunggu hasil</span>
                            <?php else : ?>
                                <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-regular fa-clock mr-1"></i><?= $m['sisa'] ?> hari lagi</span>
                            <?php endif; ?>
                            <?php if ($m['peringkat']) : ?>
                                <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-medal mr-1"></i>Juara <?= $m['peringkat'] ?></span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-bold text-slate-800 text-sm"><?= esc($m['aspek_monev']) ?></h3>
                        <p class="text-slate-500"><i class="fa-solid fa-lightbulb text-indigo-400 mr-1"></i><?= esc($m['judul_karya']) ?> • <?= esc($m['nama_kompetisi']) ?></p>

                        <?php if ($selesai) : ?>
                            <?php if ($m['hasil_temuan']) : ?>
                                <p class="text-slate-600 line-clamp-2 bg-emerald-50/50 border border-emerald-100 rounded-lg p-2.5"><?= esc($m['hasil_temuan']) ?></p>
                            <?php endif; ?>
                        <?php elseif ($m['deskripsi']) : ?>
                            <div class="bg-slate-50 border rounded-lg p-2.5 text-slate-600">
                                <span class="font-semibold text-slate-700">Yang perlu disiapkan: </span><?= esc($m['deskripsi']) ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Aksi -->
                    <?php if ($selesai) : ?>
                        <div class="flex md:flex-col gap-2 shrink-0 md:w-40">
                            <button type="button" data-m="<?= esc(json_encode($m), 'attr') ?>" onclick="lihatHasil(JSON.parse(this.dataset.m))"
                                class="flex-1 border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold px-3 py-2 rounded-lg flex items-center justify-center">
                                <i class="fa-solid fa-eye mr-1.5"></i>Lihat Hasil
                            </button>
                            <?php if ($m['url_file']) : ?>
                                <a href="<?= esc($m['url_file'], 'attr') ?>" download
                                    class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-2 rounded-lg flex items-center justify-center">
                                    <i class="fa-solid fa-download mr-1.5"></i>Unduh Laporan
                                </a>
                            <?php else : ?>
                                <span class="flex-1 text-[10px] text-slate-400 text-center self-center">Tanpa file laporan</span>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL HASIL MONITORING
     ===================================================== -->
<div id="modalHasil" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Hasil Monitoring</span>
                <h3 id="h_aspek" class="font-bold text-slate-800 text-base mt-2"></h3>
                <p id="h_sub" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalHasil')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="overflow-y-auto p-5 space-y-4">
            <div>
                <div class="text-[10px] text-slate-500 uppercase font-bold mb-1">Hasil Temuan</div>
                <div id="h_temuan" class="bg-slate-50 border rounded-xl p-3.5 text-slate-700 whitespace-pre-line leading-relaxed"></div>
            </div>
            <div>
                <div class="text-[10px] text-emerald-700 uppercase font-bold mb-1">Rekomendasi</div>
                <div id="h_rekomendasi" class="bg-emerald-50 border border-emerald-200 rounded-xl p-3.5 text-slate-700 whitespace-pre-line leading-relaxed"></div>
            </div>
            <p id="h_tanggal" class="text-[11px] text-slate-400"></p>
        </div>
        <div class="flex justify-end gap-2 border-t p-4 bg-slate-50 rounded-b-2xl">
            <a id="h_unduh" download class="hidden bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg items-center">
                <i class="fa-solid fa-download mr-1.5"></i>Unduh Laporan
            </a>
            <button type="button" onclick="closeModal('modalHasil')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<script>
    function lihatHasil(m) {
        const isi = (id, teks) => document.getElementById(id).textContent = teks || '-';
        const tanggal = t => t ? new Date(t.replace(' ', 'T')).toLocaleDateString('id-ID', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        }) : '-';

        isi('h_aspek', m.aspek_monev);
        isi('h_sub', m.judul_karya + ' • dimonitoring ' + tanggal(m.tanggal_monev));
        isi('h_temuan', m.hasil_temuan);
        isi('h_rekomendasi', m.rekomendasi);
        isi('h_tanggal', m.tanggal_hasil ? 'Hasil diisi pada ' + tanggal(m.tanggal_hasil) : '');

        const unduh = document.getElementById('h_unduh');
        unduh.classList.toggle('hidden', !m.url_file);
        unduh.classList.toggle('inline-flex', !!m.url_file);
        if (m.url_file) unduh.href = m.url_file;

        openModal('modalHasil');
    }
</script>
<?= $this->endSection() ?>