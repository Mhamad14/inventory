  <div class="main-content">
      <section class="section">
          <div class="section-header">
              <h1><?= labels('generate_barcode', 'Generate Barcode') ?></h1>
          </div>
          <div class="row">
              <div class="col-md">
              </div>
          </div>
          <div class="row">
              <div class="col-md">
                  <div class="text-danger" class="alert alert-danger" id="add_subscription_result"> </div>
              </div>
          </div>
          <?php
            $session = session();
            if ($session->has("message")) { ?>
              <div class="text-danger"><?= session("message"); ?></label></div>
          <?php } ?>
          <div class="section-body">
              <div class="row mt-sm-4">
                  <div class='col-md-12'>
                      <div class="card">
                          <div class="card-body">
                              <h2 class="section-title"> <?= labels('generate_barcode_here', 'Generate Barcode Here') ?>!</h2>
                              <div class="mt-3">
                                  <form method="post" id="register_customer_form">
                                      <div class="row">
                                          <div class="form-group col-md">
                                              <label for="product_name"><?= labels('product_name', 'Product Name') ?></label><span class="asterisk text-danger"> *</span>
                                              <select class="select_product form-control selectric" name="products_name" id="products_name">
                                                  <option value="">Select</option>
                                                  <?php foreach ($products as $item) :   $barcode_value = isset($item['barcode']) && !empty($item['barcode']) ? $item['barcode'] : $item['id']    ?>
                                                      <option value="<?= $barcode_value  ?>"><?= $item['name'] ?>-<?= $item['variant_name'] ?></option>
                                                  <?php endforeach ?>
                                              </select>
                                          </div>
                                          <div class="form-group col-md">
                                              <label for="identity"><?= labels('quantity', 'Quantity') ?> </label><span class="asterisk text-danger"> *</span>
                                              <input type="number" class="form-control" id="quantity" min="0" placeholder="Enter Quantity of Barcode You Want To Generate" name="quantity">
                                          </div>
                                      </div>
                                      <button type="button" class="btn btn-primary" id="generate-barcode" name="generate-barcode" value="generate"><?= labels('generate', 'Generate') ?></button>
                                      <button type="button" class=" btn btn-danger" id="barcode-reset" value="reset" /><?= labels('reset', 'Reset') ?></button>
                                      <button type="button" class=" btn btn-info" id="barcode-print" value="print" " /><?= labels('print', 'Print') ?></button>
                                      <div id="printDiv" class="printDiv">
                                          <div class="mt-3 " id="bar-gn">
                                          </div>
                              </div>
                          </div>
                          </form>
                      </div>
                  </div>
              </div>
          </div>
  </div>
  </div>