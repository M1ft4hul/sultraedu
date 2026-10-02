<?php
/** @var array $k */
/** @var array $kategori */
/** @var array $kriteria */
/** @var int $peserta */
$bulan = [1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
$tgl   = fn ($d) => date('j', strtotime($d)) . ' ' . $bulan[(int) date('n', strtotime($d))] . ' ' . date('Y', strtotime($d));

$judul      = $k['nama_kompetisi'] . ' — EDUVATION Sulawesi Tenggara';
$ringkasan  = mb_strimwidth(trim(preg_replace('/\s+/', ' ', (string) $k['deskripsi'])) ?: 'Kompetisi inovasi pendidikan Dinas Pendidikan dan Kebudayaan Provinsi Sulawesi Tenggara.', 0, 180, '…');
$gambar     = $k['banner'] ? base_url($k['banner']) : base_url('dashboard/Pemprov Sultra.png');
$alamat     = current_url();

$sisa = (int) floor((strtotime($k['tanggal_selesai'] . ' 23:59:59') - time()) / 86400);
[$labelStatus, $warnaStatus] = match (true) {
    (bool) $k['hasil_diumumkan']     => ['Pemenang telah diumumkan', 'bg-emerald-100 text-emerald-800'],
    $k['status'] === 'pendaftaran'   => ['Pendaftaran dibuka', 'bg-blue-100 text-blue-700'],
    $k['status'] === 'berlangsung'   => ['Tahap penjurian', 'bg-amber-100 text-amber-800'],
    default                          => ['Kompetisi selesai', 'bg-slate-100 text-slate-700'],
};
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($judul) ?></title>
    <meta name="description" content="<?= esc($ringkasan, 'attr') ?>">

    <!-- Pratinjau tautan (WhatsApp, Facebook, Telegram) -->
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="EDUVATION Sulawesi Tenggara">
    <meta property="og:title" content="<?= esc($k['nama_kompetisi'], 'attr') ?>">
    <meta property="og:description" content="<?= esc($ringkasan, 'attr') ?>">
    <meta property="og:image" content="<?= esc($gambar, 'attr') ?>">
    <meta property="og:url" content="<?= esc($alamat, 'attr') ?>">
    <meta name="twitter:card" content="summary_large_image">

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

<body class="bg-slate-100 min-h-screen text-slate-700">

    <header class="bg-slate-900 text-white">
        <div class="max-w-3xl mx-auto px-4 py-3 flex items-center gap-3">
            <img src="<?= base_url('dashboard/Pemprov Sultra.png') ?>" alt="Logo" class="w-10 h-10 object-contain">
            <div>
                <div class="font-extrabold tracking-wide">EDUVATION</div>
                <div class="text-[11px] text-slate-300">Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara</div>
            </div>
        </div>
    </header>

    <main class="max-w-3xl mx-auto px-4 py-6 space-y-5">
        <article class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <?php if ($k['banner']) : ?>
                <img src="<?= base_url($k['banner']) ?>" alt="Banner <?= esc($k['nama_kompetisi'], 'attr') ?>" class="w-full">
            <?php endif; ?>

            <div class="p-6 space-y-5 text-sm">
                <div class="space-y-2">
                    <div class="flex flex-wrap gap-2">
                        <span class="<?= $warnaStatus ?> text-xs font-bold px-3 py-1 rounded-full"><?= $labelStatus ?></span>
                        <?php if ($k['status'] === 'pendaftaran' && $sisa >= 0) : ?>
                            <span class="bg-red-50 text-red-600 text-xs font-bold px-3 py-1 rounded-full"><i class="fa-regular fa-clock mr-1"></i><?= $sisa === 0 ? 'Hari terakhir!' : $sisa . ' hari lagi' ?></span>
                        <?php endif; ?>
                    </div>
                    <h1 class="text-2xl font-extrabold text-slate-900 leading-snug"><?= esc($k['nama_kompetisi']) ?></h1>
                    <p class="text-slate-500"><i class="fa-regular fa-calendar mr-1.5"></i><?= $tgl($k['tanggal_mulai']) ?> – <?= $tgl($k['tanggal_selesai']) ?> • <?= $peserta ?> karya terdaftar</p>
                </div>

                <?php if ($k['deskripsi']) : ?>
                    <section>
                        <h2 class="font-bold text-slate-900 mb-1.5">Ketentuan</h2>
                        <div class="bg-slate-50 border rounded-xl p-4 whitespace-pre-line leading-relaxed"><?= esc($k['deskripsi']) ?></div>
                    </section>
                <?php endif; ?>

                <div class="grid md:grid-cols-2 gap-5">
                    <?php if ($kategori) : ?>
                        <section>
                            <h2 class="font-bold text-slate-900 mb-1.5">Kategori Lomba</h2>
                            <ul class="space-y-1.5">
                                <?php foreach ($kategori as $nama) : ?>
                                    <li class="flex gap-2"><i class="fa-solid fa-tag text-indigo-400 mt-1"></i><span><?= esc($nama) ?></span></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>
                    <?php if ($kriteria) : ?>
                        <section>
                            <h2 class="font-bold text-slate-900 mb-1.5">Kriteria Penilaian</h2>
                            <ul class="space-y-1.5">
                                <?php foreach ($kriteria as $kr) : ?>
                                    <li class="flex justify-between gap-2"><span><?= esc($kr['nama_kriteria']) ?></span><b class="text-slate-900"><?= (int) $kr['skor_maks'] ?>%</b></li>
                                <?php endforeach; ?>
                            </ul>
                        </section>
                    <?php endif; ?>
                </div>

                <div class="border-t pt-5 flex flex-wrap items-center justify-between gap-3">
                    <p class="text-slate-500 text-xs max-w-sm">
                        <?= $k['status'] === 'pendaftaran'
                            ? 'Pendaftaran dilakukan oleh guru melalui akun EDUVATION. Belum punya akun? Hubungi operator sekolah Anda.'
                            : 'Informasi hasil dan perkembangan lomba dapat dilihat melalui akun EDUVATION.' ?>
                    </p>
                    <a href="<?= site_url('login') ?>" class="bg-amber-500 hover:bg-amber-600 text-white font-bold px-5 py-2.5 rounded-xl shadow">
                        <i class="fa-solid fa-right-to-bracket mr-2"></i><?= $k['status'] === 'pendaftaran' ? 'Masuk untuk Mendaftar' : 'Masuk ke EDUVATION' ?>
                    </a>
                </div>
            </div>
        </article>

        <p class="text-center text-[11px] text-slate-400">© <?= date('Y') ?> EDUVATION • Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara</p>
    </main>
</body>

</html>