<div class="row">
    <div class="col-md-12 mt-3">
        <div class="form-group">
            <label for="payment_status_item">Payment Status</label>
            <select class="form-control" id="payment_status_item" name="payment_status">
                <!-- select payment status -->
                <option value="pending" <?= (isset($purchase['payment_status']) && $purchase['payment_status'] == 'pending') ? 'selected' : '' ?>>Select Payment Status</option>
                <option value="fully_paid" <?= (isset($purchase['payment_status']) && $purchase['payment_status'] == 'fully_paid') ? 'selected' : '' ?>>Fully Paid</option>
                <option value="partially_paid" <?= (isset($purchase['payment_status']) && $purchase['payment_status'] == 'partially_paid') ? 'selected' : '' ?>>Partially Paid</option>
                <option value="unpaid" <?= (isset($purchase['payment_status']) && $purchase['payment_status'] == 'unpaid') ? 'selected' : '' ?>>Unpaid</option>
                <option value="cancelled" <?= (isset($purchase['payment_status']) && $purchase['payment_status'] == 'cancelled') ? 'selected' : '' ?>>Cancelled</option>
            </select>
        </div>
        <div class="form-group">
            <label>Payments</label>
            <table class="table table-bordered" id="payments_table">
                <thead>
                    <tr>
                        <th>Currency</th>
                        <th>Amount</th>
                        <th>Exchange Rate</th>
                        <th>Converted IQD</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="payment-row">
                        <td>
                            <select class="form-control payment-currency" name="payments[0][currency_id]">
                                <!-- Options will be populated by JS -->
                            </select>
                        </td>
                        <td>
                            <input type="text" class="form-control payment-amount format-number" name="payments[0][amount]" value="" />
                        </td>
                        <td>
                            <input type="number" step="0.0001" class="form-control payment-rate format-number" name="payments[0][rate_at_payment]" min="0" readonly />
                        </td>
                        <td>
                            <input type="hidden" class="payment-converted-iqd-raw" name="payments[0][converted_iqd]" value="" />
                            <span class="form-control-static payment-converted-iqd-display format-number">0</span>
                        </td>
                        <td>
                            <input type="text" class="form-control payment-date" name="payments[0][paid_at]" />
                        </td>
                        <td>
                            <button type="button" class="btn btn-danger btn-sm remove-payment-row" disabled>&times;</button>
                        </td>
                        <input type="hidden" name="payments[0][payment_type]" value="cash" />
                    </tr>
                </tbody>
            </table>
            <button type="button" class="btn btn-success btn-sm mt-3" id="add_payment_row">Add Payment</button>
            
        </div>
    </div>
</div>