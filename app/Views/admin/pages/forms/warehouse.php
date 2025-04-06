<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('warehouse', 'Warehouse') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#syncProductModel" data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('sync-all-products-to-one-warehouse', 'Sync all products to one warehouse') ?>">
                        <i class="fas fa-sync"></i>
                    </button>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createWarehouseModel" data-toggle="tooltip" data-bs-placement="bottom" title="<?= labels('create-warehouse', 'Create Warehouse') ?> ">
                        <i class="fas fa-plus"></i>
                    </button>
                </div>
            </div>
        </div>




        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "tax-list"}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/warehouse/warehouse-table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="city" data-sortable="true"><?= labels('city', 'City') ?></th>
                                            <th data-field="country" data-sortable="true"><?= labels('country', 'Country') ?></th>
                                            <th data-field="address" data-sortable="true"><?= labels('address', 'Address') ?></th>
                                            <th data-field="zip_code" data-sortable="true"><?= labels('zip-code', 'Zip code') ?></th>

                                            <th data-field="action"><?= labels('action', 'Action') ?></th>
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

<div class="modal fade" id="createWarehouseModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="createWarehouseModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createWarehouseModelLabel"><?= labels('create-warehouse', 'Create Warehouse') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md">

                        <form action="<?= base_url('admin/warehouse/save')  ?>" id="storeWarehouse" method="post">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="name"> Name <small class="text-danger">*</small></label>
                                        <input id="name" type="text" class="form-control" name="name" placeholder="Enter Warehouse name">

                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="country"> Country <small class="text-danger">*</small></label>
                                        <input id="country" type="text" class="form-control" name="country" placeholder="Enter Country name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="city"> City <small class="text-danger">*</small></label>
                                        <input id="city" type="text" class="form-control" name="city" placeholder="Enter City name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="zip_code"> Zip_code <small class="text-danger">*</small></label>
                                        <input id="zip_code" type="text" class="form-control" name="zip_code" placeholder="Enter zip_code name">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="address">Address</label><span class="asterisk text-danger"> *</span>
                                        <textarea name="address" class="form-control" id="address" placeholder="Enter Address"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <?= labels('submit', 'Submit') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- // edit model  -->
<div class="modal fade" id="editWarehouseModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editWarehouseModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editWarehouseLabel"><?= labels('edit-warehouse', 'Edit Warehouse') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md">

                        <form action="<?= base_url('admin/warehouse/save')  ?>" id="editWarehouse" method="post">
                            <div class="row">

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editWarehouseName"> Name <small class="text-danger">*</small></label>
                                        <input id="editWarehouseName" type="text" class="form-control" name="name" placeholder="Enter Warehouse name">
                                        <input type="hidden" name="id" id="editWarehouseId">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="editWarehouseCountry"> Country <small class="text-danger">*</small></label>
                                        <input id="editWarehouseCountry" type="text" class="form-control" name="country" placeholder="Enter Country name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group ">
                                        <label for="editWarehouseCity"> City <small class="text-danger">*</small></label>
                                        <input id="editWarehouseCity" type="text" class="form-control" name="city" placeholder="Enter City name">
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="editWarehouseZip_code"> Zip_code <small class="text-danger">*</small></label>
                                        <input id="editWarehouseZip_code" type="text" class="form-control" name="zip_code" placeholder="Enter zip_code name">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="editWarehouseAddress">Address</label><span class="asterisk text-danger"> *</span>
                                        <textarea name="address" class="form-control" id="editWarehouseAddress" placeholder="Enter Address"></textarea>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <?= labels('submit', 'Submit') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<!-- sync All products to one warehouse -->

<div class="modal fade" id="syncProductModel" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="syncProductModelLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="syncProductLabel"><?= labels('sync-all-products-to-one-warehouse', 'Sync all products to one warehouse') ?></h5>
                <div class="mx-2">
                    <button type="button" class="btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#syncProductModel" data-toggle="tooltip" data-bs-placement="bottom" title="All products of current business will be added to selected warehouse">
                        <i class="fas fa-info"></i>
                    </button>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md">
                        <form data-action="<?= base_url('admin/warehouse/sync-all-all-products')  ?>" id="syncAllProductToWarehouse">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="editWarehouseName"> Select Warehouse <small class="text-danger">*</small></label>
                                        <select class="form-control" id="warehouse_id" name="warehouse_id">
                                            <option value="" selected>Select warehouse</option>
                                            <?php foreach ($warehouses as $warehouse) { ?>
                                                <option value="<?= $warehouse['id'] ?>"><?= $warehouse['name'] ?></option>
                                            <?php  } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <button type="submit" class="btn btn-primary">
                                    <?= labels('sync', 'Sync') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>