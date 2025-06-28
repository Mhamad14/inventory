<div class="main-content">
    <section class="section">
        <div class="section-header d-flex justify-content-between align-items-center mb-4">
            <h1 class="fw-bold text-primary"><?= labels('invoice', 'Invoice') ?></h1>
        </div>

        <?= session("message"); ?>

        <?php if (!empty($order)) { ?>
            <div class="section-body">
                <div class="card shadow-lg border-0 rounded-4 p-5 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <img src="<?= base_url($order['icon']) ?>" class="img-fluid rounded-3 shadow-sm" style="max-height: 80px;">
                        <div id="section-not-to-print" class="d-flex gap-2">
                            <a href="<?= base_url('admin/invoices/view_invoice/' . $order['id']); ?>" class="btn btn-outline-primary" target="_blank">
                                <i class="bi bi-file-pdf"></i> PDF
                            </a>
                            <a data-order_id="<?= $order['id'] ?>" data-email="<?= $order['email'] ?>" class="btn btn-success" id="send_invoice">
                                <i class="bi bi-envelope"></i> Send
                            </a>
                            <a href="<?= base_url('admin/invoices/thermal_print/' . $order['id']) ?>" class="btn btn-warning" target="_blank">
                                <i class="fas fa-print"></i> Thermal Print
                            </a>
                        </div>
                    </div>

                    <h5 class="fw-bold mb-2 text-dark"><?= $order['business_name'] . " - " . $order['description'] ?></h5>
                    <address class="mb-4 text-secondary small">
                        <span class="d-block mb-1"><i class="bi bi-geo-alt"></i> Address: <?= $order['address'] ?></span>
                        <span class="d-block mb-1"><i class="bi bi-telephone"></i> Contact: <?= $order['contact'] ?></span>
                        <?php if (isset($order['warehouse_id']) && !empty($order['warehouse_id'])) { ?>
                            <span class="d-block mb-1"><i class="bi bi-box"></i> Warehouse: <?= ucfirst($order['warehouse_name']) ?></span>
                        <?php } ?>
                    </address>

                    <div class="d-flex justify-content-start align-items-center mb-3 gap-2">
                        <h2 class="fw-bold text-gradient mb-0">Invoice number :</h2>
                        <div class="fs-5 text-muted bg-light px-3 py-1 rounded-pill shadow-sm mb-0"><?= $order['id'] ?></div>
                    </div>
                    <hr class="mb-4">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <address class="bg-light rounded-3 p-3 mb-2">
                                <strong class="text-dark fs-5">Billed To:</strong><br>
                                <span class="fw-semibold fs-6"><?= $order['first_name'] . " " . $order['last_name'] ?></span><br>
                                <span class="fs-6"><?= $order['mobile'] ?></span><br>
                                <span class="fs-6"><?= $order['email'] ?></span><br>
                            </address>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <address class="bg-light rounded-3 p-3 mb-2">
                                <strong class="text-dark fs-5">Payment Method:</strong><br>
                                <span class="fs-6"><?= $order['payment_method'] ?></span><br>
                                <strong class="text-dark fs-5">Order Date:</strong><br>
                                <span class="fs-6"><?= $order['created_at'] ?></span><br>
                            </address>
                        </div>
                    </div>

                    <h5 class="fw-bold mt-4 mb-2 text-dark">Order Summary</h5>
                    <p class="text-muted small mb-3">All items here cannot be deleted.</p>
                    <div class="table-responsive rounded-3 shadow-sm">
                        <table class="table table-hover align-middle mb-0" id="invoice_table"
                            data-toggle="table"
                            data-url="<?= base_url('admin/invoices/invoice_table/' . $order['id']); ?>">
                            <thead class="table-primary">
                                <tr>
                                    <th data-field="order_type">Product/Service</th>
                                    <th data-field="name">Name</th>
                                    <th data-field="price">Price</th>
                                    <th data-field="quantity">Quantity</th>
                                    <th data-field="subtotal">Subtotal</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="row mt-5">
                        <div class="col-lg-8">
                            <h5 class="fw-bold mb-3 text-dark" style="font-size: 1.35rem;">Payment Summary</h5>
                            <?php if ($order['payment_status'] == "fully_paid") { ?>
                                <div class="alert alert-success py-2 px-3 mb-2 border-0 shadow-sm" style="font-size: 1.15rem;">
                                    <i class="bi bi-check-circle-fill"></i>
                                    Fully Paid, Amount Paid: <strong><?= currency_location(number_format($order['amount_paid'])) ?></strong>,
                                    Remaining: <strong><?= currency_location(number_format($order['final_total'] - $order['amount_paid'])) ?></strong>
                                </div>
                            <?php } elseif ($order['payment_status'] == "partially_paid") { ?>
                                <div class="alert alert-warning py-2 px-3 mb-2 border-0 shadow-sm" style="font-size: 1.15rem;">
                                    <i class="bi bi-exclamation-circle-fill"></i>
                                    Partially Paid, Amount Paid: <strong><?= currency_location(number_format($order['amount_paid'])) ?></strong>,
                                    Remaining: <strong><?= currency_location(number_format($order['final_total'] - $order['amount_paid'])) ?></strong>
                                </div>
                            <?php } elseif ($order['payment_status'] == "unpaid") { ?>
                                <div class="alert alert-danger py-2 px-3 mb-2 border-0 shadow-sm" style="font-size: 1.15rem;">
                                    <i class="bi bi-x-circle-fill"></i>
                                    No Payment of order found!
                                </div>
                            <?php } elseif ($order['payment_status'] == "cancelled") { ?>
                                <div class="alert alert-secondary py-2 px-3 mb-2 border-0 shadow-sm" style="font-size: 1.15rem;">
                                    <i class="bi bi-slash-circle-fill"></i>
                                    Cancelled
                                </div>
                            <?php } ?>
                        </div>

                        <div class="col-lg-4">
                            <div class="border-0 rounded-4 p-4 bg-gradient bg-light shadow-sm">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Subtotal</span>
                                    <strong><?= currency_location(number_format($order['total'], 2)) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Delivery Charges</span>
                                    <strong><?= currency_location(number_format($order['delivery_charges'], 2)) ?></strong>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-secondary">Discount</span>
                                    <strong><?= currency_location(number_format($order['discount'], 2)) ?></strong>
                                </div>
                                <hr>
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold text-dark">Total</span>
                                    <span class="fw-bold text-primary fs-4"><?= currency_location(number_format($order['final_total'], 2)) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } else { ?>
            <div class="section-body">
                <div class="alert alert-warning text-center shadow-sm rounded-3">No invoice data found.</div>
            </div>
        <?php } ?>
    </section>
</div>
<style>
.text-gradient {
    background: linear-gradient(90deg, #007bff 0%, #00c6ff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.bg-gradient {
    background: linear-gradient(135deg, #f8fafc 0%, #e9ecef 100%) !important;
}
</style>