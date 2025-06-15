 <div class="row">
     <div class="col-12">
         <button id="remove" class="btn btn-danger" disabled>Delete</button>
         <table class='table-striped' data-toolbar="#remove" id='purchase_order' data-toggle="table" data-click-to-select="true" data-toggle="table" data-url="" data-click-to-select="true" data-page-list="[5, 10, 20, 50, 100, 200]" data-page-size="20" data-show-columns="true" data-mobile-responsive="true" data-toolbar="#toolbar" data-maintain-selected="true" data-query-params="queryParams" data-pagination="true">
             <thead>
                 <tr>
                     <th data-field="state" data-checkbox="true" data-width="20"></th>
                     <th data-field="id" data-sortable="true" data-visible="false" data-card-visible="false"><?= labels('id', 'id') ?></th>
                     <th data-field="variant_ids" data-sortable="true" data-visible="false" data-card-visible="false"><?= labels('variant_ids', 'variant_ids') ?></th>
                     <th data-field="image" data-sortable="true" data-visible="true"><?= labels('image', 'Image') ?></th>
                     <th data-field="name" data-sortable="true" data-visible="true"><?= labels('name', 'Name') ?></th>
                     <th data-field="quantity" data-sortable="true" data-visible="true"><?= labels('qty', 'Qty') ?></th>
                     <th data-field="price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('cost_price', 'Cost Price') ?></th>
                     <th data-field="sell_price" data-editable="true" data-sortable="true" data-visible="true"><?= labels('sell_price', 'Sell Price') ?></th>
                     <th data-field="expire" data-editable="true" data-sortable="true" data-visible="true"><?= labels('expiration_date', 'Expiration Date') ?></th>
                     <th data-field="discount" data-sortable="true" data-visible="true"><?= labels('discount', 'Discount') . "<small> $currency</small>" ?></th>
                     <th data-field="total" data-sortable="true" data-visible="true"><?= labels('sub_total', 'SubTotal') ?></th>
                     <th data-field="hidden_inputs" data-visible="false"></th>
                 </tr>
             </thead>
         </table>
     </div>
 </div>