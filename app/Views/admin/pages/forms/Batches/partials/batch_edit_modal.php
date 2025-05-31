<!-- Modal Order Items Update ..Start -->
<div class="modal fade" id="batch_edit_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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

                                        <div class="row">
                                            <div class="row">
                                                <div class="col-md-4 col-sm-12">
                                                    <div class="card" style="width: 100%">
                                                        <img id="modalCardImage" src="" class="card-img-top" alt="Batch Image">
                                                        <div class="card-body">
                                                            <h5 id="modalCardTitle" class="card-title"></h5>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-8 col-sm-12">
                                                    <div class="row">
                                                        <div class="col-4 text-left"><label for=""><label><?= labels("product", "Product") ?></label></div>
                                                        <div class="col-8 text-left">
                                                            <label id="product_display"></label>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-4 text-left"><label for=""><label><?= labels("batch_number", "Batch no.") ?></label></div>
                                                        <div class="col-8 text-left">
                                                            <label id="batch_number_display"></label>
                                                        </div>
                                                    </div>
                                                    <!-- Status -->
                                                    <div class="row">
                                                        <div class="col-4 text-left">
                                                            <label class="form-label"><?= labels("status", "Status") ?></label>
                                                        </div>
                                                        <div class="col-8 text-left">
                                                            <form action="<?= url_to("admin/batches/update_purchase_item_status") ?>" method="post" id="status-form">
                                                                <?= csrf_field("csrf_update_status") ?>
                                                                <input type="hidden" name="order_id" id="order_id" />
                                                                <select name="status" class="form-select" id="status-select">
                                                                    <?php foreach ($status_list as $status): ?>
                                                                        <option value="<?= $status['id'] ?>">
                                                                            <?= $status['status'] ?>
                                                                        </option>
                                                                    <?php endforeach; ?>
                                                                </select>
                                                            </form>
                                                        </div>
                                                    </div>

                                                    <!-- batch form -->
                                                    <form id="batch-form" class="mt-3">
                                                        <input type="hidden" name="item_id" id="item_id"> <!-- Used for identifying item -->
                                                        <input type="hidden" name="save_product_variant_id" id="save_product_variant_id"> <!-- Used for identifying item -->
                                                        <input type="hidden" name="save_batch_id" id="save_batch_id"> <!-- Used for identifying item -->
                                                        <div class="row mb-3">
                                                            <div class="col-4 text-left"><label for="quantity"><label><?= labels("quantity", "Quantity") ?></label></div>
                                                            <div class="col-8 text-left">
                                                                <input type="number" name="quantity" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4 text-left"><label for="cost_price"><label><?= labels("cost_price", "Cost Price") ?></label></div>
                                                            <div class="col-8 text-left">
                                                                <input type="number" name="cost_price" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4 text-left"><label for="sell_price"><label><?= labels("sell_price", "Sell Price") ?></label></div>
                                                            <div class="col-8 text-left">
                                                                <input type="number" name="sell_price" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4 text-left"><label for="sell_price"><label><?= labels("discount", "Discount") ?></label></div>
                                                            <div class="col-8">
                                                                <input type="number" name="discount" class="form-control">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4 text-left"><label for="expire"><?= labels("expire", "Expire Date") ?></label></div>
                                                            <div class="col-8 text-left">
                                                                <input type="text" name="expire" id="expire" class="form-control" placeholder="Select expire date" autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-4"></div>
                                                            <div class="col-8 ">
                                                                <button type="button" class="btn btn-danger" id="delete-btn">Delete</button>
                                                                <button type="button" class="btn btn-success" id="save-btn">Save</button>
                                                            </div>

                                                        </div>
                                                    </form>

                                                </div>
                                            </div>

                                            <!-- total calculation -->
                                            <div class="card mt-5">
                                                <div class="row">
                                                    <div class="col text-center">
                                                        <h6 class="h6"><strong><?= labels('total', 'Total') ?></strong></h6>
                                                        <h4 class="text-info h6 m-1 px-2" id="modal_sub_total" data-currency=""></h4>
                                                        <!-- <input type="visible" name="total" id="total"> -->
                                                    </div>
                                                    <div class="col text-center">
                                                        <h6 class="h6"><strong><?= labels('sell_total', 'Sell Total') ?></strong></h6>
                                                        <h4 class="text-black h6 m-1 px-2" id="modal_sell_total" data-currency=""></h4>
                                                        <!-- <input type="visible" name="total" id="sell_total"> -->
                                                    </div>
                                                    <div class="col text-center">
                                                        <h6 class="h6"><strong><?= labels('estimated_profit', 'Profit') ?></strong></h6>
                                                        <h4 class="text-success h6 m-1 px-2" id="modal_profit_total" data-currency=""></h4>
                                                        <!-- <input type="visible" name="total" id="profit_total"> -->
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                        <div class="row">
                                            <h4 class="mb-4">Return Order Item</h4>
                                        </div>
                                        <form id="batch_return_form">
                                            <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                                            <input type="hidden" name="return_data" id="return_data">

                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="return_quantity"><label><?= labels("return_quantity", "Return Quantity") ?></label></div>
                                                <div class="col-8">
                                                    <input type="number" name="return_quantity" id="return_quantity" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="return_price"><label><?= labels("return_price", "Return Price") ?></label></div>
                                                <div class="col-8">
                                                    <input type="number" name="return_price" id="return_price" class="form-control">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="sell_price"><label><?= labels("return_reason", "Return Reason") ?></label></div>
                                                <div class="col-8 form-group">
                                                    <textarea class="form-control" name="return_reason" id="return_reason"></textarea>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="return_date"><?= labels("return_date", "Return Date") ?></label></div>
                                                <div class="col-8 text-left">
                                                    <input type="text" name="return_date" id="return_date" class="form-control" placeholder="Select Return date" autocomplete="off" required>
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="sell_price"><label><?= labels("return_total", "Return Total") ?></label></div>
                                                <div class="col-8">
                                                    <p class="" id="return_total"><b></b></p>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-4 text-left"><label for="sell_price"></div>
                                                <div class="col-8">
                                                    <button type="button" class="btn btn-danger" id="return-btn">Return</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<!-- define values -->
