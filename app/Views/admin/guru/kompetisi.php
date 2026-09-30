<?php

/** @var string $tab */
/** @var array $jumlahTab */
/** @var array $label */
/** @var array|null $kompetisi */
/** @var array|null $praktik */
/** @var array|null $riwayat */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$bulan = [1 => 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
$tgl   = fn($d) => $d ? date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d)) : '-';
$angka = fn($n) => number_format((float) $n, 2, ',', '.');

// Sisa hari pendaftaran
$sisaHari = function ($tanggal) {
    $sisa = (int) floor((strtotime($tanggal . ' 23:59:59') - time()) / 86400);

    return $sisa < 0 ? 'Ditutup' : ($sisa === 0 ? 'Hari terakhir!' : $sisa . ' hari lagi');
};

$medali = [
    1 => 'bg-amber-100 text-amber-700 border-amber-300',
    2 => 'bg-slate-200 text-slate-700 border-slate-300',
    3 => 'bg-orange-100 text-orange-700 border-orange-300',
];
$badgeTahap = [
    'pendaftaran' => 'bg-blue-100 text-blue-700',
    'berlangsung' => 'bg-amber-100 text-amber-800',
    'selesai'     => 'bg-emerald-100 text-emerald-800',
];
?>
<section class="space-y-6">

    <div class="bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <h2 class="text-xl font-bold text-slate-800">Kompetisi Inovasi</h2>
        <p class="text-xs text-slate-500">Daftarkan karya inovasi Anda pada lomba yang sedang dibuka, dan pantau hasil lomba yang Anda ikuti.</p>
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
        <?php foreach (['buka' => ['Pendaftaran Dibuka', 'fa-door-open'], 'saya' => ['Lomba Saya', 'fa-user-check']] as $kunci => [$teks, $ikon]) : ?>
            <a href="<?= site_url('kompetisi') . ($kunci === 'saya' ? '?tab=saya' : '') ?>"
                class="px-4 py-2 rounded-xl border flex items-center gap-2 transition
                       <?= $tab === $kunci ? 'bg-brand-600 border-brand-600 text-white shadow-sm' : 'bg-white border-slate-200 text-slate-600 hover:bg-slate-50' ?>">
                <i class="fa-solid <?= $ikon ?>"></i><?= $teks ?>
                <span class="<?= $tab === $kunci ? 'bg-white/25' : 'bg-slate-100' ?> text-[10px] px-1.5 rounded-full"><?= $jumlahTab[$kunci] ?></span>
            </a>
        <?php endforeach; ?>
    </div>

    <?php if ($tab === 'buka') : ?>
        <!-- ================= PENDAFTARAN DIBUKA ================= -->
        <?php if (empty($kompetisi)) : ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-trophy text-2xl text-amber-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">Belum ada lomba yang membuka pendaftaran</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Pengumuman lomba baru dari Dinas akan tampil di sini dan di Beranda.</p>
            </div>
        <?php else : ?>
            <div class="space-y-5">
                <?php foreach ($kompetisi as $k) : ?>
                    <?php $karya = $k['karya']; ?>
                    <div class="bg-white rounded-2xl border <?= $karya ? 'border-emerald-300' : 'border-slate-200' ?> shadow-sm overflow-hidden text-xs">

                        <div class="p-5 space-y-3">
                            <div class="flex flex-wrap justify-between items-start gap-3">
                                <div class="min-w-0">
                                    <div class="flex flex-wrap gap-2">
                                        <span class="bg-blue-100 text-blue-700 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-door-open mr-1"></i>Pendaftaran Dibuka</span>
                                        <span class="bg-red-50 text-red-600 text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-regular fa-clock mr-1"></i><?= $sisaHari($k['tanggal_selesai']) ?></span>
                                    </div>
                                    <h3 class="font-bold text-slate-800 text-base mt-2"><?= esc($k['nama_kompetisi']) ?></h3>
                                    <p class="text-[11px] text-slate-500"><i class="fa-regular fa-calendar mr-1"></i><?= $tgl($k['tanggal_mulai']) ?> – <?= $tgl($k['tanggal_selesai']) ?> • <?= $k['jumlah'] ?> karya terdaftar</p>
                                </div>

                                <?php if (! $karya) : ?>
                                    <button type="button" data-k="<?= esc(json_encode($k), 'attr') ?>" onclick="bukaDaftar(JSON.parse(this.dataset.k))"
                                        class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-4 py-2.5 rounded-xl shadow flex items-center">
                                        <i class="fa-solid fa-paper-plane mr-2"></i>Daftarkan Karya
                                    </button>
                                <?php endif; ?>
                            </div>

                            <?php if ($k['deskripsi']) : ?>
                                <div class="bg-slate-50 border rounded-xl p-3.5 text-slate-600 whitespace-pre-line leading-relaxed"><?= esc($k['deskripsi']) ?></div>
                            <?php endif; ?>

                            <details class="group">
                                <summary class="cursor-pointer text-brand-600 font-semibold list-none flex items-center gap-1">
                                    <i class="fa-solid fa-chevron-right text-[10px] transition group-open:rotate-90"></i>
                                    Lihat kategori (<?= count($k['kategori']) ?>) & kriteria penilaian (<?= count($k['kriteria']) ?>)
                                </summary>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-3">
                                    <div>
                                        <div class="font-bold text-slate-600 uppercase text-[10px] tracking-wider mb-2">Kategori Lomba</div>
                                        <ul class="space-y-1.5">
                                            <?php foreach ($k['kategori'] as $kat) : ?>
                                                <li class="flex gap-2"><i class="fa-solid fa-tag text-indigo-400 mt-0.5"></i><span><?= esc($kat['nama_kategori']) ?></span></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <div>
                                        <div class="font-bold text-slate-600 uppercase text-[10px] tracking-wider mb-2">Kriteria Penilaian Juri</div>
                                        <ul class="space-y-1.5">
                                            <?php foreach ($k['kriteria'] as $kr) : ?>
                                                <li class="flex justify-between gap-2" title="<?= esc($kr['keterangan'] ?? '') ?>">
                                                    <span><?= esc($kr['nama_kriteria']) ?></span>
                                                    <span class="font-bold text-slate-700 shrink-0"><?= (int) $kr['skor_maks'] ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </details>
                        </div>

                        <?php if ($karya) : ?>
                            <?php
                            $namaKategori = '-';
                            foreach ($k['kategori'] as $kat) {
                                if ((int) $kat['id_kategori'] === (int) $karya['id_kategori']) {
                                    $namaKategori = $kat['nama_kategori'];
                                }
                            }
                            ?>
                            <div class="bg-emerald-50 border-t border-emerald-200 p-5 space-y-3">
                                <div class="flex flex-wrap justify-between items-start gap-3">
                                    <div class="min-w-0">
                                        <span class="bg-emerald-600 text-white text-[10px] font-bold px-2.5 py-1 rounded-full"><i class="fa-solid fa-check mr-1"></i>Sudah terdaftar</span>
                                        <div class="font-bold text-slate-800 text-sm mt-2"><?= esc($karya['judul_karya']) ?></div>
                                        <div class="text-[11px] text-slate-500"><i class="fa-solid fa-tag mr-1"></i><?= esc($namaKategori) ?> • didaftarkan <?= $tgl($karya['created_at']) ?></div>
                                    </div>
                                    <?php if ($karya['status_validasi'] !== 'tervalidasi') : ?>
                                        <div class="flex gap-1.5">
                                            <button type="button" data-k="<?= esc(json_encode($k), 'attr') ?>" onclick="bukaDaftar(JSON.parse(this.dataset.k))"
                                                class="h-8 px-3 rounded-lg border border-emerald-300 bg-white hover:bg-emerald-100 text-emerald-700 font-semibold flex items-center">
                                                <i class="fa-solid fa-pen mr-1.5"></i>Ubah Karya
                                            </button>
                                            <button type="button"
                                                onclick="konfirmasiBatal('<?= site_url('kompetisi/batal/' . $k['id_kompetisi']) ?>', <?= esc(json_encode($k['nama_kompetisi']), 'attr') ?>)"
                                                class="h-8 px-3 rounded-lg border border-red-200 bg-white hover:bg-red-50 text-red-600 font-semibold flex items-center">
                                                <i class="fa-solid fa-xmark mr-1.5"></i>Batalkan
                                            </button>
                                        </div>
                                    <?php else : ?>
                                        <span class="text-[11px] text-emerald-700 font-semibold"><i class="fa-solid fa-lock mr-1"></i>Sudah divalidasi</span>
                                    <?php endif; ?>
                                </div>
                                <p class="text-slate-600 line-clamp-2"><?= esc($karya['deskripsi_karya']) ?></p>
                                <?php if (preg_match('#^https?://#i', (string) $karya['link_video'])) : ?>
                                    <a href="<?= esc($karya['link_video'], 'attr') ?>" target="_blank" rel="noopener" class="inline-flex items-center text-red-600 font-semibold hover:underline">
                                        <i class="fa-brands fa-youtube mr-1.5"></i>Lihat video karya
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

    <?php else : ?>
        <!-- ================= LOMBA SAYA ================= -->
        <?php if (empty($riwayat)) : ?>
            <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
                <div class="relative w-16 h-16 mb-4">
                    <div class="absolute inset-0 rounded-2xl bg-brand-100 rotate-12"></div>
                    <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                        <i class="fa-solid fa-user-check text-2xl text-brand-500"></i>
                    </div>
                </div>
                <p class="text-sm font-semibold text-slate-700">Anda belum pernah mengikuti lomba</p>
                <p class="text-xs text-slate-400 mt-1 max-w-[300px] leading-relaxed">Lihat tab <b>Pendaftaran Dibuka</b> untuk mendaftarkan karya pertama Anda.</p>
            </div>
        <?php else : ?>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                <?php foreach ($riwayat as $r) : ?>
                    <?php $juara = $r['hasil_diumumkan'] && $r['peringkat']; ?>
                    <div class="bg-white rounded-2xl border <?= $juara ? 'border-amber-300' : 'border-slate-200' ?> shadow-sm p-5 space-y-3 text-xs">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div class="min-w-0">
                                <span class="<?= $badgeTahap[$r['status']] ?? 'bg-slate-100 text-slate-600' ?> text-[10px] font-bold px-2.5 py-1 rounded-full">
                                    <?= $r['hasil_diumumkan'] ? 'Hasil diumumkan' : esc($label[$r['status']] ?? $r['status']) ?>
                                </span>
                                <div class="text-[11px] text-slate-500 mt-2"><?= esc($r['nama_kompetisi']) ?> • <?= date('Y', strtotime($r['tanggal_mulai'])) ?></div>
                                <div class="font-bold text-slate-800 text-sm"><?= esc($r['judul_karya']) ?></div>
                                <div class="text-[11px] text-slate-400"><i class="fa-solid fa-tag mr-1"></i><?= esc($r['nama_kategori'] ?? '-') ?></div>
                            </div>
                            <?php if ($juara) : ?>
                                <div class="text-center shrink-0">
                                    <i class="fa-solid fa-trophy text-3xl <?= [1 => 'text-amber-400', 2 => 'text-slate-400', 3 => 'text-orange-400'][$r['peringkat']] ?? 'text-slate-300' ?>"></i>
                                    <div class="<?= $medali[$r['peringkat']] ?? '' ?> border text-[10px] font-bold px-2 py-0.5 rounded-full mt-1">Juara <?= $r['peringkat'] ?></div>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="flex flex-wrap justify-between items-center gap-2 border-t pt-3">
                            <?php if ($r['status_validasi'] === 'ditolak') : ?>
                                <span class="text-red-600 font-semibold"><i class="fa-solid fa-circle-xmark mr-1"></i>Karya tidak lolos validasi</span>
                            <?php elseif (! $r['hasil_diumumkan']) : ?>
                                <span class="text-blue-600 font-semibold">
                                    <i class="fa-regular fa-clock mr-1"></i><?= $r['status'] === 'pendaftaran' ? 'Menunggu penjurian dimulai' : ($r['status'] === 'berlangsung' ? 'Sedang dinilai juri' : 'Menunggu pengumuman hasil') ?>
                                </span>
                            <?php else : ?>
                                <span class="text-slate-500">Nilai akhir: <b class="text-base text-slate-800"><?= $angka($r['nilai']) ?></b> / 100</span>
                                <button type="button" data-r="<?= esc(json_encode($r), 'attr') ?>" onclick="lihatNilai(JSON.parse(this.dataset.r))"
                                    class="bg-brand-600 hover:bg-brand-700 text-white font-semibold px-3 py-1.5 rounded-lg flex items-center">
                                    <i class="fa-solid fa-chart-simple mr-1.5"></i>Detail Nilai
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</section>

