<div class="main-content">
    <section class="section">

        <?= view('common_partials/page_header', ['header_label' => labels('currency', 'Currency'),  'btn_url' => base_url('admin/currency'), 'btn_label' => labels('currencies', 'Currencies'), 'btn_icon' => 'fas fa-list']) ?>

        <div class="section-body">
            <div class="row">
                <div class="col-md">
                    <div class="alert alert-danger d-none" id="add_currency_result"></div>
                </div>
            </div>
            <?php
            $session = session();
            if ($session->has("message")) { ?>
                <div class="text-danger"><?= session("message"); ?></div>
            <?php } ?>

            <div class="card">
                <div class="card-body">
                    <div class="row mt-sm-4">
                        <div class='col-md-12'>
                            <h2 class="section-title"><?= labels($from_title) ?></h2>

                            <form id="currency_form" onsubmit="return false;">
                                <?= csrf_field() ?>
                                <div class="card-footer">
                                    <div class="row">
                                        <input type="hidden" name="id" id="id" value="<?= !empty($currency) ? $currency['id'] : "" ?>">

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="code"><?= labels('code', 'Currency Code') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="code" id="code" value="<?= !empty($currency) ? $currency['code'] : "" ?>" maxlength="3">
                                                <small class="form-text text-muted">ISO 4217 code (e.g., USD, IQD, EUR)</small>
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="name"><?= labels('name', 'Currency Name') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="name" id="name" value="<?= !empty($currency) ? $currency['name'] : "" ?>">
                                            </div>
                                        </div>

                                        <div class="col-md-4">
                                            <div class="form-group">
                                                <label for="symbol"><?= labels('symbol', 'Symbol') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="text" class="form-control" name="symbol" id="symbol" value="<?= !empty($currency) ? $currency['symbol'] : "" ?>">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="symbol_position"><?= labels('symbol_position', 'Symbol Position') ?></label>
                                                <select class="form-control" name="symbol_position" id="symbol_position">
                                                    <option value="0" <?= (!empty($currency) && $currency['symbol_position'] == 0) ? 'selected' : '' ?>><?= labels('before', 'Before Amount') ?></option>
                                                    <option value="1" <?= (!empty($currency) && $currency['symbol_position'] == 1) ? 'selected' : '' ?>><?= labels('after', 'After Amount') ?></option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="decimal_places"><?= labels('decimal_places', 'Decimal Places') ?></label><span class="asterisk text-danger"> *</span>
                                                <input type="number" class="form-control" name="decimal_places" id="decimal_places" value="<?= !empty($currency) ? $currency['decimal_places'] : "2" ?>" min="0" max="4">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="is_base"><?= labels('is_base', 'Base Currency') ?></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="is_base" id="is_base" value="1" <?= (!empty($currency) && $currency['is_base'] == 1) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="is_base"><?= labels('set_as_base', 'Set as base currency') ?></label>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                <label for="status"><?= labels('status', 'Status') ?></label>
                                                <div class="custom-control custom-checkbox">
                                                    <input type="checkbox" class="custom-control-input" name="status" id="status" value="1" <?= (empty($currency) || !empty($currency['status'])) ? 'checked' : '' ?>>
                                                    <label class="custom-control-label" for="status"><?= labels('active', 'Active') ?></label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <button type="button" id="save_currency" class="btn btn-primary"><?= labels('save', 'Save') ?></button>&nbsp;
                                    <button type="button" class="btn btn-info" onclick="this.form.reset();"><?= labels('reset', 'Reset') ?></button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>


<?= view('admin/pages/forms/Currencies/js/save_currency') ?>