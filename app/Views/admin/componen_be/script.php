<script>
    // let currentRole = 'sekolah';
    let currentRole = <?= json_encode(session()->get('role') === 'admin_pusat' ? 'admin' : 'sekolah') ?>;

    let praktekBaikData = [{
            id: 1,
            nama: "Program 'Pagi Berliterasi & Digital Quiet Hour'",
            sekolah: "SMAN 1 Kendari",
            kab: "Kota Kendari",
            masalah: "Rendahnya retensi fokus membaca siswa di era HP",
            solusi: "Penyediaan 15 menit membaca rutin sebelum KBM dengan e-library offline",
            hasil: "Meningkatkan durasi membaca siswa hingga 85%",
            tanggal: "2026-09-10"
        },
        {
            id: 2,
            nama: "Kantin Kejujuran Berbasis Kasir Tab Mandiri",
            sekolah: "SMKN 2 Bau-Bau",
            kab: "Kota Bau-Bau",
            masalah: "Penguatan karakter kejujuran & kemandirian siswa vokasi",
            solusi: "Sistem transaksi mandiri tanpa penjaga dengan pencatatan digital",
            hasil: "Tingkat akurasi kas sebesar 98.5% selama 6 bulan",
            tanggal: "2026-09-14"
        },
        {
            id: 3,
            nama: "Sensory Garden & Terapi Inklusi SLB",
            sekolah: "SLBN 1 Konawe",
            kab: "Kab. Konawe",
            masalah: "Kebutuhan stimulasi sensorik bagi anak berkebutuhan khusus",
            solusi: "Taman terapi tanaman herbal & jalur batu alam interaktif",
            hasil: "Meningkatkan ketenangan & ketrampilan kognitif siswa",
            tanggal: "2026-09-18"
        }
    ];

    let bankInovasiData = [{
            id: 101,
            judul: "SIM-PRESENCE: Presensi Geofencing & Notifikasi Ortu",
            sekolah: "SMKN 1 Kendari",
            penggagas: "Drs. H. Ahmad & Tim IT",
            kebaruan: "Integrasi GPS lokasi sekolah dengan Bot WhatsApp Orang Tua secara real-time",
            status: "approved"
        },
        {
            id: 102,
            judul: "Modul Vokasi Adaptif Pertanian Barcode QR",
            sekolah: "SMKN 4 Konawe Selatan",
            penggagas: "Siti Rahma, S.Pd",
            kebaruan: "Media tanam dilengkapi barcode menuju video tutorial Bahasa Daerah",
            status: "pending"
        }
    ];

    let kompetisiData = [{
            id: 201,
            judul: "EduBot Sultra: Asisten AI Belajar Matematika Daerah",
            sekolah: "SMAN 2 Kendari",
            kategori: "Kategori 1: Transformasi Digital",
            videoUrl: "https://www.youtube.com/watch?v=demo1",
            skorJuri: 83,
            statusScored: true
        },
        {
            id: 202,
            judul: "Dapur Gizi Sekolah Sehat Bebas Stunting",
            sekolah: "SMAN 3 Kolaka",
            kategori: "Kategori 3: Penguatan Karakter & Gizi",
            videoUrl: "https://www.youtube.com/watch?v=demo2",
            skorJuri: 0,
            statusScored: false
        }
    ];

    let apresiasiData = [{
            id: 301,
            inovasi: "SIM-PRESENCE Vokasi",
            sekolah: "SMKN 1 Kendari",
            pemberi: "Gubernur / Dinas Pendidikan Provinsi Sulawesi Tenggara",
            tahun: "2026",
            status: "Verified"
        },
        {
            id: 302,
            inovasi: "E-Rubrik Inklusi SLB",
            sekolah: "SLBN 1 Kendari",
            pemberi: "Lembaga Penjaminan Mutu Pendidikan",
            tahun: "2025",
            status: "Verified"
        }
    ];

    let suaraTickets = [{
            code: "SVR-202609-001",
            nama: "SMAN 1 Kolaka",
            kontak: "0811223344",
            kategori: "Pertanyaan",
            subjek: "Format unggah proposal kompetisi",
            pesan: "Apakah proposal kompetisi wajib menyertakan surat rekomendasi Cabdin?",
            status: "Selesai",
            respon: "Ya, surat rekomendasi Cabdin diunggah gabung pada halaman lampiran PDF proposal."
        },
        {
            code: "SVR-202609-002",
            nama: "Gurusiana Sultra",
            kontak: "guru@gmail.com",
            kategori: "Saran",
            subjek: "Penambahan kategori inovasi bahasa daerah",
            pesan: "Mohon dipertimbangkan penambahan kategori pelestarian bahasa daerah di kompetisi mendatang.",
            status: "Diproses",
            respon: ""
        }
    ];

    function navTo(tabId) {
        // Menu yang sudah punya halaman sendiri
        const halamanTerpisah = {
            'praktek-baik': '<?= site_url('praktik-baik') ?>',
            'kompetisi': '<?= site_url('kompetisi') ?>',
            'suara': '<?= site_url('suara') ?>',
            'apresiasi': '<?= site_url('apresiasi') ?>',
            'monev': '<?= site_url('monev') ?>',
        };
        if (halamanTerpisah[tabId]) {
            window.location.href = halamanTerpisah[tabId];
            return;
        }

        const target = document.getElementById(`sec-${tabId}`);

        if (!target) {
            // Sudah di dashboard tapi tab tidak ada: berhenti, jangan reload
            if (document.getElementById('sec-beranda')) return;

            // Di halaman lain: pindah ke dashboard lalu buka tab tersebut
            window.location.href = '<?= site_url('dashboard') ?>#' + tabId;
            return;
        }

        const tabs = ['beranda', 'bank-inovasi', 'kompetisi', 'apresiasi', 'monev', 'suara'];
        tabs.forEach(t => {
            const sec = document.getElementById(`sec-${t}`);
            const tab = document.getElementById(`tab-${t}`);
            if (sec) sec.classList.add('hidden');
            if (tab) tab.classList.remove('active-tab');
        });
        document.querySelectorAll('.active-tab').forEach(el => el.classList.remove('active-tab'));

        target.classList.remove('hidden');
        const tabAktif = document.getElementById(`tab-${tabId}`);
        if (tabAktif) tabAktif.classList.add('active-tab');

        if (tabId === 'monev') initMonevCharts();
    }

    // function switchRole(role) {
    //     currentRole = role;
    //     const badge = document.getElementById('roleBadge');
    //     const adminSuaraBox = document.getElementById('admin-suara-box');

    //     if (role === 'sekolah') {
    //         badge.innerText = "Sekolah";
    //         badge.className = "bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30";
    //         if (adminSuaraBox) adminSuaraBox.classList.add('hidden');
    //     } else if (role === 'admin') {
    //         badge.innerText = "Admin Dinas";
    //         badge.className = "bg-brand-500/20 text-brand-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-brand-500/30";
    //         if (adminSuaraBox) adminSuaraBox.classList.remove('hidden');
    //     } else if (role === 'juri') {
    //         badge.innerText = "Tim Juri";
    //         badge.className = "bg-amber-500/20 text-amber-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-amber-500/30";
    //         if (adminSuaraBox) adminSuaraBox.classList.add('hidden');
    //     } else {
    //         badge.innerText = "Guru";
    //         badge.className = "bg-purple-500/20 text-purple-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-purple-500/30";
    //         if (adminSuaraBox) adminSuaraBox.classList.add('hidden');
    //     }
    //     renderAllViews();
    //     showToast("Simulasi Akses Diubah", `Akses pengguna kini disimulasikan sebagai: ${role.toUpperCase()}`, 'info');
    // }
    function gantiAkun(select) {
        const namaRole = select.options[select.selectedIndex].text;
        select.value = select.dataset.current;
        document.getElementById('gantiAkunRole').innerText = namaRole;
        openModal('modalGantiAkun');
    }

    function aturTampilanRole() {
        const adminSuaraBox = document.getElementById('admin-suara-box');
        if (adminSuaraBox) {
            adminSuaraBox.classList.toggle('hidden', currentRole !== 'admin');
        }
    }

    function toggleUserMenu() {
        document.getElementById('userMenuDropdown').classList.toggle('hidden');
    }

    function toggleKelolaMenu() {
        document.getElementById('kelolaMenuDropdown').classList.toggle('hidden');
    }

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('kelolaMenu');
        if (menu && !menu.contains(e.target)) {
            document.getElementById('kelolaMenuDropdown').classList.add('hidden');
        }
    });

    document.addEventListener('click', function(e) {
        const menu = document.getElementById('userMenu');
        if (menu && !menu.contains(e.target)) {
            document.getElementById('userMenuDropdown').classList.add('hidden');
        }
    });

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

        if (homeList) homeList.innerHTML = '';
        if (grid) grid.innerHTML = '';

        praktekBaikData.forEach((item, index) => {
            if (homeList && index < 2) {
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

            if (grid) { // ← BARU
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
            } // ← BARU
        });
    }

    function renderBankInovasi() {
        const tbody = document.getElementById('bank-table-body');
        if (!tbody) return;
        tbody.innerHTML = '';

        bankInovasiData.forEach(item => {
            const statusBadge = item.status === 'approved' ?
                `<span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Approved (Bank Inovasi)</span>` :
                `<span class="bg-amber-100 text-amber-800 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-clock mr-1"></i>Pending Verval</span>`;

            const actionButton = (currentRole === 'admin' && item.status === 'pending') ?
                `<button onclick="approveInovasi(${item.id})" class="bg-emerald-600 text-white text-[10px] px-2.5 py-1 rounded-lg hover:bg-emerald-700 font-semibold shadow-sm">Setujui Verval</button>` :
                `<span class="text-slate-400 text-[11px]">Sistematika 16 Poin Lengkap</span>`;

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
        if (!grid) return;
        grid.innerHTML = '';

        kompetisiData.forEach(item => {
            const scoreDisplay = item.statusScored ?
                `<span class="text-xl font-extrabold text-amber-600">${item.skorJuri} / 100</span>` :
                `<span class="text-xs text-slate-400 italic">Belum Dinilai Juri</span>`;

            const juriButton = (currentRole === 'juri' || currentRole === 'admin') ?
                `<button onclick="openModalJuri(${item.id}, '${item.judul}')" class="bg-amber-500 hover:bg-amber-600 text-white font-semibold text-xs px-3 py-1.5 rounded-lg shadow">
                        <i class="fa-solid fa-gavel mr-1"></i>Input Nilai (7 Kriteria)
                       </button>` :
                '';

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
        if (!grid) return;
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
        if (!list) return;
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

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function openModalPraktekBaik() {
        openModal('modalPraktekBaik');
    }

    function openModalBankInovasi() {
        openModal('modalBankInovasi');
    }

    function openModalKompetisi() {
        openModal('modalKompetisi');
    }

    function openModalApresiasi() {
        showToast("Klaim Apresiasi", "Silakan unggah SK/Sertifikat Penghargaan untuk verifikasi Dinas.", "info");
    }

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
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
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
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    }

    // window.onload = function() {
    //     renderAllViews();
    // };
    window.onload = function() {
        aturTampilanRole();
        renderAllViews();
        // Hanya buka tab kalau section-nya memang ada di halaman ini
        const tabDariUrl = location.hash.substring(1);
        if (tabDariUrl && document.getElementById('sec-' + tabDariUrl)) {
            navTo(tabDariUrl);
        }
    };
</script>