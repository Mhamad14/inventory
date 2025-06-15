<script>
    // preselect supplier
    const selectedBatchSupplier = <?= json_encode(["id" => $purchase['supplier_id'], "text" => $purchase['supplier_name']] ?? null) ?>;
    const baseSubtotal = <?= (float) ($purchase['total'] ?? 0) ?>;

    // total calculation
    $(document).ready(function() {


        function recalculateTotal() {
            const $discountInput = $("#batch_discount");
            const $shippingInput = $("#batch_shipping");
            const $totalDisplay = $("#final_total");
            const $totalHidden = $("input[name='final_total']");
            const $discountFeedback = $("#discount_feedback");

            let shipping = parseFloat($shippingInput.val()) || 0;
            let subtotal = typeof baseSubtotal !== "undefined" ? baseSubtotal : 0;

            let discountInput = $discountInput.val().trim();
            let discount = 0;
            let discountText = "";

            $discountFeedback.removeClass("text-danger").addClass("text-muted");

            if (discountInput.endsWith("%")) {
                let percent = parseFloat(discountInput.slice(0, -1));
                if (!isNaN(percent)) {
                    discount = (subtotal * percent) / 100;
                    discountText = `(${percent}% of ${subtotal.toFixed(2)} = ${discount.toFixed(2)})`;
                } else {
                    $discountFeedback
                        .text("Invalid percentage format.")
                        .removeClass("text-muted")
                        .addClass("text-danger");
                }
            } else if (!isNaN(parseFloat(discountInput))) {
                discount = parseFloat(discountInput);
                discountText = `(-${discount.toFixed(2)} flat)`;
            } else {
                $discountFeedback
                    .text("Please enter a valid number or percentage (e.g., 10 or 10%).")
                    .removeClass("text-muted")
                    .addClass("text-danger");
            }

            let total = subtotal - discount + shipping;
            total = total.toFixed(2);

            $totalDisplay.text(total);
            $totalHidden.val(total);
            if (discountText) {
                $discountFeedback.html(`Applied discount: ${discountText}`);
            }
        }
        // shipping
        // Block invalid characters for shipping input
        $("#batch_shipping").on("keypress", function(e) {
            let char = String.fromCharCode(e.which);
            let current = $(this).val();
            let isValid = true;
            let message = "";

            // Allow control keys
            if (e.ctrlKey || e.metaKey || e.which < 32) return;

            if (/\d/.test(char)) {
                isValid = true;
            } else if (char === "." && current.indexOf(".") === -1) {
                isValid = true;
            } else {
                isValid = false;
                message = "Only numeric values are allowed for shipping.";
            }

            if (!isValid) {
                e.preventDefault();
                showToastMessage(message, 'error');
            } 
        });

        // Validate pasted values
        $("#batch_shipping").on("paste", function(e) {
            let pasted = e.originalEvent.clipboardData.getData('text');
            if (!/^\d*\.?\d*$/.test(pasted)) {
                e.preventDefault();
                showToastMessage("Pasted value must be a number (e.g., 10 or 10.50).", 'error');

            } 
        });

        // Hide error on blur
        $("#batch_shipping").on("blur", function() {
            $("#shipping_error").hide();
        });

        //discount
        // Allow only numbers, one dot, and one % at the end
        $("#batch_discount").on("keypress", function(e) {
            let char = String.fromCharCode(e.which);
            let current = $(this).val();
            let isValid = true;
            let message = "";

            // Allow control keys
            if (e.ctrlKey || e.metaKey || e.which < 32) return;

            if (/\d/.test(char)) {
                isValid = true;
            } else if (char === "." && current.indexOf(".") === -1) {
                isValid = true;
            } else if (char === "%" && current.indexOf("%") === -1 && this.selectionStart === current.length) {
                isValid = true;
            } else {
                isValid = false;
                message = "Only numbers and a single '%' at the end are allowed.";
            }

            if (!isValid) {
                e.preventDefault();
                showToastMessage(message, 'error');

            }
        });
        $("#batch_discount").on("paste", function(e) {
            let pasted = e.originalEvent.clipboardData.getData('text');
            if (!/^\d*\.?\d*%?$/.test(pasted)) {
                e.preventDefault();
                showToastMessage("Pasted value is invalid. Use numbers or one '%' only.", 'error');

            }
        });

        // On input blur: hide error message
        $("#batch_discount").on("blur", function() {
            $("#discount_error").hide();
        });
        // Auto-add % when typing a percent discount
        $("#batch_discount").on("blur", function() {
            let val = $(this).val().trim();
            if (/^\d+$/.test(val)) {
                // e.g., user types 10, we leave it
                return;
            } else if (/^\d+\s*%?$/.test(val)) {
                if (!val.endsWith("%")) {
                    $(this).val(val + "%");
                }
            }
        });
        // Trigger recalc when discount or shipping changes
        $("#batch_discount, #batch_shipping").on("input blur", function() {
            recalculateTotal();
        });

        // Initial calculation
        recalculateTotal();
    });



    // initialize flatpicker
    $(document).ready(function() {
        $("#purchase_date").flatpickr({
            dateFormat: "Y-m-d", // format like 2025-06-04
            defaultDate: $("#purchase_date").val() || null,
            allowInput: true,
        });
    });
</script>