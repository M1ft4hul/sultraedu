<?php include 'style.php' ?>
<body class="bg-slate-50 text-slate-800 flex flex-col min-h-screen font-sans">
   
    <main class="flex-grow max-w-7xl w-full mx-auto px-4 py-6">
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
    </main>
    <footer class="bg-slate-900 border-t border-slate-800 text-slate-400 text-xs py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 flex flex-wrap justify-between items-center gap-4">
            <div>
                <p class="font-bold text-slate-200">EDUVATION &copy; 2026 Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara</p>
                <p class="text-slate-400">Platform Ekosistem Inovasi & Praktik Baik Pendidikan Terintegrasi</p>
            </div>
            <div class="flex space-x-4">
                <span class="hover:underline cursor-pointer">Panduan Penggunaan</span>
                <span class="hover:underline cursor-pointer">Syarat & Ketentuan</span>
                <span class="hover:underline cursor-pointer">Helpdesk SUARA</span>
            </div>
        </div>
    </footer>
    <div id="modalPraktekBaik" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-book-open text-brand-600 mr-2"></i>Form Input Ringkas Praktek Baik (Lampiran II)</h3>
                <button onclick="closeModal('modalPraktekBaik')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form onsubmit="savePraktekBaik(event)" class="space-y-4 text-xs">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Nama Praktek Baik</label>
                    <input type="text" id="pb_nama" required placeholder="misal: Program Literasi Pagi 'Satu Buku Satu Pekan'" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Nama Satuan Pendidikan</label>
                        <input type="text" id="pb_sekolah" required placeholder="SMAN 1 Kendari" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Kabupaten/Kota</label>
                        <input type="text" id="pb_kab" required placeholder="Kota Kendari" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Permasalahan yang Dihadapi</label>
                    <textarea id="pb_masalah" rows="2" required placeholder="Rendahnya minat baca peserta didik..." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Praktek Baik & Pelaksanaan</label>
                    <textarea id="pb_solusi" rows="2" required placeholder="Menyediakan pojok baca kreatif..." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Hasil & Manfaat Real</label>
                    <textarea id="pb_hasil" rows="2" required placeholder="Peningkatan peminjaman buku sebesar 65%..." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div class="flex justify-end space-x-2 border-t pt-3">
                    <button type="button" onclick="closeModal('modalPraktekBaik')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">Simpan Praktek Baik</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modalBankInovasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-3xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-lightbulb text-indigo-600 mr-2"></i>Usulkan Inovasi (Standar 16-Poin Bank Inovasi)</h3>
                    <p class="text-[11px] text-slate-500">Sistematika Dokumen Inovasi Pendidikan Terverifikasi Dinas</p>
                </div>
                <button onclick="closeModal('modalBankInovasi')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form onsubmit="saveBankInovasi(event)" class="space-y-4 text-xs">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Judul Inovasi Pendidikan</label>
                        <input type="text" id="bi_judul" required placeholder="SIM-PRESENCE Vokasi..." class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Nama Penggagas / Tim Inovator</label>
                        <input type="text" id="bi_penggagas" required placeholder="Drs. H. Ahmad & Tim IT" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Satuan Pendidikan</label>
                        <input type="text" id="bi_sekolah" required placeholder="SMKN 1 Kendari" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Kebaruan Utama (Unsur Inovasi)</label>
                        <input type="text" id="bi_kebaruan" required placeholder="Integrasi GPS Geofencing dengan Notifikasi WhatsApp" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Latar Belakang & Permasalahan Ringkas</label>
                    <textarea id="bi_masalah" rows="2" required placeholder="Uraikan latar belakang masalah..." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div class="flex justify-end space-x-2 border-t pt-3">
                    <button type="button" onclick="closeModal('modalBankInovasi')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">Kirim untuk Verval Dinas</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modalKompetisi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-trophy text-amber-500 mr-2"></i>Pendaftaran Ajang Kompetisi Inovasi 2026</h3>
                <button onclick="closeModal('modalKompetisi')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>
            <form onsubmit="saveKompetisi(event)" class="space-y-4 text-xs">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Judul Inovasi yang Didaftarkan</label>
                    <input type="text" id="komp_judul" required placeholder="EduBot Sultra: Asisten AI Belajar..." class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Nama Satuan Pendidikan</label>
                        <input type="text" id="komp_sekolah" required placeholder="SMAN 2 Kendari" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Kategori Kompetisi</label>
                        <select id="komp_kategori" class="w-full border border-slate-300 rounded-lg p-2.5">
                            <option>Kategori 1: Transformasi Digital</option>
                            <option>Kategori 2: Akses & Inklusi</option>
                            <option>Kategori 3: Karakter & Gizi</option>
                            <option>Kategori 4: Pembelajaran Efektif</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Tautan Video Media Sosial (Max 3 Menit & Tagar #sultraeduvation)</label>
                    <input type="url" id="komp_video" required placeholder="https://www.youtube.com/watch?v=xxx" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div class="flex justify-end space-x-2 border-t pt-3">
                    <button type="button" onclick="closeModal('modalKompetisi')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">Daftarkan Ke Kompetisi</button>
                </div>
            </form>
        </div>
    </div>
    <div id="modalJuriScoring" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b pb-3 mb-4">
                <div>
                    <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-gavel text-amber-500 mr-2"></i>Lembar Penilaian Juri Kompetisi</h3>
                    <p class="text-[11px] text-slate-500" id="juri_target_title">Inovasi: -</p>
                </div>
                <button onclick="closeModal('modalJuriScoring')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <form onsubmit="saveJuriScore(event)" class="space-y-3 text-xs">
                <input type="hidden" id="juri_kompetisi_id">
                
                <div class="bg-amber-50 p-3.5 rounded-xl border border-amber-200 flex justify-between items-center">
                    <span class="font-bold text-amber-900">Total Skor Akhir:</span>
                    <span class="text-2xl font-black text-amber-600" id="total_skor_live">0 / 100</span>
                </div>

                <div class="space-y-2 divide-y">
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">1. Relevansi Permasalahan (Max 15%)</div>
                            <div class="text-[10px] text-slate-500">Kesesuaian solusi dengan masalah nyata sekolah</div>
                        </div>
                        <input type="number" min="0" max="15" id="score_1" value="12" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">2. Kebaruan / Inovasi (Max 20%)</div>
                            <div class="text-[10px] text-slate-500">Unsur kebaruan dan nilai keunikan strategi</div>
                        </div>
                        <input type="number" min="0" max="20" id="score_2" value="16" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">3. Efektivitas & Hasil (Max 20%)</div>
                            <div class="text-[10px] text-slate-500">Capaian target dan efektivitas implementasi</div>
                        </div>
                        <input type="number" min="0" max="20" id="score_3" value="17" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">4. Manfaat & Dampak (Max 15%)</div>
                            <div class="text-[10px] text-slate-500">Dampak bagi peserta didik & satuan pendidikan</div>
                        </div>
                        <input type="number" min="0" max="15" id="score_4" value="13" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">5. Keberlanjutan (Max 10%)</div>
                            <div class="text-[10px] text-slate-500">Potensi inovasi terus berjalan jangka panjang</div>
                        </div>
                        <input type="number" min="0" max="10" id="score_5" value="8" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">6. Potensi Replikasi (Max 10%)</div>
                            <div class="text-[10px] text-slate-500">Kemudahan diadopsi oleh sekolah lain</div>
                        </div>
                        <input type="number" min="0" max="10" id="score_6" value="8" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                    <div class="pt-2 flex justify-between items-center">
                        <div>
                            <div class="font-semibold">7. Dokumentasi & Video (Max 10%)</div>
                            <div class="text-[10px] text-slate-500">Kejelasan penyajian video 3 menit #sultraeduvation</div>
                        </div>
                        <input type="number" min="0" max="10" id="score_7" value="9" oninput="calcJuriScore()" class="w-16 border rounded p-1 text-center font-bold">
                    </div>
                </div>

                <div class="flex justify-end space-x-2 border-t pt-3">
                    <button type="button" onclick="closeModal('modalJuriScoring')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-semibold">Simpan Penilaian Juri</button>
                </div>
            </form>
        </div>
    </div>
    <div id="toastNotification" class="fixed bottom-5 right-5 z-50 transform translate-y-20 opacity-0 transition-all duration-300 pointer-events-none">
        <div id="toastBox" class="bg-slate-900 text-white px-5 py-3.5 rounded-xl shadow-2xl border border-slate-700 flex items-center space-x-3 max-w-md">
            <div id="toastIcon" class="text-emerald-400 text-lg"><i class="fa-solid fa-circle-check"></i></div>
            <div>
                <div id="toastTitle" class="font-bold text-xs">Pemberitahuan System</div>
                <div id="toastMsg" class="text-[11px] text-slate-300">Pesan sukses disini...</div>
            </div>
        </div>
    </div>
    <script>
        let currentRole = 'sekolah';

        let praktekBaikData = [
            { id: 1, nama: "Program 'Pagi Berliterasi & Digital Quiet Hour'", sekolah: "SMAN 1 Kendari", kab: "Kota Kendari", masalah: "Rendahnya retensi fokus membaca siswa di era HP", solusi: "Penyediaan 15 menit membaca rutin sebelum KBM dengan e-library offline", hasil: "Meningkatkan durasi membaca siswa hingga 85%", tanggal: "2026-09-10" },
            { id: 2, nama: "Kantin Kejujuran Berbasis Kasir Tab Mandiri", sekolah: "SMKN 2 Bau-Bau", kab: "Kota Bau-Bau", masalah: "Penguatan karakter kejujuran & kemandirian siswa vokasi", solusi: "Sistem transaksi mandiri tanpa penjaga dengan pencatatan digital", hasil: "Tingkat akurasi kas sebesar 98.5% selama 6 bulan", tanggal: "2026-09-14" },
            { id: 3, nama: "Sensory Garden & Terapi Inklusi SLB", sekolah: "SLBN 1 Konawe", kab: "Kab. Konawe", masalah: "Kebutuhan stimulasi sensorik bagi anak berkebutuhan khusus", solusi: "Taman terapi tanaman herbal & jalur batu alam interaktif", hasil: "Meningkatkan ketenangan & ketrampilan kognitif siswa", tanggal: "2026-09-18" }
        ];

        let bankInovasiData = [
            { id: 101, judul: "SIM-PRESENCE: Presensi Geofencing & Notifikasi Ortu", sekolah: "SMKN 1 Kendari", penggagas: "Drs. H. Ahmad & Tim IT", kebaruan: "Integrasi GPS lokasi sekolah dengan Bot WhatsApp Orang Tua secara real-time", status: "approved" },
            { id: 102, judul: "Modul Vokasi Adaptif Pertanian Barcode QR", sekolah: "SMKN 4 Konawe Selatan", penggagas: "Siti Rahma, S.Pd", kebaruan: "Media tanam dilengkapi barcode menuju video tutorial Bahasa Daerah", status: "pending" }
        ];

        let kompetisiData = [
            { id: 201, judul: "EduBot Sultra: Asisten AI Belajar Matematika Daerah", sekolah: "SMAN 2 Kendari", kategori: "Kategori 1: Transformasi Digital", videoUrl: "https://www.youtube.com/watch?v=demo1", skorJuri: 83, statusScored: true },
            { id: 202, judul: "Dapur Gizi Sekolah Sehat Bebas Stunting", sekolah: "SMAN 3 Kolaka", kategori: "Kategori 3: Penguatan Karakter & Gizi", videoUrl: "https://www.youtube.com/watch?v=demo2", skorJuri: 0, statusScored: false }
        ];

        let apresiasiData = [
            { id: 301, inovasi: "SIM-PRESENCE Vokasi", sekolah: "SMKN 1 Kendari", pemberi: "Gubernur / Dinas Pendidikan Provinsi Sulawesi Tenggara", tahun: "2026", status: "Verified" },
            { id: 302, inovasi: "E-Rubrik Inklusi SLB", sekolah: "SLBN 1 Kendari", pemberi: "Lembaga Penjaminan Mutu Pendidikan", tahun: "2025", status: "Verified" }
        ];

        let suaraTickets = [
            { code: "SVR-202609-001", nama: "SMAN 1 Kolaka", kontak: "0811223344", kategori: "Pertanyaan", subjek: "Format unggah proposal kompetisi", pesan: "Apakah proposal kompetisi wajib menyertakan surat rekomendasi Cabdin?", status: "Selesai", respon: "Ya, surat rekomendasi Cabdin diunggah gabung pada halaman lampiran PDF proposal." },
            { code: "SVR-202609-002", nama: "Gurusiana Sultra", kontak: "guru@gmail.com", kategori: "Saran", subjek: "Penambahan kategori inovasi bahasa daerah", pesan: "Mohon dipertimbangkan penambahan kategori pelestarian bahasa daerah di kompetisi mendatang.", status: "Diproses", respon: "" }
        ];

        function navTo(tabId) {
            const tabs = ['beranda', 'praktek-baik', 'bank-inovasi', 'kompetisi', 'apresiasi', 'monev', 'suara'];
            tabs.forEach(t => {
                document.getElementById(`sec-${t}`).classList.add('hidden');
                document.getElementById(`tab-${t}`).classList.remove('active-tab');
            });
            document.getElementById(`sec-${tabId}`).classList.remove('hidden');
            document.getElementById(`tab-${tabId}`).classList.add('active-tab');

            if (tabId === 'monev') initMonevCharts();
        }

        function switchRole(role) {
            currentRole = role;
            const badge = document.getElementById('roleBadge');
            const adminSuaraBox = document.getElementById('admin-suara-box');
            
            if (role === 'sekolah') {
                badge.innerText = "Sekolah";
                badge.className = "bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30";
                if(adminSuaraBox) adminSuaraBox.classList.add('hidden');
            } else if (role === 'admin') {
                badge.innerText = "Admin Dinas";
                badge.className = "bg-brand-500/20 text-brand-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-brand-500/30";
                if(adminSuaraBox) adminSuaraBox.classList.remove('hidden');
            } else if (role === 'juri') {
                badge.innerText = "Tim Juri";
                badge.className = "bg-amber-500/20 text-amber-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-amber-500/30";
                if(adminSuaraBox) adminSuaraBox.classList.add('hidden');
            } else {
                badge.innerText = "Publik";
                badge.className = "bg-purple-500/20 text-purple-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-purple-500/30";
                if(adminSuaraBox) adminSuaraBox.classList.add('hidden');
            }
            renderAllViews();
            showToast("Simulasi Akses Diubah", `Akses pengguna kini disimulasikan sebagai: ${role.toUpperCase()}`, 'info');
        }

        function showToast(title, message, type = 'success') {
            const toast = document.getElementById('toastNotification');
            const tTitle = document.getElementById('toastTitle');
            const tMsg = document.getElementById('toastMsg');
            const tIcon = document.getElementById('toastIcon');

            tTitle.innerText = title;
            tMsg.innerText = message;

            if (type === 'success') {
                tIcon.innerHTML = `<i class="fa-solid fa-circle-check text-emerald-400"></i>`;
            } else if (type === 'info') {
                tIcon.innerHTML = `<i class="fa-solid fa-circle-info text-brand-400"></i>`;
            } else {
                tIcon.innerHTML = `<i class="fa-solid fa-triangle-exclamation text-amber-400"></i>`;
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3500);
        }

        function renderAllViews() {
            renderPraktekBaik();
            renderBankInovasi();
            renderKompetisi();
            renderApresiasi();
            renderSuaraAdmin();
        }

        function renderPraktekBaik() {
            const homeList = document.getElementById('home-praktek-list');
            const grid = document.getElementById('praktek-grid');
            
            homeList.innerHTML = '';
            grid.innerHTML = '';

            praktekBaikData.forEach((item, index) => {
                if (index < 2) {
                    homeList.innerHTML += `
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-200 flex flex-col sm:flex-row justify-between sm:items-center gap-2">
                            <div>
                                <div class="text-[11px] font-bold text-brand-700">${item.sekolah} (${item.kab})</div>
                                <div class="text-xs font-semibold text-slate-800">${item.nama}</div>
                                <div class="text-[11px] text-slate-500 line-clamp-1">${item.hasil}</div>
                            </div>
                            <span class="text-[10px] bg-emerald-100 text-emerald-800 px-2.5 py-1 rounded-full font-bold whitespace-nowrap self-start sm:self-center">Terdokumentasi</span>
                        </div>
                    `;
                }

                grid.innerHTML += `
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div class="space-y-2.5">
                            <div class="flex justify-between items-start">
                                <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full">${item.kab}</span>
                                <span class="text-[10px] text-slate-400"><i class="fa-regular fa-calendar mr-1"></i>${item.tanggal}</span>
                            </div>
                            <h4 class="font-bold text-slate-800 text-sm leading-snug">${item.nama}</h4>
                            <p class="text-xs font-semibold text-brand-600"><i class="fa-solid fa-school mr-1.5"></i>${item.sekolah}</p>
                            <div class="text-xs text-slate-600 bg-slate-50 p-3 rounded-xl border space-y-1">
                                <div><strong class="text-slate-700">Masalah:</strong> ${item.masalah}</div>
                                <div><strong class="text-slate-700">Solusi:</strong> ${item.solusi}</div>
                            </div>
                        </div>
                        <div class="mt-4 pt-3 border-t flex justify-between items-center text-xs">
                            <span class="text-emerald-600 font-bold"><i class="fa-solid fa-circle-check mr-1"></i>${item.hasil}</span>
                        </div>
                    </div>
                `;
            });
        }

        function renderBankInovasi() {
            const tbody = document.getElementById('bank-table-body');
            tbody.innerHTML = '';

            bankInovasiData.forEach(item => {
                const statusBadge = item.status === 'approved' 
                    ? `<span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Approved (Bank Inovasi)</span>`
                    : `<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-clock mr-1"></i>Pending Verval</span>`;

                const actionButton = (currentRole === 'admin' && item.status === 'pending')
                    ? `<button onclick="approveInovasi(${item.id})" class="bg-emerald-600 text-white text-[10px] px-2.5 py-1 rounded-lg hover:bg-emerald-700 font-semibold shadow-sm">Setujui Verval</button>`
                    : `<span class="text-slate-400 text-[11px]">Sistematika 16 Poin Lengkap</span>`;

                tbody.innerHTML += `
                    <tr class="hover:bg-slate-50 transition">
                        <td class="p-3.5 font-bold text-slate-800">${item.judul}</td>
                        <td class="p-3.5">${item.sekolah}</td>
                        <td class="p-3.5">${item.penggagas}</td>
                        <td class="p-3.5 max-w-xs truncate">${item.kebaruan}</td>
                        <td class="p-3.5">${statusBadge}</td>
                        <td class="p-3.5 text-center">${actionButton}</td>
                    </tr>
                `;
            });
        }

        function renderKompetisi() {
            const grid = document.getElementById('kompetisi-grid');
            grid.innerHTML = '';

            kompetisiData.forEach(item => {
                const scoreDisplay = item.statusScored 
                    ? `<span class="text-xl font-extrabold text-amber-600">${item.skorJuri} / 100</span>`
                    : `<span class="text-xs text-slate-400 italic">Belum Dinilai Juri</span>`;

                const juriButton = (currentRole === 'juri' || currentRole === 'admin')
                    ? `<button onclick="openModalJuri(${item.id}, '${item.judul}')" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow">
                        <i class="fa-solid fa-gavel mr-1"></i>Input Nilai (7 Kriteria)
                       </button>`
                    : '';

                grid.innerHTML += `
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3">
                        <div class="flex justify-between items-start">
                            <span class="bg-indigo-50 text-indigo-700 font-bold text-[10px] px-2.5 py-1 rounded-full">${item.kategori}</span>
                            <span class="text-xs font-bold text-slate-500"><i class="fa-hashtag text-brand-600"></i>sultraeduvation</span>
                        </div>
                        <h4 class="font-bold text-slate-800 text-base">${item.judul}</h4>
                        <p class="text-xs text-brand-600 font-semibold"><i class="fa-solid fa-school mr-1.5"></i>${item.sekolah}</p>
                        
                        <div class="bg-slate-900 rounded-xl p-4 text-center text-white text-xs space-y-2">
                            <i class="fa-solid fa-circle-play text-3xl text-amber-400"></i>
                            <div>Video Inovasi Durasi Max 3 Menit</div>
                            <a href="${item.videoUrl}" target="_blank" class="text-[10px] text-brand-300 underline block">Buka Tautan Media Sosial Sekolah</a>
                        </div>

                        <div class="pt-3 border-t flex justify-between items-center">
                            <div>
                                <div class="text-[10px] text-slate-400 font-medium">Skor Akhir Juri:</div>
                                ${scoreDisplay}
                            </div>
                            ${juriButton}
                        </div>
                    </div>
                `;
            });
        }

        function renderApresiasi() {
            const grid = document.getElementById('apresiasi-grid');
            grid.innerHTML = '';

            apresiasiData.forEach(item => {
                grid.innerHTML += `
                    <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm space-y-3 relative overflow-hidden">
                        <div class="absolute -right-3 -top-3 opacity-10 pointer-events-none"><i class="fa-solid fa-award text-8xl text-emerald-600"></i></div>
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full border border-emerald-200"><i class="fa-solid fa-shield-halved mr-1"></i>Verifikasi Resmi</span>
                        <h4 class="font-bold text-slate-800 text-sm">${item.inovasi}</h4>
                        <div class="text-xs text-slate-600 space-y-1">
                            <div><strong>Satuan:</strong> ${item.sekolah}</div>
                            <div><strong>Lembaga Pemberi:</strong> ${item.pemberi}</div>
                            <div><strong>Tahun:</strong> ${item.tahun}</div>
                        </div>
                    </div>
                `;
            });
        }

        function renderSuaraAdmin() {
            const list = document.getElementById('admin-suara-list');
            if(!list) return;
            list.innerHTML = '';

            suaraTickets.forEach(t => {
                list.innerHTML += `
                    <div class="bg-white p-3 rounded-xl border border-amber-200 flex justify-between items-center">
                        <div>
                            <span class="font-bold text-brand-700">[${t.code}]</span> <span class="font-semibold">${t.subjek}</span>
                            <div class="text-[10px] text-slate-500">${t.nama} • Status: <strong class="text-slate-800">${t.status}</strong></div>
                        </div>
                        <button onclick="respondSuara('${t.code}')" class="bg-brand-600 hover:bg-brand-700 text-white text-[10px] px-2.5 py-1 rounded-lg font-semibold">Tanggapi</button>
                    </div>
                `;
            });
        }

        function openModal(id) { document.getElementById(id).classList.remove('hidden'); }
        function closeModal(id) { document.getElementById(id).classList.add('hidden'); }

        function openModalPraktekBaik() { openModal('modalPraktekBaik'); }
        function openModalBankInovasi() { openModal('modalBankInovasi'); }
        function openModalKompetisi() { openModal('modalKompetisi'); }
        function openModalApresiasi() { showToast("Klaim Apresiasi", "Silakan unggah SK/Sertifikat Penghargaan untuk verifikasi Dinas.", "info"); }

        function savePraktekBaik(e) {
            e.preventDefault();
            const newPb = {
                id: Date.now(),
                nama: document.getElementById('pb_nama').value,
                sekolah: document.getElementById('pb_sekolah').value,
                kab: document.getElementById('pb_kab').value,
                masalah: document.getElementById('pb_masalah').value,
                solusi: document.getElementById('pb_solusi').value,
                hasil: document.getElementById('pb_hasil').value,
                tanggal: "2026-09-23"
            };
            praktekBaikData.unshift(newPb);
            closeModal('modalPraktekBaik');
            renderPraktekBaik();
            showToast("Berhasil Disimpan", "Praktek Baik berhasil disimpan dan terpublikasi di portal!", "success");
        }

        function saveBankInovasi(e) {
            e.preventDefault();
            const newBi = {
                id: Date.now(),
                judul: document.getElementById('bi_judul').value,
                sekolah: document.getElementById('bi_sekolah').value,
                penggagas: document.getElementById('bi_penggagas').value,
                kebaruan: document.getElementById('bi_kebaruan').value,
                status: 'pending'
            };
            bankInovasiData.unshift(newBi);
            closeModal('modalBankInovasi');
            renderBankInovasi();
            showToast("Usulan Inovasi Dikirim", "Dokumen 16-Poin Inovasi berhasil diajukan untuk Verval Dinas.", "success");
        }

        function saveKompetisi(e) {
            e.preventDefault();
            const newKomp = {
                id: Date.now(),
                judul: document.getElementById('komp_judul').value,
                sekolah: document.getElementById('komp_sekolah').value,
                kategori: document.getElementById('komp_kategori').value,
                videoUrl: document.getElementById('komp_video').value,
                skorJuri: 0,
                statusScored: false
            };
            kompetisiData.unshift(newKomp);
            closeModal('modalKompetisi');
            renderKompetisi();
            showToast("Pendaftaran Berhasil", "Inovasi Anda terdaftar dalam Ajang Kompetisi 2026 (#sultraeduvation)!", "success");
        }

        function approveInovasi(id) {
            const item = bankInovasiData.find(x => x.id === id);
            if (item) {
                item.status = 'approved';
                renderBankInovasi();
                showToast("Verval Disetujui", `Inovasi "${item.judul}" resmi masuk Bank Inovasi Provinsi!`, "success");
            }
        }

        function openModalJuri(id, judul) {
            document.getElementById('juri_kompetisi_id').value = id;
            document.getElementById('juri_target_title').innerText = "Inovasi: " + judul;
            calcJuriScore();
            openModal('modalJuriScoring');
        }

        function calcJuriScore() {
            let s1 = parseInt(document.getElementById('score_1').value) || 0;
            let s2 = parseInt(document.getElementById('score_2').value) || 0;
            let s3 = parseInt(document.getElementById('score_3').value) || 0;
            let s4 = parseInt(document.getElementById('score_4').value) || 0;
            let s5 = parseInt(document.getElementById('score_5').value) || 0;
            let s6 = parseInt(document.getElementById('score_6').value) || 0;
            let s7 = parseInt(document.getElementById('score_7').value) || 0;

            let total = s1 + s2 + s3 + s4 + s5 + s6 + s7;
            document.getElementById('total_skor_live').innerText = `${total} / 100`;
            return total;
        }

        function saveJuriScore(e) {
            e.preventDefault();
            let id = parseInt(document.getElementById('juri_kompetisi_id').value);
            let total = calcJuriScore();
            
            let item = kompetisiData.find(x => x.id === id);
            if (item) {
                item.skorJuri = total;
                item.statusScored = true;
                renderKompetisi();
                closeModal('modalJuriScoring');
                showToast("Penilaian Disimpan", "Nilai 7 Kriteria Rubrik berhasil diperbarui!", "success");
            }
        }

        function submitSuara(e) {
            e.preventDefault();
            const code = "SVR-202609-00" + (suaraTickets.length + 1);
            const newTicket = {
                code: code,
                nama: document.getElementById('suara_nama').value,
                kontak: document.getElementById('suara_kontak').value,
                kategori: document.getElementById('suara_kategori').value,
                subjek: document.getElementById('suara_subjek').value,
                pesan: document.getElementById('suara_pesan').value,
                status: "Diproses",
                respon: ""
            };
            suaraTickets.push(newTicket);
            renderSuaraAdmin();
            document.getElementById('formSuara').reset();
            showToast("Tiket SUARA Terkirim", `Kode Tiket Anda: ${code}. Gunakan kode ini untuk melacak tanggapan.`, "success");
        }

        function trackTiket() {
            const code = document.getElementById('track_code').value.trim();
            const res = document.getElementById('track_result');
            const item = suaraTickets.find(x => x.code.toLowerCase() === code.toLowerCase());

            res.classList.remove('hidden');
            if (item) {
                res.innerHTML = `
                    <div class="font-bold text-amber-400">Kode Tiket: ${item.code}</div>
                    <div class="text-white mt-1"><strong>Subjek:</strong> ${item.subjek}</div>
                    <div class="text-slate-300"><strong>Status:</strong> <span class="text-emerald-400 font-bold">${item.status}</span></div>
                    <div class="mt-2 pt-2 border-t border-slate-700 text-slate-200">
                        <strong>Tanggapan Pengelola:</strong><br>
                        ${item.respon ? item.respon : '<em class="text-slate-400">Sedang didisposisikan ke tim teknis/bidang terkait...</em>'}
                    </div>
                `;
            } else {
                res.innerHTML = `<span class="text-rose-400">Kode tiket tidak ditemukan. Pastikan format benar (contoh: SVR-202609-001).</span>`;
            }
        }

        function respondSuara(code) {
            const item = suaraTickets.find(x => x.code === code);
            if (item) {
                const resp = prompt(`Tanggapan Pengelola untuk Tiket ${code} (${item.subjek}):`, item.respon || "Terima kasih atas aspirasinya, telah kami koordinasikan dengan Bidang terkait.");
                if (resp !== null) {
                    item.respon = resp;
                    item.status = "Selesai";
                    renderSuaraAdmin();
                    showToast("Respon Dikirim", "Tanggapan resmi pengelola telah diperbarui pada tiket tersebut.", "success");
                }
            }
        }

        let chart1, chart2;
        function initMonevCharts() {
            if (chart1) chart1.destroy();
            if (chart2) chart2.destroy();

            const ctx1 = document.getElementById('chartPartisipasi').getContext('2d');
            chart1 = new Chart(ctx1, {
                type: 'bar',
                data: {
                    labels: ['Kendari', 'Bau-Bau', 'Konawe', 'Kolaka', 'Muna', 'Konsel'],
                    datasets: [{
                        label: 'Jumlah Inovasi & Praktek Baik',
                        data: [42, 28, 35, 22, 19, 31],
                        backgroundColor: '#0284c7'
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });

            const ctx2 = document.getElementById('chartKategori').getContext('2d');
            chart2 = new Chart(ctx2, {
                type: 'doughnut',
                data: {
                    labels: ['Kat 1: Digital', 'Kat 2: Akses/Inklusi', 'Kat 3: Karakter/Gizi', 'Kat 4: Pembelajaran'],
                    datasets: [{
                        data: [35, 20, 25, 20],
                        backgroundColor: ['#0284c7', '#10b981', '#f59e0b', '#8b5cf6']
                    }]
                },
                options: { responsive: true, maintainAspectRatio: false }
            });
        }

        window.onload = function() {
            renderAllViews();
        };
    </script>
</body>
</html>