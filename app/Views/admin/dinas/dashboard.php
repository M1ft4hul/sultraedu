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
                    <!-- Contoh tampilan pengumuman (nanti diganti data dari database) -->
                    <div class="bg-slate-50 border-l-4 border-brand-500 p-3.5 rounded-r-lg text-xs space-y-1.5">
                        <p class="font-bold text-slate-800">Kompetisi Inovasi Pendidikan 2026</p>
                        <p class="text-slate-600 leading-relaxed">Pendaftaran proposal & unggah video 3 menit dengan tagar <span class="font-bold text-brand-600">#sultraeduvation</span> ditutup tanggal 30 Oktober 2026.</p>
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
<section id="sec-bank-inovasi" class="hidden space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Bank Inovasi Pendidikan</h2>
            <p class="text-xs text-slate-500">Database resmi inovasi pendidikan terverifikasi dengan sistematika dokumen 16-poin standar Provinsi Sulawesi Tenggara.</p>
        </div>
        <button onclick="openModalBankInovasi()" class="bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-file-circle-plus mr-2"></i>Usulkan Inovasi Baru (16 Poin)
        </button>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex space-x-2 border-b pb-3 text-xs font-semibold">
            <button class="px-3.5 py-1.5 rounded-lg bg-brand-600 text-white shadow-sm">Semua Inovasi</button>
            <button class="px-3.5 py-1.5 rounded-lg bg-emerald-100 text-emerald-800 hover:bg-emerald-200">Approved (Terverifikasi)</button>
            <button class="px-3.5 py-1.5 rounded-lg bg-amber-100 text-amber-800 hover:bg-amber-200">Pending Verval</button>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600">
                <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                    <tr>
                        <th class="p-3.5 rounded-l-lg">Judul Inovasi</th>
                        <th class="p-3.5">Satuan Pendidikan</th>
                        <th class="p-3.5">Penggagas</th>
                        <th class="p-3.5">Kebaruan Utama</th>
                        <th class="p-3.5">Status Verval</th>
                        <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                    </tr>
                </thead>
                <tbody id="bank-table-body" class="divide-y divide-slate-200">
                </tbody>
            </table>
        </div>
    </div>
</section>
<section id="sec-kompetisi" class="hidden space-y-6">
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap justify-between items-center gap-4">
        <div>
            <span class="inline-block bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full mb-1 border border-amber-200">Ajang Tahunan Provinsi</span>
            <h2 class="text-xl font-bold text-slate-800">Kompetisi Inovasi Pendidikan 2026</h2>
            <p class="text-xs text-slate-500">Ketentuan Video: Max 3 Menit, Tagar <span class="font-bold text-brand-600">#sultraeduvation</span>, Inovasi min. berjalan 1 bulan.</p>
        </div>
        <button onclick="openModalKompetisi()" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-paper-plane mr-2"></i>Daftar Kompetisi Inovasi
        </button>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="bg-blue-50 border border-blue-200 p-3.5 rounded-xl">
            <div class="font-bold text-blue-900 mb-1">Kategori 1</div>
            <div class="text-slate-700">Transformasi Digital Pembelajaran & Manajemen Sekolah</div>
        </div>
        <div class="bg-emerald-50 border border-emerald-200 p-3.5 rounded-xl">
            <div class="font-bold text-emerald-900 mb-1">Kategori 2</div>
            <div class="text-slate-700">Pemerataan Akses & Inklusi Pendidikan</div>
        </div>
        <div class="bg-amber-50 border border-amber-200 p-3.5 rounded-xl">
            <div class="font-bold text-amber-900 mb-1">Kategori 3</div>
            <div class="text-slate-700">Penguatan Karakter & Gizi Anak Sekolah</div>
        </div>
        <div class="bg-purple-50 border border-purple-200 p-3.5 rounded-xl">
            <div class="font-bold text-purple-900 mb-1">Kategori 4</div>
            <div class="text-slate-700">Inovasi Pembelajaran Efektif & Potensi Peserta Didik</div>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6" id="kompetisi-grid">
    </div>
</section>
<section id="sec-apresiasi" class="hidden space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Rekam Jejak Apresiasi & Penghargaan</h2>
            <p class="text-xs text-slate-500">Dokumentasi pengakuan resmi inovasi dari Pemprov, Perguruan Tinggi, DUDI, & Institusi Resmi.</p>
        </div>
        <button onclick="openModalApresiasi()" class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-award mr-2"></i>Klaim / Input Apresiasi
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6" id="apresiasi-grid">
    </div>
