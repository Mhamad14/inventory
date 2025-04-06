<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('top_selling_products', 'Top Selling Products') ?></h1>
            <div class="section-header-breadcrumb">
            </div>
        </div>
        <?= session("message") ?>
        <div class="card">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="date"><?= labels('date_range_filter', "Date Range Filter") ?></label>
                            <input type="text" name="daterange" id="date_top_selling_products" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button class="btn btn-danger btn-small  mb  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary mt-4  m-lg-4 py-2" id="apply">
                            Apply
                        </button>
                    </div>
                    <div class="row">
                        <table class="table table-hover table-borderd" data-show-export="true" data-export-types="['txt','excel','csv','json']" data-export-options='{"fileName": "Top Selling Products","ignoreColumn": ["action"]}' data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/top_selling_products/top_selling_products_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="top_selling_products_query" id="top_selling_products_table">
                            <thead>
                                <tr>
                                    <th data-field="product_id" data-sortable="true" data-visible="true"><?= labels('product_id', 'Product ID') ?></th>
                                    <th data-field="product_name" data-sortable="true" data-visible="true"><?= labels('product_name', 'Product Name') ?></th>
                                    <th data-field="price" data-sortable="true" data-visible="true"><?= labels('price', 'Price') ?></th>
                                    <th data-field="total_sales" data-sortable="true" data-visible="true"><span class=" badge bg-dark"><?= labels('total_sales', 'Total Sales') ?></span></th>
                                    <th data-field="total_amount" data-sortable="true" data-visible="true"><?= labels('total_amount', 'Total Amount') ?></th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
    </section>
</div>