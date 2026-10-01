<?php

/** @var array $praktik */
/** @var string $tab */
/** @var array $jumlah */
/** @var array $kategori */
/** @var array $jenisFile */
/** @var array $jenisTautan */
/** @var array $ekstensi */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';

$tahap = [
    'tunggu_sekolah' => ['Menunggu verifikasi sekolah', 'bg-amber-100 text-amber-800'],
    'tunggu_dinas'   => ['Menunggu validasi Dinas', 'bg-blue-100 text-blue-700'],
    'disetujui'      => ['Disetujui Dinas', 'bg-emerald-100 text-emerald-800'],
    'tolak_sekolah'  => ['Ditolak sekolah', 'bg-red-100 text-red-700'],
    'tolak_dinas'    => ['Ditolak Dinas', 'bg-red-100 text-red-700'],
];
$tabs = [
    ''          => ['Semua', 'fa-layer-group'],
    'diproses'  => ['Sedang Diproses', 'fa-hourglass-half'],
    'perbaikan' => ['Perlu Perbaikan', 'fa-rotate-left'],
    'disetujui' => ['Disetujui', 'fa-circle-check'],
];

// Status tiap langkah: selesai / sedang / gagal / belum
$langkah = function (string $t): array {
    $s2 = match ($t) {
        'tunggu_sekolah' => 'sedang',
        'tolak_sekolah'  => 'gagal',
        default          => 'selesai',
    };
    $s3 = match ($t) {
        'tunggu_dinas' => 'sedang',
        'tolak_dinas'  => 'gagal',
        'disetujui'    => 'selesai',
        default        => 'belum',
    };

    return [['Diajukan', 'selesai'], ['Verifikasi Sekolah', $s2], ['Validasi Dinas', $s3]];
};
$gayaLangkah = [
    'selesai' => ['bg-emerald-500 text-white', 'fa-check', 'text-emerald-700'],
    'sedang'  => ['bg-amber-400 text-white', 'fa-hourglass-half', 'text-amber-700 font-bold'],
    'gagal'   => ['bg-red-500 text-white', 'fa-xmark', 'text-red-600 font-bold'],
    'belum'   => ['bg-slate-100 text-slate-400', 'fa-circle', 'text-slate-400'],
];
?>
<section class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Praktik Baik Saya</h2>
            <p class="text-xs text-slate-500">Ajukan praktik baik Anda. Pengajuan diverifikasi sekolah, lalu divalidasi Dinas.</p>
        </div>
        <button type="button" onclick="bukaForm()"
            class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow flex items-center">
            <i class="fa-solid fa-plus mr-2"></i>Unggah Praktik Baik
        </button>
    </div>

    <?php if (session()->getFlashdata('sukses')) : ?>
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
            <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('gagal')) : ?>
        <div class="bg-red-50 border border-red-200 text-red-700 text-xs px-4 py-3 rounded-xl flex items-start">
            <i class="fa-solid fa-circle-exclamation mr-2 mt-0.5"></i><span><?= esc(session()->getFlashdata('gagal')) ?></span>
        </div>
    <?php endif; ?>

    <!-- Tab -->
    <div class="flex flex-wrap gap-2 text-xs font-semibold">
        <?php foreach ($tabs as $kunci => [$teks, $ikon]) : ?>
            <a href="<?= site_url('praktik-baik') . ($kunci !== '' ? '?tab=' . $kunci : '') ?>"
                class="px-4 py-2 rounded-xl border flex items-center gap-2 transition
                       <?= $tab === $kunci ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="fa-solid <?= $ikon ?>"></i><?= $teks ?>
                <span class="<?= $tab === $kunci ? 'bg-white/25' : ($kunci === 'perbaikan' && $jumlah[$kunci] > 0 ? 'bg-red-100 text-red-600' : 'bg-slate-100') ?> text-[10px] px-1.5 rounded-full"><?= $jumlah[$kunci] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($praktik)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-book-open text-2xl text-brand-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700"><?= $tab === '' ? 'Belum ada praktik baik' : 'Tidak ada data di tab ini' ?></p>
            <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Bagikan praktik baik yang Anda terapkan di kelas atau sekolah dengan tombol "Ajukan Praktik Baik".</p>
        </div>
    <?php else : ?>
        <div class="space-y-4">
            <?php foreach ($praktik as $p) : ?>
                <?php
                $t        = $p['tahap'];
                $ditolak  = in_array($t, ['tolak_sekolah', 'tolak_dinas'], true);
                $catatan  = $t === 'tolak_sekolah' ? $p['catatan_admin_sekolah'] : ($t === 'tolak_dinas' ? $p['catatan_petugas'] : null);
                $dataJson = esc(json_encode($p), 'attr');
                ?>
                <div class="bg-white rounded-2xl border <?= $ditolak ? 'border-red-200' : 'border-slate-200' ?> shadow-sm p-5 space-y-4 text-xs">

                    <div class="flex flex-wrap justify-between items-start gap-3">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="<?= $tahap[$t][1] ?> text-[10px] font-bold px-2.5 py-1 rounded-full"><?= $tahap[$t][0] ?></span>
                                <?php if ($p['kategori']) : ?>
                                    <span class="bg-slate-100 text-slate-600 text-[10px] font-semibold px-2.5 py-1 rounded-full"><?= esc($p['kategori']) ?></span>
                                <?php endif; ?>
                                <?php if ((int) $p['jumlah_revisi'] > 0) : ?>
                                    <span class="bg-violet-100 text-violet-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-code-branch mr-1"></i>Revisi ke-<?= (int) $p['jumlah_revisi'] ?></span>
                                <?php endif; ?>
                            </div>
                            <h3 class="font-bold text-slate-800 text-sm mt-2"><?= esc($p['judul']) ?></h3>
                            <p class="text-[11px] text-slate-400">Diajukan <?= $tgl($p['tanggal_upload']) ?> • <?= count($p['lampiran']) ?> lampiran</p>
                        </div>

                        <div class="flex gap-1.5 shrink-0">
                            <button type="button" title="Detail" data-p="<?= $dataJson ?>" onclick="lihatDetail(JSON.parse(this.dataset.p))"
                                class="h-8 px-3 rounded-lg border border-slate-200 hover:bg-slate-50 font-semibold text-slate-600 flex items-center">
                                <i class="fa-solid fa-eye mr-1.5"></i>Detail
                            </button>
                            <?php if ($p['bisa_ubah']) : ?>
                                <button type="button" data-p="<?= $dataJson ?>" onclick="bukaForm(JSON.parse(this.dataset.p))"
                                    class="h-8 px-3 rounded-lg font-semibold flex items-center <?= $ditolak ? 'bg-red-600 hover:bg-red-700 text-white' : 'border border-slate-200 hover:bg-brand-50 hover:text-brand-600 text-slate-600' ?>">
                                    <i class="fa-solid <?= $ditolak ? 'fa-screwdriver-wrench' : 'fa-pen' ?> mr-1.5"></i><?= $ditolak ? 'Perbaiki' : 'Edit' ?>
                                </button>
                                <button type="button" title="Hapus"
                                    onclick="konfirmasiHapus('<?= site_url('praktik-baik/hapus/' . $p['id_praktik_baik']) ?>', <?= esc(json_encode($p['judul']), 'attr') ?>)"
                                    class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-red-50 hover:text-red-600 flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-[11px]"></i>
                                </button>
                            <?php else : ?>
                                <span class="h-8 px-2 flex items-center text-[10px] text-slate-400" title="Tidak bisa diubah saat divalidasi atau setelah disetujui"><i class="fa-solid fa-lock mr-1"></i>Terkunci</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Progres alur -->
                    <div class="flex items-center max-w-md">
                        <?php $daftar = $langkah($t); ?>
                        <?php foreach ($daftar as $i => [$label, $st]) : ?>
                            <div class="flex flex-col items-center gap-1 w-20 shrink-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] <?= $gayaLangkah[$st][0] ?>">
                                    <i class="fa-solid <?= $gayaLangkah[$st][1] ?> <?= $st === 'belum' ? 'text-[6px]' : '' ?>"></i>
                                </div>
                                <span class="text-[9px] text-center leading-tight <?= $gayaLangkah[$st][2] ?>"><?= $label ?></span>
                            </div>
                            <?php if ($i < count($daftar) - 1) : ?>
                                <div class="flex-1 h-0.5 -mt-4 <?= $daftar[$i + 1][1] !== 'belum' ? 'bg-emerald-400' : 'bg-slate-200' ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Catatan penolakan -->
                    <?php if ($ditolak) : ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3.5 space-y-1">
                            <div class="font-bold text-red-700"><i class="fa-solid fa-comment-dots mr-1"></i>Catatan dari <?= $t === 'tolak_sekolah' ? 'Admin Sekolah' : 'Dinas' ?></div>
                            <p class="text-slate-700 whitespace-pre-line"><?= esc($catatan ?: '-') ?></p>
                            <p class="text-[11px] text-red-600 pt-1"><i class="fa-solid fa-arrow-right mr-1"></i>Klik <b>Perbaiki</b>, sesuaikan isinya, lalu kirim ulang.</p>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL FORM AJUKAN / EDIT / PERBAIKI
     ===================================================== -->