<?php if ($tab === 'buka') : ?>
    <!-- =====================================================
         MODAL DAFTAR / UBAH KARYA
         ===================================================== -->
    <div id="modalDaftar" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <form action="<?= site_url('kompetisi/daftar') ?>" method="post"
            class="bg-white rounded-2xl max-w-2xl w-full shadow-2xl max-h-[92vh] flex flex-col text-xs">
            <?= csrf_field() ?>
            <input type="hidden" name="id_kompetisi" id="df_kompetisi">

            <div class="flex justify-between items-start border-b p-5">
                <div>
                    <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-trophy text-amber-500 mr-2"></i><span id="df_judulForm">Daftarkan Karya</span></h3>
                    <p id="df_namaLomba" class="text-slate-500 mt-0.5"></p>
                </div>
                <button type="button" onclick="closeModal('modalDaftar')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="overflow-y-auto p-5 space-y-4">
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Kategori Lomba <span class="text-red-500">*</span></label>
                    <select name="id_kategori" id="df_kategori" required class="w-full border border-slate-300 rounded-lg p-2.5"></select>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Judul Karya <span class="text-red-500">*</span></label>
                    <input type="text" name="judul_karya" id="df_judul" required maxlength="200" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Deskripsi Karya <span class="text-red-500">*</span></label>
                    <textarea name="deskripsi_karya" id="df_deskripsi" rows="5" required minlength="30"
                        placeholder="Jelaskan masalah yang diselesaikan, cara kerja inovasi, dan dampaknya bagi siswa..."
                        class="w-full border border-slate-300 rounded-lg p-2.5"></textarea>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Link Video Karya <span class="text-red-500">*</span></label>
                    <input type="url" name="link_video" id="df_video" required placeholder="https://youtu.be/..." class="w-full border border-slate-300 rounded-lg p-2.5">
                    <p class="text-[10px] text-slate-400 mt-1">Unggah video ke YouTube atau Google Drive (akses publik), lalu tempel tautannya di sini. Perhatikan ketentuan durasi pada deskripsi lomba.</p>
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Praktik Baik Terkait</label>
                    <select name="id_praktik_baik" id="df_praktik" class="w-full border border-slate-300 rounded-lg p-2.5">
                        <option value="">Tidak ada</option>
                        <?php foreach ($praktik as $pr) : ?>
                            <option value="<?= $pr['id_praktik_baik'] ?>"><?= esc($pr['judul']) ?></option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1">Opsional. Hanya praktik baik Anda yang sudah disetujui Dinas.</p>
                </div>
                <div class="bg-amber-50 border border-amber-200 text-amber-800 rounded-xl p-3">
                    <i class="fa-solid fa-circle-info mr-1"></i>Setiap guru hanya bisa mendaftarkan <b>satu karya</b> per lomba. Karya masih bisa diubah atau dibatalkan selama pendaftaran dibuka.
                </div>
            </div>

            <div class="flex justify-end space-x-2 border-t p-5 bg-slate-50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modalDaftar')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-paper-plane mr-1"></i> <span id="df_tombol">Daftarkan</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Modal konfirmasi batal -->
    <div id="modalBatal" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-2xl text-center space-y-4">
            <div class="mx-auto w-14 h-14 rounded-full flex items-center justify-center bg-red-100 text-red-600"><i class="fa-solid fa-xmark text-2xl"></i></div>
            <div>
                <h3 class="font-bold text-slate-800 text-base">Batalkan pendaftaran?</h3>
                <p id="bt_nama" class="text-xs text-slate-500 mt-1"></p>
                <p class="text-[11px] text-slate-400 mt-1">Anda masih bisa mendaftar lagi selama pendaftaran dibuka.</p>
            </div>
            <form id="bt_form" method="post" class="flex justify-center space-x-2 pt-2">
                <?= csrf_field() ?>
                <button type="button" onclick="closeModal('modalBatal')" class="px-4 py-2 border rounded-lg text-xs font-semibold text-slate-600 hover:bg-slate-50">Tidak</button>
                <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg text-xs font-semibold">Ya, Batalkan</button>
            </form>
        </div>
    </div>

    <script>
        function bukaDaftar(k) {
            const karya = k.karya;
            document.getElementById('df_kompetisi').value = k.id_kompetisi;
            document.getElementById('df_namaLomba').textContent = k.nama_kompetisi;
            document.getElementById('df_judulForm').textContent = karya ? 'Ubah Karya' : 'Daftarkan Karya';
            document.getElementById('df_tombol').textContent = karya ? 'Simpan Perubahan' : 'Daftarkan';

            const sel = document.getElementById('df_kategori');
            sel.innerHTML = '';
            const kosong = document.createElement('option');
            kosong.value = '';
            kosong.textContent = 'Pilih kategori';
            sel.appendChild(kosong);
            (k.kategori || []).forEach(kat => {
                const o = document.createElement('option');
                o.value = kat.id_kategori;
                o.textContent = kat.nama_kategori;
                sel.appendChild(o);
            });
            sel.value = karya?.id_kategori || '';

            document.getElementById('df_judul').value = karya?.judul_karya || '';
            document.getElementById('df_deskripsi').value = karya?.deskripsi_karya || '';
            document.getElementById('df_video').value = karya?.link_video || '';
            document.getElementById('df_praktik').value = karya?.id_praktik_baik || '';

            openModal('modalDaftar');
        }

        function konfirmasiBatal(url, nama) {
            document.getElementById('bt_form').action = url;
            document.getElementById('bt_nama').textContent = nama;
            openModal('modalBatal');
        }
    </script>
