<div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('order_details', "Order Details") ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a href="<?= base_url("admin/invoices/invoice") . "/" . $order['id'] ?>" class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a href="<?= base_url('admin/invoices/view_invoice/' . $order['id']) ?> " class='btn btn-danger btn-sm' target='_blank' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice PDF'><i class='bi bi-file-earmark-pdf'></i></a>
                    </div>
                    <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/orders'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('orders', 'Orders') ?></a>
                    </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="flash-message-custom"><?= session("message"); ?></label>
                </div>
            <?php } ?>

            <!-- Enhanced Customer Details Section -->
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?= labels('customer_details', "Customer Details") ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="customer-info">
                                        <p class="mb-1"><i class="bi bi-person-fill mr-2"></i> <strong><?= labels('name', "Name") ?>: </strong><span><?= !empty($order['customer_name']) ? $order['customer_name'] : "" ?></span></p>
                                        <p class="mb-1"><i class="bi bi-telephone-fill mr-2"></i> <strong><?= labels('contact', 'Contact') ?>: </strong><span><?= !empty($order['customer_mobile']) ? $order['customer_mobile'] : "" ?></span></p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="payment-summary-info">
                                        <p class="mb-1"><i class="bi bi-cash mr-2"></i> <strong><?= labels('order_total', "Total Amount") ?>: </strong><span class="badge bg-primary"><?= !empty($order['final_total']) ? currency_location(decimal_points($order['final_total'])) : currency_location("0") ?></span></p>
                                        <p class="mb-1"><i class="bi bi-credit-card mr-2"></i> <strong><?= labels('amount_paid', "Amount Paid") ?>: </strong><span class="badge bg-success"><?= !empty($order['amount_paid']) ? currency_location(decimal_points($order['amount_paid'])) : currency_location("0") ?></span></p>
                                        <p class="mb-1"><i class="bi bi-exclamation-circle mr-2"></i> <strong><?= labels('balance', "Balance") ?>: </strong><span class="badge <?= ($order['final_total'] - $order['amount_paid'] > 0) ? 'bg-danger' : 'bg-success' ?>"><?= currency_location(decimal_points($order['final_total'] - $order['amount_paid'])) ?></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Improved Order Items Section -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <h4><?= labels('order_items', "Order Items") ?> #<?= $order['id'] ?></h4>
                            <div class="card-header-action">
                                <?php if (isset($items) && !empty($items) && isset($services) && !empty($services)) { ?>
                                    <ul class="nav nav-tabs" id="orderItemsTab" role="tablist">
                                        <li class="nav-item">
                                            <a class="nav-link active" id="products-tab" data-toggle="tab" href="#products" role="tab"><?= labels('products', 'Products') ?></a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" id="services-tab" data-toggle="tab" href="#services" role="tab"><?= labels('services', 'Services') ?></a>
                                        </li>
                                    </ul>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="tab-content">
                                <?php if (isset($items) && !empty($items)) { ?>
                                    <div class="tab-pane fade show <?= (!isset($services) || empty($services)) ? 'active' : '' ?>" id="products" role="tabpanel">
                                        <div class="bulk-actions mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
                                                    <lable class="badge badge-primary"><?= labels('bulk_update_label', "Select status and square box of item which you want to update") ?></lable>
                                                </div>
                                                <div class="col-md-4">
                                                    <select data-type="product" name="bulk_status" class="form-control status_bulk">
                                                        <option value="">Select Status</option>
                                                        <?php if (!empty($status)) {
                                                            foreach ($status as $status_name) { ?>
                                                                <option value="<?= $status_name['id'] ?>"><?= ucwords($status_name['status']) ?></option>
                                                        <?php }
                                                        } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary update_status_bulk" value="status">
                                                        <?= labels('bulk_update', "Bulk Update") ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <input type="checkbox" class="status_order_bulk" name="order_id[]" value=""> <label><?= labels('select_all', 'Select All') ?></label>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th width="5%"></th>
                <th width="10%"><?= labels('image', 'Image') ?></th>
                <th width="25%"><?= labels('product_name', 'Product Name') ?></th>
                <th width="10%"><?= labels('quantity', 'Quantity') ?></th>
                <th width="15%"><?= labels('price', 'Price') ?></th>
                <th width="15%"><?= labels('subtotal', 'Subtotal') ?></th>
                <th width="20%"><?= labels('order_status', "Order Status") ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $item) { ?>
                <tr>
                    <td>
                        <input type="checkbox" class="status_order" name="order_id[]" value="<?= $item['id'] ?>">
                    </td>
                    <td>
                        <img alt="image" src="<?= (!empty($item['image'])) ? base_url($item['image']) : "" ?>" class="img-fluid" style="max-height: 50px;">
                    </td>
                    <td><?= $item['product_name'] ?></td>
                    <td>
                        <select name="return_quantity[<?= $item['id'] ?>]" 
                        class="form-control form-control-sm return-quantity" 
                        data-price="<?= $item['price'] ?>"
                        data-max="<?= $item['quantity'] - $item['returned_quantity'] ?>">
                        <?php for ($i = 0; $i <= ($item['quantity'] - $item['returned_quantity']); $i++): ?>
                        <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                        </select>
                    </td>
                    <td><?= currency_location(decimal_points($item['price'])) ?></td>
                    <td><?= currency_location(decimal_points($item['sub_total'])) ?></td>
                    <td>
                        <select name="status" class="form-control status_update">
                            <option value="<?= !empty($item['status']) ? $item['status'] : "" ?>" selected><?= !empty($item['status_name']) ? ucwords($item['status_name']) : "" ?></option>
                            <?php if (!empty($status)) {
                                foreach ($status as $val) { ?>
                                    <option data-type="product" data-order_id="<?= $item["id"] ?>" value="<?= $val['id'] ?>"><?= ucwords($val['status']) ?></option>
                                <?php }
                            } ?>
                        </select>
                    </td>
                </tr>
            <?php }} ?>
        </tbody>
    </table>
