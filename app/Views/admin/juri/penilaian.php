<?php

/** @var array $kompetisi */
/** @var bool $adaTugas */
/** @var array|null $aktif */
/** @var array|null $kriteria */
/** @var array|null $kategoriLomba */
/** @var array|null $karya */
/** @var array|null $dipilih */
/** @var array|null $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$angka = fn($n) => number_format((float) $n, 2, ',', '.');
$keadaan = [
    'belum'    => ['Belum dinilai', 'bg-slate-100 text-slate-600', 'fa-circle'],
    'sebagian' => ['Belum lengkap', 'bg-amber-100 text-amber-800', 'fa-hourglass-half'],
    'lengkap'  => ['Sudah dinilai', 'bg-emerald-100 text-emerald-800', 'fa-circle-check'],
    'ditolak'  => ['Tidak memenuhi syarat', 'bg-red-100 text-red-700', 'fa-ban'],
];

// Ubah tautan YouTube menjadi alamat embed
$embedYoutube = function (?string $url): ?string {
    if (! $url || ! preg_match('#(?:youtu\.be/|youtube\.com/(?:watch\?v=|shorts/|embed/))([A-Za-z0-9_-]{11})#', $url, $m)) {
        return null;
    }

    return 'https://www.youtube.com/embed/' . $m[1];
};
?>
<section class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Penilaian Karya</h2>
            <p class="text-xs text-slate-500">Beri nilai 1–100 untuk setiap kriteria. Peringkat akhir dihitung dengan metode <b>SAW (Simple Additive Weighting)</b> dari rata-rata nilai seluruh juri.</p>
        </div>
        <?php if (count($kompetisi) > 1) : ?>
            <form method="get" action="<?= site_url('penilaian') ?>">
                <select name="kompetisi" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-2.5 text-xs">
                    <?php foreach ($kompetisi as $k) : ?>
                        <option value="<?= $k['id_kompetisi'] ?>" <?= (int) $k['id_kompetisi'] === (int) ($aktif['id_kompetisi'] ?? 0) ? 'selected' : '' ?>><?= esc($k['nama_kompetisi']) ?></option>
                    <?php endforeach; ?>
                </select>
            </form>
        <?php endif; ?>
    </div>

    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('gagal')) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-exclamation mr-2"></i><?= esc(session()->getFlashdata('gagal')) ?>
        </div>
    <?php endif; ?>

    <?php if (! $aktif) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-14 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-gavel text-2xl text-amber-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700"><?= $adaTugas ? 'Belum ada lomba dalam tahap penjurian' : 'Anda belum ditugaskan menilai' ?></p>
            <p class="text-xs text-slate-400 mt-1 max-w-[320px] leading-relaxed">
                <?= $adaTugas ? 'Karya akan muncul setelah Dinas memulai tahap penjurian.' : 'Dinas akan menentukan kategori lomba yang Anda nilai.' ?>
            </p>
        </div>
    <?php else : ?>

        <!-- Ringkasan progres -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200 shadow-sm text-xs space-y-3">
            <div class="flex flex-wrap justify-between items-center gap-2">
                <div class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-trophy text-amber-500 mr-2"></i><?= esc($aktif['nama_kompetisi']) ?></div>
                <div class="flex flex-wrap gap-2">
                    <?php foreach (['lengkap', 'sebagian', 'belum', 'ditolak'] as $st) : ?>
                        <span class="<?= $keadaan[$st][1] ?> text-[10px] font-bold px-2.5 py-1 rounded-full"><?= $keadaan[$st][0] ?>: <?= $ringkas[$st] ?></span>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php $persen = $ringkas['total'] ? (int) round(($ringkas['lengkap'] + $ringkas['ditolak']) / $ringkas['total'] * 100) : 0; ?>
            <div class="h-2.5 bg-slate-100 rounded-full overflow-hidden">
                <div class="h-full bg-amber-400 rounded-full" style="width: <?= $persen ?>%"></div>
            </div>
            <p class="text-[11px] text-slate-400"><?= $ringkas['lengkap'] + $ringkas['ditolak'] ?> dari <?= $ringkas['total'] ?> karya sudah diputuskan (<?= $persen ?>%).</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">

            <!-- ================= DAFTAR KARYA ================= -->
            <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200 shadow-sm p-4 space-y-3 text-xs self-start lg:sticky lg:top-28">
                <div class="flex flex-wrap gap-1.5">
                    <?php foreach (['' => 'Semua', 'belum' => 'Belum', 'sebagian' => 'Belum lengkap', 'lengkap' => 'Sudah', 'ditolak' => 'Ditolak'] as $kunci => $teks) : ?>
                        <button type="button" data-status="<?= $kunci ?>" onclick="saring(this)"
                            class="tombol-saring px-2.5 py-1 rounded-full border font-semibold <?= $kunci === '' ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600' ?>"><?= $teks ?></button>
                    <?php endforeach; ?>
                </div>
                <?php if (count($kategoriLomba) > 1) : ?>
                    <select id="saringKategori" onchange="saringUlang()" class="w-full border border-slate-300 rounded-lg p-2">
                        <option value="">Semua kategori tugas</option>
                        <?php foreach ($kategoriLomba as $kat) : ?>
                            <option value="<?= $kat['id_kategori'] ?>"><?= esc($kat['nama_kategori']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <div class="divide-y divide-slate-100 max-h-[60vh] overflow-y-auto -mx-1 px-1">
                    <?php if (empty($karya)) : ?>
                        <p class="text-slate-400 text-center py-6">Belum ada karya di kategori tugas Anda.</p>
                    <?php endif; ?>
                    <?php foreach ($karya as $k) : ?>
                        <?php $pilih = $dipilih && (int) $dipilih['id_peserta'] === (int) $k['id_peserta']; ?>
                        <a href="<?= site_url('penilaian') . '?kompetisi=' . $aktif['id_kompetisi'] . '&karya=' . $k['id_peserta'] ?>"
                            data-status="<?= $k['keadaan'] ?>" data-kategori="<?= $k['id_kategori'] ?>"
                            class="baris-karya flex items-center gap-3 py-3 px-2 rounded-lg transition <?= $pilih ? 'bg-amber-50 ring-1 ring-amber-300' : 'hover:bg-slate-50' ?>">
                            <span class="font-mono text-[10px] font-bold text-slate-500 bg-slate-100 px-1.5 py-0.5 rounded shrink-0"><?= $k['kode'] ?></span>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-800 truncate"><?= esc($k['judul_karya']) ?></div>
                                <div class="text-[10px] text-slate-400 truncate"><?= esc($k['nama_kategori']) ?></div>
                            </div>
                            <?php if ($k['keadaan'] === 'lengkap') : ?>
                                <span class="font-extrabold text-emerald-700 shrink-0"><?= $angka($k['nilai_bobot']) ?></span>
                            <?php else : ?>
                                <i class="fa-solid <?= $keadaan[$k['keadaan']][2] ?> shrink-0 <?= ['belum' => 'text-slate-300 text-[8px]', 'sebagian' => 'text-amber-500', 'ditolak' => 'text-red-500'][$k['keadaan']] ?? '' ?>"></i>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- ================= PANEL PENILAIAN ================= -->
            <div class="lg:col-span-3 space-y-5">
                <?php if (! $dipilih) : ?>
                    <div class="bg-white rounded-2xl border-2 border-dashed border-slate-200 flex flex-col items-center justify-center text-center py-16 px-4 text-xs">
                        <i class="fa-solid fa-hand-pointer text-3xl text-slate-300 mb-3"></i>
                        <p class="font-semibold text-slate-600">Pilih karya dari daftar untuk mulai menilai</p>
                        <p class="text-slate-400 mt-1">Identitas sekolah dan guru disembunyikan agar penilaian tetap objektif.</p>
                    </div>
                <?php else : ?>
                    <?php $d = $dipilih;
                    $video = $embedYoutube($d['link_video']); ?>

                    <!-- Karya -->
                    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <div class="flex flex-wrap gap-2">
                                    <span class="font-mono bg-slate-800 text-white text-[10px] font-bold px-2 py-0.5 rounded"><?= $d['kode'] ?></span>
                                    <span class="bg-indigo-50 text-indigo-700 text-[10px] font-bold px-2.5 py-0.5 rounded-full"><i class="fa-solid fa-tag mr-1"></i><?= esc($d['nama_kategori']) ?></span>
                                    <span class="<?= $keadaan[$d['keadaan']][1] ?> text-[10px] font-bold px-2.5 py-0.5 rounded-full"><?= $keadaan[$d['keadaan']][0] ?></span>
                                </div>
                                <h3 class="font-bold text-slate-800 text-base mt-2 leading-snug"><?= esc($d['judul_karya']) ?></h3>
                            </div>
                        </div>

                        <?php if ($video) : ?>
                            <div class="aspect-video rounded-xl overflow-hidden bg-slate-900">
                                <iframe src="<?= esc($video, 'attr') ?>" class="w-full h-full" title="Video karya" allowfullscreen
                                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                            </div>
                        <?php elseif (preg_match('#^https?://#i', (string) $d['link_video'])) : ?>
                            <a href="<?= esc($d['link_video'], 'attr') ?>" target="_blank" rel="noopener"
                                class="inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3.5 py-2 rounded-lg">
                                <i class="fa-solid fa-play mr-1.5"></i>Buka Video Karya
                            </a>
                        <?php else : ?>
                            <p class="text-red-600 bg-red-50 border border-red-200 rounded-lg p-2.5"><i class="fa-solid fa-video-slash mr-1"></i>Peserta tidak menyertakan link video yang valid.</p>
                        <?php endif; ?>

                        <div>
                            <div class="text-[10px] text-slate-500 uppercase font-bold mb-1">Deskripsi Karya</div>
                            <div class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"><?= esc($d['deskripsi_karya'] ?: '-') ?></div>
                        </div>
                    </div>

                    <?php if ($d['keadaan'] === 'ditolak') : ?>
                        <!-- Sudah ditolak -->
                        <div class="bg-red-50 border-2 border-red-200 rounded-2xl p-5 text-xs space-y-2">
                            <div class="font-bold text-red-700 text-sm"><i class="fa-solid fa-ban mr-2"></i>Karya tidak memenuhi syarat</div>
                            <p class="text-slate-700 whitespace-pre-line"><?= esc($d['catatan_juri'] ?: '-') ?></p>
                            <?php if ($d['tolak_saya']) : ?>
                                <form action="<?= site_url('penilaian/batal-tolak/' . $d['id_peserta']) ?>" method="post" onsubmit="return confirm('Batalkan penolakan karya ini?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" class="mt-2 border border-red-300 bg-white hover:bg-red-100 text-red-700 font-semibold px-3.5 py-2 rounded-lg">
                                        <i class="fa-solid fa-rotate-left mr-1.5"></i>Batalkan Penolakan
                                    </button>
                                </form>
                            <?php else : ?>
                                <p class="text-[11px] text-red-600">Ditandai oleh juri lain. Hanya juri tersebut yang dapat membatalkannya.</p>
                            <?php endif; ?>
                        </div>
                    <?php else : ?>
                        <!-- Form nilai -->
                        <form action="<?= site_url('penilaian/simpan/' . $d['id_peserta']) ?>" method="post"
                            class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs">
                            <?= csrf_field() ?>
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <h4 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-pen-to-square text-amber-500 mr-2"></i>Nilai per Kriteria</h4>
                                <span class="text-[10px] text-slate-400">Skala 1–100 • 90+ sangat baik, 75–89 baik, 60–74 cukup, &lt;60 kurang</span>
                            </div>

                            <?php foreach ($kriteria as $kr) : ?>
                                <?php $isi = $d['nilai'][$kr['id_kriteria']] ?? ''; ?>
                                <div class="border rounded-xl p-3.5 space-y-2">
                                    <div class="flex justify-between items-start gap-3">
                                        <div class="min-w-0">
                                            <div class="font-semibold text-slate-800"><?= esc($kr['nama_kriteria']) ?></div>
                                            <?php if ($kr['keterangan']) : ?><div class="text-[11px] text-slate-400"><?= esc($kr['keterangan']) ?></div><?php endif; ?>
                                        </div>
                                        <span class="bg-amber-50 text-amber-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Bobot <?= (int) $kr['skor_maks'] ?>%</span>
                                    </div>
                                    <div class="flex items-center gap-3">
                                        <input type="range" min="1" max="100" step="1" value="<?= $isi !== '' ? (int) round($isi) : 50 ?>"
                                            data-untuk="skor_<?= $kr['id_kriteria'] ?>" oninput="geserNilai(this)"
                                            class="flex-1 accent-amber-500 <?= $isi === '' ? 'opacity-40' : '' ?>">
                                        <input type="number" name="skor[<?= $kr['id_kriteria'] ?>]" id="skor_<?= $kr['id_kriteria'] ?>"
                                            min="1" max="100" step="0.01" value="<?= $isi !== '' ? esc((string) $isi) : '' ?>" placeholder="–"
                                            data-bobot="<?= (float) $kr['skor_maks'] ?>" oninput="ketikNilai(this)"
                                            class="input-skor w-20 border border-slate-300 rounded-lg p-2 text-center font-bold text-sm">
                                    </div>
                                </div>
                            <?php endforeach; ?>

                            <div class="bg-slate-900 text-white rounded-xl p-4 flex flex-wrap justify-between items-center gap-3">
                                <div>
                                    <div class="text-[10px] uppercase tracking-wider text-slate-300">Nilai terbobot dari Anda</div>
                                    <div><span id="pratinjau" class="text-3xl font-extrabold text-amber-400">–</span><span class="text-slate-400"> / 100</span></div>
                                    <div id="keteranganIsi" class="text-[10px] text-slate-400"></div>
                                </div>
                                <button type="submit" class="bg-amber-400 hover:bg-amber-500 text-slate-900 font-bold px-5 py-2.5 rounded-xl">
                                    <i class="fa-solid fa-floppy-disk mr-1.5"></i>Simpan Nilai
                                </button>
                            </div>
                            <p class="text-[10px] text-slate-400">Nilai boleh disimpan sebagian dan dilengkapi nanti. Karya dianggap <b>memenuhi syarat</b> setelah semua kriteria diisi. Nilai akhir resmi dihitung dengan SAW setelah semua juri selesai menilai.</p>
                        </form>

                        <!-- Tidak memenuhi syarat -->
                        <details class="bg-white rounded-2xl border border-red-200 shadow-sm text-xs group">
                            <summary class="p-4 cursor-pointer list-none flex items-center justify-between font-semibold text-red-700">
                                <span><i class="fa-solid fa-ban mr-2"></i>Karya tidak memenuhi syarat?</span>
                                <i class="fa-solid fa-chevron-down transition group-open:rotate-180"></i>
                            </summary>
                            <form action="<?= site_url('penilaian/tolak/' . $d['id_peserta']) ?>" method="post" class="px-4 pb-4 space-y-3"
                                onsubmit="return confirm('Tandai karya ini tidak memenuhi syarat? Karya tidak akan ikut perhitungan juara.')">
                                <?= csrf_field() ?>
                                <p class="text-slate-500">Gunakan bila karya melanggar ketentuan lomba, misalnya video tidak dapat diputar, durasi melebihi batas, atau karya bukan inovasi asli.</p>
                                <textarea name="catatan_juri" rows="3" required minlength="10"
                                    placeholder="Tuliskan alasannya. Catatan ini dapat dibaca guru, sekolah, dan Dinas."
                                    class="w-full border border-red-200 rounded-lg p-2.5"></textarea>
                                <div class="flex justify-end">
                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-semibold px-4 py-2 rounded-lg"><i class="fa-solid fa-ban mr-1.5"></i>Tandai Tidak Memenuhi Syarat</button>
                                </div>
                            </form>
                        </details>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<script>
    // ---------- Saring daftar karya ----------
    let statusAktif = '';

    function saring(tombol) {
        statusAktif = tombol.dataset.status;
        document.querySelectorAll('.tombol-saring').forEach(b => {
            const aktif = b === tombol;
            b.className = 'tombol-saring px-2.5 py-1 rounded-full border font-semibold ' + (aktif ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600');
        });
        saringUlang();
    }

    function saringUlang() {
        const kat = document.getElementById('saringKategori')?.value || '';
        document.querySelectorAll('.baris-karya').forEach(a => {
            const cocok = (!statusAktif || a.dataset.status === statusAktif) && (!kat || a.dataset.kategori === kat);
            a.classList.toggle('hidden', !cocok);
        });
    }

    // ---------- Slider & kotak angka saling mengikuti ----------
    function geserNilai(slider) {
        const input = document.getElementById(slider.dataset.untuk);
        input.value = slider.value;
        slider.classList.remove('opacity-40');
        hitungPratinjau();
    }

    function ketikNilai(input) {
        const slider = document.querySelector('[data-untuk="' + input.id + '"]');
        const v = parseFloat(input.value);
        if (slider && !isNaN(v)) {
            slider.value = Math.min(100, Math.max(1, Math.round(v)));
            slider.classList.remove('opacity-40');
        }
        hitungPratinjau();
    }

    // Pratinjau nilai terbobot dari juri ini: Σ nilai × bobot / 100
    function hitungPratinjau() {
        const semua = [...document.querySelectorAll('.input-skor')];
        if (!semua.length) return;
        let total = 0,
            terisi = 0;
        semua.forEach(i => {
            const v = parseFloat(i.value);
            if (!isNaN(v)) {
                total += v * parseFloat(i.dataset.bobot) / 100;
                terisi++;
            }
        });
        document.getElementById('pratinjau').textContent = terisi ? total.toFixed(2).replace('.', ',') : '–';
        document.getElementById('keteranganIsi').textContent = terisi + ' dari ' + semua.length + ' kriteria terisi';
    }
    document.addEventListener('DOMContentLoaded', hitungPratinjau);
</script>
<?= $this->endSection() ?>