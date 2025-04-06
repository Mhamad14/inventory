<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels('smtp_email', 'SMTP (EMAIL)') ?></h1>
        </div>
        <div class="row">
            <div class="col-md">
            </div>
        </div>
        <?php
        $session = session();
        if ($session->has('message')) { ?>
            <div class="text-danger"><?php $message = session('message');
                                        echo $message['title']; ?></label></div>
        <?php } ?>
        <div class="section-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="card card-primary">
                        <!-- form start -->
                        <form class="form-horizontal form-submit-event" action="<?= base_url('admin/settings/save_settings') ?>" method="POST" id="email_settings" enctype="multipart/form-data">
                            <!-- card -->
                            <div class="card-body">
                                <p class="text-muted text-bold"><?= labels('email_description', 'Email SMTP settings, notifications and others related to email.') ?></p>
                                <input type="hidden" class="form-control" name="setting_type" value="email" placeholder="">


                                <div class="form-group row align-items-center">
                                    <label for="email-set" class="control-label"><?= labels('email', 'Email') ?> <span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-12 col-md-12">
                                        <input type="text" name="email" class="form-control" id="email-set" value="<?= isset($email['email']) ? $email['email'] : ""   ?>" required="" dir="ltr">

                                    </div>

                                </div>

                                

                                <div class="form-group row align-items-center">
                                    <label for="password" class="col-form-label"><?= labels('password', 'Password') ?> <span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-6 col-md-12">
                                        <input type="text" name="password" class="form-control" id="<?=   isset($email['password']) ? $email['password'] : ""  ?>" value="<?php if (isset($email['password'])) {
                                                                                                                                            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                                                                                                                                                echo "ahdb***********afasf";
                                                                                                                                            } else {
                                                                                                                                                echo $email['password'];
                                                                                                                                            }
                                                                                                                                        } ?>" required="">
                                        <div class="form-text text-muted"><?= labels('password_email', 'Password of above given email.') ?></div>
                                    </div>
                                </div>


                                <div class="form-group row align-items-center">
                                    <label for="smtp_host" class="col-form-label "><?= labels('mail_host', 'SMTP Host') ?><span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-6 col-md-12">
                                        <input type="text" name="smtp_host" class="form-control" id="smtp_host" value="<?=   isset($email['smtp_host']) ? $email['smtp_host'] : ""  ?>" required="">

                                    </div>
                                </div>


                                <div class="form-group row align-items-center">
                                    <label for="smtp_port" class="col-form-label "><?= labels('smtp_port', 'SMTP Port Number') ?><span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-6 col-md-12">
                                        <input type="text" name="smtp_port" class="form-control" id="smtp_port" value="<?=  isset($email['smtp_port']) ? $email['smtp_port'] : "" ?>" required="">

                                    </div>
                                </div>

                                <div class="form-group row">
                                    <label class="col-form-label"><?= labels('email_content_type', 'Email Content Type ') ?><span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-6 col-md-12">

                                        <select class="form-control" name="mail_content_type" id="mail_content_type">
                                            <option value="text">Text</option>
                                            <option value="html" selected>Html</option>

                                        </select>

                                    </div>
                                </div>


                                <div class="form-group row">
                                    <label class="col-form-label"><?= labels('smtp_encryption', 'SMTP Encryption') ?><span class="text-danger text-sm">*</span></label>
                                    <div class="col-sm-6 col-md-12">

                                        <select class="form-control" name="smtp_encryption" id="smtp_encryption">
                                            <option value="off">off</option>
                                            <option value="ssl" selected>SSL</option>
                                            <option value="tls">TLS</option>
                                        </select>

                                    </div>
                                </div>
                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary" id="submit_btn"><?= labels('update_email_Setting', 'Update Email Settings') ?></button>
                                    <button type="reset" class="btn btn-info"><?= labels('reset', 'Reset') ?></button>
                                </div>

                                <!-- /.card-body -->
                                <div class="d-flex justify-content-center">
                                    <div id="error_box">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <!--/.card-->
                </div>
                <!--/.col-md-12-->
            </div>
        </div>

    </section>
</div>