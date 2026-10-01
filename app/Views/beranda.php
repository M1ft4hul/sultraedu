<?php echo $this->extend('componen_fe/layout') ?>

<?php echo $this->section('content') ?>
<section id="home">
    <div class="hero-content">
        <div class="container mq-lg-up" data-class="fixed-width">
            <div class="row banner-wrap">
                <div class="col-lg-6 col-md-7 col-sm-12">
                    <div class="banner-text">
                        <div class="title">
                            <h3 class="use-text-title">Raih Prestasi, Tunjukkan Potensimu!</h3>
                        </div>
                        <h5 class="subtitle">Ajang kompetisi pendidikan bagi pelajar Sulawesi Tenggara
                            untuk mengembangkan bakat, kreativitas, dan kemampuan terbaiknya.</h5>
                        <button class="btn btn secondary waves-effect" href="login.html"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 4h-3V3c0-.55-.45-1-1-1H7c-.55 0-1 .45-1 1v1H3c-.55 0-1 .45-1 1v3c0 4.29 1.79 6.88 4.81 6.99A6 6 0 0 0 11 17.91V20H8v2h8v-2h-3v-2.09a5.98 5.98 0 0 0 4.19-2.92C20.2 14.88 22 12.29 22 8V5c0-.55-.45-1-1-1M4 8V6h2v6c0 .28.03.56.06.83C4.22 12.12 4 9.31 4 8m12 4c0 2.21-1.79 4-4 4s-4-1.79-4-4V4h8zm4-4c0 1.31-.22 4.12-2.06 4.83.04-.27.06-.55.06-.83V6h2z"></path>
                            </svg>
                            Daftar Sekarang</button>
                    </div>
                </div>
                <div class="col-lg-6 col-md-5 col-sm-12 pa-6 show-sm-up deco-grid">
                    <div class="deco-banner">
                        <div class="artwork-bg">
                            <div class="oval"></div>
                            <div class="parallax-scene back">
                                <div id="scene1">
                                    <div data-depth="0.3"><span class="icon-three"></span></div>
                                    <div data-depth="0.2"><span class="icon-four"></span></div>
                                </div>
                            </div>
                            <img src="<?php echo base_url() ?>assets/images/education/download.png" alt="artwork" />
                            <div class="parallax-scene front">
                                <div id="scene2">
                                    <div data-depth="0.1"><span class="icon-two"></span></div>
                                    <div data-depth="0.15"><span class="icon-one"></span></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ##### END BANNER #####--><!-- ##### FEATURE #####-->
<section class="space-top" id="feature">
    <div class="root">
        <div class="container max-md">
            <div class="title-main align-center">
                <h4 class="primary"><span>Main Feature</span></h4>
                <p class="desc use-text-subtitle2">The world's largest selection of courses</p>
            </div>
            <div class="row spacing8 grid">
                <div class="col-sm-6 px-6">
                    <div class="counter-item">
                        <figure><img src="<?php echo base_url() ?>assets/images/education/hd-video.svg" alt="hd-video"></figure>
                        <div class="text">
                            <h4 class="use-text-title">

                                +<span class="numscroller" data-min="0" data-max="100" data-delay="5" data-increment="4">&nbsp;</span>K

                            </h4>
                            <h6 class="use-text-subtitle2">HD Videos</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 px-6">
                    <div class="counter-item">
                        <figure><img src="<?php echo base_url() ?>assets/images/education/presenter.svg" alt="presenter"></figure>
                        <div class="text">
                            <h4 class="use-text-title">
                                +<span class="numscroller" data-min="0" data-max="200" data-delay="5" data-increment="10">&nbsp;</span>
                            </h4>
                            <h6 class="use-text-subtitle2">Professional Mentors</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 px-6">
                    <div class="counter-item">
                        <figure><img src="<?php echo base_url() ?>assets/images/education/money.svg" alt="money"></figure>
                        <div class="text">
                            <h4 class="use-text-title">
                                $<span class="numscroller" data-min="0" data-max="500" data-delay="5" data-increment="20">&nbsp;</span>
                            </h4>
                            <h6 class="use-text-subtitle2">Saves per Month</h6>
                        </div>
                    </div>
                </div>
                <div class="col-sm-6 px-6">
                    <div class="counter-item">
                        <figure><img src="<?php echo base_url() ?>assets/images/education/unlimited.svg" alt="unlimited"></figure>
                        <div class="text">
                            <h4 class="use-text-title">Free</h4>
                            <h6 class="use-text-subtitle2">Life Time Access</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ##### END FEATURE #####--><!-- ##### POPULAR COURSE #####-->
