<?php
/** @var array $indikator */
/** @var array $perKab */
/** @var array $jadwal */
/** @var string $filterKompetisi */
/** @var array $kompetisi */
/** @var array $peserta */
/** @var array $aspek */
/** @var array $labelStatus */
/** @var \CodeIgniter\Pager\Pager $pager */
/** @var int $perHalaman */
/** @var int $totalJadwal */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors = session()->getFlashdata('errors') ?? [];
$bulan  = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl    = fn ($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$n      = fn ($x) => number_format((float) $x, 0, ',', '.');
$hariIni = date('Y-m-d');

$kartu = [
    ['Partisipasi Sekolah', str_replace('.', ',', $indikator['partisipasi']) . '%', $indikator['sekolahIkut'] . ' dari ' . $indikator['sekolahAktif'] . ' sekolah aktif', 'fa-school', 'bg-blue-50 text-blue-600'],
    ['Praktik Baik Tervalidasi', $n($indikator['praktik']), $indikator['praktikTunggu'] . ' menunggu validasi', 'fa-book-open', 'bg-emerald-50 text-emerald-600'],
    ['Inovasi di Bank Inovasi', $n($indikator['inovasi']), 'Terverifikasi Dinas', 'fa-lightbulb', 'bg-indigo-50 text-indigo-600'],
    ['Juara & Apresiasi', $n($indikator['apresiasi']), 'Dari hasil kompetisi', 'fa-award', 'bg-amber-50 text-amber-600'],
    ['Peserta Kompetisi', $n($indikator['peserta']), 'Seluruh kompetisi', 'fa-trophy', 'bg-orange-50 text-orange-600'],
    ['SUARA Masuk', $n($indikator['suara']), 'Kritik, saran & aspirasi', 'fa-comments', 'bg-purple-50 text-purple-600'],
];

$badgeJuara = [1 => 'bg-amber-100 text-amber-700', 2 => 'bg-slate-200 text-slate-700', 3 => 'bg-orange-100 text-orange-700'];
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Monitoring & Evaluasi (Monev)</h2>
        <p class="text-xs text-slate-500">Indikator ekosistem inovasi dan jadwal monitoring tindak lanjut hasil kompetisi.</p>
    </div>

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

    <!-- Jadwal monitoring -->
    <div id="jadwal" class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">
        <div class="flex flex-wrap justify-between items-center gap-3">
            <div>
                <h3 class="text-sm font-bold text-slate-800">
                    <i class="fa-solid fa-calendar-check text-brand-600 mr-2"></i>Jadwal Monitoring
                    <span class="text-slate-400 font-normal">(<?= $totalJadwal ?>)</span>
                </h3>
                <p class="text-[11px] text-slate-400">Monitoring tindak lanjut untuk karya kompetisi yang dipilih Dinas.</p>
            </div>
            <div class="flex gap-2 text-xs">
                <?php if ($kompetisi) : ?>
                    <form method="get" action="<?= site_url('monev') ?>#jadwal">
                        <input type="hidden" name="per" value="<?= $perHalaman ?>">
                        <select name="kompetisi" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2">
                            <option value="">Semua kompetisi</option>
                            <?php foreach ($kompetisi as $k) : ?>
                                <option value="<?= $k['id_kompetisi'] ?>" <?= $filterKompetisi == $k['id_kompetisi'] ? 'selected' : '' ?>><?= esc($k['nama_kompetisi']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                    <button type="button" onclick="bukaFormJadwal()" class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center">
                        <i class="fa-solid fa-plus mr-2"></i>Buat Jadwal
                    </button>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($kompetisi)) : ?>
            <div class="bg-slate-50 border rounded-xl p-6 text-center text-xs text-slate-500">
                <i class="fa-solid fa-circle-info text-lg mb-2 block text-slate-400"></i>
                Jadwal monitoring bisa dibuat setelah ada kompetisi yang hasilnya sudah diumumkan di menu Apresiasi.
            </div>
        <?php elseif (empty($jadwal)) : ?>
            <div class="text-center py-8 text-xs text-slate-400">
                <i class="fa-regular fa-calendar text-3xl mb-2 block"></i>
                Belum ada jadwal monitoring. Klik "Buat Jadwal" untuk memilih karya yang perlu ditindaklanjuti.
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider text-[10px]">
                        <tr>
                            <th class="p-3 rounded-l-lg">Jadwal</th>
                            <th class="p-3">Karya & Sekolah</th>
                            <th class="p-3">Fokus Monitoring</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($jadwal as $j) : ?>
                            <?php
                            $lewat  = $j['status'] === 'dijadwalkan' && $j['tanggal_monev'] < $hariIni;
                            $hari   = $j['status'] === 'dijadwalkan' && $j['tanggal_monev'] === $hariIni;
                            ?>
                            <tr class="hover:bg-slate-50">
                                <td class="p-3 whitespace-nowrap">
                                    <div class="font-bold text-slate-800"><?= $tgl($j['tanggal_monev']) ?></div>
                                    <?php if ($hari) : ?>
                                        <span class="text-[10px] text-brand-700 font-bold">Hari ini</span>
                                    <?php elseif ($lewat) : ?>
                                        <span class="text-[10px] text-red-600 font-bold">Terlewat</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <div class="font-semibold text-slate-800">
                                        <?= esc($j['judul_karya']) ?>
                                        <?php if ($j['peringkat']) : ?>
                                            <span class="<?= $badgeJuara[$j['peringkat']] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2 py-0.5 rounded-full ml-1">Juara <?= $j['peringkat'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="text-[11px] text-slate-400">
                                        <?= esc($j['nama_sekolah'] ?? '-') ?> • <?= esc($j['nama_guru'] ?? '-') ?> • <?= esc($j['nama_kompetisi']) ?>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <div class="font-medium text-slate-700"><?= esc($j['aspek_monev']) ?></div>
                                    <?php if ($j['deskripsi']) : ?>
                                        <div class="text-[11px] text-slate-400 line-clamp-1"><?= esc($j['deskripsi']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <?php if ($j['status'] === 'selesai') : ?>
                                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-solid fa-check mr-1"></i>Selesai</span>
                                    <?php else : ?>
                                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap"><i class="fa-regular fa-clock mr-1"></i>Dijadwalkan</span>
                                    <?php endif; ?>
                                </td>
                                <td class="p-3">
                                    <div class="flex justify-center gap-1.5">
                                        <?php if ($j['status'] === 'dijadwalkan') : ?>
                                            <button type="button" title="Edit" data-jadwal="<?= esc(json_encode($j), 'attr') ?>"
                                                onclick="bukaFormJadwal(JSON.parse(this.dataset.jadwal))"
                                                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                                <i class="fa-solid fa-pen text-[11px]"></i>
                                            </button>
                                            <button type="button" title="Hapus" onclick="hapusJadwal('<?= site_url('monev/hapus/' . $j['id_monev']) ?>')"
                                                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-red-50 hover:text-red-600 flex items-center justify-center">
                                                <i class="fa-solid fa-trash text-[11px]"></i>
                                            </button>
                                        <?php else : ?>
                                            <span class="text-[11px] text-slate-400 italic">Hasil tersedia</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= view('admin/componen_be/pagination', ['pager' => $pager, 'grup' => 'jadwal', 'perHalaman' => $perHalaman, 'url' => site_url('monev'), 'tahan' => ['kompetisi' => $filterKompetisi], 'satuan' => 'jadwal', 'jangkar' => '#jadwal']) ?>
        <?php endif; ?>
    </div>
</section>

<!-- Modal form jadwal -->
<div id="modalJadwal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('monev/simpan') ?>" method="post" class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_monev" id="j_id">

        <div class="flex justify-between items-center border-b p-5">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-calendar-plus text-brand-600 mr-2"></i><span id="j_judul">Buat Jadwal Monitoring</span></h3>
            <button type="button" onclick="closeModal('modalJadwal')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            <?php if ($errors) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errors as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <!-- Pilih karya (hanya saat membuat baru) -->
            <div id="j_blokPeserta" class="space-y-2">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Kompetisi <span class="text-red-500">*</span></label>
                    <select id="j_kompetisi" onchange="tampilPeserta()" class="w-full border border-slate-300 rounded-lg p-2.5">
                        <?php foreach ($kompetisi as $k) : ?>
                            <option value="<?= $k['id_kompetisi'] ?>"><?= esc($k['nama_kompetisi']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="flex justify-between items-center">
                    <label class="font-semibold text-slate-700">Karya yang akan dimonitoring <span class="text-red-500">*</span></label>
                    <button type="button" onclick="pilihJuara()" class="text-[11px] text-brand-600 font-semibold hover:underline">
                        <i class="fa-solid fa-medal mr-0.5"></i>Pilih semua juara
                    </button>
                </div>
                <div id="j_daftarPeserta" class="border rounded-xl divide-y max-h-56 overflow-y-auto"></div>
            </div>

            <!-- Info karya (saat edit) -->
            <div id="j_blokEdit" class="hidden bg-slate-50 border rounded-xl p-3">
                <div class="text-[10px] text-slate-400 uppercase font-bold">Karya</div>
                <div id="j_infoKarya" class="font-semibold text-slate-800"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Tanggal Monitoring <span class="text-red-500">*</span></label>
                    <input type="date" name="tanggal_monev" id="j_tanggal" required class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Fokus Monitoring <span class="text-red-500">*</span></label>
                    <input type="text" name="aspek_monev" id="j_aspek" list="daftarAspek" required placeholder="Pilih atau ketik fokus" class="w-full border border-slate-300 rounded-lg p-2.5">
                    <datalist id="daftarAspek"><?php foreach ($aspek as $a) : ?><option value="<?= esc($a) ?>"><?php endforeach; ?></datalist>
                </div>
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Instruksi / Tujuan Monitoring</label>
                <textarea name="deskripsi" id="j_deskripsi" rows="3" placeholder="Contoh: Pastikan inovasi masih berjalan dan catat jumlah siswa yang terlibat." class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
            </div>
        </div>

        <div class="flex justify-between items-center border-t p-5 bg-slate-50 rounded-b-2xl">
            <span id="j_jumlah" class="text-slate-500"></span>
            <div class="flex space-x-2">
                <button type="button" onclick="closeModal('modalJadwal')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Jadwal</button>
            </div>
        </div>
    </form>
</div>

<!-- Modal hapus -->
<div id="modalHapusJadwal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div class="mx-auto w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center"><i class="fa-solid fa-trash text-2xl"></i></div>
        <h3 class="font-bold text-slate-800 text-base">Hapus jadwal monitoring?</h3>
        <form id="formHapusJadwal" method="post" class="flex justify-center space-x-2">
            <?= csrf_field() ?>
            <button type="button" onclick="closeModal('modalHapusJadwal')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    const PESERTA = <?= json_encode($peserta) ?>;

    // Grafik karya tervalidasi per kabupaten/kota
    document.addEventListener('DOMContentLoaded', function() {
        const data = <?= json_encode($perKab) ?>;
        new Chart(document.getElementById('grafikKab'), {
            type: 'bar',
            data: {
                labels: Object.keys(data).map(k => k.replace('Kab. ', '')),
                datasets: [
                    { label: 'Praktik Baik', data: Object.values(data).map(v => v.praktik), backgroundColor: '#10b981' },
                    { label: 'Bank Inovasi', data: Object.values(data).map(v => v.inovasi), backgroundColor: '#6366f1' },
                ],
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
                plugins: { legend: { position: 'bottom' } },
            },
        });
    });

    // Tampilkan daftar karya dari kompetisi yang dipilih
    function tampilPeserta() {
        const id = document.getElementById('j_kompetisi').value;
        const wadah = document.getElementById('j_daftarPeserta');
        wadah.innerHTML = '';

        const daftar = PESERTA[id] || [];
        if (daftar.length === 0) {
            wadah.innerHTML = '<p class="p-4 text-center text-slate-400">Tidak ada karya tervalidasi.</p>';
        }

        daftar.forEach(p => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-3 p-2.5 hover:bg-slate-50 cursor-pointer';

            const cek = document.createElement('input');
            cek.type = 'checkbox';
            cek.name = 'peserta[]';
            cek.value = p.id_peserta;
            cek.dataset.juara = p.peringkat ? '1' : '';
            cek.className = 'cek-peserta w-4 h-4 accent-brand-600';
            cek.onchange = hitungDipilih;

            const teks = document.createElement('div');
            teks.className = 'flex-1 min-w-0';
            const judul = document.createElement('div');
            judul.className = 'font-semibold text-slate-800 truncate';
            judul.textContent = p.judul_karya;
            const sub = document.createElement('div');
            sub.className = 'text-[11px] text-slate-400';
            sub.textContent = (p.nama_sekolah || '-') + (p.nilai ? ' • Nilai ' + parseFloat(p.nilai).toFixed(2).replace('.', ',') : '');
            teks.append(judul, sub);

            label.append(cek, teks);

            if (p.peringkat) {
                const badge = document.createElement('span');
                badge.className = 'text-[10px] font-bold px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 whitespace-nowrap';
                badge.textContent = 'Juara ' + p.peringkat;
                label.appendChild(badge);
            }
            wadah.appendChild(label);
        });
        hitungDipilih();
    }

    function pilihJuara() {
        document.querySelectorAll('.cek-peserta').forEach(c => { if (c.dataset.juara) c.checked = true; });
        hitungDipilih();
    }

    function hitungDipilih() {
        const n = document.querySelectorAll('.cek-peserta:checked').length;
        document.getElementById('j_jumlah').textContent = n ? n + ' karya dipilih' : '';
    }

    function bukaFormJadwal(d = null) {
        const edit = d && d.id_monev;

        document.getElementById('j_id').value = edit ? d.id_monev : '';
        document.getElementById('j_judul').innerText = edit ? 'Edit Jadwal Monitoring' : 'Buat Jadwal Monitoring';
        document.getElementById('j_tanggal').value = d?.tanggal_monev || '';
        document.getElementById('j_aspek').value = d?.aspek_monev || '';
        document.getElementById('j_deskripsi').value = d?.deskripsi || '';

        document.getElementById('j_blokPeserta').classList.toggle('hidden', !!edit);
        document.getElementById('j_blokEdit').classList.toggle('hidden', !edit);

        if (edit) {
            document.getElementById('j_infoKarya').textContent = d.judul_karya
                ? d.judul_karya + ' — ' + (d.nama_sekolah || '-')
                : 'Karya yang sedang diedit';
            document.getElementById('j_daftarPeserta').innerHTML = '';
            document.getElementById('j_jumlah').textContent = '';
        } else {
            tampilPeserta();
        }
        openModal('modalJadwal');
    }

    function hapusJadwal(url) {
        document.getElementById('formHapusJadwal').action = url;
        openModal('modalHapusJadwal');
    }

    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', () => bukaFormJadwal(<?= json_encode([
            'id_monev'      => old('id_monev'),
            'tanggal_monev' => old('tanggal_monev'),
            'aspek_monev'   => old('aspek_monev'),
            'deskripsi'     => old('deskripsi'),
        ]) ?>));
    <?php endif; ?>
</script>
<?= $this->endSection() ?>