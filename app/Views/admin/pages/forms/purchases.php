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
            <form action="<?= base_url('admin/purchases/save') ?>" id="purchase_form" accept-charset="utf-8" method="POST">
                <?= csrf_field("csrf_test_name") ?> <!-- CSRF Token -->

                <input type="text" hidden name="order_type" value="<?= !empty($order_type) ? $order_type : 'order' ?>">
                <input type="hidden" name="products" id="products_input" />

                <div class="card">
                    <div class="card-header">
                        <h4><?= labels('bill_from', 'Bill From') ?></h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-md-6 col-lg-4">
                                <div class="form-group">
                                    <?php if (!empty($order_type) && $order_type == 'order') { ?>
                                        <label for="purchase_date">Purchase Date</label><span class="asterisk text-danger"> *</span>
                                    <?php } else { ?>
                                        <label for="purchase_date">Return Date</label><span class="asterisk text-danger"> *</span>
                                    <?php } ?>
                                    <input type="date" class="form-control" id="purchase_date" name="purchase_date" value="<?= date('Y-m-d') ?>">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="supplier">Supplier</label><span class="asterisk text-danger">*</span>
                                    <select class="select_supplier form-control" id="supplier" name="supplier_id">
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-1 supplier-add-btn">
                                <span><button class="btn btn-icon btn-secondary edit_btn" data-url="admin/suppliers/create" id=""><i class="fas fa-plus"></i></button></span>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="warehouse_id">Warehouse</label><span class="asterisk text-danger">*</span>
                                    <select class=" form-control" id="warehouse_id" name="warehouse_id">
                                        <option value="" selected>Select warehouse </option>
                                        <?php foreach ($warehouses as $warehouse) { ?>
                                            <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                        <?php  } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col">
                                <div class="form-group">
                                    <label for="products">Products</label><span class="asterisk text-danger">*</span>
                                    <select class="search_products form-control" id="search_products">

                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">

                            <div class="col-12">
                                <button id="remove" class="btn btn-danger" disabled>Delete</button>
                                <table class='table-striped' data-toolbar="#remove" id='purchase_order' data-toggle="table" data-click-to-select="true" data-toggle="table" data-url="" data-click-to-select="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-page-size="20" data-show-columns="true" data-mobile-responsive="true" data-toolbar="#toolbar" data-maintain-selected="true" data-query-params="queryParams" data-pagination="true">
                                    <thead>
                                        <tr>
                                            <th data-field="state" data-checkbox="true" data-width="20"></th>
                                            <th data-field="id" data-sortable="true" data-visible="false" data-card-visible="false"><?= labels('id', 'id') ?></th>
                                            <!-- <th data-field="sr" data-sortable="true" data-width="20" data-visible="true"><?= "" //labels('sr', 'Sr') 
                                                                                                                                ?></th> -->
                                            <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                                            <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="quantity" data-sortable="true" data-visible="true"><?= labels('qty', 'Qty') ?></th>
                                            <th data-field="price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('cost_price', 'Cost Price') ?></th>
                                            <th data-field="sell_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('sell_price', 'Sell Price') ?></th>
                                            <th data-field="expire" data-editable="true" data-sortable="true" data-visible="true"><?= labels('expiration_date', 'Expiration Date') ?></th>
                                            <th data-field="discount" data-sortable="true" data-visible="true"><?= labels('discount', 'Discount') . "<small> $currency</small>" ?></th>
                                            <th data-field="total" data-sortable="true" data-visible="true"><?= labels('sub_total', 'SubTotal') ?></th>
                                            <th data-field="hidden_inputs" data-visible="false"></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">

                        <div class="row">

                            <div class="col-md">
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="order_discount"><?= labels('discount', 'Discount') ?></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <span><?= $currency ?></span>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="order_discount" id="order_discount">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="shipping"><?= labels('shipping', 'Shipping') ?></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <span><?= $currency ?></span>
                                                    </div>
                                                </div>
                                                <input type="text" class="form-control" name="shipping" id="shipping">
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <div class="row">
                                    <div class="col">
                                        <h6 class="h6"><strong><?= labels('total', 'Total') ?></strong></h6>
                                        <h4 class="text-info h6 m-1 px-2" id="sub_total" data-currency="<?= $currency ?>"></h4>
                                        <input type="hidden" name="total" id="total">
                                    </div>
                                    <div class="col">
                                        <h6 class="h6"><strong><?= labels('sell_total', 'Sell Total') ?></strong></h6>
                                        <h4 class="text-black h6 m-1 px-2" id="sell_total" data-currency="<?= $currency ?>"></h4>
                                        <input type="hidden" name="total" id="sell_total">
                                    </div>
                                    <div class="col">
                                        <h6 class="h6"><strong><?= labels('estimated_profit', 'Estimated Profit') ?></strong></h6>
                                        <h4 class="text-success h6 m-1 px-2" id="profit_total" data-currency="<?= $currency ?>"></h4>
                                        <input type="hidden" name="total" id="profit_total">
                                    </div>

                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="payment_status_label" for="payment_status_item">Payment Status</label><span class="asterisk text-danger payment_status_label"> *</span>
                                            <select class="form-control payment_status" id="payment_status_item" name="payment_status">
                                                <option value="fully_paid" selected="">Fully Paid</option>
                                                <option value="partially_paid">Partially Paid</option>
                                                <option value="unpaid">Unpaid</option>
                                                <option value="cancelled">Cancelled</option>
                                            </select>
                                            <div class="amount_paid d-none" style="display: none;">
                                                <label for="amount_paid_item">Amount Paid</label><span class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" id="amount_paid_item" value="" placeholder="0.00" name="amount_paid" min="0.00">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
                                    <button type="button" class="btn btn-sm btn-success float-right mb-1" data-bs-toggle="modal" data-bs-target="#status_modal"><?= labels('add_status', 'Add Status') ?></button>
                                    <select class="form-control" id="status" name="status">
                                        <option value="">Select status</option>
                                        <?php if (!empty($status) && isset($status)) {
                                            foreach ($status as $val) { ?>
                                                <option value="<?= $val['id'] ?>"><?= $val['status'] ?></option>
                                        <?php }
                                        } ?>
                                    </select>
                                </div>
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
<div class="modal edit-modal-lg">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">Add Supplier</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>
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
                <form method="post" action='<?= base_url('admin/orders/create_status') ?>' id="create_status">
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

