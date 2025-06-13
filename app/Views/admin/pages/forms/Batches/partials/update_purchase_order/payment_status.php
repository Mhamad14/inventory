<div class="row">
    <div class="col-md-6 col-lg-6 col-sm-12">
        <div class="form-group">
            <label class="payment_status_label" for="payment_status_item">Payment Status</label><span class="asterisk text-danger payment_status_label"> *</span>
            <select class="form-control payment_status" id="payment_status_item" name="payment_status">
                <option value="fully_paid" <?= $purchase['payment_status'] === 'fully_paid' ? 'selected' : '' ?>>Fully Paid</option>
                <option value="partially_paid" <?= $purchase['payment_status'] === 'partially_paid' ? 'selected' : '' ?>>Partially Paid</option>
                <option value="unpaid" <?= $purchase['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Unpaid</option>
                <option value="cancelled" <?= $purchase['payment_status'] === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
            </select>

            <?php $is_partial = $purchase['payment_status'] === 'partially_paid'; ?>

            <div class="amount_paid <?= $is_partial ? '' : 'd-none' ?>">
                <label for="amount_paid_item">Amount Paid</label><span class="asterisk text-danger"> *</span>
                <input type="number" class="form-control" id="amount_paid_item"
                    value="<?= $is_partial ? esc($purchase['amount_paid']) : '' ?>"
                    placeholder="0.00" name="amount_paid" min="0.00" step="0.01">
            </div>
        </div>
    </div>
    <div class="col-md-6 col-sm-12">
        <div class="form-group">
            <label for="status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
            <div class="d-flex">
                <select class="form-control mr-2" id="status" name="status">
                    <option value="">Select status</option>
                    <?php if (!empty($status_list)) {
                        foreach ($status_list as $val) {
                            $selected = ($purchase['status'] ?? '') == $val['id'] ? 'selected' : ''; ?>
                            <option value="<?= $val['id'] ?>" <?= $selected ?>><?= $val['status'] ?></option>
                    <?php }
                    } ?>
                </select>
                <button type="button" class="btn btn-icon btn-secondary" data-bs-toggle="modal" data-bs-target="#status_modal">
                    <i class="fas fa-plus"></i>
                </button>
            </div>
        </div>
    </div>


</div>