</section>
<section id="sec-monev" class="hidden space-y-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Dashboard Monitoring & Evaluasi (Monev)</h2>
        <p class="text-xs text-slate-500">Pemantauan 10 Indikator Utama Keberlanjutan Ekosistem Inovasi Pendidikan Daerah.</p>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 mb-3 uppercase tracking-wider"><i class="fa-solid fa-chart-column text-brand-600 mr-2"></i>Partisipasi Sekolah per Kabupaten/Kota</h3>
            <div class="h-64"><canvas id="chartPartisipasi"></canvas></div>
        </div>
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 mb-3 uppercase tracking-wider"><i class="fa-solid fa-chart-pie text-indigo-600 mr-2"></i>Sebaran Inovasi per Kategori</h3>
            <div class="h-64"><canvas id="chartKategori"></canvas></div>
        </div>
    </div>
    <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
        <h3 class="font-bold text-slate-800 mb-4 text-sm flex items-center"><i class="fa-solid fa-list-check text-emerald-600 mr-2"></i>Status 10 Indikator Utama Pemantauan (Bab IX)</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-xs">
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>1. Tingkat Partisipasi Sekolah</span>
                <span class="font-bold text-emerald-600">78.4% (Tinggi)</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>2. Praktek Baik Terdokumentasi</span>
                <span class="font-bold text-brand-600">342 Berkas</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>3. Inovasi Masuk Bank Inovasi</span>
                <span class="font-bold text-indigo-600">96 Terverifikasi</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>4. Perkembangan Implementasi</span>
                <span class="font-bold text-amber-600">Aktif Pemantauan</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>5. Inovasi Memperoleh Apresiasi</span>
                <span class="font-bold text-purple-600">18 Inovasi</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>6. Rekapitulasi Kompetisi</span>
                <span class="font-bold text-blue-600">Gelombang I Berjalan</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>7. Response Rate SUARA</span>
                <span class="font-bold text-emerald-600">94% Selesai</span>
            </div>
            <div class="p-3 bg-slate-50 border rounded-xl flex justify-between items-center">
                <span>8. Manfaat & Dampak Nyata</span>
                <span class="font-bold text-slate-700">Terukur (Monev Lapangan)</span>
            </div>
        </div>
    </div>
</section>
<section id="sec-suara" class="hidden space-y-6">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4">
            <div>
                <h2 class="text-xl font-bold text-slate-800">SUARA (Saluran Umpan Balik & Aspirasi)</h2>
                <p class="text-xs text-slate-500">Sampaikan saran, kritik, pertanyaan, pengaduan kendala, atau aspirasi terkait penyelenggaraan pendidikan.</p>
            </div>

            <form id="formSuara" onsubmit="submitSuara(event)" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Nama Pengirim / Unit Sekolah</label>
                        <input type="text" id="suara_nama" required placeholder="Contoh: SMAN 1 Kendari / Drs. Ahmad" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Email / WhatsApp</label>
                        <input type="text" id="suara_kontak" required placeholder="email@sekolah.sch.id / 0812xxx" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Kategori SUARA</label>
                        <select id="suara_kategori" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                            <option value="Saran">Saran Pengembangan</option>
                            <option value="Kritik">Kritik & Masukan</option>
                            <option value="Pertanyaan">Pertanyaan Teknis</option>
                            <option value="Pengaduan">Pengaduan Kendala Lapangan</option>
                        </select>
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Subjek / Topik</label>
                        <input type="text" id="suara_subjek" required placeholder="Ringkasan topik aduan/aspirasi" class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
                    </div>
                </div>

                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Pesan Lengkap / Detail Aspirasi</label>
                    <textarea id="suara_pesan" rows="4" required placeholder="Uraikan secara spesifik masukan atau permasalahan Anda..." class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
                </div>

                <button type="submit" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold py-2.5 px-6 rounded-xl shadow transition flex items-center">
                    <i class="fa-solid fa-paper-plane mr-2"></i>Kirim Tiket SUARA
                </button>
            </form>
        </div>
        <div class="space-y-6">
            <div class="bg-slate-900 text-white p-6 rounded-2xl shadow-md border border-slate-800">
                <h3 class="font-bold mb-3 flex items-center text-sm"><i class="fa-solid fa-magnifying-glass text-brand-400 mr-2"></i>Lacak Tiket SUARA Anda</h3>
                <div class="space-y-3">
                    <input type="text" id="track_code" placeholder="Masukkan Kode Tiket (mis: SVR-202609-001)" class="w-full bg-slate-800 border border-slate-700 rounded-xl p-2.5 text-xs text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <button onclick="trackTiket()" class="w-full bg-brand-500 hover:bg-brand-600 text-xs font-semibold py-2.5 rounded-xl shadow transition">
                        Cek Status & Tanggapan Dinas
                    </button>
                </div>
                <div id="track_result" class="mt-4 hidden text-xs p-3.5 bg-slate-800/90 rounded-xl border border-slate-700">
                </div>
            </div>
            <div id="admin-suara-box" class="bg-amber-50 border border-amber-200 p-5 rounded-2xl text-xs space-y-3">
                <div class="font-bold text-amber-900 flex items-center"><i class="fa-solid fa-user-shield mr-2"></i>Desk Pengelola SUARA (Mode Admin)</div>
                <p class="text-slate-600">Klik tiket untuk memberikan disposisi/respon resmi pengelola:</p>
                <div id="admin-suara-list" class="space-y-2">
                </div>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>