<div id="modalForm" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('praktik-baik/simpan') ?>" method="post" enctype="multipart/form-data"
        class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[92vh] flex flex-col text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_praktik_baik" id="f_id">

        <div class="flex justify-between items-center border-b p-5">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-book-open text-brand-600 mr-2"></i><span id="f_judulForm">Praktik Baik</span></h3>
            <button type="button" onclick="closeModal('modalForm')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            <!-- Catatan penolakan (mode perbaikan) -->
            <div id="f_catatanWrap" class="hidden bg-red-50 border border-red-200 rounded-xl p-3.5 space-y-1">
                <div class="font-bold text-red-700"><i class="fa-solid fa-comment-dots mr-1"></i><span id="f_catatanDari"></span></div>
                <p id="f_catatan" class="text-slate-700 whitespace-pre-line"></p>
            </div>

            <div>
                <label class="font-semibold block mb-1 text-slate-700">Judul Praktik Baik <span class="text-red-500">*</span></label>
                <input type="text" name="judul" id="f_judul" required maxlength="200" class="w-full border border-slate-300 rounded-lg p-2.5">
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Kategori <span class="text-red-500">*</span></label>
                <select name="kategori" id="f_kategori" required class="w-full border border-slate-300 rounded-lg p-2.5">
                    <option value="">Pilih kategori</option>
                    <?php foreach ($kategori as $k) : ?>
                        <option value="<?= esc($k) ?>"><?= esc($k) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Deskripsi <span class="text-red-500">*</span></label>
                <textarea name="deskripsi" id="f_deskripsi" rows="6" required minlength="30"
                    placeholder="Ceritakan latar belakang masalah, langkah yang dilakukan, dan hasil atau dampaknya bagi siswa..."
                    class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
            </div>

            <!-- Lampiran lama -->
            <div id="f_lamaWrap" class="hidden">
                <label class="font-semibold block mb-1 text-slate-700">Lampiran Tersimpan</label>
                <p class="text-[10px] text-slate-400 mb-2">Centang lampiran yang ingin dihapus.</p>
                <div id="f_lama" class="space-y-1.5"></div>
            </div>

            <!-- Lampiran baru -->
            <div class="border border-dashed border-slate-300 rounded-xl p-4 space-y-3">
                <div class="flex flex-wrap justify-between items-center gap-2">
                    <label class="font-semibold text-slate-700"><i class="fa-solid fa-paperclip mr-1"></i>Tambah Lampiran</label>
                    <div class="flex gap-2">
                        <button type="button" onclick="tambahBarisFile()" class="text-[11px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-upload mr-0.5"></i>File</button>
                        <button type="button" onclick="tambahBarisTautan()" class="text-[11px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-link mr-0.5"></i>Tautan</button>
                    </div>
                </div>
                <div id="f_barisFile" class="space-y-2"></div>
                <div id="f_barisTautan" class="space-y-2"></div>
                <p class="text-[10px] text-slate-400">File: <?= strtoupper(implode(', ', $ekstensi)) ?>, maksimal 5 MB per file. Tautan: video YouTube, Google Drive, atau berita publikasi.</p>
            </div>

            <!-- Tanggapan atas catatan (mode perbaikan) -->
            <div id="f_revisiWrap" class="hidden">
                <label class="font-semibold block mb-1 text-slate-700">Tanggapan atas Catatan <span class="text-red-500">*</span></label>
                <textarea name="catatan_revisi" id="f_revisi" rows="3"
                    placeholder="Jelaskan apa saja yang sudah Anda perbaiki, misalnya: menambahkan data hasil belajar dan foto kegiatan sesuai catatan."
                    class="w-full border border-violet-300 bg-violet-50/40 rounded-lg p-2.5"></textarea>
                <p class="text-[10px] text-slate-400 mt-1">Tanggapan ini dibaca Admin Sekolah dan Dinas saat memeriksa ulang.</p>
            </div>
        </div>

        <div class="flex justify-end space-x-2 border-t p-5 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('modalForm')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
            <button type="submit" id="f_tombol" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                <i class="fa-solid fa-paper-plane mr-1"></i> <span id="f_tombolTeks">Ajukan</span>
            </button>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL DETAIL
     ===================================================== -->
