<?php

/** @var array $kompetisi */
/** @var array $label */
/** @var array $kategoriBawaan */
/** @var array $kriteriaBawaan */
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
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
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

    <?php if (empty($kompetisi)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-trophy text-2xl text-amber-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700">Belum ada kompetisi</p>
            <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Buat kompetisi pertama dengan tombol "Buat Kompetisi" di atas.</p>
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
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 flex flex-col gap-4">

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
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL FORM BUAT / EDIT KOMPETISI
     ===================================================== -->
<div id="modalKompetisiForm" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('kompetisi/simpan') ?>" method="post"
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
        const ket = buatInput('kriteria_keterangan[]', k.keterangan, 'Keterangan singkat', 'col-span-5');
        const skor = buatInput('kriteria_skor[]', k.skor_maks, '0', 'col-span-2 text-center font-bold', 'number');
        skor.min = 1;
        skor.max = 100;
        skor.oninput = hitungTotal;

        const hapus = document.createElement('button');
        hapus.type = 'button';
        hapus.title = 'Hapus kriteria';
        hapus.className = 'tombol-rubrik col-span-1 border border-slate-200 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50';
        hapus.innerHTML = '<i class="fa-solid fa-xmark"></i>';
        hapus.onclick = () => {
            baris.remove();
            hitungTotal();
        };

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
        red: ['bg-red-100 text-red-600', 'bg-red-600 hover:bg-red-700'],
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
<?= $this->endSection() ?>