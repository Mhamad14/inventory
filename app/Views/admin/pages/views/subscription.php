    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('subscription', 'Subscription') ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/subscription/packages'); ?>" class="btn"><i class="fas fa-plus"> </i> <?= labels('buy_renew_plan', 'Buy/Renew Plan') ?> </a>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                </div>
            </div>
            <div class="row">
                <div class="col-md">
                    <div class="alert alert-danger d-none" id="add_subscription_result"> </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-red"><?= session("message"); ?></label></div>
            <?php } ?>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="row">
                                <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "subscriptions-list","ignoreColumn": ["action"]}' id="package_table" data-auto-refresh="true" data-show-columns="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/subscription/package_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="package_name" data-sortable="true"><?= labels('package_name', 'Package Name') ?></th>
                                            <th data-field="status"><?= labels('status', 'Status') ?></th>
                                            <th data-field="no_of_businesses" data-sortable="true" data-visible="false"> <?= labels('no_of_businesses', 'No. of businesses') ?></th>
                                            <th data-field="no_of_delivery_boys" data-sortable="true" data-visible="false"><?= labels('No_of_delivery_boys', 'No. of delivery boys') ?></th>
                                            <th data-field="no_of_products" data-sortable="true" data-visible="false"><?= labels('No_of_products', 'No. of products') ?></th>
                                            <th data-field="no_of_customers" data-sortable="true" data-visible="false"><?= labels('No_of_customers', 'No. of customers') ?></th>
                                            <th data-field="tenure" data-sortable="true"><?= labels('tenure', 'Tenure') ?></th>
                                            <th data-field="price" data-visible="true" data-sortable="true"><?= labels('price', 'Price') ?></th>
                                            <th data-field="months" data-visible="true" data-sortable="true"><?= labels('months', 'Month(s)') ?></th>
                                            <th data-field="start_date"><?= labels('subscription_start_date', 'Subscription Start Date') ?></th>
                                            <th data-field="end_date"><?= labels('subscription_end_date', 'Subscription End Date') ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
        </section>
    </div>