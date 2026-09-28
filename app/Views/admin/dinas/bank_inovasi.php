<?php

/** @var array $inovasi */
/** @var string $status */
/** @var array $jumlah */
/** @var array $label */
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
        <h2 class="text-xl font-bold text-slate-800">Validasi Bank Inovasi</h2>
        <p class="text-xs text-slate-500">Tinjau usulan inovasi pendidikan dari satuan pendidikan sebelum resmi masuk Bank Inovasi Provinsi Sulawesi Tenggara.</p>
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
                <a href="<?= site_url('bank-inovasi') . ($kunci !== '' ? '?status=' . $kunci : '') ?>"
                    class="px-3.5 py-1.5 rounded-lg flex items-center gap-1.5 transition
                           <?= $status === $kunci ? 'bg-brand-600 text-white shadow-sm' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">
                    <?= esc($teks) ?>
                    <span class="<?= $status === $kunci ? 'bg-white/25' : 'bg-white' ?> text-[10px] px-1.5 rounded-full"><?= $jumlah[$kunci] ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <?php if (empty($inovasi)) : ?>
            <div class="flex flex-col items-center justify-center text-center py-10 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-indigo-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-lightbulb text-2xl text-indigo-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">
                    <?= $status === 'menunggu' ? 'Tidak ada usulan yang menunggu validasi' : 'Belum ada data inovasi' ?>
                </p>
                <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">
                    Usulan inovasi dari satuan pendidikan akan tampil di sini.
                </p>
            </div>
        <?php else : ?>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-100 uppercase text-slate-700 font-bold tracking-wider">
                        <tr>
                            <th class="p-3.5 rounded-l-lg">Judul Inovasi</th>
                            <th class="p-3.5">Satuan Pendidikan</th>
                            <th class="p-3.5">Pengusul</th>
                            <th class="p-3.5">Diajukan</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-center rounded-r-lg">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200">
                        <?php foreach ($inovasi as $i) : ?>
                            <tr class="hover:bg-slate-50 transition">
                                <td class="p-3.5 max-w-sm">
                                    <div class="font-bold text-slate-800"><?= esc($i['judul_inovasi']) ?></div>
                                    <div class="text-[11px] text-slate-400 line-clamp-1"><?= esc($i['deskripsi'] ?: '-') ?></div>
                                </td>
                                <td class="p-3.5">
                                    <?= esc($i['nama_sekolah'] ?? '-') ?>
                                    <div class="text-[11px] text-slate-400"><?= esc($i['kabupaten_kota'] ?? '') ?></div>
                                </td>
                                <td class="p-3.5"><?= esc($i['nama_guru'] ?? '-') ?></td>
                                <td class="p-3.5 whitespace-nowrap"><?= date('d/m/Y', strtotime($i['created_at'])) ?></td>
                                <td class="p-3.5">
                                    <span class="<?= $badge[$i['status_verifikasi']] ?? '' ?> text-[10px] font-bold px-2.5 py-1 rounded-full whitespace-nowrap">
                                        <i class="fa-solid <?= $ikonStatus[$i['status_verifikasi']] ?? '' ?> mr-1"></i><?= esc($label[$i['status_verifikasi']] ?? $i['status_verifikasi']) ?>
                                    </span>
                                </td>
                                <td class="p-3.5 text-center">
                                    <button type="button"
                                        data-inovasi="<?= esc(json_encode($i), 'attr') ?>"
                                        onclick="bukaDetail(JSON.parse(this.dataset.inovasi))"
                                        class="<?= $i['status_verifikasi'] === 'menunggu' ? 'bg-brand-600 hover:bg-brand-700 text-white' : 'border border-slate-200 hover:bg-slate-50 text-slate-600' ?> text-[11px] px-3 py-1.5 rounded-lg font-semibold whitespace-nowrap">
                                        <i class="fa-solid <?= $i['status_verifikasi'] === 'menunggu' ? 'fa-clipboard-check' : 'fa-eye' ?> mr-1"></i>
                                        <?= $i['status_verifikasi'] === 'menunggu' ? 'Periksa' : 'Detail' ?>
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

<!-- =====================================================
     MODAL DETAIL INOVASI
     ===================================================== -->