<script>
    const old_data = [];
    $(document).ready(function() {

        flatpickr("#expire", {
            dateFormat: "Y-m-d",
            minDate: "today", // disable past dates
            allowInput: true,
        });
        flatpickr("#return_date", {
            dateFormat: "Y-m-d",
            defaultDate: new Date(), // sets to today's date
            allowInput: true
        });

        $('#status-select').on('change', function() {

            let form = $('#status-form')[0]; // Get the actual form element
            let formData = new FormData(form);
            formData.append('csrf_update_status', $('input[name="csrf_update_status"]').val());

            $.ajax({
                url: $(form).attr('action'),
                type: 'POST',
                data: formData,
                dataType: 'json',
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        $('#form_batches_items').bootstrapTable('refresh');
                        showToastMessage(response.message, 'success');
                        if (response.csrf_token) {
                            $('input[name="csrf_update_status"]').val(response.csrf_token);
                        }
                    } else {
                        showToastMessage(response.message, 'error');
                    }
                },
                error: function() {
                    showToastMessage('An error occurred. Please try again.', 'error');
                }
            });
        });
    });


    const batchEditModal = document.getElementById('batch_edit_modal');

    batchEditModal.addEventListener('show.bs.modal', event => {
        var button = event.relatedTarget; // Button that triggered the modal
        var rowData = button.getAttribute('data-row');
        if (!rowData) return;

        var data = JSON.parse(rowData);
        old_data.push(data);
        const global_cost_price = data.cost_price;
        const global_quantity = data.quantity;

        //image
        var img = batchEditModal.querySelector('#modalCardImage');
        img.src = data.image_url;
        img.alt = data.product_name || 'Product Image';

        batchEditModal.querySelector('#product_display').textContent = (data.product_name + " - " + data.variant_name) || 'Product Title';
        batchEditModal.querySelector('#batch_number_display').textContent = (data.batch_number) || 'Batch Number';
        batchEditModal.querySelector('select[name="status"]').value = data.status_id;

        batchEditModal.querySelector('input[name="order_id"]').value = data.purchase_item_id;

        batchEditModal.querySelector('input[name="cost_price"]').value = data.cost_price;
        batchEditModal.querySelector('input[name="sell_price"]').value = data.sell_price;
        batchEditModal.querySelector('input[name="quantity"]').value = data.quantity;
        batchEditModal.querySelector('input[name="discount"]').value = data.discount;

        batchEditModal.querySelector('input[name="item_id"]').value = data.purchase_item_id;
        batchEditModal.querySelector('input[name="save_batch_id"]').value = data.id;

        batchEditModal.querySelector('input[name="save_product_variant_id"]').value = data.product_variant_id;

        document.getElementById('return_data').value = JSON.stringify(data);

        // expire
        const expireInput = document.querySelector('input[name="expire"]');
        const expireFlatpickr = flatpickr(expireInput, {
            dateFormat: "Y-m-d",
            minDate: "today",
            allowInput: true,
        });
        expireFlatpickr.setDate(data.expiration_date, true); // true triggers change event



        updateTotals();

    });
