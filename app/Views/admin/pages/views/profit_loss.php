<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('profit_loss', 'Profit Loss') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="col-md">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date"><?= labels('date_range_filter', "Date Range Filter") ?></label>
                                <input type="text" name="daterange" id="date_profit_loss" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <button class="btn btn-danger btn-small p-2 mb-1  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
                            <button type="button" class="btn btn-primary m-4 " id="apply">
                                Apply
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv','json']" data-export-options='{"fileName": "Profit Loss","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" id="profit_loss_table" data-search-highlight="true" data-url="<?= base_url('admin/profit_loss/profit_loss_table'); ?>" data-side-pagination="server" data-query-params="pl_query">
                    <thead>
                        <tr>
                            <th data-field="vendor_id" data-sortable="true" data-visible="false"><?= labels('vendor_id', 'Vendor Id') ?></th>
                            <th data-field="business_id" data-sortable="true" data-visible="false"><?= labels('business_id', 'Business Id') ?></th>
                            <th data-field="sales" data-sortable="true" data-visible="true"><?= labels('sales', 'Sales (+)') ?></th>
                            <th data-field="purchases" data-sortable="true" data-visible="true"><?= labels('purchases ', 'Purchases (-)') ?></th>
                            <th data-field="expenses" data-sortable="true" data-visible="true"><?= labels('expenses', 'Expenses (-)') ?></th>
                            <th data-field="amount_collected" data-sortable="true" data-visible="true"><?= labels('amount_collected', 'Amount Collected') ?></th>
                            <th data-field="outstanding_total" data-sortable="true" data-visible="true"><?= labels('outstanding_total', 'Outstanding Total (Sales - Amount Collected ) ') ?></th>
                            <th data-field="total" data-sortable="true" data-visible="true"><?= labels('total', 'Total') ?></th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
</div>
</section>
</div>  