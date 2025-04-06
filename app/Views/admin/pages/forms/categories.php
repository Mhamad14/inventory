<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('add_categories', 'Add Categories') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/categories'); ?>" class="btn" data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('categories', 'Categories') ?>"  ><i class="fas fa-list"></i> </a>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('bulk_upload', 'Bulk Upload') ?>"   ><i class="bi bi-cloud-download-fill"></i>
                        </a>
                </div>
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-danger"><?php $message = session('message');
                                        echo $message['title']; ?></label></div>
        <?php } ?>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-body">
                        <form id="vendor_categories_form" method="post" action="<?= base_url('admin/categories/save_categories'); ?>">
                            <div class="row">
                                <div class="form-group col-md">
                                    <label for="name"> <?= labels('name', 'Name') ?> <small class="text-danger">*</small></label>
                                    <input id="name" type="text" class="form-control" name="name" placeholder="Ex. clothes,electronics,accessories etc" value="<?= !empty($category) && !empty($category['name']) ? $category['name'] : "" ?>" autofocus>
                                    <input id="category_id" type="hidden" class="form-control" name="category_id" value="<?= !empty($category) && !empty($category['id']) ? $category['id'] : "" ?>" autofocus>
                                    <input id="vendor_id" type="hidden" class="form-control" name="vendor_id" value="<?= !empty($id) ? $id : "" ?>">
                                </div>
                                <div class="form-group col-md">
                                    <label for="parent_id"> <?= labels('parent_id', 'Parent ID') ?></label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="<?= !empty($category) && !empty($category['parent_id']) ? $category['parent_id'] : "" ?>" selected> <?= !empty($parent_category) && !empty($parent_category['name']) ? $parent_category['name'] : "Select Category" ?></option>
                                        <?php foreach ($categories as $category) { ?>
                                            <option value="<?= $category['id'] ?>"><?= ucwords($category['name']) ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="status" class="custom-switch  p-35">
                                            <input type="checkbox" name="status" id="status" class="custom-switch-input"
                                            <?php 
                                            
                                              if( isset($category_status) &&  $category_status ){echo"checked";}
                                                ?>
                                            >
                                            <span class="custom-switch-indicator"></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md">
                                <button type="submit" class="btn btn-primary" value="category">
                                    <?= labels('submit', 'Submit') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "categories-list","ignoreColumn": ["action"]}'  data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100]" data-url="<?= base_url('admin/categories/category_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true"  data-server-sort="false" data-query-params="queryParams" >
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="parent_id" data-sortable="true"><?= labels('parent_id', 'Parent ID') ?></th>
                                            <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                                            <th data-field="action" ><?= labels('action', 'Action') ?></th>
                                        </tr>
                                    </thead>
                                </table>
                                <script>
                                    function queryParams(params) {
                                        console.log(params); // Debug: Check parameters
                                        return {
                                            limit: params.limit,
                                            offset: params.offset,
                                            sort: params.sort,
                                            search: params.search
                                        };
                                    }
                                </script>   
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>
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
                        <form action="<?= base_url('admin/bulk_uploads/import_categories') ?>" method="post" id="bulk_uploads_form">
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
                                        <input id="vendor_id" type="hidden" class="form-control" name="vendor_id" value="<?= !empty($id) ? $id : "" ?>">
                                        <input type="file" class="form-control" id="bulk_upload_file" name="file" accept=".csv">
                                    </div>
                                </div>
                                <div class="card-body row">
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/product-bulk-upload-sample-categories.csv') ?>" class="btn btn-info" download="product-bulk-upload-sample.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-upload-instructions.txt') ?>" class="btn btn-success" download="bulk-upload-instructions.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/product-bulk-update-sample-categories.csv') ?>" class="btn btn-info" download="product-bulk-update-sample.csv">Bulk update sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3 col-xs-12">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-update-instructions.txt') ?>" class="btn btn-success" download="bulk-update-instructions.txt">Bulk update instructions <i class="fas fa-download"></i></a>
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