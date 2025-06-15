<script>
    $(document).ready(function() {

        $("#purchase_form").validate({
            rules: {
                supplier_id: {
                    required: true,
                },
                warehouse_id: {
                    required: true,
                },
                purchase_date: {
                    required: true,
                    dateISO: true,
                },
                status: {
                    required: true,
                },
                // This targets at least one product being added
                products: {
                    required: true,
                }
            },
            messages: {
                supplier_id: {
                    required: "Supplier is required",
                },
                warehouse_id: {
                    required: "Warehouse is required",
                },
                purchase_date: {
                    required: "Purchase date is required",
                    dateISO: "Please enter a valid date (YYYY-MM-DD).",
                },
                status: {
                    required: "Status is required",
                },
                products: {
                    required: "Please add at least one product.",
                }
            },

            highlight: function(element) {
                $(element).removeClass("is-valid").addClass("is-invalid");
            },
            unhighlight: function(element) {
                $(element).removeClass("is-invalid").addClass("is-valid");
            },
            errorPlacement: function(error, element) {
                error.addClass("invalid-feedback");

                // Special handling for select2
                if (element.hasClass("select2-hidden-accessible")) {
                    error.insertAfter(element.next(".select2-container"));
                } else {
                    error.insertAfter(element);
                }
            },



            submitHandler: function(form) {
                if (variant_data.length === 0) {
                    showToastMessage("You must add at least one product.", "error");
                    return false;
                }

                let isValid = true;

                $("#purchase_order tbody tr").each(function() {
                    let row = $(this);
                    let qty = parseFloat(row.find(".qty").val()) || 0;
                    let price = parseFloat(row.find(".price").val());
                    let sellPrice = parseFloat(row.find(".sell_price").val());
                    let discount = parseFloat(row.find(".discount").val());
                    let expire = row.find(".expire").val();
                    let today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
                    // Check if expire is empty
                    if (!expire) {
                        isValid = false;
                        showToastMessage("Expiration date is required.", "error");
                        row.find(".expire").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    if (!sellPrice) {
                        isValid = false;
                        showToastMessage("Sell Price is required.", "error");
                        row.find(".sell_price").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    if (!price) {
                        isValid = false;
                        showToastMessage("Cost Price is required.", "error");
                        row.find(".price").addClass("is-invalid");
                        return false; // Exit the .each() loop
                    }
                    // Quantity validation
                    if (qty <= 0) {
                        isValid = false;
                        showToastMessage("Quantity must be greater than 0.", "error");
                        return false;
                    }

                    // Price validation
                    if (price < 0) {
                        isValid = false;
                        showToastMessage("Price must not be negative.", "error");
                        return false;
                    }

                    // Sell price validation
                    if (sellPrice < 0) {
                        isValid = false;
                        showToastMessage("Sell price must not be negative.", "error");
                        return false;
                    }

                    // Expiration date validation
                    if (expire && expire < today) {
                        isValid = false;
                        showToastMessage("Expiration date cannot be in the past.", "error");
                        return false;
                    }

                    // Discount validation
                    let subtotal = qty * price;
                    if (discount < 0 || discount > subtotal) {
                        isValid = false;
                        showToastMessage("Discount must be between 0 and subtotal.", "error");
                        return false;
                    }
                });

                if (!isValid) return false;

                let formData = new FormData(form);
                formData.append(csrf_token, csrf_hash);

                $.ajax({
                    url: form.action,
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    dataType: 'json',
                    success: function(response) {
                        csrf_token = response["csrf_token"];
                        csrf_hash = response["csrf_hash"];
                        if (response.success) {
                            showToastMessage(response.message, 'success');
                            location.href = base_url + "/admin/purchases";

                        } else {
                            showToastMessage(response.message, 'error');
                        }
                    },
                    error: function() {
                        showToastMessage('An error occurred during the request.', 'error');
                    }
                });
                return false; // Stop form submission for customer form
            }
        });
    });
</script>