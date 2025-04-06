    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('services', 'Services') ?></h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <input type="hidden" id="business_id" value="<?= $business_id ?>">
                        <a class="btn btn-primary text-white" id="add_service_btn" href="<?= base_url('admin/services/Add_service'); ?>"  data-toggle="tooltip" data-bs-placement="bottom" title="<?= labels('add_service', 'Add Service') ?>"    ><i class="fas fa-plus"></i></a>
                    </div>

                    <div class="btn-group mr-2 no-shadow">
                        <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('bulk_upload', 'Bulk Upload Service') ?>"  ><i class="bi bi-cloud-download-fill"></i>
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
                                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "services-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/services/service_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true">
                                        <thead>
                                            <tr>
                                                <th data-field="id" data-sortable="true" data-visible="true"><?= labels('id', 'ID') ?></th>
                                                <th data-field="vendor_id" data-sortable="true" data-visible="false"> <?= labels('vendor_id', 'Vendor id') ?></th>
                                                <th data-field="business_name" data-sortable="true" data-visible="true"><?= labels('business_name', 'Business Name') ?></th>
                                                <th data-field="service_name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                                <th data-field="description" data-sortable="true" data-visible="true"><?= labels('description', 'Description') ?></th>
                                                <th data-field="price" data-sortable="true" data-visible="true"><?= labels('price', 'Price') ?></th>
                                                <th data-field="cost_price" data-sortable="true" data-visible="true"><?= labels('cost_price', 'Cost Price') ?></th>
                                                <th data-field="unit_id" data-sortable="true" data-visible="true"><?= labels('unit_name', 'Unit Name') ?></th>
                                                <th data-field="is_recursive" data-sortable="true" data-visible="false"><?= labels('is_recursive', 'is recursive?') ?></th>
                                                <th data-field="recurring_day" data-sortable="true" data-visible="false"><?= labels('recurring_days', 'Recurring Days') ?></th>
                                                <th data-field="recurring_price" data-sortable="true" data-visible="false"><?= labels('recurring_price', 'Recurring Price') ?></th>
                                                <th data-field="vendor_id" data-sortable="true" data-visible="false"> <?= labels('vendor_id', 'Vendor id') ?></th>
                                                <th data-field="is_tax_included" data-sortable="true" data-visible="true"><?= labels('is_tax_included', 'Is tax included?') ?></th>
                                                <th data-field="tax_id" data-sortable="true" data-visible="true"><?= labels('tax_name', 'tax name') ?></th>
                                                <th data-field="status" data-visible="true"><?= labels('status', 'Status') ?></th>
                                                <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
                                            </tr>
                                        </thead>
                                    </table>
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
                            <form action="<?= base_url('admin/bulk_uploads/import_service') ?>" method="post" id="bulk_uploads_form">
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
                                                <a href="<?= base_url('public/uploads/bulk_upload_services.csv') ?>" class="btn btn-info" download="order-bulk-upload-sample.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/bulk-upload-instructions-for-service.txt') ?>" class="btn btn-success" download="bulk-upload-instructions-for-service.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
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