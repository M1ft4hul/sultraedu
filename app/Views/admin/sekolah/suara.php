<?php

/** @var array $riwayat */
/** @var array $kategori */
/** @var array $status */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors = session()->getFlashdata('errors') ?? [];
$gaya   = [
    'saran'      => ['bg-blue-50 text-blue-700 border-blue-200',     'fa-lightbulb',            'Ide atau usulan pengembangan'],
    'keluhan'    => ['bg-red-50 text-red-700 border-red-200',        'fa-triangle-exclamation', 'Kendala atau masalah yang dialami'],
    'pertanyaan' => ['bg-amber-50 text-amber-800 border-amber-200',  'fa-circle-question',      'Hal yang ingin ditanyakan ke Dinas'],
    'lainnya'    => ['bg-slate-100 text-slate-600 border-slate-200', 'fa-comment-dots',         'Aspirasi atau masukan lainnya'],
];
$pilihan = old('kategori') ?: 'saran';

$gayaStatus = [
    'belum_ditindak' => ['bg-slate-100 text-slate-600', 'fa-check', 'Terkirim'],
    'diproses'       => ['bg-amber-50 text-amber-700', 'fa-spinner', 'Sedang Diproses'],
    'selesai'        => ['bg-emerald-50 text-emerald-700', 'fa-reply', 'Dijawab Dinas'],
];

