    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('delivery_boys', 'Delivery Boys') ?> </h1>
                <div class="section-header-breadcrumb">
                    <div class="btn-group mr-2 no-shadow">
                        <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#bulk_upload_modal"  data-toggle="tooltip" data-bs-placement="bottom" title="   <?= labels('bulk_upload', 'Bulk Upload Delivery Boy') ?>"  ><i class="bi bi-cloud-download-fill"></i>
                           </a>
                    </div>

                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-danger"><?= session("message"); ?></label></div>
            <?php } ?>
            <div class="section-body">
                <div class="row mt-sm-4">
                    <div class='col-md-12'>
                        <div class="card">
                            <div class="card-body">
                                <h2 class="section-title"><?= labels('register_delivery_boy_here', 'Register Delivery Boy Here!') ?></h2>

                                <div class="mt-3">
                                    <form method="post" action='<?= base_url('admin/delivery_boys/save') ?>' id="register_deliveryboy">
                                        <div class="row">

                                            <div class="form-group col-md">
                                                <label for="first_name"><?= labels('name', 'Name') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" id="name" placeholder="Enter Your Name" name="first_name">
                                                <input type="hidden" name="delivery_boy_id" id="delivery_boy_id">
                                                <input type="hidden" name="business_id" id="business_id" value="<?= $business_id ?>">
                                                <input type="hidden" name="vendor_id" id="vendor_id" value="<?= $vendor_id ?>">
                                            </div>
                                            <div class="form-group col-md">
                                                <label for="identity"><?= labels('mobile_number', 'Mobile') ?> <small>(<?= labels('identity', 'Identity') ?>)</small></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" id="identity" placeholder="Enter Your Mobile Number" name="identity">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md">
                                                <label for="password"><?= labels('password', 'Password') ?> <small>(<?= labels('password_delivery_boy_text', 'Enter new password if you want to update current password') ?>)</small></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" id="password" value="" placeholder="Enter Password" name="password">
                                            </div>
                                            <div class="form-group col-md">
                                                <label for="email"><?= labels('email', 'Email') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" id="email" placeholder="abc@gmail.com" name="email">
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md">
                                                <div class="form-group">
                                                    <label for="customer_permission"><?= labels('permission_for_customer', 'Do you want to allow delivery boy to create customers?') ?><span class="asterisk text-danger"> *</span></label><br>
                                                    <label for="customer_permission" class="custom-switch p-0">
                                                        <input role="button" type="checkbox" name="customer_permission" id="customer_permission" class="custom-switch-input" checked>
                                                        <span class="custom-switch-indicator"></span>
                                                    </label>
                                                </div>

                                            </div>
                                            <div class="form-group col-md">
                                                <div class="form-group">
                                                    <label for="transaction_permission"><?= labels('permission_for_transaction', 'Do you want to allow delivery boy to create transactions?') ?><span class="asterisk text-danger"> *</span></label><br>
                                                    <label for="transaction_permission" class="custom-switch p-0">
                                                        <input role="button" type="checkbox" name="transaction_permission" id="transaction_permission" class="custom-switch-input" checked>
                                                        <span class="custom-switch-indicator"></span>
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="form-group col-md">
                                                <div class="form-group">
                                                    <label for="orders_permission"><?= labels('permission_for_order', 'Do you want to allow delivery boy to create orders?') ?><span class="asterisk text-danger"> *</span></label><br>
                                                    <label for="orders_permission" class="custom-switch p-0">
                                                        <input role="button" type="checkbox" name="orders_permission" id="orders_permission" class="custom-switch-input" checked>
                                                        <span class="custom-switch-indicator"></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md">
                                                <?php if (!empty($businesses)) { ?>
                                                    <label for=""><?= labels('check_business', 'Check Business for Delivery Boy') ?></label><span class="asterisk text-danger"> *</span><br>

                                                    <?php foreach ($businesses as $business) { ?>
                                                        <div class="form-check form-check-inline business mb-2">
                                                            <input class="form-check-input <?= "business_" .$business['id'] ?>" type="checkbox" name="business_id[]" id="<?= $business['id'] ?>" value="<?= $business['id'] ?>">
                                                            <label class="form-check-label" for="<?= $business['id'] ?>"><?= $business['name'] ?></label>
                                                        </div>
                                                <?php }
                                                } ?>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="status" class="custom-switch p-0">
                                                <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                                <span class="custom-switch-indicator"></span>
                                                <span class="custom-switch-description"><?= labels('status', 'Status') ?></span>
                                            </label>
                                        </div>

                                        <button type="submit" class="btn btn-primary" id="save-register-result-btn" name="register" value="Save"><?= labels('save', 'Save') ?></button>
                                        <div class="mt-3">
                                            <div id="save-register-result"></div>
                                        </div>
                                    </form>

                                </div>
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
                                        <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "delivery-boys-list"}' id="delivery_boys_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/delivery_boys/delivery_boys_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                            <thead>
                                                <tr>
                                                    
                                                    <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                                    <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                                    <th data-field="assigned_b_id" data-sortable="true" data-visible="false"><?= labels('assigned_businesses', 'Assigned Businesses') ?></th>
                                                    <th data-field="permissions" data-sortable="true" data-visible="false"><?= labels('permissions', 'Permissions') ?></th>
                                                    <th data-field="email" data-sortable="true"><?= labels('email', 'Email') ?></th>
                                                    <th data-field="mobile" data-sortable="true"><?= labels('mobile_number', 'Mobile') ?></th>
                                                    <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                                                    <th data-field="action" data-sortable=""><?= labels('action', 'Action') ?></th>                                                </tr>
                                            </thead>
                                        </table>
                                    </div>
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
                            <form action="<?= base_url('admin/bulk_uploads/import_delivery_boys') ?>" method="post" id="bulk_uploads_form">
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
                                                <a href="<?= base_url('public/uploads/bulk-upload-delivery-boys.csv') ?>" class="btn btn-info" download="bulk-upload-delivery-boys.csv">Bulk upload sample file <i class="fas fa-download"></i></a>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <a href="<?= base_url('public/uploads/bulk-upload-instructions-for-delivery-boy.txt') ?>" class="btn btn-success" download="bulk-upload-instructions-for-delivery-boy.txt">Bulk upload instructions <i class="fas fa-download"></i></a>
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