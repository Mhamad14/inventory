<section class="py-5 mt-4 bg-white text-dark">
    <div class="container">

        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <!-- <div class="col-md-6">
                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/forgot-password.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
            </div> -->
            <!-- <div class="col-md-4 offset-1"> -->
            <div>
                <div class="card card-primary shadow">

                    <div class="login-brand m-4">
                        <img src="<?= base_url('/public/uploads/logo.png') ?>" title="UpBiz - POS, Accounting, Invoicing, Inventory Software - logo" alt="UpBiz - POS, Accounting, Invoicing, Inventory Software - logo" width="250">
                    </div>
                    <div class="card-header">
                        <div class="text-center">
                            <h4>Reset Password</h4>
                        </div>
                    </div>
                    <div class="card-body">

                        <form method="post" action="<?= base_url('forgot_password/update-password')  ?>" id="update_password">
                            <input type="hidden" name="token" value="<?=$token?>" id="token">
                            <div class="form-group">
                                <label for="email"><?= labels('email', 'Email') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                    </div>

                                    <input id="email" type="text" placeholder="Enter registered Email" class="form-control" name="email" autofocus>
                                </div>

                            </div>
                            <div class="form-group">
                                <label for="identity"><?= labels('mobile_number', 'Mobile') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-phone"></i>
                                        </div>
                                    </div>

                                    <input id="identity" type="text" placeholder="Enter registered mobile number" class="form-control" name="identity" autofocus>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reset_password_new_password"><?= labels('new_password', 'New Password') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-file-lock"></i>
                                        </div>
                                    </div>

                                    <input id="reset_password_new_password" type="password" placeholder="New Password" class="form-control" name="new_password" >
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="reset_password_confirm_password"><?= labels('confirm_password', 'Confirm Password') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-file-lock"></i>
                                        </div>
                                    </div>
                                    <input id="reset_password_confirm_password" type="password" placeholder="Confirm Password" class="form-control" name="confirm_password" >
                                </div>
                                <span id="reset_password_confirm_password_msg" class="text-danger d-none"></span>
                            </div>

                            <div class="form-check my-4">
                                <input class="form-check-input" type="checkbox" value="" id="reset_password_show_password">
                                <label class="form-check-label" for="reset_password_show_password">
                                    Show Password
                                </label>
                            </div>
                            <div class="d-flex gap-3 justify-content-center mb-4 pb-1">
                                <button type="submit" class="btn btn-video btn-primary btn-lg btn-block">Submit</button>
                                <a href="<?= base_url('login') ?>" class=" btn btn-video btn-primary btn-lg btn-block w-10em">Go To login </a><br>
                            </div>
                        </form>
                    </div>



                </div>
            </div>

        </div>
</section>