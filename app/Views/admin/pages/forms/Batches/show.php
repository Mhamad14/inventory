<div class="main-content">


    <!-- Header  ...START-->
    <section class="section">
        <div class="section-header">
            <h1><?= labels("return_purchase", "Purchase return"); ?> <i class="bi bi-box-arrow-down"></i></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/purchases'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('purchase_orders', 'Purchase Orders') ?></a>
                </div>
            </div>
        </div>
    </section>
    <!-- Header  ...END-->

    <!-- Purchase Order Details ...START-->
    <section class="section">
        <div class="card">
            <?= view('admin/pages/forms/Batches/partials/card_header', ['show_label' => labels('purchase_order_details', 'Purchase Order Details'), 'aria_control' => 'orderDetailsBody']) ?>

            <div class="card-body collapse hide" id="orderDetailsBody">
                <?= view('admin/pages/forms/Batches/partials/purchase_order_creator') ?>
                <?= view('admin/pages/forms/Batches/partials/purchase_details') ?>
                <?= view('admin/pages/forms/Batches/partials/purchase_details_message') ?>
            </div>
        </div>
    </section>

    <section class="section">
        <div class="section-body">
            <form action="<?= base_url('admin/purchases/save') ?>" id="purchase_form" accept-charset="utf-8" method="POST">
                <?= csrf_field("csrf_test_name") ?> <!-- CSRF Token -->

                <input type="text" hidden name="order_type" value="<?= !empty($order_type) ? $order_type : 'order' ?>">
                <input type="hidden" name="products" id="products_input" />

                <div class="card">
                    <?= view('admin/pages/forms/Batches/partials/card_header', ['show_label' => labels('purchase_items', 'Purchase Items'), 'aria_control' => 'batchesListBody']) ?>
                    <div class="card-body collapse hide" id="batchesListBody">
                        <div class="row">
                            <?= view('admin/pages/forms/Batches/partials/batches_table') ?>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <?= view('admin/pages/forms/Batches/partials/card_header', ['show_label' => labels('returned_items', 'Returned Items'), 'aria_control' => 'ReturnedBatchesListBody']) ?>
                    <div class="card-body collapse show" id="ReturnedBatchesListBody">
                        <div class="row">
                            <?= view('admin/pages/forms/Batches/partials/returned_batches_table') ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </section>
</div>
<?= view('admin/pages/forms/Batches/partials/batch_edit_modal') ?>

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

<!-- delete returned items -->
<script>
    $(document).on('click', '#btn_returned_delete', function() {
        const returned_batch_id = $(this).data('returned_batch_id');
        const $button = $(this);

        Swal.fire({
            title: 'Are you sure?',
            text: "You are about to delete a returned item!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#5cb85c',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, Delete!',
            cancelButtonText: 'No, cancel!'
        }).then((result) => {
            if (result.value == true) {

                $.ajax({
                    url: '<?= base_url("admin/batches/delete_returned_batch") ?>/' + returned_batch_id,
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToastMessage(response.message, 'success');
                            setTimeout(() => {
                                $('#form_returned_batches_items').bootstrapTable('refresh');
                            }, 500);

                        } else {
                            if (typeof response.message === 'object') {
                                for (const field in response.message) {
                                    showToastMessage(response.message[field], 'error');
                                }
                            } else {
                                showToastMessage(response.message, 'error');
                            }
                        }
                    },
                    error: function() {
                        showToastMessage('Deletion failed.', 'error');
                    }
                });
            } else {
                showToastMessage('Action cancelled.', 'error');
            }

        });
    });
</script>