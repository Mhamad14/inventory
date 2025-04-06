<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('add_expenses_type', 'Add Expenses Type') ?></h1>
            <div class="section-header-breadcrumb">
                <div class=" mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/expenses'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('expenses', 'Expenses') ?>"  ><i class="fas fa-list"></i> </a>
                </div>
                
                <div class="btn-group mr-2 no-shadow">
                        <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('bulk_upload', 'Bulk Upload Expenses Type') ?> "  ><i class="bi bi-cloud-download-fill"></i>
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
                        <form id="expenses_type_form" method="post" action="<?= base_url('admin/expenses_type/save_expenses_type'); ?>">
                            <div class="row">
                                <div class="form-group col-md">
                                    <label for="name"> <?= labels('title', 'Title') ?> <small class="text-danger">*</small></label>
                                    <input id="title" type="text" class="form-control" name="title" placeholder="Ex. Electricity Bill, etc" value="<?= !empty($type) && !empty($type['title']) ? $type['title'] : "" ?>" autofocus>
                                    <input id="id" type="hidden" class="form-control" name="id" value="<?= !empty($type) && !empty($type['id']) ? $type['id'] : "" ?>" autofocus>
                                    <input id="vendor_id" type="hidden" class="form-control" name="vendor_id" value="<?= !empty($id) ? $id : "" ?>" autofocus>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="note"><?= labels('description', 'Description') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="description" id="description" value="<?= !empty($type) && !empty($type['description']) ? $type['description'] : "" ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="expenses_date"><?= labels('expenses_type_date', 'Expenses Type Date') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="date" name="expenses_type_date" class="form-control" id="expenses_type_date" value="<?= !empty($type) && !empty($type['expenses_type_date']) ? $type['expenses_type_date'] : "" ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-md">
                                    <button type="submit" class="btn btn-primary" value="expenses">
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
                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "categories-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/expenses_type/expenses_type_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="title" data-sortable="true"><?= labels('title', 'Title') ?></th>

                                            <th data-field="description" data-sortable="true"><?= labels('description', 'Description') ?></th>
                                            <th data-field="expenses_type_date" data-sortable="true"><?= labels('expenses_type_date', 'Date') ?></th>
                                            <th data-field="action" data-sortable="true" ><?= labels('action' ,'Action')?></th>
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
                            <form action="<?= base_url('admin/bulk_uploads/import_expenses_types') ?>" method="post" id="bulk_uploads_form">
                                <div class="card ">
                                    <div class="card-body row">
                                        <div class="form-group">
                                            <label><?= labels('type<small>(upload)</small>', 'Type <small>(upload)</small>') ?></label>
                                            <select class="form-control" id="type" name="type">
                                                <option value=''>Select</option>
                                                <option value='upload' selected>Upload</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div class="card-body row">
                                        <div class="form-group">
                                            <label><?= labels('file', 'File') ?></label>
                                            <input type="hidden" id="vendor_id" name="vendor_id" value="<?= $business_id ?>">
                                            <input type="file" class="form-control" id="bulk_upload_file" name="file" accept=".csv">
                                        </div>
                                    </div>
                                    <div class="card-body row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/bulk-upload-expenss_type.csv') ?>" class="btn btn-info" download="bulk-upload-expenss_type.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/bulk-upload-instructions-for-expenss_type.txt') ?>" class="btn btn-success" download="bulk-upload-instructions-for-expenss_type.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
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