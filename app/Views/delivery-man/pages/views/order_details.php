    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('order_details', "Order Details") ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('delivery_boy/orders'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('orders', 'Orders') ?></a>
                    </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="flash-message-custom"><?= session("message"); ?></label></div>
            <?php } ?>
            <div class="row">
                <div class="col-md">
                    <div class="card">
                        <div class="card">
                            <div class="section-title ml-3"><?= labels('customer_details', "Customer Details") ?></div>
                        </div>
                        <div class="card-body">
                            <p class="orsder-detail-p"><strong><?= labels('name', "Name") ?>: </strong><span><?= !empty($order['customer_name']) ? $order['customer_name'] : "" ?></span></p>
                            <p class="orsder-detail-p"><strong><?= labels('contact', 'Contact') ?>: </strong><span><?= !empty($order['customer_mobile']) ? $order['customer_mobile'] : "" ?></span></p>

                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <?php if (isset($items) && !empty($items)) { ?>
                        <div id="products">
                            <div class="card">
                                <div class="card">
                                    <div class="section-title ml-3"><?= labels('orders', 'Orders') ?> #<?= $order['id'] ?></div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-5">
                                        <div class="col-md-12 mb-3">
                                            <lable class="badge badge-primary"><?= labels('bulk_update_label', "Select status and square box of item which you want to update") ?></lable>
                                        </div>
                                        <div class="col-md-4">
                                            <select data-type="product" name="bulk_status" class="form-control status_bulk">
                                                <option value="">Select Status</option>
                                                <?php if (!empty($status)) {
                                                    foreach ($status as $status_name) { ?>
                                                        <option value="<?= $status_name['id'] ?>"><?= $status_name['status'] ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>

                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary update_status_bulk" value="status">
                                                <?= labels('bulk_update', "Bulk Update") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="status_order_bulk" name="order_id[]" value="">
                                    </div>
                                    <?php if (!empty($items)) ?>
                                    <div class="row">
                                        <?php foreach ($items as $item) { ?>
                                            <div class="card  card col-md-3 col-sm-12 p-3 mb-2 bg-white rounded m-1 grow">

                                                <div class="mb-2">
                                                    <input type="checkbox" class="status_order" name="order_id[]" value="<?= $item['id'] ?>">
                                                </div>
                                                <div class="col-md mb-2">
                                                    <div data-crop-image="100">
                                                        <img alt="image" src="<?= (!empty($item['image'])) ? base_url($item['image']) : "" ?>" class="order-detail-image">
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <p class="order-detail-p"><strong> <?= labels('product_name', 'Product Name') ?>: </strong><span><?= $item['product_name'] ?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('quantity', 'Quantity') ?>: </strong><span><?= $item['quantity'] ?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('price', 'Price') ?>: </strong><span><?=currency_location (decimal_points($item['price'])) ?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('subtotal', 'Subtotal') ?>: </strong><span><?=currency_location (decimal_points($item['sub_total'])) ?></span></p>
                                                    <p class="order-detail-p"><strong> <?= labels('order_status', "Order Status") ?>: </strong></p>

                                                </div>
                                                <div class="form-group col-md">
                                                    <select name="status" id="status" class="form-control status_update">
                                                        <option value="<?= !empty($item['status']) ? $item['status'] : "" ?>" selected><?= !empty($item['status_name']) ? $item['status_name'] : "" ?></option>
                                                        <?php if (!empty($status))
                                                            foreach ($status as $val) { ?>
                                                            <option data-type="product" data-order_id="<?= $item["id"] ?>" value="<?= $val['id'] ?>"><?= ucwords($val['status']) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <hr>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if (isset($services) && !empty($services)) { ?>
                        <div id="services">
                            <div class="card">
                                <div class="card">
                                    <div class="section-title ml-3"><?= labels('orders', 'Orders') ?> #<?= $order['id'] ?></div>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-12 mb-3">
                                            <lable class="badge badge-primary"><?= labels('bulk_update_label', "Select status and square box of item which you want to update") ?></lable>
                                        </div>
                                        <div class="col-md-4">
                                            <select data-type="service" name="bulk_status" class="form-control status_bulk">
                                                <option value="">Select Status</option>
                                                <?php if (!empty($status)) {
                                                    foreach ($status as $status_name) { ?>
                                                        <option value="<?= $status_name['id'] ?>"><?= ucwords($status_name['status']) ?></option>
                                                <?php }
                                                } ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <button type="submit" class="btn btn-primary update_status_bulk" value="status">
                                                <?= labels('bulk_update', "Bulk Update") ?>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="status_order_bulk" name="order_id[]" value="">
                                    </div>
                                    <?php if (!empty($services)) ?>
                                    <div class="row">
                                        <?php foreach ($services as $service) { ?>
                                            <div class="card  card col-md-3 col-sm-12 p-3 mb-2 bg-white rounded m-1 grow">
                                                <div class="mb-2">
                                                    <input type="checkbox" class="status_order" name="order_id" value="<?= $service['id'] ?>">
                                                </div>
                                                <div class="col-md mb-2">
                                                    <div data-crop-image="100">
                                                        <img alt="image" src="<?= (!empty($service['image'])) ? base_url($service['image']) : "" ?>" class="order-detail-image">
                                                    </div>
                                                </div>
                                                <div class="col-md">
                                                    <p class="order-detail-p"><strong><?= labels('product_name', 'Product Name') ?>: </strong><span><?= $service['service_name'] ?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('quantity', 'Quantity') ?>: </strong><span><?= $service['quantity'] ?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('price', 'Price') ?>: </strong><span><?= currency_location (decimal_points($service['price']))?></span></p>
                                                    <p class="order-detail-p"><strong><?= labels('subtotal', 'Subtotal') ?>: </strong><span><?= currency_location (decimal_points($service['sub_total'])) ?></span></p>
                                                    <?php if ($service['is_recursive'] == "1") { ?>
                                                        <p class="order-detail-p"><strong><?= labels('is_recursive', 'is recursive?') ?>: </strong><span><?= labels('yes', "Yes") ?></span></p>
                                                        <p class="order-detail-p"><strong><?= labels('recurring_days', 'Recurring Days') ?>: </strong><span><?= $service['recurring_days'] ?></span></p>
                                                        <p class="order-detail-p"><strong><?= labels('starts_from', 'Starts From') ?>: </strong><span><?= $service['starts_on'] ?></span></p>
                                                        <p class="order-detail-p"><strong><?= labels('ends_on', 'Ends On') ?>: </strong><span><?= $service['ends_on'] ?></span></p>
                                                    <?php } else { ?>
                                                        <p class="order-detail-p"><strong><?= labels('is_recursive', 'is recursive?') ?>: </strong><span><?= labels('no', "No") ?></span></p>
                                                    <?php } ?>
                                                    <p class="order-detail-p"><strong> <?= labels('order_status', "Order Status") ?>: </strong></p>

                                                </div>
                                                <div class="form-group col-md">
                                                    <select name="status" id="status" class="form-control status_update">
                                                        <option value="<?= !empty($service['status']) ? $service['status'] : "" ?>" selected><?= !empty($service['status_name']) ? $service['status_name'] : "" ?></option>
                                                        <?php if (!empty($status))
                                                            foreach ($status as $val) { ?>
                                                            <option data-type="service" data-order_id="<?= $service["id"] ?>" value="<?= $val['id'] ?>"><?= ucwords($val['status']) ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>

                                                <hr>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card">
                            <div class="section-title ml-3"><?= labels('payment_details', "Payment Details") ?></div>
                        </div>
                        <div class="card-body">
                            <p class="order-detail-p"><strong><?= labels('payment_method', 'Payment Method') ?>: </strong><span><?= !empty($order['payment_method']) ? $order['payment_method'] : ""  ?></span></p>
                            <p class="order-detail-p"><strong><?= labels('total', 'Total') ?>: </strong><span><?= !empty($order['total']) ? currency_location (decimal_points($order['total'])) : "" ?></span></p>
                            <p class="order-detail-p"><strong><?= labels('discount', 'Discount') ?>: </strong><span><?= !empty($order['discount']) ? currency_location (decimal_points($order['discount'])) : "" ?></span></p>
                            <p class="order-detail-p"><strong><?= labels('delivery_charges', 'Delivery Charges') ?>: </strong><span><?= !empty($order['delivery_charges']) ? currency_location (decimal_points($order['delivery_charges'])) : "" ?></span></p>
                            <p class="order-detail-p"><strong><?= labels('order_total', "Order Total") ?>: </strong><span><?= !empty($order['final_total']) ?currency_location (decimal_points($order['final_total'])) : "" ?></span></p>
                            <p class="order-detail-p"><strong> <?= labels('amount_paid', "Amount Paid") ?>: </strong><span><?= !empty($order['amount_paid']) ? currency_location (decimal_points($order['amount_paid'])) : "0" ?></span></p>
                            <p class="order-detail-p"><strong><?= labels('message', 'Message') ?>: </strong><span><?= !empty($order['message']) ? $order['message'] : "" ?></span></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="card">
                        <div class="card">
                            <div class="section-title ml-3"><?= labels('payment_summary', "Payment Summary") ?>
                                
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (isset($order) && !empty($order)) { ?>
                                <div class="col-md">

                                    <table class="table table-bordered table-hover " id="orders_transactions_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('delivery_boy/transactions/customer_transaction_table')  . "/" . $order['id'] . "/" . $order['customer_id'];  ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                        <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true">#</th>
                                                <th data-field="amount" data-sortable="true"> <?= labels('amount', 'Amount') ?></th>
                                                <th data-field="payment_type" data-sortable="true"><?= labels('payment_mode', 'Payment Mode') ?></th>
                                                <th data-field="created_at" data-visible="true"><?= labels('payment_date', "Payment Date") ?></th>
                                                <th data-field="status" data-sortable="true" data-visible="true"><?= labels('status', "Status") ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            <?php } else { ?>

                                <div class="alert alert-primary"><?= labels('no_payments_found', "No payments found") ?></div>
                            <?php } ?>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    </div>

    <div class="modal" id="create_payment">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h6 class="modal-title"><?= labels('add_payment', "Add Payment") ?></h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form class="create_order_payment" method="post" action='<?= base_url('vendor/transactions/save_payment') ?>' id="create_order_payment">
                        <div class="form-group">
                            <label for="payment_type"><?= labels('payment_mode', 'Payment Mode') ?></label><span class="asterisk text-danger"> *</span>
                            <select name="payment_type" id="payment_type" class="form-control">
                                <option value="cash" selected><?= labels('cash', 'Cash') ?></option>
                                <option value="wallet"><?= labels('wallet', 'Wallet') ?></option>
                                <option value="card_payment"><?= labels('card_payment', 'Card Payment') ?></option>
                                <option value="bar_code"> <?= labels('Bar_code_qR_code_scan', 'Bar Code / QR Code Scan') ?></option>
                                <option value="net_banking"><?= labels('net_banking', 'Net Banking') ?></option>
                                <option value="online_payment"><?= labels('online_payment', 'Online Payment') ?></option>
                                <option value="other"><?= labels('other', 'Other') ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="created_by"><?= labels('created_by', 'Created by') ?></label><span class="asterisk text-danger"> *</span>
                            <select name="created_by" id="created_by" class="form-control">
                                <option value="<?= $delivery_boy_id ?>" selected><?= labels('you', 'You') ?></option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="amount"><?= labels('amount', 'Amount') ?>(₹)</label><span class="asterisk text-danger"> *</span>
                            <input type="number" class="form-control" id="amount" placeholder="Enter Amount" name="amount">
                            <input type="hidden" name="order_id">
                            <input type="hidden" name="customer_id">
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
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
                </div>
            </div>
        </div>
    </div>