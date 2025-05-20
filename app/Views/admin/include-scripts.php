<script src="<?= base_url("public/backend/assets/js/lightbox-plus-jquery.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/bootstrap-table.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/jquery.validate.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/popper.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/stackpath.bootstrap.min.js") ?>"> </script>
<script src=" <?= base_url("public/backend/assets/js/tableExport.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/bootstrap-table-export.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/jquery.dataTables.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/summernote-bs5.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/jquery.nicescroll.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/moment.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/daterangepicker.min.js") ?>"> </script>
<script src="<?= base_url("public/backend/assets/js/bootstrap-colorpicker.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/stisla.js") ?>"></script>
<!-- <script src="<?= base_url("public/backend/assets/module/js/iziToast.js") ?>"></script> -->
<script src="<?= base_url("public/backend/assets/summernote-dist/summernote-bs4.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/coloris.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/tinymce/tinymce.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/sweetalert2.min.js") ?>"></script>
<script src=" <?= base_url("public/backend/assets/js/scripts.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/pos.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/checkout.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/chart.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/selectize.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/barcode-gen.min.js") ?>"></script>
<script src="<?= base_url("public/backend/assets/js/select2.min.js") ?>"></script>
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
<!-- <script src="https://checkout.razorpay.com/v1/checkout-frame.js"></script>
<script src="https://js.stripe.com/v3/"></script>
<script src="https://checkout.flutterwave.com/v3.js"></script> -->

<script src="<?= base_url("public/backend/assets/js/custom.vendor.js") ?>"></script>
<script>
    //Shahram: our custome ToastMessage
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


    //Shahram:for inputing only numbers
    function restrictToNumbers(inputSelector, toastMessage = 'Only numbers are allowed!') {
        document.querySelector(inputSelector).addEventListener('keydown', function(e) {
            // Allow: Backspace, Delete, Tab, Escape, Enter, '.', and arrow keys
            if (
                [46, 8, 9, 27, 13, 110, 190].indexOf(e.keyCode) !== -1 ||
                (e.keyCode >= 35 && e.keyCode <= 40) // Home, End, arrows
            ) {
                return; // Allow these keys
            }

            // Allow: Ctrl/Cmd+A, Ctrl/Cmd+C, Ctrl/Cmd+V, Ctrl/Cmd+X
            if ((e.ctrlKey || e.metaKey) && [65, 67, 86, 88].indexOf(e.keyCode) !== -1) {
                return;
            }

            // Only allow numbers (0-9)
            if ((e.keyCode < 48 || e.keyCode > 57) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
                showToastMessage(toastMessage, 'error');
            }
        });
    }
</script>