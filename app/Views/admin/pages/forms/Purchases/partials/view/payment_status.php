<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="payment_status_label" for="payment_status_item">Payment Status</label><span class="asterisk text-danger payment_status_label"> *</span>
            <select class="form-control payment_status" id="payment_status_item" name="payment_status">
                <option value="fully_paid" selected="">Fully Paid</option>
                <option value="partially_paid">Partially Paid</option>
                <option value="unpaid">Unpaid</option>
                <option value="cancelled">Cancelled</option>
            </select>
            <div class="amount_paid d-none" style="display: none;">
                <label for="amount_paid_item">Amount Paid</label><span class="asterisk text-danger"> *</span>
                <input type="number" class="form-control" id="amount_paid_item" value="" placeholder="0.00" name="amount_paid" min="0.00">
            </div>
        </div>
    </div>
</div>