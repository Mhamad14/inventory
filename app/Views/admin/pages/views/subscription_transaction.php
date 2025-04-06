    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('transactions', 'Transactions') ?></h1>
            </div>
            <div class="row">
                <div class="col-md">
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="flash-message-custom"><?= session("message"); ?></label></div>
            <?php } ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="section-title ml-3"><?= labels('vendors_transaction', 'Vendors Transaction') ?></div>

                        <div class="card-body">
                            <div class="col-md">
                                <div class="card-body">
                                    <div class="row">

                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date"><?= labels('payment_method', "Payment Method") ?></label>
                                                <select name="payment_method" id="payment_method" class="form-control selectric">
                                                    <option value=""><?= labels('all', 'All') ?></option>
                                                    <option value="Stripe">Stripe</option>
                                                    <option value="razorpay">Razorpay</option>
                                                    <option value="flutterwave">Flutterwave</option>
                                                </select>
                                            </div>
                                        </div>
                                        <input type="hidden" id="user_id" value="<?= $id ?>">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date"><?= labels('transaction_date', "Transaction date") ?></label>
                                                <input type="text" name="date_range" id="txn_date" class="form-control">
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="date"><?= labels('filter_by_status', "Filter by status") ?></label>
                                                <select name="subscription_type" class="form-control selectric" id="transaction_status">
                                                    <option value=""><?= labels("all", "All") ?></option>
                                                    <option value="success"><?= labels("success", "Success") ?></option>
                                                    <option value="failed"><?= labels("failed", "Failed") ?></option>
                                                    <option value="pending"><?= labels('pending', "Pending") ?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for=""><?= labels('apply_filters') ?></label>
                                                <button class="btn btn-primary d-block" id="transaction_filter_btn" onclick="refresh_table('tts_table')">
                                                    <?= labels('apply', 'Apply') ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "vendor-transaction-list","ignoreColumn": ["action"]}' id="vendors_transactions_table" data-show-refresh="true" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/subscription_transactions/transactions_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="transaction_params">
                                        <thead>
                                            <tr>

                                                <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                                <th data-field="name" data-visible="true" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                                <th data-field="package_name" data-visible="true" data-sortable="true"><?= labels('package_name', 'Package Name') ?></th>
                                                <th data-field="payment_method" data-visible="true" data-sortable="true"><?= labels('payment_method', "Payment Method") ?></th>
                                                <th data-field="transaction_id" data-visible="true" data-sortable="true"><?= labels('transaction_id', 'Transaction ID') ?></th>
                                                <th data-field="amount" data-visible="true" data-sortable="true"><?= labels('amount', 'Amount') ?></th>
                                                <th data-field="created_on" data-visible="true" data-sortable="true"><?= labels('created_on', 'Created On') ?></th>
                                                <th data-field="email" data-visible="false" data-sortable="true"><?= labels('email', 'Email') ?></th>
                                                <th data-field="mobile" data-visible="false" data-sortable="true"><?= labels('mobile_number', 'Mobile') ?></th>
                                                <th data-field="status" data-sortable="false"><?= labels('status', 'Status') ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>