<div id="modalDetail" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <span id="d_status" class="text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                <h3 id="d_judul" class="font-bold text-slate-800 text-base mt-2 leading-snug"></h3>
                <p id="d_sub" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalDetail')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="overflow-y-auto p-5 space-y-5">
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-book-open text-brand-600 mr-1"></i> Deskripsi</h4>
                <div id="d_deskripsi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"></div>
            </div>
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-paperclip text-slate-500 mr-1"></i> Lampiran</h4>
                <div id="d_lampiran" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
            </div>
            <div id="d_revisiWrap" class="hidden">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-code-branch text-violet-500 mr-1"></i> Tanggapan Perbaikan Terakhir</h4>
                <div class="bg-violet-50 border border-violet-200 rounded-xl p-3.5">
                    <p id="d_revisi" class="text-slate-700 whitespace-pre-line"></p>
                    <p id="d_revisiInfo" class="text-[10px] text-violet-700 mt-1"></p>
                </div>
            </div>
            <div id="d_riwayat" class="space-y-2">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-clock-rotate-left text-slate-500 mr-1"></i> Hasil Pemeriksaan</h4>
                <div id="d_t1" class="border-l-4 rounded-r-xl p-3.5 space-y-1"></div>
                <div id="d_t2" class="border-l-4 rounded-r-xl p-3.5 space-y-1"></div>
            </div>
        </div>
        <div class="flex justify-end border-t p-4 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('modalDetail')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<!-- =====================================================
     MODAL KONFIRMASI HAPUS
     ===================================================== -->
