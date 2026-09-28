<?php

/** @var array $stat */
/** @var array $pengumuman */
/** @var array $antrean */
/** @var int $totalAntrean */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<!-- Dashboard Content -->
<section id="sec-beranda" class="space-y-6">
    <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="bg-brand-500/30 text-brand-200 text-xs px-3 py-1 rounded-full border border-brand-400/30 font-semibold uppercase tracking-wider mb-3 inline-block">Panel Pengelola Dinas</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold mb-3 leading-tight">Selamat Datang, <?= esc(session()->get('namaAdmin')) ?></h2>
            <p class="text-slate-300 text-sm sm:text-base mb-6 leading-relaxed">Validasi praktik baik dan inovasi, kelola data satuan pendidikan, serta pantau perkembangan ekosistem inovasi SMA, SMK, dan SLB se-Sulawesi Tenggara.</p>
            <div class="flex flex-wrap gap-3">
                <button onclick="navTo('praktek-baik')" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition">
                    <i class="fa-solid fa-clipboard-check mr-2"></i>Tinjau Antrean Validasi
                </button>
                <a href="<?= base_url('sekolah') ?>" class="bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow transition">
                    <i class="fa-solid fa-school mr-2"></i>Kelola Data Sekolah
                </a>
            </div>
        </div>
        <div class="absolute right-0 bottom-0 top-0 opacity-20 pointer-events-none flex items-center pr-10">
            <img src="<?= base_url('dashboard/Tut Wuri Handayani.png') ?>" alt="" width="250px">
        </div>
    </div>
    <!-- total data dari database -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><i class="fa-solid fa-school text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Sekolah Aktif</div>
                <div class="text-xl font-bold text-slate-800" id="stat-sekolah"><?= number_format($stat['sekolah'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl"><i class="fa-solid fa-book text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Praktek Baik</div>
                <div class="text-xl font-bold text-slate-800" id="stat-praktek"><?= number_format($stat['praktik'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i class="fa-solid fa-lightbulb text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Bank Inovasi</div>
                <div class="text-xl font-bold text-slate-800" id="stat-inovasi"><?= number_format($stat['inovasi'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl"><i class="fa-solid fa-award text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Peserta Ajang</div>
                <div class="text-xl font-bold text-slate-800" id="stat-kompetisi"><?= number_format($stat['kompetisi'], 0, ',', '.') ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3 col-span-2 md:col-span-1">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl"><i class="fa-solid fa-circle-check text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Tiket SUARA</div>
                <div class="text-xl font-bold text-slate-800" id="stat-suara"><?= number_format($stat['suara'], 0, ',', '.') ?></div>
            </div>
        </div>
    </div>
    <!-- Recent Activities -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Antrean Validasi Praktik Baik -->
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="font-bold text-slate-800 flex items-center text-base">
                    <i class="fa-solid fa-clipboard-list text-amber-500 mr-2"></i>Menunggu Validasi Dinas
                    <?php if ($totalAntrean > 0) : ?>
                        <span class="ml-2 bg-amber-100 text-amber-700 text-[11px] font-bold px-2 py-0.5 rounded-full"><?= $totalAntrean ?></span>
                    <?php endif; ?>
                </h3>
                <button onclick="navTo('praktek-baik')" class="text-xs text-brand-600 font-semibold hover:underline">
                    Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </div>

            <?php if (empty($antrean)) : ?>
                <div class="flex flex-col items-center justify-center text-center py-8 px-4">
                    <div class="relative w-16 h-16 mb-4">
                        <div class="absolute inset-0 rounded-2xl bg-emerald-100 rotate-12"></div>
                        <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                            <i class="fa-solid fa-clipboard-check text-2xl text-emerald-500"></i>
                        </div>
                    </div>
                    <p class="text-sm font-semibold text-slate-700">Tidak ada antrean validasi</p>
                    <p class="text-xs text-slate-400 mt-1 max-w-[260px] leading-relaxed">
                        Semua praktik baik yang diajukan sekolah sudah divalidasi.
                    </p>
                </div>
            <?php else : ?>
                <div class="space-y-3">
                    <?php foreach ($antrean as $item) : ?>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                            <div>
                                <div class="text-[11px] font-bold text-brand-700">
                                    <?= esc($item['nama_sekolah']) ?> (<?= esc($item['kabupaten_kota']) ?>)
                                </div>
                                <div class="text-xs font-semibold text-slate-800"><?= esc($item['judul']) ?></div>
                                <div class="text-[11px] text-slate-500">
                                    <i class="fa-regular fa-user mr-1"></i><?= esc($item['nama_guru']) ?>
                                    <span class="mx-1">•</span>
                                    <i class="fa-regular fa-calendar mr-1"></i><?= date('d/m/Y', strtotime($item['tanggal_upload'])) ?>
                                </div>
                            </div>
                            <span class="text-[10px] bg-amber-100 text-amber-800 px-2.5 py-1 rounded-full font-bold whitespace-nowrap self-start sm:self-center">
                                Menunggu Validasi
                            </span>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
        <!-- Pengumuman -->
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-3 flex items-center text-sm">
                    <i class="fa-solid fa-bullhorn text-brand-600 mr-2"></i>Pengumuman Pengelola
                </h3>
                <?php if (empty($pengumuman)) : ?>
                    <!-- Tampilan saat belum ada pengumuman -->
                    <div class="flex flex-col items-center justify-center text-center py-6 px-4">
                        <div class="relative w-16 h-16 mb-4">
                            <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                            <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                                <i class="fa-regular fa-bell-slash text-2xl text-brand-500"></i>
                            </div>
                        </div>
                        <p class="text-sm font-semibold text-slate-700">Belum ada pengumuman</p>
                        <p class="text-xs text-slate-400 mt-1 max-w-[230px] leading-relaxed">
                            Pengumuman kompetisi dan informasi penting akan tampil di sini.
                        </p>
                    </div>
                <?php else : ?>
                    <?php
                    $bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                    $tgl   = fn($d) => date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d));
                    ?>
                    <div class="space-y-3">
                        <?php foreach ($pengumuman as $p) : ?>
                            <?php if ($p['hasil_diumumkan']) : ?>
                                <a href="<?= site_url('apresiasi') . '?kompetisi=' . $p['id_kompetisi'] ?>"
                                    class="block bg-emerald-50 border-l-4 border-emerald-500 p-3.5 rounded-r-lg text-xs space-y-1 hover:bg-emerald-100 transition">
                                    <p class="font-bold text-emerald-800"><i class="fa-solid fa-trophy mr-1"></i>Hasil diumumkan</p>
                                    <p class="font-semibold text-slate-800"><?= esc($p['nama_kompetisi']) ?></p>
                                    <p class="text-slate-600">Juara telah ditetapkan pada <?= $tgl($p['tanggal_pengumuman']) ?>. Klik untuk melihat pemenang.</p>
                                </a>
                            <?php else : ?>
                                <a href="<?= site_url('kompetisi') ?>"
                                    class="block bg-slate-50 border-l-4 border-brand-500 p-3.5 rounded-r-lg text-xs space-y-1 hover:bg-slate-100 transition">
                                    <p class="font-bold text-brand-700"><i class="fa-solid fa-door-open mr-1"></i>Pendaftaran dibuka</p>
                                    <p class="font-semibold text-slate-800"><?= esc($p['nama_kompetisi']) ?></p>
                                    <p class="text-slate-600">Pendaftaran ditutup <?= $tgl($p['tanggal_selesai']) ?> dengan tagar <span class="font-bold text-brand-600">#sultraeduvation</span>.</p>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <!-- Layanan SUARA Dinas -->
            <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 rounded-2xl shadow-md border border-slate-800">
                <h3 class="font-bold mb-2 flex items-center text-amber-400 text-sm"><i class="fa-solid fa-headset mr-2"></i>Layanan SUARA Dinas</h3>
                <p class="text-xs text-slate-300 mb-4 leading-relaxed">Saluran Umpan Balik, Pengaduan, & Aspirasi Penyelenggaraan Pendidikan.</p>
                <button onclick="navTo('suara')" class="w-full bg-brand-600 hover:bg-brand-500 text-white text-xs font-semibold py-2.5 rounded-xl shadow transition flex items-center justify-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Kirim Aspirasi / Cek Status
                </button>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>