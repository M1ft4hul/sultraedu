<?php
/** @var array $praktik */
/** @var string $status */
/** @var array $jumlah */
/** @var array $label */
/** @var \CodeIgniter\Pager\Pager $pager */
/** @var int $perHalaman */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$badge = [
    'menunggu'  => 'bg-amber-100 text-amber-800',
    'disetujui' => 'bg-emerald-100 text-emerald-800',
    'ditolak'   => 'bg-red-100 text-red-700',
];
$ikonStatus = [
    'menunggu'  => 'fa-clock',
    'disetujui' => 'fa-check',
    'ditolak'   => 'fa-xmark',
];
$tabs = ['' => 'Semua'] + $label;
?>
<section class="space-y-6">

    <!-- Judul -->
    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Validasi Praktik Baik</h2>
        <p class="text-xs text-slate-500">Praktik baik yang telah diverifikasi Admin Sekolah dan menunggu validasi Dinas.</p>
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

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm space-y-4">

        <!-- Tab status -->
        <div class="flex flex-wrap gap-2 border-b pb-3 text-xs font-semibold">
            <?php foreach ($tabs as $kunci => $teks) : ?>
                <a href="<?= site_url('praktik-baik') . '?' . http_build_query(array_filter(['status' => $kunci, 'per' => $perHalaman !== 10 ? $perHalaman : ''])) ?>"
                    class="px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 transition
                           <?= $status === $kunci ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= esc($teks) ?>
                    <span class="<?= $status === $kunci ? 'bg-white/25' : 'bg-white' ?> text-[10px] px-1.5 rounded-full"><?= $jumlah[$kunci] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($praktik)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-regular fa-folder-open text-2xl text-brand-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">
                    <?= $status === 'menunggu' ? 'Tidak ada praktik baik yang menunggu validasi' : 'Belum ada praktik baik' ?>
                </p>
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
                            <?php $st = $p['status_verifikasi_dinas']; ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5 max-w-sm">
                                    <div class="font-bold text-slate-800"><?= esc($p['judul']) ?></div>
                                    <div class="text-[11px] text-slate-400"><?= esc($p['kategori'] ?: '-') ?></div>
                                </td>
                                <td class="p-3.5">
                                    <?= esc($p['nama_sekolah'] ?? '-') ?>
                                    <div class="text-[11px] text-slate-400"><?= esc($p['kabupaten_kota'] ?? '') ?></div>
                                </td>
                                <td class="p-3.5"><?= esc($p['nama_guru'] ?? '-') ?></td>
                                <td class="p-3.5 whitespace-nowrap"><?= date('d/m/Y', strtotime($p['tanggal_upload'])) ?></td>
                                <td class="p-3.5">
                                    <span class="<?= $badge[$st] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                        <i class="fa-solid <?= $ikonStatus[$st] ?? '' ?> mr-1"></i><?= esc($label[$st] ?? $st) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <button type="button"
                                        data-praktik="<?= esc(json_encode($p), 'attr') ?>"
                                        onclick="bukaDetail(JSON.parse(this.dataset.praktik))"
                                        class="<?= $st === 'menunggu' ? 'bg-brand-600 hover:bg-brand-700 text-white' : 'border border-slate-200 hover:bg-slate-50 text-slate-600' ?> text-[11px] px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap">
                                        <i class="fa-solid <?= $st === 'menunggu' ? 'fa-clipboard-check' : 'fa-eye' ?> mr-1"></i>
                                        <?= $st === 'menunggu' ? 'Periksa' : 'Detail' ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?= view('admin/componen_be/pagination', ['pager' => $pager, 'grup' => 'praktik', 'perHalaman' => $perHalaman, 'url' => site_url('praktik-baik'), 'tahan' => ['status' => $status], 'satuan' => 'praktik baik']) ?>
        <?php endif; ?>
    </div>
</section>

<!-- =====================================================
     MODAL DETAIL PRAKTIK BAIK
     ===================================================== -->