<div id="modalDetailInovasi" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-3xl w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">

        <!-- Kepala -->
        <div class="flex justify-between items-start gap-4 border-b p-5">
            <div>
                <span id="d_status" class="text-[10px] font-bold px-2.5 py-1 rounded-full"></span>
                <h3 id="d_judul" class="font-bold text-slate-800 text-base mt-2 leading-snug"></h3>
                <p class="text-slate-400 mt-0.5">Diajukan <span id="d_tanggal"></span></p>
            </div>
            <button type="button" onclick="closeModal('modalDetailInovasi')" class="text-slate-400 hover:text-slate-600">
                <i class="fa-solid fa-xmark text-lg"></i>
            </button>
        </div>

        <!-- Isi (bisa di-scroll) -->
        <div class="overflow-y-auto p-5 space-y-5">

            <!-- Deskripsi -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-lightbulb text-indigo-500 mr-1"></i> Deskripsi Inovasi
                </h4>
                <div id="d_deskripsi" class="bg-slate-50 border rounded-xl p-4 text-slate-700 leading-relaxed whitespace-pre-line"></div>
            </div>

            <!-- Pengusul & asal -->
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

            <!-- Praktik baik asal -->
            <div id="d_praktik_wrap" class="bg-blue-50 border border-blue-200 rounded-xl p-3.5 flex items-center gap-2">
                <i class="fa-solid fa-book-open text-blue-600"></i>
                <span class="text-slate-600">Dikembangkan dari praktik baik:</span>
                <b id="d_praktik" class="text-blue-800"></b>
            </div>

            <!-- Lampiran -->
            <div>
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-paperclip text-slate-500 mr-1"></i> Lampiran Dokumen
                </h4>
                <div id="d_lampiran" class="grid grid-cols-1 md:grid-cols-2 gap-2"></div>
            </div>

            <!-- Riwayat validasi -->
            <div id="d_riwayat" class="border rounded-xl p-4 space-y-1.5">
                <h4 class="font-bold text-slate-700 mb-2 uppercase tracking-wider text-[11px]">
                    <i class="fa-solid fa-clock-rotate-left text-slate-500 mr-1"></i> Riwayat Validasi
                </h4>
                <div><span class="text-slate-400 w-24 inline-block">Divalidasi oleh</span> <b id="d_verifikator"></b></div>
                <div><span class="text-slate-400 w-24 inline-block">Tanggal</span> <span id="d_tgl_verifikasi"></span></div>
                <div id="d_catatan_wrap"><span class="text-slate-400 w-24 inline-block align-top">Catatan</span> <span id="d_catatan" class="whitespace-pre-line"></span></div>
            </div>
        </div>

        <!-- Kaki: form validasi (hanya kalau masih menunggu) -->
        <form id="d_form" method="post" class="border-t p-5 space-y-3 bg-slate-50 rounded-b-2xl">
            <?= csrf_field() ?>
            <input type="hidden" name="aksi" id="d_aksi">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Catatan Validasi</label>
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
                    <i class="fa-solid fa-check mr-1"></i> Setujui & Masukkan ke Bank Inovasi
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    const LABEL_STATUS = <?= json_encode($label) ?>;
    const BADGE_STATUS = <?= json_encode($badge) ?>;
    const URL_VERIFIKASI = '<?= site_url('bank-inovasi/verifikasi') ?>/';

    // Format tanggal: 2026-09-28 10:15:00 -> 28/09/2026
    function formatTanggal(t) {
        if (!t) return '-';
        const d = new Date(t.replace(' ', 'T'));
        return d.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: 'long',
            year: 'numeric'
        });
    }

    // Isi teks dengan aman (tidak dieksekusi sebagai HTML)
    function isi(id, teks) {
        document.getElementById(id).textContent = teks || '-';
    }

    function bukaDetail(d) {
        const st = d.status_verifikasi;

        const badge = document.getElementById('d_status');
        badge.className = 'text-[10px] font-bold px-2.5 py-1 rounded-full ' + (BADGE_STATUS[st] || '');
        badge.textContent = LABEL_STATUS[st] || st;

        isi('d_judul', d.judul_inovasi);
        isi('d_tanggal', formatTanggal(d.created_at));
        isi('d_deskripsi', d.deskripsi || 'Belum ada deskripsi.');
        isi('d_guru', d.nama_guru);
        isi('d_nip', d.nip);
        isi('d_mapel', d.mapel);
        isi('d_sekolah', d.nama_sekolah);
        isi('d_npsn', d.npsn);
        isi('d_kab', d.kabupaten_kota);

        // Praktik baik asal
        document.getElementById('d_praktik_wrap').classList.toggle('hidden', !d.judul_praktik);
        isi('d_praktik', d.judul_praktik);

        // Lampiran
        const wadah = document.getElementById('d_lampiran');
        wadah.innerHTML = '';
        if (!d.lampiran || d.lampiran.length === 0) {
            wadah.innerHTML = '<p class="text-slate-400 italic">Tidak ada lampiran.</p>';
        } else {
            const ikon = {
                foto: 'fa-image',
                video: 'fa-video',
                tautan_publikasi: 'fa-link'
            };
            d.lampiran.forEach(f => {
                const a = document.createElement(f.url ? 'a' : 'div');
                if (f.url) {
                    a.href = f.url;
                    a.target = '_blank';
                }
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
                jenis.textContent = (f.jenis_dokumen || '').replaceAll('_', ' ');
                teks.append(nama, jenis);

                a.append(i, teks);
                wadah.appendChild(a);
            });
        }

        // Riwayat (hanya kalau sudah divalidasi)
        document.getElementById('d_riwayat').classList.toggle('hidden', st === 'menunggu');
        isi('d_verifikator', d.nama_verifikator);
        isi('d_tgl_verifikasi', formatTanggal(d.tanggal_verifikasi));
        document.getElementById('d_catatan_wrap').classList.toggle('hidden', !d.catatan_verifikasi);
        isi('d_catatan', d.catatan_verifikasi);

        // Form validasi (hanya kalau masih menunggu)
        const form = document.getElementById('d_form');
        form.classList.toggle('hidden', st !== 'menunggu');
        form.action = URL_VERIFIKASI + d.id_inovasi;
        document.getElementById('d_catatan_input').value = '';

        openModal('modalDetailInovasi');
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