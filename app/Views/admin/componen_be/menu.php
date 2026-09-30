<?php
$role = session()->get('role');
$uri  = uri_string();

// Daftar menu: 'url' = sudah punya halaman sendiri, 'tab' = masih tab di dashboard
$menus = [
    ['label' => 'Beranda',                      'icon' => 'fa-house',                   'url' => 'dashboard',           'roles' => 'semua'],
    ['label' => 'Praktik Baik',                 'icon' => 'fa-book-open',         'url' => 'praktik-baik',         'roles' => ['admin_pusat', 'admin_sekolah', 'guru']],
    ['label' => 'Bank Inovasi',              'icon' => 'fa-box-archive',        'url' => 'bank-inovasi',       'roles' => ['admin_pusat', 'admin_sekolah', 'guru']],
    ['label' => 'Kompetisi Inovasi',      'icon' => 'fa-trophy',                'url' => 'kompetisi',                'roles' => 'semua'],
    ['label' => 'Apresiasi',                    'icon' => 'fa-award',                  'url' => 'apresiasi',               'roles' => ['admin_pusat', 'admin_sekolah', 'guru']],
    ['label' => 'Monev',                        'icon' => 'fa-chart-line',             'url' => 'monev',                   'roles' => ['admin_pusat', 'guru']],
    ['label' => 'SUARA',                        'icon' => 'fa-comments',          'url' => 'suara',                      'roles' => ['admin_pusat', 'admin_sekolah', 'guru']],
    ['label' => 'Data Guru',               'icon' => 'fa-chalkboard-user',       'url' => 'guru',                     'roles' => ['admin_sekolah']],
];

$boleh   = fn($m) => $m['roles'] === 'semua' || in_array($role, $m['roles'], true);
$kelas   = 'py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center';
$aktif   = fn($slug) => str_contains($uri, $slug) ? 'active-tab' : '';
?>
<nav class="bg-white border-b border-slate-200 text-slate-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 flex flex-wrap gap-x-1 sm:gap-x-4 text-sm font-medium">

        <?php foreach ($menus as $m) : ?>
            <?php if (! $boleh($m)) continue; ?>

            <?php if (isset($m['url'])) : ?>
                <a href="<?= site_url($m['url']) ?>" class="<?= $kelas ?> <?= $aktif($m['url']) ?>">
                    <i class="fa-solid <?= $m['icon'] ?> mr-2 text-xs"></i><?= $m['label'] ?>
                </a>
            <?php else : ?>
                <button onclick="navTo('<?= $m['tab'] ?>')" id="tab-<?= $m['tab'] ?>" class="<?= $kelas ?>">
                    <i class="fa-solid <?= $m['icon'] ?> mr-2 text-xs"></i><?= $m['label'] ?>
                </button>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if ($role === 'admin_pusat') : ?>
            <div class="relative" id="kelolaMenu">
                <button type="button" onclick="toggleKelolaMenu()" class="<?= $kelas ?> <?= $aktif('sekolah') ?: $aktif('pengguna') ?>">
                    <i class="fa-solid fa-database mr-2 text-xs"></i>Kelola Data
                    <i class="fa-solid fa-chevron-down ml-1.5 text-[9px]"></i>
                </button>
                <div id="kelolaMenuDropdown"
                    class="hidden absolute left-0 mt-1 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1.5">
                    <a href="<?= site_url('sekolah') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-slate-50">
                        <i class="fa-solid fa-school w-5 text-slate-400"></i> Data Sekolah
                    </a>
                    <a href="<?= site_url('pengguna') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-slate-50">
                        <i class="fa-solid fa-users-gear w-5 text-slate-400"></i> Akun Pengguna
                    </a>
                </div>
            </div>
        <?php endif; ?>

    </div>
</nav>