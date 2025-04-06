    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('customers_subscription', 'Customers Subscription') ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/customers'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('customers', 'Customers') ?>"  ><i class="fas fa-list"></i> </a>
                    </div>
                </div>
            </div>

            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-red"><?= session("message"); ?></label></div>
            <?php } ?>

            <div class="row">
                <div class="col-md">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="customers_suscription_tab" data-toggle="tab" href="#Subscription" role="tab" aria-controls="home" aria-selected="true"><?= labels('subscriptions', 'Subscriptions') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="recursive_services_tab" data-toggle="tab" href="#Recursive_services" role="tab" aria-controls="profile" aria-selected="false"><?= labels('services', 'Services') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="myTabContent2">
                <!-- Customers Services-->
                <div class="tab-pane fade show active" id="Subscription" role="tabpanel" aria-labelledby="customers_suscription_tab">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md">
                                <div class="card">
                                    <div class="col-md">
                                        <h2 class="section-title"><?= labels('all_subscriptions', 'All Subscriptions') ?></h2>
                                        <h6 class="text-secondary">
                                            Note:Customers subscription is a list of customers who are subscribed to vendor's service which is renewable !
                                        </h6>
                                    </div>
                                    <div class="card-body">
                                        <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "customers-subscription-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/customers_subscription/customers_subscription_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                            <thead>
                                                <tr>
                                                    <th data-field="first_name" data-sortable="true" data-visible="true"><?= labels('customer_name', 'Customer Name') ?></th>
                                                    <th data-field="count_subscription" data-sortable="true" data-visible="true"><?= labels('total_subscription', 'Total Subscription') ?></th>
                                                    <th data-field="action"  data-visible="true"><?= labels('action', 'Action') ?></th>

                                                </tr>
                                            </thead>
                                        </table>

                                    </div>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
                <!-- end -->
                <!--Services Customers-->
                <div class="tab-pane fade" id="Recursive_services" role="tabpanel" aria-labelledby="recursive_services_tab">
                    <div class="section-body">
                        <div class="row mt-sm-4">
                            <div class='col-md-12'>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="card">
                                            <div class="col-md">
                                                <h2 class="section-title"><?= labels('all_services', 'All Services') ?></h2>
                                                <h6 class="text-secondary">
                                                    Note: List of services which are renewable and count of customers who are subscribed to services!
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "renewable-services-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/customers_subscription/recursive_services_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                                    <thead>
                                                        <tr>
                                                            <th data-field="service_id" data-sortable="true" data-visible="true"><?= labels('service_id', 'Service ID') ?></th>
                                                            <th data-field="service_name" data-sortable="true" data-visible="true"><?= labels('service_name', 'Service Name') ?></th>
                                                            <th data-field="count" data-sortable="true" data-visible="true"><?= labels('customers_count', 'Count of subscribed customers') ?></th>
                                                            <th data-field="action"  data-visible="true"><?= labels('action', 'Action') ?></th>

                                                        </tr>
                                                    </thead>
                                                </table>

                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end -->
            </div>

        </section>
    </div>
    <!-- customers services modal -->
    <div class="modal" id="customers_services">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?= labels('customers_subscription', 'Customers Subscription') ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="section">
                        <div class="section-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <table class="table table-hover table-borderd" id="customers_services_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url("admin/customers_subscription/customers_services_table/") ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                                <thead>
                                                    <tr>
                                                        <th data-field="service_id" data-sortable="true" data-visible="true"><?= labels('service_id', 'Service ID') ?></th>
                                                        <th data-field="service_name" data-sortable="true" data-visible="true"><?= labels('service_name', 'Service Name') ?></th>
                                                        <th data-field="price" data-sortable="true" data-visible="true"><?= labels('price', 'Price') ?></th>
                                                        <th data-field="is_recursive" data-sortable="true" data-visible="true"><?= labels('is_renewable', 'Is Renewable ?') ?></th>
                                                        <th data-field="recurring_days" data-sortable="true" data-visible="true"><?= labels('recurring_days', 'Recurring Days') ?></th>
                                                        <th data-field="starts_on" data-sortable="true" data-visible="true"><?= labels('from', 'From') ?></th>
                                                        <th data-field="ends_on" data-sortable="true" data-visible="true"><?= labels('to', 'To') ?></th>
                                                        <th data-field="status" data-sortable="true" data-visible="true"><?= labels('status', 'Status') ?></th>
                                                        <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>

    <!-- recursive services modal -->
    <div class="modal" id="recursive_services">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title"><?= labels('customers', 'Customers') ?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="section">
                        <div class="section-body">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-body">
                                            <table class="table table-hover table-borderd" id="customers_list_of_services_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url("admin/customers_subscription/customers_list_of_services_table/") ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                                <thead>
                                                    <tr>
                                                        <th data-field="customer_id" data-sortable="true" data-visible="true"><?= labels('id', 'ID') ?></th>
                                                        <th data-field="name" data-sortable="true" data-visible="true"><?= labels('customer_name', 'Customer Name') ?></th>

                                                    </tr>
                                                </thead>
                                            </table>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>