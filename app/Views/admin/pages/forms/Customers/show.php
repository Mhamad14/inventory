<div class="main-content">
    <!-- section title and Back button ..Strart -->
    <section class="section">

        <div class="section-header mt-2">
            <h1><?= labels('customer_detail', 'Customer Details') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/customers'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('customers', 'Customers') ?></a>
                </div>
            </div>
        </div>
    </section>
    <!-- section title and Back button ..End -->

    <!-- section Customer Details ..start -->
    <div class="card mt-5">
        <div class="card-header mt-2">
            <h6 class="" style="cursor: pointer;" data-toggle="collapse" data-target="#customerDetailsBody" aria-expanded="false" aria-controls="customerDetailsBody">
                <span id="toggleIcon">▶</span> <?= labels('customer_details', 'Customer Details') ?>
            </h6>
        </div>

        <div id="customerDetailsBody" class="collapse">
            <div class="card-body">
                <form action="<?= base_url('admin/customers/' . $customer['user_id']) ?>" id="form_customer" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                    <?= csrf_field("csrf_form_customer") ?> <!-- CSRF Token -->

                    <input type="hidden" name="_method" value="PUT">
                    <div class="card-footer">
                        <div class="row">
                            <input type="hidden" name="id" id="id" value="">
                        </div>
                        <div class="row">
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" placeholder="Enter Customer Name" class="form-control" name="name" id="name" value="<?= !empty($customer) && !empty($customer['first_name']) ? $customer['first_name'] : "" ?>">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="identity"><?= labels('mobile_number', 'Mobile') ?> <small>(<?= labels('identity', 'Identity') ?>)</small></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="mobile" placeholder="Enter Mobile Number" name="mobile" value="<?= !empty($customer) && !empty($customer['mobile']) ? $customer['mobile'] : "" ?>">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class=" col-md">
                                <div class="form-group">
                                    <label for="password"><?= labels('password', 'Password') ?> <small>(<?= labels('password_delivery_boy_text', 'Enter new password if you want to update current password') ?>)</small></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="password" value="" placeholder="Enter Password" name="password">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                            <div class="col-md">
                                <div class="form-group">
                                    <label for="email"><?= labels('email', 'Email') ?></label><span class="asterisk text-danger"> *</span>
                                    <input type="text" class="form-control" id="email" placeholder="abc@gmail.com" name="email" value="<?= !empty($customer) && !empty($customer['email']) ? $customer['email'] : "" ?>">
                                    <div class="invalid-feedback"></div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="status" class="custom-switch p-0">
                                <input type="checkbox" name="status" id="status" class="custom-switch-input"
                                    value="1" <?= !empty($customer) && !empty($customer['status']) ? 'checked' : '' ?>>
                                <span class="custom-switch-indicator"></span>
                                <span class="custom-switch-description"><?= labels('status', 'Status') ?></span>
                            </label>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Save</button>&nbsp;
                    <button type="reset" class="btn btn-info">Reset</button>
                </form>
            </div>
        </div>

    </div>
    <!-- section Customer Details ..End -->

    <!-- section: Customer Orders ..Start-->
    <div class="card mt-3">
        <div class="card-header">
            <h6 class="" style="cursor: pointer;"
                data-toggle="collapse"
                data-target="#ordersSectionBody"
                aria-expanded="false"
                aria-controls="ordersSectionBody">
                <span id="ordersToggleIcon">▶</span>
                <?= labels('customer_orders', "( " . $customer['first_name'] . " )" . ' Orders') ?>
            </h6>
        </div>
        <div id="ordersSectionBody" class="collapse hide">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="customer_orders_type_filter"><?= labels('filter_orders', 'Filter Orders') ?></label>
                            <select name="customer_orders_type_filter" id="customer_orders_type_filter" class="form-control selectric">
                                <option value="">-Select-</option>
                                <option value="product">Products</option>
                                <option value="service">Services</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="date_range"><?= labels('date_range_filter', 'Date Range') ?></label>
                            <input type="text" name="date_range" id="date_range" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-primary btn-small p-2  m-lg-4 mt-5 py-2" name="clear" id="clear"> Clear </button>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="payment_status_filter"><?= labels('filter_by_payment_status', 'Filter by Payment Status') ?></label>
                            <select name="payment_status_filter" class="form-control selectric" id="payment_status_filter">
                                <option value="">All</option>
                                <option value="fully_paid">Fully Paid</option>
                                <option value="partially_paid">Partially Paid</option>
                                <option value="unpaid">Unpaid</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for=""><?= labels('apply_filters', 'Apply filters') ?></label>
                            <button class="btn btn-primary d-block" id="filter">
                                <?= labels('apply', 'Apply') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "customer-Orders-list","ignoreColumn": ["action"]}' id="form_customers_order" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200]" data-url="<?= base_url('admin/customers/customer_orders_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="customer_orders_query">
                    <thead>
                        <tr>
                            <th data-field="id" style="width: 50px;" data-sortable="true"><?= labels('order_id', 'Order ID') ?></th>
                            <th data-field="order_type" data-sortable="true"><?= labels('order_type', 'Order Type') ?></th>
                            <th data-field="order_date" data-sortable="true"><?= labels('order_date', 'Order Date') ?></th>
                            <th data-field="discount" data-sortable="true"><?= labels('discount', 'discount') ?></th>
                            <th data-field="final_total" data-sortable="true"><?= labels('final_total', 'Final Total') ?></th>
                            <th data-field="amount_paid" data-sortable="true"><?= labels('amount_paid', 'Amount Paid') ?></th>
                            <th data-field="returns_amount" data-sortable="true"><?= labels('returns_amount', 'Returns Amount') ?></th>
                            <th data-field="debt" data-sortable="true"><?= labels('debt', 'Debt') ?></th>
                            <th data-field="payment_status" data-sortable="true"><?= labels('status', 'Payment Status') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <!-- section: Customer Orders ..End-->

    <!-- section: Overall Payments  ..Start-->
    <div class="card mt-3">

        <div class="card-header mt-2">
            <h6 class="" style="cursor: pointer;"
                data-toggle="collapse"
                data-target="#overallPaymentsSectionBody"
                aria-expanded="false">
                <span id="overallPaymentsToggleIcon">▶</span>
                <?= labels('overall_payments', 'Overall Payments') ?>
            </h6>
        </div>

        <div id="overallPaymentsSectionBody" class="collapse show">
            <div class="card-body">
                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Subtotal</label>
                            <div class="col-sm-7">
                                <!-- currency_location(decimal_points($customer['balance'])), -->
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($overallPayments['sub_total'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Discount</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-danger">- <?= currency_location(decimal_points($overallPayments['discount'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Delivery Charges</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-success">+ <?= currency_location(decimal_points($overallPayments['delivery_charges'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label font-weight-bold">Final Total</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext font-weight-bold"><?= currency_location(decimal_points($overallPayments['final_total'])) ?? '-' ?></p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">

                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Amount Paid</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($overallPayments['amount_paid'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Debt</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext"><?= currency_location(decimal_points($overallPayments['debt'])) ?? 'not defined' ?></p>
                                <?php if (!empty($overallPayments['debt']) && $overallPayments['debt'] != 0 && $overallPayments['debt'] != "not defined"): ?>
                                    <form action="<?= base_url('admin/customers/' . $customer['user_id']) . '/payback_all_debt' ?>" id="form_payback_all_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                        <?= csrf_field("csrf_pacsrf_payback_all_debtybacl_all_debt") ?> <!-- CSRF Token -->
                                        <input type="hidden" name="_method" value="PUT">
                                        <button class="btn btn-success" type="submit"><?= labels('payback', 'Pay back All Debt') ?></button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="form-group row mb-2">
                            <label class="col-sm-5 col-form-label">Returns</label>
                            <div class="col-sm-7">
                                <p class="form-control-plaintext text-danger">- <?= currency_location(decimal_points($overallPayments['returns_total'])) ?? 'not defined' ?></p>
                            </div>
                        </div>
                    </div>
                    <!-- righ column .END -->
                </div>
                <div class="row">
                    <?php if (!empty($overallPayments['debt']) && $overallPayments['debt'] != 0 && $overallPayments['debt'] != "not defined"): ?>
                        <form action="<?= base_url('admin/customers/payback_partial_debt') ?>" id="form_payback_partial_debt" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                            <?= csrf_field("csrf_payback_partial_debt") ?> <!-- CSRF Token -->
                            <input type="hidden" name="_method" value="PUT">

                            <div class="form-row align-items-center">

                                <div class="col-8"><input class="form-control" name="partial_amount" type="text" placeholder="Enter amount" /></div>
                                <div class="col-4"><button class="btn btn-primary form-control" type="submit"><?= labels('partial_payback', 'Pay back') ?></button></div>
                            </div>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        </div>
    </div>
    <!-- section: Overall Payments  ..End-->

</div>


<script>
    // togle Customer Order section


    // filter orders list
    var start_date = "";
    var end_date = "";
    var payment_status_filter = "";
    var customer_orders_type_filter = "";
    $("#payment_status_filter").on("change", function() {
        payment_status_filter = $(this).find("option:selected").val();
    });

    $("#customer_orders_type_filter").on("change", function() {
        customer_orders_type_filter = $(this).find("option:selected").val();
    });
    $(function() {
        $('input[name="date_range"]').daterangepicker({
                opens: "left",
            },
            function(start, end) {
                start_date = start.format("YYYY-MM-DD");
                end_date = end.format("YYYY-MM-DD");
            }
        );
    });
    $("#date_range").on("change", function() {});

    function customer_orders_query(p) {
        return {
            search: p.search,
            limit: p.limit,
            sort: p.sort,
            order: p.order,
            offset: p.offset,
            start_date: start_date,
            end_date: end_date,
            payment_status_filter: payment_status_filter,
            customer_orders_type_filter: customer_orders_type_filter,
        };
    }

    $("#filter").on("click", function(e) {
        $("#form_customers_order").bootstrapTable("refresh");
    });

    $(document).ready(function() {

        // Payback partial debt form
        $("#form_payback_partial_debt").on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            e.stopImmediatePropagation(); // This will stop the event from propagating

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to pay back Enterd Amount debt!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, pay back!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.value == true) {

                    let form = this;

                    // Using AJAX to handle the form
                    let formData = new FormData(form);
                    formData.append('csrf_payback_partial_debt', $('input[name="csrf_payback_partial_debt"]').val());

                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToastMessage(response.message, 'success');
                                $('input[name="csrf_payback_partial_debt"]').val(response.csrf_token);
                                location.reload(); // This will reload the page after the successful request
                                $(form)[0].reset();
                            } else {
                                showToastMessage(response.message, 'error');
                            }
                        },
                        error: function() {
                            showToastMessage('An error occurred during the request.', 'error');
                        }
                    });
                } else {
                    console.log(result);
                    showToastMessage('Action cancelled.', 'error');
                }

            });
            return false; // Stop further propagation and form submission

        });


        // Payback all debt form
        $("#form_payback_all_debt").on('submit', function(e) {
            e.preventDefault(); // Prevent default form submission
            e.stopImmediatePropagation(); // This will stop the event from propagating

            Swal.fire({
                title: 'Are you sure?',
                text: "You are about to pay back all debt!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#5cb85c',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, pay back!',
                cancelButtonText: 'No, cancel!'
            }).then((result) => {
                if (result.value == true) {
                    console.log("entered true");
                    let form = this;

                    // Using AJAX to handle the form
                    let formData = new FormData(form);
                    formData.append('csrf_payback_all_debt', $('input[name="csrf_payback_all_debt"]').val());

                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                showToastMessage(response.message, 'success');
                                $('input[name="csrf_payback_all_debt"]').val(response.csrf_token);
                                location.reload(); // This will reload the page after the successful request
                            } else {
                                showToastMessage(response.message, 'error');
                            }
                        },
                        error: function() {
                            showToastMessage('An error occurred during the request.', 'error');
                        }
                    });
                } else {
                    console.log(result);
                    showToastMessage('Action cancelled.', 'error');
                }

            });
            return false; // Stop further propagation and form submission

        });

        // Other form handlers, for example for the customer form
        $("#form_customer").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 3
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true,
                    digits: true,
                    minlength: 7,
                    maxlength: 20
                },
                password: {
                    minlength: 8
                }
            },
            messages: {
                name: {
                    required: "Name is required",
                    minlength: "Name must be at least 3 characters"
                },
                email: {
                    required: "Email is required",
                    email: "Please enter a valid email address"
                },
                mobile: {
                    required: "Mobile number is required",
                    digits: "Please enter only digits",
                    minlength: "Mobile number must be at least 7 digits",
                    maxlength: "Mobile number must not exceed 20 digits"
                },
                password: {
                    minlength: "Password must be at least 8 characters"
                },
            },

            highlight: function(element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");
                error.insertAfter(element);
            },


            submitHandler: function(form) {
                // Handle form submission for customer form here
                let formData = new FormData(form);
                formData.append('csrf_form_customer', $('input[name="csrf_form_customer"]').val());

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            showToastMessage(response.message, 'success');
                            $('input[name="csrf_form_customer"]').val(response.csrf_token);
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
    toggleSection('#ordersSectionBody', '#ordersToggleIcon');

    // togle Customer Details
    toggleSection('#customerDetailsBody', '#toggleIcon');

    toggleSection('#overallPaymentsSectionBody', '#overallPaymentsToggleIcon');


    // restrict mobile number to enter text
    restrictToNumbers('input[name="mobile"]', 'Only numbers are allowed for Mobile!');

    // restrict partial_amount to only numbers
    restrictToNumbers('input[name="partial_amount"]', 'Only numbers are allowed for Amount!');
    // hide error alert after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-danger');
        alerts.forEach(function(alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500); // wait for fade out
        });
    }, 5000);

    // hide success alert after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-success');
        alerts.forEach(function(alert) {
            alert.classList.remove('show');
            alert.classList.add('fade');
            setTimeout(() => {
                alert.remove();
            }, 500); // wait for fade out
        });
    }, 5000);

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