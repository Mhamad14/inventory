<script>
    $(document).ready(function() {

        // Currency conversion variables
        let currencies = [];
        let baseCurrency = null;
        let exchangeRates = {};
        let selectedCurrency = 'base';

        // Load currencies on page load
        loadCurrencies();

        // Currency selector change event
        $('#currency_selector').on('change', function() {
            selectedCurrency = $(this).val();

            // Always update totals when currency changes
            updateTotalsWithCurrency();
        });

        // Function to load currencies
        function loadCurrencies() {
            $.ajax({
                url: base_url + '/admin/purchases/get_currencies_for_purchases',
                type: 'GET',
                dataType: 'json',
                success: function(response) {

                    if (response.success) {
                        currencies = response.currencies || [];
                        baseCurrency = response.base_currency;
                        exchangeRates = response.exchange_rates || {};

                        // Populate currency dropdown
                        populateCurrencyDropdown();

                        // Initial currency conversion after loading
                        setTimeout(function() {
                            updateTotalsWithCurrency();
                        }, 500);
                    } else {
                        console.error('Currency API returned error:', response.message);
                        showToastMessage('Failed to load currencies: ' + response.message, 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Currency API request failed:', error);
                    console.error('Response:', xhr.responseText);
                    showToastMessage('Failed to load currencies. Please try again.', 'error');
                }
            });
        }

        // Function to populate currency dropdown
        function populateCurrencyDropdown() {
            const $selector = $('#currency_selector');
            $selector.empty();


            // Add base currency option
            if (baseCurrency) {
                $selector.append(`<option value="base">${baseCurrency.name} (${baseCurrency.code}) - Base Currency</option>`);
            } else {
                console.warn('No base currency found');
            }

            // Add other currencies
            let nonBaseCount = 0;
            currencies.forEach(currency => {
                // Check if currency is not base (handle both string and integer values)
                const isBase = parseInt(currency.is_base) === 1 || currency.is_base === '1';
                if (!isBase) {
                    const rate = exchangeRates[currency.id] || 0;
                    const rateText = rate > 0 ? `1 ${baseCurrency ? baseCurrency.code : 'BASE'} = ${rate} ${currency.code}` : 'No rate set';
                    $selector.append(`<option value="${currency.id}">${currency.name} (${currency.code}) - ${rateText}</option>`);
                    nonBaseCount++;
                } else {
                    console.log('Skipping base currency:', currency.name, 'is_base:', currency.is_base);
                }
            });

            // If no currencies found, show a message
            if (currencies.length === 0) {
                $selector.append('<option value="">No currencies available</option>');
                console.warn('No currencies found in the system');
            }
        }

        // Function to convert amount based on selected currency
        function convertAmount(amount, fromCurrencyId = null) {
            if (selectedCurrency === 'base') {
                return amount; // Return as is for base currency
            }

            const selectedCurrencyData = currencies.find(c => c.id == selectedCurrency);
            if (!selectedCurrencyData) {
                console.warn('Selected currency not found:', selectedCurrency);
                return amount;
            }

            // If converting from base currency to another currency
            if (!fromCurrencyId || fromCurrencyId === 'base') {
                const rate = exchangeRates[selectedCurrency];
                if (rate && rate > 0) {
                    return (amount / rate) * 100;
                } else {
                    console.warn('No valid exchange rate found for currency:', selectedCurrency);
                }
            }

            return amount;
        }

        // Function to format currency display
        function formatCurrencyDisplay(amount, currencyId = null) {
            let currencyData = baseCurrency;
            
            if (currencyId && currencyId !== 'base') {
                currencyData = currencies.find(c => c.id == currencyId);
            } else if (selectedCurrency !== 'base') {
                currencyData = currencies.find(c => c.id == selectedCurrency);
            }

            if (!currencyData) {
                console.warn('No currency data found for formatting, using default');
                return parseFloat(amount).toFixed(2);
            }

            const symbol = currencyData.symbol || '';
            const position = parseInt(currencyData.symbol_position) || 0;
            let decimals = parseInt(currencyData.decimal_places) || 2;
            
            // Special handling for IQD - no decimal places
            if (currencyData.code === 'IQD') {
                decimals = 0;
            }
            
            const formattedAmount = parseFloat(amount).toFixed(decimals);

            if (position === 0) {
                return symbol + ' ' + formattedAmount;
            } else {
                return formattedAmount + ' ' + symbol;
            }
        }

        // Function to update totals with currency conversion
        function updateTotalsWithCurrency() {

            // Get base totals from the existing purchase_total function
            const baseSubTotal = parseFloat($('input[name="total"]').val()) || 0;
            const baseSellTotal = parseFloat($('input[name="sell_total"]').val()) || 0;
            const baseProfitTotal = parseFloat($('input[name="profit_total"]').val()) || 0;

            // Convert amounts
            const convertedSubTotal = convertAmount(baseSubTotal);
            const convertedSellTotal = convertAmount(baseSellTotal);
            const convertedProfitTotal = convertAmount(baseProfitTotal);

            // Update display
            $('#sub_total').html(formatCurrencyDisplay(convertedSubTotal));
            $('#sell_total').html(formatCurrencyDisplay(convertedSellTotal));
            $('#profit_total').html(formatCurrencyDisplay(convertedProfitTotal));
        }

        // Override the existing purchase_total function to include currency conversion
        const originalPurchaseTotal = window.purchase_total;
        window.purchase_total = function() {
            // Call original function
            originalPurchaseTotal();

            // Update with currency conversion
            updateTotalsWithCurrency();
        };

        // Listen for changes in the purchase order table to trigger currency conversion
        $(document).on('change', '#purchase_order input, #purchase_order select', function() {
            // Small delay to ensure the purchase_total function has updated the values
            setTimeout(function() {
                if (selectedCurrency !== 'base') {
                    updateTotalsWithCurrency();
                }
            }, 100);
        });

        // Also listen for the order discount and shipping changes
        $(document).on('change', '#order_discount, #shipping', function() {
            setTimeout(function() {
                if (selectedCurrency !== 'base') {
                    updateTotalsWithCurrency();
                }
            }, 100);
        });


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