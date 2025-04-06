<div class="container mt-5">
    <div class="row">
        <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="login-brand img-fluid">
                <img src="<?= $logo ?>" alt="logo" class="login-logo">
            </div>

            <div class="card card-primary">
                <div class="card-header">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    <form method="POST" id="register" action="<?= base_url('auth/create_user'); ?>">
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="first_name">First Name</label>
                                <input id="first_name" type="text" class="form-control" name="first_name" autofocus>
                            </div>
                            <div class="form-group col-6">
                                <label for="last_name">Last Name</label>
                                <input id="last_name" type="text" class="form-control" name="last_name">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="email">Email</label>
                            <input id="email" type="text" class="form-control" name="email">
                            <input type="hidden" value="<?= csrf_hash(); ?>" name="<?= csrf_token(); ?>" id="csrf">

                            <div class="invalid-feedback">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Mobile</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                </div>
                                <input type="text" id="identity" class="form-control phone-number" name="identity">
                            </div>
                        </div>
                        <div class="row">
                            <div class="form-group col-6">
                                <label for="password" class="d-block">Password</label>
                                <input id="password" type="password" class="form-control pwstrength" data-indicator="pwindicator" name="password">
                                <div id="pwindicator" class="pwindicator">
                                    <div class="bar"></div>
                                    <div class="label"></div>
                                </div>
                            </div>
                            <div class="form-group col-6">
                                <label for="password_confirm" class="d-block">Password Confirmation</label>
                                <input id="password_confirm" type="password" class="form-control" name="password_confirm">
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg" tabindex="4">
                                Register
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