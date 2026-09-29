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
                            <div class="text-lg font-bold text-slate-800"><?= count($k['peserta']) ?></div>
                            <div class="text-[10px] text-slate-500">Karya</div>
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
                            <i class="fa-solid fa-users mr-2"></i>Lihat Peserta
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL DAFTAR PESERTA
     ===================================================== -->
<div id="modalPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <h3 id="p_judul" class="font-bold text-slate-800 text-base"></h3>
                <p id="p_sub" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalPeserta')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div id="p_filter" class="flex flex-wrap gap-2 px-5 pt-4"></div>

        <div class="overflow-y-auto p-5">
            <div id="p_kosong" class="hidden text-center py-8 text-slate-400">
                <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>Belum ada peserta pada kompetisi ini.
            </div>
            <table id="p_tabel" class="w-full text-left text-slate-600">
                <thead class="text-[10px] uppercase text-slate-500 border-b">
                    <tr>
                        <th class="py-2 pr-3 w-16">#</th>
                        <th class="py-2 pr-3">Karya</th>
                        <th class="py-2 pr-3">Sekolah</th>
                        <th class="py-2 pr-3">Kategori</th>
                        <th id="p_kolomNilai" class="py-2 text-right">Nilai</th>
                    </tr>
                </thead>
                <tbody id="p_isi" class="divide-y divide-slate-100"></tbody>
            </table>
        </div>

        <div class="flex justify-between items-center border-t p-4 bg-slate-50 rounded-b-2xl">
            <span id="p_catatan" class="text-[11px] text-slate-400"></span>
            <button type="button" onclick="closeModal('modalPeserta')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<script>
    let pesertaAktif = [];
    let diumumkan = false;

    function lihatPeserta(k) {
        pesertaAktif = k.peserta || [];
        diumumkan = !!parseInt(k.hasil_diumumkan);

        document.getElementById('p_judul').textContent = k.nama_kompetisi;
        document.getElementById('p_sub').textContent = pesertaAktif.length + ' karya dari ' + k.jumlah_sekolah + ' sekolah';
        document.getElementById('p_kolomNilai').classList.toggle('hidden', !diumumkan);
        document.getElementById('p_catatan').textContent = diumumkan ?
            'Nilai adalah rata-rata dari seluruh juri.' :
            'Peringkat dan nilai akan tampil setelah hasil diumumkan Dinas.';

        // Filter kategori
        const filter = document.getElementById('p_filter');
        filter.innerHTML = '';
        ['Semua kategori', ...(k.kategori || [])].forEach((nama, i) => {
            const tombol = document.createElement('button');
            tombol.type = 'button';
            tombol.textContent = nama;
            tombol.dataset.kategori = i === 0 ? '' : nama;
            tombol.className = 'tombol-kategori px-3 py-1 rounded-full border text-[11px] font-semibold';
            tombol.onclick = () => tampilPeserta(tombol.dataset.kategori);
            filter.appendChild(tombol);
        });

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

        const isi = document.getElementById('p_isi');
        isi.innerHTML = '';
        document.getElementById('p_kosong').classList.toggle('hidden', daftar.length > 0);
        document.getElementById('p_tabel').classList.toggle('hidden', daftar.length === 0);

        const warnaJuara = {
            1: 'bg-amber-100 text-amber-700',
            2: 'bg-slate-200 text-slate-700',
            3: 'bg-orange-100 text-orange-700'
        };

        daftar.forEach((p, i) => {
            const tr = document.createElement('tr');
            tr.className = p.milik_sendiri ? 'bg-violet-50/60' : '';

            const sel = (teks, kelas = '') => {
                const td = document.createElement('td');
                td.className = 'py-2.5 pr-3 ' + kelas;
                if (teks instanceof Node) td.appendChild(teks);
                else td.textContent = teks || '-';
                return td;
            };

            // Kolom posisi: medali kalau juara, nomor urut kalau bukan
            let posisi = String(i + 1);
            if (diumumkan && p.peringkat) {
                posisi = document.createElement('span');
                posisi.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap ' + (warnaJuara[p.peringkat] || '');
                posisi.textContent = 'Juara ' + p.peringkat;
            }

            // Kolom karya + penanda sekolah sendiri
            const karya = document.createElement('div');
            const judul = document.createElement('div');
            judul.className = 'font-semibold text-slate-800';
            judul.textContent = p.judul_karya;
            karya.appendChild(judul);
            if (p.milik_sendiri) {
                const tanda = document.createElement('span');
                tanda.className = 'text-[10px] font-bold text-violet-700';
                tanda.textContent = 'Karya sekolah Anda';
                karya.appendChild(tanda);
            }

            tr.append(
                sel(posisi, 'text-slate-400'),
                sel(karya),
                sel(p.nama_sekolah),
                sel(p.nama_kategori, 'text-slate-500')
            );
            if (diumumkan) {
                tr.appendChild(sel(p.nilai ? parseFloat(p.nilai).toFixed(2).replace('.', ',') : '-', 'text-right font-bold text-slate-800'));
            }
            isi.appendChild(tr);
        });
    }
</script>
<?= $this->endSection() ?>