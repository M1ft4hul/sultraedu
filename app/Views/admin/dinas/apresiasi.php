<?php

/** @var array $daftar */
/** @var array|null $dipilih */
/** @var array $kategori */
/** @var array $pemenang */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$medali = [
    1 => ['bg-amber-100 text-amber-700 border-amber-300', 'Juara 1'],
    2 => ['bg-slate-200 text-slate-700 border-slate-300', 'Juara 2'],
    3 => ['bg-orange-100 text-orange-700 border-orange-300', 'Juara 3'],
];
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$angka = fn($n) => $n === null ? '–' : number_format((float) $n, 2, ',', '.');
?>
<section class="space-y-6">

    <!-- Judul -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Apresiasi & Pengumuman Juara</h2>
        <p class="text-xs text-slate-500">Umumkan hasil kompetisi berdasarkan penilaian juri dan unggah piagam untuk para pemenang.</p>
    </div>

    <!-- Pesan -->
    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('gagal')) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2"></i><?= session()->getFlashdata('gagal') ?>
        </div>
    <?php endif; ?>

    <?php if (empty($daftar)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-emerald-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-award text-2xl text-emerald-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700">Belum ada kompetisi yang selesai</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">
                Kompetisi yang sudah melewati tahap penjurian dan berstatus Selesai akan tampil di sini untuk diumumkan hasilnya.
            </p>
        </div>
    <?php else : ?>

        <!-- Pilih kompetisi -->
        <div class="flex flex-wrap gap-2 text-xs font-semibold">
            <?php foreach ($daftar as $d) : ?>
                <?php $aktif = $dipilih && $dipilih['id_kompetisi'] == $d['id_kompetisi']; ?>
                <a href="<?= site_url('apresiasi') . '?kompetisi=' . $d['id_kompetisi'] ?>"
                    class="px-4 py-2 rounded-xl border flex items-center gap-2 transition
                           <?= $aktif ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                    <i class="fa-solid fa-trophy"></i><?= esc($d['nama_kompetisi']) ?>
                    <?php if ($d['hasil_diumumkan']) : ?>
                        <span class="<?= $aktif ? 'bg-white/25' : 'bg-emerald-100 text-emerald-700' ?> text-[10px] px-1.5 py-0.5 rounded-full">Diumumkan</span>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if ($dipilih && ! $dipilih['hasil_diumumkan']) : ?>
            <!-- =========== BELUM DIUMUMKAN: pratinjau peringkat =========== -->
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 flex flex-wrap justify-between items-center gap-4 text-xs">
                <div class="text-amber-900">
                    <p class="font-bold text-sm"><i class="fa-solid fa-eye mr-1"></i> Pratinjau hasil — belum diumumkan</p>
                    <p class="mt-1">Peringkat dihitung dari rata-rata nilai semua juri. Juara 1–3 di setiap kategori akan ditetapkan saat hasil diumumkan.</p>
                </div>
                <?php if (! empty($kategori)) : ?>
                    <button type="button" onclick="openModal('modalUmumkan')"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2.5 rounded-xl shadow flex items-center">
                        <i class="fa-solid fa-bullhorn mr-2"></i>Umumkan Hasil
                    </button>
                <?php endif; ?>
            </div>

            <?php if (empty($kategori)) : ?>
                <div class="bg-white rounded-2xl border border-slate-200 p-8 text-center text-xs text-slate-500">
                    Belum ada karya tervalidasi pada kompetisi ini.
                </div>
            <?php endif; ?>

            <?php foreach ($kategori as $kat) : ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b bg-slate-50 font-bold text-slate-700 text-sm">
                        <i class="fa-solid fa-tag text-indigo-500 mr-1"></i> <?= esc($kat['nama']) ?>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs text-slate-600">
                            <thead class="text-slate-500 uppercase tracking-wider text-[10px]">
                                <tr>
                                    <th class="px-5 py-2.5 w-24">Posisi</th>
                                    <th class="px-5 py-2.5">Karya</th>
                                    <th class="px-5 py-2.5">Sekolah</th>
                                    <th class="px-5 py-2.5 text-center">Juri</th>
                                    <th class="px-5 py-2.5 text-right">Nilai</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                <?php foreach ($kat['peserta'] as $i => $p) : ?>
                                    <tr class="<?= $p['calon_juara'] ? 'bg-emerald-50/40' : '' ?>">
                                        <td class="px-5 py-3">
                                            <?php if ($p['calon_juara']) : ?>
                                                <span class="<?= $medali[$p['calon_juara']][0] ?> border text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                                    <i class="fa-solid fa-medal mr-1"></i><?= $medali[$p['calon_juara']][1] ?>
                                                </span>
                                            <?php else : ?>
                                                <span class="text-slate-400">#<?= $i + 1 ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3">
                                            <div class="font-semibold text-slate-800"><?= esc($p['judul_karya']) ?></div>
                                            <div class="text-[11px] text-slate-400"><?= esc($p['nama_guru'] ?? '-') ?></div>
                                        </td>
                                        <td class="px-5 py-3"><?= esc($p['nama_sekolah'] ?? '-') ?></td>
                                        <td class="px-5 py-3 text-center">
                                            <?php if ($p['jumlah_juri'] > 0) : ?>
                                                <?= $p['jumlah_juri'] ?>
                                            <?php else : ?>
                                                <span class="text-red-500 text-[11px]">Belum dinilai</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-5 py-3 text-right font-bold text-slate-800"><?= $angka($p['nilai_akhir']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Modal konfirmasi umumkan -->
            <div id="modalUmumkan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
                    <div class="mx-auto w-14 h-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                        <i class="fa-solid fa-bullhorn text-2xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-800 text-base">Umumkan hasil kompetisi?</h3>
                        <p class="text-xs text-slate-500 mt-1 leading-relaxed">
                            Juara 1–3 di setiap kategori akan ditetapkan sesuai pratinjau dan dapat dilihat oleh sekolah. Pengumuman tidak bisa dibatalkan.
                        </p>
                    </div>
                    <form action="<?= site_url('apresiasi/umumkan/' . $dipilih['id_kompetisi']) ?>" method="post" class="flex justify-center space-x-2 pt-2">
                        <?= csrf_field() ?>
                        <button type="button" onclick="closeModal('modalUmumkan')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold">Ya, Umumkan</button>
                    </form>
                </div>
            </div>

        <?php elseif ($dipilih) : ?>
            <!-- =========== SUDAH DIUMUMKAN: daftar pemenang & piagam =========== -->
            <?php
            $totalJuara  = array_sum(array_map('count', $pemenang));
            $sudahPiagam = 0;
            foreach ($pemenang as $list) {
                foreach ($list as $w) {
                    $sudahPiagam += $w['bukti_file'] ? 1 : 0;
                }
            }
            ?>
            <div class="bg-emerald-50 border border-emerald-200 rounded-2xl p-5 flex flex-wrap justify-between items-center gap-4 text-xs">
                <div class="text-emerald-900">
                    <p class="font-bold text-sm"><i class="fa-solid fa-circle-check mr-1"></i> Hasil sudah diumumkan</p>
                    <p class="mt-1">Diumumkan pada <?= $tgl($dipilih['tanggal_pengumuman']) ?>. Sekolah pemenang dapat melihat hasil dan mengunduh piagamnya.</p>
                </div>
                <div class="text-right">
                    <div class="text-[11px] text-emerald-700">Piagam terunggah</div>
                    <div class="text-xl font-bold <?= $sudahPiagam === $totalJuara ? 'text-emerald-700' : 'text-amber-600' ?>">
                        <?= $sudahPiagam ?> / <?= $totalJuara ?>
                    </div>
                </div>
            </div>

            <?php foreach ($pemenang as $namaKategori => $daftarJuara) : ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
                    <div class="px-5 py-3 border-b bg-slate-50 font-bold text-slate-700 text-sm">
                        <i class="fa-solid fa-tag text-indigo-500 mr-1"></i> <?= esc($namaKategori) ?>
                    </div>
                    <div class="divide-y divide-slate-100">
                        <?php foreach ($daftarJuara as $w) : ?>
                            <div class="px-5 py-4 flex flex-wrap items-center gap-4 text-xs">
                                <span class="<?= $medali[$w['peringkat']][0] ?? '' ?> border text-[11px] font-bold px-3 py-1.5 rounded-full whitespace-nowrap">
                                    <i class="fa-solid fa-medal mr-1"></i><?= $medali[$w['peringkat']][1] ?? 'Juara' ?>
                                </span>
                                <div class="flex-1 min-w-[200px]">
                                    <div class="font-bold text-slate-800"><?= esc($w['judul_karya']) ?></div>
                                    <div class="text-[11px] text-slate-500">
                                        <?= esc($w['nama_sekolah'] ?? '-') ?> • <?= esc($w['nama_guru'] ?? '-') ?> • Nilai <?= $angka($w['nilai']) ?>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <?php if ($w['bukti_file']) : ?>
                                        <a href="<?= base_url($w['bukti_file']) ?>" target="_blank"
                                            class="border border-emerald-300 text-emerald-700 hover:bg-emerald-50 font-semibold px-3 py-1.5 rounded-lg flex items-center">
                                            <i class="fa-solid fa-file-circle-check mr-1.5"></i>Lihat Piagam
                                        </a>
                                        <button type="button" onclick="bukaUnggah(<?= $w['id_apresiasi'] ?>, <?= esc(json_encode($w['jenis_apresiasi'] . ' — ' . $w['nama_sekolah']), 'attr') ?>)"
                                            class="text-slate-500 hover:text-slate-700 font-semibold px-2 py-1.5">
                                            Ganti
                                        </button>
                                    <?php else : ?>
                                        <button type="button" onclick="bukaUnggah(<?= $w['id_apresiasi'] ?>, <?= esc(json_encode($w['jenis_apresiasi'] . ' — ' . $w['nama_sekolah']), 'attr') ?>)"
                                            class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-3 py-1.5 rounded-lg flex items-center">
                                            <i class="fa-solid fa-upload mr-1.5"></i>Unggah Piagam
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <!-- Modal unggah piagam -->
            <div id="modalUnggah" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <form id="formUnggah" method="post" enctype="multipart/form-data"
                    class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
                    <?= csrf_field() ?>
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-file-arrow-up text-brand-600 mr-2"></i>Unggah Piagam</h3>
                            <p id="unggahNama" class="text-slate-500 mt-1"></p>
                        </div>
                        <button type="button" onclick="closeModal('modalUnggah')" class="text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-xmark text-lg"></i>
                        </button>
                    </div>
                    <label class="block border-2 border-dashed border-slate-300 hover:border-brand-500 rounded-xl p-6 text-center cursor-pointer transition">
                        <i class="fa-solid fa-cloud-arrow-up text-3xl text-slate-400"></i>
                        <p id="unggahFile" class="mt-2 font-semibold text-slate-600">Klik untuk memilih file</p>
                        <p class="text-[11px] text-slate-400 mt-1">PDF, JPG, atau PNG • maksimal 2 MB</p>
                        <input type="file" name="piagam" accept=".pdf,.jpg,.jpeg,.png" required class="hidden"
                            onchange="document.getElementById('unggahFile').textContent = this.files[0]?.name || 'Klik untuk memilih file'">
                    </label>
                    <div class="flex justify-end space-x-2">
                        <button type="button" onclick="closeModal('modalUnggah')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                            <i class="fa-solid fa-upload mr-1"></i> Unggah
                        </button>
                    </div>
                </form>
            </div>

            <script>
                function bukaUnggah(id, nama) {
                    const form = document.getElementById('formUnggah');
                    form.action = '<?= site_url('apresiasi/piagam') ?>/' + id;
                    form.reset();
                    document.getElementById('unggahFile').textContent = 'Klik untuk memilih file';
                    document.getElementById('unggahNama').textContent = nama;
                    openModal('modalUnggah');
                }
            </script>
        <?php endif; ?>
    <?php endif; ?>
</section>
<?= $this->endSection() ?>