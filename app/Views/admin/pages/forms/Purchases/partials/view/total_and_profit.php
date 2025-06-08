<div class="row">
    <div class="col">
        <h6 class="h6"><strong><?= labels('total', 'Total') ?></strong></h6>
        <h4 class="text-info h6 m-1 px-2" id="sub_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="total" id="total">
    </div>
    <div class="col">
        <h6 class="h6"><strong><?= labels('sell_total', 'Sell Total') ?></strong></h6>
        <h4 class="text-black h6 m-1 px-2" id="sell_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="total" id="sell_total">
    </div>
    <div class="col">
        <h6 class="h6"><strong><?= labels('estimated_profit', 'Estimated Profit') ?></strong></h6>
        <h4 class="text-success h6 m-1 px-2" id="profit_total" data-currency="<?= $currency ?>"></h4>
        <input type="hidden" name="total" id="profit_total">
    </div>
</div>