<div id="modalHapus" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div class="mx-auto w-14 h-14 rounded-full flex items-center justify-center bg-red-100 text-red-600"><i class="fa-solid fa-trash text-2xl"></i></div>
        <div>
            <h3 class="font-bold text-slate-800 text-base">Hapus praktik baik?</h3>
            <p id="h_judul" class="text-xs text-slate-500 mt-1"></p>
            <p class="text-[11px] text-slate-400 mt-1">Semua lampiran ikut terhapus.</p>
        </div>
        <form id="h_form" method="post" class="flex justify-center space-x-2 pt-2">
            <?= csrf_field() ?>
            <button type="button" onclick="closeModal('modalHapus')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">Ya, Hapus</button>
        </form>
    </div>
</div>

<script>
    const JENIS_FILE = <?= json_encode($jenisFile) ?>;
    const JENIS_TAUTAN = <?= json_encode($jenisTautan) ?>;
    const LABEL_TAHAP = <?= json_encode(array_map(fn($x) => $x[0], $tahap)) ?>;
    const GAYA_TAHAP = <?= json_encode(array_map(fn($x) => $x[1], $tahap)) ?>;
    const TERIMA_FILE = '<?= implode(',', array_map(fn($e) => '.' . $e, $ekstensi)) ?>';

    const buat = (tag, kelas, teks) => {
        const el = document.createElement(tag);
        if (kelas) el.className = kelas;
        if (teks !== undefined) el.textContent = teks;
        return el;
    };
    const pilihan = (nama, opsi) => {
        const sel = buat('select', 'border border-slate-300 rounded-lg p-2 text-xs');
        sel.name = nama;
        Object.entries(opsi).forEach(([nilai, label]) => {
            const o = buat('option', '', label);
            o.value = nilai;
            sel.appendChild(o);
        });
        return sel;
    };
    const tombolHapusBaris = baris => {
        const b = buat('button', 'w-8 border border-slate-200 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 shrink-0');
        b.type = 'button';
        b.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        b.onclick = () => baris.remove();
        return b;
    };
    const formatTanggal = t => t ? new Date(t.replace(' ', 'T')).toLocaleDateString('id-ID', {
        day: '2-digit',
        month: 'long',
        year: 'numeric'
    }) : '-';

    // ---------- Baris lampiran ----------
    function tambahBarisFile() {
        const baris = buat('div', 'flex flex-wrap md:flex-nowrap gap-2');
        const file = buat('input', 'flex-1 min-w-0 border border-slate-300 rounded-lg p-1.5 text-xs file:mr-2 file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:rounded');
        file.type = 'file';
        file.name = 'file_lampiran[]';
        file.accept = TERIMA_FILE;
        const ket = buat('input', 'flex-1 min-w-0 border border-slate-300 rounded-lg p-2 text-xs');
        ket.name = 'keterangan_file[]';
        ket.placeholder = 'Keterangan (opsional)';
        baris.append(pilihan('jenis_file[]', JENIS_FILE), file, ket, tombolHapusBaris(baris));
        document.getElementById('f_barisFile').appendChild(baris);
    }

    function tambahBarisTautan() {
        const baris = buat('div', 'flex flex-wrap md:flex-nowrap gap-2');
        const url = buat('input', 'flex-1 min-w-0 border border-slate-300 rounded-lg p-2 text-xs');
        url.type = 'url';
        url.name = 'url_tautan[]';
        url.placeholder = 'https://...';
        const ket = buat('input', 'flex-1 min-w-0 border border-slate-300 rounded-lg p-2 text-xs');
        ket.name = 'keterangan_tautan[]';
        ket.placeholder = 'Keterangan (opsional)';
        baris.append(pilihan('jenis_tautan[]', JENIS_TAUTAN), url, ket, tombolHapusBaris(baris));
        document.getElementById('f_barisTautan').appendChild(baris);
    }

    // ---------- Form ajukan / edit / perbaiki ----------
    function bukaForm(d = null) {
        const edit = !!d;
        const revisi = edit && (d.tahap === 'tolak_sekolah' || d.tahap === 'tolak_dinas');

        document.getElementById('f_id').value = edit ? d.id_praktik_baik : '';
        document.getElementById('f_judul').value = d?.judul || '';
        document.getElementById('f_deskripsi').value = d?.deskripsi || '';

        // Kategori lama yang tidak ada di daftar tetap bisa dipilih
        const sel = document.getElementById('f_kategori');
        sel.querySelectorAll('option[data-lama]').forEach(o => o.remove());
        if (d?.kategori && ![...sel.options].some(o => o.value === d.kategori)) {
            const o = buat('option', '', d.kategori);
            o.value = d.kategori;
            o.dataset.lama = '1';
            sel.appendChild(o);
        }
        sel.value = d?.kategori || '';

        document.getElementById('f_judulForm').textContent = revisi ? 'Perbaiki & Kirim Ulang' : (edit ? 'Edit Praktik Baik' : 'Praktik Baik');
        document.getElementById('f_tombolTeks').textContent = revisi ? 'Kirim Ulang' : (edit ? 'Simpan Perubahan' : 'Ajukan');

        // Catatan penolakan & tanggapan
        document.getElementById('f_catatanWrap').classList.toggle('hidden', !revisi);
        document.getElementById('f_revisiWrap').classList.toggle('hidden', !revisi);
        document.getElementById('f_revisi').required = revisi;
        document.getElementById('f_revisi').value = '';
        if (revisi) {
            document.getElementById('f_catatanDari').textContent = 'Catatan dari ' + (d.tahap === 'tolak_sekolah' ? 'Admin Sekolah' : 'Dinas');
            document.getElementById('f_catatan').textContent = (d.tahap === 'tolak_sekolah' ? d.catatan_admin_sekolah : d.catatan_petugas) || '-';
        }

        // Lampiran lama
        const lama = document.getElementById('f_lama');
        lama.innerHTML = '';
        const adaLama = edit && d.lampiran && d.lampiran.length;
        document.getElementById('f_lamaWrap').classList.toggle('hidden', !adaLama);
        if (adaLama) {
            d.lampiran.forEach(f => {
                const label = buat('label', 'flex items-center gap-2 border rounded-lg p-2 cursor-pointer hover:bg-red-50');
                const cek = buat('input', 'accent-red-600');
                cek.type = 'checkbox';
                cek.name = 'hapus_lampiran[]';
                cek.value = f.id_dokumen;
                const nama = buat('span', 'flex-1 truncate text-slate-700', f.nama_file || 'Dokumen');
                const jenis = buat('span', 'text-[10px] text-slate-400 capitalize', (f.jenis_dokumen || '').replaceAll('_', ' '));
                label.append(cek, nama, jenis);
                lama.appendChild(label);
            });
        }

        document.getElementById('f_barisFile').innerHTML = '';
        document.getElementById('f_barisTautan').innerHTML = '';
        if (!edit) tambahBarisFile();

        openModal('modalForm');
    }

    // ---------- Detail ----------
    function kotak(id, warna, judul, isiBaris) {
        const el = document.getElementById(id);
        const gaya = {
            hijau: ['border-emerald-400 bg-emerald-50/50', 'text-emerald-800'],
            merah: ['border-red-400 bg-red-50/50', 'text-red-700'],
            kuning: ['border-amber-400 bg-amber-50/50', 'text-amber-800'],
            abu: ['border-slate-300 bg-slate-50', 'text-slate-500'],
        } [warna];
        el.className = 'border-l-4 rounded-r-xl p-3.5 space-y-1 ' + gaya[0];
        el.innerHTML = '';
        el.appendChild(buat('div', 'font-bold ' + gaya[1], judul));
        isiBaris.filter(b => b[1]).forEach(([label, nilai]) => {
            const baris = buat('div', 'flex gap-2');
            baris.append(buat('span', 'text-slate-400 w-20 shrink-0', label), buat('span', 'text-slate-700 whitespace-pre-line', nilai));
            el.appendChild(baris);
        });
    }

    function lihatDetail(d) {
        const badge = document.getElementById('d_status');
        badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full ' + GAYA_TAHAP[d.tahap];
        badge.textContent = LABEL_TAHAP[d.tahap];
        document.getElementById('d_judul').textContent = d.judul;
        document.getElementById('d_sub').textContent = (d.kategori || '-') + ' • diajukan ' + formatTanggal(d.tanggal_upload);
        document.getElementById('d_deskripsi').textContent = d.deskripsi || '-';

        // Lampiran
        const wadah = document.getElementById('d_lampiran');
        wadah.innerHTML = '';
        if (!d.lampiran || !d.lampiran.length) {
            wadah.appendChild(buat('p', 'text-slate-400 italic', 'Tidak ada lampiran.'));
        } else {
            const ikon = {
                foto: 'fa-image',
                video: 'fa-video',
                tautan_publikasi: 'fa-link',
                penghargaan: 'fa-award'
            };
            d.lampiran.forEach(f => {
                const a = buat(f.url ? 'a' : 'div', 'flex items-center gap-3 border rounded-lg p-2.5 hover:bg-slate-50');
                if (f.url) {
                    a.href = f.url;
                    a.target = '_blank';
                    a.rel = 'noopener';
                }
                a.appendChild(buat('i', 'fa-solid ' + (ikon[f.jenis_dokumen] || 'fa-file-lines') + ' text-slate-400 text-base w-5 text-center'));
                const teks = buat('div', 'min-w-0');
                teks.append(
                    buat('div', 'font-semibold text-slate-700 truncate', f.nama_file || 'Dokumen'),
                    buat('div', 'text-[10px] text-slate-400 capitalize', (f.jenis_dokumen || '').replaceAll('_', ' ') + (f.keterangan ? ' • ' + f.keterangan : ''))
                );
                a.appendChild(teks);
                wadah.appendChild(a);
            });
        }

        // Tanggapan perbaikan
        const adaRevisi = parseInt(d.jumlah_revisi) > 0 && d.catatan_revisi;
        document.getElementById('d_revisiWrap').classList.toggle('hidden', !adaRevisi);
        if (adaRevisi) {
            document.getElementById('d_revisi').textContent = d.catatan_revisi;
            document.getElementById('d_revisiInfo').textContent = 'Revisi ke-' + d.jumlah_revisi + ' • ' + formatTanggal(d.tanggal_revisi);
        }

        // Hasil pemeriksaan tahap 1 & 2
        const st1 = d.status_verifikasi_sekolah;
        if (st1 === 'menunggu') {
            kotak('d_t1', 'kuning', 'Tahap 1 — Menunggu verifikasi Admin Sekolah', []);
        } else {
            kotak('d_t1', st1 === 'disetujui' ? 'hijau' : 'merah', 'Tahap 1 — ' + (st1 === 'disetujui' ? 'Disetujui' : 'Ditolak') + ' Admin Sekolah', [
                ['Oleh', d.verifikator_sekolah],
                ['Tanggal', formatTanggal(d.tanggal_verifikasi_sekolah)],
                ['Catatan', d.catatan_admin_sekolah],
            ]);
        }

        const st2 = d.status_verifikasi_dinas;
        if (st1 !== 'disetujui') {
            kotak('d_t2', 'abu', 'Tahap 2 — Validasi Dinas (setelah lolos sekolah)', []);
        } else if (st2 === 'menunggu') {
            kotak('d_t2', 'kuning', 'Tahap 2 — Menunggu validasi Dinas', []);
        } else {
            kotak('d_t2', st2 === 'disetujui' ? 'hijau' : 'merah', 'Tahap 2 — ' + (st2 === 'disetujui' ? 'Disetujui' : 'Ditolak') + ' Dinas', [
                ['Oleh', d.verifikator_dinas],
                ['Tanggal', formatTanggal(d.tanggal_verifikasi_dinas)],
                ['Catatan', d.catatan_petugas],
            ]);
        }

        openModal('modalDetail');
    }

    function konfirmasiHapus(url, judul) {
        document.getElementById('h_form').action = url;
        document.getElementById('h_judul').textContent = judul;
        openModal('modalHapus');
    }
</script>
<?= $this->endSection() ?>