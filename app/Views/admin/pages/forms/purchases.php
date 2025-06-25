<?php $get_purchase_id = $purchase_id ?? ''; ?>
<div class="main-content">
    <section class="section">
        <!-- header -->
        <div class="section-header">
            <?php if (!empty($order_type) && $order_type == 'order') { ?>
                <h1>Purchase Orders</h1>
            <?php } else { ?>
                <h1>Purchase Return</h1>

            <?php } ?>

            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/purchases'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('purchase_orders', 'Purchase Orders') ?></a>
                </div>
            </div>
        </div>

        <!-- Purchase form  ..START-->
        <div class="section-body">
            <form action="<?= !empty($get_purchase_id) ? base_url('admin/purchases/update') : base_url('admin/purchases/save') ?>" id="purchase_form" accept-charset="utf-8" method="POST">
                <?= csrf_field("csrf_test_name") ?> <!-- CSRF Token -->
                <?php if (!empty($get_purchase_id)) : ?>
                    <input type="hidden" name="purchase_id" value="<?= $get_purchase_id ?>">
                <?php endif; ?>
                <input type="text" hidden name="order_type" value="<?= !empty($order_type) ? $order_type : 'order' ?>">
                <input type="hidden" name="products" id="products_input" />

                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('bill_from', 'Bill From') ?></h4>
                    </div>
                    <div class="card-body">
                        <!-- warehouse supplier date -->
                        <?= view('admin/pages/forms/Purchases/partials/view/warehouse_supplier_date') ?>

                        <!-- select products -->
                        <?= view('admin/pages/forms/Purchases/partials/view/select_products') ?>
                        <!-- products table -->
                        <?= view('admin/pages/forms/Purchases/partials/view/products_table') ?>

                    </div>
                    <div class="card-body">
                        <div class="row">

                            <div class="col-md">
                               <!-- discount_shipping -->
                                <?= view('admin/pages/forms/Purchases/partials/view/discount_shipping') ?>

                                <!-- Total and profit -->
                                <?= view('admin/pages/forms/Purchases/partials/view/total_and_profit') ?>

                                <!-- payment status -->
                                <?= view('admin/pages/forms/Purchases/partials/view/payment_status') ?>

                                <!-- status select and add -->
                                <?= view('admin/pages/forms/Purchases/partials/view/select_status') ?>

                                <div class="form-group">
                                    <label for="message"><?= labels('message', 'Message') ?></label>
                                    <textarea class="form-control" name="message" id="message"></textarea>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary purchase-submit"><?= labels('save', 'Save') ?></button>&nbsp;
                        <button type="reset" value="Reset" class="reset btn btn-info" onclick="return resetForm(this.form);"><?= labels('reset', 'Reset') ?></button>
                    </div>
                </div>
            </form>
        </div>

    </section>
</div>

<!-- create status modal -->
<?= view('admin/pages/forms/Purchases/partials/view/create_status_modal') ?>

<?= view('admin/pages/forms/Purchases/partials/js/purchase_form_submit') ?>

<?php if (isset($purchase['items']) && is_array($purchase['items'])): ?>
    <script>
        window.prefillPurchaseItems = <?= json_encode($purchase['items']) ?>;
    </script>
<?php endif; ?>
<?php if (isset($purchase['payments']) && is_array($purchase['payments'])): ?>
    <script>
        window.prefillPurchasePayments = <?= json_encode($purchase['payments']) ?>;
    </script>
<?php endif; ?>