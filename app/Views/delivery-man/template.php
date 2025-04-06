<?php $data = get_settings('general', true); ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $data['version'] = $version;
    $settings = get_settings('general', true);
    $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
    $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";
    $data['company'] = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
    $id = $_SESSION['user_id'];
    $data['businesses'] = (isset($businesses)) ? $businesses : $businesses = [];
    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
    $permission = get_delivery_boy_permission($id, $business_id, 'permission');
    $data['permissions'] = $permission;
    if (isset($permission) && !empty($permission)) {
        $orders_permission = $permission['orders_permission'];
        if ($orders_permission == "1") {
            $data['orders_permission'] = $orders_permission;
        } else {
            $data['orders_permission'] = "0";
        }
        $customer_permission = $permission['customer_permission'];
        if ($customer_permission == "1") {
            $data['customer_permission'] = $customer_permission;
        } else {
            $data['customer_permission'] = "0";
        }

        $transaction_permission = $permission['transaction_permission'];
        if ($transaction_permission == "1") {
            $data['transaction_permission'] = $transaction_permission;
        } else {
            $data['transaction_permission'] = "0";
        }
    }
    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url() . $favicon ?>" type="image/gif" sizes="16x16">
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <?= view("delivery-man/include-css") ?>
    <?php
    $primary_color = (isset($data['primary_color']) && $data['primary_color'] != "") ? $data['primary_color'] : '#05a6e8';
    $secondary_color = (isset($data['secondary_color']) && $data['secondary_color'] != "") ? $data['secondary_color'] : '#003e64';
     $primary_shadow = (isset($data['primary_shadow']) && $data['primary_shadow'] != "") ? $data['primary_shadow'] : '#05a6e8';
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

    <div id="app">
        <div class="main-wrapper">
            <?= view("delivery-man/header_sidebar", $data) ?>
            <?= view("delivery-man/pages/" . $page) ?>
            <?= view("delivery-man/footer") ?>
        </div>
    </div>
    <?= view("delivery-man/include-scripts") ?>

    <!-- Page Specific JS File -->
</body>

</html>