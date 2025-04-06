<?php
helper('function');
$data = [];
try {
    $data = get_settings('general', true);
} catch (Exception $e) {
    echo "<script>console.log('$e')</script>";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php

    $settings = get_settings('general', true);
    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";
    $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
    ?>
    <meta charset="utf-8" />
    <meta content="width=device-width, initial-scale=1.0" name="viewport" />

    <title><?= $title ?></title>
    <link rel="icon" href="<?= base_url() . $favicon ?>" type="image/gif" sizes="16x16">

    <?php
    $title = get_settings('appname');

    isset($data['primary_color']) && $data['primary_color'] != "" ?  $primary_color = $data['primary_color'] : $primary_color =  '#05a6e8';
    isset($data['secondary_color']) && $data['secondary_color'] != "" ?  $secondary_color = $data['secondary_color'] : $secondary_color =  '#003e64';
    isset($data['primary_shadow']) && $data['primary_shadow'] != "" ?  $primary_shadow = $data['primary_shadow'] : $primary_shadow =  '#05A6E8';
    ?>
    <style>
        body {
            --primary: <?= $primary_color ?>;
            --secondary: <?= $secondary_color ?>;
            --nav-link: <?= $secondary_color ?>;
            --primary-shadow: 0px 5px 30px <?= $primary_shadow ?>;
        }
    </style>
    <?= view("frontend/include-css"); ?>
    <script>
        var base_url = "<?= base_url() ?>";
        var site_url = "<?= site_url() ?>";
        let csrf_token = "<?= csrf_token(); ?>";
        let csrf_hash = "<?= csrf_hash();  ?>";
    </script>
</head>

<body class="d-flex flex-column min-vh-100">
    <div id="app">
        <div class="main-wrapper" data-aos="fade-up" data-aos-delay="100">
            <main class="flex-shrink-0">
                <?php echo view("frontend/retro/pages/$page", $data); ?>
            </main>
        </div>
    </div>
    <?= view("frontend/include-scripts") ?>
</body>
<?php
    // Check if there's a 'message' in the session (set by the server-side application)
    if (session()->has('message')) {
    ?>
    <script>
        /**
         * Display a toast notification based on the message type
         * @param {string} message - The message content to display
         * @param {string} type - The type of message, either 'error' or 'success'
         */
        function showToastMessage(message, type) {
            // Switch based on the type of message to show an error or success toast
            switch (type) {
                case "error":
                    $().ready(
                        iziToast.error({
                            title: "Error", // Title shown on the toast
                            message: message, // The actual message content
                            position: "topRight", // Position of the toast on the screen
                        })
                    );
                    break;
                case "success":
                    $().ready(
                        iziToast.success({
                            title: "Success", // Title shown on the toast
                            message: message, // The actual message content
                            position: "topRight", // Position of the toast on the screen
                        })
                    );
                    break;
            }
        }

        <?php
        // Check if the 'message' in the session is an array
        if (is_array(session('message'))) {
            // Loop through each message in the array and display it as a toast
            foreach (session('message') as $error) {
        ?>
                showToastMessage("<?= $error ?>", "<?= session('type') ?>");
            <?php
            }
        } else {
            // If the 'message' is a single string, display it directly
            ?>
            showToastMessage("<?= session('message') ?>", "<?= session('type') ?>");
        <?php
        }
        ?>
    </script>
<?php
}
?>


</html>