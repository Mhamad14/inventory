<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('brand', 'Brand') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrandModal" data-toggle="tooltip" data-bs-placement="bottom" title="<?= labels('add-brand', 'Add Brand') ?> ">
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
                                <table class="table table-hover table-borderd" id="brand_table" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "tax-list"}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/brand/brand-table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="description" data-sortable="true"><?= labels('city', 'City') ?></th>

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

<div class="modal fade" id="addBrandModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="addBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBrandModalLabel"><?= labels('add-brand', 'Add Brand') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md">

                        <form action="<?= base_url('admin/brand/add')  ?>" id="add_brand" method="post">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name"> Name <small class="text-danger">*</small></label>
                                        <input id="name" type="text" class="form-control" name="name" placeholder="Enter Warehouse name">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label for="country"> Description <small class="text-danger">*</small></label>
                                        <textarea name="description" class="form-control" placeholder="Enter Description "></textarea>

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
<div class="modal fade" id="editBrandModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="editBrandModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBrandModalLabel"><?= labels('edit-brand', 'Edit Brand') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md">

                        <form action="<?= base_url('admin/brand/update')  ?>" id="update_brand" method="post">
                            <input type="hidden" name="brand_id" id="brand_id">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="name"> Name <small class="text-danger">*</small></label>
                                        <input id="edit_brand_name" type="text" class="form-control" name="name" placeholder="Enter Warehouse name">
                                    </div>
                                </div>

                                <div class="col-md-12">
                                    <div class="form-group ">
                                        <label for="country"> Description <small class="text-danger">*</small></label>
                                        <textarea name="description" class="form-control" id="edit_brand_description" placeholder="Enter Description "></textarea>

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