<div x-data="variantForm" x-init="init()" class="p-3">
    <label>Search Product Variant</label>
    <select id="variant-select"></select>

    <template x-if="selected">
        <form id="add_variant_form" @submit.prevent="submitForm" class="mt-4">
            <h3 x-text="selected.text" class="font-bold mb-2"></h3>

            <input type="hidden" x-model="form.id">
            <input type="hidden" id="variant_id" x-model="form.variant_id">
            <input type="hidden" x-model="form.purchase_id">
            <input type="hidden" x-model="form.warehouse_id">

            <div class="form-group mb-2">
                <label for="add_quantity">Quantity</label>
                <input type="number"
                    x-model="form.quantity"
                    id="add_quantity"
                    class="form-control"
                    min="1"
                    value="1"
                    placeholder="Ex: 1"
                    @input="calculateTotals()">
            </div>
            <div class="form-group mb-2">
                <label>Cost Price</label>
                <input type="number"
                    id="add_cost_price"
                    x-model="form.cost_price"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="Ex: 0.00"
                    @input="calculateTotals()">
            </div>
            <div class="form-group mb-2">
                <label for="add_sell_price">Sell Price</label>
                <input type="number"
                    x-model="form.sell_price"
                    min="0"
                    id="add_sell_price"
                    step="0.01"
                    placeholder="Ex: 0.00"
                    class="form-control"
                    @input="calculateTotals()">
            </div>
            <div class="form-group mb-2">
                <label>Expire Date</label>
                <input type="text"
                    x-model="form.expire_date"
                    x-ref="expireDateInput"
                    id="add_expire_date"
                    class="form-control"
                    placeholder="YYYY-MM-DD">
            </div>
            <div class="form-group mb-2">
                <label>Discount</label>
                <input type="number"
                    id="add_discount"
                    x-model="form.discount"
                    class="form-control"
                    min="0"
                    step="0.01"
                    placeholder="Ex: 0.00"
                    @input="calculateTotals()">
            </div>
            <div class="form-group mb-2">
                <label for="add_status"><?= labels('status', 'Status') ?></label><span class="asterisk text-danger"> *</span>
                <div class="d-flex">
                    <select class="form-control mr-2" id="add_status" x-model="form.status" required>
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

            <!-- Totals Section -->
            <div class="row mb-2 mt-3">
                <div class="col-sm-12 col-md-6 col-lg-4 text-center">
                    <h6 class="h6 text-black">Cost Total</h6>
                    <h6 class="text-info" x-text="'$' + (form.cost_total?.toFixed(2) || '0.00')"></h6>
                </div>
                <div class="col-sm-12 col-md-6 col-lg-4 text-center">
                    <h6 class="h6 text-black">Sell Total</h6>
                    <h6 x-text="'$' + (form.sell_total?.toFixed(2) || '0.00')"></h6>
                </div>
                <div class="col-sm-12 col-md-12 col-lg-4 text-center">
                    <h6 class="h6 text-black">Estimated Profit</h6>
                    <h6 class="text-success" x-text="'$' + (form.profit?.toFixed(2) || '0.00')"></h6>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" :disabled="!isFormValid()">Save</button>
        </form>
    </template>
</div>