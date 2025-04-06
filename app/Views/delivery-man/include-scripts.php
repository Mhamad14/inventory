<!-- General JS Scripts -->

<script src="<?= base_url("public/backend/assets/js/jquery.validate.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/popper.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/stackpath.bootstrap.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/jquery.dataTables.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/dataTables.bootstrap5.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap-table.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/summernote-bs5.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/jquery.nicescroll.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/moment.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/daterangepicker.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap-colorpicker.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/stisla.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/module/js/iziToast.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/summernote-dist/summernote-bs4.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/tinymce/tinymce.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/sweetalert2.min.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/scripts.js") ?>"></script>

<script src="<?= base_url("public/backend/assets/js/select2.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/chart.min.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/delivery.boy.service.pos.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/custom.delivery.boy.js") ?>"></script>


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