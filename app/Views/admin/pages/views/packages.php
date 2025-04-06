<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1> <?= labels('packages', 'Packages') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/subscription'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('subscription', 'Subscription') ?></a>
                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-md">
                <h2 class="section-title"><?= labels('active_packages', 'Active packages') ?></h2>
            </div>
        </div>

        <div class="row">
            <?php foreach ($packages as $package) {
            ?>
                <?php
                $tenures = $package['tenures'];
                ?>
                <div class="col-12 col-md-4 col-lg-4">
                    <form id="packages_form" method="get" action="<?= base_url('admin/subscription/checkout/') ?>">
                        <input type="hidden" name="package_id" value="<?= $package['id'] ?>">

                        <div class="pricing pricing-highlight shadow">
                            <div class="pricing-title ">
                                <?= $package['title']; ?>
                            </div>
                            <div class="pricing-padding">
                                <div class="pricing-price">

                                    <div><?= $currency ?><span class="price price-font" id="price<?= $package['id'] ?>"><?php if ($tenures[0]['discounted_price'] != 0) {
                                                                                                                            echo $tenures[0]['discounted_price'] ?> <small class="discount-font">(<del> <?= currency_location (decimal_points($tenures[0]['price'] ))?></del>)</small>
                                            <?php } else {
                                                                                                                            echo $tenures[0]['price'];
                                                                                                                        } ?>
                                        </span></div>
                                    <div class="col-md-6 offset-md-3">
                                        <select class="form-control tenures" id="tenure_id" data-package_id="<?= $package['id'] ?>" name="tenures">
                                            <options>Select Tenure</option>
                                                <?php for ($j = 0; $j < count($tenures); $j++) { ?>
                                                    <option value="<?= $tenures[$j]['id'] ?>" data-tenure_id='<?= $tenures[$j]['id'] ?>' data-price='<?= $tenures[$j]['price'] ?>' data-discount="<?= $tenures[$j]['discounted_price'] ?>"><?= $tenures[$j]['tenure'] ?></option>
                                                <?php } ?>
                                        </select>
                                    </div>


                                </div>
                                <div class="pricing-details" id="discount_price<?= $package['id'] ?>">

                                    <div class="pricing-item">
                                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                        <div class="pricing-item-label"> <?= labels('no_of_businesses', 'No. of businesses ') . " " . $package['no_of_businesses']; ?></div>
                                    </div>
                                    <div class="pricing-item">
                                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                        <div class="pricing-item-label"><?= labels('No_of_customers', 'No. of customers') . " " . $package['no_of_customers']; ?></div>
                                    </div>
                                    <div class="pricing-item">
                                        <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                        <div class="pricing-item-label"><?= labels('No_of_delivery_boys', 'No. of delivery boys') . " " . $package['no_of_delivery_boys']; ?></div>
                                    </div>
                                    <div class="pricing-item">
                                        <div class="pricing-item-icon "><i class="fas fa-check"></i></div>
                                        <div class="pricing-item-label"><?= labels('No_of_products', 'No. of products') . " " . $package['no_of_products']; ?></div>
                                    </div>
                                    <div class="pricing-item ">
                                        <div class="pricing-item-icon <?= $tenures[0]['discounted_price'] > 0 ? 'bg-success' : 'bg-danger' ?>"><i class="fas <?= $tenures[0]['discounted_price'] > 0 ? ' fa-check' : ' fa-times' ?>"></i></div>
                                        <div class="pricing-item-label"><?= labels('discounted_price', 'Discounted Price') ?>
                                            <span id="discount_price<?= $package['id'] ?>">
                                                <?= currency_location ($tenures[0]['discounted_price']) ?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <?php
                            if ($package['type'] == "free" && $tenures[0]['price'] == "0") { ?>
                                <div class="pricing-cta">
                                    <button type="button" class="btn btn-primary free_package" data-months="<?= $tenures[0]['months'] ?>" data-price="<?= $tenures[0]['price'] ?>" data-tenure="<?= $tenures[0]['tenure'] ?>" data-no_of_products="<?= $package['no_of_products'] ?>" data-no_of_delivery_boys="<?= $package['no_of_delivery_boys']; ?>" data-no_of_customers="<?= $package['no_of_customers'] ?>" data-no_of_businesses="<?= $package['no_of_businesses'] ?>" data-package_name="<?= $package['title'] ?>" data-user_id="<?= $user_id ?>" data-package_id="<?= $package['id'] ?>">
                                        <?= labels('subscribe', 'Subscribe') ?>
                                    </button>
                                </div>
                            <?php } else { ?>
                                <div class="pricing-cta">
                                    <button class="btn btn-primary" data-package_id="<?= $package['id'] ?>">
                                        <?= labels('subscribe', 'Subscribe') ?>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                </div>
                </form>
            <?php }
            ?>
        </div>
    </section>
</div>