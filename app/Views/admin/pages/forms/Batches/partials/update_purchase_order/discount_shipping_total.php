 <div class="row">
     <div class="col-md-4 col-lg-4 col-sm-12">
         <div class="form-group">
             <label for="batch_discount"><?= labels('discount', 'Discount') ?></label>
             <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text">
                         <span><?= $currency ?></span>
                     </div>
                 </div>
                 <input type="text" class="form-control" name="order_discount" id="batch_discount" value="<?= $purchase['discount'] ?? 0 ?>">
             </div>
             <small id="discount_feedback" class="form-text text-muted"></small>

         </div>
     </div>
     <div class="col-md-4 col-lg-4 col-sm-12">
         <div class="form-group">
             <label for="batch_shipping"><?= labels('shipping', 'Shipping') ?></label>
             <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text">
                         <span><?= $currency ?></span>
                     </div>
                 </div>
                 <input type="text" class="form-control" name="shipping" id="batch_shipping" value="<?= $purchase['delivery_charges'] ?? 0 ?>">
             </div>
         </div>
     </div>
     <div class="col-md-4 col-lg-4 col-sm-12 mt-3">
         <div class="form-group text-center">
             <label for="final_total"><strong><?= labels('total', 'Total') ?></strong></label>
             <h4 class="text-info h6 m-1 px-2" id="final_total" data-currency="<?= $currency ?>">
                 <?= currency_location(decimal_points($purchase['total'])) ?? 0 ?>
             </h4>
             <input type="hidden" name="final_total" id="final_total" value="<?= $purchase['total'] ?>">
         </div>

     </div>

 </div>