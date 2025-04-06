<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('expenses', 'Expenses') ?></h1>
            <div class="section-header-breadcrumb">
                <div class=" mr-2 no-shadow">
                  
                    <input type="hidden" id="business_id" value="<?= $business_id ?>">
                    <a class="btn btn-primary text-white" data-bs-toggle="modal" data-bs-target="#expenses_modal" id=""  data-toggle="tooltip" data-bs-placement="bottom" title=" <?= labels('add_expense', 'Add Expense') ?>"  ><i class="fas fa-plus"></i> </a>
                </div>
                <div class="btn-group mr-2 no-shadow">
                    <input type="hidden" id="business_id" value="<?= $business_id ?>">
                </div>
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "products-list","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/expenses/expenses_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" >
                        <thead>
                            <tr>
                                <th data-field="business_name" data-sortable="true" data-visible="false"><?= labels('business_name', 'Business Name') ?></th>
                                <th data-field="vendor_name" data-sortable="true" data-visible="false"><?= labels('vendor_name', 'Vendor Name') ?></th>
                                <th data-field="id" data-sortable="true" data-visible="true"><?= labels('id', 'ID') ?></th>
                                <th data-field="expenses_name" data-sortable="true" data-visible="true"><?= labels('expenses_name', 'Expenses Name') ?></th>
                                <th data-field="amount" data-sortable="true" data-visible="true"><?= labels('amount', 'Amount') ?></th>
                                <th data-field="note" data-sortable="true" data-visible="true"><?= labels('note', 'Note') ?></th>
                                <th data-field="expenses_date" data-sortable="true" data-visible="true"><?= labels('expenses_date', 'Expenses Date') ?></th>

                                <th data-field="action" data-visible="true"><?= labels('action', 'Action') ?></th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>

        </div>
    </section>
</div>
<!-- The Modal -->
<div class="modal  fade " id="expenses_modal" >
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Modal Header -->
            <div class="modal-header">
                <h2 class="modal-title"> <?= labels('add_expenses', 'Add Expenses') ?> </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Modal body -->
            <div class="modal-body">
                 <form action="<?= base_url('admin/expenses/save') ?>" id="expenses_form" enctype="multipart/form-data" accept-charset="utf-8" method="POST">

                                        <div class="row">
                                            <input type="hidden" name="id" id="id" value="<?= !empty($expenses) && !empty($expenses['id']) ? $expenses['id'] : "" ?>">
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="expense_type"><?= labels('expenses_id', 'Expense Type') ?></label><span class="asterisk text-danger"> *</span>
                                                    <select class="form-control" name="expenses_type" id="expenses_type">
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="note"><?= labels('note', 'Note') ?></label><span class="asterisk text-danger"> *</span>
                                                    <input type="text" class="form-control" name="note" id="note" value="<?= !empty($expenses) && !empty($expenses['note']) ? $expenses['note'] : "" ?>" >
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="amount"><?= labels('amount', 'Amount') ?></label><span class="asterisk text-danger"> *</span>
                                                    <input type="text" class="form-control" name="amount" id="amount" value="<?= !empty($expenses) && !empty($expenses['amount']) ? $expenses['amount'] : "" ?>">
                                                </div>
                                            </div>
                                            <div class="col-md">
                                                <div class="form-group">
                                                    <label for="expenses_date"><?= labels('expense_date', 'Expense Date') ?></label><span class="asterisk text-danger"> *</span>
                                                    <input type="date" name="expenses_date" class="form-control" id="expenses_date" value="<?= !empty($expenses) && !empty($expenses['expenses_date']) ? $expenses['expenses_date'] : "" ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-primary">+ <?= labels('add', 'Add') ?></button>&nbsp;
                                        <button type="reset" class="btn btn-info"><?= labels('reset', 'Reset') ?></button>
                                </form>
            </div>

            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>