<div id="modalDetailPraktik" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">

        <!-- Kepala -->
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <div class="flex flex-wrap items-center gap-2">
                    <span id="d_status" class="text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                    <span id="d_kategori" class="bg-blue-50 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                </div>
                <h3 id="d_judul" class="font-bold text-slate-800 text-base mt-2 leading-snug"></h3>
                <p class="text-slate-400 mt-0.5">Diunggah <span id="d_tanggal"></span></p>
            </div>
            <button type="button" onclick="closeModal('modalDetailPraktik')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Isi -->
        <div class="overflow-y-auto p-5 space-y-5">

            <!-- Deskripsi -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-book-open text-brand-600 mr-1"></i> Deskripsi Praktik Baik
                </h4>
                <div id="d_deskripsi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"></div>
            </div>

            <!-- Pengusul & sekolah -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="border rounded-xl p-4 space-y-1.5">
                    <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                        <i class="fa-solid fa-user text-brand-600 mr-1"></i> Pengusul
                    </h4>
                    <div><span class="text-slate-400 w-20 inline-block">Nama</span> <b id="d_guru" class="text-slate-800"></b></div>
                    <div><span class="text-slate-400 w-20 inline-block">NIP</span> <span id="d_nip"></span></div>
                    <div><span class="text-slate-400 w-20 inline-block">Mapel</span> <span id="d_mapel"></span></div>
                </div>
                <div class="border rounded-xl p-4 space-y-1.5">
                    <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                        <i class="fa-solid fa-school text-brand-600 mr-1"></i> Satuan Pendidikan
                    </h4>
                    <div><span class="text-slate-400 w-20 inline-block">Sekolah</span> <b id="d_sekolah" class="text-slate-800"></b></div>
                    <div><span class="text-slate-400 w-20 inline-block">NPSN</span> <span id="d_npsn" class="font-mono"></span></div>
                    <div><span class="text-slate-400 w-20 inline-block">Wilayah</span> <span id="d_kab"></span></div>
                </div>
            </div>

            <!-- Lampiran -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-paperclip text-slate-500 mr-1"></i> Lampiran Dokumen
                </h4>
                <div id="d_lampiran" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
            </div>

            <!-- Tanggapan perbaikan dari guru -->
            <div id="d_revisiWrap" class="hidden bg-violet-50 border border-violet-200 rounded-xl p-3.5 space-y-1">
                <div class="font-bold text-violet-800"><i class="fa-solid fa-code-branch mr-1"></i>Tanggapan Perbaikan dari Guru <span id="d_revisiKe"></span></div>
                <p id="d_revisiIsi" class="text-slate-700 whitespace-pre-line"></p>
            </div>

            <!-- Riwayat verifikasi: tahap sekolah & tahap dinas -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-clock-rotate-left text-slate-500 mr-1"></i> Riwayat Verifikasi
                </h4>
                <div class="space-y-2">
                    <!-- Tahap 1 -->
                    <div class="border-l-4 border-emerald-400 bg-emerald-50/50 rounded-r-xl p-3.5 space-y-1">
                        <div class="font-bold text-emerald-800">
                            <i class="fa-solid fa-circle-check mr-1"></i> Tahap 1 — Disetujui Admin Sekolah
                        </div>
                        <div><span class="text-slate-400 w-24 inline-block">Oleh</span> <span id="d_vs_nama"></span></div>
                        <div><span class="text-slate-400 w-24 inline-block">Tanggal</span> <span id="d_vs_tanggal"></span></div>
                        <div id="d_vs_catatan_wrap"><span class="text-slate-400 w-24 inline-block align-top">Catatan</span> <span id="d_vs_catatan" class="whitespace-pre-line"></span></div>
                    </div>
                    <!-- Tahap 2 (hanya kalau sudah divalidasi dinas) -->
                    <div id="d_vd" class="border-l-4 rounded-r-xl p-3.5 space-y-1">
                        <div id="d_vd_judul" class="font-bold"></div>
                        <div><span class="text-slate-400 w-24 inline-block">Oleh</span> <span id="d_vd_nama"></span></div>
                        <div><span class="text-slate-400 w-24 inline-block">Tanggal</span> <span id="d_vd_tanggal"></span></div>
                        <div id="d_vd_catatan_wrap"><span class="text-slate-400 w-24 inline-block align-top">Catatan</span> <span id="d_vd_catatan" class="whitespace-pre-line"></span></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form validasi dinas (hanya kalau masih menunggu) -->
        <form id="d_form" method="post" class="border-t p-5 space-y-3 bg-slate-50 rounded-b-2xl">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" id="d_aksi">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Catatan Validasi Dinas</label>
                <textarea name="catatan" id="d_catatan_input" rows="2"
                    placeholder="Wajib diisi jika ditolak. Opsional jika disetujui."
                    class="w-full border border-slate-300 rounded-lg p-2.5 bg-white"></textarea>
            </div>
            <div class="flex justify-end gap-2">
                <button type="button" onclick="kirimValidasi('ditolak')"
                    class="px-4 py-2 border border-red-300 text-red-600 hover:bg-red-50 rounded-lg font-semibold">
                    <i class="fa-solid fa-xmark mr-1"></i> Tolak
                </button>
                <button type="button" onclick="kirimValidasi('disetujui')"
                    class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-check mr-1"></i> Setujui Praktik Baik
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const LABEL_STATUS = <?= json_encode($label) ?>;
    const BADGE_STATUS = <?= json_encode($badge) ?>;
    const URL_VERIFIKASI = '<?= site_url('praktik-baik/verifikasi') ?>/';

    function formatTanggal(t) {
        if (!t) return '-';
        const d = new Date(t.replace(' ', 'T'));
        return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
    }

    // Isi teks dengan aman (tidak dieksekusi sebagai HTML)
    function isi(id, teks) {
        document.getElementById(id).textContent = teks || '-';
    }

    function bukaDetail(d) {
        const st = d.status_verifikasi_dinas;

        const badge = document.getElementById('d_status');
        badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full ' + (BADGE_STATUS[st] || '');
        badge.textContent = LABEL_STATUS[st] || st;

        const kategori = document.getElementById('d_kategori');
        kategori.classList.toggle('hidden', !d.kategori);
        kategori.textContent = d.kategori || '';

        isi('d_judul', d.judul);
        isi('d_tanggal', formatTanggal(d.tanggal_upload));
        isi('d_deskripsi', d.deskripsi || 'Belum ada deskripsi.');
        isi('d_guru', d.nama_guru);
        isi('d_nip', d.nip);
        isi('d_mapel', d.mapel);
        isi('d_sekolah', d.nama_sekolah);
        isi('d_npsn', d.npsn);
        isi('d_kab', d.kabupaten_kota);

        // Lampiran
        const wadah = document.getElementById('d_lampiran');
        wadah.innerHTML = '';
        if (!d.lampiran || d.lampiran.length === 0) {
            wadah.innerHTML = '<p class="text-slate-400 italic">Tidak ada lampiran.</p>';
        } else {
            const ikon = { foto: 'fa-image', video: 'fa-video', tautan_publikasi: 'fa-link', penghargaan: 'fa-award' };
            d.lampiran.forEach(f => {
                const a = document.createElement(f.url ? 'a' : 'div');
                if (f.url) { a.href = f.url; a.target = '_blank'; }
                a.className = 'flex items-center gap-3 border rounded-lg p-2.5 hover:bg-slate-50';

                const i = document.createElement('i');
                i.className = 'fa-solid ' + (ikon[f.jenis_dokumen] || 'fa-file-lines') + ' text-slate-400 text-base w-5 text-center';

                const teks = document.createElement('div');
                teks.className = 'min-w-0';
                const nama = document.createElement('div');
                nama.className = 'font-semibold text-slate-700 truncate';
                nama.textContent = f.nama_file || 'Dokumen';
                const jenis = document.createElement('div');
                jenis.className = 'text-[10px] text-slate-400 capitalize';
                jenis.textContent = (f.jenis_dokumen || '').replaceAll('_', ' ') + (f.keterangan ? ' • ' + f.keterangan : '');
                teks.append(nama, jenis);

                a.append(i, teks);
                wadah.appendChild(a);
            });
        }

        // Tahap 1: verifikasi sekolah
        isi('d_vs_nama', d.verifikator_sekolah);
        isi('d_vs_tanggal', formatTanggal(d.tanggal_verifikasi_sekolah));
        document.getElementById('d_vs_catatan_wrap').classList.toggle('hidden', !d.catatan_admin_sekolah);
        isi('d_vs_catatan', d.catatan_admin_sekolah);

        // Tahap 2: validasi dinas (hanya kalau sudah diputuskan)
        const vd = document.getElementById('d_vd');
        vd.classList.toggle('hidden', st === 'menunggu');
        if (st !== 'menunggu') {
            const setuju = st === 'disetujui';
            vd.className = 'border-l-4 rounded-r-xl p-3.5 space-y-1 ' +
                (setuju ? 'border-emerald-400 bg-emerald-50/50' : 'border-red-400 bg-red-50/50');
            const judul = document.getElementById('d_vd_judul');
            judul.className = 'font-bold ' + (setuju ? 'text-emerald-800' : 'text-red-700');
            judul.textContent = setuju ? 'Tahap 2 — Disetujui Dinas' : 'Tahap 2 — Ditolak Dinas';
            isi('d_vd_nama', d.verifikator_dinas);
            isi('d_vd_tanggal', formatTanggal(d.tanggal_verifikasi_dinas));
            document.getElementById('d_vd_catatan_wrap').classList.toggle('hidden', !d.catatan_petugas);
            isi('d_vd_catatan', d.catatan_petugas);
        }

        // Form validasi hanya untuk yang masih menunggu
        const form = document.getElementById('d_form');
        form.classList.toggle('hidden', st !== 'menunggu');
        form.action = URL_VERIFIKASI + d.id_praktik_baik;
        document.getElementById('d_catatan_input').value = '';

        // Tanggapan perbaikan guru (kalau pengajuan ini hasil revisi)
        const adaRevisi = parseInt(d.jumlah_revisi) > 0 && d.catatan_revisi;
        document.getElementById('d_revisiWrap').classList.toggle('hidden', !adaRevisi);
        if (adaRevisi) {
            document.getElementById('d_revisiKe').textContent = '(revisi ke-' + d.jumlah_revisi + ')';
            document.getElementById('d_revisiIsi').textContent = d.catatan_revisi;
        }

        openModal('modalDetailPraktik');
    }

    function kirimValidasi(aksi) {
        const catatan = document.getElementById('d_catatan_input');

        if (aksi === 'ditolak' && catatan.value.trim() === '') {
            catatan.focus();
            catatan.classList.add('ring-2', 'ring-red-400');
            showToast('Catatan wajib diisi', 'Tuliskan alasan penolakan supaya guru tahu apa yang perlu diperbaiki.', 'error');
            return;
        }

        document.getElementById('d_aksi').value = aksi;
        document.getElementById('d_form').submit();
    }
</script>
<?= $this->endSection() ?>