<!-- main content form -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1> <?= labels('settings', 'Settings') ?></h1>
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
            <div class="row mt-sm-4">
                <div class='col-md-12'>
                    <div class="card">
                        <div class="card-body">
                            <form action="<?= base_url('admin/settings/save_settings') ?>" id="general_setting_form" enctype="multipart/form-data" accept-charset="utf-8" method="POST">
                                <h2 class="section-title"> <?= labels('general_settings', 'General Settings') ?> </h2>
                                <div class="row ">
                                    <div class="col-md ">
                                        <div class="form-group">
                                            <label for="title"><?= labels('company_title', 'Company Title') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="title" id="title" value="<?= !empty($general) && !empty($general['title']) ? $general['title'] : "Company title" ?>" placeholder="">
                                            <input type="hidden" class="form-control" name="setting_type" value="general" placeholder="">
                                        </div>
                                    </div>
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="support_email"><?= labels('support_email', 'Support Email') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="support_email" id="support_email" value="<?= !empty($general) && !empty($general['support_email']) ? $general['support_email'] : "support email" ?>" placeholder="">
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for='logo'><?= labels('logo', 'Logo') ?></label>
                                            <div class="gallery">
                                                <img class="settings_logo" src="<?= !empty($general) && !empty($general['logo']) ?  base_url($general['logo']) : "" ?>" alt="">
                                            </div>
                                            <input type="file" class="form-control" name="logo" id="logo" value="<?= $general['logo']; ?>" placeholder="" min="1">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="half_logo"><?= labels('half_logo', 'Half Logo') ?></label>
                                            <div class="gallery">
                                                <img class="settings_logo" src="<?= !empty($general) && !empty($general['half_logo']) ? base_url($general['half_logo']) : "" ?>" alt="">
                                            </div>
                                            <input type="file" class="form-control" name="half_logo" id="half_logo" value="<?= $general['half_logo']; ?>" placeholder="" min="1">

                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="favicon"><?= labels('favicon', 'Favicon') ?></label>
                                            <div class="gallery">
                                                <img class="settings_logo" src="<?= !empty($general) && !empty($general['favicon']) ? base_url($general['favicon'])  : "" ?>" alt="">
                                            </div>
                                            <input type="file" class="form-control" name="favicon" id="favicon" value="<?= $general['favicon']; ?>" placeholder="" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row ">
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="currency_symbol"><?= labels('currency_symbol', 'Currency Symbol') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="currency_symbol" id="currency_symbol" value="<?= !empty($general) && !empty($general['currency_symbol']) ? $general['currency_symbol'] : "currency symbol" ?>" placeholder="" min="0">
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="currency_locate"><?= labels('currency locate', 'Currency Position') ?></label><span class="asterisk text-danger"> *</span>
                                            <select class='form-control selectric' name='currency_locate' id='currency_locate'>
                                                <!-- <option>select</option> -->
                                                <option value="left">Left</option>
                                                <option value="right">Right</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_format"><?= labels('date_format', 'Date Format') ?></label><span class="asterisk text-danger"> *</span>
                                            <select class='form-control selectric' name='date_format' id='date_format'>
                                                <option disabled>select</option>
                                                <option value="m/d/y H:i A">MM/DD/YY</option>
                                                <option value="d/m/Y H:i A">DD/MM/YY</option>
                                                <option value="Y/m/d H:i A">YY/MM/DD</option>
                                                <option value="d-M-Y H:i A">DD-Mon-YYYY</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label for="date_format"><?= labels('decimal_points', 'Decimal Points') ?></label><span class="asterisk text-danger"> *</span>
                                            <input class="form-control" type="number" value="<?= !empty($general) && !empty($general['decimal_points']) ? $general['decimal_points'] : "decimal_points" ?>" placeholder="0" min="0" id="decimal_points" name="decimal_points">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="select_time_zone"><?= labels('select_time_zone', 'Select Time Zone') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="hidden" name="mysql_timezone" id="mysql_timezone" value="">
                                            <select class='form-control selectric select2' name='select_time_zone' id='select_time_zone'>
                                                <option >Select Timezone</option>
                                                <?php $options = getTimezoneOptions(); ?>
                                                <?php foreach ($options as $option) { ?>
                                                    <option value="<?= $option[2] ?>" data-gmt="<?= $option[1] ?>"><?= $option[2] ?> - GMT <?= $option[1] ?> <?= $option[0] ?> </option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone"><?= labels('phone', 'Phone') ?></label><span class="asterisk text-danger"> *</span>
                                            <input type="text" class="form-control" name="phone" id="phone" value="<?= !empty($general) && !empty($general['phone']) ? $general['phone'] : "phone" ?>" placeholder="" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="primary_color"><?= labels('primary_color', 'Primary Color') ?></label>
                                            <div class="col-md-3">
                                                <input type="color" class="form-control" name="primary_color" id="primary_color" value="<?= !empty($general) && !empty($general['primary_color']) ? $general['primary_color'] : "Primary color" ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="secondary_color"><?= labels('secondary_color', 'Secondary Color') ?></label>
                                            <div class="col-md-3">
                                                <input type="color" class="form-control" name="secondary_color" id="secondary_color" value="<?= !empty($general) && !empty($general['secondary_color']) ? $general['secondary_color'] : "Secondary color" ?>" />
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="primary_shadow"><?= labels('primary_shadow_color', 'Primary Shadow Color') ?></label>
                                            <div class="col-md-3">
                                                <input type="color" class="form-control" name="primary_shadow" id="primary_shadow" value="<?= !empty($general) && !empty($general['primary_shadow']) ? $general['primary_shadow'] : "Primary shadow" ?>" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="address"><?= labels('address', 'Address') ?></label>
                                            <textarea rows=30 class='form-control h-50 summernote' id="address" name="address"><?= !empty($general) && !empty($general['address']) ? $general['address'] : "Address" ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="short_description"><?= labels('short_description', 'Short Description') ?></label>
                                            <textarea rows=30 class='form-control h-50 summernote' id="short_description" name="short_description"><?= !empty($general) && !empty($general['short_description']) ? $general['short_description'] : "Short description" ?></textarea>
                                        </div>
                                    </div>
                                 </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="copyright_details"><?= labels('copyright_details', 'Copyright Details') ?></label>
                                            <textarea rows=30 class='form-control h-50 summernote' id="copyright_details" name="copyright_details"><span class="general-setting"><?= !empty($general) && !empty($general['copyright_details']) ? $general['copyright_details'] : "Copyright details" ?></span></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <label for="support_hours"><?= labels('support_hours', 'Support hours') ?></label>
                                            <textarea rows=30 class='form-control h-50 summernote' id="support_hours" name="support_hours"><?= !empty($general) && !empty($general['support_hours']) ? $general['support_hours'] : "Support hours" ?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md">
                                        <h4>Cron Job URL for Auto Renewal of services</h4>
                                        <div class="form-group">
                                            <label for="Cron_job">Cron Job URL <small>(Set this URL at your server cron job list for "once a day")</small></label><span class="asterisk text-danger"> *</span>
                                            <button type="button" class="btn btn-sm btn-primary mb-1" data-bs-toggle="modal" data-bs-target="#cronjonbmodal" title="how it works?"> How Auto-renwal works?</button>

                                            <input type="text" class="form-control" value="<?= base_url('admin/Cron_job/renew_service') ?>" disabled>
                                        </div>
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <input type='submit' name='update' id='update' value='<?= labels('update', 'Update') ?>' class='btn btn-primary' />
                                            <input type='reset' name='clear' id='clear' value='<?= labels('reset', 'Reset') ?>' class='btn btn-info' />
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal" id="cronjonbmodal">
    <div class="modal-dialog modal-m">
        <div class="modal-content">
            <!-- Modal Header -->
            <div class="modal-header">
                <h4>How service will renew automatically ?</h4>
                <button type="button" class="close" data-bs-dismiss="modal"><span aria-hidden="true">×</span></button>
            </div>
            <hr>
            <!-- Modal body -->
            <div class="modal-body">
                <ol>
                    <li>
                        Cron job must be set (For once in a day) on your server for <strong>Service Renewal</strong> to work.
                    </li>
                    <li>
                        Cron job will run every mid night at 12:00 AM.
                    </li>
                    <li> It will check on subcribed services and </li>
                    <li> from reccuring days of service it will calculate expiration date of service.</li>
                    <li> and create new order of that service from expiry date to recurring days. </li>
                    <li>you can delete subscription if you want to stop the renawal.</li>
                </ol>
            </div>
            <!-- Modal footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
            </div>
        </div>
    </div>
</div>