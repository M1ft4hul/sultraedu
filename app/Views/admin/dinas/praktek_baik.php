<?php

/** @var array $stat */
/** @var array $praktik */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$badgeStatus = [
    'menunggu'  => 'bg-amber-100 text-amber-800',
    'disetujui' => 'bg-emerald-100 text-emerald-800',
    'ditolak'   => 'bg-red-100 text-red-700',
];
?>
<section class="space-y-6">
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Validasi Praktik Baik</h2>
        <p class="text-xs text-slate-500">Praktik baik yang telah diverifikasi Admin Sekolah dan menunggu validasi Dinas.</p>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <?php if (empty($praktik)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-regular fa-folder-open text-2xl text-brand-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum ada praktik baik</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">
                    Praktik baik yang sudah diverifikasi Admin Sekolah akan tampil di sini untuk divalidasi.
                </p>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 rounded-l-lg">Judul</th>
                            <th class="p-3.5">Sekolah</th>
                            <th class="p-3.5">Guru</th>
                            <th class="p-3.5">Tanggal</th>
                            <th class="p-3.5">Status Dinas</th>
                            <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($praktik as $p) : ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5">
                                    <div class="font-bold text-slate-800"><?= esc($p['judul']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= esc($p['kategori']) ?></div>
                                </td>
                                <td class="p-3.5">
                                    <?= esc($p['nama_sekolah']) ?>
                                    <div class="text-[11px] text-slate-400"><?= esc($p['kabupaten_kota']) ?></div>
                                </td>
                                <td class="p-3.5"><?= esc($p['nama_guru']) ?></td>
                                <td class="p-3.5 whitespace-nowrap"><?= date('d/m/Y', strtotime($p['tanggal_upload'])) ?></td>
                                <td class="p-3.5">
                                    <span class="<?= $badgeStatus[$p['status_verifikasi_dinas']] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2.5 py-1 rounded-full capitalize">
                                        <?= esc($p['status_verifikasi_dinas']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <button type="button" class="bg-brand-600 hover:bg-brand-700 text-white text-[10px] px-3 py-1.5 rounded-lg font-semibold">
                                        Periksa
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
<?= $this->endSection() ?>