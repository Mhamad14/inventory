<div class="main-content">
    <!-- section title and Back button ..Strart -->
    <section class="section">
        <div class="section-header mt-1">
            <h1><?= labels('order_details', 'Order Details') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/orders'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('orders', 'Orders') ?></a>
                </div>
            </div>
        </div>
    </section>
    <!-- section title and Back button ..End -->

    <!-- section Customer Details ..start -->
    <div class="card mt-5">
        <!-- card header -->
        <div class="card-header mt-2">
            <h6 class="" style="cursor: pointer;" data-toggle="collapse" data-target="#orderDetailsBody" aria-expanded="false" aria-controls="orderDetailsBody">
                <span id="orderToggleIcon">▶</span>
                <?= labels('order_details', 'Order Details') ?>
            </h6>

        </div>

        <div id="orderDetailsBody" class="collapse show">
            <div class="card-body">

                <div class="row mb-5">
                    <div class="col-4">
                        <div class="row">
                            <div class="col-5 text-right text-muted"><label class="col-form-label "><?= labels('order_id', 'Order Id:')  ?></label></div>
                            <div class="col-7 text-left">
                                <p class="form-control-plaintext text-muted text-left">#<?= $order['id'] ?? '' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="row">
                            <label class="col-5 col-form-label text-muted text-right"><?= labels('created_by', 'Created by:')  ?></label>
                            <div class="col-7">
                                <p class="form-control-plaintext text-muted text-left"><?= $order['creator_name'] ?? '' ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-4 ">
                        <div class="row">
                            <label class="col-5 col-form-label text-muted text-right"><?= labels('creator_role', 'Creater Role:')  ?></label>
                            <div class="col-7 text-left">
                                <p class="form-control-plaintext text-muted text-left"><?= $order['creator_role'] ?? '' ?></p>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row">

                    <!-- Left Column -->
                    <div class="col-md-6">

                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Subtotal</label>
                            <div class="col-sm-7">
                                <!-- currency_location(decimal_points($customer['balance'])), -->
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($order['total'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Discount</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-danger">- <?= currency_location(decimal_points($order['discount'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Delivery Charges</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-success">+ <?= currency_location(decimal_points($order['delivery_charges'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold">Final Total</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext font-weight-bold"><?= currency_location(decimal_points($order['final_total'])) ?? '-' ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- left column end -->
                    <!-- Right Column -->
                    <div class="col-md-6">

                        <!-- Amount Paid  -->
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Amount Paid</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($order['amount_paid'])) ?? 'not defined' ?></p>
                            </div>
                        </div>

                        <!-- Debt and overall order payback -->
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Debt</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($order['debt'])) ?? 'not defined' ?></p>
                                <?php if (!empty($order['debt']) && $order['debt'] != 0 && $order['debt'] != "not defined"): ?>
                                    <!-- <form action="<?= "" //base_url('admin/customers/' . $order['user_id']) . '/payback_all_debt' 
                                                        ?>" id="form_payback_all_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST"> -->
                                    <form action="" id="form_payback_all_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                        <?= csrf_field("csrf_pacsrf_payback_all_debtybacl_all_debt") ?> <!-- CSRF Token -->
                                        <input type="hidden" name="_method" value="PUT">
                                        <button class="btn btn-success" type="submit"><?= labels('full_order_payback', 'Full Order Payback') ?></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- returned total -->
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Returned Total</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-danger">- <?= currency_location(decimal_points($order['returns_total'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- righ column .END -->
                </div>

                <!-- partial payback -->
                <div class="row">
                    <?php if (!empty($order['debt']) && $order['debt'] != 0 && $order['debt'] != "not defined"): ?>
                        <!-- <form action="<?= "" //base_url('admin/customers/payback_partial_debt') 
                                            ?>" id="form_payback_partial_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST"> -->
                        <form action="<?= base_url('admin/customers/payback_partial_debt') ?>" id="form_payback_partial_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                            <?= csrf_field("csrf_payback_partial_debt") ?> <!-- CSRF Token -->
                            <input type="hidden" name="_method" value="PUT">

                            <div class="form-row align-items-center">

                                <div class="col-8"><input class="form-control" name="partial_amount" type="text" placeholder="Enter amount" /></div>
                                <div class="col-4"><button class="btn btn-primary form-control" type="submit"><?= labels('partial_payback', 'Partial Pay back') ?></button></div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

                <!-- Customer and message -->
                <div class="row mt-4">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">

                                <h5 class="card-title mb-1 text-small text-secondery" style="cursor: pointer;" data-toggle="collapse" data-target="#orderMessageBody" aria-expanded="false" aria-controls="orderMessageBody">
                                    <span id="orderMessageToggleIcon">▶</span>
                                    <?= labels('order_message', 'Order Message') ?>
                                </h5>
                                <blockquote class="blockquote collapse hide text-small mb-0" id="orderMessageBody">
                                    <p><?= $order['message'] ?></p>
                                    <footer class="blockquote-footer"><?= labels('customer_name', 'Customer Name') ?>: <cite title="Source Title"><?= $order['customer_name'] ?? 'No Customer' ?></cite></footer>
                                </blockquote>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- section Customer Details ..End -->


    <!-- section: Order Items ..Start-->
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="" style="cursor: pointer;" data-toggle="collapse" data-target="#ordersSectionBody" aria-expanded="false" aria-controls="ordersSectionBody">
                <span id="ordersToggleIcon">▶</span> <?= labels('order_items', 'Order Items') ?>
            </h6>
        </div>
        <div id="ordersSectionBody" class="collapse show">
            <div class="card-body">

                <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "orders-items-list","ignoreColumn": ["action"]}' id="form_orders_items" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200]" data-url="<?= base_url('admin/orders/orders_items_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="orders_items_query" data-show-index="true">
                    <thead>
                        <tr>
                            <th data-formatter="rowNumberFormatter">#</th>
                            <!-- <th data-field="id" data-width="20" data-width-unit="px" style="width: 20px;" data-sortable="true"><?= "" //labels('orders_items_id', 'Id') 
                                                                                                                                    ?></th> -->
                            <th data-field="image" data-width="50" data-width-unit="px" data-sortable="true"><?= labels('image', 'Image') ?></th>
                            <th data-field="categorey" data-sortable="true"><?= labels('categorey', 'Categorey') ?></th>
                            <th data-field="brand" data-sortable="true"><?= labels('brand', 'Brand') ?></th>
                            <th data-field="product_name" data-sortable="true"><?= labels('product_name', 'Name') ?></th>
                            <th data-field="quantity" data-width="40" data-width-unit="px" style="width: 40px;" data-sortable="true"><?= labels('quantity', 'Quantity') ?></th>
                            <th data-field="price" data-sortable="true"><?= labels('price', 'Price') ?></th>
                            <th data-field="total" data-sortable="true"><?= labels('total', 'Total') ?></th>
                            <th data-field="warehouse_name" data-width="70" data-width-unit="px" style="width: 50px;" data-sortable="true"><?= labels('warehouse', 'Warehouse') ?></th>
                            <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                            <th data-field="actions" data-sortable="true"><?= labels('actions', 'Actions') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- section: Order items ..End-->


</div>


<script>
    // predefinded function
    toggleSection('#orderDetailsBody', '#orderToggleIcon');
    toggleSection('#orderMessageBody', '#orderMessageToggleIcon');

    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-order-item')) {
            const button = e.target.closest('.edit-order-item');
            const rowData = JSON.parse(button.dataset.row);

            // Access data like:
            document.getElementById('product_name_display').textContent = rowData.product_name;
            document.getElementById('product_brand_display').textContent = rowData.brand;
            document.getElementById('product_categorey_display').textContent = rowData.category;

            document.querySelector('input[name="quantity"]').value = rowData.quantity;
            document.querySelector('input[name="price"]').value = rowData.price;
            document.getElementById('product_total_display').textContent = rowData.total;

            // Set the selected status in dropdown
            const statusSelect = document.querySelector('select[name="status"]');
            statusSelect.value = rowData.status_id; // This will automatically select the matching option

            // Set the selected warehouse in dropdown
            const warehouseSelect = document.querySelector('select[name="warehouse"]');
            warehouseSelect.value = rowData.warehouse_id; // This will automatically select the matching option


        }
    });

    function rowNumberFormatter(value, row, index) {
        return index + 1;
    }

    function toggleSection(sectionBody, toggleIcon) {
        $(document).ready(function() {
            $(sectionBody).on('show.bs.collapse', function() {
                $(toggleIcon).html('▼');
            }).on('hide.bs.collapse', function() {
                $(toggleIcon).html('▶');
            });
        });
    }
