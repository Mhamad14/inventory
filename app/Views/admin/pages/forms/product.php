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
                            <form action="<?= base_url('admin/products/save_products') ?>" id="product_form" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <div class="card-footer">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="name"><?= labels('product_name', 'Product Name') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="name" id="name" value="<?= !empty($products) && !empty($products['name']) ? $products['name'] : "" ?>">
                                                <textarea class="d-none" id="units"><?= json_encode($units) ?> </textarea>
                                                <textarea class="d-none" id="all_warehouses"><?= json_encode($all_warehouses) ?> </textarea>
                                                <textarea class="d-none" id="variants1"> </textarea>
                                                <textarea class="d-none" id="variant_unit_name"><?= json_encode($variant_unit_name) ?> </textarea>
                                                <input type="hidden" name="product_id" id="product_id" value="<?= !empty($products) && !empty($products['id']) ? $products['id'] : "" ?>">
                                                <input type="hidden" id="products_tax_value" value='<?= isset($products_tax_value) ? $products_tax_value : '' ?>'>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="category_id"><?= labels('category', 'Category') ?></label><span class="asterisk text-danger"> *</span>
                                                <select class="form-control" name="category_id" id="category_id">
                                                    <option value="<?= !empty($products['category_id']) ? $products['category_id'] : "1" ?>"><?= !empty($category_name)  ? $category_name : "General" ?> </option>
                                                    <?php
                                                    foreach ($categories as $category) { ?>
                                                        <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="brand_id"><?= labels('brand', 'Brand') ?></label>
                                                <select class="form-control" name="brand_id" id="brand_id">
                                                    <option value="">Please Select</option>
                                                    <?php
                                                    foreach ($brands as $brand) { ?>
                                                        <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="is_tax_inlcuded" class="custom-switch  p-35">
                                                    <?php if (!empty($products['is_tax_included']) && $products['is_tax_included'] == "1") { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded" class="custom-switch-input" checked>
                                                    <?php } elseif (isset($products['is_tax_included']) && $products['is_tax_included'] == "0") { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded" class="custom-switch-input">
                                                    <?php } else { ?>
                                                        <input type="checkbox" name="is_tax_inlcuded" id="is_tax_inlcuded" class="custom-switch-input" checked>
                                                    <?php } ?>
                                                    <span class="custom-switch-indicator"></span>
                                                    <span class="custom-switch-description"><?= labels('is_tax_included', 'Is tax included?') ?></span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row" id="tax_rows">
                                        <div class="form-group">
                                            <label for="">Select tax </label>
                                            <div>
                                                <input name='tax_ids' id="tax_ids"
                                                    class='some_class_name mb-3 p-1'

                                                    placeholder='write some tags'>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="description"><?= labels('description', 'Description') ?></label><span class="asterisk text-danger"> *</span>
                                                <textarea name="description" class="form-control" name="description" id="description"><?= !empty($products) && !empty($products['description']) ? $products['description'] : "" ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="card-body">
                                                <a href="<?= !empty($products) && !empty($products['image']) ?  base_url($products['image']) : "" ?>" data-lightbox="product-image">
                                                    <img class="product-image-dimensions" src="<?= !empty($products) && !empty($products['image']) ?  base_url($products['image']) : "" ?>" alt="">
                                                </a>

                                            </div>
                                        </div>
                                        <div class="col-md">
                                            <label for="image"><?= labels('image', 'Image') ?></label>
                                            <input type="file" class="form-control" name="image" id="image">
                                            <input type="hidden" name="old_image" id="old_image" value="<?= !empty($products['image']) ? $products['image'] : "" ?>">
                                            <!-- <div id="image-preview" class="image-preview">
                                                <label for="image"><?= labels('image', 'Image') ?></label>
                                                <input type="file" class="form-control" name="image" id="image">
                                                <input type="hidden" name="old_image" id="old_image" value="<?= !empty($products['image']) ? $products['image'] : "" ?>">
                                                <img class="settings_logo" src="<?= !empty($products) && !empty($products['image']) ?  base_url($products['image']) : "" ?>" alt="">
                                            </div> -->
                                        </div>
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="product_type"><?= labels('product_type', 'Product Type') ?></label><span class="asterisk text-danger"> *</span>
                                                <select class="form-control select" name="product_type" id="product_type">
                                                    <?php if (!empty($products['type']) && $products['type'] == "simple") { ?>
                                                        <option value="<?= !empty($products['type']) ? $products['type'] : "" ?>" selected> <?= ucwords($products['type']) . " Product" ?></option>
                                                        <option value="variable"><?= labels('variable_product', 'Variable Product') ?></option>
                                                    <?php } elseif (!empty($products['type']) && $products['type'] == "variable") { ?>
                                                        <option value="<?= !empty($products['type']) ? $products['type'] : "" ?>" selected> <?= ucwords($products['type']) . " Product" ?></option>
                                                        <option value="simple"><?= labels('simple_product', 'Simple Product') ?></option>
                                                    <?php  } else { ?>
                                                        <option value="simple" selected><?= labels('simple_product', 'Simple Product') ?></option>
                                                        <option value="variable"><?= labels('variable_product', 'Variable Product') ?></option>
                                                    <?php  } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-2">
                                            <div class="form-group">
                                                <label for="stock_management"><?= labels('stock_management', 'Stock Management') ?></label><span class="asterisk text-danger"> *</span><br>
                                                <label for="stock_management" class="custom-switch p-0">
                                                    <?php if (!empty($products['stock_management']) && $products['stock_management'] == "1") { ?>
                                                        <input role="button" type="checkbox" name="stock_management" id="stock_management" class="custom-switch-input" checked>
                                                    <?php } elseif (isset($products['stock_management']) && $products['stock_management'] == "0") { ?>
                                                        <input role="button" type="checkbox" name="stock_management" id="stock_management" class="custom-switch-input">
                                                    <?php } elseif (isset($products['stock_management']) && $products['stock_management'] == "2") { ?>
                                                        <input role="button" type="checkbox" name="stock_management" id="stock_management" class="custom-switch-input" checked>
                                                    <?php } else { ?>
                                                        <input role="button" type="checkbox" name="stock_management" id="stock_management" class="custom-switch-input">
                                                    <?php } ?>
                                                    <span class="custom-switch-indicator"></span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-10 row">
                                            <div class="col-md-4" id="stock_management_type_div">
                                                <div class="form-group">
                                                    <label for="stock_management_type"><?= labels('type', 'Type') ?></label><span class="asterisk text-danger"> *</span>
                                                    <select class="form-control select" name="stock_management_type" id="stock_management_type">
                                                        <?php if (!empty($products['stock_management']) && $products['stock_management'] == "1") { ?>
                                                            <option value="<?= !empty($products['stock_management']) ? $products['stock_management'] : "" ?>" selected> <?= "Product Level" ?></option>
                                                            <option value=""> <?= labels('select_level', 'Select Level') ?></option>
                                                            <option value="2"><?= labels('variant_level', 'Variant Level') ?></option>
                                                        <?php } elseif (!empty($products['stock_management']) && $products['stock_management'] == "2") { ?>
                                                            <option value="<?= !empty($products['stock_management']) ? $products['stock_management'] : "" ?>" selected> <?= "Variant Product" ?></option>
                                                            <option value=""> <?= labels('select_level', 'Select Level') ?></option>
                                                            <option value="1"><?= labels('product_level', 'Product Level') ?></option>
                                                        <?php  } else { ?>
                                                            <option value=""><?= labels('select_level', 'Select Level') ?></option>
                                                            <option value="1"><?= labels('product_level', 'Product Level') ?></option>
                                                            <option value="2"><?= labels('variant_level', 'Variant Level') ?></option>
                                                        <?php  } ?>

                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-3 stock_product_level">
                                                <div class="form-group">
                                                    <label for="simple_product_stock"><?= labels('stock', 'Stock') ?></label><span class="asterisk text-danger"> *</span>
                                                    <input type="number" class="form-control" id="simple_product_stock" name="simple_product_stock" min="0.00" placeholder="0.00" step="0.1" value="<?= !empty($products['stock']) ? $products['stock'] : "" ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-3 stock_product_level">
                                                <div class="form-group">
                                                    <label for="simple_product_unit_id"><?= labels('unit', 'Unit') ?></label><span class="asterisk text-danger"> *</span>
                                                    <select class="form-control" id="simple_product_unit_id" name="simple_product_unit_id">
                                                        <option value="<?= !empty($products) && !empty($products['unit_id']) ? $products['unit_id'] : "" ?>"> <?= isset($product_unit_name) ? $product_unit_name : "Select unit" ?></option>
                                                        <?php
                                                        foreach ($units as $unit) { ?>
                                                            <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-2 stock_product_level">
                                                <div class="form-group">
                                                    <label for="simple_product_qty_alert"><?= labels('min_stock_level', 'Minimum stock level ') ?></label><span class="asterisk text-danger"> *</span>
                                                    <input type="number" class="form-control" id="simple_product_qty_alert" name="simple_product_qty_alert" min="0.00" placeholder="0.00" value="<?= !empty($products['qty_alert']) ? $products['qty_alert'] : "" ?>">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <div class="">
                                            <h2 class="section-title"> <?= labels('product_details', 'Product details') ?> </h2>
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
                                                        // echo "<pre>";
                                                        // print_r($variants);
                                                        // die();
                                                ?>
                                                        <div class="variant-item py-1 mb-3 border-top border-2">

                                                            <div class="d-flex justify-content-between my-1">
                                                                <div>
                                                                    <p class="text-black font-weight-bolder"> Variant <?= $count ?> </p>
                                                                </div>
                                                                <div class="d-flex gap-3">
                                                                    <div>
                                                                        <button class="btn btn-icon btn-danger  remove_variant"
                                                                            data-variant_id="<?= $variant['id'] ?>"
                                                                            name="remove_variant"
                                                                            data-toggle="tooltip"
                                                                            data-placement="top"
                                                                            title="<?= labels('remove_variant', 'Remove variant') ?>">
                                                                            <i class="fas fa-trash"></i>
                                                                            <span class="d-none d-md-inline"> <?= labels('remove_variant', 'Remove variant') ?> </span>
                                                                        </button>
                                                                    </div>
                                                                    <div>
                                                                        <button class="btn btn-primary addWarehouseBtn"
                                                                            data-route="<?= base_url('admin/warehouse/get-all-warehouse') ?>"
                                                                            data-toggle="tooltip"
                                                                            data-placement="top"
                                                                            title="<?= labels('add_warehouse', 'Add warehouse') ?>"
                                                                            data-variant_index="<?= $count - 1 ?>"
                                                                            type="button">
                                                                            <i class="fas fa-plus"></i>
                                                                            <span class="d-none d-md-inline"> <?= labels('add_warehouse', 'Add warehouse') ?> </span>
                                                                        </button>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <div class="row mb-3">
                                                                <div class="col-md-2 custom-col">
                                                                    <label id=""> <?= labels('variant_name', 'Variant Name') ?><span class="asterisk text-danger"> *</span> </label>
                                                                    <input type="hidden" name="variant_id[]" id="variant_id" value="<?= $variant['id'] ?>">
                                                                    <input type="text" class="form-control" id="variant_name" name="variant_name[]" value="<?= $variant['variant_name'] ?>" placeholder="Ex. 1 kg..">
                                                                </div>
                                                                <div class="col-md-2 custom-col">
                                                                    <label id=""> <?= labels('variant_barcode', 'Variant Barcode') ?> </label>
                                                                    <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]" value="<?= isset($variant['barcode']) && !empty($variant['barcode']) ? $variant['barcode'] : ""  ?>" placeholder="Enter Barcode , Ex : 9875855">
                                                                </div>

                                                                <div class="col-md-2 custom-col">
                                                                    <label for=""> <?= labels('sale_price', 'Sale Price') ?>(₹)<span class="asterisk text-danger"> *</span> </label>
                                                                    <input type="number" class="form-control No-negative" id="sale_price" name="sale_price[]" value="<?= $variant['sale_price'] ?>" min="0.00" placeholder="0.00">
                                                                </div>
                                                                <div class="col-md-2 custom-col">
                                                                    <label for=""><?= labels('purchase_price', 'Purchase Price') ?>(₹)<span class="asterisk text-danger"> *</span> </label>
                                                                    <input type="number" class="form-control No-negative" id="purchase_price" name="purchase_price[]" value="<?= $variant['purchase_price'] ?>" min="0.00" placeholder="0.00">
                                                                </div>
                                                                <div class="col-md-2 custom-col stock_variant_level">
                                                                    <label for=""><?= labels('unit', 'Unit') ?><span class="asterisk text-danger"> *</span> </label>
                                                                    <select class="form-control" id="unit_id" name="unit_id[]">
                                                                        <option value=""> -<?= labels('select_unit', 'Select Unit') ?>-</option>
                                                                        <?php
                                                                        foreach ($units as $unit) { ?>
                                                                            <option value="<?= $unit['id'] ?>" <?= $variant['unit_id'] == $unit['id'] ? "selected" : '' ?>><?= $unit['name'] ?></option>
                                                                        <?php } ?>
                                                                    </select>
                                                                </div>
                                                                <div class="col-md-2 custom-col stock_variant_level">
                                                                    <label for=""><?= labels('stock', 'Stock') ?><span class="asterisk text-danger"> *</span> </label>
                                                                    <input type="number" class="form-control" id="stock" name="stock[]" value="<?= $variant['stock'] ?>" step="0.1" min="0.00" placeholder="0.00">
                                                                </div>
                                                                <div class="col-md-2 custom-col stock_variant_level">
                                                                    <label for=""><?= labels('min_stock', 'Minimum stock') ?><span class="asterisk text-danger"> *</span></label>
                                                                    <input type="number" class="form-control" id="qty_alert" name="qty_alert[]" value="<?= $variant['qty_alert'] ?>" min="0.00" placeholder="0.00">
                                                                </div>

                                                            </div>
                                                            <div class="warehouses">
                                                                <?php foreach ($variant['warehouse_data'] as $row) { ?>
                                                                    <div class="row">
                                                                        <div class="col-md-3">
                                                                            <div class="">
                                                                                <label for="">Warehouse</label><span class="asterisk text-danger">*</span>
                                                                                <?php

                                                                                $default_warehouse = 1;
                                                                                $selected_warehouse_id = 0;
                                                                                $warehouse_stock = '';
                                                                                $warehouse_qty_alert = '';
                                                                                if (! empty($variant['warehouse_data'])) {
                                                                                    $selected_warehouse_id = $row['warehouse_id'];
                                                                                    $warehouse_stock = $row['stock'];
                                                                                    $warehouse_qty_alert = $row['qty_alert'];
                                                                                }

                                                                                ?>
                                                                                <select class=" form-control" id="" name="warehouses[<?= $count - 1 ?>][warehouse_ids][]">
                                                                                    <option value=""> Select warehouse </option>
                                                                                    <?php foreach ($warehouses as $warehouse) {
                                                                                    ?>
                                                                                        <option value="<?= $warehouse['id'] ?>" <?= ($warehouse['id'] == $selected_warehouse_id)  ? "selected" : '' ?>> <?= $warehouse['name'] ?> </option>
                                                                                    <?php  } ?>
                                                                                </select>
                                                                            </div>
                                                                        </div>

                                                                        <div class="col-md-3">
                                                                            <div class="">
                                                                                <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                                                                                <input type="number" class=" form-control No-negative" id="warehouse_stock" step="0.1" min="0.00" name="warehouses[<?= $count - 1 ?>][warehouse_stock][]"
                                                                                    value="<?= $warehouse_stock ?>">

                                                                            </div>
                                                                        </div>
                                                                        <div class="col-md-3">
                                                                            <div class="">
                                                                                <label for="warehouse_qty_alert">Warehouse Minimum stock level</label><span class="asterisk text-danger">*</span>
                                                                                <input type="number" class=" form-control No-negative" id="warehouse_qty_alert" step="0.1" min="0.1" name="warehouses[<?= $count - 1 ?>][warehouse_qty_alert][]"
                                                                                    value="<?= $warehouse_qty_alert ?>">

                                                                            </div>
                                                                        </div>

                                                                    </div>
                                                                <?php    }  ?>

                                                            </div>

                                                        </div>
                                            </div>
                                        <?php
                                                        $count++;
                                                    }
                                                } else { ?>
                                        <div class="variant-item py-1 mb-3 border-top border-2">

                                            <div class="d-flex justify-content-between my-1">
                                                <div>
                                                    <p class="text-black font-weight-bolder"> Variant <?= "1" ?></p>
                                                </div>
                                                <div class="d-flex gap-3">
                                                    <!-- do not display remove variant btn for first variant -->
                                                    <!-- <div>
                                                        <button class="btn btn-icon btn-danger remove_variant"
                                                            data-variant_id=""
                                                            name="remove_variant"
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="<?= labels('remove_variant', 'Remove variant') ?>">
                                                            <i class="fas fa-trash"></i>
                                                            <span class="d-none d-md-inline"> <?= labels('remove_variant', 'Remove variant') ?> </span>
                                                        </button>
                                                    </div> -->
                                                    <div>
                                                        <button class="btn btn-primary addWarehouseBtn"
                                                            data-toggle="tooltip"
                                                            data-placement="top"
                                                            title="<?= labels('add_warehouse', 'Add warehouse') ?>"
                                                            data-variant_index="0"
                                                            type="button">
                                                            <i class="fas fa-plus"></i>
                                                            <span class="d-none d-md-inline"> <?= labels('add_warehouse', 'Add warehouse') ?> </span>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row mb-3">
                                                <div class="col-md-2 custom-col">

                                                    <label> <?= labels('variant_name', 'Variant Name') ?><span class="asterisk text-danger"> *</span> </label>

                                                    <input type="text" class="form-control" id="variant_name" name="variant_name[]" placeholder="Ex. 1 kg..">
                                                </div>
                                                <div class="col-md-2 custom-col">
                                                    <label id=""> <?= labels('variant_barcode', 'Variant Barcode') ?> </label>
                                                    <input type="text" class="form-control" id="variant_barcodee" name="variant_barcode[]" value="" placeholder="Enter Barcode , Ex : 9875855">
                                                </div>
                                                <div class="col-md-2 custom-col">
                                                    <label for=""> <?= labels('sale_price', 'Sale Price') ?>(₹)<span class="asterisk text-danger"> *</span> </label>
                                                    <input type="number" class="form-control No-negative" id="sale_price" name="sale_price[]" min="0.00" placeholder="0.00">
                                                </div>
                                                <div class="col-md-2 custom-col">
                                                    <label for=""><?= labels('purchase_price', 'Purchase Price') ?>(₹)<span class="asterisk text-danger"> *</span> </label>
                                                    <input type="number" class="form-control No-negative" id="purchase_price" name="purchase_price[]" min="0.00" placeholder="0.00">
                                                </div>
                                                <div class="col-md-2 custom-col stock_variant_level">
                                                    <label for=""><?= labels('unit', 'Unit') ?><span class="asterisk text-danger"> *</span> </label>
                                                    <select class="form-control" id="unit_id" name="unit_id[]">
                                                        <option value=""> -<?= labels('select_unit', 'Select Unit') ?>-</option>
                                                        <?php
                                                        foreach ($units as $unit) { ?>
                                                            <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                                                        <?php } ?>
                                                    </select>
                                                </div>
                                                <div class="col-md-2 custom-col stock_variant_level">
                                                    <label for=""><?= labels('stock', 'Stock') ?><span class="asterisk text-danger"> *</span> </label>
                                                    <input type="number" class="form-control" id="stock" step="0.1" name="stock[]" min="0.00" placeholder="0.00">
                                                </div>
                                                <div class="col-md-2 custom-col stock_variant_level">
                                                    <label for=""><?= labels('min_stock', 'Minimum stock') ?><span class="asterisk text-danger"> *</span></label>
                                                    <input type="number" class="form-control" id="qty_alert" name="qty_alert[]" min="0.00" placeholder="0.00">
                                                </div>

                                            </div>
                                            <div class="warehouses">
                                                <div class="row">
                                                    <div class="col-md-3">
                                                        <div class="">
                                                            <label for="">Warehouse</label><span class="asterisk text-danger">*</span>
                                                            <select class=" form-control" id="warehouse" name="warehouses[0][warehouse_ids][]">
                                                                <option value="" selected>Select warehouse </option>
                                                                <?php foreach ($warehouses as $warehouse) { ?>
                                                                    <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                                                <?php  } ?>
                                                            </select>
                                                        </div>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <div class="">
                                                            <label for="warehouse_stock">Warehouse Stock</label><span class="asterisk text-danger">*</span>
                                                            <input type="number" class=" form-control No-negative" id="warehouse_stock" step="0.1" min="0.1" name="warehouses[0][warehouse_stock][]">

                                                        </div>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <div class="">
                                                            <label for="warehouse_qty_alert">Warehouse Minimum stock level</label><span class="asterisk text-danger">*</span>
                                                            <input type="number" class=" form-control No-negative" id="warehouse_qty_alert" step="0.1" min="0.1" name="warehouses[0][warehouse_qty_alert][]">

                                                        </div>
                                                    </div>
                                                    <!-- <div class="col-md-2 custom-col ">
                <label for="" class="d-block"> <?= labels('remove_warehouse', 'Remove warehouse') ?></label>
                <button class="btn btn-icon btn-danger  remove-warehouse" type="button" data-variant_id="" name="remove_warehouse" data-toggle="tooltip" data-placement="bottom" title="Remove warehouse"><i class="fas fa-trash"></i></button>
            </div> -->
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

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