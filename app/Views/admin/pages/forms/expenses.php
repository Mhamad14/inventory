<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('expenses', 'Expenses') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <a class="btn btn-primary text-white" href="<?= base_url('admin/expenses'); ?>" class="btn"  ><i class="fas fa-list"></i> <?= labels('expenses', 'Expenses') ?></a>
                </div>
            </div>
        </div>
        <div class="section-body">
            <div class="row">
                <div class="col-md">
                    <div class="alert alert-danger d-none" id="add_subscription_result"> </div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-danger"><?= session("message"); ?></label></div>

            <?php } ?>


            <div class="card">
                <div class="card-body">
                    <div class="row mt-sm-4">
                        <div class='col-md-12'>
                            <h2 class="section-title"> <?= labels($from_title) ?> </h2>

                            <form action="<?= base_url('admin/expenses/save') ?>" id="expenses_form" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <div class="card-footer">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id" value="<?= !empty($expenses) && !empty($expenses['id']) ? $expenses['id'] : "" ?>">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="expense_type"><?= labels('expenses_id', 'Expense Type') ?></label><span class="asterisk text-danger"> *</span>
                                                <select class="form-control" name="expenses_type" id="expenses_type">
                                                    <option value="">Select Your Expense Type</option>

                                                    <?php foreach ($expenses_type as $type) {

                                                    ?>
                                                        <option value="<?=  $type['id']  ?>"  <?= $selected_expenses_id == $type['id'] ? 'selected' : ''; ?> > <?= $type['title'] ?> </option>
                                                    <?php } ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md">
                                            <div class="form-group">
                                                <label for="note"><?= labels('note', 'Note') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="note" id="note" value="<?= !empty($expenses) && !empty($expenses['note']) ? $expenses['note'] : "" ?>">
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
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</div>
</div>