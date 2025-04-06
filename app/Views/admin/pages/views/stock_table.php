<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('manage_stock', 'Manage Stock') ?></h1>
            <div class="section-header-breadcrumb">
                <div class="btn-group mr-2 no-shadow">
                    <input type="hidden" id="business_id" value="<?= $business_id ?>">
                    <a class="btn btn-primary text-white" id="add_service_btn" href="<?= base_url('admin/services/Add_service'); ?>"><i class="fas fa-plus"></i><?= labels('add_service', 'Add Service') ?></a>
                </div>
            </div>
        </div>
        <?= session("message") ?>
        <div class="section">
            <div class="section-body">
                <div class="row">
                    <div class="col-12">
                        <div class="card">
                            <div class="card-body">
                                <table class="table table-hover table-borderd" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/products/stock_table/'.$flag); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="stock_params">
                                    <thead>
                                        <tr>
                                            <th data-field="product_id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                            <th data-field="product" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                                            <th data-field="variant_name" data-sortable="true" data-visible="true"><?= labels('variant_name', 'Variant') ?></th>
                                            <th data-field="stock" data-sortable="true" data-visible="true"><?= labels('stock', 'Stock(qty)') ?></th>
                                            <th data-field="qty_alert" data-sortable="true" data-visible="true"><?= labels('qty_alert', 'Quantity Alert') ?></th>
                                            <th data-field="stock_management" data-visible="false"><?= labels('stock_management', 'Stock Management') ?></th>
                                        </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!--container div  -->
    </section>
</div>