</script>

<!-- Modal Order Items Update ..Start -->
<div class="modal fade" id="orders_items_edit_moadl" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" role="form">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Update order item</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="section">
                    <div class="section-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-body">

                                        <!-- categorey -->
                                        <div class="row">
                                            <div class="col-6 text-left"><label><?= labels("categorey", "categorey") ?></label></div>
                                            <div class="col-6 text-left">
                                                <p id="product_categorey_display"></p>
                                            </div>
                                        </div>
                                        <!-- brand -->
                                        <div class="row">
                                            <div class="col-6 text-left"><label><?= labels("brand", "Brand") ?></label></div>
                                            <div class="col-6 text-left">
                                                <p id="product_brand_display"></p>
                                            </div>
                                        </div>
                                        <!-- Product Name -->
                                        <div class="row">
                                            <div class="col-6 text-left"><label><?= labels("product_name", "Product Name: ") ?></label></div>
                                            <div class="col-6 text-left">
                                                <p id="product_name_display"></p>
                                            </div>
                                        </div>

                                        <form id="order_item_edit_form">
                                            <input type="hidden" name="item_id">
                                            <input type="hidden" name="order_id">

                                            <!-- Quantity -->
                                            <div class="mb-3">
                                                <label class="form-label"><?= labels("quantity", "Quantity") ?></label>
                                                <input type="number" name="quantity" class="form-control">
                                            </div>

                                            <!-- Price -->
                                            <div class="mb-3">
                                                <label class="form-label">Price</label>
                                                <input type="text" name="price" class="form-control">
                                            </div>

                                            <!-- Total -->
                                            <div class="row">
                                                <div class="col-6 text-left"><label><?= labels("total", "Total") ?></label></div>
                                                <div class="col-6 text-left">
                                                    <?= ""//currency_location(decimal_points(""))?> <p id='product_total_display'></p>
                                                </div>
                                            </div>

                                            <!-- Status -->
                                            <div class="mb-3">
                                                <label class="form-label"><?= labels("status", "Status") ?></label>
                                                <select name="status" class="form-select">
                                                    <?php foreach ($status_list as $status): ?>
                                                        <option value="<?= $status['id'] ?>">
                                                            <?= $status['status'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <!-- Warehouse -->
                                            <div class="mb-3">
                                                <label class="form-label"><?= labels("warehouse", "Warehouse") ?></label>
                                                <select name="warehouse" class="form-select">
                                                    <?php foreach ($warehouses as $warehouse): ?>
                                                        <option value="<?= $warehouse['id'] ?>">
                                                            <?=$warehouse['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>


                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Return</button>
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<!-- Modal Order Items Update ..End -->