<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('sales_summary', 'Sales Summary') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date"><?= labels('date_range_filter', "Date Range Filter") ?></label>
                            <input type="text" name="daterange" id="date" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-danger btn-small  mb  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="payment_type_filter"><?= labels('filter_payments', 'Filter Payments') ?></label>
                            <select name="payment_type_filter" id="payment_type_filter" class="form-control selectric">
                                <option value="">ALL</option>
                                <option value="cash">Cash</option>
                                <option value="wallet">Wallet</option>
                                <option value="net_banking">Net Banking</option>
                                <option value="bar_code">Bar Code</option>
                                <option value="online_payment">Online Payment</option>
                                <option value="card_payment">Card Payment</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary mt-4  m-lg-4 py-2" id="apply">
                            Apply
                        </button>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv','json']" data-export-options='{"fileName": "Sales_summary","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" id="sales_summary_table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/sales_summary/sales_summary_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="reports_query">
                        <thead>
                            <tr>
                                <th data-field="order_id" data-sortable="true" data-visible="true"><?= labels('id', ' Id') ?></th>
                                <th data-field="users_id" data-sortable="true" data-visible="true"><?= labels('user_id', 'User Id') ?></th>
                                <th data-field="username" data-sortable="true" data-visible="true"><?= labels('username', 'Username') ?></th>
                                <th data-field="vendor_id" data-sortable="true" data-visible="false"><?= labels('vendor_id', 'Vendor Id') ?></th>
                                <th data-field="payment_method" data-sortable="true" data-visible="true"><?= labels('payment_method', 'Payment Method') ?></th>
                                <th data-field="payment_status" data-sortable="true" data-visible="true"><?= labels('payment_status', 'Payment Status') ?></th>
                                <th data-field="total" data-sortable="true" data-visible="true"><?= labels('total', 'Total') ?></th>
                                <th data-field="amount_paid" data-sortable="true" data-visible="true"><?= labels('amount_paid', 'Amount Paid') ?></th>
                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th data-field="total"></th>
                                <th data-field="amount_paid"></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>