<section class="space-top-short" id="popular">
    <div class="root">
        <div class="parallax-wrap">
            <div class="parallax-wrap dots-wrap">
                <div class="inner-parallax">
                    <div class="figure">
                        <div data-enllax-ratio="-0.2" data-enllax-type="foreground">
                            <img class="parallax-vertical parallax-dot" src="<?php echo base_url() ?>assets/images/decoration/dot-deco.svg" alt="dot" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="floating-title">
                <div class="title-main align-left dark">
                    <h4 class="primary"><span>Popular Course</span></h4>
                    <p class="desc use-text-subtitle2">Choose from many options of popular course at a breakthrough price.</p>
                </div>
            </div>
        </div>
        <div class="slider-wrap">
            <div class="carousel">
                <div class="slick-carousel" id="course_carousel" data-length="6">
                    <div class="props item-props-first show-md-up">
                        <div></div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Instant Kendo UI Mobile</h6>
                                <p class="use-text-paragraph">Filled with practical, step-by-step instructions and clear explanations for the most important and useful tasks.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>

                                        <i class="material-icons star-icon" title="4">star</i>

                                        <i class="material-icons star-icon" title="5">star</i>
                                    </div><strong>$50</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Joy of Cooking in the House</h6>
                                <p class="use-text-paragraph">The famously irreverent chef also offers playful riffs on classics, reimagining tuna-and-rice bowls.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>

                                        <i class="material-icons star-icon" title="4">star</i>
                                    </div><strong>$10</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Face the Music: A Life Exposed</h6>
                                <p class="use-text-paragraph">Face the Music is the shocking inspirational story of one of rock’s most enduring icons.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>

                                        <i class="material-icons star-icon" title="4">star</i>

                                        <i class="material-icons star-icon" title="5">star</i>
                                    </div><strong>$50</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Metaverse For Beginners</h6>
                                <p class="use-text-paragraph">When people talk about the future, they usually mean virtual reality.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>
                                    </div><strong>$25</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Practical Ethics in Sport Management</h6>
                                <p class="use-text-paragraph">Leaders and managers throughout the sporting world face many ethical challenges on a daily basis.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>

                                        <i class="material-icons star-icon" title="4">star</i>

                                        <i class="material-icons star-icon" title="5">star</i>
                                    </div><strong>$50</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="item">
                        <div class="card general-card">
                            <figure>
                                <img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="title" />
                            </figure>
                            <div class="desc">
                                <h6 class="title pb-2">Economic Geography: Past Present Future</h6>
                                <p class="use-text-paragraph">The impact of economic geography both within the field of geography.</p>
                                <div class="property">
                                    <div class="rating">
                                        <i class="material-icons star-icon" title="1">star</i>

                                        <i class="material-icons star-icon" title="2">star</i>

                                        <i class="material-icons star-icon" title="3">star</i>

                                        <i class="material-icons star-icon" title="4">star</i>

                                        <i class="material-icons star-icon" title="5">star</i>
                                    </div><strong>$40</strong>
                                </div>
                                <a class="button btn btn-outlined primary waves-effect" href="#">Explore</a>
                            </div>
                        </div>
                    </div>
                    <div class="props item-props-last show-md-up">
                        <div></div>
                    </div>
                </div>
                <button class="btn-floating nav prev waves-effect" id="prev_project">
                    <i class="ion-ios-arrow-back"></i>
                </button>
                <button class="btn-floating nav next waves-effect" id="next_project">
                    <i class="ion-ios-arrow-forward"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<!-- ##### END POPULAR COURSE #####--><!-- ##### EXPLORE #####-->
