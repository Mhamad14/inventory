<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('stock_management', 'Stock Management') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <input type="hidden" id="business_id" value="<?= $business_id ?>">
                    <!-- <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#new_stock"><i class="fas fa-plus"></i>
                        <?= labels('add_adjustment', 'New Stock') ?></a> -->
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal" data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('bulk_upload', 'Bulk Upload') ?>"><i class="bi bi-cloud-download-fill"></i>
                    </a>
                </div>
            </div>
        </div>
        <?= session("message") ?>

        <div class="card">
            <div class="card-body">
                <div class="row">
                    <table class="table table-hover table-borderd" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/products/table'); ?>" data-pagination="true" data-search="true" data-query-params="stock_params">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                                <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                <th data-field="stock" data-sortable="true" data-visible="true"><?= labels('current_stock', 'Current Stock(qty)') ?></th>
                                <th data-field="warehouse_stock" data-sortable="true" data-visible="true"><?= labels('warehouse-stock', 'Warehouse Stock(qty)') ?></th>
                                <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>

<div class="modal" id="new_stock">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">New Stock adjustment</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">


                <form action="<?= base_url('admin/products/save_adjustment') ?>" id="stock_adjustment_form" accept-charset="utf-8" method="POST">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="products">Products</label><span class="asterisk text-danger">*</span>
                                <!-- <select class="fetch_stock form-control" id="fetch_stock"></select> -->
                                <input type="text" class="form-control" name="name" id="name" readonly>
                                <input type="hidden" name="product">
                                <input type="hidden" name="stock_management">
                                <input type="hidden" name="variant_id">
                            </div>
                        </div>
                        <div class="col">
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
                        <div class="col-4">
                            <div class="form-group">
                                <label><?= labels('current_stock', 'Current Stock') ?></label>
                                <input type="text" class="form-control current_stock" name="current_stock" id="current_stock" readonly>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?= labels('quantity', 'Quantity') ?></label><span class="asterisk text-danger">*</span>
                                <input type="number" class="form-control" name="quantity" id="quantity">
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="form-group">
                                <label><?= labels('type', 'Type') ?></label>
                                <select class="form-control" id="type" name="type">
                                    <option value='add'><?= labels('add', 'Add') ?></option>
                                    <option value='subtract'><?= labels('subtract', 'Subtract') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group ">
                                <label for="note"><?= labels('note', 'Note') ?></label>
                                <textarea class="form-control" id="note" name="note"></textarea>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" value="Save"><?= labels('save', 'Save') ?></button>
                </form>

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="modal" id="transfer_stock">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('transfer-stock', 'Transfer Stock') ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">


                <form action="<?= base_url('admin/product/save_transfer') ?>" id="stock_adjustment_form" accept-charset="utf-8" method="POST">
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="products"><?= labels('products', 'Products') ?></label><span class="asterisk text-danger">*</span>
                                <!-- <select class="fetch_stock form-control" id="fetch_stock"></select> -->
                                <input type="text" class="form-control" name="ts_name" id="ts_name" readonly>
                                <input type="hidden" name="ts_variant_id">
                            </div>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col">
                            <div class="form-group">
                                <label for="ts_from_warehouse_id"><?= labels('from-warehouse', 'From Warehouse') ?></label><span class="asterisk text-danger">*</span>
                                <select class=" form-control" id="ts_from_warehouse_id" name="ts_from_warehouse_id">
                                    <option value="" selected>Select warehouse </option>
                                    <?php foreach ($warehouses as $warehouse) { ?>
                                        <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                    <?php  } ?>
                                </select>
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="ts_to_warehouse_id"><?= labels('to-warehouse', 'To Warehouse') ?> </label><span class="asterisk text-danger">*</span>
                                <select class=" form-control" id="ts_to_warehouse_id" name="ts_to_warehouse_id">
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
                                <label><?= labels('quantity', 'Quantity') ?></label><span class="asterisk text-danger">*</span>
                                <input type="number" class="form-control No-negative" name="ts_quantity" id="ts_quantity">
                            </div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary" value="Save"><?= labels('save', 'Save') ?></button>
                </form>

            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>


<div class="modal" id="bulk_upload_modal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Bulk Upload</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="section">
                    <div class="section-body">
                        <form action="<?= base_url('admin/bulk_uploads/import_stock') ?>" method="post" id="bulk_uploads_form">
                            <div class="card ">
                                <div class="card-body row">
                                    <div class="form-group">
                                        <label><?= labels('type<small>(upload/update)</small>', 'Type <small>(upload/update)</small>') ?></label>
                                        <select class="form-control" id="type" name="type">
                                            <option value=''>Select</option>
                                            <option value='update'>Update</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="card-body row">
                                    <div class="form-group">
                                        <label><?= labels('file', 'File') ?></label>
                                        <input type="hidden" id="business_id" name="business_id" value="<?= $business_id ?>">
                                        <input type="file" class="form-control" id="bulk_upload_file" name="file" accept=".csv">
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/product-bulk-update-sample-stock.csv') ?>" class="btn btn-info" download="product-bulk-update-sample-stock.csv">Bulk update sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/Instructions-for-bulk-upload-stock.txt') ?>" class="btn btn-success" download="Instructions-for-bulk-upload-stock.txt">Bulk update instructions <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary bulk_upload" value="Save"><?= labels('import', 'Import') ?></button>
                            </div>

                        </form>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
            </div>
        </div>
    </div>
</div>