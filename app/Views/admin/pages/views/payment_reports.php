<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('payment_reports', 'Payment Reports') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="date"><?= labels('date_range_filter', "Date Range Filter") ?></label>
                                    <input type="text" name="daterange" id="date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn-danger btn-small p-2 mb-1   mt-4 py-2" name="clear" id="clear"> Clear </button>
                            </div>
                        </div>
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
                        <label for=""></label>
                        <button type="button" class="btn btn-primary mt-4 py-2" id="apply">
                            Apply
                        </button>
                    </div>
                </div>
                <div class="row">
                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv','json']" data-export-options='{"fileName": "payment-reports","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" id="payment_reports_table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/payment_reports/payment_reports_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="reports_query">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true" data-visible="true"><?= labels('id', ' Id') ?></th>
                                <th data-field="customer_id" data-sortable="true" data-visible="false"><?= labels('customer_id', 'Customer Id') ?></th>
                                <th data-field="vendor_id" data-sortable="true" data-visible="false"><?= labels('vendor_id', 'Vendor Id') ?></th>
                                <th data-field="username" data-sortable="true" data-visible="false"><?= labels('username', 'Username') ?></th>
                                <th data-field="name" data-sortable="false" data-visible="true"><?= labels('name', 'Name') ?></th>
                                <th data-field="email" data-sortable="true" data-visible="false"><?= labels('email', 'Email') ?></th>
                                <th data-field="payment_type" data-sortable="true" data-visible="true"><?= labels('payment_type', 'Payment Type') ?></th>
                                <th data-field="amount" data-sortable="true" data-visible="true"><?= labels('amount', 'Amount') ?></th>
                                <th data-field="created_at" data-sortable="true" data-visible="true"><?= labels('payment_date', 'Payment Date') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </section>
</div>