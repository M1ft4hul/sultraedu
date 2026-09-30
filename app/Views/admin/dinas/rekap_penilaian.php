<?php

/** @var array $kompetisi */
/** @var array|null $aktif */
/** @var string|null $metode */
/** @var array|null $kriteria */
/** @var array|null $kategori */
/** @var array|null $namaJuri */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$angka  = fn($n) => $n === null ? '–' : number_format((float) $n, 2, ',', '.');
$medali = [
    1 => ['bg-amber-100 text-amber-800 border-amber-300', 'text-amber-500'],
    2 => ['bg-slate-200 text-slate-700 border-slate-300', 'text-slate-400'],
    3 => ['bg-orange-100 text-orange-700 border-orange-300', 'text-orange-500'],
];
?>
<section class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Rekap Penilaian Juri</h2>
            <p class="text-xs text-slate-500">Nilai dari setiap juri, nilai akhir hasil Sistem Pendukung Keputusan, dan calon juara per kategori.</p>
        </div>
        <?php if ($aktif) : ?>
            <div class="flex flex-wrap gap-2 text-xs">
                <form method="get" action="<?= site_url('rekap-penilaian') ?>">
                    <select name="kompetisi" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2.5">
                        <?php foreach ($kompetisi as $k) : ?>
                            <option value="<?= $k['id_kompetisi'] ?>" <?= (int) $k['id_kompetisi'] === (int) $aktif['id_kompetisi'] ? 'selected' : '' ?>>
                                <?= esc($k['nama_kompetisi']) ?> — <?= $k['status'] === 'berlangsung' ? 'Penjurian' : ($k['hasil_diumumkan'] ? 'Diumumkan' : 'Selesai') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <a href="<?= site_url('rekap-penilaian/export/' . $aktif['id_kompetisi']) ?>"
                    class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-lg flex items-center">
                    <i class="fa-solid fa-file-excel mr-2"></i>Unduh Excel
                </a>
            </div>
        <?php endif; ?>
    </div>

    <?php if (! $aktif) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-14 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-table-list text-2xl text-amber-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700">Belum ada lomba yang dinilai</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Rekap akan tersedia setelah lomba memasuki tahap penjurian.</p>
        </div>
    <?php else : ?>

        <!-- Status lomba -->
        <div class="rounded-2xl p-4 text-xs flex flex-wrap justify-between items-center gap-3 border
                    <?= $aktif['hasil_diumumkan'] ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : ($aktif['status'] === 'berlangsung' ? 'bg-amber-50 border-amber-200 text-amber-900' : 'bg-blue-50 border-blue-200 text-blue-800') ?>">
            <span>
                <?php if ($aktif['hasil_diumumkan']) : ?>
                    <i class="fa-solid fa-circle-check mr-1.5"></i><b>Hasil sudah diumumkan.</b> Peringkat di bawah adalah perhitungan ulang dari nilai juri saat ini.
                <?php elseif ($aktif['status'] === 'berlangsung') : ?>
                    <i class="fa-solid fa-gavel mr-1.5"></i><b>Penjurian berlangsung.</b> Klasemen berubah setiap kali juri menyimpan nilai.
                <?php else : ?>
                    <i class="fa-solid fa-flag-checkered mr-1.5"></i><b>Penjurian selesai.</b> Calon juara siap diumumkan melalui menu Apresiasi.
                <?php endif; ?>
            </span>
            <span class="font-semibold"><i class="fa-solid fa-calculator mr-1"></i>Metode: <?= esc($metode) ?></span>
        </div>

        <?php foreach ($kategori as $kat) : ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden text-xs">

                <!-- Kepala kategori & progres juri -->
                <div class="p-5 border-b space-y-3">
                    <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-tag text-indigo-500 mr-2"></i><?= esc($kat['nama']) ?>
                        <span class="text-slate-400 font-normal">(<?= count($kat['karya']) ?> karya dinilai<?= $kat['ditolak'] ? ', ' . count($kat['ditolak']) . ' tidak memenuhi syarat' : '' ?>)</span>
                    </h3>
                    <?php if (empty($kat['progres'])) : ?>
                        <p class="text-red-600 bg-red-50 border border-red-200 rounded-lg p-2.5"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Kategori ini belum memiliki juri. Atur di Kelola Data → Tim Juri.</p>
                    <?php else : ?>
                        <div class="flex flex-wrap gap-2">
                            <?php foreach ($kat['progres'] as $pr) : ?>
                                <?php $tuntas = $pr['total'] > 0 && $pr['selesai'] >= $pr['total']; ?>
                                <div class="border rounded-xl px-3 py-2 flex items-center gap-2 <?= $tuntas ? 'border-emerald-200 bg-emerald-50' : 'border-slate-200' ?>">
                                    <i class="fa-solid fa-user-tie <?= $tuntas ? 'text-emerald-600' : 'text-slate-400' ?>"></i>
                                    <span class="font-semibold text-slate-700"><?= esc($pr['nama']) ?></span>
                                    <span class="<?= $tuntas ? 'text-emerald-700' : 'text-amber-700' ?> font-bold"><?= $pr['selesai'] ?>/<?= $pr['total'] ?></span>
                                    <?php if (! $pr['ditugaskan']) : ?><span class="text-[9px] text-slate-400">(tidak lagi ditugaskan)</span><?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Tabel nilai -->
                <?php if (empty($kat['karya'])) : ?>
                    <p class="text-slate-400 text-center py-8">Belum ada karya di kategori ini.</p>
                <?php else : ?>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-slate-600">
                            <thead class="bg-slate-50 text-[10px] uppercase text-slate-500">
                                <tr>
                                    <th class="p-3 w-24">Peringkat</th>
                                    <th class="p-3">Karya</th>
                                    <?php foreach ($kat['juri'] as $idJ) : ?>
                                        <th class="p-3 text-center whitespace-nowrap"><?= esc($namaJuri[$idJ] ?? 'Juri #' . $idJ) ?></th>
                                    <?php endforeach; ?>
                                    <th class="p-3 text-right whitespace-nowrap bg-amber-50 text-amber-800">Nilai Akhir SPK</th>
                                    <th class="p-3 w-10"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($kat['karya'] as $i => $p) : ?>
                                    <?php $j = $p['calon_juara']; ?>
                                    <tr class="<?= $j ? 'bg-amber-50/30' : '' ?> hover:bg-slate-50">
                                        <td class="p-3">
                                            <?php if ($j) : ?>
                                                <span class="<?= $medali[$j][0] ?> border text-[10px] font-bold px-2 py-1 rounded-full whitespace-nowrap"><i class="fa-solid fa-medal mr-1"></i>Juara <?= $j ?></span>
                                            <?php elseif ($p['nilai_spk'] !== null) : ?>
                                                <span class="text-slate-400 font-bold pl-2"><?= $i + 1 ?></span>
                                            <?php else : ?>
                                                <span class="text-[10px] text-slate-400">Belum lengkap</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="p-3">
                                            <div class="font-semibold text-slate-800"><?= esc($p['judul_karya']) ?></div>
                                            <div class="text-[10px] text-slate-400"><?= esc($p['nama_sekolah'] ?? '-') ?> • <?= esc($p['nama_guru'] ?? '-') ?></div>
                                        </td>
                                        <?php foreach ($kat['juri'] as $idJ) : ?>
                                            <?php $pj = $p['per_juri'][$idJ] ?? null; ?>
                                            <td class="p-3 text-center">
                                                <?php if (! $pj || $pj['total'] === null) : ?>
                                                    <span class="text-slate-300">belum</span>
                                                <?php else : ?>
                                                    <span class="font-bold <?= $pj['lengkap'] ? 'text-slate-800' : 'text-amber-600' ?>"><?= $angka($pj['total']) ?></span>
                                                    <?php if (! $pj['lengkap']) : ?><div class="text-[9px] text-amber-600"><?= $pj['terisi'] ?>/<?= count($kriteria) ?> kriteria</div><?php endif; ?>
                                                <?php endif; ?>
                                            </td>
                                        <?php endforeach; ?>
                                        <td class="p-3 text-right bg-amber-50/60">
                                            <span class="text-base font-extrabold <?= $j ? $medali[$j][1] : 'text-slate-800' ?>"><?= $angka($p['nilai_spk']) ?></span>
                                        </td>
                                        <td class="p-3 text-center">
                                            <button type="button" onclick="document.getElementById('rinci-<?= $p['id_peserta'] ?>').classList.toggle('hidden')"
                                                title="Nilai per kriteria" class="w-7 h-7 rounded-lg border border-slate-200 hover:bg-slate-100"><i class="fa-solid fa-chevron-down text-[10px]"></i></button>
                                        </td>
                                    </tr>
                                    <!-- Rincian per kriteria -->
                                    <tr id="rinci-<?= $p['id_peserta'] ?>" class="hidden bg-slate-50/70">
                                        <td></td>
                                        <td colspan="<?= count($kat['juri']) + 3 ?>" class="p-3">
                                            <table class="w-full text-[11px]">
                                                <thead class="text-slate-500">
                                                    <tr>
                                                        <th class="text-left py-1 pr-3">Kriteria (bobot)</th>
                                                        <?php foreach ($kat['juri'] as $idJ) : ?><th class="text-center py-1 px-2"><?= esc($namaJuri[$idJ] ?? '-') ?></th><?php endforeach; ?>
                                                        <th class="text-center py-1 px-2 text-indigo-700">Rata-rata</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-slate-200">
                                                    <?php foreach ($kriteria as $kr) : ?>
                                                        <tr>
                                                            <td class="py-1.5 pr-3 text-slate-700"><?= esc($kr['nama_kriteria']) ?> <span class="text-slate-400">(<?= (int) $kr['skor_maks'] ?>%)</span></td>
                                                            <?php foreach ($kat['juri'] as $idJ) : ?>
                                                                <?php $s = $p['per_juri'][$idJ]['skor'][(int) $kr['id_kriteria']] ?? null; ?>
                                                                <td class="py-1.5 px-2 text-center font-semibold"><?= $s === null ? '<span class="text-slate-300">–</span>' : $angka($s) ?></td>
                                                            <?php endforeach; ?>
                                                            <td class="py-1.5 px-2 text-center font-bold text-indigo-700"><?= $angka($p['rata'][(int) $kr['id_kriteria']] ?? null) ?></td>
                                                        </tr>
                                                    <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <p class="px-5 py-2 text-[10px] text-slate-400 border-t">Kolom juri = nilai terbobot dari juri tersebut (Σ nilai × bobot). Nilai Akhir SPK dihitung dari rata-rata nilai seluruh juri per kriteria.</p>
                <?php endif; ?>

                <!-- Karya tidak memenuhi syarat -->
                <?php if ($kat['ditolak']) : ?>
                    <div class="border-t p-4 space-y-2">
                        <div class="font-bold text-red-700 text-[11px] uppercase tracking-wider"><i class="fa-solid fa-ban mr-1"></i>Tidak Memenuhi Syarat</div>
                        <?php foreach ($kat['ditolak'] as $p) : ?>
                            <div class="bg-red-50 border border-red-100 rounded-lg p-2.5">
                                <div class="font-semibold text-slate-800"><?= esc($p['judul_karya']) ?> <span class="text-slate-400 font-normal">• <?= esc($p['nama_sekolah'] ?? '-') ?> • <?= esc($p['nama_guru'] ?? '-') ?></span></div>
                                <div class="text-slate-600 mt-0.5"><?= esc($p['catatan_juri'] ?: '-') ?> <span class="text-[10px] text-red-600">— <?= esc($p['juri_penolak'] ?? 'Juri') ?></span></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <!-- Langkah perhitungan SPK -->
                <?php if ($kat['spk']) : ?>
                    <?php $spk = $kat['spk'];
                    $judulKarya = array_column($kat['karya'], 'judul_karya', 'id_peserta'); ?>
                    <details class="border-t group">
                        <summary class="px-5 py-3 cursor-pointer list-none flex justify-between items-center font-semibold text-indigo-700 hover:bg-indigo-50/40">
                            <span><i class="fa-solid fa-calculator mr-2"></i>Langkah perhitungan <?= esc($spk['metode']) ?></span>
                            <i class="fa-solid fa-chevron-down transition group-open:rotate-180"></i>
                        </summary>
                        <div class="px-5 pb-5 space-y-4 overflow-x-auto">
                            <?php
                            $tabelLangkah = [
                                ['1. Matriks keputusan (rata-rata nilai juri, x)', 'matriks', 2],
                                ['2. Normalisasi (r = x ÷ nilai maksimal kriteria)', 'normalisasi', 4],
                            ];
                            ?>
                            <?php foreach ($tabelLangkah as [$judulLangkah, $kunci, $desimal]) : ?>
                                <div>
                                    <div class="font-bold text-slate-700 mb-1.5"><?= $judulLangkah ?></div>
                                    <table class="w-full text-[11px] border border-slate-200">
                                        <thead class="bg-slate-50 text-slate-500">
                                            <tr>
                                                <th class="text-left p-2 border-b">Karya</th>
                                                <?php foreach ($kriteria as $kr) : ?><th class="p-2 border-b text-center"><?= esc($kr['nama_kriteria']) ?></th><?php endforeach; ?>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($spk[$kunci] as $idP => $baris) : ?>
                                                <tr class="border-b border-slate-100">
                                                    <td class="p-2 text-slate-700"><?= esc($judulKarya[$idP] ?? '#' . $idP) ?></td>
                                                    <?php foreach ($kriteria as $kr) : ?>
                                                        <td class="p-2 text-center"><?= number_format((float) ($baris[(int) $kr['id_kriteria']] ?? 0), $desimal, ',', '.') ?></td>
                                                    <?php endforeach; ?>
                                                </tr>
                                            <?php endforeach; ?>
                                            <?php if ($kunci === 'matriks') : ?>
                                                <tr class="bg-slate-50 font-bold">
                                                    <td class="p-2">Nilai maksimal</td>
                                                    <?php foreach ($kriteria as $kr) : ?><td class="p-2 text-center"><?= $angka($spk['maks'][(int) $kr['id_kriteria']] ?? 0) ?></td><?php endforeach; ?>
                                                </tr>
                                            <?php else : ?>
                                                <tr class="bg-amber-50 font-bold text-amber-800">
                                                    <td class="p-2">Bobot (w)</td>
                                                    <?php foreach ($kriteria as $kr) : ?><td class="p-2 text-center"><?= number_format((float) ($spk['bobot'][(int) $kr['id_kriteria']] ?? 0), 2, ',', '.') ?></td><?php endforeach; ?>
                                                </tr>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endforeach; ?>
                            <div>
                                <div class="font-bold text-slate-700 mb-1.5">3. Nilai preferensi (V = Σ w × r), ditampilkan dalam skala 0–100</div>
                                <div class="flex flex-wrap gap-2">
                                    <?php foreach ($kat['karya'] as $p) : ?>
                                        <?php if ($p['nilai_spk'] !== null) : ?>
                                            <span class="border rounded-lg px-2.5 py-1.5 <?= $p['calon_juara'] ? $medali[$p['calon_juara']][0] : 'border-slate-200' ?>">
                                                <?= esc($p['judul_karya']) ?>: <b><?= $angka($p['nilai_spk']) ?></b>
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </details>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>

        <?php if (! $aktif['hasil_diumumkan'] && $aktif['status'] === 'selesai') : ?>
            <div class="flex justify-end">
                <a href="<?= site_url('apresiasi?kompetisi=' . $aktif['id_kompetisi']) ?>" class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold px-5 py-2.5 rounded-xl shadow flex items-center">
                    <i class="fa-solid fa-bullhorn mr-2"></i>Umumkan Juara di Menu Apresiasi
                </a>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>