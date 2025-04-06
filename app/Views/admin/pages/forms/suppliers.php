<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Suppliers</h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" id="" href="<?= base_url('admin/suppliers'); ?>"  data-toggle="tooltip" data-bs-placement="bottom"    title="  <?= labels('suppliers_list', 'Suppliers') ?> "    ><i class="fas fa-list"></i> </a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="card">
                <div class="card-header">
                    <h4><?=   labels($from_title) ?></h4>
                </div>
                <div class="card-body">
                    <form action="<?= base_url('admin/suppliers/save') ?>" method="post" id="supplier_form" class="form-submit-event">
                        <?php if (isset($fetched_data[0]['id'])) { ?>
                            <input type="hidden" name="edit_attribute_set" value="<?= @$fetched_data[0]['id'] ?>">
                        <?php  } ?>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="name" name="name" value="<?= !empty($supplier['first_name']) ? $supplier['first_name'] : "" ?>" required>
                                    <input type="hidden" name="user_id" value="<?= isset($user_id) ? $user_id : "" ?>">
                                    <input type="hidden" name="supplier_id" value="<?= !empty($supplier['sup_id']) ? $supplier['sup_id'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="mobile"><?= labels('mobile_number', 'Mobile') ?></label><span class="asterisk text-danger"> *</span>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <i class="fas fa-phone"></i>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control phone-number" placeholder="Enter Your Mobile Number" id="identity" name="identity" value="<?= !empty($supplier['mobile']) ? $supplier['mobile'] : "" ?>" required>
                                    </div>
                                    <span class="text-danger text-bold phone-number-error-message"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="email"><?= labels('email', 'Email') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="email" class="form-control" id="email" name="email" value="<?= !empty($supplier['email']) ? $supplier['email'] : "" ?>" required>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <label for="balance"><?= labels('opening_balance', 'Opening Balance') ?></label>
                                    <input type="number" min="0.00" class="form-control" id="balance" name="balance" value="<?= !empty($supplier['balance']) ? $supplier['balance'] : "" ?>">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="credit_period"><?= labels('credit', 'Credit Period') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <span>Days</span>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="credit_period" id="credit_period" value="<?= !empty($supplier['credit_period']) ? $supplier['credit_period'] : "" ?>">
                                    </div>
                                    <span class="text-danger text-bold credit_period-error-message"></span>
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="credit_limit"><?= labels('credit_limit', 'Credit Limit') ?></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <div class="input-group-text">
                                                <span><?= $currency ?></span>
                                            </div>
                                        </div>
                                        <input type="text" class="form-control" name="credit_limit" id="credit_limit" value="<?= !empty($supplier['credit_limit']) ? $supplier['credit_limit'] : "" ?>">
                                    </div>
                                    <span class="text-danger text-bold credit_limit-error-message"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group ">
                                    <label for="billing_address"><?= labels('billing_address', 'Billing Address') ?></label>
                                    <textarea class="form-control" id="billing_address" name="billing_address"><?= !empty($supplier['billing_address']) ? $supplier['billing_address'] : "" ?></textarea>
                                </div>
                            </div>

                            <div class="col-12 col-md-6 col-lg-6">
                                <div class="form-group ">
                                    <label for="shipping_address"><?= labels('shipping_address', 'Shipping Address') ?></label>
                                    <textarea class="form-control" id="shipping_address" name="shipping_address"><?= !empty($supplier['shipping_address']) ? $supplier['shipping_address'] : "" ?></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="tax_name"><?= labels('tax_name', 'Tax Name') ?></label>
                                    <input type="text" class="form-control" name="tax_name" id="tax_name" value="<?= !empty($supplier['tax_name']) ? $supplier['tax_name'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-12 col-md-6 col-lg-3">
                                <div class="form-group">
                                    <label for="tax_no"><?= labels('tax_no', 'Tax No') ?></label>
                                    <input type="text" class="form-control" name="tax_no" id="tax_no" value="<?= !empty($supplier['tax_num']) ? $supplier['tax_num'] : "" ?>">
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="status" class="custom-switch  p-35">
                                        <?php if (!empty($supplier['status']) && $supplier['status'] == "1") { ?>

                                            <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                        <?php } elseif (isset($supplier['status']) && $supplier['status'] == "0") { ?>
                                            <input type="checkbox" name="status" id="status" class="custom-switch-input">
                                        <?php } else { ?>
                                            <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                        <?php } ?>
                                        <span class="custom-switch-indicator"></span>
                                        <span class="custom-switch-description"><?= labels('status', 'Status') ?></span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary"><?= labels('save', 'Save') ?></button>&nbsp;
                        <button type="reset" value="Reset" class="reset btn btn-info" onclick="return resetForm(this.form);"><?= labels('reset', 'Reset') ?></button>
                    </form>
                </div>
            </div>
        </div>
    </section>
</div>