<?php $data = get_settings('general', true);  ?>
<!-- primary color #1679c5 -->
<!DOCTYPE html>
<html lang="en">

<head>
    <?php
    $settings = $data;
    $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
    $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";
    $data['company'] = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
    $id = $_SESSION['user_id'];
    $team_member = fetch_details('team_members', ['user_id' => $id]);
    if (empty($team_member)) {
        $businesses = fetch_details('businesses', ['user_id' => $id]);
        $data['businesses'] = (isset($businesses)) ? $businesses : $businesses = [];
    } else {
        $businesses = [];

        foreach ($team_member as $key) {
            // Fetch the business details and extract the first element of the array
            $business_ids = $key['business_ids'];
            $business_ids = json_decode($business_ids, true);

            $business = fetch_details('businesses', ['id' => $business_ids]);

            if (!empty($business)) {
                $businesses[] = $business[0]; // Assuming fetch_details returns an array with a single business
            }
        }

        $data['businesses'] = (isset($businesses)) ? $businesses : $businesses = [];
    }

    ?>
    <meta charset="UTF-8">
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no" name="viewport">
    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url() . $favicon ?>" type="image/gif" sizes="16x16">
    <meta name="description" content="<?= $meta_description ?>">
    <meta name="keywords" content="<?= $meta_keywords ?>">
    <?php include("include-css.php") ?>
    <?php
    $primary_color = (isset($data['primary_color']) && $data['primary_color'] != "") ? $data['primary_color'] : '#05a6e8';
    $secondary_color = (isset($data['secondary_color']) && $data['secondary_color'] != "") ? $data['secondary_color'] : '#003e64';
    $primary_shadow = (isset($data['primary_shadow']) && $data['primary_shadow'] != "") ? $data['primary_shadow'] : '#05a6e8';
    ?>
    <!-- Add NRT font for Kurdish -->
    <link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic&display=swap" rel="stylesheet">
    <style>
        body {
            --primary-color: <?= $primary_color ?>;
            --secondary-color: <?= $secondary_color ?>;
            --shadow: <?= $primary_shadow ?>;
        }

        /* Default font for English */
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Font for Kurdish language */
        body[lang="ku"] {
            font-family: 'Noto Naskh Arabic', serif;
        }

        /* Apply font to specific elements */
        body[lang="ku"] h1,
        body[lang="ku"] h2,
        body[lang="ku"] h3,
        body[lang="ku"] h4,
        body[lang="ku"] h5,
        body[lang="ku"] h6,
        body[lang="ku"] p,
        body[lang="ku"] span,
        body[lang="ku"] div,
        body[lang="ku"] button,
        body[lang="ku"] input,
        body[lang="ku"] select,
        body[lang="ku"] textarea {
            font-family: 'Noto Naskh Arabic', serif;
        }
    </style>
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= site_url() ?>";
        var csrf_token = "<?= csrf_token(); ?>";
        var csrf_hash = "<?= csrf_hash();  ?>";
    </script>
    <script src="<?= base_url("public/backend/assets/module/js/iziToast.js") ?>"></script>
</head>

<body lang="<?= $current_lang ?>">

    <div id="app">
        <div class="main-wrapper">
            <?= view("admin/header_sidebar", $data) ?>
            <?= view("admin/pages/" . $page) ?>
            <?= view("admin/footer") ?>
        </div>
    </div>


    <script>
        function showToastMessage(message, type) {
            switch (type) {
                case "error":
                    $().ready(
                        iziToast.error({
                            title: "Error",
                            message: message,
                            position: "topCenter",
                        })
                    );
                    break;
                case "success":
                    $().ready(
                        iziToast.success({
                            title: "Success",
                            message: message,
                            position: "topCenter",
                        })
                    );
                    break;
            }
        }
    </script>
    <?php if (session()->has('message')): ?>
        <script>
            showToastMessage("<?= session('message') ?>", "<?= session('type') ?>");
            <?= session()->remove('message') ?>;
        </script>
    <?php elseif (session()->has('permission_error')): ?>
        <script>
            showToastMessage("<?= session('permission_error') ?>", "error");
            <?= session()->remove('permission_error') ?>;
        </script>
    <?php endif; ?>

    <!-- Page Specific JS File -->
    <?php include("include-scripts.php") ?>
</body>

</html>