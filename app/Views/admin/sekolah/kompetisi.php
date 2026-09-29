<?php

/** @var array $kompetisi */
/** @var array $label */
/** @var string $tab */
/** @var array $jumlahTab */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$urutan = array_keys($label);
$badge  = [
    'pendaftaran' => 'bg-blue-100 text-blue-700',
    'berlangsung' => 'bg-amber-100 text-amber-800',
    'selesai'     => 'bg-emerald-100 text-emerald-800',
];

$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';

// Penjelasan singkat untuk setiap tahap
$keterangan = function ($k) use ($tgl) {
    if ($k['hasil_diumumkan']) {
        return ['fa-trophy', 'text-emerald-700 bg-emerald-50 border-emerald-200', 'Hasil sudah diumumkan pada ' . $tgl($k['tanggal_pengumuman']) . '. Lihat para juara di menu Apresiasi.'];
    }

    return match ($k['status']) {
        'pendaftaran' => ['fa-door-open', 'text-blue-700 bg-blue-50 border-blue-200', 'Pendaftaran sedang dibuka hingga ' . $tgl($k['tanggal_selesai']) . '. Guru dapat mendaftarkan karya inovasinya.'],
        'berlangsung' => ['fa-gavel', 'text-amber-800 bg-amber-50 border-amber-200', 'Pendaftaran ditutup. Tim juri sedang menilai karya peserta.'],
        'selesai'     => ['fa-hourglass-half', 'text-slate-700 bg-slate-50 border-slate-200', 'Penjurian selesai. Dinas sedang menyiapkan pengumuman hasil.'],
        default       => ['fa-circle-info', 'text-slate-700 bg-slate-50 border-slate-200', ''],
    };
};
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Kompetisi Inovasi</h2>
        <p class="text-xs text-slate-500">Jadwal dan perkembangan kompetisi inovasi pendidikan yang diselenggarakan Dinas.</p>
    </div>

    <!-- Tab -->
    <div class="flex flex-wrap gap-2 text-xs font-semibold">
        <?php foreach (['berjalan' => ['Sedang Berlangsung', 'fa-bolt'], 'riwayat' => ['Riwayat', 'fa-clock-rotate-left']] as $kunci => [$teks, $ikonTab]) : ?>
            <a href="<?= site_url('kompetisi') . ($kunci === 'riwayat' ? '?tab=riwayat' : '') ?>"
                class="px-4 py-2 rounded-xl border flex items-center gap-2 transition
                       <?= $tab === $kunci ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="fa-solid <?= $ikonTab ?>"></i><?= $teks ?>
                <span class="<?= $tab === $kunci ? 'bg-white/25' : 'bg-slate-100' ?> text-[10px] px-1.5 rounded-full"><?= $jumlahTab[$kunci] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($kompetisi)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-2xl text-amber-500"></i>
                </div>
            </div>
            <?php if ($tab === 'berjalan') : ?>
                <p class="text-sm font-semibold text-slate-700">Belum ada kompetisi yang sedang berlangsung</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Kompetisi yang dibuka Dinas akan tampil di sini. Kompetisi sebelumnya dapat dilihat di tab Riwayat.</p>
            <?php else : ?>
                <p class="text-sm font-semibold text-slate-700">Belum ada riwayat kompetisi</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Kompetisi yang sudah selesai akan tersimpan di sini.</p>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($kompetisi as $k) : ?>
                <?php
                $pos       = array_search($k['status'], $urutan, true);
                [$ikonKet, $warnaKet, $teksKet] = $keterangan($k);
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-4">

                    <div>
                        <div class="flex flex-wrap gap-2">
                            <span class="<?= $badge[$k['status']] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2.5 py-1 rounded-full"><?= esc($label[$k['status']] ?? $k['status']) ?></span>
                            <?php if ($k['karya_sendiri'] > 0) : ?>
                                <span class="bg-violet-100 text-violet-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-school mr-1"></i>Sekolah Anda ikut</span>
                            <?php endif; ?>
                        </div>
                        <h3 class="font-bold text-slate-800 text-base mt-2 leading-snug"><?= esc($k['nama_kompetisi']) ?></h3>
                        <p class="text-[11px] text-slate-500 mt-0.5"><i class="fa-regular fa-calendar mr-1"></i><?= $tgl($k['tanggal_mulai']) ?> – <?= $tgl($k['tanggal_selesai']) ?></p>
                    </div>

                    <?php if ($k['deskripsi']) : ?>
                        <p class="text-xs text-slate-600 line-clamp-2"><?= esc($k['deskripsi']) ?></p>
                    <?php endif; ?>

                    <!-- Penanda tahapan -->
                    <div class="flex items-center">
                        <?php foreach ($urutan as $i => $tahap) : ?>
                            <?php $lewat = $i <= $pos; ?>
                            <div class="flex flex-col items-center gap-1 w-16 shrink-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold <?= $lewat ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400' ?>">
                                    <?= $i < $pos ? '<i class="fa-solid fa-check"></i>' : $i + 1 ?>
                                </div>
                                <span class="text-[9px] text-center leading-tight <?= $i === $pos ? 'font-bold text-brand-700' : 'text-slate-400' ?>"><?= esc($label[$tahap]) ?></span>
                            </div>
                            <?php if ($i < count($urutan) - 1) : ?>
                                <div class="flex-1 h-0.5 -mt-4 <?= $i < $pos ? 'bg-brand-600' : 'bg-slate-200' ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Keterangan tahap saat ini -->
                    <?php if ($teksKet) : ?>
                        <div class="border rounded-xl px-3.5 py-2.5 text-xs flex items-start gap-2 <?= $warnaKet ?>">
                            <i class="fa-solid <?= $ikonKet ?> mt-0.5"></i><span><?= esc($teksKet) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- Ringkasan -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-slate-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-slate-800"><?= $k['total_karya'] ?></div>
                            <div class="text-[10px] text-slate-500">Total Karya</div>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-slate-800"><?= $k['jumlah_sekolah'] ?></div>
                            <div class="text-[10px] text-slate-500">Sekolah</div>
                        </div>
                        <div class="bg-violet-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-violet-700"><?= $k['karya_sendiri'] ?></div>
                            <div class="text-[10px] text-violet-600">Karya Sekolah Anda</div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-3 border-t mt-auto">
                        <button type="button" data-kompetisi="<?= esc(json_encode($k), 'attr') ?>"
                            onclick="lihatPeserta(JSON.parse(this.dataset.kompetisi))"
                            class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2 rounded-lg flex items-center">
                            <i class="fa-solid fa-users mr-2"></i>Peserta Sekolah Anda
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL PESERTA SEKOLAH
     ===================================================== -->
