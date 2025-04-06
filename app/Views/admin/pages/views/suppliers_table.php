<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('suppliers', 'Suppliers') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" id="" href="<?= base_url('admin/suppliers/create'); ?>"  data-toggle="tooltip" data-bs-placement="bottom"    title="  <?= labels('add_supplier', 'Add Supplier') ?> "  ><i class="fas fa-plus"></i> </a>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom"    title="  <?= labels('bulk_upload', 'Bulk Upload Suppliers') ?> "    ><i class="bi bi-cloud-download-fill"></i>
                        </a>
                </div>
            </div>
        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-auto-refresh="true"
                                    data-show-columns="true"
                                    data-show-toggle="true"
                                    data-show-refresh="true"
                                    data-toggle="table"
                                    data-search-highlight="true"
                                    data-page-list="[5, 10, 25, 50, 100, 200, All]"
                                    data-url="<?= base_url('admin/suppliers/suppliers_table'); ?>"
                                    data-side-pagination="server"
                                    data-pagination="true"
                                    data-search="true"
                                    data-query-params="queryParams">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="email" data-sortable="true" data-visible="true"><?= labels('email', 'Email') ?></th>
                                            <th data-field="mobile" data-sortable="true" data-visible="true"><?= labels('mobile_number', 'Mobile Number') ?></th>
                                            <th data-field="balance" data-sortable="true" data-visible="true"><?= labels('balance', 'Balance') ?></th>
                                            <th data-field="status" data-sortable="true" data-visible="true"><?= labels('status', 'Status') ?></th>
                                            <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
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
        <!--container div  -->
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
                        <form action="<?= base_url('admin/bulk_uploads/import_suppliers') ?>" method="post" id="bulk_uploads_form">
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
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-update-supplier-new.csv') ?>" class="btn btn-info" download="bulk-upload-supplier.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-upload-supplier-upload.txt') ?>" class="btn btn-success" download="bulk-upload-supplier-upload.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-upload-supplier.csv') ?>" class="btn btn-info" download="bulk-update-supplier-new.csv">Bulk update sample file <i class="fas fa-download"></i></a>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <a href="<?= base_url('public/uploads/bulk-upload-instructions-for-supplier-update.txt') ?>" class="btn btn-success" download="bulk-upload-instructions-for-supplier-update.txt">Bulk update instructions <i class="fas fa-download"></i></a>
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