<?php endif; ?>

<?php if ($tab === 'saya') : ?>
    <!-- =====================================================
         MODAL DETAIL NILAI
         ===================================================== -->
    <div id="modalNilai" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
            <div class="flex justify-between items-start gap-4 border-b p-5">
                <div class="flex items-center gap-3">
                    <div id="n_piala" class="w-12 h-12 rounded-2xl flex items-center justify-center shrink-0"><i class="fa-solid fa-trophy text-xl"></i></div>
                    <div>
                        <div id="n_hasil" class="text-[11px] font-bold uppercase tracking-wider"></div>
                        <h3 id="n_judul" class="font-bold text-slate-800 text-base leading-snug"></h3>
                        <p id="n_sub" class="text-slate-400"></p>
                    </div>
                </div>
                <button type="button" onclick="closeModal('modalNilai')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
            </div>

            <div class="overflow-y-auto p-5 space-y-4">
                <div class="bg-slate-50 border rounded-xl p-4 flex justify-between items-center">
                    <span class="font-semibold text-slate-600">Nilai Akhir</span>
                    <span><b id="n_total" class="text-3xl font-extrabold text-slate-800"></b><span class="text-slate-400 font-semibold ml-1">/ 100</span></span>
                </div>
                <div>
                    <h4 class="font-bold text-slate-700 mb-3 uppercase tracking-wider text-[11px]"><i class="fa-solid fa-list-check text-brand-600 mr-1"></i> Rincian per Kriteria</h4>
                    <div id="n_rincian" class="space-y-3"></div>
                </div>
                <p id="n_catatan" class="text-[10px] text-slate-400"></p>
            </div>

            <div class="flex justify-end border-t p-4 bg-slate-50 rounded-b-2xl">
                <button type="button" onclick="closeModal('modalNilai')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Tutup</button>
            </div>
        </div>
    </div>

    <script>
        function lihatNilai(r) {
            const buat = (tag, kelas, teks) => {
                const el = document.createElement(tag);
                if (kelas) el.className = kelas;
                if (teks !== undefined) el.textContent = teks;
                return el;
            };
            const angka = n => parseFloat(n).toFixed(2).replace('.', ',');
            const warnaPiala = {
                1: 'bg-gradient-to-br from-amber-300 to-yellow-500 text-white',
                2: 'bg-gradient-to-br from-slate-300 to-slate-500 text-white',
                3: 'bg-gradient-to-br from-orange-300 to-orange-600 text-white',
            };
            const juara = parseInt(r.peringkat) || 0;

            document.getElementById('n_piala').className = 'w-12 h-12 rounded-2xl flex items-center justify-center shrink-0 ' + (warnaPiala[juara] || 'bg-slate-100 text-slate-400');
            const hasil = document.getElementById('n_hasil');
            hasil.textContent = juara ? 'Juara ' + juara : 'Peserta';
            hasil.className = 'text-[11px] font-bold uppercase tracking-wider ' + ({
                1: 'text-amber-600',
                2: 'text-slate-500',
                3: 'text-orange-600'
            } [juara] || 'text-slate-400');
            document.getElementById('n_judul').textContent = r.judul_karya;
            document.getElementById('n_sub').textContent = r.nama_kompetisi + (r.nama_kategori ? ' • ' + r.nama_kategori : '');
            document.getElementById('n_total').textContent = angka(r.nilai || 0);

            const wadah = document.getElementById('n_rincian');
            wadah.innerHTML = '';
            const rincian = r.rincian || [];
            if (!rincian.length) {
                wadah.appendChild(buat('p', 'text-slate-400 italic', 'Rincian nilai belum tersedia.'));
            }
            rincian.forEach(k => {
                const persen = Math.round(k.skor / k.skor_maks * 100);
                const baris = buat('div');
                const atas = buat('div', 'flex justify-between mb-1');
                atas.append(
                    buat('span', 'text-slate-700', k.kriteria),
                    buat('span', 'font-semibold text-slate-800', angka(k.skor) + ' / ' + k.skor_maks)
                );
                const bar = buat('div', 'h-2 bg-slate-100 rounded-full overflow-hidden');
                const isi = buat('div', 'h-full rounded-full ' + (persen >= 80 ? 'bg-emerald-500' : persen >= 60 ? 'bg-amber-500' : 'bg-red-400'));
                isi.style.width = persen + '%';
                bar.appendChild(isi);
                baris.append(atas, bar);
                wadah.appendChild(baris);
            });

            const jumlahJuri = rincian.length ? Math.max(...rincian.map(k => k.juri)) : 0;
            document.getElementById('n_catatan').textContent = jumlahJuri ?
                'Skor setiap kriteria adalah rata-rata dari ' + jumlahJuri + ' juri. Hijau = kuat (≥80%), kuning = cukup (60–79%), merah = perlu ditingkatkan (<60%).' :
                '';

            openModal('modalNilai');
        }
    </script>
<?php endif; ?>
<?= $this->endSection() ?>