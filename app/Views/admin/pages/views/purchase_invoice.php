<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('purchase_invoice', 'Purchase Invoice') ?></h1>
        </div>
        <?= session("message"); ?>
        <?php if (!empty($order)) { ?>
            <div class="section">
                <div class="section-body">
                    <div class="invoice section-to-print">
                        <div class="invoice-print">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="invoice-title">
                                        <h2>Invoice</h2>
                                        <div class="invoice-number"><?= "#INVOC-" . $order['id'] ?></div>
                                    </div>
                                    <hr>

                                    <div class="row">
                                        <div class="col-md-6 col-sm-12">
                                            <address>
                                                <strong>Billed From:</strong><br>
                                                <?= $order['first_name'] . " " . $order['last_name'] ?><br>
                                                <?= $order['mobile'] ?><br>
                                                <?= $order['email'] ?><br>
                                            </address>
                                        </div>
                                        <div class="col-md-6 col-sm-12 text-md-right">
                                            <div class=" invoice-logo">
                                            </div>
                                            <strong><?= $order['name'] . " - " . $order['description'] ?></strong><br>
                                            <address>
                                                Address: <?= $order['address'] ?><br>
                                                Contact: <?= $order['contact'] ?><br>
                                                <?php if (isset($order['warehouse_id']) && ! empty($order['warehouse_id'])) { ?>
                                                    Warehouse : <?= ucfirst($order['warehouse_name'])  ?><br>
                                                <?php }  ?>
                                                <strong><?= $order['b_tax'] ?></strong><?= ": " . $order['tax_value'] ?>
                                            </address>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <address>
                                                <strong>Payment Method:</strong><br>
                                                <?php //echo $order['payment_method'];
                                                ?><br>
                                            </address>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <address>
                                                <strong>Order Date:</strong><br>
                                                <?= $order['created_at'] ?><br><br>
                                            </address>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="section-title">Order Summary</div>
                                <p class="section-lead">All items here cannot be deleted.</p>
                                <div class="table-responsive">
                                    <table class="table table-hover table-borderd" id="invoice_table" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "invoice-order-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/purchases/invoice_table/' . $order['id']); ?>" data-search="true">
                                        <thead>
                                            <tr>
                                                <th data-field="name" data-sortable="true" data-visible="true">Name</th>
                                                <th data-field="price" data-sortable="true" data-visible="true">Price <small>(Inclusive of Tax)</small> </th>
                                                <th data-field="quantity" data-sortable="true" data-visible="true">Quantity</th>
                                                <th data-field="discount" data-sortable="true" data-visible="true"><?= labels('discount', 'Discount') ?></th>
                                                <th data-field="subtotal" data-sortable="true" data-visible="true">Subtotal</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-8">
                                        <div class="section-title">Payment Summary</div>
                                        <?php if ($order['payment_status'] == "fully_paid") { ?>
                                            <p class="section-lead"><?= "<strong>Fully Paid</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "partially_paid") { ?>
                                            <p class="section-lead"><?= "<strong>Partially Paid</strong>" ?></p>
                                            <p class="section-lead"><?= "<strong>" . $order['amount_paid'] . "</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "unpaid") { ?>
                                            <p class="section-lead"><?= "<strong>No Payment of order found!</strong>" ?></p>
                                        <?php } ?>
                                        <?php if ($order['payment_status'] == "cancelled") { ?>
                                            <p class="section-lead"><?= "<strong>Cancelled</strong>" ?></p>
                                        <?php } ?>

                                    </div>
                                    <div class="col-lg-4 text-right">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Delivery charges</div>
                                            <div class="invoice-detail-value"><?= currency_location(number_format($order['delivery_charges'], 2)) ?></div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Tax</div>
                                            <div class="invoice-detail-value">

                                                <?php

                                                $taxTotal = 0;
                                                foreach ($tax as $tax_item) {
                                                    echo $tax_item['name'] . " : ";
                                                    echo $tax_item['percentage'] . " %";
                                                    $taxTotal +=  $sub_total * ($tax_item['percentage'] / 100);
                                                    echo "<br>";
                                                }
                                                if ($taxTotal) {
                                                    echo "Total tax : " . number_format($taxTotal, 2);
                                                } else {
                                                    echo currency_location(number_format($taxTotal, 2));
                                                }
                                                ?>
                                            </div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Discount</div>
                                            <div class="invoice-detail-value"><?= currency_location(number_format($order['purchase_discount'], 2)) ?></div>
                                        </div>
                                        <hr class="mt-2 mb-2">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Total</div>
                                            <div class="invoice-detail-value"><?= currency_location(number_format($order['total'], 2)) ?></div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        <?php } else { ?>
            <div class="section">
                <div class="section-body">
                    <div class="invoice">
                        <div class="invoice-print">
                            <div class="row">
                                <div class="col-md-12 col-sm-12 d-flex justify-content-between">
                                    <h2 class="text-left invoice-logo">
                                        <img class="d-block img-fluid">
                                    </h2>
                                </div>
                                <h6 class="text-left">
                                    </h2>
                                    <address>
                                        Address: <br>
                                        Contact: <br>
                                        <strong></strong>
                                    </address>
                                    <div class="invoice-title col-md-12 col-sm-12 d-flex justify-content-between">
                                        <h2>Invoice</h2>
                                    </div>
                                    <div class="invoice-number"> "#INVOC-"</div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <address>
                                                <strong>Billed To:</strong><br>

                                            </address>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <address>
                                                <strong>Payment Method:</strong><br>
                                                <br>
                                            </address>
                                        </div>
                                        <div class="col-md-6 text-md-right">
                                            <address>
                                                <strong>Order Date:</strong><br>


                                                <br><br>
                                            </address>
                                        </div>
                                    </div>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-md-12">
                                <div class="section-title">Order Summary</div>
                                <p class="section-lead">All items here cannot be deleted.</p>
                                <div class="table-responsive">
                                    <table class="table table-striped table-hover table-md">

                                    </table>
                                </div>
                                <div class="row mt-4">
                                    <div class="col-lg-8">
                                        <div class="section-title">Payment Method</div>
                                        <p class="section-lead">The payment method that we provide is to make it easier for you to pay invoices.</p>
                                        <div class="images">

                                        </div>
                                    </div>
                                    <div class="col-lg-4 text-right">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Subtotal</div>
                                            <div class="invoice-detail-value"></div>
                                        </div>
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Shipping</div>
                                            <div class="invoice-detail-value"></div>
                                        </div>
                                        <hr class="mt-2 mb-2">
                                        <div class="invoice-detail-item">
                                            <div class="invoice-detail-name">Total</div>
                                            <div class="invoice-detail-value invoice-detail-value-lg"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
            </div>
        <?php } ?>
    </section>
</div>