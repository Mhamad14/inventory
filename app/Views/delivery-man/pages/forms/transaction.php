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
                <div class="col-md-6">
                    <div class="card">
                        <div class="section-title ml-3"><?= labels('create_wallet_payment', 'Create Wallet Payment') ?></div>
                        <div class="card-body">
                            <form class="create_order_payment" method="post" action='<?= base_url('vendor/transactions/save_payment') ?>' id="create_order_payment">
                                <div class="form-group">
                                    <div class="form-group">
                                        <label for="name"><?= labels('customer_name', 'Customer Name') ?></label><span class="asterisk text-danger"> *</span>
                                        <input type="text" class="form-control" id="name" placeholder="" readonly>
                                        <input type="hidden" name="customer_id" id="customer_id">
                                    </div>
                                    <div class="form-group">
                                        <label for="email"><?= labels('email', 'Email') ?></label>
                                        <input type="email" class="form-control" id="email" placeholder="" readonly>
                                    </div>
                                    <label for="payment_mode"><?= labels('payment_mode', 'Payment Mode') ?></label><span class="asterisk text-danger"> *</span>
                                    <select name="payment_type" id="payment_mode" class="form-control">
                                        <option value="cash" selected><?= labels('cash', 'Cash') ?></option>
                                        <option value="card_payment"><?= labels('card_payment', 'Card Payment') ?></option>
                                        <option value="bar_code"> <?= labels('Bar_code_qR_code_scan', 'Bar Code / QR Code Scan') ?></option>
                                        <option value="net_banking"><?= labels('net_banking', 'Net Banking') ?></option>
                                        <option value="online_payment"><?= labels('online_payment', 'Online Payment') ?></option>
                                        <option value="other"><?= labels('other', 'Other') ?></option>
                                    </select>
                                </div>

                                <div class="form-group" id="type">

                                </div>

                                <div class="form-group">
                                    <label for="amount"><?= labels('amount', 'Amount') ?>(₹)</label><span class="asterisk text-danger"> *</span>
                                    <input type="number" class="form-control" id="amount" placeholder="Enter Amount" min="0.00" name="amount">
                                </div>

                                <div class="form-group">
                                    <label for="type"><?= labels('transaction_type', 'Transaction Type') ?></label><span class="asterisk text-danger"> *</span>'
                                    <select name="type" id="type" class="form-control">
                                        <option value="credit" selected><?= labels('credit', 'Credit') ?></option>
                                        <option value="debit"><?= labels('debit', 'Debit') ?></option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="created_by"><?= labels('created_by', 'Created by') ?></label><span class="asterisk text-danger"> *</span>
                                    <select name="created_by" id="created_by" class="form-control">
                                        <option value="<?= $delivery_boy_id ?>" selected><?= labels('you', 'You') ?></option>
                                    </select>
                                </div>

                                <div class="form-group transaction_id">
                                    <label for="transaction_id"><?= labels('transaction_id', 'Transaction ID') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                                </div>

                                <div class="form-group">
                                    <label for="message"><?= labels('message', 'Message') ?></label>
                                    <textarea class="form-control" name="message" id="message"></textarea>
                                </div>
                                <button type="submit" class="btn btn-primary" id="add_payment" name="add_payment" value="Save"><?= labels('add', 'Add') ?></button>
                                <div class="mt-3">
                                    <div id="save-register-result"></div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="section-title ml-3"><?= labels('customers_details', 'Customers Details') ?></div>
                        <div class="card-body">
                            <table class="table table-bordered table-hover" id="customers_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('delivery_boy/transactions/customers_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                <thead>
                                    <tr>
                                        <th data-radio="true"></th>
                                        <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                        <th data-field="customer_name" data-sortable="true"><?= labels('customer_name', 'Customer Name') ?></th>
                                        <th data-field="email" data-sortable="true" data-visible="true"><?= labels('email', 'Email') ?></th>
                                        <th data-field="balance" data-sortable="true" data-visible="true"><?= labels('wallet_balance', 'wallet balance') ?>(₹)</th>

                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card">
                        <div class="section-title ml-3"><?= labels('customer_wallet_transaction', 'Customer Wallet Transaction') ?></div>
                        <div class="card-body">
                            <div class="row">

                                <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "customers-wallet-transaction-list","ignoreColumn": ["action"]}' id="transactions_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('delivery_boy/transactions/transactions_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="transaction_type" data-sortable="true"><?= labels('type', 'Type') ?></th>
                                            <th data-field="order_id" data-visible="false"><?= labels('order_id', 'Order Id') ?></th>
                                            <th data-field="user_id" data-visible="false"><?= labels('user_id', 'User Id') ?></th>
                                            <th data-field="customer_id" data-visible="false"><?= labels('customer_id', 'Customer Id') ?></th>
                                            <th data-field="supplier_id" data-visible="false"><?= labels('supplier_id', 'Supplier Id') ?></th>
                                            <th data-field="payment_for" data-visible="true" data-sortable="true"><?= labels('payment_for', 'Payment For') ?></th>
                                            <th data-field="first_name" data-sortable="false"><?= labels('first_name', 'First Name') ?></th>
                                            <th data-field="last_name" data-sortable="false"><?= labels('last_name', 'Last Name') ?></th>
                                            <th data-field="payment_type" data-sortable="true" data-visible="true"><?= labels('payment_mode', 'Payment Mode') ?></th>
                                            <th data-field="transaction_id" data-sortable="true" data-visible="true"><?= labels('transaction_id', 'Transaction Id') ?></th>
                                            <th data-field="amount" data-sortable="true" data-visible="true"><?= labels('amount', 'Amount') ?></th>

                                            <th data-field="opening_balance" data-sortable="true" data-visible="true"><?= labels('opening_balance', 'Opening Balance') ?></th>
                                            <th data-field="closing_balance" data-sortable="true" data-visible="true"><?= labels('closing_balance', 'Closing Balance') ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </section>
    </div>