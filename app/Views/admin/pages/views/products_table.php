<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('products', 'Products') ?></h1>
            <div class="section-header-breadcrumb">
                <div class=" mr-2 no-shadow">
                    <input type="hidden" id="business_id" value="<?= $business_id ?>">
                    <a type="button" class="btn btn-primary text-white" id="add_product_btn" href="<?= base_url('admin/products/add_products'); ?>"  data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('add_product', 'Add Product') ?>"    ><i class="fas fa-plus"></i> </a>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('bulk_upload', 'Bulk Upload') ?>"    ><i class="bi bi-cloud-download-fill"></i>
                        </a>
                </div>
            </div>
        </div>
        <?= session("message") ?>

        <div class="card">
            <div class="card-body">
                <div class="row">

                    <div class="col-md-4">
                        <select class="select2 product_category form-control" id="product_category" name="product_category">
                            <option value=""><?= labels('all_categories', 'All Categories') ?></option>
                            <?php foreach ($categories as $category) { ?>
                                <option value="<?= $category['id'] ?>"><?= $category['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <select class="select2 product_brand form-control" id="product_brand" name="product_category">
                            <option value=""><?= labels('all_brand', 'All Brand') ?></option>
                            <?php foreach ($brands as $brand) { ?>
                                <option value="<?= $brand['id'] ?>"><?= $brand['name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>

                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "products-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/products/products_table'); ?>"  data-pagination="true" data-search="true" data-side-pagination="server" data-query-params = "cat_query" id="products_table">
                        <thead>
                            <tr>
                                <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                <th data-field="description" data-sortable="true" data-visible="true"><?= labels('description', 'Description') ?></th>
                                <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                                <th data-field="stock" data-sortable="true" data-visible="true"><?= labels('stock', 'Stock(qty)') ?></th>
                                <th data-field="caregory_name" data-sortable="true" data-visible="false"><?= labels('category_name', 'Category name') ?></th>
                                <th data-field="business_name" data-sortable="true" data-visible="false"><?= labels('business_name', 'Business Name') ?></th>
                                <th data-field="creator" data-sortable="true" data-visible="true"><?= labels('creator', 'Creator') ?></th>
                                <th data-field="status" data-visible="true"><?= labels('status', 'Status') ?></th>
                                <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<!-- variants modal -->
<div class="modal" id="variants_Modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Product Variants</h4>
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
                                        <table class="table table-hover table-bordered" id="variants_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25,50, 100, 200, All]" data-url="<?= base_url('admin/products/variants_table/'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                            <thead>
                                                <tr>
                                                    <th data-field="id" data-sortable="true" data-visible="true">ID</th>
                                                    <th data-field="product_id" data-sortable="true" data-visible="false">Product ID</th>
                                                    <th data-field="variant_name" data-sortable="true" data-visible="true">Name</th>
                                                    <th data-field="stock" data-sortable="true" data-visible="true">Stock</th>
                                                    <th data-field="qty_alert" data-sortable="false" data-visible="true"><?= labels('qty_alert', 'Quantity Alert') ?></th>
                                                    <th data-field="unit_id" data-sortable="true" data-visible="true">Unit Name</th>
                                                    <th data-field="status" data-visible="false">Status</th>
                                                    <th data-field="action" data-visible="true">Action</th>
                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
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
                        <form action="<?= base_url('admin/bulk_uploads/import_products') ?>" method="post" id="bulk_uploads_form">
                            <div class="card ">
                                <div class="card-body row">
                                    <div class="form-group">
                                        <label><?= labels('type<small>(upload/update)</small>', 'Type <small>(upload/update)</small>') ?></label>
                                        <select class="form-control" id="type" name="type">
                                            <option value=''>Select</option>
                                            <option value='upload'>Upload</option>
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
                                            <a href="<?= base_url('public/uploads/bulk-upload-product-with-variant-format.csv') ?>" class="btn btn-info" download="bulk-upload-product-with-variant-format.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/Instructions-for-bulk-upload-product.txt') ?>" class="btn btn-success" download="Instructions-for-bulk-upload-product.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-update-product-with-variant-update-format.csv') ?>" class="btn btn-info" download="bulk-update-product-with-variant-update-format.csv">Bulk update sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/Instructions-for-bulk-update-product.txt') ?>" class="btn btn-success" download="Instructions-for-bulk-update-product.txt">Bulk update instructions <i class="fas fa-download"></i></a>
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

<!-- barcode modal -->
<div class="modal" id="barcode_Modal">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title">Barcode</h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <div class="">
                    <div class="container">
                        <div class="row">
                            <div class="col-12 printDiv" id="printDiv">
                                
                                    <div class="row" id="variant-barcode">
                                    </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="download-barcode">Download</button>
            </div>
        </div>
    </div>
</div>