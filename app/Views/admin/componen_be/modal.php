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