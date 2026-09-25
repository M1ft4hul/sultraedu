<?php
// Label role yang tampil di header
$labelRole = [
    'admin_pusat'      => 'Admin Dinas',
    'admin_sekolah'    => 'Admin Sekolah',
    'penanggung_jawab' => 'Penanggung Jawab',
    'guru'             => 'Guru',
];

// Warna badge per role
$warnaRole = [
    'admin_pusat'      => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
    'admin_sekolah'    => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
    'penanggung_jawab' => 'bg-sky-500/20 text-sky-400 border-sky-500/30',
    'guru'             => 'bg-violet-500/20 text-violet-400 border-violet-500/30',
];

$role  = session()->get('role');
$nama  = session()->get('nama');
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

        <div class="flex items-center space-x-3 mt-2 sm:mt-0 bg-slate-800/90 px-3 py-1.5 rounded-lg border border-slate-700">
            <span class="text-xs text-slate-300 font-medium"><i class="fa-solid fa-user-gear text-brand-400 mr-1"></i> Simulasi Akses:</span>
            <select id="roleSelector" onchange="switchRole(this.value)" class="bg-slate-900 text-xs text-white border border-slate-600 rounded px-2.5 py-1 focus:outline-none focus:ring-2 focus:ring-brand-500">
                <option value="sekolah">Satuan Pendidikan (SMA/SMK/SLB)</option>
                <option value="admin">Admin / Pengelola Dinas</option>
                <option value="juri">Tim / Juri Kompetisi</option>
                <option value="publik">Masyarakat / Pengguna Publik</option>
            </select>
            <span id="roleBadge" class="bg-emerald-500/20 text-emerald-400 text-[10px] px-2 py-0.5 rounded-full font-semibold border border-emerald-500/30">Sekolah</span>
        </div>
    </div>
    <!-- menu -->
     <?php include 'menu.php' ?>
</header>