<script>
    $(document).ready(function() {

        // Other form handlers, for example for the customer form
        $("#purchase_form").validate({
            rules: {
                supplier_id: {
                    required: true,
                },
                warehouse_id: {
                    required: true,
                },
                purchase_date: {
                    required: true,
                    dateISO: true,
                },
                status: {
                    required: true,
                },
                // This targets at least one product being added
                products: {
                    required: true,
                }
            },
            messages: {
                supplier_id: {
                    required: "Supplier is required",
                },
                warehouse_id: {
                    required: "Warehouse is required",
                },
                purchase_date: {
                    required: "Purchase date is required",
                    dateISO: "Please enter a valid date (YYYY-MM-DD).",
                },
                status: {
                    required: "Status is required",
                },
                products: {
                    required: "Please add at least one product.",
                }
            },

            highlight: function(element) {
                $(element).removeClass("is-valid").addClass("is-invalid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");

                // Special handling for select2
                if (element.hasClass("select2-hidden-accessible")) {
                    error.insertAfter(element.next(".select2-container"));
                } else {
                    error.insertAfter(element);
                }
            },



            submitHandler: function(form) {
                if (variant_data.length === 0) {
                    showToastMessage("You must add at least one product.", "error");
                    return false;
                }

                let isValid = true;

                $("#purchase_order tbody tr").each(function() {
                    let row = $(this);
                    let qty = parseFloat(row.find(".qty").val()) || 0;
                    let price = parseFloat(row.find(".price").val());
                    let sellPrice = parseFloat(row.find(".sell_price").val());
                    let discount = parseFloat(row.find(".discount").val());
                    let expire = row.find(".expire").val();
                    let today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
                    // Check if expire is empty
                    if (!expire) {
                        isValid = false;
                        showToastMessage("Expiration date is required.", "error");
                        row.find(".expire").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    if (!sellPrice) {
                        isValid = false;
                        showToastMessage("Sell Price is required.", "error");
                        row.find(".sell_price").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    if (!price) {
                        isValid = false;
                        showToastMessage("Cost Price is required.", "error");
                        row.find(".price").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    // Quantity validation
                    if (qty <= 0) {
                        isValid = false;
                        showToastMessage("Quantity must be greater than 0.", "error");
                        return false;
                    }

                    // Price validation
                    if (price < 0) {
                        isValid = false;
                        showToastMessage("Price must not be negative.", "error");
                        return false;
                    }

                    // Sell price validation
                    if (sellPrice < 0) {
                        isValid = false;
                        showToastMessage("Sell price must not be negative.", "error");
                        return false;
                    }

                    // Expiration date validation
                    if (expire && expire < today) {
                        isValid = false;
                        showToastMessage("Expiration date cannot be in the past.", "error");
                        return false;
                    }

                    // Discount validation
                    let subtotal = qty * price;
                    if (discount < 0 || discount > subtotal) {
                        isValid = false;
                        showToastMessage("Discount must be between 0 and subtotal.", "error");
                        return false;
                    }
                });

                if (!isValid) return false;

                let formData = new FormData(form);
                formData.append(csrf_token, csrf_hash);

                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        csrf_token = response["csrf_token"];
                        csrf_hash = response["csrf_hash"];
                        if (response.success) {
                            showToastMessage(response.message, 'success');
                            // location.href = base_url + "/admin/purchases";

                        } else {
                            showToastMessage(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToastMessage('An error occurred during the request.', 'error');
                    }
                });
                return false; // Stop form submission for customer form
            }
        });
    });
</script>