<div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label"><?= labels("supplier", "Supplier:"); ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= $purchase['supplier_name'] ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label"><?= labels("warehouse", "Warehouse:"); ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= $purchase['warehouse'] ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label"><?= labels("payment_status", "Payment Status:"); ?></label>
                            <div class="col-sm-7">
                                <?php if ($purchase['payment_status'] === "fully_paid") { ?>
                                    <p class="form-control-plaintext"><span class='badge badge-success'>Fully Paid</span></p>
                                <?php } ?>
                                <?php if ($purchase['payment_status'] === "partially_paid") { ?>
                                    <p class="form-control-plaintext"><span class='badge badge-primary'>Partially Paid</span></p>
                                <?php } ?>
                                <?php if ($purchase['payment_status'] === "unpaid") { ?>
                                    <p class="form-control-plaintext"><span class='badge badge-warning'>Unpaid</span></p>
                                <?php } ?>
                                <?php if ($purchase['payment_status'] === "cancelled") { ?>
                                    <p class="form-control-plaintext"><span class='badge badge-danger'>Cancelled</span></p>
                                <?php } ?>
                                <?php if (empty($purchase['payment_status'])) { ?>
                                    <p class="form-control-plaintext"><span class='badge badge-info'>Not Decided</span></p>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label"><?= labels("discount", "Discount:") ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-success">+ <?= currency_location(decimal_points($purchase['discount'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label"><?= labels("shipping", "shipping:") ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-danger">- <?= currency_location(decimal_points($purchase['delivery_charges'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold"><?= labels("final_total", "Final Total:") ?></label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext font-weight-bold"><?= currency_location(decimal_points($purchase['total'])) ?? '-' ?></p>
                            </div>
                        </div>

                    </div>
                </div>