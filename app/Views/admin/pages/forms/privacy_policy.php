<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>
                <h1> <?= labels('settings', 'Settings') ?></h1>
            </h1>
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
                            <form action="<?= base_url('admin/settings/save_settings') ?>" id="privacy_policy_setting_form" accept-charset="utf-8" method="POST">
                                <h2 class="section-title"> <?= labels('privacy_policy', 'Privacy Policy') ?> </h2>
                                <div class="row mb-3">
                                    <div class="col-md">
                                        <textarea class="texteditor" rows=30 id="privacy_policy" name="privacy_policy"><?= !empty($privacy_policy) && !empty($privacy_policy['privacy_policy']) ? $privacy_policy['privacy_policy'] : "Privacy Policy" ?></textarea>
                                        <input type="hidden" name="setting_type" value="privacy_policy">
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-md">
                                        <div class="form-group">
                                            <input type='submit' name='update' id='update' value='<?= labels('update', 'Update') ?>' class='btn btn-primary' />
                                            <input type='reset' name='clear' id='clear' value='<?= labels('clear', 'Clear') ?>' class='btn btn-info' />
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