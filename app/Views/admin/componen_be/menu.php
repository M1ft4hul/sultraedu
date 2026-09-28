<?php
// Tandai menu aktif berdasarkan alamat halaman saat ini
$uri   = uri_string();
$aktif = fn($slug) => str_contains($uri, $slug) ? 'active-tab' : '';
?>
<nav class="bg-white border-b border-slate-200 text-slate-700 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 flex flex-wrap gap-x-1 sm:gap-x-4 text-sm font-medium">
        <a href="<?= site_url('dashboard') ?>" id="tab-beranda" class="py-3 px-3.5 whitespace-nowrap <?= $aktif('dashboard') ?> hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-house mr-2 text-xs"></i>Beranda
        </a>
        <a href="<?= site_url('praktik-baik') ?>" id="tab-praktek-baik" class="py-3 px-3.5 whitespace-nowrap <?= $aktif('praktik-baik') ?> hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-book-open mr-2 text-xs"></i>Praktek Baik
        </a>
        <button onclick="navTo('bank-inovasi')" id="tab-bank-inovasi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-box-archive mr-2 text-xs"></i>Bank Inovasi
        </button>
        <button onclick="navTo('kompetisi')" id="tab-kompetisi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-trophy mr-2 text-xs"></i>Kompetisi Inovasi
        </button>
        <button onclick="navTo('apresiasi')" id="tab-apresiasi" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-award mr-2 text-xs"></i>Apresiasi
        </button>
        <button onclick="navTo('monev')" id="tab-monev" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-chart-line mr-2 text-xs"></i>Monev
        </button>
        <button onclick="navTo('suara')" id="tab-suara" class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
            <i class="fa-solid fa-comments mr-2 text-xs"></i>SUARA
        </button>
        <?php if (session()->get('role') === 'admin_pusat') : ?>
            <div class="relative" id="kelolaMenu">
                <button type="button" onclick="toggleKelolaMenu()"
                    class="py-3 px-3.5 whitespace-nowrap hover:text-brand-600 transition flex items-center">
                    <i class="fa-solid fa-database mr-2 text-xs"></i>Kelola Data
                    <i class="fa-solid fa-chevron-down ml-1.5 text-[9px]"></i>
                </button>
                <div id="kelolaMenuDropdown"
                    class="hidden absolute left-0 mt-1 w-52 bg-white border border-slate-200 rounded-xl shadow-xl z-50 p-1.5">
                    <a href="<?= base_url('sekolah') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-slate-50">
                        <i class="fa-solid fa-school w-5 text-slate-400"></i> Data Sekolah
                    </a>
                    <a href="<?= base_url('pengguna') ?>" class="flex items-center px-3 py-2 text-sm rounded-lg hover:bg-slate-50">
                        <i class="fa-solid fa-user w-5 text-slate-400"></i> Akun Pengguna
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</nav>