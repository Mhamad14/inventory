  <div class="main-content">
      <section class="section">
          <div class="section-header">
              <h1> <?= labels('payment_reminder', 'Payment Reminder') ?></h1>
              <div class="section-header-breadcrumb">
                  <div class="btn-group mr-2 no-shadow">
                      <a class="btn btn-primary text-white" href="<?= base_url('admin/orders'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title="  <?= labels('pos_order', 'POS Order') ?>"    ><i class="fas fa-plus"></i></a>
                  </div>
                  <div class="btn-group mr-2 no-shadow">
                      <a class="btn btn-primary text-white" href="<?= base_url('admin/orders/sales_orders'); ?>" class="btn"  data-toggle="tooltip" data-bs-placement="bottom" title="<?= labels('sales_order', 'Sales Order') ?>"    ><i class="fas fa-plus-circle"></i> </a>
                  </div>
                  <!-- <div class="btn-group mr-2 no-shadow">
                        <a class="btn btn-primary text-white" href="<?= base_url('admin/payment_reminder'); ?>" class="btn"><i class="fas fa-bell"></i> <?= labels('payment_reminder', 'Payment Reminder') ?></a>
                    </div> -->
              </div>
          </div>
          <div class="card">
              <div class="card-body">
                  <div class="row">
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="order_type_filter"><?= labels('filter_orders', 'Filter Orders') ?></label>
                              <select name="order_type_filter" id="order_type_filter" class="form-control selectric">
                                  <option value="">-Select-</option>
                                  <option value="product">Products</option>
                                  <option value="service">Services</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="date_range"><?= labels('date_range_filter', 'Date Range') ?></label>
                              <input type="text" name="date_range" id="date_range" class="form-control">
                          </div>
                      </div>
                      <div class="col-md-1">
                          <button class="btn btn-danger btn-small p-2 mb-1  m-lg-4 mt-4 py-2" name="clear" id="clear"> Clear </button>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for="payment_status_filter"><?= labels('filter_by_payment_status', 'Filter by Payment Status') ?></label>
                              <select name="payment_status_filter" class="form-control selectric" id="payment_status_filter">
                                  <option value="">All</option>

                                  <option value="partially_paid">Partially Paid</option>
                                  <option value="unpaid">Unpaid</option>
                              </select>
                          </div>
                      </div>
                      <div class="col-md-2">
                          <div class="form-group">
                              <label for=""><?= labels('apply_filters', 'Apply filters') ?></label>
                              <button class="btn btn-primary d-block" id="filter">
                                  <?= labels('apply', 'Apply') ?>
                              </button>
                          </div>
                      </div>
                      <table class="table table-bordered table-hover" data-show-export="true" data-export-types="['txt','excel','csv']" data-export-options='{"fileName": "orders-list","ignoreColumn": ["action"]}' id="payment_reminder_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="true" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('admin/orders/payment_reminder_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-query-params="orders_query">
                          <thead>
                              <tr>
                                  <th data-field="id" data-sortable="true" data-visible="false"><?= labels('id', 'ID') ?></th>
                                  <th data-field="Order_date" data-sortable="true"><?= labels('order_date', 'Order Date') ?></th>
                                  <th data-field="customer_name" data-sortable="true"><?= labels('customer_name', 'Customer Name') ?></th>
                                  <th data-field="total" data-sortable="true" data-visible="true"><?= labels('total', 'Total') ?><?= "($currency)" ?></th>
                                  <th data-field="amount_paid" data-sortable="true" data-visible="true"><?= labels('amount_paid', 'Amount Paid') ?><?= "($currency)" ?></th>
                                  <th data-field="remaining_amount" data-visible="true"><?= labels('remaining_amount', 'Remaining Amount') ?><?= "($currency)" ?></th>
                                  <th data-field="delivery_charges" data-sortable="true" data-visible="false"><?= labels('delivery_charges', 'Delivery Charges') ?><?= "($currency)" ?></th>
                                  <th data-field="final_total" data-sortable="true"><?= labels('final_total', 'Final Total') ?><?= "($currency)" ?></th>
                                  <th data-field="payment_status" data-sortable="true"><?= labels('payment_status', 'Payment Status') ?></th>
                                  <th data-field="balance" data-visible="false" data-sortable="true"><?= labels('balance', 'Balance') ?><?= "($currency)" ?></th>
                                  <th data-field="payment_method" data-visible="false" data-sortable="true"><?= labels('payment_method', 'Payment Method') ?></th>
                                  <th data-field="message" data-sortable="true" data-visible="false"><?= labels('message', 'Message') ?></th>
                                  <th data-field="delivery_boy" data-sortable="true" data-visible="false"><?= labels('delivery_boy', 'Delivery Boy') ?></th>
                                  <th data-field="action"><?= labels('action', 'Action') ?></th>
                              </tr>
                          </thead>
                      </table>
                  </div>

              </div>
          </div>
      </section>