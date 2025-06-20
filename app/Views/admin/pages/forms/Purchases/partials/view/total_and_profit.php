<div class="row mb-3">
    <div class="col-md-6">
        <div class="form-group">
            <label for="currency_selector"><?= labels('display_currency', 'Display Currency') ?></label>
            <select class="form-control" id="currency_selector" name="display_currency">
                <option value="base"><?= labels('base_currency', 'Base Currency') ?></option>
            </select>
            <small class="form-text text-muted"><?= labels('currency_conversion_note', 'Select currency to view totals in different currency') ?></small>
        </div>
    </div>
</div>

<div class="row">
    <div class="col">
        <h6 class="h6"><strong><?= labels('total', 'Total') ?></strong></h6>
        <h4 class="text-info h6 m-1 px-2" id="sub_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="total" id="total" value="<?= isset($purchase['total']) ? esc($purchase['total']) : '' ?>">
    </div>
    <div class="col">
        <h6 class="h6"><strong><?= labels('sell_total', 'Sell Total') ?></strong></h6>
        <h4 class="text-black h6 m-1 px-2" id="sell_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="sell_total" id="sell_total_input" value="<?= isset($purchase['sell_total']) ? esc($purchase['sell_total']) : '' ?>">
    </div>
    <div class="col">
        <h6 class="h6"><strong><?= labels('estimated_profit', 'Estimated Profit') ?></strong></h6>
        <h4 class="text-success h6 m-1 px-2" id="profit_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="profit_total" id="profit_total_input" value="<?= isset($purchase['profit_total']) ? esc($purchase['profit_total']) : '' ?>">
    </div>
</div>