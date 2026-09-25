<?php
$labelRole = [
    'admin_pusat'      => 'Admin Pusat',
    'admin_sekolah'    => 'Admin Sekolah',
    'penanggung_jawab' => 'Penanggung Jawab',
    'guru'             => 'Guru',
];
$role = session()->get('role');
?>

<div class="user-info">
    <p>Login berhasil. Halo, <strong><?= esc(session()->get('nama')) ?></strong></p>
    <p>Role: <?= esc($labelRole[$role] ?? $role) ?></p>
    <a href="<?= base_url('logout') ?>">Keluar</a>
</div>