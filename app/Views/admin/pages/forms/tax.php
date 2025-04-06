<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('add_tax', 'Add Tax') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/tax'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('tax', 'Tax') ?>"   ><i class="fas fa-list"></i> </a>
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
                        <form method="post" id="tax_form" action="<?= base_url('admin/tax/save_tax'); ?>">
                            <div class="row">
                                <div class="form-group col">
                                    <label for="name"> <?= labels('name', 'Name') ?> <small class="text-danger">*</small></label>
                                    <input id="name" type="text" class="form-control" name="name" placeholder="Ex. GST@2.5 " value="<?= !empty($tax) && !empty($tax['name']) ? $tax['name'] : "" ?>" autofocus>
                                    <input type="hidden" id="tax_id" name="tax_id" value="<?= !empty($tax) && !empty($tax['id']) ? $tax['id'] : "" ?>">
                                </div>
                                <div class="form-group col-md">
                                    <label for="percentage"> <?= labels('percentage', 'Percentage') ?> <small class="text-danger">*</small></label>
                                    <input type="text" name="percentage" id="percentage" class="form-control" placeholder="Ex 2.5% etc.." value="<?= !empty($tax) && !empty($tax['percentage']) ? $tax['percentage'] : "" ?>">
                                </div>
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label for="status" class="custom-switch  p-35">

                                            <?php if (!empty($tax['status']) && $tax['status'] == "1") { ?>
                                                <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                            <?php } elseif (isset($tax['status']) && $tax['status'] == "0") { ?>
                                                <input type="checkbox" name="status" id="status" class="custom-switch-input">
                                            <?php } else { ?>
                                                <input type="checkbox" name="status" id="status" class="custom-switch-input" checked>
                                            <?php } ?>
                                            <span class="custom-switch-indicator"></span>
                                            <span class="custom-switch-description"><?= labels('status', 'status') ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group col-md">
                                <button type="submit" class="btn btn-primary" value="tax">
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
                                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "tax-list"}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/tax/tax_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                    <thead>
                                        <tr>
                                            <th data-field="id" data-sortable="true"><?= labels('id', 'ID') ?></th>
                                            <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="percentage" data-sortable="true"><?= labels('percentage', 'Percentage') ?></th>
                                            <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
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