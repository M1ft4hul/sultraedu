<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<section id="sec-beranda" class="space-y-6">
    <div class="bg-gradient-to-r from-brand-900 via-brand-800 to-slate-900 rounded-2xl p-6 sm:p-8 text-white shadow-xl relative overflow-hidden">
        <div class="relative z-10 max-w-2xl">
            <span class="bg-brand-500/30 text-brand-200 text-xs px-3 py-1 rounded-full border border-brand-400/30 font-semibold uppercase tracking-wider mb-3 inline-block">Portal Resmi Inovasi Daerah</span>
            <h2 class="text-2xl sm:text-4xl font-extrabold mb-3 leading-tight">Wadah Transformasi & Praktik Baik Pendidikan</h2>
            <p class="text-slate-300 text-sm sm:text-base mb-6 leading-relaxed">Menghimpun, mendokumentasikan, mengapresiasi, dan memantau inovasi satuan pendidikan SMA, SMK, dan SLB se-Provinsi Sulawesi Tenggara.</p>
            <div class="flex flex-wrap gap-3">
                <button onclick="navTo('praktek-baik'); openModalPraktekBaik()" class="bg-brand-500 hover:bg-brand-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow-md hover:shadow-lg transition">
                    <i class="fa-solid fa-plus mr-2"></i>Input Praktek Baik
                </button>
                <button onclick="navTo('kompetisi')" class="bg-slate-800 hover:bg-slate-700 border border-slate-600 text-white font-semibold text-sm px-5 py-2.5 rounded-lg shadow transition">
                    <i class="fa-solid fa-trophy mr-2"></i>Lihat Ajang Kompetisi
                </button>
            </div>
        </div>
        <div class="absolute right-0 bottom-0 top-0 opacity-20 pointer-events-none flex items-center pr-10">
            <img src="<?= base_url('dashboard/Tut Wuri Handayani.png') ?>" alt="" width="250px">
        </div>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-blue-50 text-blue-600 rounded-xl"><i class="fa-solid fa-school text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Sekolah Aktif</div>
                <div class="text-xl font-bold text-slate-800" id="stat-sekolah">184</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl"><i class="fa-solid fa-book text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Praktek Baik</div>
                <div class="text-xl font-bold text-slate-800" id="stat-praktek">342</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl"><i class="fa-solid fa-lightbulb text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Bank Inovasi</div>
                <div class="text-xl font-bold text-slate-800" id="stat-inovasi">96</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3">
            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl"><i class="fa-solid fa-award text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Peserta Ajang</div>
                <div class="text-xl font-bold text-slate-800" id="stat-kompetisi">48</div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center space-x-3 col-span-2 md:col-span-1">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl"><i class="fa-solid fa-circle-check text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Tiket SUARA</div>
                <div class="text-xl font-bold text-slate-800" id="stat-suara">128</div>
            </div>
        </div>
    </div>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
            <div class="flex justify-between items-center mb-4 border-b pb-3">
                <h3 class="font-bold text-slate-800 flex items-center text-base"><i class="fa-solid fa-star text-amber-500 mr-2"></i>Praktek Baik Unggulan Minggu Ini</h3>
                <button onclick="navTo('praktek-baik')" class="text-xs text-brand-600 font-semibold hover:underline">Lihat Semua <i class="fa-solid fa-arrow-right ml-1"></i></button>
            </div>
            <div class="space-y-3" id="home-praktek-list">
            </div>
        </div>
        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm">
                <h3 class="font-bold text-slate-800 mb-3 flex items-center text-sm"><i class="fa-solid fa-bullhorn text-brand-600 mr-2"></i>Pengumuman Pengelola</h3>
                <div class="bg-slate-50 border-l-4 border-brand-500 p-3.5 rounded-r-lg text-xs space-y-1.5">
                    <p class="font-bold text-slate-800">Kompetisi Inovasi Pendidikan 2026</p>
                    <p class="text-slate-600 leading-relaxed">Pendaftaran proposal & unggah video 3 menit dengan tagar <span class="font-bold text-brand-600">#sultraeduvation</span> ditutup tanggal 30 Oktober 2026.</p>
                </div>
            </div>

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
<section id="sec-praktek-baik" class="hidden space-y-6">
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Repository Praktek Baik Sekolah</h2>
            <p class="text-xs text-slate-500">Pengalaman, kegiatan, & strategi yang bermanfaat di tingkat satuan pendidikan (Min 1 input per sekolah).</p>
        </div>
        <button onclick="openModalPraktekBaik()" class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-plus mr-2"></i>Tambah Praktek Baik
        </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" id="praktek-grid">
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