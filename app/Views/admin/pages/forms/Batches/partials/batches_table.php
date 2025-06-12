 <div class="col-12">
     <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "purchase-batches-list","ignoreColumn": ["action"]}' id="form_batches_items" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200]" data-url="<?= base_url('admin/batches/batches_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="batches_items_query" data-show-index="true">
         <thead>
             <tr>
                 <th data-field="id" data-sortable="true" data-visible="false" data-card-visible="false"><?= labels('id', 'id') ?></th>
                 <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                 <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                 <th data-field="quantity" data-sortable="true" data-visible="true" data-width="20"><?= labels('qty', 'Qty') ?></th>
                 <th data-field="cost_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('cost_price', 'Cost Price') ?></th>
                 <th data-field="sell_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('sell_price', 'Sell Price') ?></th>
                 <th data-field="expire" data-editable="true" data-sortable="true" data-visible="true"><?= labels('expiration_date', 'Expiration Date') ?></th>
                 <th data-field="discount" data-width="40" data-sortable="true" data-visible="true"><?= labels('discount', 'Discount') . "<small> $currency</small>" ?></th>
                 <th data-field="total" data-sortable="true" data-visible="true"><?= labels('sub_total', 'SubTotal') ?></th>
                 <th data-field="status" data-sortable="true" data-visible="true"><?= labels('status', 'Status') ?></th>
                 <th data-field="actions" data-sortable="true" data-visible="true"><?= labels('actions', 'Actions') ?></th>

                 <!-- <th data-field="hidden_inputs" data-visible="false"></th> -->
             </tr>
         </thead>
     </table>
 </div>