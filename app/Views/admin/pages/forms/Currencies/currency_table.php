<div class="main-content">
    <section class="section">
        
        <?= view('common_partials/page_header', [ 'header_label' => labels('currencies', 'Currencies'),  'btn_url' => base_url('admin/currency/add'),'btn_label' => labels('add_currency', 'Add Currency'),'btn_icon' => 'fas fa-plus']) ?>
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
                    <div class="table-responsive">
                        <table class="table table-hover table-borderd" id="currency_table"
                            data-show-export="true"
                            data-export-types="['txt','excel','csv']"
                            data-export-options='{"fileName": "currencies-list","ignoreColumn": ["action"]}'
                            data-auto-refresh="true"
                            data-show-columns="true"
                            data-show-toggle="true"
                            data-show-refresh="true"
                            data-toggle="table"
                            data-search-highlight="true"
                            data-server-sort="false"
                            data-page-list="[5, 10, 25, 50, 100, 200, All]"
                            data-url="<?= base_url('admin/currency/currency_table') ?>"
                            data-side-pagination="server"
                            data-pagination="true"
                            data-search="true"
                            data-server-sort="false">
                            <thead>
                                <tr>
                                    <th data-field="id" data-sortable="true">#</th>
                                    <th data-field="code" data-sortable="true"><?= labels('code', 'Code') ?></th>
                                    <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                    <th data-field="symbol" data-sortable="true"><?= labels('symbol', 'Symbol') ?></th>
                                    <th data-field="symbol_position" data-sortable="true"><?= labels('symbol_position', 'Symbol Position') ?></th>
                                    <th data-field="decimal_places" data-sortable="true"><?= labels('decimal_places', 'Decimals') ?></th>
                                    <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                                    <th data-field="is_base" data-sortable="true"><?= labels('is_base', 'Base') ?></th>
                                    <th data-field="action" data-sortable="false"><?= labels('action', 'Action') ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<?= view('admin/pages/forms/Currencies/js/currency_table') ?>