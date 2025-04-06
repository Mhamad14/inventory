<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('add_unit', 'Add Unit') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/units'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('units', 'Units') ?>"   ><i class="fas fa-list"></i> </a>
                </div>
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-red"><?php $message = session('message');
                                    echo $message['title']; ?></label></div>
        <?php }
        !empty($unit)  ?  $unit : "";
        ?>
        <div class="row">
            <div class="col-md">
                <div class="card">
                    <div class="card-body">
                        <form method="post" id="vendor_units_form" action="<?= base_url('admin/units/save_unit'); ?>">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="name"> <?= labels('name', 'Name') ?> <small class="text-danger">*</small></label>
                                    <input id="name" type="text" class="form-control" name="name" placeholder="Ex. kilogram gram etc.." value="<?= !empty($unit) && !empty($unit['name']) ? $unit['name'] : "" ?>" autofocus>
                                    <input id="unit_id" type="hidden" class="form-control" name="unit_id" value="<?= !empty($unit) && !empty($unit['id']) ? $unit['id'] : "" ?>" autofocus>
                                    <input id="vendor_id" type="hidden" class="form-control" name="vendor_id" value="<?= !empty($id)  ? $id : "" ?>" autofocus>
                                </div>
                                <div class="form-group col-md">
                                    <label for="symbol"> <?= labels('symbol', 'Symbol') ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="symbol" id="symbol" class="form-control" placeholder="Ex. kg g etc.." value="<?= !empty($unit) && !empty($unit['symbol']) ? $unit['symbol'] : "" ?>">
                                </div>
                                <div class="form-group col-md">
                                    <label for="parent_id"> <?= labels('parent_unit', 'Parent Unit') ?> <small class="text-danger">*</small></label>
                                    <select name="parent_id" id="parent_id" class="form-control">
                                        <option value="<?= !empty($unit) && !empty($unit['parent_id']) ? $unit['parent_id'] : "" ?>"><?= !empty($parent_unit) && !empty($parent_unit['name']) ? $parent_unit['name'] : "Select Unit" ?></option>
                                        <?php foreach ($units as $unit) { ?>
                                            <option value="<?= $unit['id'] ?>"><?= $unit['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="form-group col-md">
                                    <label for="conversion"> <?= labels('conversion', 'Conversion') ?> <small class="text-danger">*</small></label>
                                    <input type="number" class="form-control" id="conversion" name="conversion" placeholder="Ex. 1000g(unit) = 1kg(Parent unit)" value="<?= !empty($unit) && !empty($unit['conversion']) ? $unit['conversion'] : "" ?>">
                                </div>
                            </div>
                            <div class="form-group col-md">
                                <button type="submit" class="btn btn-primary" value="unit">
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
                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']"  data-export-options='{"fileName": "units-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/units/unit_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="symbol" data-sortable="true"><?= labels('symbol', 'Symbol') ?></th>
                                            <th data-field="parent_id" data-sortable="true"> <?= labels('parent_id', 'Parent ID') ?></th>
                                            <th data-field="conversion" data-sortable="true"><?= labels('conversion', 'Conversion') ?></th>
                                            <th data-field="action" ><?= labels('action', 'Action') ?></th>
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