<!DOCTYPE html>
<html lang="id">

<head>
    <!-- Title -->
    <title>Masuk | EDUVATION Sulawesi Tenggara</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="author" content="Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara">
    <meta name="robots" content="noindex, nofollow">
    <meta name="format-detection" content="telephone=no">
    <meta name="description" content="EDUVATION - Ekosistem Inovasi & Praktik Baik Pendidikan Daerah, Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara.">

    <!-- Favicon -->
    <link rel="shortcut icon" type="image/png" href="<?= base_url('logosultra.png') ?>">

    <!-- Plugins Stylesheet -->
    <link href="<?= base_url('admin/vendor/metismenu/dist/metisMenu.min.css') ?>" rel="stylesheet">
    <link href="<?= base_url('admin/vendor/bootstrap-select/dist/css/bootstrap-select.min.css') ?>" rel="stylesheet">
    <link class="main-switcher" href="<?= base_url('admin/css/switcher.css') ?>" rel="stylesheet">

    <!-- Style CSS -->
    <link class="main-plugins" href="<?= base_url('admin/css/plugins.css') ?>" rel="stylesheet">
    <link class="main-css" href="<?= base_url('admin/css/style.css') ?>" rel="stylesheet">
</head>

<body>

    <div class="auth-wrapper">
        <div class="row">

            <!-- Kolom kiri: identitas -->
            <div class="col-xl-6 col-lg-6 order-lg-1">
                <div class="auth-info text-center">
                    <div class="mb-5 mx-auto col-xxl-8">
                        <div class="brand-logo mb-3">
                            <img src="<?= base_url('logosultra.png') ?>" alt="Logo Provinsi Sulawesi Tenggara" style="max-height: 110px;">
                        </div>
                        <h4 class="mb-1">EDUVATION</h4>
                        <h5 class="mb-2">Dinas Pendidikan & Kebudayaan Provinsi Sulawesi Tenggara</h5>
                        <p class="info-text">Ekosistem Inovasi & Praktik Baik Pendidikan Daerah</p>
                    </div>
                    <div class="auth-media">
                        <img class="w-100 img-fluid" src="<?= base_url('admin/images/login.png') ?>" alt="">
                    </div>
                </div>
            </div>

            <!-- Kolom kanan: form -->
            <div class="col-xl-6 col-lg-6 mx-auto align-self-center">
                <div class="auth-form">
                    <div class="text-center mb-4">
                        <h3 class="mb-0">Masuk</h3>
                        <p class="mb-0">Silakan masuk menggunakan username dan kata sandi Anda.</p>
                    </div>

                    <!-- Pesan error dari controller -->
                    <?php if (session()->getFlashdata('error')) : ?>
                        <div class="alert alert-danger" role="alert">
                            <?= esc(session()->getFlashdata('error')) ?>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('add_login') ?>" method="post">
                        <?= csrf_field() ?>

                        <div class="mb-3">
                            <label class="form-label" for="username">Username</label>
                            <input type="text" id="username" name="username"
                                class="form-control form-control-lg"
                                placeholder="Masukkan username"
                                value="<?= esc(old('username')) ?>"
                                autocomplete="username" required autofocus>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="password">Kata Sandi</label>
                            <div class="position-relative">
                                <input type="password" id="password" name="password"
                                    class="form-control form-control-lg dz-password"
                                    placeholder="Masukkan kata sandi"
                                    autocomplete="current-password" required>
                                <span class="show-pass position-absolute top-50 end-0 me-2 translate-middle">
                                    <span class="show"><i class="fa fa-eye-slash"></i></span>
                                    <span class="hide"><i class="fa fa-eye"></i></span>
                                </span>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mb-4 mb-lg-5">
                            <span class="text-muted small">Lupa kata sandi? <a href="<?php echo base_url('contact'); ?>">Hubungi admin</a></span>
                        </div>

                        <div class="text-center mb-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100 mb-3">Masuk</button>
                        </div>

                        <p class="text-center small text-muted">Belum punya akun? <a href="<?php echo base_url('contact'); ?>">Hubungi Admin Sekolah Anda.</a> </p>
                    </form>
                </div>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= base_url('admin/vendor/jquery/dist/jquery.min.js') ?>"></script>
    <script src="<?= base_url('admin/vendor/bootstrap/dist/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?= base_url('admin/vendor/bootstrap-select/dist/js/bootstrap-select.min.js') ?>"></script>
    <script src="<?= base_url('admin/vendor/metismenu/dist/metisMenu.min.js') ?>"></script>
    <script src="<?= base_url('admin/vendor/@yaireo/tagify/dist/tagify.js') ?>"></script>
    <script src="<?= base_url('admin/js/custom.js') ?>"></script>

</body>

</html>