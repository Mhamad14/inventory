    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('pos_point_of_sale', 'POS - Point Of Sale') ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a href="#" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#today_stat" data-toggle="tooltip" data-bs-placement="bottom" title="" data-bs-original-title=" ToDay Statastices">
                            <i class="fas fa-suitcase"></i>
                        </a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/orders'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('orders', 'Orders') ?> "><i class="fas fa-list"></i> </a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <button type="button" class="btn btn-dark" id="chat-scrn" data-value="min">
                            <i data-value="min" class="fas fa-expand chat-scrn"></i>
                        </button>
                    </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-danger"><?= session("message"); ?></label></div>
            <?php } ?>
            <div class="row">
                <div class="col-md">
                    <div class="card">
                        <div class="card-body">
                            <ul class="nav nav-pills" id="myTab3" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" id="products_tab" data-toggle="tab" href="#Products" role="tab" aria-controls="home" aria-selected="true"><?= labels('products', 'Products') ?></a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" id="services-tab" data-toggle="tab" href="#Services" role="tab" aria-controls="profile" aria-selected="false"><?= labels('services', 'Services') ?></a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-content" id="myTabContent2">
                <!-- products pos view -->
                <div class="tab-pane fade show active" id="Products" role="tabpanel" aria-labelledby="products_tab">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="col-md">
                                        <h2 class="section-title"><?= labels('all_products', 'All Products') ?></h2>
                                    </div>
                                    <div class="row m-1">
                                        <div class="col-md-4">
                                            <select class="select2 product_category form-control" id="product_category" name="product_category" onchange="fetch_products(this)">
                                                <option value=""><?= labels('all_categories', 'All Categories') ?></option>
                                                <?php foreach ($categories as $category) { ?>
                                                    <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <select class="select2 product_category form-control" id="product_brand" name="brand_id" onchange="fetch_products(this)">
                                                <option value=""><?= labels('all_brands', 'All Brands') ?></option>
                                                <?php foreach ($brands as $brand) { ?>
                                                    <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4 search-element">
                                            <input class="form-control" type="search" placeholder="Search" id="search_product" oninput="fetch_products(this)" aria-label="Search">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card-body">
                                        <input type="hidden" name="limit" id="limit" value="10" />
                                        <input type="hidden" name="offset" id="offset" value="0" />
                                        <input type="hidden" name="total" id="total_products" />
                                        <input type="hidden" name="current_page" id="current_page" value="0" />
                                        <input type="hidden" name="business_id" id="business_id" value="<?= $business_id ?>" />

                                        <div class="row" id="products_div">
                                            <!-- display products here -->
                                        </div>

                                        <div class="pagination d-flex justify-content-center">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- cart column -->
                            <div class="col-md-4">
                                <div class="card">
                                    <section class="p-2">
                                        <form action="<?= base_url('admin/orders/save_order') ?>" id="place_order_form" accept-charset="utf-8" method="POST">
                                            <div class="mt-2 d-flex justify-content-center">
                                                <?= labels('already_registered', 'Already Registered') ?>?
                                                <input type="button" class="btn btn-xs btn-secondary mx-5" id="clear_user_search" value="Clear">
                                            </div>
                                            <!-- select user -->
                                            <div class="mt-2 text-center">
                                                <select class="select_user form-control" id="product_wallet"></select>
                                            </div>
                                            <div class="mt-3">
                                                <?= labels('dont_have_account', 'Dont Have An Account? Register Here') ?>
                                                <div class="">
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#customer_register"><?= labels('register', 'Register') ?></button>
                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <p class="h4 mt-4 "><strong><?= labels('current_orders', 'Current Orders') ?></strong></p>
                                            </div>
                                            <div class="products">
                                                <div class="row cart-row mt-4 mb-2">
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('item', 'Item') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('price', 'Price') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('quantity', 'Quantity') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><i class="fas fa-edit"></i></p>
                                                    </div>
                                                </div>
                                                <div class="cart-items">
                                                </div>
                                                <div class=" mb-2 mt-3">
                                                    <div class=" ">

                                                        <div class="invoice-detail-item d-flex gap-3">
                                                            <p class="cart-total"><?= labels('subtotal', 'Subtotal') ?></p>
                                                            <p class="cart-value h6 m-1 px-2" id="cart-total-price" data-currency="<?= $currency ?>"></p>

                                                        </div>

                                                        <label for="delivery_charge"><?= labels('shipping_charge', 'Shipping charge') ?></label></span>
                                                        <input type="number" class="final_total form-control" id="delivery_charge" value="" placeholder="0.00" name="delivery_charge" min="0.00">
                                                        <label for="discount"><?= labels('discount', 'Discount') ?></label> <small>(<?= labels('if_any', 'if any') ?>)</small></span>
                                                        <input type="number" class="final_total form-control" id="discount" value="" placeholder="0.00" name="discount" min="0.00">

                                                        <hr class="mt-2 mb-2">
                                                        <div class="invoice-detail-item">
                                                            <div class="cart-total"><?= labels('total', 'Total') ?></div>
                                                            <p class="cart-value h6 m-1 px-2" id="final_total" data-currency="<?= $currency ?>"></p>
                                                            <hr>
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
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
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
                                            </div>

                                            <div class="form-group">
                                                <label for="status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
                                                <button type="button" class="btn btn-sm btn-success float-right mb-1" data-bs-toggle="modal" data-bs-target="#status_modal"><?= labels('add_status', 'Add Status') ?></button>
                                                <select class="form-control" id="status" name="status">
                                                    <option value="">Select status</option>
                                                    <?php if (!empty($status) && isset($status)) {
                                                        foreach ($status as $val) { ?>
                                                            <option value="<?= $val['id'] ?>"><?= ucwords($val['status']) ?></option>

                                                    <?php }
                                                    } ?>
                                                </select>

                                            </div>
                                            <div class="form-group">
                                                <label for="message"><?= labels('message', 'Message') ?></label>
                                                <textarea class="form-control" name="message" id="message"></textarea>
                                                <input type="hidden" name="order_type" id="order_type" value="product">
                                            </div>

                                            <div class="text-center mt-4">
                                                <button class="btn btn-sm btn-clear_cart btn-danger mb-2" type="reset" id="clear_cart_btn"><?= labels('clear_cart', 'Clear Cart') ?></button>
                                                    <!-- the hold and load buttons -->
                                                <button class="btn btn-sm btn-warning mb-2 mx-2" type="button" id="hold_cart_btn" onclick="console.log('Hold button clicked')"><?= labels('hold_cart', 'Hold Cart') ?></button>
                                                <button class="btn btn-sm btn-info mb-2 mx-2" type="button" id="load_drafts_btn"><?= labels('load_drafts', 'Load Drafts') ?> (<span id="draft-count">0</span>)</button>
                                                    <!-- the hold and load buttons -->
                                                <button class="btn btn-sm btn-purchase btn-primary mb-2" type="submit" id="place_order_btn"><?= labels('create_order', 'Create Order') ?></button>
                                                <button type="button" class="btn btn-sm btn-dark mb-2 d-none" id="pos_quick_invoice" onclick="printInvoice()" data-id="">Print last order Invoce</button>
                                            </div>
                                </div>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- end -->
                <!-- services pos view -->
                <div class="tab-pane fade" id="Services" role="tabpanel" aria-labelledby="services-tab">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-md-7">
                                <div class="card">
                                    <div class="col-md">
                                        <h2 class="section-title"><?= labels('all_services', 'All Services') ?></h2>
                                    </div>
                                    <div class="row m-1">
                                        <div class="col-md-8 search-element">
                                            <input class="form-control" type="search" placeholder="Search" id="search_service" oninput="fetch_services(this)" aria-label="Search">
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="card-body">
                                        <input type="hidden" name="limit_service" id="limit_service" value="10" />
                                        <input type="hidden" name="offset_service" id="offset_service" value="0" />
                                        <input type="hidden" name="total_services" id="total_services" />
                                        <input type="hidden" name="current_page_service" id="current_page_service" value="0" />
                                        <input type="hidden" name="business_id" id="business_id" value="<?= $business_id ?>" />

                                        <div class="row" id="services_div">
                                            <!-- display products here -->
                                        </div>

                                        <div class="pagination_services d-flex justify-content-center">

                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-5">
                                <div class="card">
                                    <section class="p-2">
                                        <form action="<?= base_url('admin/orders/save_order') ?>" id="place_service_order_form" accept-charset="utf-8" method="POST">
                                            <div class="mt-2 d-flex justify-content-center">
                                                <?= labels('already_registered', 'Already Registered') ?>?

                                                <input type="button" class="btn btn-xs btn-warning mx-5" id="clear_user_search" value="Clear">
                                            </div>
                                            <!-- select user -->
                                            <div class="mt-2 text-center">
                                                <select class="select_user form-control" id="service_wallet"></select>
                                            </div>
                                            <div class="mt-3">
                                                <?= labels('dont_have_account', 'Dont Have An Account? Register Here') ?>
                                                <div class="">
                                                    <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#customer_register">Register</button>

                                                </div>
                                            </div>

                                            <div class="mt-3">
                                                <p class="h4 mt-4 "><strong><?= labels('current_orders', 'Current Orders') ?></strong></p>
                                            </div>
                                            <div class="services">
                                                <div class="row cart-row mt-4 mb-2">
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('item', 'Item') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('price', 'Price') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('quantity', 'Quantity') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('starts_from', 'Starts From') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><?= labels('ends_on', 'Ends On') ?></p>
                                                    </div>
                                                    <div class="col">
                                                        <p class="cart-item h6"><i class="fas fa-edit"></i></p>
                                                    </div>
                                                </div>
                                                <div class="cart-services">
                                                </div>
                                                <div class=" mb-2 mt-3">
                                                    <div class="">

                                                        <div class="invoice-detail-item">
                                                            <p class="cart-total"><?= labels('subtotal', 'Subtotal') ?></p>
                                                            <p class="cart-value h6 m-1 px-2" id="cart-total-price-service" data-currency="<?= $currency ?>"></p>

                                                        </div>
                                                        <label for="delivery_charge_service"><?= labels('shipping_charge', 'Shipping charge') ?></label></span>
                                                        <input type="number" class="final_total_service form-control" id="delivery_charge_service" value="" placeholder="0.00" name="delivery_charge" min="0.00">
                                                        <label for="discount_service"><?= labels('discount', 'Discount') ?></label> <small>(<?= labels('if_any', 'if any') ?>)</small></span>
                                                        <input type="number" class="final_total_service form-control" id="discount_service" value="" placeholder="0.00" name="discount" min="0.00">


                                                        <hr class="mt-2 mb-2">
                                                        <div class="invoice-detail-item">
                                                            <div class="cart-total"><?= labels('total', 'Total') ?></div>
                                                            <p class="cart-value h6 m-1 px-2" id="final_total_service" data-currency="<?= $currency ?>"></p>
                                                            <hr>
                                                            <label class="payment_status_label_service" for="payment_status"><?= labels('payment_status', 'Payment Status') ?></label><span class="asterisk text-danger payment_status_label_service"> *</span>
                                                            <select class="form-control payment_status_service" id="payment_status" name="payment_status">
                                                                <option value="fully_paid" selected><?= labels('fully_paid', 'Fully Paid') ?></option>
                                                                <option value="partially_paid"><?= labels('partially_paid', 'Partially Paid') ?></option>
                                                                <option value="unpaid"><?= labels('unpaid', 'Unpaid') ?></option>
                                                                <option value="cancelled"><?= labels('cancelled', 'Cancelled') ?></option>
                                                            </select>

                                                            <div class="amount_paid d-none">
                                                                <label for="amount_paid"><?= labels('amount_paid', 'Amount Paid') ?></label><span class="asterisk text-danger"> *</span>
                                                                <input type="number" class="form-control" id="amount_paid" value="" placeholder="0.00" name="amount_paid" min="0.00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div>
                                                <div class="section-title"><?= labels('payment_method', 'Payment Methods') ?></div>
                                                <div class="custom-control custom-radio cash_payment ">
                                                    <input type="radio" id="cod_service" name="payment_method_service[]" value="cash" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="cod_service"> <?= labels('cash', 'Cash') ?></label>
                                                </div>
                                                <div class="custom-control custom-radio type">
                                                    <input type="radio" id="wallet_service" name="payment_method_service[]" value="wallet" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="wallet_service"> <?= labels('wallet', 'Wallet') ?></label><span class="float-right"><small><label id="wallet_balance_service"><?= labels('wallet_balance', 'wallet balance') ?>: 0.00₹</label></small></span>
                                                </div>

                                                <div class="custom-control custom-radio card_payment ">
                                                    <input type="radio" id="card_payment_service" name="payment_method_service[]" value="card_payment" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="card_payment_service"> <?= labels('card_payment', 'Card Payment') ?></label>
                                                </div>

                                                <div class="custom-control custom-radio bar_code ">
                                                    <input type="radio" id="bar_code_service" name="payment_method_service[]" value="bar_code" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="bar_code_service"> <?= labels('Bar_code_qR_code_scan', 'Bar Code / QR Code Scan') ?></label>
                                                </div>

                                                <div class="custom-control custom-radio net_banking ">
                                                    <input type="radio" id="net_banking_service" name="payment_method_service[]" value="net_banking" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="net_banking_service"> <?= labels('net_banking', 'Net Banking') ?></label>
                                                </div>

                                                <div class="custom-control custom-radio online_payment ">
                                                    <input type="radio" id="online_payment_service" name="payment_method_service[]" value="online_payment" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="online_payment_service"> <?= labels('online_payment', 'Online Payment') ?></label>
                                                </div>

                                                <div class="custom-control custom-radio other">
                                                    <input type="radio" id="other_service" name="payment_method_service[]" value="other" class="custom-control-input payment_method_service">
                                                    <label class="custom-control-label" for="other_service"> <?= labels('other', 'Other') ?></label>
                                                </div>
                                                <div class="payment_method_name_service mt-3">
                                                    <p><?= labels('enter_payment_method_name', 'Enter Payment method Name') ?> <input type="text" class="form-control" name="payment_method_name_service" id="payment_method_name_service"></p>
                                                </div>
                                                <div class="transaction_id_service mt-3">
                                                    <p><?= labels('enter_transaction_id', 'Enter Transaction ID') ?> <input type="text" class="form-control" name="transaction_id" id="transaction_id_service"></p>
                                                </div>

                                                <div class="form-group">
                                                    <label for="service_status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
                                                    <button type="button" class="btn btn-sm btn-success float-right mb-1" data-bs-toggle="modal" data-bs-target="#status_modal"><?= labels('add_status', 'Add Status') ?></button>
                                                    <select class="form-control" id="service_status" name="service_status">
                                                        <option value="">Select status</option>
                                                        <?php if (!empty($status) && isset($status)) {
                                                            foreach ($status as $val) { ?>
                                                                <option value="<?= $val['id'] ?>"><?= $val['status'] ?></option>

                                                        <?php }
                                                        } ?>
                                                    </select>

                                                </div>
                                                <div class="form-group">
                                                    <label for="service_message"><?= labels('message', 'Message') ?></label>
                                                    <textarea class="form-control" name="service_message" id="service_message"></textarea>
                                                    <input type="hidden" name="order_type" id="order_type_service" value="service">

                                                </div>
                                            </div>

                                            <div class="text-center mt-4">
                                                <button class="btn btn-sm btn-clear_cart btn-danger mb-2 mx-3" type="reset" id="clear_cart_btn"><?= labels('clear_cart', 'Clear Cart') ?></button>
                                                <button class="btn btn-sm btn-purchase btn-primary mb-2" type="submit" id="place_order_service_btn"><?= labels('create_order', 'Create Order') ?></button>
                                            </div>
                                        </form>

                                    </section>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end -->
            </div>
        </section>
    </div>
    <!-- register modal -->
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
                            <input type="text" class="form-control" id="password" value="12345678" placeholder="Enter Password" name="password">
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
    <!-- status modal -->
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
                    <form method="post" action='<?= base_url('admin/orders/create_status') ?>' id="create_status" class="form-submit-event">
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

    <input type="hidden" id="barcode_scanned" autofocus />

    <div class="modal  fade " id="today_stat">
        <div class="modal-dialog  modal-lg">
            <div class="modal-content">

                <!-- Modal Header -->
                <div class="modal-header">
                    <h2 class="modal-title"> To day's statistics</h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <!-- Modal body -->
                <div class="modal-body">
                    <div class="row">
                        <table class="table">
                            <tr></tr>
                            <tbody>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <b>Total Sales </b> :
                                                <span id="today_sales" data-currency="<?= $currency ?>"> </span>
                                            </div>
                                            <div>
                                                <b>Total Payments Received</b> :
                                                <span id="today_payments" data-currency="<?= $currency ?>"> </span>
                                            </div>
                                            <div>
                                                <b>Total Remaining Payments</b> :
                                                <span id="today_payments_remaining" data-currency="<?= $currency ?>"> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <b>Total Purchase </b> : <span id="today_purchase" data-currency="<?= $currency ?>"></span>
                                            </div>
                                            <div>
                                                <b>Total Paid for Purchase </b> :
                                                <span id="today_paid" data-currency="<?= $currency ?>"> </span>
                                            </div>
                                            <div>
                                                <b>Total Amount to be Paid</b> :
                                                <span id="today_amount_to_pay" data-currency="<?= $currency ?>"> </span>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <div class="d-flex justify-content-between">
                                            <div>
                                                <b> Total Expenses </b> : <span id="today_expanse" data-currency="<?= $currency ?>"></span>
                                            </div>
                                            <div>
                                                <b> Total Profit </b> : <span id="today_profit" data-currency="<?= $currency ?>"></span>
                                            </div>
                                        </div>

                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>