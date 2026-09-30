<?php

/** @var array $juri */
/** @var array $kompetisi */
/** @var array $tanpaJuri */
/** @var string $q */
/** @var array $ringkas */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errors   = session()->getFlashdata('errors') ?? [];
$akunBaru = session()->getFlashdata('akunBaru');
$labelTahap = ['draft' => 'Draft', 'pendaftaran' => 'Pendaftaran', 'berlangsung' => 'Penjurian', 'selesai' => 'Selesai'];
$warnaTahap = ['draft' => 'bg-slate-100 text-slate-600', 'pendaftaran' => 'bg-blue-100 text-blue-700', 'berlangsung' => 'bg-amber-100 text-amber-800', 'selesai' => 'bg-emerald-100 text-emerald-800'];
$inisial = function ($nama) {
    $bagian = preg_split('/\s+/', trim(preg_replace('/^(Dr|Prof|Drs|Ir|H|Hj)\.?\s+/i', '', $nama)));

    return strtoupper(mb_substr($bagian[0] ?? '', 0, 1) . mb_substr($bagian[1] ?? '', 0, 1));
};
?>
<section class="space-y-6">

    <div class="flex flex-wrap justify-between items-center gap-4 bg-white p-5 rounded-2xl border border-slate-200 shadow-sm">
        <div>
            <h2 class="text-xl font-bold text-slate-800">Tim Juri</h2>
            <p class="text-xs text-slate-500">Kelola akun juri dan tentukan kategori lomba yang dinilai setiap juri.</p>
        </div>
        <button type="button" onclick="bukaFormJuri()"
            class="bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow flex items-center">
            <i class="fa-solid fa-user-plus mr-2"></i>Tambah Juri
        </button>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <?php foreach ([['Total Juri', $ringkas['total'], 'fa-users', 'bg-amber-50 text-amber-600'], ['Juri Aktif', $ringkas['aktif'], 'fa-user-check', 'bg-emerald-50 text-emerald-600'], ['Sudah Ditugaskan', $ringkas['bertugas'], 'fa-list-check', 'bg-indigo-50 text-indigo-600']] as [$judul, $angka, $ikon, $warna]) : ?>
            <div class="bg-white p-4 rounded-xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="p-3 rounded-xl <?= $warna ?>"><i class="fa-solid <?= $ikon ?> text-xl"></i></div>
                <div>
                    <div class="text-[11px] text-slate-500 font-medium uppercase tracking-wider"><?= $judul ?></div>
                    <div class="text-xl font-bold text-slate-800"><?= $angka ?></div>
                </div>
            </div>
        <?php endforeach; ?>
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

    <!-- Peringatan kategori tanpa juri -->
    <?php if ($tanpaJuri) : ?>
        <div class="bg-amber-50 border border-amber-300 text-amber-900 text-xs px-4 py-3 rounded-xl">
            <p class="font-bold"><i class="fa-solid fa-triangle-exclamation mr-2"></i><?= count($tanpaJuri) ?> kategori lomba belum memiliki juri:</p>
            <ul class="list-disc list-inside mt-1 space-y-0.5">
                <?php foreach ($tanpaJuri as $t) : ?><li><?= esc($t) ?></li><?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- Info akun (tampil sekali) -->
    <?php if ($akunBaru) : ?>
        <div class="bg-white border-2 border-emerald-300 rounded-2xl p-5 shadow-sm text-xs space-y-3">
            <div class="flex flex-wrap justify-between items-start gap-3">
                <div>
                    <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-key text-emerald-600 mr-2"></i><?= $akunBaru['jenis'] === 'reset' ? 'Password Berhasil Direset' : 'Akun Juri Berhasil Dibuat' ?></h3>
                    <p class="text-[11px] text-amber-700 mt-1"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Password hanya ditampilkan sekali. Salin dan berikan kepada juri yang bersangkutan.</p>
                </div>
                <button type="button" onclick="salinAkun(this)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-4 py-2 rounded-lg">
                    <i class="fa-regular fa-copy mr-2"></i>Salin Info Akun
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase">Nama</div>
                    <div class="font-semibold text-slate-800"><?= esc($akunBaru['nama']) ?></div>
                </div>
                <div class="bg-slate-50 rounded-lg p-3">
                    <div class="text-[10px] text-slate-400 uppercase">Username</div>
                    <div class="font-mono font-semibold text-slate-800"><?= esc($akunBaru['username']) ?></div>
                </div>
                <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-3">
                    <div class="text-[10px] text-emerald-600 uppercase">Password</div>
                    <div class="font-mono font-bold text-emerald-800"><?= esc($akunBaru['password']) ?></div>
                </div>
            </div>
            <textarea id="teksAkun" class="hidden"><?= esc(
                                                        "Akun EDUVATION - Tim Juri\n" .
                                                            "Nama     : {$akunBaru['nama']}\n" .
                                                            "Username : {$akunBaru['username']}\n" .
                                                            "Password : {$akunBaru['password']}\n" .
                                                            "Login di : " . site_url('login') . "\n\n" .
                                                            "Segera ganti password setelah login pertama melalui menu Profil Saya."
                                                    ) ?></textarea>
        </div>
    <?php endif; ?>

    <!-- Pencarian -->
    <form method="get" action="<?= site_url('juri') ?>" class="flex gap-2 text-xs">
        <input type="text" name="q" value="<?= esc($q) ?>" placeholder="Cari nama, username, atau instansi juri..."
            class="flex-1 border border-slate-300 rounded-lg p-2.5 bg-white focus:ring-2 focus:ring-brand-500 focus:outline-none">
        <button type="submit" class="bg-slate-800 hover:bg-slate-700 text-white px-4 rounded-lg"><i class="fa-solid fa-magnifying-glass"></i></button>
        <?php if ($q !== '') : ?>
            <a href="<?= site_url('juri') ?>" class="border border-slate-300 bg-white hover:bg-slate-50 text-slate-600 px-3 rounded-lg flex items-center"><i class="fa-solid fa-rotate-left"></i></a>
        <?php endif; ?>
    </form>

    <?php if (empty($juri)) : ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm flex flex-col items-center justify-center text-center py-12 px-4">
            <div class="relative w-16 h-16 mb-4">
                <div class="absolute inset-0 rounded-2xl bg-amber-100 rotate-12"></div>
                <div class="absolute inset-0 rounded-2xl bg-white border border-slate-200 shadow-sm -rotate-3 flex items-center justify-center">
                    <i class="fa-solid fa-gavel text-2xl text-amber-500"></i>
                </div>
            </div>
            <p class="text-sm font-semibold text-slate-700"><?= $q !== '' ? 'Juri tidak ditemukan' : 'Belum ada juri' ?></p>
            <p class="text-xs text-slate-400 mt-1 max-w-[280px] leading-relaxed">Tambahkan akun juri, lalu tugaskan kategori lomba yang akan dinilainya.</p>
        </div>
    <?php else : ?>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            <?php foreach ($juri as $j) : ?>
                <?php $dataJuri = esc(json_encode($j), 'attr'); ?>
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 space-y-4 text-xs <?= $j['status'] === 'nonaktif' ? 'opacity-60' : '' ?>">
                    <div class="flex items-start gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-amber-400 to-orange-500 text-white font-bold text-base flex items-center justify-center shrink-0">
                            <?= esc($inisial($j['nama_admin'])) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="font-bold text-slate-800 text-sm"><?= esc($j['nama_admin']) ?></span>
                                <span class="<?= $j['status'] === 'aktif' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-600' ?> text-[10px] font-bold px-2 py-0.5 rounded-full"><?= $j['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' ?></span>
                            </div>
                            <div class="text-slate-500 truncate"><?= esc($j['keterangan'] ?: 'Instansi/keahlian belum diisi') ?></div>
                            <div class="text-[11px] text-slate-400 font-mono">@<?= esc($j['username']) ?><?= $j['email'] ? ' • ' . esc($j['email']) : '' ?></div>
                        </div>
                        <div class="text-center shrink-0">
                            <div class="text-lg font-extrabold text-slate-800"><?= $j['dinilai'] ?></div>
                            <div class="text-[10px] text-slate-400">karya dinilai</div>
                        </div>
                    </div>

                    <!-- Penugasan -->
                    <div class="bg-slate-50 rounded-xl p-3 space-y-2">
                        <div class="font-bold text-slate-600 text-[10px] uppercase tracking-wider"><i class="fa-solid fa-list-check mr-1"></i>Kategori yang Dinilai</div>
                        <?php if (empty($j['tugas'])) : ?>
                            <p class="text-slate-400 italic">Belum ditugaskan.</p>
                        <?php else : ?>
                            <?php foreach ($j['tugas'] as $t) : ?>
                                <div>
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="font-semibold text-slate-700"><?= esc($t['nama']) ?></span>
                                        <span class="<?= $warnaTahap[$t['status']] ?? '' ?> text-[9px] font-bold px-1.5 py-0.5 rounded-full"><?= $labelTahap[$t['status']] ?? $t['status'] ?></span>
                                    </div>
                                    <div class="flex flex-wrap gap-1.5">
                                        <?php foreach ($t['kategori'] as $kat) : ?>
                                            <span class="bg-white border border-indigo-200 text-indigo-700 text-[10px] font-semibold px-2 py-0.5 rounded-full"><i class="fa-solid fa-tag mr-1"></i><?= esc($kat['nama']) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>

                    <!-- Aksi -->
                    <div class="flex flex-wrap gap-1.5 justify-end">
                        <button type="button" data-j="<?= $dataJuri ?>" onclick="bukaTugas(JSON.parse(this.dataset.j))"
                            class="h-8 px-3 rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold flex items-center">
                            <i class="fa-solid fa-list-check mr-1.5"></i>Atur Tugas
                        </button>
                        <button type="button" data-j="<?= $dataJuri ?>" onclick="bukaFormJuri(JSON.parse(this.dataset.j))" title="Edit"
                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-brand-50 hover:text-brand-600 flex items-center justify-center">
                            <i class="fa-solid fa-pen text-[11px]"></i>
                        </button>
                        <button type="button" data-j="<?= $dataJuri ?>" onclick="bukaReset(JSON.parse(this.dataset.j))" title="Reset password"
                            class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-indigo-50 hover:text-indigo-600 flex items-center justify-center">
                            <i class="fa-solid fa-key text-[11px]"></i>
                        </button>
                        <form action="<?= site_url('juri/status/' . $j['id_admin']) ?>" method="post"
                            onsubmit="return confirm('<?= $j['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> juri ini?')">
                            <?= csrf_field() ?>
                            <button type="submit" title="<?= $j['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center">
                                <i class="fa-solid <?= $j['status'] === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</section>

<!-- =====================================================
     MODAL TAMBAH / EDIT JURI
     ===================================================== -->
<div id="modalJuri" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form action="<?= site_url('juri/simpan') ?>" method="post" autocomplete="off" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <?= csrf_field() ?>
        <input type="hidden" name="id_admin" id="j_id">
        <div class="flex justify-between items-center border-b pb-3">
            <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-gavel text-amber-500 mr-2"></i><span id="j_judul">Tambah Juri</span></h3>
            <button type="button" onclick="closeModal('modalJuri')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <?php if ($errors) : ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                <ul class="list-disc list-inside"><?php foreach ($errors as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
            </div>
        <?php endif; ?>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
            <input type="text" name="nama_admin" id="j_nama" required class="w-full border border-slate-300 rounded-lg p-2.5">
        </div>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Instansi / Keahlian</label>
            <input type="text" name="keterangan" id="j_keterangan" maxlength="150" placeholder="contoh: Dosen Teknologi Pendidikan, UHO" class="w-full border border-slate-300 rounded-lg p-2.5">
        </div>
        <div>
            <label class="font-semibold block mb-1 text-slate-700">Email</label>
            <input type="email" name="email" id="j_email" class="w-full border border-slate-300 rounded-lg p-2.5">
        </div>
        <div id="j_blokAkun" class="space-y-3 border-t pt-3">
            <div>
                <label class="font-semibold block mb-1 text-slate-700">Username <span class="text-red-500">*</span></label>
                <input type="text" name="username" id="j_username" placeholder="contoh: juri_hasniah" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono lowercase">
            </div>
            <div>
                <div class="flex justify-between items-center mb-1">
                    <label class="font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                    <button type="button" onclick="buatSandi('j_password')" class="text-[10px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis</button>
                </div>
                <input type="password" name="password" id="j_password" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
            </div>
        </div>
        <div class="flex justify-end space-x-2 border-t pt-3">
            <button type="button" onclick="closeModal('modalJuri')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan</button>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL ATUR TUGAS
     ===================================================== -->
<div id="modalTugas" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form id="t_form" method="post" class="bg-white rounded-2xl max-w-lg w-full shadow-2xl max-h-[90vh] flex flex-col text-xs">
        <?= csrf_field() ?>
        <div class="flex justify-between items-start border-b p-5">
            <div>
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-list-check text-indigo-600 mr-2"></i>Atur Tugas Penilaian</h3>
                <p id="t_nama" class="text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalTugas')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div class="overflow-y-auto p-5 space-y-4">
            <?php if (empty($kompetisi)) : ?>
                <p class="text-slate-400 text-center py-6">Belum ada lomba yang bisa ditugaskan. Buat kompetisi terlebih dulu di menu Kompetisi.</p>
            <?php else : ?>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Lomba</label>
                    <select name="id_kompetisi" id="t_kompetisi" onchange="tampilKategori()" class="w-full border border-slate-300 rounded-lg p-2.5">
                        <?php foreach ($kompetisi as $k) : ?>
                            <option value="<?= $k['id_kompetisi'] ?>"><?= esc($k['nama_kompetisi']) ?> (<?= $labelTahap[$k['status']] ?? $k['status'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <div class="flex justify-between items-center mb-2">
                        <label class="font-semibold text-slate-700">Kategori yang dinilai</label>
                        <button type="button" onclick="centangSemua()" class="text-[10px] text-brand-600 font-semibold hover:underline">Pilih semua</button>
                    </div>
                    <div id="t_kategori" class="space-y-2"></div>
                    <p class="text-[10px] text-slate-400 mt-2">Satu kategori boleh dinilai beberapa juri. Nilai akhir karya adalah rata-rata dari juri yang menilainya.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="flex justify-end space-x-2 border-t p-5 bg-slate-50 rounded-b-2xl">
            <button type="button" onclick="closeModal('modalTugas')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600 bg-white">Batal</button>
            <?php if (! empty($kompetisi)) : ?>
                <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Tugas</button>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- =====================================================
     MODAL RESET PASSWORD
     ===================================================== -->
<div id="modalReset" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <form id="r_form" method="post" autocomplete="off" class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
        <?= csrf_field() ?>
        <div class="flex justify-between items-start border-b pb-3">
            <div>
                <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-key text-indigo-500 mr-2"></i>Reset Password Juri</h3>
                <p id="r_nama" class="text-slate-500 mt-0.5"></p>
            </div>
            <button type="button" onclick="closeModal('modalReset')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
        </div>
        <div>
            <div class="flex justify-between items-center mb-1">
                <label class="font-semibold text-slate-700">Password Baru</label>
                <button type="button" onclick="buatSandi('r_password')" class="text-[10px] text-brand-600 font-semibold hover:underline"><i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis</button>
            </div>
            <input type="password" name="password" id="r_password" required minlength="8" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
        </div>
        <div class="flex justify-end space-x-2 border-t pt-3">
            <button type="button" onclick="closeModal('modalReset')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold">Reset Password</button>
        </div>
    </form>
</div>

<script>
    const KOMPETISI = <?= json_encode($kompetisi) ?>;
    let juriAktif = null;

    function bukaFormJuri(d = null) {
        const edit = !!d?.id_admin;
        document.getElementById('j_id').value = edit ? d.id_admin : '';
        document.getElementById('j_nama').value = d?.nama_admin || '';
        document.getElementById('j_keterangan').value = d?.keterangan || '';
        document.getElementById('j_email').value = d?.email || '';
        document.getElementById('j_judul').textContent = edit ? 'Edit Data Juri' : 'Tambah Juri';

        // Username & password hanya saat membuat akun baru
        document.getElementById('j_blokAkun').classList.toggle('hidden', edit);
        document.getElementById('j_username').required = !edit;
        document.getElementById('j_password').required = !edit;
        document.getElementById('j_username').value = d?.username_baru || '';
        document.getElementById('j_password').value = '';

        openModal('modalJuri');
    }

    function bukaTugas(j) {
        juriAktif = j;
        document.getElementById('t_form').action = '<?= site_url('juri/tugas') ?>/' + j.id_admin;
        document.getElementById('t_nama').textContent = j.nama_admin + (j.keterangan ? ' • ' + j.keterangan : '');

        // Pilih lomba yang sudah pernah ditugaskan, kalau ada
        const sel = document.getElementById('t_kompetisi');
        if (sel) {
            const pernah = Object.keys(j.tugas || {}).find(id => KOMPETISI.some(k => String(k.id_kompetisi) === id));
            if (pernah) sel.value = pernah;
            tampilKategori();
        }
        openModal('modalTugas');
    }

    function tampilKategori() {
        const id = document.getElementById('t_kompetisi').value;
        const lomba = KOMPETISI.find(k => String(k.id_kompetisi) === String(id));
        const terpilih = ((juriAktif.tugas || {})[id]?.kategori || []).map(k => String(k.id));
        const wadah = document.getElementById('t_kategori');
        wadah.innerHTML = '';

        if (!lomba || !lomba.kategori.length) {
            wadah.innerHTML = '<p class="text-slate-400 italic">Lomba ini belum punya kategori.</p>';
            return;
        }

        lomba.kategori.forEach(kat => {
            const label = document.createElement('label');
            label.className = 'flex items-center gap-3 border rounded-xl p-3 cursor-pointer hover:bg-indigo-50/50 has-[:checked]:border-indigo-400 has-[:checked]:bg-indigo-50';
            const cek = document.createElement('input');
            cek.type = 'checkbox';
            cek.name = 'kategori[]';
            cek.value = kat.id_kategori;
            cek.className = 'w-4 h-4 accent-indigo-600 cek-kategori';
            cek.checked = terpilih.includes(String(kat.id_kategori));

            const info = document.createElement('div');
            info.className = 'flex-1 min-w-0';
            const nama = document.createElement('div');
            nama.className = 'font-semibold text-slate-800';
            nama.textContent = kat.nama_kategori;
            const sub = document.createElement('div');
            const juri = parseInt(kat.jumlah_juri);
            sub.className = 'text-[10px] ' + (juri === 0 ? 'text-red-600 font-semibold' : 'text-slate-400');
            sub.textContent = kat.jumlah_karya + ' karya • ' + (juri === 0 ? 'belum ada juri' : juri + ' juri');
            info.append(nama, sub);

            label.append(cek, info);
            wadah.appendChild(label);
        });
    }

    function centangSemua() {
        const semua = [...document.querySelectorAll('.cek-kategori')];
        const nilai = !semua.every(c => c.checked);
        semua.forEach(c => c.checked = nilai);
    }

    function bukaReset(j) {
        document.getElementById('r_form').action = '<?= site_url('juri/reset') ?>/' + j.id_admin;
        document.getElementById('r_nama').textContent = j.nama_admin + ' (@' + j.username + ')';
        const p = document.getElementById('r_password');
        p.value = '';
        p.type = 'password';
        openModal('modalReset');
    }

    function buatSandi(id) {
        const huruf = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        const acak = new Uint32Array(10);
        crypto.getRandomValues(acak);
        const input = document.getElementById(id);
        input.value = Array.from(acak, n => huruf[n % huruf.length]).join('');
        input.type = 'text';
    }

    function salinAkun(tombol) {
        navigator.clipboard.writeText(document.getElementById('teksAkun').value).then(() => {
            tombol.innerHTML = '<i class="fa-solid fa-check mr-2"></i>Tersalin!';
            setTimeout(() => tombol.innerHTML = '<i class="fa-regular fa-copy mr-2"></i>Salin Info Akun', 2000);
        });
    }

    <?php if ($errors) : ?>
        document.addEventListener('DOMContentLoaded', () => bukaFormJuri(<?= json_encode([
                                                                                'id_admin'      => old('id_admin'),
                                                                                'nama_admin'    => old('nama_admin'),
                                                                                'keterangan'    => old('keterangan'),
                                                                                'email'         => old('email'),
                                                                                'username_baru' => old('username'),
                                                                            ]) ?>));
    <?php endif; ?>
</script>
<?= $this->endSection() ?>