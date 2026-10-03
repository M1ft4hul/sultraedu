<?php
/** @var array $akun */
/** @var bool $isGuru */
/** @var string $nama */
/** @var string $labelRole */
/** @var array|null $sekolah */
/** @var array|null $timDinas */
/** @var string $labelTim */
?>
<?= $this->extend('admin/componen_be/layout') ?>

<?= $this->section('content') ?>
<?php
$errorsProfil   = session()->getFlashdata('errorsProfil') ?? [];
$errorsPassword = session()->getFlashdata('errorsPassword') ?? [];

// Inisial untuk avatar, misalnya "Muh Syamdudin" -> "MS"
$kata    = preg_split('/\s+/', trim($nama));
$inisial = strtoupper(mb_substr($kata[0] ?? '', 0, 1) . mb_substr($kata[1] ?? '', 0, 1));

$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$sejak = ! empty($akun['created_at'])
    ? $bulan[(int) date('n', strtotime($akun['created_at']))] . ' ' . date('Y', strtotime($akun['created_at']))
    : '-';
?>
<section class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">

    <!-- Kolom kiri: identitas & video sekolah -->
    <div class="space-y-6">
        <!-- Kartu identitas -->
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="h-20 bg-gradient-to-r from-brand-800 to-slate-900"></div>
            <div class="px-5 pb-5 -mt-10 text-center space-y-3">
                <div class="mx-auto w-20 h-20 rounded-full bg-white p-1 shadow">
                    <div class="w-full h-full rounded-full bg-brand-600 text-white flex items-center justify-center text-2xl font-bold">
                        <?= esc($inisial) ?>
                    </div>
                </div>
                <div>
                    <h2 class="font-bold text-slate-800 text-base"><?= esc($nama) ?></h2>
                    <p class="text-xs text-slate-400 font-mono">@<?= esc($akun['username']) ?></p>
                </div>
                <span class="inline-block bg-brand-50 text-brand-700 text-[11px] font-bold px-3 py-1 rounded-full"><?= esc($labelRole) ?></span>

                <div class="text-left text-xs border-t pt-4 space-y-2.5">
                    <?php if ($sekolah) : ?>
                        <div class="flex gap-3">
                            <i class="fa-solid fa-school text-slate-400 w-4 mt-0.5"></i>
                            <div>
                                <div class="font-semibold text-slate-700"><?= esc($sekolah['nama_sekolah']) ?></div>
                                <div class="text-[11px] text-slate-400">NPSN <?= esc($sekolah['npsn']) ?> • <?= esc($sekolah['kabupaten_kota']) ?></div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <?php if (! $isGuru && ! empty($akun['email'])) : ?>
                        <div class="flex gap-3">
                            <i class="fa-solid fa-envelope text-slate-400 w-4 mt-0.5"></i>
                            <span class="text-slate-600 break-all"><?= esc($akun['email']) ?></span>
                        </div>
                    <?php endif; ?>
                    <div class="flex gap-3">
                        <i class="fa-solid fa-calendar text-slate-400 w-4 mt-0.5"></i>
                        <span class="text-slate-600">Bergabung sejak <?= $sejak ?></span>
                    </div>
                    <div class="flex gap-3">
                        <i class="fa-solid fa-circle-check text-emerald-500 w-4 mt-0.5"></i>
                        <span class="text-slate-600">Akun <?= esc($akun['status'] ?? 'aktif') ?></span>
                    </div>
                </div>
            </div>
        </div>

        <?php if (session()->get('role') === 'admin_sekolah' && $sekolah) : ?>
            <?php
            $video      = $sekolah['video_profil'] ?? null;
            $idYoutube  = \App\Controllers\Cprofil::idYoutube($video);
            $diperbarui = ! empty($sekolah['video_diperbarui'])
                ? date('j', strtotime($sekolah['video_diperbarui'])) . ' ' . $bulan[(int) date('n', strtotime($sekolah['video_diperbarui']))] . ' ' . date('Y', strtotime($sekolah['video_diperbarui']))
                : null;
            $maksMb = \App\Controllers\Cprofil::VIDEO_MAKS_MB;
            ?>
            <!-- Video profil sekolah (khusus Admin Sekolah) -->
            <div id="video" class="bg-white rounded-2xl border <?= $video ? 'border-slate-200' : 'border-red-200' ?> shadow-sm overflow-hidden text-xs">
                <div class="px-5 pt-5 flex justify-between items-start gap-2">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-film text-red-500 mr-2"></i>Video Profil Sekolah</h3>
                        <p class="text-slate-400 mt-0.5">Wajib diunggah oleh setiap sekolah.</p>
                    </div>
                    <?php if ($video) : ?>
                        <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0"><i class="fa-solid fa-check mr-1"></i>Sudah ada</span>
                    <?php else : ?>
                        <span class="bg-red-100 text-red-700 text-[10px] font-bold px-2 py-0.5 rounded-full shrink-0">Wajib</span>
                    <?php endif; ?>
                </div>

                <div class="p-5 space-y-3">
                    <?php if (session()->getFlashdata('gagalVideo')) : ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-3 py-2 rounded-lg"><?= esc(session()->getFlashdata('gagalVideo')) ?></div>
                    <?php endif; ?>

                    <?php if ($video) : ?>
                        <!-- Pemutar -->
                        <div class="aspect-video rounded-xl overflow-hidden bg-slate-900">
                            <?php if ($idYoutube) : ?>
                                <iframe src="https://www.youtube.com/embed/<?= esc($idYoutube, 'attr') ?>" class="w-full h-full" title="Video profil sekolah" allowfullscreen
                                    allow="accelerometer; clipboard-write; encrypted-media; gyroscope; picture-in-picture"></iframe>
                            <?php else : ?>
                                <video src="<?= base_url($video) ?>" controls preload="metadata" class="w-full h-full"></video>
                            <?php endif; ?>
                        </div>
                        <p class="text-[11px] text-slate-400">
                            <i class="fa-<?= $idYoutube ? 'brands fa-youtube text-red-500' : 'solid fa-file-video' ?> mr-1"></i>
                            <?= $idYoutube ? 'Tautan YouTube' : 'File video' ?><?= $diperbarui ? ' • diperbarui ' . $diperbarui : '' ?>
                        </p>
                        <div class="flex gap-2">
                            <button type="button" onclick="document.getElementById('formVideo').classList.toggle('hidden')"
                                class="flex-1 border border-slate-300 hover:bg-slate-50 text-slate-700 font-semibold px-3 py-2 rounded-lg">
                                <i class="fa-solid fa-arrows-rotate mr-1.5"></i>Ganti Video
                            </button>
                            <form action="<?= site_url('profil/video/hapus') ?>" method="post" onsubmit="return confirm('Hapus video profil sekolah?')">
                                <?= csrf_field() ?>
                                <button type="submit" title="Hapus video" class="h-full border border-red-200 hover:bg-red-50 text-red-600 font-semibold px-3 py-2 rounded-lg">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </form>
                        </div>
                    <?php else : ?>
                        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-red-700 flex gap-2">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <span>Sekolah Anda belum memiliki video profil. Unggah video singkat yang memperkenalkan sekolah.</span>
                        </div>
                    <?php endif; ?>

                    <!-- Form unggah / ganti -->
                    <form id="formVideo" action="<?= site_url('profil/video') ?>" method="post" enctype="multipart/form-data"
                        onsubmit="return unggahVideo(event)" class="<?= $video ? 'hidden' : '' ?> border border-dashed border-slate-300 rounded-xl p-3 space-y-3">
                        <?= csrf_field() ?>
                        <input type="hidden" name="jenis" id="v_jenis" value="file">

                        <div class="grid grid-cols-2 gap-1 bg-slate-100 rounded-lg p-1 font-semibold">
                            <button type="button" id="v_tabFile" onclick="pilihJenisVideo('file')" class="py-1.5 rounded-md bg-white shadow-sm text-slate-800"><i class="fa-solid fa-upload mr-1"></i>Unggah File</button>
                            <button type="button" id="v_tabYoutube" onclick="pilihJenisVideo('youtube')" class="py-1.5 rounded-md text-slate-500"><i class="fa-brands fa-youtube mr-1"></i>Tautan YouTube</button>
                        </div>

                        <div id="v_blokFile" class="space-y-1">
                            <input type="file" name="video" id="v_file" accept="video/mp4,video/webm"
                                class="w-full border border-slate-300 rounded-lg p-1.5 file:mr-2 file:border-0 file:bg-slate-100 file:px-2 file:py-1 file:rounded">
                            <p class="text-[10px] text-slate-400">MP4 atau WEBM, maksimal <?= $maksMb ?> MB. Video lebih besar sebaiknya diunggah ke YouTube.</p>
                        </div>
                        <div id="v_blokYoutube" class="hidden space-y-1">
                            <input type="url" name="url_youtube" id="v_url" placeholder="https://youtu.be/..." class="w-full border border-slate-300 rounded-lg p-2">
                            <p class="text-[10px] text-slate-400">Pastikan video di YouTube berstatus Publik atau Tidak Publik (unlisted).</p>
                        </div>

                        <!-- Progres unggah -->
                        <div id="v_progres" class="hidden space-y-1">
                            <div class="h-2 bg-slate-100 rounded-full overflow-hidden"><div id="v_bar" class="h-full bg-red-500 rounded-full transition-all" style="width:0%"></div></div>
                            <p id="v_persen" class="text-[10px] text-slate-500 text-center">0%</p>
                        </div>
                        <p id="v_pesan" class="hidden text-red-600"></p>

                        <button type="submit" id="v_tombol" class="w-full bg-red-600 hover:bg-red-700 text-white font-semibold px-3 py-2 rounded-lg">
                            <i class="fa-solid fa-cloud-arrow-up mr-1.5"></i><?= $video ? 'Simpan Video Baru' : 'Unggah Video' ?>
                        </button>
                    </form>
                </div>
            </div>

            <script>
                const VIDEO_MAKS_MB = <?= (int) $maksMb ?>;

                function pilihJenisVideo(jenis) {
                    document.getElementById('v_jenis').value = jenis;
                    document.getElementById('v_blokFile').classList.toggle('hidden', jenis !== 'file');
                    document.getElementById('v_blokYoutube').classList.toggle('hidden', jenis !== 'youtube');
                    document.getElementById('v_tabFile').className = 'py-1.5 rounded-md ' + (jenis === 'file' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500');
                    document.getElementById('v_tabYoutube').className = 'py-1.5 rounded-md ' + (jenis === 'youtube' ? 'bg-white shadow-sm text-slate-800' : 'text-slate-500');
                }

                function pesanVideo(teks) {
                    const p = document.getElementById('v_pesan');
                    p.textContent = teks;
                    p.classList.toggle('hidden', !teks);
                }

                // Unggah dengan bar progres (video bisa berukuran besar)
                function unggahVideo(e) {
                    e.preventDefault();
                    const form = document.getElementById('formVideo');
                    const jenis = document.getElementById('v_jenis').value;
                    pesanVideo('');

                    if (jenis === 'file') {
                        const file = document.getElementById('v_file').files[0];
                        if (!file) return pesanVideo('Pilih file video terlebih dulu.'), false;
                        if (file.size > VIDEO_MAKS_MB * 1024 * 1024) return pesanVideo('Ukuran video melebihi ' + VIDEO_MAKS_MB + ' MB. Unggah ke YouTube lalu gunakan tab Tautan YouTube.'), false;
                    } else if (!document.getElementById('v_url').value.trim()) {
                        return pesanVideo('Tempel tautan video YouTube terlebih dulu.'), false;
                    }

                    const tombol = document.getElementById('v_tombol');
                    tombol.disabled = true;
                    tombol.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-1.5"></i>Mengunggah...';
                    document.getElementById('v_progres').classList.toggle('hidden', jenis !== 'file');

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', form.action);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.upload.onprogress = ev => {
                        if (!ev.lengthComputable) return;
                        const persen = Math.round(ev.loaded / ev.total * 100);
                        document.getElementById('v_bar').style.width = persen + '%';
                        document.getElementById('v_persen').textContent = persen < 100 ? persen + '%' : 'Memproses video...';
                    };
                    xhr.onload = () => {
                        let data = {};
                        try { data = JSON.parse(xhr.responseText); } catch (err) {}
                        if (xhr.status === 200 && data.ok) {
                            location.href = '<?= site_url('profil') ?>#video';
                            location.reload();
                        } else {
                            pesanVideo(data.pesan || 'Video gagal diunggah. Periksa ukuran file lalu coba lagi.');
                            tombol.disabled = false;
                            tombol.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-1.5"></i>Coba Lagi';
                            document.getElementById('v_progres').classList.add('hidden');
                        }
                    };
                    xhr.onerror = () => {
                        pesanVideo('Koneksi terputus saat mengunggah. Coba lagi.');
                        tombol.disabled = false;
                        tombol.innerHTML = '<i class="fa-solid fa-cloud-arrow-up mr-1.5"></i>Coba Lagi';
                    };
                    xhr.send(new FormData(form));
                    return false;
                }
            </script>
        <?php endif; ?>
    </div>

    <div class="lg:col-span-2 space-y-6">

        <?php if (session()->getFlashdata('sukses')) : ?>
            <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs px-4 py-3 rounded-xl flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i><?= session()->getFlashdata('sukses') ?>
            </div>
        <?php endif; ?>

        <!-- Data diri -->
        <form action="<?= site_url('profil/simpan') ?>" method="post" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
            <?= csrf_field() ?>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-user-pen text-brand-600 mr-2"></i>Data Diri</h3>
                <p class="text-slate-400 mt-0.5">Perbarui nama dan informasi kontak Anda.</p>
            </div>

            <?php if ($errorsProfil) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errorsProfil as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                <div class="md:col-span-2">
                    <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                    <input type="text" name="nama" required value="<?= esc(old('nama', $nama)) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                </div>

                <?php if ($isGuru) : ?>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">NIP</label>
                        <input type="text" name="nip" value="<?= esc(old('nip', $akun['nip'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Mata Pelajaran</label>
                        <input type="text" name="mapel" value="<?= esc(old('mapel', $akun['mapel'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                <?php else : ?>
                    <div class="md:col-span-2">
                        <label class="font-semibold block mb-1 text-slate-700">Email</label>
                        <input type="email" name="email" value="<?= esc(old('email', $akun['email'] ?? '')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                <?php endif; ?>

                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Username</label>
                    <input type="text" value="<?= esc($akun['username']) ?>" disabled class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-lg p-2.5 font-mono">
                </div>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700">Hak Akses</label>
                    <input type="text" value="<?= esc($labelRole) ?>" disabled class="w-full border border-slate-200 bg-slate-50 text-slate-500 rounded-lg p-2.5">
                </div>
            </div>
            <p class="text-[11px] text-slate-400"><i class="fa-solid fa-circle-info mr-1"></i>Username, hak akses, dan sekolah hanya dapat diubah oleh Admin Dinas.</p>

            <div class="flex justify-end border-t pt-4">
                <button type="submit" class="px-4 py-2 bg-brand-600 hover:bg-brand-700 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-floppy-disk mr-1"></i> Simpan Data Diri
                </button>
            </div>
        </form>

        <!-- Ubah password -->
        <form action="<?= site_url('profil/password') ?>" method="post" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs" autocomplete="off">
            <?= csrf_field() ?>
            <div>
                <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-lock text-amber-500 mr-2"></i>Ubah Password</h3>
                <p class="text-slate-400 mt-0.5">Gunakan minimal 8 karakter. Password tidak ditampilkan kepada siapa pun, termasuk Admin Dinas.</p>
            </div>

            <?php if ($errorsPassword) : ?>
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                    <ul class="list-disc list-inside"><?php foreach ($errorsPassword as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <?php foreach ([
                ['password_lama', 'Password Lama', 'current-password'],
                ['password_baru', 'Password Baru', 'new-password'],
                ['konfirmasi_password', 'Ulangi Password Baru', 'new-password'],
            ] as [$nm, $label, $auto]) : ?>
                <div>
                    <label class="font-semibold block mb-1 text-slate-700"><?= $label ?></label>
                    <div class="relative">
                        <input type="password" name="<?= $nm ?>" id="<?= $nm ?>" required autocomplete="<?= $auto ?>"
                            class="w-full border border-slate-300 rounded-lg p-2.5 pr-9">
                        <button type="button" onclick="lihatSandi('<?= $nm ?>', this)"
                            class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="flex justify-end border-t pt-4">
                <button type="submit" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white rounded-lg font-semibold">
                    <i class="fa-solid fa-key mr-1"></i> Ubah Password
                </button>
            </div>
        </form>

        <?php if ($timDinas !== null) : ?>
            <?php
            $errorsTim = session()->getFlashdata('errorsTim') ?? [];
            $akunBaru  = session()->getFlashdata('akunBaru');
            ?>
            <!-- Tim Admin Dinas (khusus Admin Dinas) -->
            <div id="tim" class="bg-white p-6 rounded-2xl border border-slate-200 shadow-sm space-y-4 text-xs">
                <div class="flex flex-wrap justify-between items-start gap-3">
                    <div>
                        <h3 class="font-bold text-slate-800 text-sm"><i class="fa-solid fa-users-gear text-indigo-500 mr-2"></i>Tim <?= $labelTim ?></h3>
                        <p class="text-slate-400 mt-0.5"><?= $labelTim === 'Admin Sekolah' ? 'Tambahkan rekan di sekolah Anda, misalnya kepala sekolah, supaya pengelolaan tidak bergantung pada satu akun.' : 'Tambahkan rekan kerja di Dinas supaya pengelolaan platform tidak bergantung pada satu akun.' ?></p>
                    </div>
                    <button type="button" onclick="bukaFormTim()"
                        class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold px-4 py-2 rounded-lg flex items-center">
                        <i class="fa-solid fa-user-plus mr-2"></i>Tambah <?= $labelTim ?>
                    </button>
                </div>

                <?php if (session()->getFlashdata('gagalTim')) : ?>
                    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                        <i class="fa-solid fa-circle-exclamation mr-1"></i><?= session()->getFlashdata('gagalTim') ?>
                    </div>
                <?php endif; ?>

                <?php if ($akunBaru) : ?>
                    <!-- Password akun baru (tampil sekali) -->
                    <div class="border-2 border-emerald-300 rounded-xl p-4 space-y-3">
                        <div class="flex flex-wrap justify-between items-start gap-2">
                            <div>
                                <p class="font-bold text-slate-800"><i class="fa-solid fa-key text-emerald-600 mr-1"></i>Akun berhasil dibuat</p>
                                <p class="text-[11px] text-amber-700 mt-0.5"><i class="fa-solid fa-triangle-exclamation mr-1"></i>Password hanya ditampilkan sekali. Salin dan berikan kepada yang bersangkutan.</p>
                            </div>
                            <button type="button" onclick="salinAkunTim(this)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg">
                                <i class="fa-regular fa-copy mr-1"></i>Salin Info Akun
                            </button>
                        </div>
                        <div class="grid grid-cols-3 gap-2">
                            <div class="bg-slate-50 rounded-lg p-2.5"><div class="text-[10px] text-slate-400 uppercase">Nama</div><div class="font-semibold"><?= esc($akunBaru['nama']) ?></div></div>
                            <div class="bg-slate-50 rounded-lg p-2.5"><div class="text-[10px] text-slate-400 uppercase">Username</div><div class="font-mono font-semibold"><?= esc($akunBaru['username']) ?></div></div>
                            <div class="bg-emerald-50 border border-emerald-200 rounded-lg p-2.5"><div class="text-[10px] text-emerald-600 uppercase">Password</div><div class="font-mono font-bold text-emerald-800"><?= esc($akunBaru['password']) ?></div></div>
                        </div>
                        <textarea id="teksAkunTim" class="hidden"><?= esc(
                            "Akun EDUVATION - {$labelTim}\n" .
                            "Nama     : {$akunBaru['nama']}\n" .
                            "Username : {$akunBaru['username']}\n" .
                            "Password : {$akunBaru['password']}\n" .
                            "Login di : {$akunBaru['url']}\n\n" .
                            "Segera ganti password setelah login pertama melalui menu Profil Saya."
                        ) ?></textarea>
                    </div>
                <?php endif; ?>

                <div class="divide-y divide-slate-100 border border-slate-200 rounded-xl overflow-hidden">
                    <?php foreach ($timDinas as $t) : ?>
                        <?php $saya = (int) $t['id_admin'] === (int) session()->get('user_id'); ?>
                        <div class="flex items-center gap-3 p-3 <?= $t['status'] === 'nonaktif' ? 'opacity-60' : '' ?>">
                            <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold shrink-0">
                                <?php $k = preg_split('/\s+/', trim($t['nama_admin'])); ?>
                                <?= esc(strtoupper(mb_substr($k[0] ?? '', 0, 1) . mb_substr($k[1] ?? '', 0, 1))) ?>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="font-semibold text-slate-800 truncate">
                                    <?= esc($t['nama_admin']) ?>
                                    <?php if ($saya) : ?><span class="ml-1 bg-brand-50 text-brand-700 text-[10px] font-bold px-2 py-0.5 rounded-full">Anda</span><?php endif; ?>
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">@<?= esc($t['username']) ?><?= $t['email'] ? ' • ' . esc($t['email']) : '' ?></div>
                            </div>
                            <?php if ($t['status'] === 'aktif') : ?>
                                <span class="bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2.5 py-1 rounded-full">Aktif</span>
                            <?php else : ?>
                                <span class="bg-slate-200 text-slate-600 text-[10px] font-bold px-2.5 py-1 rounded-full">Nonaktif</span>
                            <?php endif; ?>
                            <?php if (! $saya) : ?>
                                <form action="<?= site_url('profil/tim/status/' . $t['id_admin']) ?>" method="post"
                                    onsubmit="return confirm('<?= $t['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?> akun <?= esc($t['nama_admin'], 'js') ?>?')">
                                    <?= csrf_field() ?>
                                    <button type="submit" title="<?= $t['status'] === 'aktif' ? 'Nonaktifkan' : 'Aktifkan' ?>"
                                        class="w-8 h-8 rounded-lg border border-slate-200 hover:bg-amber-50 hover:text-amber-600 flex items-center justify-center">
                                        <i class="fa-solid <?= $t['status'] === 'aktif' ? 'fa-toggle-on' : 'fa-toggle-off' ?>"></i>
                                    </button>
                                </form>
                            <?php else : ?>
                                <span class="w-8"></span>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Modal tambah Admin Dinas -->
            <div id="modalTim" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
                <form action="<?= site_url('profil/tim') ?>" method="post" autocomplete="off"
                    class="bg-white rounded-2xl max-w-md w-full p-6 shadow-2xl space-y-4 text-xs">
                    <?= csrf_field() ?>
                    <div class="flex justify-between items-center border-b pb-3">
                        <h3 class="font-bold text-slate-800 text-base"><i class="fa-solid fa-user-plus text-indigo-500 mr-2"></i>Tambah <?= $labelTim ?></h3>
                        <button type="button" onclick="closeModal('modalTim')" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
                    </div>

                    <?php if ($errorsTim) : ?>
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl">
                            <ul class="list-disc list-inside"><?php foreach ($errorsTim as $e) : ?><li><?= esc($e) ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Nama Lengkap <span class="text-red-500">*</span></label>
                        <input type="text" name="nama_admin" required value="<?= esc(old('nama_admin')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Username <span class="text-red-500">*</span></label>
                        <input type="text" name="username" required value="<?= esc(old('username')) ?>" placeholder="<?= $labelTim === 'Admin Sekolah' ? 'contoh: kepsek_smkn4kdi' : 'contoh: dinas_andi' ?>" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono lowercase">
                    </div>
                    <div>
                        <label class="font-semibold block mb-1 text-slate-700">Email</label>
                        <input type="email" name="email" value="<?= esc(old('email')) ?>" class="w-full border border-slate-300 rounded-lg p-2.5">
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-1">
                            <label class="font-semibold text-slate-700">Password <span class="text-red-500">*</span></label>
                            <button type="button" onclick="buatSandiTim()" class="text-[10px] text-brand-600 font-semibold hover:underline">
                                <i class="fa-solid fa-wand-magic-sparkles mr-0.5"></i>Buat Otomatis
                            </button>
                        </div>
                        <input type="password" name="password" id="sandiTim" required minlength="8" autocomplete="new-password" class="w-full border border-slate-300 rounded-lg p-2.5 font-mono">
                        <p class="text-[10px] text-slate-400 mt-1">Minimal 8 karakter.</p>
                    </div>
                    <div class="flex justify-end space-x-2 border-t pt-3">
                        <button type="button" onclick="closeModal('modalTim')" class="px-4 py-2 border rounded-lg font-semibold text-slate-600">Batal</button>
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-semibold"><i class="fa-solid fa-floppy-disk mr-1"></i> Buat Akun</button>
                    </div>
                </form>
            </div>

            <script>
                function bukaFormTim() {
                    openModal('modalTim');
                }

                function buatSandiTim() {
                    const huruf = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
                    const acak = new Uint32Array(10);
                    crypto.getRandomValues(acak);
                    const input = document.getElementById('sandiTim');
                    input.value = Array.from(acak, n => huruf[n % huruf.length]).join('');
                    input.type = 'text';
                }

                function salinAkunTim(tombol) {
                    navigator.clipboard.writeText(document.getElementById('teksAkunTim').value).then(() => {
                        tombol.innerHTML = '<i class="fa-solid fa-check mr-1"></i>Tersalin!';
                        setTimeout(() => tombol.innerHTML = '<i class="fa-regular fa-copy mr-1"></i>Salin Info Akun', 2000);
                    });
                }

                <?php if ($errorsTim) : ?>
                    document.addEventListener('DOMContentLoaded', () => openModal('modalTim'));
                <?php endif; ?>
            </script>
        <?php endif; ?>
    </div>
</section>

<script>
    function lihatSandi(id, tombol) {
        const input = document.getElementById(id);
        const tampil = input.type === 'password';
        input.type = tampil ? 'text' : 'password';
        tombol.innerHTML = tampil ? '<i class="fa-solid fa-eye-slash"></i>' : '<i class="fa-solid fa-eye"></i>';
    }
</script>
<?= $this->endSection() ?>