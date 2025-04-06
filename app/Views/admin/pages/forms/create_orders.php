<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Create Orders</h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/orders'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title="   <?= labels('orders', 'Orders') ?> "><i class="fas fa-list"></i></a>
                </div>
            </div>
        </div>

        <div class="section-body">
            <form action="<?= base_url('admin/orders/save_sales_order') ?>" id="sales_order_form" accept-charset="utf-8" method="POST">
                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('bill_from', 'Bill From') ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="product_wallet">Order No.</label>
                                    <input type="text" class="form-control" id="sales_order_no" name="order_no" placeholder="Please Enter Sales Order No">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <input type="hidden" name="limit" id="limit" value="10" />
                                    <input type="hidden" name="offset" id="offset" value="0" />
                                    <input type="hidden" name="total" id="total_products" />
                                    <input type="hidden" name="current_page" id="current_page" value="0" />
                                    <input type='hidden' name="sale_product_id" id="sale_product_id">
                                    <input type="hidden" name="business_id" id="business_id" value="<?= $business_id ?>" />
                                    <input type="hidden" class="form-control" id="products" name="products">
                                    <label for="purchase_date">Order Date</label><span class="asterisk text-danger"> *</span>

                                    <input type="date" class="form-control" id="sales_date" name="sales_date">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="product_wallet">Customer</label><span class="asterisk text-danger">*</span>
                                    <select class="select_user form-control" id="customer_id" name="customer_id"></select>
                                </div>
                            </div>
                            <div class="col-md-1 supplier-add-btn">
                                <span><button type="button" class="btn btn-success" data-bs-target="#customer_register" data-bs-toggle="modal" id=""><i class="fas fa-plus"></i></button></span>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                                        <select class=" form-control" id="warehouse_id" name="warehouse_id">
                                            <option value="" selected>Select warehouse </option>
                                            <?php foreach ($warehouses as $warehouse) { ?>
                                                <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="products">Products</label><span class="asterisk text-danger">*</span>
                                    <select class="search_products form-control" id="search_product"></select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-12">
                                <button id="remove" class="btn btn-danger" disabled>Delete</button>
                                <table class='table-striped' data-toolbar="#remove" id='sales_order' data-toggle="table" data-url="" data-click-to-select="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-show-columns="true" data-mobile-responsive="true" data-toolbar="#toolbar" data-maintain-selected="true" data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true"></th>
                                            <th data-field="id" data-sortable="true" data-visible="true"><?= labels('#', '#') ?></th>
                                            <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                                            <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="quantity" data-sortable="true" data-visible="true"><?= labels('qty', 'Qty') ?></th>
                                            <th data-field="price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('price', 'Price') . " <small>(Inclusive of Tax)</small>" ?></th>
                                            <th data-field="discount" data-sortable="true" data-visible="true"><?= labels('discount', 'Discount') . "<small> $currency</small>" ?></th>
                                            <th data-field="total" data-sortable="true" data-visible="true"><?= labels('sub_total', 'SubTotal') ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-9">
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="order_discount"><?= labels('discount', 'Discount') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <span><?= $currency ?></span>
                                            </div>
                                        </div>
                                        <input type="text" class="final_total form-control" name="order_discount" id="order_discount">
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="delivery_charge"><?= labels('shipping', 'Shipping') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <span><?= $currency ?></span>
                                            </div>
                                        </div>
                                        <input type="text" class="final_total form-control" name="delivery_charge" id="shipping">
                                    </div>
                                </div>
                                <div>
                                    <h6><strong><?= labels('total', 'Total') ?></strong></h6>
                                    <h4 class="cart-value h6 m-1 px-2" id="sub_total" data-currency="<?= $currency ?>"></h4>
                                    <input type="hidden" name="final_total" id="final_total">
                                </div>
                                <div class="form-group">
                                    <label class="payment_status_label" for="payment_status_item"><?= labels('payment_status', 'Payment Status') ?></label><span class="asterisk text-danger payment_status_label"> *</span>
                                    <select class="form-control payment_status" id="payment_status_item" name="payment_status">
                                        <option value="fully_paid" selected><?= labels('fully_paid', 'Fully Paid') ?></option>
                                        <option value="partially_paid"><?= labels('partially_paid', 'Partially Paid') ?></option>
                                        <option value="unpaid"><?= labels('unpaid', 'Unpaid') ?></option>
                                        <option value="cancelled"><?= labels('cancelled', 'Cancelled') ?></option>
                                    </select>
                                    <div class="amount_paid d-none">
                                        <label for="amount_paid_item"><?= labels('amount_paid', 'Amount Paid') ?></label><span class="asterisk text-danger"> *</span>
                                        <input type="number" class="form-control" id="amount_paid_item" value="" placeholder="0.00" name="amount_paid" min="0.00">
                                    </div>
                                </div>


                                <div class="section-title"><?= labels('payment_method', 'Payment Methods') ?></div>

                                <div class="custom-control custom-radio cash_payment ">
                                    <input type="radio" id="cod" name="payment_method[]" value="cash" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="cod"> <?= labels('cash', 'Cash') ?></label>
                                </div>

                                <div class="custom-control custom-radio  type">
                                    <input type="radio" id="wallet" name="payment_method[]" value="wallet" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="wallet"> <?= labels('wallet', 'Wallet') ?></label><span class="float-right"><small><label id="wallet_balance"><?= labels('wallet_balance', 'wallet balance') ?>: 0.00₹</label></small></span>
                                </div>

                                <div class="custom-control custom-radio card_payment ">
                                    <input type="radio" id="card_payment" name="payment_method[]" value="card_payment" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="card_payment"><?= labels('card_payment', 'Card Payment') ?></label>
                                </div>

                                <div class="custom-control custom-radio bar_code ">
                                    <input type="radio" id="bar_code" name="payment_method[]" value="bar_code" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="bar_code"> <?= labels('Bar_code_qR_code_scan', 'Bar Code / QR Code Scan') ?></label>
                                </div>

                                <div class="custom-control custom-radio net_banking ">
                                    <input type="radio" id="net_banking" name="payment_method[]" value="net_banking" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="net_banking"><?= labels('net_banking', 'Net Banking') ?></label>
                                </div>

                                <div class="custom-control custom-radio online_payment ">
                                    <input type="radio" id="online_payment" name="payment_method[]" value="online_payment" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="online_payment"><?= labels('online_payment', 'Online Payment') ?></label>
                                </div>

                                <div class="custom-control custom-radio other">
                                    <input type="radio" id="other" name="payment_method[]" value="other" class="custom-control-input payment_method">
                                    <label class="custom-control-label" for="other"> <?= labels('other', 'Other') ?></label>
                                </div>
                                <div class="payment_method_name mt-3">
                                    <p><?= labels('enter_payment_method_name', 'Enter Payment method Name') ?><input type="text" class="form-control" name="payment_method_name" id="payment_method_name"></p>
                                </div>
                                <div class="transaction_id mt-3">
                                    <p><?= labels('enter_transaction_id', 'Enter Transaction ID') ?> <input type="text" class="form-control" name="transaction_id" id="transaction_id"></p>
                                </div>

                                <div class="form-group">
                                    <label for="status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
                                    <button type="button" class="btn btn-sm btn-success float-right mb-1" data-bs-toggle="modal" data-bs-target="#status_modal"><?= labels('add_status', 'Add Status') ?></button>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select status</option>
                                        <?php if (!empty($status) && isset($status)) {
                                            foreach ($status as $val) { ?>
                                                <option value="<?= $val['id'] ?>"><?= $val['status'] . " - " .  $val['id'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="sales_message"><?= labels('message', 'Message') ?></label>
                                    <textarea class="form-control" name="message" id="sales_message"></textarea>
                                    <input type="hidden" name="order_type" id="order_type" value="product">
                                </div>
                                <button class="btn  btn-purchase btn-primary mb-2" type="submit"><?= labels('create_order', 'Create Order') ?></button>&nbsp;
                                <button type="reset" value="Reset" class="reset btn mb-2 btn-info" onclick="return resetForm(this.form);"><?= labels('reset', 'Reset') ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
</div>
<div class="modal" id="customer_register">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('register_user', "Register User") ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('admin/orders/register') ?>' id="register_customer">
                    <div class="form-group">
                        <label for="first_name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="name" placeholder="Enter Your Name" name="first_name">
                    </div>
                    <div class="form-group">
                        <label for="identity"><?= labels('mobile_number', 'Mobile') ?> <small>(<?= labels('identity', 'Identity') ?>)</small></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="identity" placeholder="Enter Your Mobile Number" name="identity">
                    </div>
                    <div class="form-group">
                        <label for="password"><?= labels('password', 'Password') ?></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="password" value="" placeholder="Enter Password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="email"><?= labels('email', 'Email') ?></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="email" placeholder="abc@gmail.com" name="email">
                    </div>
                    <button type="submit" class="btn btn-primary" id="save-register-result-btn" name="register" value="Save"><?= labels('register', 'Register') ?></button>
                    <div class="mt-3">
                        <div id="save-register-result"></div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"> <?= labels('close', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>
<div class="modal" id="status_modal">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('create_status', "Create Status") ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('admin/orders/create_status') ?>' id="create_status">
                    <div class="form-group">
                        <label for="status"><?= labels('status_name', "Status Name") ?></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="status" placeholder="Ex. Ordered ,pending,delivered" name="status">
                    </div>
                    <div class="form-group">
                        <label for="operation"><?= labels('operation', "Operation") ?></label><span class="asterisk text-danger"> *</span>
                        <button type="button" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="right" title="Ex. Debit From wallet balance, Credit balance in wallet ,do nothing etc.">
                            <small>( <?= labels('what_to_do_with_wallet_balance', "what to do with wallet balance?") ?>)</small>
                        </button>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="do_nothing" name="operation" value="0" class="custom-control-input" checked>
                            <label class="custom-control-label" for="do_nothing"><?= labels('do_nothing', "Do nothing") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="credit" name="operation" value="1" class="custom-control-input">
                            <label class="custom-control-label" for="credit"><?= labels('credit_wallet_balance', "Credit wallet balance") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="debit" name="operation" value="2" class="custom-control-input">
                            <label class="custom-control-label" for="debit"><?= labels('debit_wallet_balance', "Debit wallet balance") ?></label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="save-register-result-btn" name="register" value="Save"><?= labels('save', 'Save') ?></button>
                    <div class="mt-3">
                        <div id="save-register-result"></div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>