<div id="modalPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">

        <!-- Header dengan piala -->
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div class="flex items-center gap-4">
                <div id="p_piala" class="w-14 h-14 rounded-2xl flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-trophy text-2xl"></i>
                </div>
                <div>
                    <div id="p_prestasi" class="text-[11px] font-bold uppercase tracking-wider"></div>
                    <h3 id="p_judul" class="font-bold text-slate-800 text-base leading-snug"></h3>
                    <p id="p_sub" class="text-slate-400 mt-0.5"></p>
                    <span id="p_kategori" class="hidden mt-1.5 inline-block bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                </div>
            </div>
            <button type="button" onclick="closeModal('modalPeserta')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div id="p_filter" class="flex flex-wrap gap-2 px-5 pt-4"></div>

        <div class="overflow-y-auto p-5">
            <div id="p_kosong" class="hidden text-center py-8 text-slate-400">
                <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>Sekolah Anda belum mengikutkan karya pada kompetisi ini.
            </div>
            <div id="p_kartu" class="space-y-4"></div>
        </div>

        <div class="flex justify-between items-center border-t p-4 bg-slate-50 rounded-b-2xl">
            <span id="p_catatan" class="text-[11px] text-slate-400"></span>
            <button type="button" onclick="closeModal('modalPeserta')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<script>
    // Warna per peringkat: [latar piala, warna ikon, pita kartu, teks]
    const WARNA_JUARA = {
        1: {
            piala: 'bg-gradient-to-br from-amber-300 to-yellow-500 text-white shadow-lg shadow-amber-200',
            pita: 'bg-gradient-to-r from-amber-400 to-yellow-400 text-amber-950',
            teks: 'text-amber-600'
        },
        2: {
            piala: 'bg-gradient-to-br from-slate-300 to-slate-500 text-white shadow-lg shadow-slate-200',
            pita: 'bg-gradient-to-r from-slate-300 to-slate-400 text-slate-900',
            teks: 'text-slate-500'
        },
        3: {
            piala: 'bg-gradient-to-br from-orange-300 to-orange-600 text-white shadow-lg shadow-orange-200',
            pita: 'bg-gradient-to-r from-orange-300 to-orange-400 text-orange-950',
            teks: 'text-orange-600'
        },
    };
    const WARNA_BIASA = {
        piala: 'bg-slate-100 text-slate-400',
        pita: 'bg-slate-100 text-slate-600',
        teks: 'text-slate-400'
    };

    let pesertaAktif = [];
    let diumumkan = false;
    let kategoriTunggal = false; // true kalau semua karya sekolah ada di satu kategori

    function lihatPeserta(k) {
        pesertaAktif = k.peserta || [];
        diumumkan = !!parseInt(k.hasil_diumumkan);

        // Prestasi terbaik sekolah menentukan warna piala di header
        const terbaik = diumumkan ?
            Math.min(...pesertaAktif.map(p => parseInt(p.peringkat) || 99), 99) :
            99;
        const warna = WARNA_JUARA[terbaik] || WARNA_BIASA;

        document.getElementById('p_piala').className = 'w-14 h-14 rounded-2xl flex items-center justify-center shrink-0 ' + warna.piala;
        const prestasi = document.getElementById('p_prestasi');
        prestasi.className = 'text-[11px] font-bold uppercase tracking-wider ' + warna.teks;
        prestasi.textContent = WARNA_JUARA[terbaik] ?
            'Sekolah Anda meraih Juara ' + terbaik :
            (diumumkan ? 'Hasil sudah diumumkan' : 'Menunggu pengumuman');

        document.getElementById('p_judul').textContent = k.nama_kompetisi;
        document.getElementById('p_sub').textContent = pesertaAktif.length ?
            pesertaAktif.length + ' karya dari sekolah Anda • total ' + k.total_karya + ' karya dari ' + k.jumlah_sekolah + ' sekolah' :
            'Total ' + k.total_karya + ' karya dari ' + k.jumlah_sekolah + ' sekolah';
        document.getElementById('p_catatan').textContent = diumumkan ?
            'Total nilai adalah rata-rata dari seluruh juri.' :
            'Peringkat dan nilai akan tampil setelah hasil diumumkan Dinas.';

        // Filter kategori hanya kalau karya sekolah ada di lebih dari satu kategori
        const kategoriSekolah = [...new Set(pesertaAktif.map(p => p.nama_kategori).filter(Boolean))];

        // Kalau kategorinya sama semua, cukup tampil sekali di header
        kategoriTunggal = kategoriSekolah.length === 1;
        const chip = document.getElementById('p_kategori');
        chip.classList.toggle('hidden', !kategoriTunggal);
        chip.innerHTML = '';
        if (kategoriTunggal) {
            const ikonTag = document.createElement('i');
            ikonTag.className = 'fa-solid fa-tag mr-1';
            chip.append(ikonTag, document.createTextNode(kategoriSekolah[0]));
        }
        const filter = document.getElementById('p_filter');
        filter.innerHTML = '';
        filter.classList.toggle('hidden', kategoriSekolah.length <= 1);
        if (kategoriSekolah.length > 1) {
            ['Semua kategori', ...kategoriSekolah].forEach((nama, i) => {
                const tombol = document.createElement('button');
                tombol.type = 'button';
                tombol.textContent = nama;
                tombol.dataset.kategori = i === 0 ? '' : nama;
                tombol.onclick = () => tampilPeserta(tombol.dataset.kategori);
                tombol.className = 'tombol-kategori';
                filter.appendChild(tombol);
            });
        }

        tampilPeserta('');
        openModal('modalPeserta');
    }

    function tampilPeserta(kategori) {
        document.querySelectorAll('.tombol-kategori').forEach(t => {
            const aktif = t.dataset.kategori === kategori;
            t.className = 'tombol-kategori px-3 py-1 rounded-full border text-[11px] font-semibold ' +
                (aktif ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50');
        });

        let daftar = pesertaAktif.filter(p => !kategori || p.nama_kategori === kategori);
        if (diumumkan) {
            daftar = [...daftar].sort((a, b) => (a.peringkat || 99) - (b.peringkat || 99) || (b.nilai || 0) - (a.nilai || 0));
        }

        const wadah = document.getElementById('p_kartu');
        wadah.innerHTML = '';
        document.getElementById('p_kosong').classList.toggle('hidden', daftar.length > 0);

        const buat = (tag, kelas, teks) => {
            const el = document.createElement(tag);
            if (kelas) el.className = kelas;
            if (teks !== undefined) el.textContent = teks;
            return el;
        };
        const formatNilai = n => parseFloat(n).toFixed(2).replace('.', ',');

        const juara = daftar.filter(p => diumumkan && WARNA_JUARA[p.peringkat]);
        const lainnya = daftar.filter(p => !(diumumkan && WARNA_JUARA[p.peringkat]));

        // ---------- Kartu besar untuk juara ----------
        juara.forEach(p => {
            const warna = WARNA_JUARA[p.peringkat];
            const kartu = buat('div', 'border border-slate-200 rounded-2xl overflow-hidden shadow-sm');

            const pita = buat('div', 'px-4 py-2.5 flex flex-wrap justify-between items-center gap-2 ' + warna.pita);
            const status = buat('div', 'font-bold flex items-center gap-2');
            status.append(buat('i', 'fa-solid fa-medal'), document.createTextNode('Juara ' + p.peringkat));
            pita.appendChild(status);
            if (!kategoriTunggal) pita.appendChild(buat('span', 'text-[11px] font-semibold opacity-80', p.nama_kategori || ''));

            const isi = buat('div', 'grid grid-cols-1 md:grid-cols-2 gap-4 p-4');
            [
                ['fa-user', 'bg-brand-50 text-brand-600', 'Peserta', p.nama_guru, p.mapel],
                ['fa-lightbulb', 'bg-indigo-50 text-indigo-600', 'Karya', p.judul_karya, '']
            ].forEach(([ikon, warnaIkon, label, utama, kecil]) => {
                const kolom = buat('div', 'flex gap-3');
                const bulat = buat('div', 'w-9 h-9 rounded-full flex items-center justify-center shrink-0 ' + warnaIkon);
                bulat.appendChild(buat('i', 'fa-solid ' + ikon));
                const info = buat('div', 'min-w-0');
                info.append(
                    buat('div', 'text-[10px] text-slate-400 uppercase font-bold tracking-wider', label),
                    buat('div', 'font-bold text-slate-800 text-sm leading-snug', utama || '-')
                );
                if (kecil) info.appendChild(buat('div', 'text-[11px] text-slate-400', kecil));
                kolom.append(bulat, info);
                isi.appendChild(kolom);
            });

            const bawah = buat('div', 'border-t bg-slate-50 px-4 py-3 flex justify-between items-center');
            bawah.appendChild(buat('span', 'text-slate-500 font-semibold', 'Total Nilai'));
            const nilai = buat('div', 'text-right');
            nilai.append(
                buat('span', 'text-2xl font-extrabold ' + warna.teks, formatNilai(p.nilai)),
                buat('span', 'text-slate-400 font-semibold ml-1', '/ 100')
            );
            bawah.appendChild(nilai);

            kartu.append(pita, isi, bawah);
            wadah.appendChild(kartu);
        });

        // ---------- Baris ringkas untuk peserta lainnya ----------
        if (lainnya.length) {
            if (juara.length) {
                wadah.appendChild(buat('div', 'text-[11px] font-bold text-slate-500 uppercase tracking-wider pt-2',
                    'Peserta lainnya (' + lainnya.length + ')'));
            }

            const daftarRingkas = buat('div', 'border border-slate-200 rounded-2xl divide-y divide-slate-100 overflow-hidden');
            lainnya.forEach((p, i) => {
                const baris = buat('div', 'flex items-center gap-3 px-4 py-3 hover:bg-slate-50');

                baris.appendChild(buat('span', 'w-6 h-6 rounded-full bg-slate-100 text-slate-500 text-[10px] font-bold flex items-center justify-center shrink-0',
                    String(juara.length + i + 1)));

                const info = buat('div', 'flex-1 min-w-0');
                info.appendChild(buat('div', 'font-semibold text-slate-800 truncate', p.judul_karya || '-'));
                const sub = buat('div', 'text-[11px] text-slate-400 truncate');
                sub.textContent = (p.nama_guru || '-') + (p.mapel ? ' • ' + p.mapel : '') + (!kategoriTunggal && p.nama_kategori ? ' • ' + p.nama_kategori : '');
                info.appendChild(sub);
                baris.appendChild(info);

                if (diumumkan && p.nilai) {
                    const nilai = buat('div', 'text-right shrink-0');
                    nilai.append(
                        buat('span', 'text-base font-bold text-slate-800', formatNilai(p.nilai)),
                        buat('span', 'text-[10px] text-slate-400 ml-0.5', '/100')
                    );
                    baris.appendChild(nilai);
                } else {
                    baris.appendChild(buat('span', 'text-[11px] text-slate-400 italic shrink-0', diumumkan ? '-' : 'Menunggu'));
                }

                daftarRingkas.appendChild(baris);
            });
            wadah.appendChild(daftarRingkas);
        }
    }
</script>
<?= $this->endSection() ?>