    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1><?= labels('customers', 'Customers') ?> </h1>
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
                                <h2 class="section-title"> <?= labels('register_customer_here', 'Register Customer Here') ?>!</h2>
                                <div class="mt-3">

                                    <div class="">
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#customer_register"><?= labels('register', 'Register') ?></button>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="section">
                <div class="section-body">
                    <div class="row">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-body">
                                    <table class="table table-hover table-borderd" id="customers_table" data-auto-refresh="true" data-show-columns="true" data-show-toggle="true" data-show-refresh="true" data-toggle="table" data-search-highlight="true" data-server-sort="false" data-page-list="[5, 10, 25, 50, 100, 200, All]" data-url="<?= base_url('delivery_boy/customers/customers_table'); ?>" data-side-pagination="server" data-pagination="true" data-search="true" data-server-sort="false">
                                        <thead>
                                            <tr>

                                                <th data-field="id" data-sortable="true"><?= labels('customer_id', 'Customer ID') ?></th>
                                                <th data-field="name" data-sortable="true"><?= labels('name', 'Name') ?></th>
                                                <th data-field="email" data-sortable="true"><?= labels('email', 'Email') ?></th>
                                                <th data-field="mobile" data-sortable="true"><?= labels('mobile_number', 'Mobile') ?></th>
                                                <th data-field="balance" data-sortable="true"><?= labels('balance', 'Balance') ?></th>
                                                <th data-field="status" data-sortable="true"><?= labels('status', 'Status') ?></th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    <div class="modal" id="customer_register">
        <div class="modal-dialog modal-m">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <h4 class="modal-title">Register User</h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>

                </div>
                <!-- Modal body -->
                <div class="modal-body">
                    <form method="post" action='<?= base_url('admin/orders/register') ?>' id="register_customer_form">
                        <div class="form-group">
                            <label for="first_name">Name</label><span class="asterisk text-danger"> *</span>
                            <input type="text" class="form-control" id="name" placeholder="Enter Your Name" name="first_name">
                        </div>
                        <div class="form-group">
                            <label for="created_by">Created by</label><span class="asterisk text-danger"> *</span>
                            <select name="created_by" id="created_by" class="form-control">
                                <option value="<?= $delivery_boy_id ?>" selected>You</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="identity">Mobile <small>(identity)</small></label><span class="asterisk text-danger"> *</span>
                            <input type="text" class="form-control" id="identity" placeholder="Enter Your Mobile Number" name="identity">
                        </div>
                        <div class="form-group">
                            <label for="password">Password</label><span class="asterisk text-danger"> *</span>
                            <input type="text" class="form-control" id="password" value="12345678" placeholder="Enter Password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="text" class="form-control" id="email" placeholder="abc@gmail.com" name="email">
                        </div>
                        <button type="submit" class="btn btn-primary" id="save-register-result-btn" name="register" value="Save">Register</button>
                        <div class="mt-3">
                            <div id="save-register-result"></div>
                        </div>
                    </form>
                </div>
                <!-- Modal footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>