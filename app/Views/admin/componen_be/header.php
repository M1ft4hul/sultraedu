<?php
// Label role yang tampil di header
$labelRole = [
    'admin_pusat'      => 'Admin / Pengelola Dinas',
    'admin_sekolah'    => 'Satuan Pendidikan (SMA/SMK/SLB)',
    'tim_juri' => 'Tim / Juri Kompetisi',
    'guru'             => 'Guru',
];

$labelSingkat = [
    'admin_pusat'      => 'Admin Dinas',
    'admin_sekolah'    => 'Sekolah',
    'tim_juri' => 'Tim Juri',
    'guru'             => 'Guru',
];

// Warna badge per role
$warnaRole = [
    'admin_pusat'      => 'bg-brand-500/20 text-brand-400 border-brand-500/30',
    'admin_sekolah'    => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    'tim_juri' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    'guru'             => 'bg-purple-500/20 text-purple-400 border-purple-500/30',
];

$role  = session()->get('role');
$namaAdmin  = session()->get('namaAdmin');
$label = $labelRole[$role] ?? $role;
$warna = $warnaRole[$role] ?? 'bg-slate-500/20 text-slate-300 border-slate-500/30';
?>
<header class="bg-slate-900 text-white sticky top-0 z-40 shadow-md">
    <div class="max-w-7xl mx-auto px-4 py-3 flex flex-wrap justify-between items-center border-b border-slate-800">
        <!-- identitas -->
        <div class="flex items-center space-x-3">
            <div class=" text-white p-2.5 rounded-xl font-black text-xl tracking-wider flex items-center justify-center shadow-lg align-items-center gap-2">
                <img src="<?= base_url('dashboard/Pemprov Sultra.png') ?>" alt="" width="80px">
            </div>
            <div>
                <h1><strong>EDUVATION</strong></h1>
                <h1 class="text-sm font-bold leading-tight tracking-wide">Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara</h1>
                <p class="text-xs text-slate-400">Ekosistem Inovasi & Praktik Baik Pendidikan Daerah</p>
            </div>
        </div>

        <div class="flex items-center gap-2 mt-2 sm:mt-0">
            <!-- hak akses/ role -->
            <div class="flex items-center space-x-3 mt-2 sm:mt-0 bg-slate-800/90 px-3 py-1.5 rounded-lg border border-slate-700">
                <span class="text-xs text-slate-300 font-medium">
                    <i class="fa-solid fa-user-gear text-brand-400 mr-1"></i> Akses:
                </span>
                <select id="roleSelector" data-current="<?= esc($role) ?>" onchange="gantiAkun(this)"
                    class="bg-slate-900 text-xs text-white border border-slate-600 rounded px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-brand-500">
                    <?php foreach ($labelRole as $value => $teks) : ?>
                        <option value="<?= $value ?>" <?= $value === $role ? 'selected' : '' ?>><?= $teks ?></option>
                    <?php endforeach; ?>
                </select>
                <span class="<?= $warna ?> text-[10px] px-2 py-0.5 rounded-full font-semibold border">
                    <?= esc($labelSingkat[$role] ?? $label) ?>
                </span>
            </div>

            <!-- user logout -->
            <div class="relative" id="userMenu">
                <button type="button" onclick="toggleUserMenu()" title="Akun"
                    class="h-[38px] w-[38px] flex items-center justify-center bg-slate-800/90 border border-slate-700 rounded-lg text-slate-300 hover:text-white hover:bg-slate-700 transition">
                    <i class="fa-solid fa-circle-user text-lg"></i>
                </button>
                <div id="userMenuDropdown"
                    class="hidden absolute right-0 mt-3 w-64 bg-slate-900 border border-slate-700 rounded-2xl shadow-2xl z-50 p-2 text-slate-200">

                    <!-- Nama & username -->
                    <div class="px-4 pt-3 pb-3">
                        <p class="text-sm font-bold text-white truncate"><?= esc($namaAdmin) ?></p>
                        <p class="text-[11px] text-slate-400 truncate">@<?= esc(session()->get('username')) ?></p>
                    </div>

                    <div class="border-t border-slate-700 mx-3 mb-1"></div>

                    <!-- Menu Profil (nanti) -->
                    <a href="<?= site_url('profil') ?>" class="block px-4 py-2.5 text-sm rounded-xl hover:bg-slate-800 transition">
                        Profil Saya
                    </a>

                    <!-- Keluar -->
                    <button type="button" onclick="toggleUserMenu(); openModal('modalLogout')"
                        class="w-full flex items-center justify-between px-4 py-2.5 text-sm rounded-xl hover:bg-slate-800 hover:text-red-400 transition">
                        Keluar
                        <i class="fa-solid fa-right-from-bracket text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <!-- menu -->
    <?= $this->include('admin/componen_be/menu') ?>
</header>