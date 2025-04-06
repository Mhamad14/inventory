<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
            <div class="login-brand img-fluid">
                <img src="<?= $logo ?>" alt="logo" class="login-logo">
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Forgot Password</h4>
                </div>
                <?php
                $session = session();
                if ($session->has("message")) { ?>
                    <div class="flash-message-custom"><?= session("message"); ?></label></div>
                <?php } ?>

                <div class="card-body">
                    <p class="text-muted">We will send a link to reset your password</p>
                    <form method="POST" action="<?= base_url('auth/forgot_password') ?>" id="forgot_password">
                        <div class="form-group">
                            <label for="identity">Email</label>
                            <input id="identity" type="email" class="form-control" name="identity" tabindex="1" required autofocus>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                                Forgot Password
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            <div class="simple-footer">
                <div class="d-flex justify-content-center copyright">
                    <p> Copyright &copy; <?= date("Y") ?> <?= $company ?><br>
                        Design & Developed By <a href="https://infinitietech.com" target="_blank">Infinitie Technologies</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>