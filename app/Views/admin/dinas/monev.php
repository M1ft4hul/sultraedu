<?php

/** @var array $indikator */
/** @var array $perKab */
/** @var array $catatan */
/** @var string $filterSekolah */
/** @var array $aspek */
/** @var array $sekolah */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors = session()->getFlashdata('errors') ?? [];
$bulan  = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl    = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$n      = fn($x) => number_format((float) $x, 0, ',', '.');

$kartu = [
    ['Partisipasi Sekolah', str_replace('.', ',', $indikator['partisipasi']) . '%', $indikator['sekolahIkut'] . ' dari ' . $indikator['sekolahAktif'] . ' sekolah aktif', 'fa-school', 'bg-blue-50 text-blue-600'],
    ['Praktik Baik Tervalidasi', $n($indikator['praktik']), $indikator['praktikTunggu'] . ' menunggu validasi', 'fa-book-open', 'bg-emerald-50 text-emerald-600'],
    ['Inovasi di Bank Inovasi', $n($indikator['inovasi']), 'Terverifikasi Dinas', 'fa-lightbulb', 'bg-indigo-50 text-indigo-600'],
    ['Juara & Apresiasi', $n($indikator['apresiasi']), 'Dari hasil kompetisi', 'fa-award', 'bg-amber-50 text-amber-600'],
    ['Peserta Kompetisi', $n($indikator['peserta']), 'Seluruh kompetisi', 'fa-trophy', 'bg-orange-50 text-orange-600'],
    ['SUARA Masuk', $n($indikator['suara']), 'Kritik, saran & aspirasi', 'fa-comments', 'bg-purple-50 text-purple-600'],
];
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Monitoring & Evaluasi (Monev)</h2>
        <p class="text-xs text-slate-500">Indikator dihitung otomatis dari data platform, dilengkapi catatan pemantauan lapangan oleh Dinas.</p>
    </div>

    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>

    <!-- Indikator -->
    <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($kartu as [$judul, $angka, $ket, $ikon, $warna]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div class="min-w-0">
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $angka ?></div>
                    <div class="text-[11px] text-slate-400 truncate"><?= esc($ket) ?></div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Sebaran per kabupaten/kota -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 mb-3 uppercase tracking-wider">
                <i class="fa-solid fa-chart-column text-brand-600 mr-2"></i>Karya Tervalidasi per Kabupaten/Kota
            </h3>
            <div class="h-80"><canvas id="grafikKab"></canvas></div>
        </div>

        <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
            <h3 class="text-xs font-bold text-slate-800 mb-3 uppercase tracking-wider">
                <i class="fa-solid fa-map-location-dot text-emerald-600 mr-2"></i>Partisipasi Sekolah per Wilayah
            </h3>
            <div class="overflow-y-auto" style="max-height: 20rem">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="text-[10px] uppercase text-slate-500 sticky top-0 bg-white">
                        <tr>
                            <th class="py-2">Wilayah</th>
                            <th class="py-2 text-center">Sekolah</th>
                            <th class="py-2 w-40">Partisipasi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($perKab as $kab => $v) : ?>
                            <?php $persen = $v['sekolah'] ? round($v['ikut'] / $v['sekolah'] * 100) : 0; ?>
                            <tr>
                                <td class="py-2 font-medium text-slate-700"><?= esc($kab) ?></td>
                                <td class="py-2 text-center"><?= $v['ikut'] ?>/<?= $v['sekolah'] ?></td>
                                <td class="py-2">
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-slate-100 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full <?= $persen >= 70 ? 'bg-emerald-500' : ($persen >= 40 ? 'bg-amber-500' : 'bg-red-400') ?>" style="width: <?= $persen ?>%"></div>
                                        </div>
                                        <span class="w-9 text-right font-semibold"><?= $v['sekolah'] ? $persen . '%' : '–' ?></span>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Catatan monev lapangan -->
    <div id="catatan" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <h3 class="text-sm font-bold text-slate-800">
                <i class="fa-solid fa-clipboard-list text-brand-600 mr-2"></i>Catatan Monev Lapangan
                <span class="text-slate-400 font-normal">(<?= count($catatan) ?>)</span>
            </h3>
            <div class="flex gap-2 text-xs">
                <form method="get" action="<?= site_url('monev') ?>#catatan">
                    <select name="sekolah" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                        <option value="">Semua sekolah</option>
                        <?php foreach ($sekolah as $s) : ?>
                            <option value="<?= $s['id_sekolah'] ?>" <?= $filterSekolah == $s['id_sekolah'] ? 'selected' : '' ?>><?= esc($s['nama_sekolah']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </form>
                <button type="button" onclick="bukaFormMonev()" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i>Tambah Catatan
                </button>
            </div>
        </div>

        <?php if (empty($catatan)) : ?>
            <div class="text-center py-8 text-xs text-slate-400">
                <i class="fa-regular fa-clipboard text-3xl mb-2 block"></i>
                Belum ada catatan monev lapangan.
            </div>
        <?php else : ?>
            <div class="space-y-3">
                <?php foreach ($catatan as $c) : ?>
                    <div class="border border-slate-200 rounded-xl p-4 text-xs space-y-2">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div>
                                <span class="bg-brand-50 text-brand-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><?= esc($c['aspek_monev']) ?></span>
                                <div class="font-bold text-slate-800 mt-2"><?= esc($c['nama_sekolah'] ?? '-') ?>
                                    <span class="font-normal text-slate-400">• <?= esc($c['kabupaten_kota'] ?? '') ?></span>
                                </div>
                                <div class="text-[11px] text-slate-400"><i class="fa-regular fa-calendar mr-1"></i><?= $tgl($c['tanggal_monev']) ?> • dicatat oleh <?= esc($c['pencatat'] ?? '-') ?></div>
                            </div>
                            <div class="flex gap-1.5">
                                <button type="button" title="Edit" data-monev="<?= esc(json_encode($c), 'attr') ?>"
                                    onclick="bukaFormMonev(JSON.parse(this.dataset.monev))"
                                    class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                    <i class="fa-solid fa-pen text-[11px]"></i>
                                </button>
                                <button type="button" title="Hapus"
                                    onclick="hapusMonev('<?= site_url('monev/hapus/' . $c['id_monev']) ?>')"
                                    class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-red-50 hover:text-red-600 flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-[11px]"></i>
                                </button>
                            </div>
                        </div>
                        <?php if ($c['deskripsi']) : ?>
                            <p class="text-slate-500 whitespace-pre-line"><?= esc($c['deskripsi']) ?></p>
                        <?php endif; ?>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                            <div class="bg-slate-50 rounded-lg p-3">
                                <div class="text-[10px] font-bold text-slate-500 uppercase mb-1">Hasil Temuan</div>
                                <p class="text-slate-700 whitespace-pre-line"><?= esc($c['hasil_temuan']) ?></p>
                            </div>
                            <div class="bg-emerald-50 rounded-lg p-3">
                                <div class="text-[10px] font-bold text-emerald-700 uppercase mb-1">Rekomendasi</div>
                                <p class="text-slate-700 whitespace-pre-line"><?= esc($c['rekomendasi'] ?: '-') ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal form catatan -->
<div id="modalMonev" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('monev/simpan') ?>" method="post" class="bg-white rounded-2xl max-w-2xl w-full p-6 shadow-2xl max-h-[90vh] overflow-y-auto space-y-4 text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_monev" id="m_id">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-clipboard-list text-brand-600 mr-2"></i><span id="m_judul">Tambah Catatan Monev</span></h3>
            <button type="button" onclick="closeModal('modalMonev')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <?php if ($errors) : ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside"><?php foreach ($errors as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Sekolah <span class="text-red-500">*</span></label>
                <select name="id_sekolah" id="m_id_sekolah" required class="w-full border border-slate-300 rounded-lg p-2.5">
                    <option value="">Pilih sekolah</option>
                    <?php foreach ($sekolah as $s) : ?>
                        <option value="<?= $s['id_sekolah'] ?>"><?= esc($s['nama_sekolah']) ?> — <?= esc($s['kabupaten_kota']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Tanggal Monev <span class="text-red-500">*</span></label>
                <input type="date" name="tanggal_monev" id="m_tanggal_monev" required class="w-full border border-slate-300 rounded-lg p-2.5">
            </div>
        </div>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Aspek yang Dipantau <span class="text-red-500">*</span></label>
            <input type="text" name="aspek_monev" id="m_aspek_monev" list="daftarAspek" required placeholder="Pilih atau ketik aspek" class="w-full border border-slate-300 rounded-lg p-2.5">
            <datalist id="daftarAspek">
                <?php foreach ($aspek as $a) : ?><option value="<?= esc($a) ?>"><?php endforeach; ?>
            </datalist>
        </div>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Deskripsi Kegiatan</label>
            <textarea name="deskripsi" id="m_deskripsi" rows="2" placeholder="Contoh: Kunjungan lapangan dan wawancara kepala sekolah." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Hasil Temuan <span class="text-red-500">*</span></label>
                <textarea name="hasil_temuan" id="m_hasil_temuan" rows="4" required class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Rekomendasi</label>
                <textarea name="rekomendasi" id="m_rekomendasi" rows="4" class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
            </div>
        </div>
        <div class="flex justify-end space-x-2 border-t pt-3">
            <button type="button" onclick="closeModal('modalMonev')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
        </div>
    </form>
</div>

<!-- Modal hapus -->
<div id="modalHapusMonev" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div class="mx-auto w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center"><i class="fa-solid fa-trash text-2xl"></i></div>
        <h3 class="font-bold text-slate-800 text-base">Hapus catatan monev?</h3>
        <form id="formHapusMonev" method="post" class="flex justify-center space-x-2">
            <?= csrf_field() ?>
            <button type="button" onclick="closeModal('modalHapusMonev')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    // Grafik karya tervalidasi per kabupaten/kota
    document.addEventListener('DOMContentLoaded', function() {
        const data = <?= json_encode($perKab) ?>;
        const label = Object.keys(data).map(k => k.replace('Kab. ', '').replace('Kota ', 'Kota '));
        new Chart(document.getElementById('grafikKab'), {
            type: 'bar',
            data: {
                labels: label,
                datasets: [{
                        label: 'Praktik Baik',
                        data: Object.values(data).map(v => v.praktik),
                        backgroundColor: '#10b981'
                    },
                    {
                        label: 'Bank Inovasi',
                        data: Object.values(data).map(v => v.inovasi),
                        backgroundColor: '#6366f1'
                    },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0
                        }
                    }
                },
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
            },
        });
    });

    function bukaFormMonev(d = null) {
        ['id_sekolah', 'tanggal_monev', 'aspek_monev', 'deskripsi', 'hasil_temuan', 'rekomendasi'].forEach(k => {
            document.getElementById('m_' + k).value = d?.[k] || '';
        });
        document.getElementById('m_id').value = d?.id_monev || '';
        document.getElementById('m_judul').innerText = d?.id_monev ? 'Edit Catatan Monev' : 'Tambah Catatan Monev';
        if (!d) document.getElementById('m_tanggal_monev').value = new Date().toISOString().slice(0, 10);
        openModal('modalMonev');
    }

    function hapusMonev(url) {
        document.getElementById('formHapusMonev').action = url;
        openModal('modalHapusMonev');
    }

    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', () => bukaFormMonev(<?= json_encode([
                                                                                'id_monev' => old('id_monev'),
                                                                                'id_sekolah' => old('id_sekolah'),
                                                                                'tanggal_monev' => old('tanggal_monev'),
                                                                                'aspek_monev' => old('aspek_monev'),
                                                                                'deskripsi' => old('deskripsi'),
                                                                                'hasil_temuan' => old('hasil_temuan'),
                                                                                'rekomendasi' => old('rekomendasi'),
                                                                            ]) ?>));
    <?php endif; ?>
</script>
<?= $this->endSection() ?>