<script src="<?= base_url("public/backend/assets/module/js/iziToast.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/jquery.nicescroll.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/popper.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/moment.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/stisla.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/scripts.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/custom.js") ?>"></script>
<script>
    function showToastMessage(message, type) {
        switch (type) {
            case "error":
                $().ready(
                    iziToast.error({
                        title: "Error",
                        message: message,
                        position: "topRight",
                    })
                );
                break;

            case "success":
                $().ready(
                    iziToast.success({
                        title: "Success",
                        message: message,
                        position: "topRight",
                    })
                );
                break;
        }
    }
</script>