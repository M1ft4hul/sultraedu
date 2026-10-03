<?php
/**
 * Komponen pagination bersama.
 * Panggil dari view:
 *   <?= view('admin/componen_be/pagination', [
 *       'pager'      => $pager,          // \CodeIgniter\Pager\Pager
 *       'grup'       => 'praktik',       // nama grup pager (query: page_<grup>)
 *       'perHalaman' => $perHalaman,
 *       'url'        => site_url('praktik-baik'),
 *       'tahan'      => ['status' => $status], // filter yang dipertahankan saat ganti jumlah per halaman
 *       'satuan'     => 'praktik baik',
 *       'jangkar'    => '',              // opsional, misalnya '#jadwal'
 *   ]) ?>
 */
$jangkar = $jangkar ?? '';
$tahan   = $tahan ?? [];
$satuan  = $satuan ?? 'data';

$halaman = $pager->getCurrentPage($grup);
$jmlHal  = $pager->getPageCount($grup);
$total   = $pager->getTotal($grup);
$dari    = $total ? ($halaman - 1) * $perHalaman + 1 : 0;
$sampai  = min($halaman * $perHalaman, $total);

// Halaman pertama, terakhir, dan 2 di sekitar halaman aktif
$nomor = [];
for ($n = 1; $n <= $jmlHal; $n++) {
    if ($n === 1 || $n === $jmlHal || abs($n - $halaman) <= 2) {
        $nomor[] = $n;
    }
}
$kelas = 'min-w-[34px] h-[34px] px-2 rounded-lg border flex items-center justify-center font-semibold transition';
$ke    = fn ($n) => $pager->getPageURI($n, $grup) . $jangkar;
?>
<div class="flex flex-wrap justify-between items-center gap-3 pt-2 text-xs">
    <div class="flex items-center gap-3 text-slate-500">
        <span>Menampilkan <b class="text-slate-700"><?= number_format($dari, 0, ',', '.') ?>–<?= number_format($sampai, 0, ',', '.') ?></b> dari <b class="text-slate-700"><?= number_format($total, 0, ',', '.') ?></b> <?= esc($satuan) ?></span>
        <form method="get" action="<?= $url . $jangkar ?>" class="flex items-center gap-1.5">
            <?php foreach ($tahan as $kunci => $nilai) : ?>
                <?php if ((string) $nilai !== '') : ?><input type="hidden" name="<?= esc($kunci, 'attr') ?>" value="<?= esc($nilai, 'attr') ?>"><?php endif; ?>
            <?php endforeach; ?>
            <select name="per" onchange="this.form.submit()" class="border border-slate-300 rounded-lg p-1.5">
                <?php foreach ([10, 25, 50] as $opsi) : ?>
                    <option value="<?= $opsi ?>" <?= (int) $perHalaman === $opsi ? 'selected' : '' ?>><?= $opsi ?> / halaman</option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if ($jmlHal > 1) : ?>
        <nav class="flex items-center gap-1" aria-label="Halaman">
            <?php if ($halaman > 1) : ?>
                <a href="<?= $ke($halaman - 1) ?>" class="<?= $kelas ?> border-slate-200 text-slate-600 hover:bg-slate-50" title="Sebelumnya"><i class="fa-solid fa-chevron-left text-[10px]"></i></a>
            <?php else : ?>
                <span class="<?= $kelas ?> border-slate-100 text-slate-300"><i class="fa-solid fa-chevron-left text-[10px]"></i></span>
            <?php endif; ?>

            <?php $sebelumnya = 0; ?>
            <?php foreach ($nomor as $n) : ?>
                <?php if ($n - $sebelumnya > 1) : ?><span class="px-1 text-slate-400">…</span><?php endif; ?>
                <?php if ($n === $halaman) : ?>
                    <span class="<?= $kelas ?> bg-brand-600 border-brand-600 text-white" aria-current="page"><?= $n ?></span>
                <?php else : ?>
                    <a href="<?= $ke($n) ?>" class="<?= $kelas ?> border-slate-200 text-slate-600 hover:bg-slate-50"><?= $n ?></a>
                <?php endif; ?>
                <?php $sebelumnya = $n; ?>
            <?php endforeach; ?>

            <?php if ($halaman < $jmlHal) : ?>
                <a href="<?= $ke($halaman + 1) ?>" class="<?= $kelas ?> border-slate-200 text-slate-600 hover:bg-slate-50" title="Berikutnya"><i class="fa-solid fa-chevron-right text-[10px]"></i></a>
            <?php else : ?>
                <span class="<?= $kelas ?> border-slate-100 text-slate-300"><i class="fa-solid fa-chevron-right text-[10px]"></i></span>
            <?php endif; ?>
        </nav>
    <?php endif; ?>
</div>