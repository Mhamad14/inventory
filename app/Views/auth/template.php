<?php $data = get_settings('general', true); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php

    $settings = get_settings('general', true);
    $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
    $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";
    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url() . $favicon ?>" type="image/gif" sizes="16x16">
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <?= view("auth/include-css") ?>
    <?php
    isset($data['primary_color']) && $data['primary_color'] != "" ?  $primary_color = $data['primary_color'] : $primary_color =  '#05a6e8';
    isset($data['secondary_color']) && $data['secondary_color'] != "" ?  $secondary_color = $data['secondary_color'] : $secondary_color =  '#003e64';
    isset($data['primary_shadow']) && $data['primary_shadow'] != "" ?  $primary_shadow = $data['primary_shadow'] : $primary_shadow =  '#05a6e8';
    ?>
    <style>
        body {
            --primary-color: <?= $primary_color ?>;
            --secondary-color: <?= $secondary_color ?>;
        }
    </style>
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= site_url() ?>";
        var csrf_token = "<?= csrf_token(); ?>";
        var csrf_hash = "<?= csrf_hash();  ?>";
    </script>
</head>

<body>
    <?php
    if (isset($_SESSION['toastMessage'])) { ?>
        <script>
            $(document).ready(function() {
                showToastMessage("<?= $_SESSION['toastMessage'] ?>", "<?= $_SESSION['toastMessageType'] ?>")
            });
        </script>";
    <?php } ?>

    <div id="app">
        <section class="section">
            <?= view("auth/pages/" . $page) ?>
        </section>
    </div>
    <div class="modal" id="system_pages" data-bs-backdrop="static" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title"></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="content"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
        <?= view("auth/include-scripts") ?>
        <!-- Page Specific JS File -->
</body>

</html>