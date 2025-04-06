<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="row">
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <?= session("message"); ?>
            <?php } ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('business_access_statistics', 'Business Access Statistics') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-briefcase text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('no_of_businesses', 'No. of businesses') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($business_count) ? $business_count : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"> <?= labels('delivered_order_statistics', 'Delivered Order Statistics') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-cart-plus-fill text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_orders', 'Total Orders') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($orders_count) ? $orders_count : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('customers_statistics', 'Customers Statistics') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-people-fill text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('customers', 'Customers') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($customers_count) ? $customers_count : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('earning_statistics', 'Earings Statistics') ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row mt-4">
                            <div class="col-12 col-lg-8 offset-lg-2">
                                <div class="wizard-steps">
                                    <div class="wizard-step wizard-step-active">
                                        <div class="wizard-step-icon">
                                            <i class="fas fa-money-bill"></i>
                                        </div>
                                        <div>
                                            <label for=""><strong><?php echo isset($orders_count) ? $orders_count : "0" ?> </strong></label>
                                        </div>
                                        <div class="wizard-step-label">
                                            <?= labels('total_orders', 'Total Orders') ?>
                                        </div>
                                    </div>
                                    <div class="wizard-step wizard-step-success">
                                        <div class="wizard-step-icon">
                                            <i class="fas fa-credit-card"></i>
                                        </div>
                                        <div>
                                            <label for=""><strong><?php echo isset($total_amount_paid) ? currency_location(decimal_points($total_amount_paid)) : 0 ?> </strong></label>
                                        </div>
                                        <div class="wizard-step-label">
                                            <?= labels('total_order_payment', 'Total Order Payment') ?>
                                        </div>
                                    </div>
                                    <div class="wizard-step wizard-step-warning">
                                        <div class="wizard-step-icon">
                                            <i class="fas fa-wallet"></i>
                                        </div>
                                        <div>
                                            <label for=""><strong><?php echo isset($amount_left) ? currency_location(decimal_points($amount_left)) : 0 ?> </strong></label>
                                        </div>
                                        <div class="wizard-step-label">
                                            <?= labels('remaining_payments_amount', 'Remaining Payment Amount') ?>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('products_sales', 'Product Sales') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="158"></canvas>
                    </div>
                </div>
            </div>
        </div>


    </section>
</div>