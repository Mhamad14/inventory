 <section class="section">
     <div class="card">
         <div class="card-body">
             <div class="row">
                 <div class="col-md-4 col-12 mb-3 mb-md-0">
                     <div class="row">
                         <label class="col-5 text-right text-muted col-form-label"><?= labels('order_id', 'Order Id:') ?></label>
                         <div class="col-7">
                             <p class="form-control-plaintext text-muted">#<?= $purchase['id'] ?? '' ?></p>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-4 col-12 mb-3 mb-md-0">
                     <div class="row">
                         <label class="col-5 text-right text-muted col-form-label"><?= labels('created_by', 'Created by:') ?></label>
                         <div class="col-7">
                             <p class="form-control-plaintext text-muted"><?= $purchase['creator'] ?? '' ?></p>
                         </div>
                     </div>
                 </div>
                 <div class="col-md-4 col-12">
                     <div class="row">
                         <label class="col-5 text-right text-muted col-form-label"><?= labels('created_at', 'Created At:') ?></label>
                         <div class="col-7">
                             <p class="form-control-plaintext text-muted"><?= $purchase['created_at'] ?? '' ?></p>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
 </section>