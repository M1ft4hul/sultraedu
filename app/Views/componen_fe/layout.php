<?php include 'style.php'; ?>

<body>
    <div id="preloader" style="position: fixed; z-index: 10000; background: #fafafa; width: 100%; height: 100%"><img style="opacity: 0.5; position: fixed; top: calc(50% - 50px); left: calc(50% - 50px)" src="<?php echo base_url() ?>assets/images/loading.gif" alt="loading"></div>
    <div class="m-application theme--light transition-page" id="app">
        <div class="loading"></div>
        <div class="m-content smart smart-var" id="main-wrap">
            <div>
                <div class="main-wrap">
                    <?php include 'menu.php'; ?>
                    <!-- ##### END HEADER #####-->
                    <main class="container-wrap">
                        <!-- ##### BANNER #####-->

                        <?php echo $this->renderSection('content') ?>

                        <?php include 'footer.php' ?>
                    </main>
                    <!-- ##### PAGE NAVE #####-->
                    <div class="hidden-md-down">
                        <div class="page-nav" id="page_nav">
                            <nav class="section-nav">
                                <div class="scrollnav">
                                    <ul>
                                        <li style="top: 120px"><a class="tooltipped" href="#feature" data-position="left" data-tooltip="main feature"></a></li>
                                        <li style="top: 90px"><a class="tooltipped" href="#popular" data-position="left" data-tooltip="popular course"></a></li>
                                        <li style="top: 60px"><a class="tooltipped" href="#explore" data-position="left" data-tooltip="explore"></a></li>
                                        <li style="top: 30px"><a class="tooltipped" href="#blog" data-position="left" data-tooltip="blog"></a></li>
                                    </ul>
                                </div>
                            </nav>
                            <div class="scrollnav">
                                <a class="btn-floating btn-large primary tooltipped waves-effect waves-light" href="#home" data-position="left" data-tooltip="To Top">
                                    <div class="icon material-icons">arrow_upward</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div><!-- Scripts--><!-- Put the 3rd/plugins javascript here-->

    <?php include 'script.php'; ?>