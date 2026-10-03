<?php
/** @var array $suara */
/** @var array $filter */
/** @var array $ringkas */
/** @var array $kategori */
/** @var array $status */
/** @var array $kabKota */
/** @var array $sekolah */
/** @var \CodeIgniter\Pager\Pager $pager */
/** @var int $perHalaman */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$gaya = [
    'saran'      => ['bg-blue-50 text-blue-700',     'fa-lightbulb',         'text-blue-600'],
    'keluhan'    => ['bg-red-50 text-red-700',       'fa-triangle-exclamation', 'text-red-600'],
    'pertanyaan' => ['bg-amber-50 text-amber-800',   'fa-circle-question',   'text-amber-600'],
    'lainnya'    => ['bg-slate-100 text-slate-600',  'fa-comment-dots',      'text-slate-500'],
];

$gayaStatus = [
    'belum_ditindak' => 'bg-red-50 text-red-600 border-red-200',
    'diproses'       => 'bg-amber-50 text-amber-700 border-amber-200',
    'selesai'        => 'bg-emerald-50 text-emerald-700 border-emerald-200',
];

$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn ($d) => date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y, H:i', strtotime($d));

$adaFilter = array_filter($filter);
?>
<section class="space-y-6">

    <!-- Judul -->
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Rekap SUARA</h2>
            <p class="text-xs text-slate-500">Saluran Umpan Balik & Aspirasi dari guru, sekolah, dan masyarakat se-Sulawesi Tenggara.</p>
        </div>
        <a href="<?= site_url('suara/export') . '?' . http_build_query($filter) ?>"
            class="bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-file-excel mr-2"></i>Unduh Excel
        </a>
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

    <?php if ($ringkas['belum'] > 0 && $filter['status'] !== 'belum_ditindak') : ?>
        <a href="<?= site_url('suara') . '?' . http_build_query(['status' => 'belum_ditindak'] + $filter) ?>"
            class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-center justify-between hover:bg-red-100 transition">
            <span><i class="fa-solid fa-bell mr-2"></i><b><?= $ringkas['belum'] ?> SUARA</b> belum dibalas.</span>
            <span class="font-semibold">Tampilkan <i class="fa-solid fa-arrow-right ml-1"></i></span>
        </a>
    <?php endif; ?>

    <!-- Ringkasan per kategori (klik untuk menyaring) -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <a href="<?= site_url('suara') ?>"
            class="bg-white p-4 rounded-xl border shadow-sm flex items-center gap-3 transition hover:shadow-md
                   <?= $filter['kategori'] === '' ? 'border-brand-500 ring-1 ring-brand-500' : 'border-slate-200' ?>">
            <div class="p-3 bg-purple-50 text-purple-600 rounded-xl"><i class="fa-solid fa-comments text-lg"></i></div>
            <div>
                <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider">Semua</div>
                <div class="text-xl font-bold text-slate-800"><?= number_format($ringkas['total'], 0, ',', '.') ?></div>
            </div>
        </a>
        <?php foreach ($kategori as $kunci => $teks) : ?>
            <a href="<?= site_url('suara') . '?' . http_build_query(['kategori' => $kunci] + $filter) ?>"
                class="bg-white p-4 rounded-xl border shadow-sm flex items-center gap-3 transition hover:shadow-md
                       <?= $filter['kategori'] === $kunci ? 'border-brand-500 ring-1 ring-brand-500' : 'border-slate-200' ?>">
                <div class="p-3 rounded-xl <?= $gaya[$kunci][0] ?>"><i class="fa-solid <?= $gaya[$kunci][1] ?> text-lg"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= esc($teks) ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= number_format($ringkas[$kunci], 0, ',', '.') ?></div>
                </div>
            </a>
        <?php endforeach; ?>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

        <!-- Filter -->
        <form method="get" action="<?= site_url('suara') ?>" class="grid grid-cols-1 md:grid-cols-6 gap-3 text-xs">
            <input type="hidden" name="kategori" value="<?= esc($filter['kategori']) ?>">
            <input type="hidden" name="per" value="<?= $perHalaman ?>">
            <input type="text" name="q" value="<?= esc($filter['q']) ?>" placeholder="Cari isi, nama pengirim, atau sekolah..."
                class="md:col-span-1 border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <select name="status" class="border border-slate-300 rounded-lg p-2.5">
                <option value="">Semua status</option>
                <?php foreach ($status as $kunci => $teks) : ?>
                    <option value="<?= $kunci ?>" <?= $filter['status'] === $kunci ? 'selected' : '' ?>><?= esc($teks) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="md:col-span-2 flex gap-2">
                <select name="sekolah" class="flex-1 min-w-0 border border-slate-300 rounded-lg p-2.5">
                    <option value="">Semua Sekolah</option>
                    <?php foreach ($sekolah as $s) : ?>
                        <option value="<?= $s['id_sekolah'] ?>" <?= $filter['sekolah'] == $s['id_sekolah'] ? 'selected' : '' ?>><?= esc($s['nama_sekolah']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" title="Terapkan filter" class="bg-slate-800 hover:bg-slate-700 text-white px-3 rounded-lg">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </button>
                <a href="<?= site_url('suara') ?>" title="Reset filter" class="border border-slate-300 hover:bg-slate-50 text-slate-600 px-3 rounded-lg flex items-center">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            </div>
        </form>


        <!-- Daftar -->
        <?php if (empty($suara)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-purple-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-regular fa-comments text-2xl text-purple-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700"><?= $adaFilter ? 'Tidak ada SUARA yang cocok' : 'Belum ada SUARA yang masuk' ?></p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">
                    <?= $adaFilter ? 'Coba ubah kata kunci atau filter.' : 'Kritik, saran, dan aspirasi dari sekolah akan tampil di sini.' ?>
                </p>
            </div>
        <?php else : ?>
            <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                <?php foreach ($suara as $d) : ?>
                    <?php $g = $gaya[$d['kategori']] ?? $gaya['lainnya']; ?>
                    <button type="button"
                        data-suara="<?= esc(json_encode($d), 'attr') ?>"
                        onclick="bukaSuara(JSON.parse(this.dataset.suara))"
                        class="w-full text-left p-4 hover:bg-slate-50 transition flex gap-4 items-start text-xs">

                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 <?= $g[0] ?>">
                            <i class="fa-solid <?= $g[1] ?>"></i>
                        </div>

                        <div class="flex-1 min-w-0 space-y-1">
                            <div class="flex flex-wrap items-center gap-x-2 gap-y-1">
                                <span class="font-bold text-slate-800"><?= esc($d['pengirim']) ?></span>
                                <span class="text-[10px] px-2 py-0.5 rounded-full <?= ['Guru' => 'bg-violet-50 text-violet-700', 'Admin Sekolah' => 'bg-blue-50 text-blue-700'][$d['jenis_pengirim']] ?? 'bg-slate-100 text-slate-500' ?>">
                                    <?= $d['jenis_pengirim'] ?>
                                </span>
                                <?php if ($d['nama_sekolah']) : ?>
                                    <span class="text-slate-400">•</span>
                                    <span class="text-slate-600"><?= esc($d['nama_sekolah']) ?></span>
                                <?php endif; ?>
                            </div>
                            <p class="text-slate-600 line-clamp-2 leading-relaxed"><?= esc($d['isi_suara']) ?></p>
                        </div>

                        <div class="text-right shrink-0 space-y-1.5">
                            <span class="inline-block text-[10px] font-bold px-2.5 py-1 rounded-full <?= $g[0] ?>">
                                <?= esc($kategori[$d['kategori']] ?? $d['kategori']) ?>
                            </span>
                            <div class="text-[10px] text-slate-400 whitespace-nowrap"><?= $tgl($d['tanggal_kirim']) ?></div>
                            <span class="inline-block border text-[10px] font-bold px-2 py-0.5 rounded-full <?= $gayaStatus[$d['status_tindak_lanjut']] ?? '' ?>">
                                <?= esc($status[$d['status_tindak_lanjut']] ?? '-') ?>
                            </span>
                        </div>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- Pagination -->
            <?php
            $grup    = 'suara';
            $halaman = $pager->getCurrentPage($grup);
            $jmlHal  = $pager->getPageCount($grup);
            $total   = $pager->getTotal($grup);
            $dari    = ($halaman - 1) * $perHalaman + 1;
            $sampai  = min($halaman * $perHalaman, $total);

            // Halaman pertama, terakhir, dan 2 di sekitar halaman aktif
            $nomor = [];
            for ($n = 1; $n <= $jmlHal; $n++) {
                if ($n === 1 || $n === $jmlHal || abs($n - $halaman) <= 2) {
                    $nomor[] = $n;
                }
            }
            $kelasTombol = 'min-w-[34px] h-[34px] px-2 rounded-lg border flex items-center justify-center font-semibold transition';
            ?>
            <div class="flex flex-wrap justify-between items-center gap-3 pt-2 text-xs">
                <div class="flex items-center gap-3 text-slate-500">
                    <span>Menampilkan <b class="text-slate-700"><?= number_format($dari, 0, ',', '.') ?>–<?= number_format($sampai, 0, ',', '.') ?></b> dari <b class="text-slate-700"><?= number_format($total, 0, ',', '.') ?></b> SUARA<?= $adaFilter ? ' sesuai filter' : '' ?></span>
                    <form method="get" action="<?= site_url('suara') ?>" class="flex items-center gap-1.5">
                        <?php foreach ($filter as $kunci => $nilai) : ?>
                            <?php if ($nilai !== '') : ?><input type="hidden" name="<?= $kunci ?>" value="<?= esc($nilai) ?>"><?php endif; ?>
                        <?php endforeach; ?>
                        <select name="per" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-1.5">
                            <?php foreach ([10, 25, 50] as $opsi) : ?>
                                <option value="<?= $opsi ?>" <?= $perHalaman === $opsi ? 'selected' : '' ?>><?= $opsi ?> / halaman</option>
                            <?php endforeach; ?>
                        </select>
                    </form>
                </div>

                <?php if ($jmlHal > 1) : ?>
                    <nav class="flex items-center gap-1" aria-label="Halaman">
                        <?php if ($halaman > 1) : ?>
                            <a href="<?= $pager->getPageURI($halaman - 1, $grup) ?>" class="<?= $kelasTombol ?> border-slate-200 text-slate-600 hover:bg-slate-50" title="Sebelumnya"><i class="fa-solid fa-chevron-left text-[10px]"></i></a>
                        <?php else : ?>
                            <span class="<?= $kelasTombol ?> border-slate-100 text-slate-300"><i class="fa-solid fa-chevron-left text-[10px]"></i></span>
                        <?php endif; ?>

                        <?php $sebelumnya = 0; ?>
                        <?php foreach ($nomor as $n) : ?>
                            <?php if ($n - $sebelumnya > 1) : ?><span class="px-1 text-slate-400">…</span><?php endif; ?>
                            <?php if ($n === $halaman) : ?>
                                <span class="<?= $kelasTombol ?> bg-brand-600 border-brand-600 text-white" aria-current="page"><?= $n ?></span>
                            <?php else : ?>
                                <a href="<?= $pager->getPageURI($n, $grup) ?>" class="<?= $kelasTombol ?> border-slate-200 text-slate-600 hover:bg-slate-50"><?= $n ?></a>
                            <?php endif; ?>
                            <?php $sebelumnya = $n; ?>
                        <?php endforeach; ?>

                        <?php if ($halaman < $jmlHal) : ?>
                            <a href="<?= $pager->getPageURI($halaman + 1, $grup) ?>" class="<?= $kelasTombol ?> border-slate-200 text-slate-600 hover:bg-slate-50" title="Berikutnya"><i class="fa-solid fa-chevron-right text-[10px]"></i></a>
                        <?php else : ?>
                            <span class="<?= $kelasTombol ?> border-slate-100 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
                        <?php endif; ?>
                    </nav>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- =====================================================
     MODAL DETAIL SUARA & BALAS
     ===================================================== -->
<div id="modalSuara" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <span id="s_kategori" class="text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                <p class="text-slate-400 mt-2"><i class="fa-regular fa-clock mr-1"></i><span id="s_tanggal"></span></p>
            </div>
            <button type="button" onclick="closeModal('modalSuara')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto p-5 space-y-5">
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-message text-purple-500 mr-1"></i> Isi SUARA
                </h4>
                <div id="s_isi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line text-[13px]"></div>
            </div>

            <div class="border rounded-xl p-4 space-y-1.5">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-user text-brand-600 mr-1"></i> Pengirim
                </h4>
                <div><span class="text-slate-400 w-24 inline-block">Nama</span> <b id="s_nama" class="text-slate-800"></b> <span id="s_jenis" class="text-[10px] px-2 py-0.5 rounded-full ml-1"></span></div>
                <div id="s_nip_wrap"><span class="text-slate-400 w-24 inline-block">NIP</span> <span id="s_nip"></span></div>
                <div><span class="text-slate-400 w-24 inline-block">Email</span> <span id="s_email"></span></div>
                <div><span class="text-slate-400 w-24 inline-block">Sekolah</span> <span id="s_sekolah"></span></div>
                <div><span class="text-slate-400 w-24 inline-block">Wilayah</span> <span id="s_kab"></span></div>
            </div>

            <!-- Tanggapan yang sudah dikirim -->
            <div id="s_balasanWrap" class="hidden">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-reply text-emerald-600 mr-1"></i> Tanggapan Dinas
                </h4>
                <div class="bg-emerald-50 border border-emerald-200 rounded-xl p-4 space-y-2">
                    <div id="s_balasan" class="text-slate-700 leading-relaxed whitespace-pre-line"></div>
                    <div class="text-[10px] text-emerald-700"><i class="fa-solid fa-user-pen mr-1"></i><span id="s_penindak"></span></div>
                </div>
            </div>
        </div>

        <!-- Form balas -->
        <form id="s_form" method="post" class="border-t p-5 space-y-3 bg-slate-50 rounded-b-2xl">
            <?= csrf_field() ?>
            <label class="font-semibold block text-slate-700"><span id="s_labelForm">Tulis Tanggapan</span></label>
            <textarea name="tanggapan" id="s_tanggapan" rows="3" maxlength="2000" required
                placeholder="Tuliskan jawaban atau tindak lanjut dari Dinas..."
                class="w-full border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none"></textarea>
            <div class="flex flex-wrap justify-between items-center gap-2">
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="status" value="diproses" id="s_stDiproses" class="accent-amber-500"> Sedang Diproses
                    </label>
                    <label class="flex items-center gap-1.5 cursor-pointer">
                        <input type="radio" name="status" value="selesai" id="s_stSelesai" class="accent-emerald-600"> Selesai
                    </label>
                </div>
                <div class="flex gap-2">
                    <button type="button" onclick="closeModal('modalSuara')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
                    <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                        <i class="fa-solid fa-paper-plane mr-1"></i> <span id="s_tombol">Kirim Tanggapan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const LABEL_KATEGORI = <?= json_encode($kategori) ?>;
    const LABEL_STATUS = <?= json_encode($status) ?>;
    const GAYA_KATEGORI  = <?= json_encode(array_map(fn ($g) => $g[0], $gaya)) ?>;

    function isi(id, teks) {
        document.getElementById(id).textContent = teks || '-';
    }

    function bukaSuara(d) {
        const badge = document.getElementById('s_kategori');
        badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full ' + (GAYA_KATEGORI[d.kategori] || '');
        badge.textContent = LABEL_KATEGORI[d.kategori] || d.kategori;

        const t = new Date(d.tanggal_kirim.replace(' ', 'T'));
        isi('s_tanggal', t.toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' }));

        isi('s_isi', d.isi_suara);
        isi('s_nama', d.pengirim);
        isi('s_email', d.email_pengirim);
        isi('s_sekolah', d.nama_sekolah);
        isi('s_kab', d.kabupaten_kota);

        const jenis = document.getElementById('s_jenis');
        jenis.textContent = d.jenis_pengirim;
        jenis.className = 'text-[10px] px-2 py-0.5 rounded-full ml-1 ' +
            ({ 'Guru': 'bg-violet-50 text-violet-700', 'Admin Sekolah': 'bg-blue-50 text-blue-700' }[d.jenis_pengirim] || 'bg-slate-100 text-slate-500');

        document.getElementById('s_nip_wrap').classList.toggle('hidden', !d.nip);
        isi('s_nip', d.nip);

        // Tanggapan yang sudah ada
        const sudah = !!d.tanggapan;
        document.getElementById('s_balasanWrap').classList.toggle('hidden', !sudah);
        if (sudah) {
            isi('s_balasan', d.tanggapan);
            const waktu = d.tanggal_tindak_lanjut
                ? new Date(d.tanggal_tindak_lanjut.replace(' ', 'T')).toLocaleString('id-ID', { day: '2-digit', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })
                : '';
            isi('s_penindak', (d.nama_penindak || 'Admin Dinas') + (waktu ? ' • ' + waktu : '') +
                ' • ' + (LABEL_STATUS[d.status_tindak_lanjut] || ''));
        }

        // Form: kosong untuk balasan baru, terisi untuk perbarui
        document.getElementById('s_form').action = '<?= site_url('suara/balas') ?>/' + d.id_suara + location.search;
        document.getElementById('s_tanggapan').value = d.tanggapan || '';
        document.getElementById('s_labelForm').textContent = sudah ? 'Perbarui Tanggapan' : 'Tulis Tanggapan';
        document.getElementById('s_tombol').textContent = sudah ? 'Perbarui' : 'Kirim Tanggapan';
        document.getElementById(d.status_tindak_lanjut === 'diproses' ? 's_stDiproses' : 's_stSelesai').checked = true;

        openModal('modalSuara');
    }
</script>
<?= $this->endSection() ?>