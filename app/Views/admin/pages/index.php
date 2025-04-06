<!-- Main Content -->
<div class="main-content">
    <section class="section">

        <!-- card section -->
        <div class="container-fluid card mt-2">
            <h2 class="section-title"><?= labels('dashborad', 'Dashboard') ?></h2>
        </div>

        <div class="row">
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <script>
                    iziToast.error({
                        title: 'Error',
                        message: "<?php echo $session->get('message'); ?>", // Echo the session message properly
                        position: "topRight",
                    });
                </script>
            <?php } ?>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('businss_statistics', 'Business Statistics') ?>
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
                        <div class="card-stats-title"><?= labels('order_statistics', 'Order Statistics') ?>
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
                        <div class="card-stats-title"><?= labels('products_statistics', 'Products Statistics') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-box text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_products', 'Total Products') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($products_count) ? $products_count : 0 ?>
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
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('delivery_boys_statistics', 'Delivery Boys Statistics') ?>
                        </div>

                        <div class="card-icon shadow-primary bg-primary">
                            <i class="bi bi-person-video text-white"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('delivery_boys', 'Delivery Boys') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($delivery_boys_count) ? $delivery_boys_count : 0 ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('overalls_statistics', 'Overall Statistics ') ?>
                            <div class="card-wrap">
                                <div class="row">
                                    <div class="col-md">
                                        <div class="  card-header">
                                            <h4><?= labels('orders', ' Orders') ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <?php echo isset($overall_orders) ? $overall_orders : 0 ?>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="card-header">
                                            <h4><?= labels('products', 'Products') ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <?php echo isset($overall_products) ? $overall_products : 0 ?>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="  card-header">
                                            <h4><?= labels('customers', 'Customers') ?></h4>
                                        </div>
                                        <div class="card-body">
                                            <?php echo isset($overall_customers) ? $overall_customers : 0 ?>
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
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <script>
                    iziToast.error({
                        title: 'Error',
                        message: "<?php echo $session->get('message'); ?>", // Echo the session message properly
                        position: "topRight",
                    });
                </script>
            <?php } ?>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('purchase_sales_statistics', 'Sales Statistics') ?>
                        </div>
                        <div class="card-icon shadow-primary badge-custom">
                            <i class="bi bi-sort-up-alt "></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_Sales', 'Total Sales') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($sales_purchase) ? currency_location(decimal_points($sales_purchase['total_sale'][0])) : 0 ?>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="card card-statistic-2">
                    <div class="card-stats">
                        <div class="card-stats-title"><?= labels('purchase_sales_statistics', 'Purchase Statistics') ?>
                        </div>
                        <div class="card-icon shadow-primary badge-custom">
                            <i class="bi bi-box-arrow-right"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4><?= labels('total_purchases', 'Total Purchases') ?></h4>
                            </div>
                            <div class="card-body">
                                <?php echo isset($sales_purchase) ? currency_location(decimal_points($sales_purchase['total_purchases'][0])) : 0 ?>
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
        <div class="card">
            <div class="card-header">
                <h4><?= labels('stock_statistics', 'Stock Statistics') ?></h4>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-primary">
                                <i class="far fa-user"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?= labels('total_products', 'Total Products') ?></h4>
                                </div>
                                <div class="card-body">
                                    <?php echo isset($products_count) ? $products_count : 0 ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-warning">
                                <i class="far fa-file"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?= labels('low_in_stock', 'Low in Stock') ?></h4>
                                </div>
                                <div class="card-body">
                                    <?= $low ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-4 col-md-6 col-sm-6 col-12">
                        <div class="card card-statistic-1">
                            <div class="card-icon bg-danger">
                                <i class="far fa-newspaper"></i>
                            </div>
                            <div class="card-wrap">
                                <div class="card-header">
                                    <h4><?= labels('out_of_stock', 'Out of Stock') ?></h4>
                                </div>
                                <div class="card-body">
                                    <?= $out ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <a href="<?= base_url('admin/products') ?>" class="text-decoration-none small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
                </div>
                <div class="row">
                    <table class="table table-hover table-borderd" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/products/products_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                <th data-field="type" data-sortable="true" data-visible="true"><?= labels('product_type', 'Product Type') ?></th>
                                <th data-field="stock" data-sortable="true" data-visible="true"><?= labels('stock', 'Stock(qty)') ?></th>
                                <th data-field="qty_alert" data-sortable="true" data-visible="true"><?= labels('quantity_alert', 'Quantity Alert') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('total_sales', 'Total Sales') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="myChart" height="158"></canvas>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('business_insights', 'Business Insights') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="pieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('sales-per-warehouse', 'Sales per Warehouse') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="sales-per-warehouse-chart" height="158"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('purchases_sales', 'Purchases vs Sales') ?></h4>
                    </div>
                    <div class="card-body">
                        <canvas id="charttest" height="158"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>