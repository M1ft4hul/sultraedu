<?php
/** @var array $kompetisi */
/** @var array $label */
/** @var array $kategoriBawaan */
/** @var array $kriteriaBawaan */
/** @var string $tab */
/** @var array $jumlahTab */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors = session()->getFlashdata('errors') ?? [];
$urutan = array_keys($label);

$badge = [
    'draft'       => 'bg-slate-100 text-slate-600',
    'pendaftaran' => 'bg-blue-100 text-blue-700',
    'berlangsung' => 'bg-amber-100 text-amber-800',
    'selesai'     => 'bg-emerald-100 text-emerald-800',
];

// Teks tombol untuk maju satu tahap
$tombolMaju = [
    'draft'       => ['Buka Pendaftaran', 'fa-door-open'],
    'pendaftaran' => ['Mulai Penjurian', 'fa-gavel'],
    'berlangsung' => ['Selesaikan Kompetisi', 'fa-flag-checkered'],
];

$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn ($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
?>
<section class="space-y-6">

    <!-- Judul -->
    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Kelola Kompetisi Inovasi</h2>
            <p class="text-xs text-slate-500">Buat jadwal lomba, atur kategori & kriteria penilaian, dan kendalikan tahapan kompetisi.</p>
        </div>
        <button type="button" onclick="bukaFormKompetisi()"
            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow transition flex items-center">
            <i class="fa-solid fa-plus mr-2"></i>Buat Kompetisi
        </button>
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
    <?php if (session()->getFlashdata('peringatan')) : ?>
        <div class="bg-amber-50 border border-amber-300 text-amber-900 text-xs px-4 py-3 rounded-xl flex items-start justify-between gap-3">
            <span><i class="fa-solid fa-triangle-exclamation mr-2"></i><?= esc(session()->getFlashdata('peringatan')) ?></span>
            <a href="<?= site_url('juri') ?>" class="shrink-0 font-bold hover:underline">Atur Juri <i class="fa-solid fa-arrow-right ml-1"></i></a>
        </div>
    <?php endif; ?>

    <!-- Tab -->
    <div class="flex flex-wrap gap-2 text-xs font-semibold">
        <?php foreach (['aktif' => ['Belum Selesai', 'fa-bolt'], 'selesai' => ['Selesai', 'fa-flag-checkered']] as $kunci => [$teks, $ikonTab]) : ?>
            <a href="<?= site_url('kompetisi') . ($kunci === 'selesai' ? '?tab=selesai' : '') ?>"
                class="px-4 py-2 rounded-xl border flex items-center gap-2 transition
                       <?= $tab === $kunci ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="fa-solid <?= $ikonTab ?>"></i><?= $teks ?>
                <span class="<?= $tab === $kunci ? 'bg-white/25' : 'bg-slate-100' ?> text-[10px] px-1.5 rounded-full"><?= $jumlahTab[$kunci] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if (empty($kompetisi)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-2xl text-amber-500"></i>
                </div>
            </div>
            <?php if ($tab === 'aktif') : ?>
                <p class="text-sm font-semibold text-slate-700">Tidak ada kompetisi yang sedang berjalan</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Buat kompetisi baru dengan tombol "Buat Kompetisi" di atas. Kompetisi yang sudah selesai ada di tab Selesai.</p>
            <?php else : ?>
                <p class="text-sm font-semibold text-slate-700">Belum ada kompetisi yang selesai</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Kompetisi yang sudah melewati tahap penjurian akan tersimpan di sini.</p>
            <?php endif; ?>
        </div>
    <?php else : ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <?php foreach ($kompetisi as $k) : ?>
                <?php
                $st     = $k['status'];
                $pos    = array_search($st, $urutan, true);
                $maju   = $urutan[$pos + 1] ?? null;
                $mundur = $urutan[$pos - 1] ?? null;
                ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden flex flex-col">
                    <?php if (! empty($k['banner'])) : ?>
                        <img src="<?= base_url($k['banner']) ?>" alt="Banner <?= esc($k['nama_kompetisi'], 'attr') ?>" class="w-full aspect-video object-cover border-b">
                    <?php endif; ?>
                <div class="p-5 flex flex-col gap-4 flex-1">

                    <!-- Kepala kartu -->
                    <div class="flex justify-between items-start gap-3">
                        <div class="min-w-0">
                            <span class="<?= $badge[$st] ?? '' ?> text-[10px] font-bold px-2.5 py-1 rounded-full"><?= esc($label[$st] ?? $st) ?></span>
                            <h3 class="font-bold text-slate-800 text-base mt-2 leading-snug"><?= esc($k['nama_kompetisi']) ?></h3>
                            <p class="text-[11px] text-slate-500 mt-0.5">
                                <i class="fa-regular fa-calendar mr-1"></i><?= $tgl($k['tanggal_mulai']) ?> – <?= $tgl($k['tanggal_selesai']) ?>
                            </p>
                        </div>
                        <div class="flex gap-1.5 shrink-0">
                            <button type="button" title="Edit"
                                data-kompetisi="<?= esc(json_encode($k), 'attr') ?>"
                                onclick="bukaFormKompetisi(JSON.parse(this.dataset.kompetisi))"
                                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                                <i class="fa-solid fa-pen text-[11px]"></i>
                            </button>
                            <?php if ($k['jumlah_peserta'] === 0) : ?>
                                <button type="button" title="Hapus"
                                    onclick="konfirmasiAksi('<?= site_url('kompetisi/hapus/' . $k['id_kompetisi']) ?>', 'Hapus kompetisi?', <?= esc(json_encode($k['nama_kompetisi']), 'attr') ?>, 'red')"
                                    class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-red-50 hover:text-red-600 flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-[11px]"></i>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($k['deskripsi']) : ?>
                        <p class="text-xs text-slate-600 line-clamp-2"><?= esc($k['deskripsi']) ?></p>
                    <?php endif; ?>

                    <!-- Penanda tahapan -->
                    <div class="flex items-center">
                        <?php foreach ($urutan as $i => $tahap) : ?>
                            <?php $lewat = $i <= $pos; ?>
                            <div class="flex flex-col items-center gap-1 w-16 shrink-0">
                                <div class="w-6 h-6 rounded-full flex items-center justify-center text-[10px] font-bold
                                            <?= $lewat ? 'bg-brand-600 text-white' : 'bg-slate-100 text-slate-400' ?>">
                                    <?= $i < $pos ? '<i class="fa-solid fa-check"></i>' : $i + 1 ?>
                                </div>
                                <span class="text-[9px] text-center leading-tight <?= $i === $pos ? 'font-bold text-brand-700' : 'text-slate-400' ?>">
                                    <?= esc($label[$tahap]) ?>
                                </span>
                            </div>
                            <?php if ($i < count($urutan) - 1) : ?>
                                <div class="flex-1 h-0.5 -mt-4 <?= $i < $pos ? 'bg-brand-600' : 'bg-slate-200' ?>"></div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>

                    <!-- Ringkasan -->
                    <div class="grid grid-cols-3 gap-2 text-center">
                        <div class="bg-slate-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-slate-800"><?= $k['jumlah_peserta'] ?></div>
                            <div class="text-[10px] text-slate-500">Peserta</div>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-slate-800"><?= count($k['kategori']) ?></div>
                            <div class="text-[10px] text-slate-500">Kategori</div>
                        </div>
                        <div class="bg-slate-50 rounded-lg p-2.5">
                            <div class="text-lg font-bold text-slate-800"><?= count($k['kriteria']) ?></div>
                            <div class="text-[10px] text-slate-500">Kriteria</div>
                        </div>
                    </div>

                    <!-- Lihat peserta / juara -->
                    <?php if ($k['jumlah_peserta'] > 0) : ?>
                        <button type="button" data-kompetisi="<?= esc(json_encode($k), 'attr') ?>"
                            onclick="lihatPeserta(JSON.parse(this.dataset.kompetisi))"
                            class="w-full text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center justify-center border transition
                                   <?= $k['hasil_diumumkan'] ? 'bg-amber-50 border-amber-200 text-amber-700 hover:bg-amber-100' : 'bg-slate-50 border-slate-200 text-slate-700 hover:bg-slate-100' ?>">
                            <i class="fa-solid <?= $k['hasil_diumumkan'] ? 'fa-trophy' : 'fa-users' ?> mr-2"></i>
                            <?= $k['hasil_diumumkan'] ? 'Lihat Juara & Peserta' : 'Lihat Peserta & Karya' ?>
                        </button>
                    <?php endif; ?>

                    <!-- Bagikan ke WhatsApp -->
                    <?php if ($st !== 'draft') : ?>
                        <button type="button" data-kompetisi="<?= esc(json_encode([
                            'id_kompetisi'    => $k['id_kompetisi'],
                            'nama_kompetisi'  => $k['nama_kompetisi'],
                            'deskripsi'       => $k['deskripsi'],
                            'tanggal_mulai'   => $k['tanggal_mulai'],
                            'tanggal_selesai' => $k['tanggal_selesai'],
                            'status'          => $st,
                            'hasil_diumumkan' => $k['hasil_diumumkan'],
                            'banner'          => $k['banner'] ?? null,
                            'kategori'        => array_map(fn ($x) => $x['nama_kategori'] ?? $x, $k['kategori']),
                        ]), 'attr') ?>" onclick="bukaBagikan(JSON.parse(this.dataset.kompetisi))"
                            class="w-full text-xs font-semibold px-4 py-2.5 rounded-lg flex items-center justify-center bg-green-600 hover:bg-green-700 text-white transition">
                            <i class="fa-brands fa-whatsapp text-base mr-2"></i>Bagikan ke WhatsApp
                        </button>
                    <?php endif; ?>

                    <!-- Tombol tahapan -->
                    <div class="flex flex-wrap justify-between items-center gap-2 pt-3 border-t mt-auto">
                        <div>
                            <?php if ($mundur) : ?>
                                <button type="button"
                                    onclick="konfirmasiTahap('<?= site_url('kompetisi/status/' . $k['id_kompetisi']) ?>', '<?= $mundur ?>', 'Kembalikan tahap?', <?= esc(json_encode('Tahap akan dikembalikan ke: ' . $label[$mundur]), 'attr') ?>, 'amber')"
                                    class="text-[11px] text-slate-500 hover:text-slate-700 font-semibold">
                                    <i class="fa-solid fa-rotate-left mr-1"></i>Kembali ke <?= esc($label[$mundur]) ?>
                                </button>
                            <?php endif; ?>
                        </div>
                        <?php if ($maju) : ?>
                            <button type="button"
                                onclick="konfirmasiTahap('<?= site_url('kompetisi/status/' . $k['id_kompetisi']) ?>', '<?= $maju ?>', <?= esc(json_encode($tombolMaju[$st][0] . '?'), 'attr') ?>, <?= esc(json_encode($k['nama_kompetisi']), 'attr') ?>, 'brand')"
                                class="bg-brand-600 hover:bg-brand-700 text-white text-xs font-semibold px-4 py-2 rounded-lg flex items-center">
                                <i class="fa-solid <?= $tombolMaju[$st][1] ?> mr-2"></i><?= $tombolMaju[$st][0] ?>
                            </button>
                        <?php else : ?>
                            <span class="text-[11px] text-emerald-700 font-semibold"><i class="fa-solid fa-circle-check mr-1"></i>Kompetisi telah selesai</span>
                        <?php endif; ?>
                    </div>
                </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL FORM BUAT / EDIT KOMPETISI
     ===================================================== -->
