<div class="sidenav mobile-nav" id="slide-menu">
    <div class="menu">
        <ul class="collection">
            <li class="collection-item" style="animation-duration: 0.25s"><a class="sidenav-close waves-effect menu-list" href="#feature">Beranda</a></li>
            <li class="collection-item" style="animation-duration: 0.5s"><a class="sidenav-close waves-effect menu-list" href="#popular">Prestasi Pendidik</a></li>
            <li class="collection-item" style="animation-duration: 0.75s"><a class="sidenav-close waves-effect menu-list" href="#explore">Pengumuman</a></li>
            <li class="collection-item" style="animation-duration: 1s"><a class="sidenav-close waves-effect menu-list" href="#blog">blog</a></li>
            <li class="collection-item" style="animation-duration: 1s"><a class="waves-effect menu-list" href="contact.html">contact</a></li>
        </ul>
        <hr class="divider-sidebar">
        <ul class="collection">
            <li class="collection-item" style="animation-duration: 1s"><a class="waves-effect menu-list" href="<?php echo base_url('login') ?>">login</a></li>
            <li class="collection-item" style="animation-duration: 1s"><a class="waves-effect menu-list" href="register.html">register</a></li>
        </ul>
    </div>
</div><!-- ##### HEADER #####-->
<header class="app-bar header" id="header">
    <div class="container">
        <div class="header-content">
            <nav class="nav-logo">
                <button class="mobile-menu btn-icon waves-effect hamburger hamburger--spin show-md-down" id="mobile_menu" type="button">
                    <span class="hamburger-box"><span class="bar hamburger-inner"></span></span>
                </button>
                <div class="logo scrollnav">
                    <a href="#home"><img src="<?php echo base_url() ?>logosultra.png" alt="logo" /></a>
                </div>
            </nav>
            <nav class="nav-menu">
                <div class="scrollactive-nav show-lg-up scrollnav">
                    <ul>
                        <li><a class="btn btn-flat anchor-link waves-effect" href="#feature"><span class="text">Beranda</span></a></li>
                        <li><a class="btn btn-flat anchor-link waves-effect" href="#popular"><span class="text">Prestasi Pendidik</span></a></li>
                        <li><a class="btn btn-flat anchor-link waves-effect" href="#explore"><span class="text">Pengumuman</span></a></li>
                        <li><a class="btn btn-flat anchor-link waves-effect" href="#blog"><span class="text">blog</span></a></li>
                        <li><a class="btn btn-flat anchor-link waves-effect" href="contact.html"> <span class="text">contact</span></a></li>
                    </ul>
                </div>
            </nav>
            <nav class="nav-menu nav-auth">
                <div class="hidden-xs-down">
                    <div class="deco"></div>
                    <?php if (session()->get('logged_in')) : ?>
                        <a class="btn white light button waves-effect" href="<?= site_url('dashboard') ?>">Buka Dashboard</a>
                    <?php else : ?>
                        <a class="btn white light button waves-effect" href="<?= base_url('login') ?>">login</a>
                    <?php endif; ?>
                </div>
            </nav>
        </div>
    </div>
</header>