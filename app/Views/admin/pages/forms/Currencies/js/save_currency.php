<script>
    // Currency CRUD AJAX
    $(document).ready(function() {
        // Add/Edit Currency
        var validator = $('#currency_form').validate({
            rules: {
                code: {
                    required: true,
                    minlength: 3,
                    maxlength: 3
                },
                name: {
                    required: true
                },
                symbol: {
                    required: true
                },
                decimal_places: {
                    required: true,
                    digits: true,
                    min: 0,
                    max: 4
                }
            },
            messages: {
                code: {
                    required: "Please enter currency code",
                    minlength: "Currency code must be 3 characters",
                    maxlength: "Currency code must be 3 characters"
                },
                name: {
                    required: "Please enter currency name"
                },
                symbol: {
                    required: "Please enter currency symbol"
                },
                decimal_places: {
                    required: "Please enter decimal places",
                    digits: "Please enter a valid number",
                    min: "Minimum 0 decimal places",
                    max: "Maximum 4 decimal places"
                }
            },
            errorElement: 'span',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group').append(error);
            },
            highlight: function(element, errorClass, validClass) {
                $(element).addClass('is-invalid');
            },
            unhighlight: function(element, errorClass, validClass) {
                $(element).removeClass('is-invalid');
            }
        });

        // Handle save button click
        $('#save_currency').on('click', function(e) {
            e.preventDefault();

            if (!validator.form()) {
                return false;
            }

            // Disable save button to prevent double submission
            var $submitButton = $(this);
            $submitButton.prop('disabled', true);

            var form = $('#currency_form')[0];
            var formData = new FormData(form);

            // Log form data for debugging
            console.log('Submitting form data:');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }

            $.ajax({
                type: 'POST',
                url: base_url + '/admin/currency/save',
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                dataType: 'json',
                success: function(result) {
                    console.log('Server response:', result);

                    if (result.csrf_token) {
                        csrf_token = result.csrf_token;
                        csrf_hash = result.csrf_hash;
                    }

                    if (result.error == true) {
                        if (typeof result.message === 'object') {
                            Object.keys(result.message).forEach(function(key) {
                                iziToast.error({
                                    title: 'Error!',
                                    message: result.message[key],
                                    position: 'topRight'
                                });
                            });
                        } else {
                            iziToast.error({
                                title: 'Error!',
                                message: result.message,
                                position: 'topRight'
                            });
                        }
                    } else {
                        showToastMessage(result.message, 'success');
                        setTimeout(function() {
                            window.location = base_url + '/admin/currency';
                        }, 1000);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error Details:');
                    console.error('Status:', status);
                    console.error('Error:', error);
                    console.error('Response Text:', xhr.responseText);
                    console.error('Status Code:', xhr.status);
                    console.error('Status Text:', xhr.statusText);

                    try {
                        var response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            showToastMessage(response.message, 'error');
                        } else {
                            showToastMessage('An error occurred while saving the currency. Please try again.', 'error');
                        }
                    } catch (e) {
                        showToastMessage('An error occurred while saving the currency. Please try again.', 'error');
                    }
                },
                complete: function() {
                    // Re-enable save button
                    $submitButton.prop('disabled', false);
                }
            });
        });
        
    });
</script>