</div>
<div class="row mb-3">
    <div class="col-md-12 text-right">
        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#returnItemsModal">
            <i class="bi bi-arrow-return-left"></i> <?= labels('return_items', 'Return Items') ?>
        </button>
    </div>
</div>               
                                <?php if (isset($services) && !empty($services)) { ?>
                                    <div class="tab-pane fade <?= (!isset($items) || empty($items)) ? 'show active' : '' ?>" id="services" role="tabpanel">
                                        <div class="bulk-actions mb-4">
                                            <div class="row align-items-center">
                                                <div class="col-md-6">
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
                                                <div class="col-md-2">
                                                    <button type="submit" class="btn btn-primary update_status_bulk" value="status">
                                                        <?= labels('bulk_update', "Bulk Update") ?>
                                                    </button>
                                                </div>
                                            </div>
                                            <div class="mt-2">
                                                <input type="checkbox" class="status_order_bulk" name="order_id[]" value=""> <label><?= labels('select_all', 'Select All') ?></label>
                                            </div>
                                        </div>
                                        
                                        <div class="table-responsive">
                                            <table class="table table-striped table-bordered table-hover">
                                                <thead>
                                                    <tr>
                                                        <th width="5%"></th>
                                                        <th width="10%"><?= labels('image', 'Image') ?></th>
                                                        <th width="20%"><?= labels('service_name', 'Service Name') ?></th>
                                                        <th width="8%"><?= labels('quantity', 'Quantity') ?></th>
                                                        <th width="12%"><?= labels('price', 'Price') ?></th>
                                                        <th width="12%"><?= labels('subtotal', 'Subtotal') ?></th>
                                                        <th width="13%"><?= labels('recurring', 'Recurring') ?></th>
                                                        <th width="20%"><?= labels('order_status', "Order Status") ?></th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <?php foreach ($services as $service) { ?>
                                                        <tr>
                                                            <td>
                                                                <input type="checkbox" class="status_order" name="order_id" value="<?= $service['id'] ?>">
                                                            </td>
                                                            <td>
                                                                <img alt="image" src="<?= (!empty($service['image'])) ? base_url($service['image']) : "" ?>" class="img-fluid" style="max-height: 50px;">
                                                            </td>
                                                            <td><?= $service['service_name'] ?></td>
                                                            <td><?= $service['quantity'] ?></td>
                                                            <td><?= currency_location(decimal_points($service['price'])) ?></td>
                                                            <td><?= currency_location(decimal_points($service['sub_total'])) ?></td>
                                                            <td>
                                                                <?php if ($service['is_recursive'] == "1") { ?>
                                                                    <span class="badge bg-info"><?= labels('yes', "Yes") ?></span>
                                                                    <small class="d-block mt-1"><?= $service['recurring_days'] ?> <?= labels('days', 'days') ?></small>
                                                                    <small class="d-block"><?= $service['starts_on'] ?> - <?= $service['ends_on'] ?></small>
                                                                <?php } else { ?>
                                                                    <span class="badge bg-secondary"><?= labels('no', "No") ?></span>
                                                                <?php } ?>
                                                            </td>
                                                            <td>
                                                                <select name="status" class="form-control status_update">
                                                                    <option value="<?= !empty($service['status']) ? $service['status'] : "" ?>" selected><?= !empty($service['status_name']) ? $service['status_name'] : "" ?></option>
                                                                    <?php if (!empty($status))
                                                                        foreach ($status as $val) { ?>
                                                                        <option data-type="service" data-order_id="<?= $service["id"] ?>" value="<?= $val['id'] ?>"><?= ucwords($val['status']) ?></option>
                                                                    <?php } ?>
                                                                </select>
                                                            </td>
                                                        </tr>
                                                    <?php } ?>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Improved Payment Details and Summary Section -->
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header">
                            <h4><?= labels('payment_details', "Payment Details") ?></h4>
                        </div>
                        <div class="card-body">
                            <div class="payment-summary-box">
                                <div class="info-item">
                                    <div class="info-label"><i class="bi bi-credit-card mr-2"></i> <?= labels('payment_method', 'Payment Method') ?></div>
                                    <div class="info-value badge bg-info"><?= !empty($order['payment_method']) ? ucfirst($order['payment_method']) : "" ?></div>
                                </div>
                                
                                <div class="info-item">
                                    <div class="info-label"><i class="bi bi-hash mr-2"></i> <?= labels('transaction_id', 'Transaction Id') ?></div>
                                    <div class="info-value"><?= !empty($order['order_transaction_id']) ? $order['order_transaction_id'] : '-' ?></div>
                                </div>
                                
                                <div class="payment-calculation mt-4">
                                    <div class="calculation-item">
                                        <div class="item-label"><?= labels('total', 'Subtotal') ?></div>
                                        <div class="item-value"><?= !empty($order['total']) ? currency_location(decimal_points($order['total'])) : currency_location("0") ?></div>
                                    </div>
                                    
                                    <div class="calculation-item text-success">
                                        <div class="item-label"><?= labels('discount', 'Discount') ?></div>
                                        <div class="item-value">- <?= !empty($order['discount']) ? currency_location(decimal_points($order['discount'])) : currency_location("0") ?></div>
                                    </div>
                                    
                                    <div class="calculation-item">
                                        <div class="item-label"><?= labels('delivery_charges', 'Delivery Charges') ?></div>
                                        <div class="item-value">+ <?= !empty($order['delivery_charges']) ? currency_location(decimal_points($order['delivery_charges'])) : currency_location("0") ?></div>
                                    </div>
                                    
                                    <div class="calculation-item font-weight-bold">
                                        <div class="item-label"><?= labels('order_total', "Order Total") ?></div>
                                        <div class="item-value"><?= !empty($order['final_total']) ? currency_location(decimal_points($order['final_total'])) : currency_location("0") ?></div>
                                    </div>
                                    
                                    <div class="calculation-item text-success">
                                        <div class="item-label"><?= labels('amount_paid', "Amount Paid") ?></div>
                                        <div class="item-value"><?= !empty($order['amount_paid']) ? currency_location(decimal_points($order['amount_paid'])) : currency_location("0") ?></div>
                                    </div>
                                    
                                    <div class="calculation-item text-danger font-weight-bold">
                                        <div class="item-label"><?= labels('balance', "Balance Due") ?></div>
                                        <div class="item-value"><?= currency_location(decimal_points($order['final_total'] - $order['amount_paid'])) ?></div>
                                    </div>
                                </div>
                                <div class="calculation-item text-danger">
    <div class="item-label"><?= labels('returns', 'Returns') ?></div>
    <div class="item-value">- <?= !empty($order['returns_total']) ? currency_location(decimal_points($order['returns_total'])) : currency_location("0") ?></div>
</div>
                                
                                <?php if (!empty($order['message'])) { ?>
                                <div class="order-message mt-4">
                                    <div class="message-label"><i class="bi bi-chat-left-text mr-2"></i> <?= labels('message', 'Message') ?></div>
                                    <div class="message-value"><?= $order['message'] ?></div>
                                </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h4><?= labels('payment_summary', "Payment Summary") ?></h4>
                            <?php if ($has_transactions && !empty($order) && ($order['payment_method'] != "wallet")) { ?>
                                <button type="button" class="btn btn-sm btn-success" data-customer_id="<?= !empty($order['customer_id']) ? $order['customer_id'] : "" ?>" data-order_id="<?= !empty($order['id']) ? $order['id'] : "" ?>" data-bs-toggle="modal" data-bs-target="#create_payment">
                                    <i class="bi bi-plus-circle mr-1"></i> <?= labels('create_payment', "Create Payment") ?>
                                </button>
                            <?php } ?>
                        </div>
                        <div class="card-body">
                            <?php if (isset($order) && !empty($order)) { ?>
                                <?php if ($has_transactions) { ?>
                                    <div class="table-responsive">
                                        <table class="table table-striped table-bordered" id="orders_transactions_table" data-auto-refresh="true" data-show-toggle="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100]"
                                            data-url="<?= base_url('admin/transactions/customer_transaction_table') . "/" . $order['id'] . "/" . $order['customer_id'];  ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable="true">#</th>
                                                    <th data-field="amount" data-sortable="true" data-formatter="priceFormatter"><?= labels('amount', 'Amount') ?></th>
                                                    <th data-field="payment_type" data-sortable="true"><?= labels('payment_mode', 'Payment Mode') ?></th>
                                                    <th data-field="created_at" data-visible="true"><?= labels('payment_date', "Payment Date") ?></th>
                                                    <th data-field="transaction_id" data-visible="true"><?= labels('transaction_id', "Transaction Id") ?></th>
                                                    <th data-field="status" data-sortable="true" data-formatter="statusFormatter"><?= labels('status', "Status") ?></th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                    
                                    <!-- Add JavaScript formatter functions for better display -->
                                    <script>
                                        function priceFormatter(value) {
                                            return '<span class="badge bg-success">' + value + '</span>';
                                        }
                                        
                                        function statusFormatter(value) {
                                            var className = 'bg-info';
                                            if (value.toLowerCase() === 'success' || value.toLowerCase() === 'completed') {
                                                className = 'bg-success';
                                            } else if (value.toLowerCase() === 'pending') {
                                                className = 'bg-warning';
                                            } else if (value.toLowerCase() === 'failed') {
                                                className = 'bg-danger';
                                            }
                                            return '<span class="badge ' + className + '">' + value + '</span>';
                                        }
                                    </script>
                                <?php } else { ?>
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle mr-2"></i> <?= labels('no_payments_found_payment_has_done_at_time_of_order', "No payments found payment has done at time of order") ?>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle mr-2"></i> <?= labels('no_payments_found', "No payments found") ?>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Payment Modal (Kept as is) -->
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
                    <form class="create_order_payment" method="post" action='<?= base_url('admin/transactions/save_payment') ?>' id="create_order_payment">
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
                            <label for="type"><?= labels('transaction_type', 'Transaction Type') ?></label><span class="asterisk text-danger">*</span>
                            <button type="button" class="btn btn-group btn-sm rounded-circle" data-toggle="tooltip" data-bs-placement="right" title="select 'Debit' for deducting amount from customer's balance (wallet) else select Credit">
                                <i class="fas fa-info" style="color: #63E6BE;"></i>
                            </button>
                            <select name="type" id="type" class="form-control">
                                <option value="credit" selected><?= labels('credit', 'Credit') ?></option>
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
                            <input type="number" class="form-control" id="amount" placeholder="Enter Amount" name="amount">
                            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                            <input type="hidden" name="customer_id" value="<?= $order['customer_id'] ?>">
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
<!-- Return Modal -->
<div class="modal fade" id="returnItemsModal" tabindex="-1" role="dialog" aria-labelledby="returnItemsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="returnItemsModalLabel">
                    <i class="fas fa-undo-alt mr-2"></i> <?= labels('return_items', 'Return Items') ?>
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="process_return_form" method="post" action="<?= base_url('admin/orders/process_return') ?>">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle mr-2"></i> 
                        <?= labels('return_instructions', 'Select the items to return and specify quantities.') ?>
                    </div>
                    
                    <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                            <thead class="thead-light">
                                <tr>
                                    <th width="5%">Return</th>
                                    <th width="15%">Image</th>
                                    <th width="25%">Product</th>
                                    <th width="15%">Price</th>
                                    <th width="10%">Ordered</th>
                                    <th width="10%">Remaining</th> <!-- NEW HEADER -->
                                    <th width="10%">Returned</th>
                                    <th width="10%">Qty</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($items as $item): 
                                $already_returned = isset($item['returned_quantity']) ? $item['returned_quantity'] : 0;
                                $returnable_qty = $item['quantity'] - $already_returned;
                                if ($returnable_qty <= 0) continue;
                            ?>
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" class="item-checkbox" checked>
                                </td>
                                <td>
                                    <img src="<?= !empty($item['image']) ? base_url($item['image']) : base_url('assets/admin/img/default-product.jpg') ?>" 
                                        class="img-thumbnail" style="max-height: 60px;">
                                </td>
                                <td><?= $item['product_name'] ?></td>
                                <td><?= currency_location(decimal_points($item['price'])) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <!-- NEW COLUMN ADDED HERE -->
                                <td><?= $returnable_qty ?></td>
                                <!-- END NEW COLUMN -->
                                <td><?= $already_returned ?></td>
                                <td>
                                    <select name="return_quantity[<?= $item['id'] ?>]" 
                                            class="form-control form-control-sm return-quantity" 
                                            data-price="<?= $item['price'] ?>"
                                            data-max="<?= $returnable_qty ?>">
                                        <?php for ($i = 0; $i <= $returnable_qty; $i++): ?>
                                            <option value="<?= $i ?>" <?= $i == 0 ? 'selected' : '' ?>><?= $i ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        </table>
                    </div>
                    
                    <div class="form-group">
                        <label for="return_reason" class="font-weight-bold">
                            <?= labels('return_reason', 'Return Reason') ?> *
                        </label>
                        <textarea name="return_reason" id="return_reason" class="form-control" rows="3" required></textarea>
                    </div>
                    
                    <div class="card border-primary mb-3">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><?= labels('return_summary', 'Return Summary') ?></h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?= labels('total_items', 'Total Items') ?></label>
                                        <div class="h4" id="return-item-count">0</div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label><?= labels('total_amount', 'Total Amount') ?></label>
                                        <div class="h4 text-success" id="return-total-amount"><?= currency_location('0') ?></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">
                            <i class="fas fa-times mr-1"></i> <?= labels('cancel', 'Cancel') ?>
                        </button>
                        <button type="submit" class="btn btn-primary" id="submit-return">
                            <i class="fas fa-check mr-1"></i> <?= labels('submit_return', 'Submit Return') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
    <!-- End of Return Modal -->
    <style>
        /* Customer info styling */
.customer-info p {
    padding: 8px 0;
    border-bottom: 1px dashed #eee;
    margin-bottom: 8px;
}

.payment-summary-info p {
    padding: 8px 0;
    border-bottom: 1px dashed #eee;
    margin-bottom: 8px;
}

/* Payment summary styling */
.payment-summary-box {
    padding: 10px 0;
}

.info-item {
    display: flex;
    justify-content: space-between;
    padding: 8px 0;
    border-bottom: 1px solid #eee;
    margin-bottom: 5px;
}

.calculation-item {
    display: flex;
    justify-content: space-between;
    padding: 5px 0;
    border-bottom: 1px dotted #eee;
}

.order-message {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
    border-left: 3px solid #007bff;
}

/* Make tables more readable */
.table td, .table th {
    vertical-align: middle;
}

/* Improve card styling */
.card {
    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
    margin-bottom: 20px;
}

.card-header h4 {
    margin-bottom: 0;
    font-size: 16px;
    font-weight: 600;
}

/* Improve status dropdown */
.status_update {
    min-width: 140px;
}

/* Better image display */
.img-fluid {
    max-height: 50px;
    object-fit: contain;
}

/* Tab navigation improvements */
.nav-tabs .nav-link.active {
    font-weight: 600;
    border-bottom: 3px solid #007bff;
}

/* Better badges */
.badge {
    padding: 0.4em 0.6em;
    font-weight: 600;
}

    </style>
<script>
$(document).ready(function() {
    // Calculate return totals
    function calculateReturnTotals() {
        let totalAmount = 0;
        let itemCount = 0;
        
        $('.return-quantity').each(function() {
            if ($(this).closest('tr').find('.item-checkbox').is(':checked')) {
                const quantity = parseInt($(this).val());
                const price = parseFloat($(this).data('price'));
                totalAmount += quantity * price;
                if (quantity > 0) itemCount++;
            }
        });
        
        $('#return-total-amount').text(formatCurrency(totalAmount));
        $('#return-item-count').text(itemCount);
    }
    
    // Format currency
    function formatCurrency(amount) {
        return '<?= $currency ?>' + amount.toFixed(2);
    }
    
    // Initialize calculations
    calculateReturnTotals();
    
    // Update calculations when quantities change
    $('.return-quantity').on('change', function() {
        const max = parseInt($(this).data('max'));
        const selected = parseInt($(this).val());
        
        if (selected > max) {
            $(this).val(max);
            alert('<?= labels('max_return_qty', 'Maximum return quantity is') ?> ' + max);
        }
        
        calculateReturnTotals();
    });
    
    // Update calculations when checkbox changes
    $('.item-checkbox').on('change', function() {
        if (!$(this).is(':checked')) {
            $(this).closest('tr').find('.return-quantity').val(0);
        }
        calculateReturnTotals();
    });
    
    // Form submission
    $('#process_return_form').on('submit', function(e) {
        e.preventDefault();
        
        // Validate form
        if ($('#return_reason').val().trim().length < 10) {
            alert('<?= labels('return_reason_required', 'Please provide a detailed return reason (min 10 characters)') ?>');
            return false;
        }
        
        // Check if any items are selected for return
        let hasReturns = false;
        $('.return-quantity').each(function() {
            if ($(this).closest('tr').find('.item-checkbox').is(':checked') && parseInt($(this).val()) > 0) {
                hasReturns = true;
                return false; // Break the loop
            }
        });
        
        if (!hasReturns) {
            alert('<?= labels('select_items_to_return', 'Please select at least one item to return') ?>');
            return false;
        }
        
        // Submit the form via AJAX
        const form = $(this);
        const submitBtn = $('#submit-return');
        const originalBtnText = submitBtn.html();
        
        submitBtn.html('<i class="fas fa-spinner fa-spin mr-1"></i> Processing...');
        submitBtn.prop('disabled', true);
        
        $.ajax({
    url: form.attr('action'),
    type: 'POST',
    method: 'POST', // Explicitly set both type and method
    headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'X-HTTP-Method-Override': 'POST'
    },
    data: form.serialize(),
        dataType: 'json',
        success: function(response) {
                if (response.error === false) {
                    // Success - show message and reload page
                    showAlert('success', response.message);
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    // Error - show message
                    showAlert('danger', response.message);
                    submitBtn.html(originalBtnText);
                    submitBtn.prop('disabled', false);
                }
            },
        error: function(xhr) {
            console.error('AJAX Error:', xhr.responseText);
            let errorMessage = 'Request failed';
            if (xhr.responseJSON && xhr.responseJSON.message) {
                errorMessage = xhr.responseJSON.message;
            }
            showAlert('danger', errorMessage);
        }
    });
});
    
    function showAlert(type, message) {
        const alertHtml = `
            <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        `;
        $('#process_return_form').prepend(alertHtml);
    }
});
</script>