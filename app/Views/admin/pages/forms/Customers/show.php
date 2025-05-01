<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('customer_detail', 'Customer Details') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/customers'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('customers', 'Customers') ?></a>
                </div>
            </div>
        </div>


        <div class="section-body">

            <div class="card">
                <div class="card-body">
                    <div class="row mt-sm-4">
                        <div class='col-md-12'>
                            <h2 class="section-title"> <?= labels($from_title, 'Customer Details') ?> </h2>

                            <form action="<?= base_url('admin/customers/' . $customer['user_id']) ?>" id="form_customer" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
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
            </div>
        </div>
    </section>
    <div class="section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
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
                                    <button class="btn btn-danger btn-small p-2 mb-1  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
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
                                        <th data-radio="true"></th>
                                        <th data-field="id" style="width: 50px;" data-sortable="true"><?= labels('order_id', 'Order ID') ?></th>
                                        <th data-field="order_type" data-sortable="true"><?= labels('order_type', 'Order Type') ?></th>
                                        <th data-field="order_date" data-sortable="true"><?= labels('order_date', 'Order Date') ?></th>
                                        <th data-field="discount" data-sortable="true"><?= labels('discount', 'discount') ?></th>
                                        <th data-field="final_total" data-sortable="true"><?= labels('final_total', 'Final Total') ?></th>
                                        <th data-field="amount_paid" data-sortable="true"><?= labels('amount_paid', 'Amount Paid') ?></th>
                                        <th data-field="returns_amount" data-sortable="true"><?= labels('returns_amount', 'Returns Amount') ?></th>
                                        <th data-field="debt" data-sortable="true"><?= labels('debt', 'Debt') ?></th>
                                        <th data-field="payment_status" data-sortable="true"><?= labels('status', 'Payment Status') ?></th>
                                        <!--<th data-field="debit" data-sortable="true"><?= labels('debit', 'Debit') ?></th>
                                        <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                                        <th data-field="actions" data-sortable="true"><?= labels('action', 'Actions') ?></th> -->
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </section>
</div>


<script>
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
    // jquery validation for name and ajax submission
    $(document).ready(function() {
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
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').find('.invalid-feedback').html(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            // using ajax to handle the form
            submitHandler: function(form) {
                // Check if switch is checked
                if (!$('#status').is(':checked')) {
                    // Add or update hidden input
                    let statusInput = $('input[name="status"]');
                    if (statusInput.length) {
                        statusInput.val(0);
                    } else {
                        $('<input>').attr({
                            type: 'hidden',
                            name: 'status',
                            value: 0
                        }).appendTo(form);
                    }
                }

                let formData = new FormData(form);

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            // $('#responseMessage').html('<div class="alert alert-success">' + response.message + '</div>');
                            showToastMessage(response.message, 'success');
                        } else {
                            showToastMessage(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToastMessage(response.message, 'error');
                    }
                });
            }
        });
    });


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
    }, 5000); // 3 seconds delay before start fading

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

    // validate balance
    function restrictToNumbers(inputSelector, toastMessage = 'Only numbers are allowed!') {
        document.querySelector(inputSelector).addEventListener('keydown', function(e) {
            // Allow: Backspace, Delete, Tab, Escape, Enter, '.', and arrow keys
            if (
                [46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                (e.keyCode >= 35 && e.keyCode <= 40) // Home, End, arrows
            ) {
                return; // Allow these keys
            }

            // Allow: Ctrl/Cmd+A, Ctrl/Cmd+C, Ctrl/Cmd+V, Ctrl/Cmd+X
            if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].indexOf(e.keyCode) !== -1) {
                return;
            }

            // Only allow numbers (0-9)
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                showToastMessage(toastMessage, 'error');
            }
        });
    }

    restrictToNumbers('input[name="balance"]', 'Only numbers are allowed for Balance!');
    restrictToNumbers('input[name="debt"]', 'Only numbers are allowed for Debt!');
</script>