    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1> <?= labels('orders', 'Orders') ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal" data-toggle="tooltip" data-bs-placement="bottom" title="   <?= labels('bulk_upload', 'Bulk Upload Orders') ?>  "><i class="bi bi-cloud-download-fill"></i>
                        </a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/orders'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('pos_order', 'POS Order') ?>  "><i class="fas fa-plus"></i> </a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/sales_order'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('sales_orders', 'Sales Orders') ?> "><i class="fas fa-plus-circle"></i> </a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/payment_reminder'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('payment_reminder', 'Payment Reminder') ?> "><i class="fas fa-bell"></i> </a>
                    </div>
                </div>

            </div>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="order_type_filter"><?= labels('filter_orders', 'Filter Orders') ?></label>
                                <select name="order_type_filter" id="order_type_filter" class="form-control selectric">
                                    <option value="">-Select-</option>
                                    <option value="product">Products</option>
                                    <option value="service">Services</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="date_range"><?= labels('date_range_filter', 'Date Range') ?></label>
                                <input type="text" name="date_range" id="date_range" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-danger btn-small p-2 mb-1  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="payment_status_filter"><?= labels('filter_by_payment_status', 'Filter by Payment Status') ?></label>
                                <select name="payment_status_filter" class="form-control selectric" id="payment_status_filter">
                                    <option value="">All</option>
                                    <option value="fully_paid">Fully Paid</option>
                                    <option value="partially_paid">Partially Paid</option>
                                    <option value="unpaid">Unpaid</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for=""><?= labels('apply_filters', 'Apply filters') ?></label>
                                <button class="btn btn-primary d-block" id="filter">
                                    <?= labels('apply', 'Apply') ?>
                                </button>
                            </div>
                        </div>
                        <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "orders-list","ignoreColumn": ["action"]}' id="orders_items_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/orders/orders_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="orders_query">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                    <th data-field="created_at" data-sortable="true"><?= labels('order_date', 'Order Date') ?></th>
                                    <th data-field="first_name" data-sortable="true"><?= labels('customer_name', 'Customer Name') ?></th>
                                    <th data-field="total" data-sortable="true" data-visible="false"><?= labels('total', 'Total(₹)') ?></th>
                                    <th data-field="discount" data-visible="false"><?= labels('discount', 'Discount') ?></th>
                                    <th data-field="delivery_charges" data-sortable="true" data-visible="false"><?= labels('delivery_charges', 'Delivery Charges') ?></th>
                                    <th data-field="final_total" data-sortable="true"><?= labels('final_total', 'Final Total') ?></th>
                                    <th data-field="payment_status" data-sortable="true"><?= labels('payment_status', 'Payment Status') ?></th>
                                    <th data-field="balance" data-sortable="true"><?= labels('balance', 'Balance') ?></th>
                                    <th data-field="payment_method" data-sortable="true"><?= labels('payment_method', 'Payment Method') ?></th>
                                    <th data-field="message" data-sortable="true" data-visible="false"><?= labels('message', 'Message') ?></th>
                                    <th data-field="action"><?= labels('action', 'Action') ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </section>
    </div>


    <div class="modal" id="bulk_upload_modal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Bulk Upload</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <div class="section">
                        <div class="section-body">
                            <form action="<?= base_url('admin/bulk_uploads/import_orders') ?>" method="post" id="bulk_uploads_form">
                                <div class="card ">
                                    <div class="card-body row">
                                        <div class="form-group">
                                            <label><?= labels('type<small>(upload/update)</small>', 'Type <small>(upload/update)</small>') ?></label>
                                            <select class="form-control" id="type" name="type">
                                                <option value=''>Select</option>
                                                <option value='upload' selected>Upload</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-body row">
                                        <div class="form-group">
                                            <label><?= labels('file', 'File') ?></label>
                                            <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $business_id ?>">
                                            <input type="file" class="form-control" id="bulk_upload_file" name="file" accept=".csv">
                                        </div>
                                    </div>
                                    <div class="card-body row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/bulk-upload-order-final.csv') ?>" class="btn btn-info" download="bulk-upload-order-final.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/Instructions-for-bulk-upload-orders.txt') ?>" class="btn btn-success" download="Instructions-for-bulk-upload-orders.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="submit" class="btn btn-primary bulk_upload" value="Save"><?= labels('import', 'Import') ?></button>
                                    </div>

                            </form>
                        </div>
                    </div>


                </div>