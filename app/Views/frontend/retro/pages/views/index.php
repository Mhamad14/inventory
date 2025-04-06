<!-- main section -->
<?php $data = get_settings('about_us', true) ?>
<?php $data = get_settings('general', true) ?>

<div class="container container-wrapper d-flex justify-content-center  section-wrapper" id="main-section">
    <section>
        <div class="container py-14 py-md-16">
            <div class="row">
                <div class="col-md  hero-content">
                    <h1 class="lh-1">Welcome to <span class="logo-color fw-bold"><?= !empty($data['title']) ? $data['title'] : "UpBiz" ?></span></h1>
                    <p><?= !empty($data['short_description']) ? strip_tags($data['short_description']) : "Digital transformation of your traditional business." ?></p>
                    <div class="row">
                        <div class="col-md-3">
                            <button href="" class="btn btn-get-upbiz mt-2">Get Started</button>
                        </div>
                        <div class="col-md-3">
                            <a href="https://www.youtube.com/watch?v=jDDaplaOz7Q" class="btn btn-video mt-2"><i class="bi bi-play-circle m-1"></i> Watch Video</a>
                        </div>
                    </div>
                </div>
                <div class="col-md ">
                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/hero-1.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
                </div>
            </div>
            <div class="contact mt-2">
                <div class="row text-center" data-aos="fade-up" data-aos-delay="100">
                    <div class="col-md-4">
                        <div class="info-box mb-4">
                            <i class="bi bi-star ic"></i>
                            <h4>Cost Effective</h4>
                            <span>A reasonable amount and quality of services for the amount of money one spends.</span>
                        </div>
                    </div>

                    <div class="col-md-4 ">
                        <div class="info-box  mb-4">
                            <i class="bi bi-download ic"></i>
                            <h4>Prominent Features </h4>
                            <span>Tracking of orders , Payment records , Dealing business transactions</span>
                        </div>
                    </div>

                    <div class="col-md-4">
                        <div class="info-box mb-4">
                            <i class="bi bi-credit-card ic"></i>
                            <h4>Real Time Deal</h4>
                            <span>Digitally manage your complete business.</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </section>
</div>

<!-- features -->
<div class="container container-wrapper d-flex justify-content-center section-wrapper" id="features">
    <section>
        <div class="container py-14 py-md-16">
            <div class="row mb-5 ">
                <div class="section-title">
                    <div class="container">
                        <h2>How can we help you?</h2>
                    </div>
                </div>
            </div>
            <div class="row gx-md-8 gy-8">
                <div class="col-md-6 col-lg-3 ">

                    <div class=" d-flex justify-content-center">
                        <div class="features_icon btn-yellow mb-5">
                            <i class="bi bi-calendar-check mx-md-5"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4>Manage multiple businesses at one platform </h4>
                        <p class="mb-3">This allows you to run more than one business on a single platform, now manage your businesses separately. </p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex justify-content-center">
                        <div class="features_icon btn-red mb-5">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4>Track orders Payment</h4>
                        <p class="mb-3">This allows you to track all order payments and information access whether it is partially paid, overdue or fully paid.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex justify-content-center">
                        <div class="features_icon btn-green mb-5">
                            <i class="bi bi-bootstrap-reboot"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4>Auto renewal of services</h4>
                        <p class="mb-3">This will make it possible to automatically renew subscription services.</p>
                    </div>
                </div>
                <div class="col-md-6 col-lg-3">
                    <div class="d-flex justify-content-center">
                        <div class="features_icon btn-blue mb-5">
                            <i class="bi bi-brush"></i>
                        </div>
                    </div>
                    <div class="text-center">
                        <h4>Theme customization</h4>
                        <p class="mb-3">Choose your preferred theme and personalize it.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- end fetures -->

<!-- abot us-->
<div class="container container-wrapper d-flex justify-content-center section-wrapper" id="about">
    <section>
        <div class="container py-14 py-md-16">
            <div class="row">
                <div class="section-title">
                    <div class="container">
                        <h2>About</h2>
                        <p>Very simple solution for companies that includes subscription services as well as products, stock management, order tracking, payment tracking now easily managed at this platform. </p>
                    </div>
                </div>
            </div>
            <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
                <div class="col-lg-5">
                    <lottie-player src="<?= base_url('public/frontend/assets/retro/img/about-us-1.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>

                </div>
                <div class="col-lg-7 about-content">
                    <h4 class="text-center">Who / What we serve?</h4>
                    <p class="text-center">Small and medium-sized businesses that need daily monitoring of sales, purchase of products/services..</p>
                    <div class="d-flex justify-content-center">
                        <ul class="icon-list bullet-soft-fuchsia">
                            <li><i class="bi bi-check"></i>for transforming traditional approaches to business tracking.</li>
                            <li><i class="bi bi-check"></i>efficient point of sale (POS) system to create orders</li>
                            <li><i class="bi bi-check"></i>allows your delivery person to accept and track a service or product order.</li>
                        </ul>
                    </div>
                    <div class="col-md text-center">
                        <a href="<?= base_url('about') ?>" class="btn btn-get-upbiz mt-2">Learn More</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
<!-- End  -->