$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y, H:i', strtotime($d));
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">SUARA</h2>
        <p class="text-xs text-slate-500">Saluran Umpan Balik & Aspirasi. Sampaikan kritik, saran, pertanyaan, atau keluhan sekolah Anda langsung kepada Dinas.</p>
    </div>

    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form kirim -->
        <form action="<?= site_url('suara/kirim') ?>" method="post" class="lg:col-span-2 bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <?= csrf_field() ?>
            <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-paper-plane text-brand-600 mr-2"></i>Kirim SUARA</h3>

            <?php if ($errors) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errors as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <!-- Pilih kategori -->
            <div>
                <label class="font-semibold block mb-2 text-slate-700">Kategori</label>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                    <?php foreach ($kategori as $kunci => $teks) : ?>
                        <label class="cursor-pointer">
                            <input type="radio" name="kategori" value="<?= $kunci ?>" class="peer hidden" <?= $pilihan === $kunci ? 'checked' : '' ?>>
                            <div class="border-2 border-slate-200 rounded-xl p-3 text-center transition
                                        peer-checked:border-brand-500 peer-checked:bg-brand-50 hover:bg-slate-50">
                                <div class="w-9 h-9 mx-auto rounded-full flex items-center justify-center border <?= $gaya[$kunci][0] ?>">
                                    <i class="fa-solid <?= $gaya[$kunci][1] ?>"></i>
                                </div>
                                <div class="font-bold text-slate-700 mt-1.5"><?= esc($teks) ?></div>
                                <div class="text-[10px] text-slate-400 leading-tight mt-0.5"><?= $gaya[$kunci][2] ?></div>
                            </div>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Isi -->
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Isi SUARA</label>
                <textarea name="isi_suara" id="isiSuara" rows="6" maxlength="2000" required
                    oninput="document.getElementById('hitungIsi').textContent = this.value.length"
                    placeholder="Uraikan kritik, saran, pertanyaan, atau keluhan Anda secara jelas dan spesifik..."
                    class="w-full border border-slate-300 rounded-lg p-3 focus:ring-2 focus:ring-brand-500 focus:outline-none"><?= esc(old('isi_suara')) ?></textarea>
                <div class="flex justify-between text-[10px] text-slate-400 mt-1">
                    <span>Minimal 10 karakter.</span>
                    <span><span id="hitungIsi"><?= mb_strlen((string) old('isi_suara')) ?></span> / 2000</span>
                </div>
            </div>

            <div class="flex justify-end border-t pt-4">
                <button type="submit" class="px-5 py-2.5 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-paper-plane mr-1.5"></i> Kirim ke Dinas
                </button>
            </div>
        </form>

        <!-- Info -->
        <div class="bg-gradient-to-br from-slate-900 to-indigo-950 text-white p-6 rounded-2xl shadow-md border border-slate-800 text-xs space-y-3 h-fit">
            <h3 class="font-bold text-amber-400 text-sm"><i class="fa-solid fa-circle-info mr-2"></i>Tentang SUARA</h3>
            <p class="text-slate-300 leading-relaxed">SUARA yang Anda kirim akan diterima langsung oleh Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara sebagai bahan evaluasi penyelenggaraan pendidikan.</p>
            <ul class="space-y-2 text-slate-300">
                <li class="flex gap-2"><i class="fa-solid fa-school text-amber-400 mt-0.5"></i>Nama sekolah dan nama Anda tercatat sebagai pengirim.</li>
                <li class="flex gap-2"><i class="fa-solid fa-pen text-amber-400 mt-0.5"></i>Gunakan bahasa yang santun dan sertakan informasi yang spesifik.</li>
                <li class="flex gap-2"><i class="fa-solid fa-lock text-amber-400 mt-0.5"></i>SUARA yang sudah dikirim tidak dapat diubah.</li>
                <li class="flex gap-2"><i class="fa-solid fa-reply text-amber-400 mt-0.5"></i>Tanggapan Dinas akan muncul di riwayat di bawah.</li>
            </ul>
        </div>
    </div>

    <!-- Riwayat kiriman sekolah -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <h3 class="font-bold text-slate-800 text-sm">
            <i class="fa-solid fa-clock-rotate-left text-slate-500 mr-2"></i>Riwayat SUARA Sekolah
            <span class="text-slate-400 font-normal">(<?= count($riwayat) ?>)</span>
        </h3>

        <?php if (empty($riwayat)) : ?>
            <div class="text-center py-8 text-xs text-slate-400">
                <i class="fa-regular fa-comments text-3xl mb-2 block"></i>
                Sekolah Anda belum pernah mengirim SUARA.
            </div>
        <?php else : ?>
            <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                <?php foreach ($riwayat as $r) : ?>
                    <?php $g = $gaya[$r['kategori']] ?? $gaya['lainnya']; ?>
                    <details class="group">
                        <summary class="flex items-start gap-3 p-4 text-xs cursor-pointer hover:bg-slate-50 list-none">
                            <div class="w-9 h-9 rounded-full flex items-center justify-center shrink-0 border <?= $g[0] ?>">
                                <i class="fa-solid <?= $g[1] ?>"></i>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <span class="font-bold text-slate-800"><?= esc($kategori[$r['kategori']] ?? $r['kategori']) ?></span>
                                    <span class="text-slate-400">•</span>
                                    <span class="text-slate-500">oleh <?= esc($r['pengirim']) ?></span>
                                </div>
                                <p class="text-slate-600 truncate group-open:hidden"><?= esc($r['isi_suara']) ?></p>
                            </div>
                            <div class="text-right shrink-0 space-y-1">
                                <?php $gs = $gayaStatus[$r['status_tindak_lanjut']] ?? $gayaStatus['belum_ditindak']; ?>
                                <span class="inline-block text-[10px] font-bold px-2 py-0.5 rounded-full <?= $gs[0] ?>"><i class="fa-solid <?= $gs[1] ?> mr-1"></i><?= $gs[2] ?></span>
                                <div class="text-[10px] text-slate-400 whitespace-nowrap"><?= $tgl($r['tanggal_kirim']) ?></div>
                            </div>
                            <i class="fa-solid fa-chevron-down text-slate-400 mt-3 transition group-open:rotate-180"></i>
                        </summary>
                        <div class="px-4 pb-4 pl-16 text-xs space-y-3">
                            <div class="bg-slate-50 border rounded-xl p-3.5 text-slate-700 whitespace-pre-line leading-relaxed"><?= esc($r['isi_suara']) ?></div>
                            <?php if ($r['tanggapan']) : ?>
                                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-3.5 space-y-1.5">
                                    <div class="font-bold text-emerald-800"><i class="fa-solid fa-reply mr-1"></i>Tanggapan Dinas</div>
                                    <div class="text-slate-700 whitespace-pre-line leading-relaxed"><?= esc($r['tanggapan']) ?></div>
                                    <div class="text-[10px] text-emerald-700"><?= $r['tanggal_tindak_lanjut'] ? $tgl($r['tanggal_tindak_lanjut']) : '' ?> • <?= esc($status[$r['status_tindak_lanjut']] ?? '') ?></div>
                                </div>
                            <?php else : ?>
                                <p class="text-[11px] text-slate-400 italic"><i class="fa-regular fa-clock mr-1"></i>Belum ada tanggapan dari Dinas.</p>
                            <?php endif; ?>
                        </div>
                    </details>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
<?= $this->endSection() ?>