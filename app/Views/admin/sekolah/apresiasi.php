<?php

/** @var array $prestasi */
/** @var array $riwayat */
/** @var array $kompetisi */
/** @var array $daftarTahun */
/** @var array $filter */
/** @var array $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$medali = [
    1 => 'bg-amber-100 text-amber-700 border-amber-300',
    2 => 'bg-slate-200 text-slate-700 border-slate-300',
    3 => 'bg-orange-100 text-orange-700 border-orange-300',
];
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$angka = fn($n) => $n === null ? '–' : number_format((float) $n, 2, ',', '.');
$menunggu = $ringkas['juara'] - $ringkas['tersedia'];
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Apresiasi & Piagam</h2>
        <p class="text-xs text-slate-500">Prestasi sekolah Anda dari kompetisi inovasi, status piagam, dan riwayat keikutsertaan.</p>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-violet-50 text-violet-600"><i class="fa-solid fa-paper-plane text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Karya Diikutkan</div>
                <div class="text-xl font-bold text-slate-800"><?= $ringkas['ikut'] ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-amber-50 text-amber-600"><i class="fa-solid fa-award text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Juara Sekolah Anda</div>
                <div class="text-xl font-bold text-slate-800"><?= $ringkas['juara'] ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-emerald-50 text-emerald-600"><i class="fa-solid fa-file-circle-check text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Piagam Tersedia</div>
                <div class="text-xl font-bold text-emerald-600"><?= $ringkas['tersedia'] ?></div>
            </div>
        </div>
        <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
            <div class="p-3 rounded-xl bg-blue-50 text-blue-600"><i class="fa-solid fa-hourglass-half text-xl"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Sedang Disiapkan</div>
                <div class="text-xl font-bold <?= $menunggu > 0 ? 'text-blue-600' : 'text-slate-400' ?>"><?= $menunggu ?></div>
            </div>
        </div>
    </div>

    <!-- Filter (berlaku untuk seluruh halaman) -->
    <?php if (! empty($daftarTahun)) : ?>
        <form method="get" action="<?= site_url('apresiasi') ?>" class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm flex flex-wrap items-center gap-3 text-xs">
            <span class="font-semibold text-slate-600"><i class="fa-solid fa-filter text-brand-600 mr-1"></i>Tampilkan:</span>
            <select name="tahun" onchange="this.form.kompetisi.value=''; this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                <?php foreach ($daftarTahun as $th) : ?>
                    <option value="<?= $th ?>" <?= $filter['tahun'] === $th ? 'selected' : '' ?>>Tahun <?= $th ?><?= $th === $daftarTahun[0] ? ' (terbaru)' : '' ?></option>
                <?php endforeach; ?>
                <option value="semua" <?= $filter['tahun'] === 'semua' ? 'selected' : '' ?>>Semua tahun</option>
            </select>
            <select name="kompetisi" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2 min-w-[220px]">
                <option value="">Semua kompetisi</option>
                <?php foreach ($kompetisi as $id => $nama) : ?>
                    <option value="<?= $id ?>" <?= $filter['kompetisi'] === (string) $id ? 'selected' : '' ?>><?= esc($nama) ?></option>
                <?php endforeach; ?>
            </select>
            <a href="<?= site_url('apresiasi') ?>" title="Kembali ke tampilan awal" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-3 py-2 rounded-lg">
                <i class="fa-solid fa-rotate-left"></i>
            </a>
        </form>
    <?php endif; ?>

    <!-- Prestasi sekolah sendiri -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-medal text-amber-500 mr-2"></i>Prestasi Sekolah Anda</h3>

        <?php if (empty($prestasi)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-8 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-award text-2xl text-amber-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum ada juara<?= $filter['tahun'] !== 'semua' ? ' pada tahun ' . esc($filter['tahun']) : '' ?></p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Dorong guru untuk mengikuti kompetisi inovasi yang sedang dibuka.</p>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <?php foreach ($prestasi as $p) : ?>
                    <?php $ada = ! empty($p['bukti_file']); ?>
                    <div class="border <?= $ada ? 'border-emerald-200' : 'border-slate-200' ?> rounded-2xl p-4 space-y-3 text-xs">
                        <div class="flex justify-between items-start gap-3">
                            <div class="min-w-0">
                                <span class="<?= $medali[$p['peringkat']] ?? 'bg-slate-100 text-slate-600' ?> border text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                    <i class="fa-solid fa-medal mr-1"></i>Juara <?= $p['peringkat'] ?>
                                </span>
                                <h4 class="font-bold text-slate-800 text-sm mt-2 leading-snug"><?= esc($p['judul_karya']) ?></h4>
                                <p class="text-[11px] text-slate-500"><?= esc($p['nama_guru'] ?? '-') ?> • <?= esc($p['nama_kategori'] ?? '-') ?></p>
                                <p class="text-[11px] text-slate-400"><?= esc($p['nama_kompetisi']) ?> • Nilai <?= $angka($p['nilai']) ?></p>
                            </div>
                            <i class="fa-solid fa-award text-4xl <?= $ada ? 'text-emerald-200' : 'text-slate-100' ?> shrink-0"></i>
                        </div>

                        <!-- Progres piagam -->
                        <div class="flex items-center">
                            <?php
                            $langkah = [
                                ['Juara diumumkan', true],
                                ['Piagam disiapkan', true],
                                ['Piagam tersedia', $ada],
                            ];
                            ?>
                            <?php foreach ($langkah as $i => [$teks, $selesai]) : ?>
                                <div class="flex flex-col items-center gap-1 w-20 shrink-0">
                                    <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px]
                                                <?= $selesai ? 'bg-emerald-500 text-white' : 'bg-slate-100 text-slate-400' ?>">
                                        <i class="fa-solid <?= $selesai ? 'fa-check' : 'fa-hourglass-half' ?>"></i>
                                    </div>
                                    <span class="text-[9px] text-center leading-tight <?= $selesai ? 'text-emerald-700 font-semibold' : 'text-slate-400' ?>"><?= $teks ?></span>
                                </div>
                                <?php if ($i < count($langkah) - 1) : ?>
                                    <div class="flex-1 h-0.5 -mt-4 <?= $langkah[$i + 1][1] ? 'bg-emerald-500' : 'bg-slate-200' ?>"></div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>

                        <!-- Status & aksi -->
                        <div class="flex flex-wrap justify-between items-center gap-2 pt-3 border-t">
                            <?php if ($ada) : ?>
                                <span class="text-emerald-700 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i>Piagam tersedia sejak <?= $tgl($p['tanggal_piagam']) ?></span>
                                <a href="<?= base_url($p['bukti_file']) ?>" target="_blank"
                                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg flex items-center">
                                    <i class="fa-solid fa-download mr-1.5"></i>Unduh Piagam
                                </a>
                            <?php else : ?>
                                <span class="text-blue-700 font-semibold"><i class="fa-solid fa-hourglass-half mr-1"></i>Piagam sedang disiapkan Dinas</span>
                                <span class="text-[11px] text-slate-400">Diumumkan <?= $tgl($p['tanggal_pengumuman']) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Riwayat keikutsertaan sekolah -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-list-check text-brand-600 mr-2"></i>Riwayat Keikutsertaan Sekolah</h3>
                <p class="text-[11px] text-slate-400">Semua karya sekolah Anda yang pernah diikutkan dalam kompetisi.</p>
            </div>

        </div>

        <?php if (empty($riwayat)) : ?>
            <div class="text-center py-8 text-xs text-slate-400">
                <i class="fa-regular fa-folder-open text-3xl mb-2 block"></i>
                <?= empty($daftarTahun) ? 'Sekolah Anda belum pernah mengikuti kompetisi.' : 'Tidak ada data untuk filter yang dipilih.' ?>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3 rounded-l-lg">Kompetisi</th>
                            <th class="p-3">Karya & Guru</th>
                            <th class="p-3">Kategori</th>
                            <th class="p-3">Hasil</th>
                            <th class="p-3 text-right">Nilai</th>
                            <th class="p-3 text-center">Piagam</th>
                            <th class="p-3 text-center rounded-r-lg">Detail</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($riwayat as $r) : ?>
                            <tr class="baris-riwayat hover:bg-slate-50">
                                <td class="p-3">
                                    <div class="font-semibold text-slate-700"><?= esc($r['nama_kompetisi']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= date('Y', strtotime($r['tanggal_mulai'])) ?></div>
                                </td>
                                <td class="p-3 max-w-[260px]">
                                    <div class="font-semibold text-slate-800 truncate"><?= esc($r['judul_karya']) ?></div>
                                    <div class="text-[11px] text-slate-400 truncate"><?= esc($r['nama_guru'] ?? '-') ?></div>
                                </td>
                                <td class="p-3 text-slate-500"><?= esc($r['nama_kategori'] ?? '-') ?></td>
                                <td class="p-3">
                                    <?php if ($r['status_validasi'] === 'ditolak') : ?>
                                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">Tidak lolos validasi</span>
                                    <?php elseif (! $r['hasil_diumumkan']) : ?>
                                        <span class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                            <i class="fa-regular fa-clock mr-1"></i><?= $r['status_kompetisi'] === 'pendaftaran' ? 'Masa pendaftaran' : 'Menunggu pengumuman' ?>
                                        </span>
                                    <?php elseif ($r['peringkat']) : ?>
                                        <span class="<?= $medali[$r['peringkat']] ?? '' ?> border text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                            <i class="fa-solid fa-medal mr-1"></i>Juara <?= $r['peringkat'] ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="bg-slate-100 text-slate-500 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">Tidak juara</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-right font-bold text-slate-800"><?= $angka($r['nilai']) ?></td>
                                <td class="p-3 text-center">
                                    <?php if (! empty($r['bukti_file'])) : ?>
                                        <a href="<?= base_url($r['bukti_file']) ?>" target="_blank"
                                            class="inline-flex items-center border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold px-2.5 py-1 rounded-lg whitespace-nowrap">
                                            <i class="fa-solid fa-download mr-1"></i>Unduh
                                        </a>
                                    <?php elseif ($r['id_apresiasi']) : ?>
                                        <span class="text-[11px] text-blue-600 whitespace-nowrap"><i class="fa-solid fa-hourglass-half mr-1"></i>Disiapkan</span>
                                    <?php else : ?>
                                        <span class="text-slate-300">–</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3 text-center">
                                    <button type="button" title="Lihat detail karya"
                                        data-karya="<?= esc(json_encode($r), 'attr') ?>"
                                        onclick="lihatKarya(JSON.parse(this.dataset.karya))"
                                        class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 inline-flex items-center justify-center">
                                        <i class="fa-solid fa-eye text-[11px]"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

</section>

<!-- =====================================================
     MODAL DETAIL KARYA
     ===================================================== -->
<div id="modalKarya" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <div class="flex flex-wrap gap-2">
                    <span id="k_hasil" class="text-[10px] font-bold px-2.5 py-1 rounded-full border"></span>
                    <span id="k_kategori" class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                </div>
                <h3 id="k_judul" class="font-bold text-slate-800 text-base mt-2 leading-snug"></h3>
                <p id="k_kompetisi" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalKarya')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-5">
            <!-- Karya -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-lightbulb text-indigo-500 mr-1"></i> Karya yang Dilombakan</h4>
                <div id="k_deskripsi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"></div>
                <a id="k_video" target="_blank" rel="noopener"
                    class="hidden mt-2 inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded-lg">
                    <i class="fa-brands fa-youtube mr-1.5"></i>Tonton Video Karya
                </a>
            </div>

            <!-- Peserta -->
            <div class="border rounded-xl p-4 space-y-1.5">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-user text-brand-600 mr-1"></i> Peserta</h4>
                <div><span class="text-slate-400 w-20 inline-block">Nama</span> <b id="k_guru" class="text-slate-800"></b></div>
                <div><span class="text-slate-400 w-20 inline-block">NIP</span> <span id="k_nip"></span></div>
                <div><span class="text-slate-400 w-20 inline-block">Mapel</span> <span id="k_mapel"></span></div>
            </div>

            <!-- Rincian nilai -->
            <div id="k_nilaiWrap">
                <div class="flex justify-between items-center mb-2">
                    <h4 class="font-bold text-slate-700 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-chart-simple text-amber-500 mr-1"></i> Rincian Nilai Juri</h4>
                    <span class="text-slate-500">Nilai akhir: <b id="k_nilai" class="text-slate-800 text-sm"></b></span>
                </div>
                <div id="k_rincian" class="space-y-2"></div>
                <p class="text-[10px] text-slate-400 mt-2">Skor setiap kriteria adalah rata-rata dari seluruh juri.</p>
            </div>
            <div id="k_belumNilai" class="bg-blue-50 border border-blue-200 text-blue-800 rounded-xl p-3.5">
                <i class="fa-regular fa-clock mr-1"></i> Rincian nilai akan tampil setelah hasil kompetisi diumumkan Dinas.
            </div>
        </div>

        <div class="flex justify-end border-t p-4 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('modalKarya')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<script>
    function lihatKarya(d) {
        const isi = (id, teks) => document.getElementById(id).textContent = teks || '-';
        const angka = n => parseFloat(n).toFixed(2).replace('.', ',');
        const diumumkan = !!parseInt(d.hasil_diumumkan);

        // Badge hasil
        const hasil = document.getElementById('k_hasil');
        let teksHasil = 'Menunggu pengumuman',
            gaya = 'bg-blue-50 text-blue-700 border-blue-200';
        if (d.status_validasi === 'ditolak') {
            teksHasil = 'Tidak lolos validasi';
            gaya = 'bg-red-100 text-red-700 border-red-200';
        } else if (diumumkan && d.peringkat) {
            teksHasil = 'Juara ' + d.peringkat;
            gaya = 'bg-amber-100 text-amber-700 border-amber-300';
        } else if (diumumkan) {
            teksHasil = 'Tidak juara';
            gaya = 'bg-slate-100 text-slate-600 border-slate-200';
        }
        hasil.textContent = teksHasil;
        hasil.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full border ' + gaya;

        isi('k_kategori', d.nama_kategori);
        isi('k_judul', d.judul_karya);
        isi('k_kompetisi', d.nama_kompetisi);
        isi('k_deskripsi', d.deskripsi_karya || 'Belum ada deskripsi karya.');
        isi('k_guru', d.nama_guru);
        isi('k_nip', d.nip);
        isi('k_mapel', d.mapel);

        // Hanya tautan http/https yang boleh dibuka
        const video = document.getElementById('k_video');
        const linkAman = /^https?:\/\//i.test(d.link_video || '');
        video.classList.toggle('hidden', !linkAman);
        if (linkAman) video.href = d.link_video;

        // Rincian nilai per kriteria
        const adaNilai = diumumkan && d.rincian && d.rincian.length > 0;
        document.getElementById('k_nilaiWrap').classList.toggle('hidden', !adaNilai);
        document.getElementById('k_belumNilai').classList.toggle('hidden', diumumkan || d.status_validasi === 'ditolak');

        const wadah = document.getElementById('k_rincian');
        wadah.innerHTML = '';
        if (adaNilai) {
            isi('k_nilai', angka(d.nilai));
            d.rincian.forEach(r => {
                const persen = Math.round(r.skor / r.skor_maks * 100);
                const baris = document.createElement('div');

                const atas = document.createElement('div');
                atas.className = 'flex justify-between mb-1';
                const nama = document.createElement('span');
                nama.className = 'text-slate-700';
                nama.textContent = r.kriteria;
                const skor = document.createElement('span');
                skor.className = 'font-semibold text-slate-800';
                skor.textContent = angka(r.skor) + ' / ' + r.skor_maks;
                atas.append(nama, skor);

                const bar = document.createElement('div');
                bar.className = 'h-2 bg-slate-100 rounded-full overflow-hidden';
                const isiBar = document.createElement('div');
                isiBar.className = 'h-full rounded-full ' + (persen >= 80 ? 'bg-emerald-500' : persen >= 60 ? 'bg-amber-500' : 'bg-red-400');
                isiBar.style.width = persen + '%';
                bar.appendChild(isiBar);

                baris.append(atas, bar);
                wadah.appendChild(baris);
            });
        }

        openModal('modalKarya');
    }
</script>
<?= $this->endSection() ?>