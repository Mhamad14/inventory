<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('products', 'Products') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/products'); ?>" class="btn"><i class="fas fa-list"></i> <?= labels('products', 'Products') ?></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md">
                    <div class="alert alert-danger d-none" id="add_subscription_result"> </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-danger"><?= session("message"); ?></label></div>

            <?php } ?>
            <?php
            $variant_unit_name = !empty($variant_unit_name) ? $variant_unit_name : "";
            ?>
            <div class="card">
                <div class="card-body p-0">
                    <div class="row mt-sm-4">
                        <div class='col-md-12'>
                            <h2 class="section-title"> <?= labels($from_title) ?> </h2>

                            <!-- Save products form -->
                            <form action="<?= base_url('admin/products/save_products') ?>" id="product_form" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <?= csrf_field("csrf_test_name") ?> <!-- CSRF Token -->

                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name"><?= labels('product_name', 'Product Name') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="name" id="name" value="<?= !empty($products) && !empty($products['name']) ? $products['name'] : "" ?>">
                                                <textarea class="d-none" id="units"><?= json_encode($units) ?> </textarea>
                                                <textarea class="d-none" id="variants1"> </textarea>
                                                <textarea class="d-none" id="variant_unit_name"><?= json_encode($variant_unit_name) ?> </textarea>
                                                <input type="hidden" name="product_id" id="product_id" value="<?= !empty($products) && !empty($products['id']) ? $products['id'] : "" ?>">
                                                <!-- <input type="hidden" id="products_tax_value" value='<?= "" //isset($products_tax_value) ? $products_tax_value : '' 
                                                                                                            ?>'> -->
                                            </div>
                                        </div>

                                        <!-- Category -->
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="category_id"><?= labels('category', 'Category') ?></label><span class="asterisk text-danger"> *</span>
                                                <select class="form-control" name="category_id" id="category_id">
                                                    <option value="<?= $products['category_id'] ?? '1' ?>">
                                                        <?= $category_name ?? 'General' ?>
                                                    </option>
                                                    <?php foreach ($categories as $category): ?>
                                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Brand -->
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="brand_id"><?= labels('brand', 'Brand') ?></label>
                                                <select class="form-control" name="brand_id" id="brand_id">
                                                    <option value="<?= $products['brand_id'] ?? '1' ?>">
                                                        <?= $brand_name ?? 'Please Select' ?>
                                                    </option>
                                                    <?php foreach ($brands as $brand): ?>
                                                        <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>


                                        <!-- Description -->
                                        <div class="row">
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="description"><?= labels('description', 'Description') ?></label><span class="asterisk text-danger"> *</span>
                                                    <textarea name="description" class="form-control" name="description" id="description"><?= !empty($products) && !empty($products['description']) ? $products['description'] : "" ?></textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- image and product type-->
                                        <div class="row">
                                            <!-- Image upload + live preview -->
                                            <div class="col-md">
                                                <label for="image"><?= labels('image', 'Image') ?></label>
                                                <input type="file" class="form-control" name="image" id="image">
                                                <input type="hidden" name="old_image" value="<?= !empty($products['image']) ? $products['image'] : '' ?>">

                                                <!-- Live preview container -->
                                                <div id="image-preview" class="mt-2">
                                                    <img id="preview-img" class="settings_logo" src="<?= !empty($products['image']) ? base_url($products['image']) : '' ?>" alt="Image Preview" style="max-height: 150px;">
                                                </div>
                                            </div>
                                            <!-- Existing image preview -->
                                            <div class="col-md-3">
                                                <div class="card-body">
                                                    <?php if (!empty($products['image'])): ?>
                                                        <a href="<?= base_url($products['image']) ?>" data-lightbox="product-image">
                                                            <img style="max-height: 250px;" class="product-image-dimensions" id="existing-image" src="<?= base_url($products['image']) ?>" alt="Product Image">
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>


                                        <div class="d-flex justify-content-between mt-4">
                                            <div class="">
                                                <h2 class="section-title"> <?= labels('product_variants', 'Product Variants') ?> </h2>
                                            </div>
                                            <div class="custom-col add_btn_action">
                                                <button class="btn btn-icon btn-primary" id="add_variant" type="button"><i class="fas fa-plus"></i> <?= labels('add_variant', 'Add Variant') ?></button>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="container-fluid">
                                                <input type="hidden" id="mutli_lang_remove_warehouse" value="<?= labels('remove_warehouse', 'Remove warehouse') ?>">

                                                <br>
                                                <div id="existing_variants">
                                                    <?php if (isset($variants)) {
                                                        $count = 1;
                                                        foreach ($variants as $variant) {
                                                    ?>
                                                            <!-- Edit section -->
                                                            <div class="variant-item py-1 mb-3 border-top border-2">

                                                                <!-- Variant count and deltete button -->
                                                                <div class="d-flex justify-content-between my-1">
                                                                    <div>
                                                                        <p class="text-black font-weight-bolder"> Variant <?= $count ?> </p>
                                                                    </div>
                                                                    <?php if ($count > 1): ?>
                                                                        <div class="d-flex gap-3">
                                                                            <div>
                                                                                <button class="btn btn-icon btn-danger  remove_variant" data-variant_id="<?= $variant['id'] ?>" name="remove_variant"
                                                                                    data-toggle="tooltip" data-placement="top" title="<?= labels('remove_variant', 'Remove variant') ?>"> <i class="fas fa-trash"></i>
                                                                                    <span class="d-none d-md-inline"> <?= labels('remove_variant', 'Remove variant') ?> </span>
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    <?php endif; ?>
                                                                </div>

                                                                <!-- Variant details in edit mode-->
                                                                <div class="row mb-3">
                                                                    <div class="col ">
                                                                        <label id=""> <?= labels('variant_name', 'Variant Name') ?><span class="asterisk text-danger"> *</span> </label>
                                                                        <input type="hidden" name="variant_id[]" id="variant_id" value="<?= $variant['id'] ?>">
                                                                        <input type="text" class="form-control" id="variant_name" name="variant_name[]" value="<?= $variant['variant_name'] ?>" placeholder="Ex. 1 kg..">
                                                                    </div>
                                                                    <div class="col ">
                                                                        <label id=""> <?= labels('variant_barcode', 'Variant Barcode') ?> </label>
                                                                        <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]" value="<?= isset($variant['barcode']) && !empty($variant['barcode']) ? $variant['barcode'] : ""  ?>" placeholder="Enter Barcode , Ex : 9875855">
                                                                    </div>

                                                                    <div class="col ">
                                                                        <label for=""><?= labels('unit', 'Unit') ?><span class="asterisk text-danger"> *</span> </label>
                                                                        <select class="form-control" id="unit_id" name="unit_id[]">
                                                                            <option value=""> -<?= labels('select_unit', 'Select Unit') ?>-</option>
                                                                            <?php
                                                                            foreach ($units as $unit) { ?>
                                                                                <option value="<?= $unit['id'] ?>" <?= $variant['unit_id'] == $unit['id'] ? "selected" : '' ?>><?= $unit['name'] ?></option>
                                                                            <?php } ?>
                                                                        </select>
                                                                    </div>

                                                                    <div class="col">
                                                                        <label for=""><?= labels('min_stock', 'Minimum stock') ?><span class="asterisk text-danger"> *</span></label>
                                                                        <input type="number" class="form-control" id="qty_alert" name="qty_alert[]" value="<?= $variant['qty_alert'] ?>" min="0.00" placeholder="0.00">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                </div>
                                            <?php
                                                            $count++;
                                                        }
                                                    } else {
                                                        $insert_count = 0; ?>
                                            <!-- Insert part -->
                                            <div class="variant-item py-1 mb-3 border-top border-2">

                                                <!-- Variant 1 and remove variant -->
                                                <div class="d-flex justify-content-between my-1">
                                                    <div>
                                                        <p class="text-black font-weight-bolder"> Variant <?= "1" ?></p>
                                                    </div>
                                                </div>

                                                <div class="row mb-3">
                                                    <div class="col">

                                                        <label> <?= labels('variant_name', 'Variant Name') ?><span class="asterisk text-danger"> *</span> </label>

                                                        <input type="text" class="form-control" id="variant_name" name="variant_name[]" placeholder="Ex. 1 kg..">
                                                    </div>
                                                    <div class="col">
                                                        <label id=""> <?= labels('variant_barcode', 'Variant Barcode') ?> </label>
                                                        <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]" value="" placeholder="Enter Barcode , Ex : 9875855">
                                                    </div>

                                                    <div class="col">
                                                        <label for=""><?= labels('unit', 'Unit') ?><span class="asterisk text-danger"> *</span> </label>
                                                        <select class="form-control" id="unit_id" name="unit_id[]">
                                                            <option value=""> -<?= labels('select_unit', 'Select Unit') ?>-</option>
                                                            <?php
                                                            foreach ($units as $unit) { ?>
                                                                <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                                                            <?php } ?>
                                                        </select>
                                                    </div>
                                                    <div class="col">
                                                        <label for=""><?= labels('min_stock', 'Minimum stock') ?><span class="asterisk text-danger"> *</span></label>
                                                        <input type="number" class="form-control" id="qty_alert" name="qty_alert[]" min="0.00" placeholder="0.00">
                                                    </div>

                                                </div>
                                            </div>
                                        <?php $insert_count++;
                                                    } ?>

                                        <!-- Variants data -->
                                        <div id="variant">
                                        </div>

                                        <div>
                                            <div class="form-group">
                                                <label for="status" class="custom-switch p-0">
                                                    <?php if (!empty($products['status']) && $products['status'] == "1") { ?>
                                                        <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                                    <?php } elseif (isset($products['status']) && $products['status'] == "0") { ?>
                                                        <input type="checkbox" name="status" id="status" class="custom-switch-input">
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                                    <?php } ?>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description"><?= labels('status', 'Status') ?></span>
                                                </label>
                                            </div>
                                        </div>
                                        <div>
                                            <button type="submit" class="btn btn-primary"><?= labels('save', 'Save') ?></button>&nbsp;
                                            <button type="reset" class="btn btn-info"><?= labels('reset', 'Reset') ?></button>
                                        </div>

                                            </div>
                                        </div>
                                    </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<script>
    // Product form
    var product_type;
    $(document).ready(function() {

        $("#team_members_formss").on("submit", function(e) {
            e.preventDefault();
            let isValid = 1;
            if ($("#password_confirm").val() != $("#password").val()) {
                isValid = 0;
            }

            if (!isValid) {
                iziToast.error({
                    title: "Error!",
                    message: "Confirm password is not same as password",
                    position: "topRight",
                });
                return;
            }

            var formData = new FormData(this);
            formData.append(csrf_token, csrf_hash);

            $.ajax({
                type: "post",
                url: this.action,
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                // dataType: "json",
                success: function(result) {
                    csrf_token = result["csrf_token"];
                    csrf_hash = result["csrf_hash"];
                    var message = result.message;

                    if (result.error == true) {
                        Object.keys(result.message).map((key) => {
                            showToastMessage(result["message"][key], "error");
                        });
                    } else {
                        showToastMessage(message, "success");
                        setTimeout(function() {
                            window.location = base_url + "/admin/team_members";
                        }, 2000);
                    }
                },
            });
        });
        $("#team_members_form").on("submit", function(e) {
            e.preventDefault();
            let isValid = 1;
            if ($("#password_confirm").val() != $("#password").val()) {
                isValid = 0;
            }

            if (!isValid) {
                iziToast.error({
                    title: "Error!",
                    message: "Confirm password is not same as password",
                    position: "topRight",
                });
                return;
            }

            var formData = new FormData(this);
            formData.append(csrf_token, csrf_hash);
            $.ajax({
                type: "post",
                url: this.action,
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: "json",
                success: function(result) {
                    csrf_token = result["csrf_token"];
                    csrf_hash = result["csrf_hash"];
                    if (result.error == true) {
                        var message = "";
                        Object.keys(result.message).map((key) => {
                            showToastMessage(result["message"][key], "error");
                        });
                    } else {
                        showToastMessage(result["message"], "success");
                        setTimeout(function() {
                            window.location = base_url + "/admin/team_members";
                        }, 2000);
                    }
                },
            });
        });



        // remove-variant
        $(document).on("click", ".remove_variant", function(e) {

            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();
            var variant_id = $(this).attr("data-variant_id");
            console.log('varuant_id: ', variant_id);
            if (variant_id == null || variant_id === "") {
                $(this).parent().parent().parent().parent().remove();
                return 0;
            }

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
                        type: "get",
                        url: site_url + "/admin/products/remove_variant/" + variant_id,
                        cache: false,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        success: function(response) {
                            if (response.success) {
                                showToastMessage(response.message, 'success');
                                csrf_token = response["csrf_token"];
                                csrf_hash = response["csrf_hash"];
                            } else {
                                if (typeof response.message === 'object') {
                                    let errorMessages = Object.values(response.message).join('<br>');
                                    showToastMessage(errorMessages, 'error'); // Display all errors
                                } else {
                                    showToastMessage(response.message, 'error'); // Generic error
                                }
                            }
                        },
                    });
                    $(this).parent().parent().parent().parent().remove();

                } else {
                    showToastMessage('Action cancelled.', 'error');
                }

            });
        });



        $("#reset").on("click", function(e) {
            e.preventDefault();
        });
    });

    // add variant click
    $("#add_variant").on("click", function(e) {
        e.preventDefault();
        var units = $("#units").val();
        if (units) {
            units = JSON.parse(units);
            var options = "<option value=''>Select Unit</option>";
            $.each(units, function(i, units) {
                options +=
                    '<option value = "' +
                    units["id"] +
                    '" > ' +
                    units["name"] +
                    "</option>";
            });
        }

        var html = `
            <div class="variant-item py-1 mb-3 border-top border-2">
                <div class="d-flex justify-content-between my-1">
                    <div>
                        <p class="text-black font-weight-bolder">Variant ${
                          $(".variant-item").length + 1
                        }</p>
                    </div>
                    <div class="d-flex gap-3">
                        <div>
                            <button class="btn btn-icon btn-danger  remove_variant" 
                                    data-variant_id=""
                                    name="remove_variant"
                                    data-toggle="tooltip"
                                    data-placement="top"
                                    title="Remove variant">
                                <i class="fas fa-trash"></i>
                                <span class="d-none d-md-inline">Remove variant</span>
                            </button>
                        </div>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label>Variant Name<span class="asterisk text-danger"> *</span></label>
                        <input type="text" class="form-control" name="variant_name[]" placeholder="Ex. 1 kg..">
                    </div>
                    <div class="col">
                        <label id=""> Variant Barcode </label>
                        <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]"  placeholder="Enter Barcode , Ex : 9875855">
                    </div>
                    <div class="col">
                        <label>Unit<span class="asterisk text-danger"> *</span></label>
                        <select class="form-control" id="unit_id" name="unit_id[]">
                            ${options}
                        </select>
                    </div>
                    <div class="col">
                        <label>Minimum Stock<span class="asterisk text-danger"> *</span></label>
                        <input type="number" class="form-control" id="qty_alert" step="0.1" min="0.1" name="qty_alert[]" min="0.00" placeholder="0.00">
                    </div>
                </div>
                
            </div>`;

        $("#variant").append(html);
    });


    $(document).ready(function() {

        $("#product_form").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 2
                },
                description: {
                    required: true,
                    minlength: 3
                },
                "variant_name[]": {
                    required: true,
                    minlength: 2
                },
                "unit_id[]": {
                    required: true
                },
                "qty_alert[]": {
                    required: true,
                    min: 0
                },
                category_id: {
                    required: true
                },
                brand_id: {
                    required: true
                }
            },
            messages: {
                name: {
                    required: "Name is required",
                    minlength: "Name must be at least 2 characters"
                },
                description: {
                    required: "Description is required",
                    minlength: "Description must be at least 3 characters"
                },
                "variant_name[]": {
                    required: "Variant name is required",
                    minlength: "Variant name must be at least 2 characters"
                },
                "unit_id[]": {
                    required: "Unit is required"
                },
                "qty_alert[]": {
                    required: "Minimum stock is required",
                    min: "Minimum stock cannot be negative"
                },
                category_id: {
                    required: "Category is required"
                },
                brand_id: {
                    required: "Brand is required"
                }
            },

            highlight: function(element) {
                $(element).removeClass('is-valid').addClass('is-invalid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            },
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");

                // Place error after a parent container if it's a repeated field
                if (element.attr("name").includes("[]")) {
                    if (element.closest('.form-group').length) {
                        error.insertAfter(element.closest('.form-group'));
                    } else {
                        error.insertAfter(element);
                    }
                } else {
                    error.insertAfter(element);
                }
            },
            submitHandler: function(form) {
                console.log("SubmitHandler triggered");

                const $submitBtn = $(form).find('[type="submit"]');
                $submitBtn.prop('disabled', true); // Disable to prevent double click

                let formData = new FormData(form);
                formData.append(csrf_token, csrf_hash);

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        $submitBtn.prop('disabled', false); // Re-enable

                        if (response.success) {
                            showToastMessage(response.message, 'success');
                            csrf_token = response["csrf_token"];
                            csrf_hash = response["csrf_hash"];
                            resetFormValidation("#product_form");
                            setTimeout(function() {
                                window.location.href =  "<?= site_url('admin/products') ?>"; 
                            }, 1500);

                        } else {
                            // If `message` is an object (e.g. validation errors)
                            if (typeof response.message === 'object') {
                                let errorMessages = Object.values(response.message).join('<br>');
                                showToastMessage(errorMessages, 'error'); // Display all errors
                            } else {
                                showToastMessage(response.message, 'error'); // Generic error
                            }
                        }
                    },
                    error: function() {
                        $submitBtn.prop('disabled', false);
                        showToastMessage('An error occurred during the request.', 'error');
                    }
                });
                return false;
            }
        });
    });

    // image preview handlling
    document.getElementById('image').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('preview-img');
                preview.src = e.target.result;
            }
            reader.readAsDataURL(file);
        }
    });

    function resetFormValidation(formSelector) {
        const $form = $(formSelector);

        // Reset jQuery Validation plugin
        $form.validate().resetForm();

        // Remove validation classes
        $form.find('.is-invalid').removeClass('is-invalid');
        $form.find('.is-valid').removeClass('is-valid');

        // Remove error messages
        $form.find('.invalid-feedback').remove();
    }

    $('button[type="reset"]').click(function() {
        resetFormValidation("#product_form");
    });
</script>