<div id="modalKompetisiForm" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('kompetisi/simpan') ?>" method="post" enctype="multipart/form-data"
        class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_kompetisi" id="k_id">

        <div class="flex justify-between items-center border-b p-5">
            <h3 class="font-bold text-slate-800 text-base">
                <i class="fa-solid fa-trophy text-amber-500 mr-2"></i><span id="k_judul_form">Buat Kompetisi</span>
            </h3>
            <button type="button" onclick="closeModal('modalKompetisiForm')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <div class="overflow-y-auto p-5 space-y-5">
            <?php if ($errors) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <p class="font-bold mb-1">Data belum bisa disimpan:</p>
                    <ul class="list-disc list-inside space-y-0.5">
                        <?php foreach ($errors as $e) : ?>
                            <li><?= esc($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <!-- Informasi umum -->
            <div class="space-y-3">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Nama Kompetisi <span class="text-red-500">*</span></label>
                    <input type="text" name="nama_kompetisi" id="k_nama" required placeholder="Contoh: Kompetisi Inovasi Pendidikan 2026"
                        class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Deskripsi & Ketentuan</label>
                    <textarea name="deskripsi" id="k_deskripsi" rows="3"
                        placeholder="Contoh: Video maksimal 3 menit dengan tagar #sultraeduvation, inovasi minimal sudah berjalan 1 bulan."
                        class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Tanggal Mulai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_mulai" id="k_mulai" required class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Tanggal Selesai <span class="text-red-500">*</span></label>
                        <input type="date" name="tanggal_selesai" id="k_selesai" required class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                </div>

                <!-- Banner lomba -->
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Banner Lomba</label>
                    <div class="flex flex-col md:flex-row gap-3 items-start">
                        <div id="k_bannerPratinjau" class="w-full md:w-56 aspect-video rounded-xl border border-dashed border-slate-300 bg-slate-50 flex items-center justify-center overflow-hidden shrink-0">
                            <span class="text-slate-400 text-center px-3"><i class="fa-regular fa-image text-2xl block mb-1"></i>Belum ada banner</span>
                        </div>
                        <div class="flex-1 space-y-2">
                            <input type="file" name="banner" id="k_banner" accept=".jpg,.jpeg,.png,.webp" onchange="pratinjauBanner(this)"
                                class="w-full border border-slate-300 rounded-lg p-1.5 text-xs file:mr-2 file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:rounded">
                            <p class="text-[10px] text-slate-400">JPG, PNG, atau WEBP, maksimal 2 MB. Disarankan rasio 16:9 atau 1:1 (misalnya desain dari Canva). Banner dipakai saat membagikan lomba ke WhatsApp.</p>
                            <label id="k_hapusBannerWrap" class="hidden items-center gap-2 text-red-600 cursor-pointer">
                                <input type="checkbox" name="hapus_banner" value="1" class="accent-red-600"> Hapus banner saat ini
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Rubrik: kategori & kriteria -->
            <div id="wadahRubrik" class="space-y-5">
                <div id="k_kunci" class="hidden bg-amber-50 border border-amber-200 text-amber-800 px-4 py-3 rounded-xl">
                    <i class="fa-solid fa-lock mr-1"></i>
                    Kategori dan kriteria <b>dikunci</b> karena kompetisi ini sudah memiliki peserta. Nama, ketentuan, dan jadwal tetap bisa diubah.
                </div>

                <!-- Kategori -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">
                            <i class="fa-solid fa-tags text-indigo-500 mr-1"></i> Kategori Lomba
                        </label>
                        <button type="button" onclick="tambahKategori()" class="tombol-rubrik text-[11px] text-brand-600 font-semibold hover:underline">
                            <i class="fa-solid fa-plus mr-0.5"></i> Tambah Kategori
                        </button>
                    </div>
                    <div id="daftarKategori" class="space-y-2"></div>
                </div>

                <!-- Kriteria -->
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-bold text-slate-700 uppercase tracking-wider text-[11px]">
                            <i class="fa-solid fa-list-check text-emerald-600 mr-1"></i> Kriteria Penilaian Juri
                        </label>
                        <button type="button" onclick="tambahKriteria()" class="tombol-rubrik text-[11px] text-brand-600 font-semibold hover:underline">
                            <i class="fa-solid fa-plus mr-0.5"></i> Tambah Kriteria
                        </button>
                    </div>
                    <div class="grid grid-cols-12 gap-2 text-[10px] text-slate-400 font-semibold uppercase px-1 mb-1">
                        <div class="col-span-4">Nama Kriteria</div>
                        <div class="col-span-5">Keterangan</div>
                        <div class="col-span-2 text-center">Skor Maks</div>
                    </div>
                    <div id="daftarKriteria" class="space-y-2"></div>
                    <div class="flex justify-end items-center gap-2 mt-2 pr-10">
                        <span class="text-slate-500">Total skor maksimal:</span>
                        <span id="totalSkor" class="font-bold text-sm px-2.5 py-0.5 rounded-full">0 / 100</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end space-x-2 border-t p-5 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('modalKompetisiForm')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">
                <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan
            </button>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL KONFIRMASI (pindah tahap / hapus)
     ===================================================== -->
<div id="modalKonfirmasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
        <div id="konfirmasiIkon" class="mx-auto w-14 h-14 rounded-full flex items-center justify-center">
            <i class="fa-solid fa-circle-question text-2xl"></i>
        </div>
        <div>
            <h3 id="konfirmasiJudul" class="font-bold text-slate-800 text-base">Yakin?</h3>
            <p id="konfirmasiPesan" class="text-xs text-slate-500 mt-1 leading-relaxed"></p>
        </div>
        <form id="konfirmasiForm" method="post" class="flex justify-center space-x-2 pt-2">
            <?= csrf_field() ?>
            <input type="hidden" name="status" id="konfirmasiStatus">
            <button type="button" onclick="closeModal('modalKonfirmasi')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Batal</button>
            <button type="submit" id="konfirmasiTombol" class="px-4 py-2 text-white rounded-lg text-xs font-semibold">Ya, Lanjutkan</button>
        </form>
    </div>
</div>

<script>
    const KATEGORI_BAWAAN = <?= json_encode($kategoriBawaan) ?>;
    const KRITERIA_BAWAAN = <?= json_encode($kriteriaBawaan) ?>;

    // ---------- Baris kategori ----------
    function tambahKategori(nama = '') {
        const baris = document.createElement('div');
        baris.className = 'flex gap-2';

        const input = document.createElement('input');
        input.type = 'text';
        input.name = 'kategori[]';
        input.value = nama;
        input.placeholder = 'Nama kategori';
        input.className = 'flex-1 border border-slate-300 rounded-lg p-2.5';

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.title = 'Hapus kategori';
        hapus.className = 'tombol-rubrik w-9 border border-slate-200 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50';
        hapus.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        hapus.onclick = () => baris.remove();

        baris.append(input, hapus);
        document.getElementById('daftarKategori').appendChild(baris);
    }

    // ---------- Baris kriteria ----------
    function tambahKriteria(k = {}) {
        const baris = document.createElement('div');
        baris.className = 'grid grid-cols-12 gap-2';

        const buatInput = (nama, nilai, placeholder, kelas, tipe = 'text') => {
            const el = document.createElement('input');
            el.type = tipe;
            el.name = nama;
            el.value = nilai ?? '';
            el.placeholder = placeholder;
            el.className = kelas + ' border border-slate-300 rounded-lg p-2.5';
            return el;
        };

        const nama = buatInput('kriteria_nama[]', k.nama_kriteria, 'Nama kriteria', 'col-span-4');
        const ket  = buatInput('kriteria_keterangan[]', k.keterangan, 'Keterangan singkat', 'col-span-5');
        const skor = buatInput('kriteria_skor[]', k.skor_maks, '0', 'col-span-2 text-center font-bold', 'number');
        skor.min = 1;
        skor.max = 100;
        skor.oninput = hitungTotal;

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.title = 'Hapus kriteria';
        hapus.className = 'tombol-rubrik col-span-1 border border-slate-200 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50';
        hapus.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        hapus.onclick = () => { baris.remove(); hitungTotal(); };

        baris.append(nama, ket, skor, hapus);
        document.getElementById('daftarKriteria').appendChild(baris);
    }

    // ---------- Total skor (harus 100) ----------
    function hitungTotal() {
        let total = 0;
        document.querySelectorAll('input[name="kriteria_skor[]"]').forEach(el => total += parseInt(el.value) || 0);
        const label = document.getElementById('totalSkor');
        label.textContent = total + ' / 100';
        label.className = 'font-bold text-sm px-2.5 py-0.5 rounded-full ' +
            (total === 100 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600');
    }

    // ---------- Buka form ----------
    function bukaFormKompetisi(d = null) {
        const edit = d && d.id_kompetisi;

        document.getElementById('k_id').value = edit ? d.id_kompetisi : '';
        document.getElementById('k_nama').value = d?.nama_kompetisi || '';
        document.getElementById('k_deskripsi').value = d?.deskripsi || '';
        document.getElementById('k_mulai').value = d?.tanggal_mulai || '';
        document.getElementById('k_selesai').value = d?.tanggal_selesai || '';
        document.getElementById('k_judul_form').innerText = edit ? 'Edit Kompetisi' : 'Buat Kompetisi';
        tampilBannerLama(edit ? (d.banner || null) : null);

        // Isi kategori & kriteria (pakai bawaan kalau kosong)
        document.getElementById('daftarKategori').innerHTML = '';
        document.getElementById('daftarKriteria').innerHTML = '';

        const kategori = d?.kategori?.length ? d.kategori.map(k => k.nama_kategori ?? k) : KATEGORI_BAWAAN;
        const kriteria = d?.kriteria?.length ? d.kriteria : KRITERIA_BAWAAN;
        kategori.forEach(nama => tambahKategori(nama));
        kriteria.forEach(k => tambahKriteria(k));
        hitungTotal();

        // Kunci rubrik kalau sudah ada peserta
        const kunci = edit && d.jumlah_peserta > 0;
        document.getElementById('k_kunci').classList.toggle('hidden', !kunci);
        document.querySelectorAll('#daftarKategori input, #daftarKriteria input').forEach(el => el.disabled = kunci);
        document.querySelectorAll('.tombol-rubrik').forEach(el => el.classList.toggle('hidden', kunci));

        openModal('modalKompetisiForm');
    }

    // ---------- Konfirmasi ----------
    const GAYA = {
        red:   ['bg-red-100 text-red-600',     'bg-red-600 hover:bg-red-700'],
        amber: ['bg-amber-100 text-amber-600', 'bg-amber-500 hover:bg-amber-600'],
        brand: ['bg-brand-100 text-brand-600', 'bg-brand-600 hover:bg-brand-700'],
    };

    function tampilKonfirmasi(url, judul, pesan, warna, status = '') {
        document.getElementById('konfirmasiForm').action = url;
        document.getElementById('konfirmasiStatus').value = status;
        document.getElementById('konfirmasiJudul').innerText = judul;
        document.getElementById('konfirmasiPesan').innerText = pesan;
        document.getElementById('konfirmasiIkon').className = 'mx-auto w-14 h-14 rounded-full flex items-center justify-center ' + GAYA[warna][0];
        document.getElementById('konfirmasiTombol').className = 'px-4 py-2 text-white rounded-lg text-xs font-semibold ' + GAYA[warna][1];
        openModal('modalKonfirmasi');
    }

    function konfirmasiTahap(url, status, judul, pesan, warna) {
        tampilKonfirmasi(url, judul, pesan, warna, status);
    }

    function konfirmasiAksi(url, judul, pesan, warna) {
        tampilKonfirmasi(url, judul, pesan, warna);
    }

    // Kalau simpan gagal: buka lagi form dengan isian sebelumnya
    <?php if ($errors) : ?>
        <?php
        $oldKriteria = [];
        $oldKet      = (array) old('kriteria_keterangan');
        $oldSkor     = (array) old('kriteria_skor');
        foreach ((array) old('kriteria_nama') as $i => $n) {
            $oldKriteria[] = ['nama_kriteria' => $n, 'keterangan' => $oldKet[$i] ?? '', 'skor_maks' => $oldSkor[$i] ?? ''];
        }
        ?>
        document.addEventListener('DOMContentLoaded', function() {
            bukaFormKompetisi(<?= json_encode([
                'id_kompetisi'    => old('id_kompetisi'),
                'nama_kompetisi'  => old('nama_kompetisi'),
                'deskripsi'       => old('deskripsi'),
                'tanggal_mulai'   => old('tanggal_mulai'),
                'tanggal_selesai' => old('tanggal_selesai'),
                'kategori'        => array_values((array) old('kategori')),
                'kriteria'        => $oldKriteria,
                'jumlah_peserta'  => 0,
            ]) ?>);
        });
    <?php endif; ?>
</script>

<!-- =====================================================
     MODAL PESERTA & JUARA
     ===================================================== -->
<div id="modalPeserta" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-4xl w-full shadow-2xl max-h-[92vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <span id="ps_badge" class="text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                <h3 id="ps_judul" class="font-bold text-slate-800 text-base mt-2"></h3>
                <p id="ps_sub" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalPeserta')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="px-5 pt-4 space-y-3">
            <input type="text" id="ps_cari" oninput="renderPeserta()" placeholder="Cari sekolah, guru, atau judul karya..."
                class="w-full border border-slate-300 rounded-lg p-2.5 focus:ring-2 focus:ring-brand-500 focus:outline-none">
            <div id="ps_filter" class="flex flex-wrap gap-2"></div>
        </div>

        <div id="ps_isi" class="overflow-y-auto p-5 space-y-6"></div>

        <div class="flex justify-between items-center border-t p-4 bg-slate-50 rounded-b-2xl">
            <span id="ps_catatan" class="text-[11px] text-slate-400"></span>
            <button type="button" onclick="closeModal('modalPeserta')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
        </div>
    </div>
</div>

<!-- Modal deskripsi karya -->
<div id="modalKarya" class="fixed inset-0 bg-slate-900/70 backdrop-blur-sm z-[60] hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl max-h-[85vh] flex flex-col text-xs">
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <h3 id="ky_judul" class="font-bold text-slate-800 text-base leading-snug"></h3>
                <p id="ky_sub" class="text-slate-400 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalKarya')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="overflow-y-auto p-5 space-y-3">
            <div id="ky_deskripsi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"></div>
            <a id="ky_video" target="_blank" rel="noopener" class="hidden inline-flex items-center bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-1.5 rounded-lg">
                <i class="fa-brands fa-youtube mr-1.5"></i>Tonton Video Karya
            </a>
        </div>
    </div>
</div>

<script>
    let kompetisiAktif = null;
    let kategoriAktif = '';

    const WARNA_JUARA = {
        1: { kartu: 'border-amber-300 bg-gradient-to-b from-amber-50 to-white', pita: 'bg-gradient-to-r from-amber-400 to-yellow-400 text-amber-950', ikon: 'text-amber-500' },
        2: { kartu: 'border-slate-300 bg-gradient-to-b from-slate-50 to-white', pita: 'bg-gradient-to-r from-slate-300 to-slate-400 text-slate-900', ikon: 'text-slate-400' },
        3: { kartu: 'border-orange-300 bg-gradient-to-b from-orange-50 to-white', pita: 'bg-gradient-to-r from-orange-300 to-orange-400 text-orange-950', ikon: 'text-orange-500' },
    };
    const VALIDASI = {
        menunggu:    ['Menunggu validasi', 'bg-amber-100 text-amber-800'],
        tervalidasi: ['Tervalidasi', 'bg-emerald-100 text-emerald-800'],
        ditolak:     ['Ditolak', 'bg-red-100 text-red-700'],
    };

    const el = (tag, kelas, teks) => {
        const e = document.createElement(tag);
        if (kelas) e.className = kelas;
        if (teks !== undefined && teks !== null) e.textContent = teks;
        return e;
    };
    const angka = n => (n === null || n === undefined || n === '') ? '–' : parseFloat(n).toFixed(2).replace('.', ',');

    function lihatPeserta(k) {
        kompetisiAktif = k;
        kategoriAktif = '';
        const diumumkan = !!parseInt(k.hasil_diumumkan);

        const badge = document.getElementById('ps_badge');
        badge.textContent = diumumkan ? 'Hasil diumumkan' : (<?= json_encode($label) ?>[k.status] || k.status);
        badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full ' + (diumumkan ? 'bg-amber-100 text-amber-800' : 'bg-blue-100 text-blue-700');
        document.getElementById('ps_judul').textContent = k.nama_kompetisi;

        const jumlahSekolah = new Set(k.peserta.map(p => p.nama_sekolah)).size;
        document.getElementById('ps_sub').textContent = k.peserta.length + ' karya • ' + jumlahSekolah + ' sekolah • ' + k.kategori.length + ' kategori';
        document.getElementById('ps_catatan').textContent = diumumkan
            ? 'Juara ditetapkan dari rata-rata nilai seluruh juri di setiap kategori.'
            : 'Klik judul karya untuk membaca deskripsi lengkapnya.';
        document.getElementById('ps_cari').value = '';

        // Filter kategori
        const filter = document.getElementById('ps_filter');
        filter.innerHTML = '';
        [{ id_kategori: '', nama_kategori: 'Semua kategori' }, ...k.kategori].forEach(kat => {
            const b = el('button', 'tombol-kat', kat.nama_kategori);
            b.type = 'button';
            b.dataset.id = kat.id_kategori;
            b.onclick = () => { kategoriAktif = String(kat.id_kategori); renderPeserta(); };
            filter.appendChild(b);
        });

        renderPeserta();
        openModal('modalPeserta');
    }

    function renderPeserta() {
        const k = kompetisiAktif;
        const diumumkan = !!parseInt(k.hasil_diumumkan);
        const cari = document.getElementById('ps_cari').value.trim().toLowerCase();

        document.querySelectorAll('.tombol-kat').forEach(b => {
            const aktif = String(b.dataset.id) === kategoriAktif;
            b.className = 'tombol-kat px-3 py-1 rounded-full border text-[11px] font-semibold ' +
                (aktif ? 'bg-brand-600 border-brand-600 text-white' : 'border-slate-300 text-slate-600 hover:bg-slate-50');
        });

        const cocok = p => !cari || [p.nama_sekolah, p.nama_guru, p.judul_karya].some(t => (t || '').toLowerCase().includes(cari));

        const wadah = document.getElementById('ps_isi');
        wadah.innerHTML = '';
        let adaIsi = false;

        k.kategori
            .filter(kat => !kategoriAktif || String(kat.id_kategori) === kategoriAktif)
            .forEach(kat => {
                const daftar = k.peserta.filter(p => String(p.id_kategori) === String(kat.id_kategori) && cocok(p));
                if (!daftar.length) return;
                adaIsi = true;

                const bagian = el('div', 'space-y-3');
                const judul = el('div', 'flex items-center gap-2 font-bold text-slate-700 text-sm');
                judul.append(el('i', 'fa-solid fa-tag text-indigo-500'), el('span', '', kat.nama_kategori), el('span', 'text-[11px] font-normal text-slate-400', '(' + daftar.length + ' karya)'));
                bagian.appendChild(judul);

                const juara = diumumkan ? daftar.filter(p => WARNA_JUARA[p.peringkat]) : [];
                const lainnya = daftar.filter(p => !juara.includes(p));

                // ---------- Kartu juara ----------
                if (juara.length) {
                    const grid = el('div', 'grid grid-cols-1 md:grid-cols-3 gap-3');
                    juara.forEach(p => {
                        const w = WARNA_JUARA[p.peringkat];
                        const kartu = el('div', 'border-2 rounded-2xl overflow-hidden shadow-sm ' + w.kartu);
                        const pita = el('div', 'px-3 py-2 flex justify-between items-center font-bold ' + w.pita);
                        const kiri = el('span');
                        kiri.append(el('i', 'fa-solid fa-medal mr-1.5'), document.createTextNode('Juara ' + p.peringkat));
                        pita.append(kiri, el('span', 'text-sm', angka(p.nilai)));

                        const isi = el('div', 'p-3 space-y-2');
                        const karya = el('button', 'font-bold text-slate-800 text-left hover:text-brand-600 hover:underline leading-snug', p.judul_karya);
                        karya.type = 'button';
                        karya.onclick = () => lihatKarya(p);
                        const guru = el('div', 'flex gap-2 items-start');
                        guru.append(el('i', 'fa-solid fa-user text-slate-400 mt-0.5'));
                        const infoGuru = el('div');
                        infoGuru.append(el('div', 'font-semibold text-slate-700', p.nama_guru || '-'), el('div', 'text-[10px] text-slate-400', p.mapel || ''));
                        guru.appendChild(infoGuru);
                        const sekolah = el('div', 'flex gap-2 items-start');
                        sekolah.append(el('i', 'fa-solid fa-school text-slate-400 mt-0.5'));
                        const infoSekolah = el('div');
                        infoSekolah.append(el('div', 'font-semibold text-slate-700', p.nama_sekolah || '-'), el('div', 'text-[10px] text-slate-400', p.kabupaten_kota || ''));
                        sekolah.appendChild(infoSekolah);

                        isi.append(karya, guru, sekolah);
                        kartu.append(pita, isi);
                        grid.appendChild(kartu);
                    });
                    bagian.appendChild(grid);
                }

                // ---------- Tabel peserta lainnya ----------
                if (lainnya.length) {
                    if (juara.length) bagian.appendChild(el('div', 'text-[11px] font-bold text-slate-500 uppercase tracking-wider pt-1', 'Peserta lainnya (' + lainnya.length + ')'));
                    const kotak = el('div', 'border border-slate-200 rounded-xl overflow-x-auto');
                    const tabel = el('table', 'w-full text-left text-slate-600');
                    const kepala = el('thead', 'bg-slate-50 text-[10px] uppercase text-slate-500');
                    const barisKepala = el('tr');
                    ['#', 'Karya', 'Guru', 'Sekolah', diumumkan ? 'Nilai' : 'Status'].forEach((t, i) => barisKepala.appendChild(el('th', 'p-2.5 ' + (i === 4 ? 'text-right' : ''), t)));
                    kepala.appendChild(barisKepala);
                    const badan = el('tbody', 'divide-y divide-slate-100');

                    lainnya.forEach((p, i) => {
                        const tr = el('tr', 'hover:bg-slate-50');
                        tr.appendChild(el('td', 'p-2.5 text-slate-400', String(juara.length + i + 1)));

                        const tdKarya = el('td', 'p-2.5');
                        const tombolKarya = el('button', 'font-semibold text-slate-800 text-left hover:text-brand-600 hover:underline', p.judul_karya);
                        tombolKarya.type = 'button';
                        tombolKarya.onclick = () => lihatKarya(p);
                        tdKarya.appendChild(tombolKarya);
                        tr.appendChild(tdKarya);

                        const tdGuru = el('td', 'p-2.5');
                        tdGuru.append(el('div', 'text-slate-700', p.nama_guru || '-'), el('div', 'text-[10px] text-slate-400', p.mapel || ''));
                        tr.appendChild(tdGuru);

                        const tdSekolah = el('td', 'p-2.5');
                        tdSekolah.append(el('div', 'text-slate-700', p.nama_sekolah || '-'), el('div', 'text-[10px] text-slate-400', p.kabupaten_kota || ''));
                        tr.appendChild(tdSekolah);

                        const tdAkhir = el('td', 'p-2.5 text-right');
                        if (diumumkan) {
                            tdAkhir.appendChild(el('span', 'font-bold text-slate-800', angka(p.nilai)));
                        } else {
                            const v = VALIDASI[p.status_validasi] || ['-', 'bg-slate-100 text-slate-500'];
                            tdAkhir.appendChild(el('span', 'text-[10px] font-bold px-2 py-0.5 rounded-full whitespace-nowrap ' + v[1], v[0]));
                        }
                        tr.appendChild(tdAkhir);
                        badan.appendChild(tr);
                    });

                    tabel.append(kepala, badan);
                    kotak.appendChild(tabel);
                    bagian.appendChild(kotak);
                }

                wadah.appendChild(bagian);
            });

        if (!adaIsi) {
            wadah.appendChild(el('p', 'text-center text-slate-400 py-8', cari ? 'Tidak ada peserta yang cocok dengan pencarian.' : 'Belum ada peserta di kategori ini.'));
        }
    }

    function lihatKarya(p) {
        document.getElementById('ky_judul').textContent = p.judul_karya;
        document.getElementById('ky_sub').textContent = (p.nama_guru || '-') + ' • ' + (p.nama_sekolah || '-');
        document.getElementById('ky_deskripsi').textContent = p.deskripsi_karya || 'Belum ada deskripsi.';
        const video = document.getElementById('ky_video');
        const aman = /^https?:\/\//i.test(p.link_video || '');
        video.classList.toggle('hidden', !aman);
        if (aman) video.href = p.link_video;
        openModal('modalKarya');
    }
</script>

<!-- =====================================================
     MODAL BAGIKAN KE WHATSAPP
     ===================================================== -->
<div id="modalBagikan" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl max-h-[92vh] flex flex-col text-xs">
        <div class="flex justify-between items-start border-b p-5">
            <div>
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-brands fa-whatsapp text-green-600 mr-2"></i>Bagikan ke WhatsApp</h3>
                <p id="b_nama" class="text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalBagikan')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>

        <div class="overflow-y-auto p-5 space-y-4">
            <img id="b_banner" alt="Banner lomba" class="hidden w-full rounded-xl border">
            <div id="b_tanpaBanner" class="hidden bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-3">
                <i class="fa-solid fa-circle-info mr-1"></i>Lomba ini belum punya banner. Pesan dikirim sebagai teks saja. Unggah banner lewat tombol Edit.
            </div>
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Teks Pengumuman <span class="font-normal text-slate-400">(bisa diedit)</span></label>
                <textarea id="b_teks" rows="12" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono text-[11px] leading-relaxed"></textarea>
            </div>
            <div class="bg-slate-50 border rounded-xl p-3 text-[11px] text-slate-500 space-y-1">
                <p><i class="fa-solid fa-mobile-screen mr-1"></i><b>Di HP:</b> banner dan teks dikirim bersamaan. Pilih WhatsApp, lalu pilih grup atau kontak.</p>
                <p><i class="fa-solid fa-desktop mr-1"></i><b>Di komputer:</b> WhatsApp Web terbuka dengan teks dan tautan lomba. Banner tampil sebagai pratinjau tautan setelah aplikasi online, atau unduh banner lalu lampirkan manual.</p>
            </div>
        </div>

        <div class="flex flex-wrap justify-end gap-2 border-t p-5 bg-slate-50 rounded-b-2xl">
            <a id="b_unduh" download class="hidden px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white hover:bg-slate-100 items-center">
                <i class="fa-solid fa-download mr-1.5"></i>Unduh Banner
            </a>
            <button type="button" onclick="salinPengumuman(this)" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white hover:bg-slate-100">
                <i class="fa-regular fa-copy mr-1.5"></i>Salin Teks
            </button>
            <button type="button" onclick="kirimWhatsApp(this)" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-semibold">
                <i class="fa-brands fa-whatsapp mr-1.5"></i>Kirim ke WhatsApp
            </button>
        </div>
    </div>
</div>

<script>
    const URL_DASAR  = '<?= rtrim(base_url(), '/') ?>/';
    const URL_LOMBA  = '<?= site_url('lomba') ?>/';
    const URL_LOGIN  = '<?= site_url('login') ?>';
    let bagikanAktif = null;

    // ---------- Banner di form ----------
    function pratinjauBanner(input) {
        const wadah = document.getElementById('k_bannerPratinjau');
        const file = input.files[0];
        if (!file) return;
        if (file.size > 2 * 1024 * 1024) {
            alert('Ukuran banner maksimal 2 MB.');
            input.value = '';
            return;
        }
        const img = document.createElement('img');
        img.className = 'w-full h-full object-cover';
        img.src = URL.createObjectURL(file);
        wadah.innerHTML = '';
        wadah.appendChild(img);
    }

    function tampilBannerLama(path) {
        const wadah = document.getElementById('k_bannerPratinjau');
        document.getElementById('k_banner').value = '';
        const hapus = document.getElementById('k_hapusBannerWrap');
        hapus.classList.toggle('hidden', !path);
        hapus.classList.toggle('flex', !!path);
        hapus.querySelector('input').checked = false;
        if (path) {
            const img = document.createElement('img');
            img.className = 'w-full h-full object-cover';
            img.src = URL_DASAR + path;
            wadah.innerHTML = '';
            wadah.appendChild(img);
        } else {
            wadah.innerHTML = '<span class="text-slate-400 text-center px-3"><i class="fa-regular fa-image text-2xl block mb-1"></i>Belum ada banner</span>';
        }
    }

    // ---------- Teks pengumuman otomatis ----------
    function tanggalPanjang(t) {
        return new Date(t + 'T00:00:00').toLocaleDateString('id-ID', { weekday: 'long', day: 'numeric', month: 'long', year: 'numeric' });
    }

    function susunPengumuman(k) {
        const baris = [];
        const diumumkan = !!parseInt(k.hasil_diumumkan);

        if (k.status === 'pendaftaran') baris.push('📢 *PENDAFTARAN DIBUKA*');
        else if (k.status === 'berlangsung') baris.push('⚖️ *TAHAP PENJURIAN*');
        else baris.push(diumumkan ? '🏆 *PENGUMUMAN PEMENANG*' : '🏁 *KOMPETISI SELESAI*');

        baris.push('*' + k.nama_kompetisi + '*', '');
        baris.push('🗓️ Periode: ' + tanggalPanjang(k.tanggal_mulai) + ' s.d. ' + tanggalPanjang(k.tanggal_selesai));

        if (k.kategori && k.kategori.length) {
            baris.push('', '🏷️ Kategori lomba:');
            k.kategori.forEach(nama => baris.push('• ' + nama));
        }
        if (k.deskripsi) {
            baris.push('', '📝 Ketentuan:', k.deskripsi);
        }

        baris.push('');
        if (k.status === 'pendaftaran') {
            const sisa = Math.floor((new Date(k.tanggal_selesai + 'T23:59:59') - new Date()) / 86400000);
            if (sisa >= 0) baris.push('⏳ Pendaftaran ditutup ' + (sisa === 0 ? '*hari ini*' : 'dalam *' + sisa + ' hari*') + '.');
            baris.push('Bapak/Ibu guru SMA/SMK/SLB se-Sulawesi Tenggara dapat mendaftarkan karya melalui akun EDUVATION masing-masing.');
        } else if (diumumkan) {
            baris.push('Selamat kepada para pemenang! Hasil lengkap dapat dilihat melalui akun EDUVATION.');
        }

        baris.push('', '🔗 Info lomba: ' + URL_LOMBA + k.id_kompetisi);
        if (k.status === 'pendaftaran') baris.push('🔑 Masuk: ' + URL_LOGIN);
        baris.push('', '_Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara_');

        return baris.join('\n');
    }

    function bukaBagikan(k) {
        bagikanAktif = k;
        document.getElementById('b_nama').textContent = k.nama_kompetisi;
        document.getElementById('b_teks').value = susunPengumuman(k);

        const img = document.getElementById('b_banner');
        const unduh = document.getElementById('b_unduh');
        const ada = !!k.banner;
        img.classList.toggle('hidden', !ada);
        unduh.classList.toggle('hidden', !ada);
        unduh.classList.toggle('inline-flex', ada);
        document.getElementById('b_tanpaBanner').classList.toggle('hidden', ada);
        if (ada) {
            img.src = URL_DASAR + k.banner;
            unduh.href = URL_DASAR + k.banner;
        }
        openModal('modalBagikan');
    }

    function salinPengumuman(tombol) {
        navigator.clipboard.writeText(document.getElementById('b_teks').value).then(() => {
            const awal = tombol.innerHTML;
            tombol.innerHTML = '<i class="fa-solid fa-check mr-1.5"></i>Tersalin!';
            setTimeout(() => tombol.innerHTML = awal, 2000);
        });
    }

    async function kirimWhatsApp(tombol) {
        const teks = document.getElementById('b_teks').value;
        const k = bagikanAktif;

        // HP: kirim banner + teks lewat menu bagikan bawaan
        if (k.banner && navigator.canShare) {
            const awal = tombol.innerHTML;
            tombol.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Menyiapkan...';
            try {
                const res = await fetch(URL_DASAR + k.banner);
                const blob = await res.blob();
                const ext = (k.banner.split('.').pop() || 'jpg').toLowerCase();
                const file = new File([blob], 'banner-lomba.' + ext, { type: blob.type || 'image/jpeg' });
                if (navigator.canShare({ files: [file], text: teks })) {
                    await navigator.share({ files: [file], text: teks, title: k.nama_kompetisi });
                    tombol.innerHTML = awal;
                    return;
                }
            } catch (e) {
                if (e && e.name === 'AbortError') { tombol.innerHTML = awal; return; } // dibatalkan pengguna
            }
            tombol.innerHTML = awal;
        }

        // Komputer / tanpa banner: buka WhatsApp dengan teks
        window.open('https://wa.me/?text=' + encodeURIComponent(teks), '_blank', 'noopener');
    }
</script>
<?= $this->endSection() ?>