</script>

<!-- update and delete batch  -->
<script>
    $(document).ready(function() {

        // Define validation rules once when the page is ready
        $('#batch_return_form').validate({
            rules: {
                return_quantity: {
                    required: true,
                    number: true,
                    min: 1
                },
                return_price: {
                    required: true,
                    number: true,
                    min: 0
                },
                return_date: {
                    required: true
                }
            },
            messages: {
                return_quantity: {
                    required: "Please enter quantity",
                    number: "Must be a number",
                    min: "Quantity must be at least 1"
                },
                return_price: {
                    required: "Please enter price",
                    number: "Must be a number",
                    min: "Price must be at least 0"
                },
                return_date: {
                    required: "Return date is required"
                }
            },
            errorElement: 'div',
            errorClass: 'text-danger small',
            highlight: function(element) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid');
            }
        });

        // Handle Return button click
        $(document).on('click', '#return-btn', function() {
            if (!$('#batch_return_form').valid()) {
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to return this item",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Return!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.value == true) {
                    const return_batch_id = $('#return_batch_id').val();
                    const return_price = $('#return_price').val();
                    const return_quantity = $('#return_quantity').val();
                    if (parseFloat(old_data[0]['cost_price']) < return_price) {
                        showToastMessage("Return price can't be greater than cost price", 'error');
                        return;
                    }
                    if (parseFloat(old_data[0]['quantity']) < return_quantity) {
                        showToastMessage("Return quantity can't be greater than item quantity", 'error');
                        return;
                    }

                    console.log(old_data[0]['cost_price']);

                    const form = $('#batch_return_form')[0];
                    const formData = new FormData(form);
                    formData.append('action', 'return');

                    $.ajax({
                        url: '<?= base_url("admin/batches/return_batch") ?>',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToastMessage(response.message, 'success');
                                setTimeout(() => {
                                    location.reload();
                                }, 1000);
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
                            showToastMessage('Something went wrong!', 'error');
                        }
                    });
                } else {
                    showToastMessage('Action cancelled.', 'error');
                }
            });
        });



        $('#save-btn').on('click', function() {

            $('#batch-form').validate({
                rules: {
                    quantity: {
                        required: true,
                        number: true,
                        min: 1
                    },
                    cost_price: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    sell_price: {
                        required: true,
                        number: true,
                        min: 0
                    },
                    discount: {
                        required: true,
                        number: true,
                        min: 0,
                    }
                },
                messages: {
                    quantity: {
                        required: "Please enter quantity",
                        number: "Quantity must be a number",
                        min: "Quantity must be at least 1"
                    },
                    cost_price: {
                        required: "Please enter cost price",
                        number: "Must be a number",
                        min: "Must be 0 or higher"
                    },
                    sell_price: {
                        required: "Please enter sell price",
                        number: "Must be a number",
                        min: "Must be 0 or higher"
                    },
                    discount: {
                        required: "Please enter discount",
                        number: "Must be a number",
                        min: "At least 0",
                    }
                },
                errorElement: 'div',
                errorClass: 'text-danger small',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                }
            });

            // Save button click handler with validation check
            $(document).on('click', '#save-btn', function() {
                if (!$('#batch-form').valid()) {
                    return;
                }
                Swal.fire({
                    title: 'Are you sure?',
                    text: "",
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonColor: '#5cb85c',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, Save!',
                    cancelButtonText: 'No, cancel!'
                }).then((result) => {
                    if (result.value == true) {
                        const form = $('#batch-form')[0];
                        const formData = new FormData(form);
                        formData.append('action', 'save');
                        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
                        $.ajax({
                            url: '<?= base_url("admin/batches/save_batch") ?>',
                            type: 'POST',
                            data: formData,
                            contentType: false,
                            processData: false,
                            dataType: 'json',
                            success: function(response) {
                                if (response.success) {
                                    showToastMessage(response.message, 'success');
                                    location.reload();
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
                                showToastMessage('Something went wrong!', 'error');
                            }
                        });
                    } else {
                        showToastMessage('Action cancelled.', 'error');
                    }
                });
            });
        });

        $(document).on('click', '#delete-btn', function() {
            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to delete a purchased item!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, Delete!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.value == true) {
                    const itemId = $('#item_id').val();

                    if (!itemId) {
                        showToastMessage("No item selected to delete", 'error');
                        return 0;
                    }
                    const form = $('#batch-form')[0];
                    const formData = new FormData(form);
                    formData.append('action', 'delete');
                    formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                    $.ajax({

                        url: '<?= base_url("admin/batches/delete_batch") ?>',
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToastMessage(response.message, 'success');
                                location.reload();
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
    });
</script>

<!-- Profit Calculation script -->
<script>
    function updateTotals() {
        const quantity = parseFloat(document.querySelector('input[name="quantity"]').value) || 0;
        const costPrice = parseFloat(document.querySelector('input[name="cost_price"]').value) || 0;
        const sellPrice = parseFloat(document.querySelector('input[name="sell_price"]').value) || 0;
        const discount = parseFloat(document.querySelector('input[name="discount"]').value) || 0;
        const total = (quantity * costPrice) - discount;
        const sellTotal = (quantity * sellPrice);
        const profit = sellTotal - total;

        const currency = ""; // e.g., '$', '₹', etc.

        document.getElementById('modal_sub_total').textContent = currency + ' ' + total.toFixed(2);
        document.getElementById('modal_sell_total').textContent = currency + ' ' + sellTotal.toFixed(2);
        document.getElementById('modal_profit_total').textContent = currency + ' ' + profit.toFixed(2);
    }

    // Trigger recalculation on input changes
    ['quantity', 'cost_price', 'sell_price', 'discount'].forEach(name => {
        document.querySelector(`input[name="${name}"]`).addEventListener('input', updateTotals);
    });
</script>

<!-- Return Total Calculation -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const quantityInput = document.querySelector('input[name="return_quantity"]');
        const priceInput = document.querySelector('input[name="return_price"]');
        const totalDisplay = document.getElementById('return_total');

        function updateReturnTotal() {
            const quantity = parseFloat(quantityInput.value) || 0;
            const price = parseFloat(priceInput.value) || 0;
            const total = quantity * price;
            totalDisplay.innerHTML = `<b>${total.toFixed(2)}</b>`;
        }

        quantityInput.addEventListener('input', updateReturnTotal);
        priceInput.addEventListener('input', updateReturnTotal);
    });
</script>