<section id="explore">
    <div class="root">
        <div class="parallax-wrap">
            <div class="parallax-wrap dots-wrap">
                <div class="inner-parallax">
                    <div class="figure">
                        <div data-enllax-ratio="-0.2" data-enllax-type="foreground">
                            <img class="parallax-vertical parallax-dot" src="<?php echo base_url() ?>assets/images/decoration/dot-deco.svg" alt="dot" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container fixed-width-md-up">
            <div class="px-6">
                <div class="title-main align-left">
                    <h4 class="primary"><span>Explore Course</span></h4>
                    <p class="desc use-text-subtitle2">Choose from 100,000 online video courses with new additions published every month.</p>
                </div>
            </div>
            <div class="massonry">
                <div class="row">
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="0s" data-wow-duration="0.4s">
                            <div class="card-wrap">
                                <span class="fold"></span>
                                <a class="waves-effect category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">Photography</span>
                                        <span class="desc">Nulla lobortis nunc vitae nisi semper semper.</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="0.2s" data-wow-duration="0.4s">
                            <div class="card-wrap">
                                <span class="fold"></span>
                                <a class="waves-effect category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">Artificial Intelligence</span>
                                        <span class="desc">Nulla lobortis nunc vitae nisi semper semper.</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="0.4s" data-wow-duration="0.4s">
                            <div class="card-wrap">
                                <span class="fold"></span>
                                <a class="waves-effect category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">Architect</span>
                                        <span class="desc">Nulla lobortis nunc vitae nisi semper semper.</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="0.6000000000000001s" data-wow-duration="0.4s">
                            <div class="card-wrap">
                                <span class="fold"></span>
                                <a class="waves-effect category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">Geography</span>
                                        <span class="desc">Nulla lobortis nunc vitae nisi semper semper.</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="0.8s" data-wow-duration="0.4s">
                            <div class="card-wrap">
                                <span class="fold"></span>
                                <a class="waves-effect category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/270x320/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">Art</span>
                                        <span class="desc">Nulla lobortis nunc vitae nisi semper semper.</span>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6 col-6 pa-md-6 pa-2">
                        <div class="wow fadeInUpShort" data-wow-delay="1s" data-wow-duration="0.4s">
                            <div class="card-wrap"><span class="fold"></span><a class="waves-effect waves-light all-category-card" href="#">
                                    <span class="figure"><img src="https://via.placeholder.com/364x258/189a96/FFFFFF" alt="img" /></span>
                                    <span class="property"><span class="title-category">ALL COURSE</span>
                                        <i class="icons material-icons">arrow_forward</i>
                                    </span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ##### END EXPLORE #####--><!-- ##### ABOUT #####-->
<div id="about">
    <div class="root">
        <div class="container fixed-width">
            <div class="row">
                <div class="col-md-5 col-sm-12 illu-wrap">
                    <div class="hidden-sm-down">
                        <div class="illustration one"></div>
                        <figure class="illustration two">
                            <img src="https://via.placeholder.com/456x304/36a2c9/FFFFFF" alt="about" />
                        </figure>
                        <figure class="illustration three">
                            <img src="https://via.placeholder.com/237x158/98ad25/FFFFFF" alt="about" />
                        </figure>
                        <figure class="illustration four">
                            <img src="https://via.placeholder.com/317x211/d03c3c/FFFFFF" alt="about" />
                        </figure>
                        <div class="illustration five"></div>
                    </div>
                </div>
                <div class="col-md-7 col-sm-12">
                    <div class="wow fadeInRight" data-wow-offset="-100" data-wow-delay="0.2s" data-wow-duration="0.6s">
                        <div>
                            <div class="title-about">
                                <h3><span>About us</span></h3>
                            </div>
                            <p class="use-text-subtitle2">Our mission is to diversify the tech industry through accessible education and apprenticeship, unlocking the door to opportunity and empowering people to achieve their dreams.</p><a class="btn white waves-effect" href="login.html">Join Now</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ##### END ABOUT #####--><!-- ##### TESTIMONIALS #####-->
