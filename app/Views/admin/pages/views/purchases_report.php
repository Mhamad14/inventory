<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchases_report', 'Purchases Report') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="supplier_filter"><?= labels('supplier_filter', 'Supplier') ?></label>
                            <select name="supplier_filter" id="supplier_filter" class="form-control selectric">
                                <option value="">-Select-</option>
                                 <?php foreach ($supplier as $type) {
                                                             ?>
                                                            <option value="<?= $type['supplier_id']  ?>"> <?= $type['first_name'] ?> </option>
                                                        <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_range"><?= labels('date_range_filter', 'Date Range') ?></label>
                            <input type="text" name="date_range" id="date_purchases_report" class="form-control">
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
                            <button class="btn btn-primary d-block" id="apply">
                                <?= labels('apply', 'Apply') ?>
                            </button>
                        </div>
                    </div>
                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv','json']" data-export-options='{"fileName": "Top Selling Products","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/purchases_report/purchases_report_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="purchase_report_query" id="purchase_report_table">
                        <thead>
                            <tr>
                                <th data-field="purchase_id" data-sortable="true" data-visible="true"><?= labels('purchase_id', 'Purchase ID') ?></th>
                                <th data-field="supplier_id" data-sortable="true" data-visible="false"><?= labels('supplier_id', 'Supplier ID') ?></th>
                                <th data-field="first_name" data-sortable="true" data-visible="true"><?= labels('supplier_name', 'Supplier Name') ?></th>
                                <th data-field="mobile" data-sortable="true" data-visible="true"><?= labels('mobile_number', 'Mobile Number') ?></th>
                                <th data-field="purchase_date" data-sortable="true" data-visible="true"><?= labels('purchase_date', 'Purchase Date') ?></th>
                                <th data-field="amount_paid" data-sortable="true" data-visible="true"><?= labels('amount_paid', 'Amount Paid') ?></th>
                                <th data-field="remaining_amount" data-sortable="true" data-visible="true"><?= labels('remaining_amount', 'Remaining Amount ') ?></th>
                                <th data-field="total" data-sortable="true" data-visible="true"><?= labels('total', 'Total') ?></th>
                                <th data-field="payment_status" data-sortable="true" data-visible="true"><?= labels('payment_status', 'Payment Status') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>