 <div class="col-12">
     <table class="table table-bordered table-hover" data-id-field="id" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "purchase-batches-list","ignoreColumn": ["action"]}' id="form_returned_batches_items" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200]" data-url="<?= base_url('admin/batches/Returned_batches_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="returned_batches_items_query" data-show-index="true">
         <thead>
             <tr>
                 <th data-field="id" data-search="true" data-sortable="true" data-visible="false" data-card-visible="false"><?= labels('id', 'id') ?></th>
                 <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                 <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                 <th data-field="quantity" data-sortable="true" data-visible="true" data-width="20"><?= labels('returned_qty', 'Returned Qty') ?></th>
                 <th data-field="cost_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('cost_price', 'Cost Price') ?></th>
                 <th data-field="return_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('return_price', 'Return Price') ?></th>
                 <th data-field="return_date" data-editable="true" data-sortable="true" data-visible="true"><?= labels('expiration_date', 'Expiration Date') ?></th>
                 <th data-field="return_total" data-width="40" data-sortable="true" data-visible="true"><?= labels('return_total', 'Return Total')?></th>
                 <th data-field="return_reason" data-sortable="true" data-visible="true"><?= labels('return_reason', 'Return Reason') ?></th>
                 <th data-field="actions" data-sortable="true" data-visible="true"><?= labels('actions', 'Actions') ?></th>

                 <!-- <th data-field="hidden_inputs" data-visible="false"></th> -->
             </tr>
         </thead>
     </table>
 </div>