<section class="space-top" id="testimonials">
    <div class="root">
        <div class="title-main align-center">
            <h4 class="primary"><span>Testimonials</span></h4>
            <p class="desc use-text-subtitle2">They Are Doing Great Things With Us</p>
        </div>
        <div class="slider-wrap">
            <div class="carousel">
                <button class="btn-floating nav prev waves-effect" id="prev_testi">
                    <i class="ion-ios-arrow-back"></i>
                </button>
                <div class="slick-carousel" id="testimonial_carousel">
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">Taking this course will help me to learn and study this SAP ERP system Training and also to implement it.</p>
                            <h6>John Doe</h6>
                            <p class="caption">Chief Digital Officer</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">It is a great app. Although it seems one can't get a certification after accessing the 24hours unlocked Courses even after watching, notwithstanding it great and there always took for improvement.</p>
                            <h6>Jean Doe</h6>
                            <p class="caption">Chief Digital Officer</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">This has been the best app to continue learning and improving. If I had known about this app when I was working full time I could have been a supervisor.</p>
                            <h6>Jena Doe</h6>
                            <p class="caption">Graphic Designer</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">This is a hub of all the required learnings for a corporate employee. It not only includes the corporate skill related courses but also the skills of personal development.</p>
                            <h6>Jovelin Doe</h6>
                            <p class="caption">Senior Graphic Designer</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">Variety of curses on same topics so you can learn with comparisons so I will say it's a great choice to make.</p>
                            <h6>Jihan Doe</h6>
                            <p class="caption">CEO Software House</p>
                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-card">
                            <div class="icon"><i class="material-icons">format_quote</i></div>
                            <p class="text">Great video. Simplicity and effectiveness was so adaptly exhibited.</p>
                            <h6>Jovelin Doe</h6>
                            <p class="caption">Senior Graphic Designer</p>
                        </div>
                    </div>
                </div>
                <button class="btn-floating nav next waves-effect" id="next_testi">
                    <i class="ion-ios-arrow-forward"></i>
                </button>
            </div>
        </div>
    </div>
