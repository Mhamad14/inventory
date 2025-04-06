<body>
    <div id="app">
        <section class="section">
            <div class="container mt-5">
                <div class="row">

                    <?php
                    $admin_mobile = $password = "";
                    if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { 
                    $admin_mobile = "9876543210"; 
                    $password = "12345678";
                        ?>
                        <div class="col-12">
                            <div class="alert alert-warning mb-0">
                                <b>Note:</b> If you cannot login here, please close the codecanyon frame by clicking on <b>x Remove Frame</b> button from top right corner on the page or <a href="https://upbiz.taskhub.company" target="_blank">&gt;&gt; Click here <<</a>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                        <div class="login-brand mb-3">
                            <img src="<?= $logo ?>" title="<?= $title ?> - POS, Accounting, Invoicing, Inventory Software - logo" alt="<?= $title ?> - POS, Accounting, Invoicing, Inventory Software - logo" width="250">
                        </div>

                        <div class="card card-primary">
                            <div class="card-header">
                                <h4>Login</h4>
                            </div>

                            <div class="card-body">
                                <div class="col-lg-12 " id="sign_in">
                                    <?php
                                    $session = session();
                                    if ($session->has("message")) { ?>
                                        <?= session("message"); ?>
                                    <?php } ?>
                                    <form method="POST" action="<?= base_url('auth/login') ?>" id="login_form" novalidate="">
                                        <div class="form-group">
                                            <label for="identity"><?= ucwords(config('IonAuth')->identity); ?></label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi bi-phone"></i>
                                                    </div>
                                                </div>
                                                <input id="identity" type="text" class="form-control" name="identity" 
                                                    placeholder="Mobile"
                                                autofocus value="<?=$admin_mobile?>">

                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label>Password </label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <div class="input-group-text">
                                                        <i class="bi bi-lock"></i>
                                                    </div>
                                                </div>
                                                <input type="password" id="password" name="password" class="form-control pwstrength" 
                                                placeholder="Password"
                                                data-indicator="pwindicator" value="<?=$password?>">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember_me">
                                                <label class="custom-control-label" for="remember_me">Remember Me</label>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <button type="submit" class="btn btn-video btn-primary btn-lg btn-block" tabindex="4">
                                                Login
                                            </button>
                                        </div>
                                        <div class="form-group d-flex">
                                            <a href="<?= base_url('forgot_password') ?>" class="float-left mt-3">
                                                Forgot Password?
                                            </a>
                                        </div>
                                    </form>
                                    <?php if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) { ?>
                                        <div class="row  text-center mt-2">
                                            <div class="col-12">
                                                <button class="mb-2 btn-buy btn-buy-danger w-100 w-50" onclick="set_vendor()">
                                                    Login as Admin
                                                </button>
                                            </div>
                                            <div class="col-12">
                                                <button class="mb-2 btn-buy btn-buy-warning w-100 w-50" onclick="set_delivery_boy()">
                                                    Login as Delivery Boy
                                                </button>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div id="sign_up" class="col-lg-12">
                                    <?php
                                    $session = session();
                                    if ($session->has("message")) { ?>
                                        <div class="text-danger"><?= session("message"); ?></label></div>
                                    <?php } ?>
                                    <form method="POST" id="register_form" action="<?= base_url('auth/create_user'); ?>">
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="first_name">First Name</label>
                                                <input id="first_name" type="text" class="form-control" name="first_name" autofocus>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="last_name">Last Name</label>
                                                <input id="last_name" type="text" class="form-control" name="last_name">
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <label for="email">Email</label>
                                            <input id="email" type="text" class="form-control" name="email">

                                            <div class="invalid-feedback">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="identity">Mobile</label>
                                            <input type="text" id="identity" class="form-control phone-number" name="identity">
                                        </div>
                                        <div class="row">
                                            <div class="form-group col-md-6">
                                                <label for="password" class="d-block">Password</label>
                                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password">
                                                <div id="pwindicator" class="pwindicator">
                                                    <div class="bar"></div>
                                                    <div class="label"></div>
                                                </div>
                                            </div>
                                            <div class="form-group col-md-6">
                                                <label for="password_confirm" class="d-block">Password Confirmation</label>
                                                <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                                            </div>
                                        </div>
                                        <div class="d-flex">
                                            <button type="submit" class="btn btn-get-upbiz btn-lg btn-block">
                                                Register
                                            </button>
                                            <button type="button" id="login_btn_of_register" class="btn btn-video mx-2 " tabindex="4">
                                                Login
                                            </button>
                                        </div>
                                </div>
                                </form>
                            </div>
                        </div>
                        <!--/column -->
                    </div>
                    <div class="simple-footer">
                        Copyright &copy; <?= date("Y") ?> <?= $title ?><br/>
                        Designed by <a href="https://codecanyon.net/user/infinitietech/portfolio" target="_blank">Infinitietech</a>
                    </div>
                </div>
            </div>
    </div>
    </section>
    </div>