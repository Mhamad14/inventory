<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchase_details', "Purchase Details") ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/purchases'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('purchase_orders', 'Purchase Orders') ?></a>
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
                        <div class="section-title ml-3"><?= labels('supplier_details', "Supplier Details") ?></div>
                    </div>
                    <div class="card-body">
                        <p class="orsder-detail-p"><strong><?= labels('supplier_name', "Supplier Name") ?>: </strong><span><?= !empty($order['supplier_name']) ? $order['supplier_name'] : "" ?></span></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <?php if (isset($order['items']) && !empty($order['items'])) { ?>
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
                                                    <option data-order_id="<?= $order['id'] ?>" value="<?= $status_name['id'] ?>"><?= ucwords($status_name['status']) ?></option>
                                            <?php }
                                            } ?>
                                        </select>
                                    </div>

                                    <div class="col-md-4">
                                        <button type="submit" class="btn btn-primary purchase_update_status_bulk" value="status">
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
                                                    <img alt="image" src="<?= (!empty(get_product_image($item['product_variant_id']))) ? base_url(get_product_image($item['product_variant_id'])) : "" ?>" class="order-detail-image">
                                                </div>
                                            </div>
                                            <div class="col-md">

                                                <p class="order-detail-p"><strong> <?= labels('product_name', 'Product Name') ?>: </strong><span><?= get_variant_name($item['product_variant_id']) ?></span></p>
                                                <p class="order-detail-p"><strong><?= labels('quantity', 'Quantity') ?>: </strong><span><?= $item['quantity'] ?></span></p>
                                                <p class="order-detail-p"><strong><?= labels('price', 'Price') ?>: </strong><span><?= currency_location(($item['price'])) ?></span></p>
                                                <p class="order-detail-p"><strong><?= labels('discount', 'Discount') ?>: </strong><span><?= currency_location(($item['discount'])) ?></span></p>
                                                <p class="order-detail-p"><strong> <?= labels('order_status', "Order Status") ?>: </strong></p>
                                            </div>
                                            <div class="form-group col-md">
                                                <select name="status" id="status" class="form-control purchase_status_update">
                                                    <option value="<?= !empty($item['status']) ? $item['status'] : "" ?>" selected><?= !empty($item['status']) ? ucwords(status_name($item['status'])) : "Select Status" ?></option>
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
                        <p class="order-detail-p"><strong><?= labels('total', 'Total') ?>: </strong><span><?= !empty($order['total']) ? currency_location(decimal_points($order['total'])) : "" ?></span></p>
                        <p class="order-detail-p"><strong><?= labels('discount', 'Discount') ?>: </strong><span><?= !empty($order['discount']) ? currency_location(decimal_points($order['discount'])) : currency_location("0") ?></span></p>
                        <p class="order-detail-p"><strong><?= labels('delivery_charges', 'Delivery Charges') ?>: </strong><span><?= !empty($order['delivery_charges']) ? currency_location(decimal_points($order['delivery_charges'])) : currency_location("0") ?></span></p>
                        <p class="order-detail-p"><strong> <?= labels('amount_paid', "Amount Paid") ?>: </strong><span><?= !empty($order['amount_paid']) ? currency_location(decimal_points($order['amount_paid'])) : currency_location("0") ?></span></p>
                        <p class="order-detail-p"><strong><?= labels('message', 'Message') ?>: </strong><span><?= !empty($order['message']) ? $order['message'] : "" ?></span></p>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card">
                        <div class="section-title ml-3"><?= labels('payment_summary', "Payment Summary") ?>
                            <?php if (!empty($order)) { ?>
                                <button type="button" class="btn btn-sm btn-success" data-supplier_id="<?= !empty($order['supplier_id']) ? $order['supplier_id'] : "" ?>" data-order_id="<?= !empty($order['id']) ? $order['id'] : "" ?>" data-bs-toggle="modal" data-bs-target="#create_payment"><?= labels('create_payment', "Create Payment") ?></button>
                            <?php  } ?>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="col-md">
                            <?php if (isset($order) && !empty($order)) { ?>
                                <?php if ($has_transactions) { ?>
                                    <table class="table table-bordered table-hover " id="orders_transactions_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/transactions/purchase_transaction_table') . "/" . $order['id']; ?>" data-side-pagination="server" data-page-list="[1,5, 10, 25, 50, 100, 200, All]" data-pagination="true" data-search="true">
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

                                <?php } else { ?>
                                    <div class="alert alert-primary"><?= labels('no_payments_found_payment_has_done_at_time_of_order', "No payments found payment has done at time of order") ?></div>
                                <?php } ?>

                            <?php } else { ?>
                                <div class="alert alert-primary"><?= labels('no_payments_found_payment_has_done_at_time_of_order', "No payments found payment has done at time of order") ?></div>
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
                <form class="create_order_payment" method="post" action='<?= base_url('admin/transactions/save_purchase_payment') ?>' id="create_order_payment">
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
                        <label for="type"><?= labels('transaction_type', 'Transaction Type') ?></label><span class="asterisk text-danger"> *</span>'
                        <select name="type" id="type" class="form-control">

                            <option value="debit"><?= labels('debit', 'Debit') ?></option>
                        </select>
                    </div>
                    <div class="form-group transaction_id">
                        <label for="transaction_id">Transaction ID</label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="transaction_id" name="transaction_id">
                    </div>
                    <div class="form-group" id="payment_method_name_type">

                    </div>
                    <div class="form-group">
                        <label for="created_by"><?= labels('created_by', 'Created by') ?></label><span class="asterisk text-danger"> *</span>
                        <select name="created_by" id="created_by" class="form-control">
                            <option value="<?= $vendor_id ?>" selected><?= labels('you', 'You') ?></option>
                            <option value="delivery_man"><?= labels('delivery_boy', 'Delivery Boy') ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="amount"><?= labels('amount', 'Amount') ?>(₹)</label><span class="asterisk text-danger"> *</span>
                        <input type="number" class="form-control" id="amount" placeholder="Enter Amount" name="amount" step="0.1">
                        <input type="hidden" name="order_id">
                        <input type="hidden" name="supplier_id">
                        <input type="hidden" name="order_type" value="<?= $order['order_type'] ?>">
                        <input type="hidden" name="payment_for" value="1">
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