</section>
<!-- ##### END TESTIMONIALS #####--><!-- ##### BLOG #####-->
<section class="space-top-short" id="blog">
    <div class="root">
        <div class="modal video-popup" id="video_modal">
            <div class="modal-content">
                <div class="headline">
                    <h4>Home Interior Design - Video Tour</h4>
                </div>
                <button class="btn-icon modal-close waves-effect"><i class="material-icons">close</i></button>
                <div class="text-center">
                    <div id="video_iframe"></div>
                </div>
            </div>
        </div>
        <div class="hidden-sm-down">
            <div class="deco"></div>
        </div>
        <div class="container fixed-width">
            <div class="px-md-12 pa-2">
                <div class="title-main align-left">
                    <h4 class="secondary"><span>What's update?</span></h4>
                    <p class="desc use-text-subtitle2">Up-to-the-minute news, breaking news, video, audio and feature stories</p>
                </div>
            </div>
            <div class="row flex-sm-row-reverse">
                <div class="col-md-6 col-sm-12 pa-md-8 pa-0">
                    <div class="video-wrap">
                        <div class="video-carousel">
                            <div class="carousel">
                                <div class="slick-carousel" id="blog_carousel">
                                    <div class="card item">
                                        <img src="https://via.placeholder.com/700x462/8e8e8e/FFFFFF" alt="cover" />
                                        <button class="btn play-btn waves-effect modal-trigger" data-video="6p0VM-yUpGk" data-target="video_modal">
                                            <i class="material-icons">play_arrow</i>
                                        </button>
                                    </div>
                                    <div class="card item">
                                        <img src="https://via.placeholder.com/700x462/52596b/FFFFFF" alt="cover" />
                                        <button class="btn play-btn waves-effect modal-trigger" data-video="HBeJA3q19mk" data-target="video_modal">
                                            <i class="material-icons">play_arrow</i>
                                        </button>
                                    </div>
                                    <div class="card item">
                                        <img src="https://via.placeholder.com/700x463/8e8e8e/FFFFFF" alt="cover" />
                                        <button class="btn play-btn waves-effect modal-trigger" data-video="6p0VM-yUpGk" data-target="video_modal">
                                            <i class="material-icons">play_arrow</i>
                                        </button>
                                    </div>
                                    <div class="card item">
                                        <img src="https://via.placeholder.com/700x463/52596b/FFFFFF" alt="cover" />
                                        <button class="btn play-btn waves-effect modal-trigger" data-video="HBeJA3q19mk" data-target="video_modal">
                                            <i class="material-icons">play_arrow</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12 pa-md-8 pa-2">
                    <div class="blog-list">
                        <div class="wow fadeInLeftShort" data-wow-offset="-200" data-wow-delay="0s" data-wow-duration="0.6s">
                            <div class="blog-card">
                                <div class="text">
                                    <a class="btn-flat" href="#"><span>Science - Math</span></a>
                                    <h4>
                                        <a class="btn-flat waves-effect" href="#">Learn about the essential factors that should drive your selection of an LMS.</a>
                                    </h4>
                                </div>
                                <div class="date">
                                    <h3>Feb</h3>
                                    <h2>08</h2>
                                    <h4>2021</h4>
                                </div>
                            </div>
                        </div>
                        <div class="wow fadeInLeftShort" data-wow-offset="-200" data-wow-delay="0.2s" data-wow-duration="0.6s">
                            <div class="blog-card">
                                <div class="text">
                                    <a class="btn-flat" href="#"><span>Science - Math</span></a>
                                    <h4>
                                        <a class="btn-flat waves-effect" href="#">The Essential Guide to LMS evaluation to build a better teaching &amp; learning experience</a>
                                    </h4>
                                </div>
                                <div class="date">
                                    <h3>Feb</h3>
                                    <h2>08</h2>
                                    <h4>2021</h4>
                                </div>
                            </div>
                        </div>
                        <div class="wow fadeInLeftShort" data-wow-offset="-200" data-wow-delay="0.4s" data-wow-duration="0.6s">
                            <div class="blog-card">
                                <div class="text">
                                    <a class="btn-flat" href="#"><span>Science - Math</span></a>
                                    <h4>
                                        <a class="btn-flat waves-effect" href="#">Empower educators and improve institutional effectiveness.</a>
                                    </h4>
                                </div>
                                <div class="date">
                                    <h3>Feb</h3>
                                    <h2>08</h2>
                                    <h4>2021</h4>
                                </div>
                            </div>
                        </div>
                        <a class="more secondary btn-flat waves-effect">more<i class="material-icons">arrow_forward</i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<!-- ##### END BLOG #####--><!-- ##### SUBSCRIBE #####-->
<section class="space-top-short" id="subscribe">
    <div class="root">
        <div class="parallax-form-wrap">
            <div class="parallax" data-enllax-ratio="0.2"></div>
        </div>
        <div class="container fixed-width">
            <div class="card form">
                <h4 class="use-text-title2">Stay in touch</h4>
                <p class="use-text-subtitle2">Subscribe to our newsletter and stay updated on the latest developments and special offers!</p>
                <form>
                    <div class="field">
                        <div class="input-field field dark">
                            <input type="email" name="email" id="email">
                            <label for="email">Enter your email address</label>
                        </div>
                    </div>
                    <button class="btn btn-large primary button waves-effect">Subscribe</button>
                </form>
            </div>
        </div>
    </div>
</section>
<!-- ##### END SUBSCRIBE #####--><!-- ##### FOOTER #####-->
<?= $this->endSection() ?>