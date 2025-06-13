 <div class="row">
     <div class="col-md">
         <div class="form-group">
             <label for="order_discount"><?= labels('discount', 'Discount') ?></label>
             <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text">
                         <span><?= $currency ?></span>
                     </div>
                 </div>
                 <input type="text" class="form-control" name="order_discount" id="order_discount">
             </div>
         </div>
     </div>
     <div class="col-md">
         <div class="form-group">
             <label for="shipping"><?= labels('shipping', 'Shipping') ?></label>
             <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text">
                         <span><?= $currency ?></span>
                     </div>
                 </div>
                 <input type="text" class="form-control" name="shipping" id="shipping">
             </div>
         </div>
     </div>
 </div>