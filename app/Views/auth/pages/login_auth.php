<div class="container mt-2">
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-5 offset-xl-3">
            <div class="login-brand img-fluid">
                <img src="<?= $logo ?>" alt="logo" class="login-logo">
            </div>
            <?php

            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            ?>
                <div class="col-12 text-center mb-5">
                    <div class="alert alert-warning mb-0">
                        <b>Note:</b> If you cannot login here, please close the codecanyon frame by clicking on <b>x Remove Frame</b> button from top right corner on the page or <a href="https://upbiz.taskhub.company/" target="_blank">&gt;&gt; Click here &lt;&lt;</a>
                    </div>
                </div>
            <?php } ?>
            <div class="card card-primary">
                <div class="card-header">
                    <h4>Login</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?= base_url('auth/login') ?>" id="login_form" class="needs-validation" novalidate="">

                        <div class="form-group">
                            <label for="identity"><?= ucwords(config('IonAuth')->identity); ?></label>
                            <input id="identity" type="text" class="form-control" name="identity" tabindex="1" required autofocus>
                            <div class="invalid-feedback">
                                Please fill in your Mobile no.
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="d-block">
                                <label for="password" class="control-label">Password</label>
                                <div class="float-right">
                                    <a href="<?= base_url('forgot_password') ?>" class="text-small">
                                        Forgot Password?
                                    </a>
                                </div>
                            </div>
                            <input id="password" type="password" class="form-control" name="password" tabindex="2" required>

                            <div class="invalid-feedback">
                                please fill in your password
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" name="remember" class="custom-control-input" tabindex="3" id="remember-me">
                                <label class="custom-control-label" for="remember-me">Remember Me</label>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Login
                            </button>
                        </div>
                    </form>
                    <?php

                    if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                    ?>
                        <div class="form-group">

                            <button class="btn btn-danger btn-lg btn-block" onclick="set_admin()">
                                Login as admin
                            </button>


                            <button class="btn btn-success btn-lg btn-block" onclick="set_vendor()">
                                Login as Vendor
                            </button>


                            <button class="btn btn-warning btn-lg btn-block" onclick="set_delivery_boy()">
                                Login as Delivery Boy
                            </button>
                        </div>
                </div>
            </div>
        <?php } ?>
        <div class="text-muted text-center">
            Don't have an account? <a href="<?= base_url("register") ?>">Create One</a>
        </div>
        <div class="simple-footer text-center">
            <div class="d-flex justify-content-center copyright">
                <p> Copyright &copy; <?= date("Y") ?> <?= $company ?><br>
                    Design & Developed By <a href="https://infinitietech.com" target="_blank">Infinitie Technologies</a>
                </p>
            </div>

            <a href='<?= base_url("about"); ?>' target="_blank">About Us</a> |
            <a href='<?= base_url("privacy_policy"); ?>' target="_blank">Privacy Policy</a> |
            <a href='<?= base_url("refundpolicy"); ?>' target="_blank">Refund Policy</a> |
            <a href='<?= base_url("terms_and_conditions"); ?>' target="_blank">Terms And Conditions</a>
        </div>
        </div>
    </div>
</div>