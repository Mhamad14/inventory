<div class="form-group">
    <label for="status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
    <button type="button" class="btn btn-sm btn-success float-right mb-1" data-bs-toggle="modal" data-bs-target="#status_modal"><?= labels('add_status', 'Add Status') ?></button>
    <select class="form-control" id="status" name="status">
        <option value="">Select status</option>
        <?php if (!empty($status) && isset($status)) {
            foreach ($status as $val) { ?>
                <option value="<?= $val['id'] ?>" <?= (isset($purchase['status']) && $purchase['status'] == $val['id']) ? 'selected' : '' ?>><?= $val['status'] ?></option>
        <?php }
        } ?>
    </select>
</div>