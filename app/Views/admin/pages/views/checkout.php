     <div class="main-content">
         <section class="section">
             <div class="section-header">
                 <h1>Checkout</h1>
             </div>
             <div class="row">
                 <div class="col-md offset-md-3">
                     <div class="col-md-8">
                         <input type="hidden" id="plan_id" value="<?= $package['id'] ?>">
                         <input type="hidden" id="logo" value="<?= base_url($logo); ?>">
                         <input type="hidden" id="price" value="<?= $tenures['discounted_price'] ? $tenures['discounted_price'] : $tenures['price']  ?>">
                         <input type="hidden" id="tenure_id" value="<?= $tenure_id ?>">
                         <input type="hidden" id="user_id" value="<?= $user_id ?>">
                         <?php if ($razorpay) { ?>
                             <input type="hidden" id="razorpay_payment_id" value>
                             <input type="hidden" id="razorpay_key_id" value='<?= $razorpay_key ?>'>
                             <input type="hidden" id="razorpay_signature" value>
                         <?php } ?>
                         <?php if ($flutterwave) { ?>

                             <input type="hidden" name="flutterwave_public_key" id="flutterwave_public_key" value="FLWPUBK_TEST-1ffbaed6ee3788cd2bcbb898d3b90c59-X" />
                             <input type="hidden" id="flutterwave_currency_symbol" value="NGN" />
                             <input type="hidden" name="flutterwave_transaction_id" id="flutterwave_transaction_id" value="" />
                             <input type="hidden" name="flutterwave_transaction_ref" id="flutterwave_transaction_ref" value="" />
                             <input type="hidden" name="vendor_name" id="vendor_name" value="<?= $vendor_name ?>" />
                             <input type="hidden" name="phone" id="phone" value="<?= $phone ?>" />
                             <input type="hidden" name="email" id="email" value="<?= $email ?>" />

                         <?php } ?>

                         <?php if ($stripe) { ?>
                             <input type="hidden" id="stripe_key" value='<?= $stripe_key ?>'>
                         <?php } ?>

                         <input type="hidden" id="razorpay_currency" value='<?= $razorpay_currency ?>'>
                         <input type="hidden" id="stripe_client_secret" value>
                         <input type="hidden" id="razorpay_order_id" value>
                         <input type="hidden" id="app_name" value="subscription">
                         <div class="pricing pricing-highlight shadow">
                             <div class="pricing-title">
                                 <?= $package['title'] ?>
                             </div>
                             <div class="pricing-padding">
                                 <div class="pricing-price">
                                     <div><?= $currency ?><span><?= $tenures['discounted_price'] ? $tenures['discounted_price'] : $tenures['price']  ?></span></div>
                                     <div class="col-md-4 offset-md-4">
                                         <span><?= $tenures['tenure'] ?></span>
                                     </div>
                                 </div>
                                 <div class="pricing-details" id="discount_price<?= $package['id'] ?>">
                                     <div class="pricing-item">
                                         <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                         <div class="pricing-item-label"> <?= "No of businesses " . $package['no_of_businesses']; ?></div>
                                     </div>
                                     <div class="pricing-item">
                                         <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                         <div class="pricing-item-label"><?= "No of customers " . $package['no_of_customers']; ?></div>
                                     </div>
                                     <div class="pricing-item">
                                         <div class="pricing-item-icon"><i class="fas fa-check"></i></div>
                                         <div class="pricing-item-label"><?= "No of delivery boys " . $package['no_of_delivery_boys']; ?></div>
                                     </div>
                                     <div class="pricing-item">
                                         <div class="pricing-item-icon "><i class="fas fa-check"></i></div>
                                         <div class="pricing-item-label"><?= "No of products " . $package['no_of_products']; ?></div>
                                     </div>
                                     <div class="pricing-item ">
                                         <div class="pricing-item-icon <?= $tenures['discounted_price'] > 0 ? 'bg-success' : 'bg-danger' ?>"><i class="fas <?= $tenures['discounted_price'] > 0 ? ' fa-check' : ' fa-times' ?>"></i></div>
                                         <div class="pricing-item-label">Discounted price
                                             <span id="discount_price<?= $package['id'] ?>">
                                                 <?= $tenures['discounted_price'] ?></span>
                                         </div>
                                     </div>
                                 </div>

                             </div>

                             <div class="mb-2">
                                 <?= labels('select_payment_type', "Select Payment Type") ?> :-
                             </div>
                             <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                 <?php
                                    if ($razorpay) {
                                    ?>
                                     <label class="btn btn-primary">
                                         <input type="radio" id="razorpay" value="razorpay" name="payment_type" value="razorpay" aria-label="Radio button for following text input"> Razorpay
                                     </label>
                                 <?php } ?>
                                 <?php
                                    if ($flutterwave) {
                                    ?>
                                     <label class="btn btn-primary">
                                         <input type="radio" id="flutterwave" value="flutterwave" name="payment_type" value="flutterwave" aria-label="Radio button for following text input"> Flutterwave
                                     </label>
                                 <?php }
                                    if ($stripe) { ?>

                                     <label class="btn btn-primary">
                                         <input type="radio" name="payment_type" value="stripe" id="stripe"> Stripe
                                     </label>
                                 <?php } ?>

                             </div>
                             <div id="stripe_div" class="px-4 pt-4">
                                 <div class="form-group">
                                     <div class="form-control">
                                         <div id="stripe-card">
                                             <!-- A Stripe Element will be inserted here. -->
                                         </div>
                                     </div>
                                 </div>
                             </div>

                             <div class="pricing-cta p-4">
                                 <button class="btn btn-primary" id="buy_package"><?= labels('buy_now', "Buy Now") ?></a>
                             </div>
                         </div>


                         <div class="modal fade" id="loadMe" tabindex="-1" role="dialog" aria-labelledby="loadMeLabel">
                             <div class="modal-dialog modal-sm" role="document">
                                 <div class="modal-content">
                                     <div class="modal-body text-center">
                                         <div class="loader">
                                             <div class="spinner-border text-light" role="status">
                                                 <span class="sr-only">Loading...</span>
                                             </div>
                                         </div>
                                         <div clas="loader-txt">
                                             <p>Please wait while we process your transaction. </p>
                                         </div>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
                 <div class="offset-1"></div>
             </div>
         </section>
     </div>