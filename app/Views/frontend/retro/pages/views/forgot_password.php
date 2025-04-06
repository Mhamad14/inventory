<section class="breadcrumbs">

    <div class="d-md-flex m-0">



        <div class="container ">
            <ol class='floatc-right'>
                <li><a href="<?= base_url() ?>">Home</a></li>
                <li>Forgot Password</li>
            </ol>
        </div>
    </div>

</section>

<section class="py-5 mt-4 bg-white text-dark">


    <div class="container">


        <div class="row gx-lg-8 gx-xl-12 gy-10 align-items-center">
            <div class="col-md-6">
                <lottie-player src="<?= base_url('public/frontend/assets/retro/img/forgot-password.json') ?>" background="transparent" speed="1" loop autoplay class="w-300-h-300"></lottie-player>
            </div>
            <div class="col-md-4 offset-1">

                <div class="card card-primary">

                    <div class="login-brand m-4">
                        <img src="./public/uploads/logo.png" title="UpBiz - POS, Accounting, Invoicing, Inventory Software - logo" alt="UpBiz - POS, Accounting, Invoicing, Inventory Software - logo" width="250">
                    </div>

                    <!-- <?php if (session()->getFlashdata('errors')) { ?>
                        <div class="">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <div class="alert alert-danger " role="alert">
                                    <p><?= esc($error) ?></p>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php } ?>


                    <?php if (session()->getFlashdata('success')) { ?>
                        <div class="">
                            <?php foreach (session()->getFlashdata('errors') as $error) : ?>
                                <div class="alert alert-success" role="alert">
                                    <p><?= esc($error) ?></p>
                                </div>

                            <?php endforeach; ?>
                        </div>
                    <?php } ?> -->
                    <div class="card-header">
                        <div class="text-center">
                            <h4>Forgot Password</h4>
                        </div>
                    </div>
                    <div class="card-body">

                        <form method="post" action="<?= base_url('forgot_password/verify')  ?>">
                            <div class="form-group">
                                <label for="identity"><?= labels('email', 'Email') ?></label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text">
                                            <i class="bi bi-envelope"></i>
                                        </div>
                                    </div>

                                    <input id="identity" type="text" placeholder="Enter registered Email" class="form-control" name="email" autofocus>
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

                            <div class="text-center form-outline mb-4 pb-1">
                                <input type="submit" class="mb-2 btn btn-get-upbiz w-10em" value="Submit">
                                <a href="<?= base_url('login') ?>" class=" mb-2 btn btn-get-upbiz w-10em">Go Back</a><br>
                            </div>
                        </form>
                    </div>



                </div>
            </div>

        </div>
    </div>
</section>