<!-- pricicing------------------------------>
<div class="container container-wrapper  section-wrapper" id="pricing">
    <section class="pricing contact wrapper bg-light">
        <div class="container py-14 py-md-16">
            <div class="row">
                <div class="section-title">
                    <div class="container">
                        <h2>Pricing</h2>
                    </div>
                </div>
            </div>
            <div class="row gy-4 p-5 mt-5">
                <?php if (isset($packages) && !empty($packages)) { ?>
                    <?php
                    $i = 0;
                    foreach ($packages as $key => $value) {
                        $tenures = $value['tenures'];
                        if ($i == 4) {
                            break;
                        }
                    ?>
                        <div class="col-lg-3 col-md-6">
                            <div class="box">
                                <h3 class="title"><?= ucwords($value['title']) ?> </h3>
                                <div class="price d-inline-flex"><?= $currency ?>
                                    <div id='price<?= $value['id'] ?>'><?php if ($tenures[0]['discounted_price'] != 0) {
                                                                            echo number_format($tenures[0]['discounted_price']) ?> <small class="discount-font">(<del> <?= currency_location(decimal_points($tenures[0]['price']))?></del>)</small>
                                        <?php } else {
                                                                            echo number_format($tenures[0]['price']);
                                                                        } ?>

                                    </div>

                                </div>
                                <div class="container">
                                    <div class="form-group">
                                        <select class="form-control tenures" data-package_id="<?= $value['id'] ?>" name="tenures">
                                            <?php for ($j = 0; $j < count($tenures); $j++) { ?>
                                                <option value="<?= $tenures[$j]['price'] ?>" data-price='<?= $tenures[$j]['price'] ?>' data-discount="<?= $tenures[$j]['discounted_price'] ?>"><?= $tenures[$j]['tenure'] ?></option>
                                            <?php } ?>

                                        </select>
                                    </div>
                                </div>
                                <ul>
                                    <li></li>
                                    <li><?= "No. of businesses " . $value['no_of_businesses']; ?></li>
                                    <li><?= "No. of customers " . $value['no_of_customers']; ?></li>
                                    <li><?= "No. of delivery boys " . $value['no_of_delivery_boys']; ?></li>
                                    <li><?= "No. of products " . $value['no_of_products']; ?></li>
                                </ul>
                                <div class="link">
                                    <a class="btn btn-sm btn-get-upbiz rounded m-1" href="<?= isset($vendor) ? base_url('vendor/subscription/packages') : base_url('login') ?>">Buy Now</a>
                                </div>
                            </div>
                        </div>
                    <?php $i++;
                    } ?>
                    <div class="text-center mt-5">
                        <a href="<?= base_url('pricing') ?>" class="btn btn-get-upbiz mt-2">See More</a>
                    </div>
                <?php } else { ?>

                    <div class="section-title">
                        <h4>Package doesn't exist yet!</h4>
                    </div>
                <?php } ?>

            </div>
    </section>
</div>
<!-- end -->

<!-- faqs -->
<div class="container container-wrapper section-wrapper my-5" id="faq">
    <section class="wrapper">
        <div class="container py-14 py-md-16">
            <div class="row">
                <div class="section-title">
                    <div class="container">
                        <h2>FAQs</h2>
                    </div>
                </div>
            </div>
            <div class="row gx-lg-8 gx-xl-12 gy-10">
                <div class="col-md">
                    <div class="faq faq-wrapper" id="faq_div">

                        <div class="card plain faq-item">
                            <div class="card-header" id="headingOne-2">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-0" aria-expanded="false" aria-controls="question-1"> What is <?= !empty($data['title']) ? $data['title'] : "UpBiz" ?>? </button>
                            </div>
                            <div id="question-0" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                                <div class="card-body">
                                    <p>The <?= !empty($data['title']) ? $data['title'] : "UpBiz" ?> system will make it possible to keep track of orders ,products and services through Website. an easy approach to turning your traditional way of doing business into a digital platform.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card plain faq-item">
                            <div class="card-header" id="headingOne-2">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-1" aria-expanded="false" aria-controls="question-1"> What / Where this system can be used? </button>
                            </div>
                            <div id="question-1" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                                <div class="card-body">
                                    <p>small to medium scale businesses Ex, Wholeseller, Vendor, Retailers, Shop owner, Urban service provider, Good suppliers</p>
                                </div>
                            </div>
                        </div>

                        <div class="card plain faq-item">
                            <div class="card-header" id="headingOne-2">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-2" aria-expanded="false" aria-controls="question-2"> What benefit will it do to my business? </button>
                            </div>
                            <div id="question-2" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                                <div class="card-body">
                                    <p>Rapid order placement, payment tracking and inventory management will help businesses that rely on bills and pens.</p>
                                </div>
                            </div>
                        </div>

                        <div class="card plain faq-item">
                            <div class="card-header" id="headingOne-2">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-3" aria-expanded="false" aria-controls="question-3"> Which is the best digital platform for my business? </button>
                            </div>
                            <div id="question-3" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                                <div class="card-body">
                                    <p> <?=
                                        !empty($data['title']) ? $data['title'] : "UpBiz" ?> </p>
                                </div>
                            </div>
                        </div>
                        <div class="card plain faq-item pb-4">
                            <div class="card-header" id="headingOne-2">
                                <button class="accordion-button collapsed" data-bs-toggle="collapse" data-bs-target="#question-4" aria-expanded="false" aria-controls="question-4"> How it will help your business to transform into digital platform ? </button>
                            </div>
                            <div id="question-4" class="faq-collapse collapse" aria-labelledby="headingOne-2" data-bs-parent="#faq_div">
                                <div class="card-body">
                                    <p>Top features of this system which will transorm your business</p>
                                    <p>1.Pos systen for orders</p>
                                    <p>2.Auto renew subscriptions</p>
                                    <p>3.Delivery Challan</p>
                                    <p>4.Cash Flow</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

</div>
<!-- faqs end -->