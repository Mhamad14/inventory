<div class="modal" id="status_modal">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4 class="modal-title"><?= labels('create_status', "Create Status") ?></h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <!-- Modal body -->
            <div class="modal-body">
                <form method="post" action='<?= base_url('admin/orders/create_status') ?>' id="create_status">
                    <div class="form-group">
                        <label for="status"><?= labels('status_name', "Status Name") ?></label><span class="asterisk text-danger"> *</span>
                        <input type="text" class="form-control" id="status" placeholder="Ex. Ordered ,pending,delivered" name="status">
                    </div>
                    <div class="form-group">
                        <label for="operation"><?= labels('operation', "Operation") ?></label><span class="asterisk text-danger"> *</span>
                        <button type="button" class="btn btn-sm" data-bs-toggle="tooltip" data-bs-placement="right" title="Ex. Debit From wallet balance, Credit balance in wallet ,do nothing etc.">
                            <small>( <?= labels('what_to_do_with_wallet_balance', "what to do with wallet balance?") ?>)</small>
                        </button>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="do_nothing" name="operation" value="0" class="custom-control-input" checked>
                            <label class="custom-control-label" for="do_nothing"><?= labels('do_nothing', "Do nothing") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="credit" name="operation" value="1" class="custom-control-input">
                            <label class="custom-control-label" for="credit"><?= labels('credit_wallet_balance', "Credit wallet balance") ?></label>
                        </div>
                        <div class="custom-control custom-radio">
                            <input type="radio" id="debit" name="operation" value="2" class="custom-control-input">
                            <label class="custom-control-label" for="debit"><?= labels('debit_wallet_balance', "Debit wallet balance") ?></label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="save-register-result-btn" name="register" value="Save"><?= labels('save', 'Save') ?></button>
                    <div class="mt-3">
                        <div id="save-register-result"></div>
                    </div>
                </form>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>