<script>
    function removeCommas(x) {
        return x ? x.toString().replace(/,/g, '') : x;
    }
    $(document).on("submit", "#purchase_form", function(e) {
        e.preventDefault();

        $('.payment-converted-iqd').each(function() {
            this.value = removeCommas(this.value);
        });

        let isValid = true;
        const errorMessages = [];

        // 1. Validate Supplier
        if (!$('select[name="supplier_id"]').val()) {
            isValid = false;
            errorMessages.push("Please select a supplier.");
        }

        // 2. Validate at least one product is added
        const productsInTable = $("#purchase_order").bootstrapTable("getData");
        if (productsInTable.length === 0) {
            isValid = false;
            errorMessages.push("Please add at least one product to the purchase order.");
        }

        // 3. Validate each product row
        $("#purchase_order tbody tr").each(function() {
            const row = $(this);
            const productName = row.find("td:eq(1)").text().trim();

            const qtyInput = row.find(".qty");
            const priceInput = row.find(".price");
            const sellPriceInput = row.find(".sell_price");
            const discountInput = row.find(".discount");
            const expireInput = row.find(".expire");

            const qty = parseFloat(qtyInput.val()) || 0;
            const price = parseFloat(priceInput.val()) || 0;
            const sellPrice = parseFloat(sellPriceInput.val()) || 0;
            const discount = parseFloat(discountInput.val()) || 0;
            const expire = expireInput.val();

            if (qty < 1) {
                isValid = false;
                errorMessages.push(`For ${productName}: Quantity must be at least 1.`);
            }
            if (price < 0) {
                isValid = false;
                errorMessages.push(`For ${productName}: Cost price cannot be negative.`);
            }
            if (sellPrice < 0) {
                isValid = false;
                errorMessages.push(`For ${productName}: Sell price cannot be negative.`);
            }

            const subtotal = qty * price;
            if (discount < 0) {
                isValid = false;
                errorMessages.push(`For ${productName}: Discount cannot be negative.`);
            } else if (discount > subtotal) {
                isValid = false;
                errorMessages.push(`For ${productName}: Discount cannot be greater than subtotal.`);
            }

            if (expire && new Date(expire) < new Date(new Date().setHours(0, 0, 0, 0))) {
                isValid = false;
                errorMessages.push(`For ${productName}: Expiration date cannot be in the past.`);
            }
        });

        if (!isValid) {
            errorMessages.forEach(msg => showToastMessage(msg, "error"));
            return;
        }

        // If all validation passes, submit the form via AJAX
        var formData = new FormData(this);
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        // Recalculate and append payments if the section is visible
        if ($('#add_payment_row').is(':visible')) {
            recalculateTotalPaid();
            let totalPaidInBase = parseFloat($('#total_paid_input').val()) || 0;
            formData.append('total_paid_amount', totalPaidInBase);

            // Append payment details
            let payments = [];
            $('#payments_table tbody tr').each(function() {
                payments.push({
                    currency_id: $(this).find('.payment-currency').val(),
                    amount: $(this).find('.payment-amount').val(),
                    note: $(this).find('.payment-note').val(),
                });
            });
            formData.append('payments', JSON.stringify(payments));
        }


        $.ajax({
            type: "POST",
            url: this.action,
            dataType: "json",
            data: formData,
            processData: false,
            contentType: false,
            success: function(result) {

                if (result.error == false) {
                    showToastMessage(result.message, "success");
                    setTimeout(() => {
                        window.location.href = base_url + "/admin/purchases";
                    }, 1000);
                } else {
                    const messages = result.message;
                    if (typeof messages === 'object' && messages !== null) {
                        Object.values(messages).forEach(msg => showToastMessage(msg, "error"));
                    } else {
                        showToastMessage(messages, "error");
                    }
                }
            },
            error: function(jqXHR, textStatus, errorThrown) {
                showToastMessage('An error occurred. Please try again.', 'error');
                console.error(textStatus, errorThrown);
            }
        });
    });

    $(document).ready(function() {
        // Hide add payment button and payment row on page load
        $('#add_payment_row').hide();
        $('#payments_table tbody').html('');

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
                    const formattedRate = numeral(rate).format('0,0') || 0;
                    const rateText = rate > 0 ? `${baseCurrency ? baseCurrency.code : 'BASE'} = ${formattedRate} ${baseCurrency.symbol}` : 'No rate set';

                    $selector.append(`<option value="${currency.id}">100${currency.symbol} (${currency.code}) - ${rateText}</option>`);
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
                return numeral(amount).format('0,0.00');
            }

            const symbol = currencyData.symbol || '';
            const position = parseInt(currencyData.symbol_position) || 0;
            let decimals = parseInt(currencyData.decimal_places) || 2;

            // Special case: IQD = no decimals
            if (parseInt(currencyData.is_base) == 1) {
                decimals = 0;
            }

            const decimalPattern = '0,0.' + '0'.repeat(decimals);
            const formattedAmount = numeral(amount).format(decimalPattern);

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

        // Function to handle the currency update logic
        function triggerCurrencyUpdate() {
            setTimeout(function() {
                if (selectedCurrency !== 'base') {
                    updateTotalsWithCurrency();
                }
            }, 100); // 100ms delay
        }
        $(document).on('change', '#purchase_order input, #purchase_order select, #order_discount, #shipping', function() {
            triggerCurrencyUpdate();
        });

        // --- MULTI-CURRENCY PAYMENTS LOGIC ---
        let paymentRowIndex = 1;
        let paymentCurrencies = [];
        let paymentRates = {};

        function fetchCurrenciesAndRates() {
            $.ajax({
                url: base_url + '/admin/purchases/get_currencies_for_purchases',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        paymentCurrencies = response.currencies || [];
                        paymentRates = response.exchange_rates || {};
                        populatePaymentCurrencyDropdowns();
                    }
                }
            });
        }

        function populatePaymentCurrencyDropdowns() {
            $('.payment-currency').each(function() {
                const $select = $(this);
                // Store current value
                const currentValue = $select.val();
                $select.empty();
                paymentCurrencies.forEach(function(currency) {
                    $select.append(`<option value="${currency.id}">${currency.name} (${currency.code})</option>`);
                });
                // Restore previous value if possible
                if (currentValue && $select.find(`option[value='${currentValue}']`).length > 0) {
                    $select.val(currentValue);
                } else {
                    // If not found, keep the first option selected
                    $select.prop('selectedIndex', 0);
                }
                $select.trigger('change');
            });
        }

        function recalcConvertedIQD($row) {
            const currencyId = $row.find('.payment-currency').val();
            const amountStr = $row.find('.payment-amount').val(); // Get as string
            const amount = parseFloat(removeCommas(amountStr)) || 0; // Remove commas before parsing

            let rate = 1; // Default rate
            let currencyCode = '';

            if (paymentCurrencies && paymentCurrencies.length > 0) {
                const currencyObj = paymentCurrencies.find(c => String(c.id) == String(currencyId));
                if (currencyObj) {
                    currencyCode = currencyObj.code;
                    // Get rate from paymentRates
                    rate = parseFloat(paymentRates[currencyId]) || 1;
                }
            }

            $row.find('.payment-rate').val(rate); // Set the rate input

            let convertedRaw;
            if (currencyCode === 'IQD') {
                convertedRaw = Math.round(amount * rate); // No decimals for IQD
            } else {
                convertedRaw = parseFloat(((amount * rate) / 100).toFixed(2)); // Ensure it's a number
            }

            // Update the hidden input with the raw numeric value
            $row.find('.payment-converted-iqd-raw').val(convertedRaw);
            // Update the span/display element with the formatted value
            $row.find('.payment-converted-iqd-display').text(formatNumberWithCommas(convertedRaw));
        }

        function formatNumberWithCommas(x) {
            if (!x) return '';
            x = x.toString().replace(/,/g, '');
            let parts = x.split('.');
            parts[0] = parts[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            return parts.join('.');
        }



        $(document).on('input change', '.payment-currency, .payment-amount', function() { // Use 'input' for payment-amount for real-time
            const $row = $(this).closest('tr');
            recalcConvertedIQD($row);
            // The display is updated by recalcConvertedIQD
        });

        // Format payment amounts as they are typed
        $(document).on('input', '.payment-amount', function() {
            const $input = $(this);
            let value = $input.val();

            // Remove any characters except digits and dot
            value = value.replace(/[^\d.]/g, '');

            // Only allow one decimal point
            const parts = value.split('.');
            if (parts.length > 2) {
                value = parts[0] + '.' + parts.slice(1).join('');
            }

            // Format integer part with commas
            let integerPart = parts[0];
            let decimalPart = parts[1] !== undefined ? '.' + parts[1] : '';
            // Remove leading zeros unless before a dot
            integerPart = integerPart.replace(/^0+(?!$)/, '');
            // Add commas to integer part
            integerPart = integerPart.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
            value = integerPart + decimalPart;

            $input.val(value);

            // Trigger conversion calculation
            const $row = $input.closest('tr');
            recalcConvertedIQD($row);
        });

        $('#add_payment_row').on('click', function() {
            const $paymentsTableBody = $('#payments_table tbody');
            const rowIdx = $paymentsTableBody.find('tr').length;

            let currencyOptions = '';
            paymentCurrencies.forEach(function(currency) {
                currencyOptions += `<option value="${currency.id}">${currency.name} (${currency.code})</option>`;
            });

            let now = new Date();
            let nowStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

            const newRow = `
                <tr class="payment-row">
                    <td><select class="form-control payment-currency" name="payments[${rowIdx}][currency_id]">${currencyOptions}</select></td>
                    <td>
                        <input type="text" class="form-control payment-amount" name="payments[${rowIdx}][amount]" value="" />
                    </td>
                    <td><input type="number" step="any" class="form-control payment-rate" name="payments[${rowIdx}][rate_at_payment]" value="1" readonly /></td>
                    <td>
                        <input type="hidden" class="payment-converted-iqd-raw" name="payments[${rowIdx}][converted_iqd]" value="" />
                        <span class="form-control-static payment-converted-iqd-display">${formatNumberWithCommas(0)}</span>
                    </td>
                    <td><input type="text" class="form-control payment-date" name="payments[${rowIdx}][paid_at]" value="${nowStr}" /></td>
                    <td><button type="button" class="btn btn-danger btn-sm remove-payment-row">×</button></td>
                    <input type="hidden" name="payments[${rowIdx}][payment_type]" value="cash" />
                </tr>
            `;

            $paymentsTableBody.append(newRow);
            populatePaymentCurrencyDropdowns();

            // Initialize flatpickr for the new payment date field
            const $newRow = $paymentsTableBody.find('tr:last-child');
            if ($newRow.length) {
                initPaymentDatePickersForElement($newRow.find('.payment-date'));
                recalcConvertedIQD($newRow);
            }

            // Enable remove buttons for all rows except the first one
            $paymentsTableBody.find('.remove-payment-row').prop('disabled', false);
        });

        // Handle remove payment row
        $(document).on('click', '.remove-payment-row', function() {
            const $row = $(this).closest('tr');
            const $paymentsTableBody = $('#payments_table tbody');

            if ($paymentsTableBody.find('tr').length > 1) {
                $row.remove();

                // Re-index the remaining rows
                $paymentsTableBody.find('tr').each(function(index) {
                    $(this).find('select, input').each(function() {
                        const name = $(this).attr('name');
                        if (name) {
                            $(this).attr('name', name.replace(/\[\d+\]/, `[${index}]`));
                        }
                    });
                });

                // Disable remove button if only one row remains
                if ($paymentsTableBody.find('tr').length === 1) {
                    $paymentsTableBody.find('.remove-payment-row').prop('disabled', true);
                }
            }
            updatePaymentsSummary();
        });

        // Before form submit, ensure all payment amounts are properly formatted
        $('.payment-amount').each(function() {
            const value = $(this).val();
            if (value && value.includes(',')) {
                $(this).val(removeCommas(value));
            }
        });


        // Initial fetch
        fetchCurrenciesAndRates();

        // Initialize the first payment row when currencies are loaded
        function initializeFirstPaymentRow() {
            if (paymentCurrencies.length > 0) {
                const $firstRow = $('#payments_table tbody tr:first-child');
                if ($firstRow.length) {
                    populatePaymentCurrencyDropdowns();

                    // Set default date for the first row
                    let now = new Date();
                    let nowStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');
                    $firstRow.find('.payment-date').val(nowStr);

                    // Initialize flatpickr for the first row
                    initPaymentDatePickersForElement($firstRow.find('.payment-date'));
                }
            }

        }

        // Call initialization after currencies are loaded
        setTimeout(initializeFirstPaymentRow, 1000);

        // Initialize flatpickr for existing payment date fields on page load
        $(document).ready(function() {
            // Initialize flatpickr for any existing payment date fields
            $('.payment-date').each(function() {
                if (!$(this).hasClass('flatpickr-input')) {
                    initPaymentDatePickersForElement(this);
                }
            });
        });

        function initPaymentDatePickersForElement(element) {
            // If element is provided, initialize only that element
            if (element) {
                // Check if flatpickr is already initialized
                if (!$(element).hasClass('flatpickr-input')) {
                    $(element).flatpickr({
                        enableTime: true,
                        dateFormat: 'Y-m-d H:i',
                        time_24hr: true,
                        allowInput: true,
                        defaultDate: new Date()
                    });
                }
            } else {
                // Initialize all payment date fields that don't already have flatpickr
                $('.payment-date').each(function() {
                    if (!$(this).hasClass('flatpickr-input')) {
                        $(this).flatpickr({
                            enableTime: true,
                            dateFormat: 'Y-m-d H:i',
                            time_24hr: true,
                            allowInput: true,
                            defaultDate: new Date()
                        });
                    }
                });
            }
        }

        // Handle payment status changes
        $('#payment_status_item').on('change', function() {
            const status = $(this).val();
            const $paymentsTableBody = $('#payments_table tbody');
            const $paymentsTableGroup = $('#payments_table').closest('.form-group');
            const $addPaymentRowButton = $('#add_payment_row');

            if (status === 'fully_paid') {
                $paymentsTableGroup.hide();
                $addPaymentRowButton.hide();

                // Show payment summary if there are existing payments
                if (hasExistingPayments) {
                    $('#payments_summary_row').show();
                    updatePaymentsSummary();
                } else {
                    $('#payments_summary_row').hide();
                }

                const total = parseFloat(removeCommas($('input[name="total"]').val())) || 0;
                let iqdCurrency = paymentCurrencies.find(c => c.code === 'IQD');
                let iqdId = iqdCurrency ? iqdCurrency.id : '';
                let now = new Date();
                let nowStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

                $paymentsTableBody.html(`
                        <tr class="payment-row">
                            <td>
                            <select class="form-control payment-currency"  disabled>
                                <option value="${iqdId}" selected>IQD</option>
                            </select>
                            <input type="hidden" name="payments[0][currency_id]" value="${iqdId}" />
                            </td>                
                            <td><input type="text" class="form-control payment-amount" name="payments[0][amount]" value="${formatNumberWithCommas(total)}" readonly /></td>
                            <td><input type="number" step="any" class="form-control payment-rate" name="payments[0][rate_at_payment]" value="1" readonly /></td>
                            <td>
                                <input type="hidden" class="payment-converted-iqd-raw" name="payments[0][converted_iqd]" value="${total}" />
                                <span class="form-control-static payment-converted-iqd-display">${formatNumberWithCommas(total)}</span>
                            </td>
                            <td><input type="text" class="form-control payment-date" name="payments[0][paid_at]" value="${nowStr}" readonly /></td>
                            <td><button type="button" class="btn btn-danger btn-sm remove-payment-row" disabled>×</button></td>
                            <input type="hidden" name="payments[0][payment_type]" value="cash" />
                        </tr>
                    `);

                // Initialize flatpickr for the payment date field (even though it's readonly)
                const $newRow = $paymentsTableBody.find('tr:first-child');
                if ($newRow.length) {
                    initPaymentDatePickersForElement($newRow.find('.payment-date'));
                }

            } else if (status === 'unpaid' || status === 'cancelled') {
                $paymentsTableGroup.hide();
                $addPaymentRowButton.hide();
                $paymentsTableBody.html('');
                $('#payments_summary_row').hide();
                // Set summary values to 0
                $('#total_payments_display').text('0');
                $('#remaining_payments_display').text('0');
            } else if (status === 'partially_paid') {
                $paymentsTableGroup.show();
                $addPaymentRowButton.show();
                $('#payments_summary_row').show();

                if ($paymentsTableBody.find('tr').length == 0 || $paymentsTableBody.find('tr').length == 1) {
                    const rowIdx = 0; // Or manage index dynamically if adding multiple rows
                    let currencyOptions = '';
                    paymentCurrencies.forEach(function(currency) {
                        currencyOptions += `<option value="${currency.id}">${currency.name} (${currency.code})</option>`;
                    });
                    let now = new Date();
                    let nowStr = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0') + '-' + String(now.getDate()).padStart(2, '0') + ' ' + String(now.getHours()).padStart(2, '0') + ':' + String(now.getMinutes()).padStart(2, '0');

                    // Ensure paymentCurrencies and paymentRates are loaded before creating the first row if status is 'partially_paid' on load
                    if (paymentCurrencies.length === 0) {
                        // Defer adding row or show loading, fetch if not already fetching
                        console.warn("Payment currencies not loaded yet for new row.");
                        // Potentially call fetchCurrenciesAndRates and then add row in its success callback
                    }

                    const initialAmount = ""; // Or 0
                    const initialConverted = 0; // Or ""
                    $paymentsTableBody.html(`
                            <tr class="payment-row">
                                <td><select class="form-control payment-currency" name="payments[${rowIdx}][currency_id]">${currencyOptions}</select></td>
                                <td>
                                    <input type="text" class="form-control payment-amount" name="payments[${rowIdx}][amount]" value="${formatNumberWithCommas(initialAmount)}" />
                                </td>
                                <td><input type="number" step="any" class="form-control payment-rate" name="payments[${rowIdx}][rate_at_payment]" value="1" readonly /></td>
                                <td>
                                    <input type="hidden" class="payment-converted-iqd-raw" name="payments[${rowIdx}][converted_iqd]" value="" />
                                    <span class="form-control-static payment-converted-iqd-display">${formatNumberWithCommas(0)}</span>
                                </td>
                                <td><input type="text" class="form-control payment-date" name="payments[${rowIdx}][paid_at]" value="${nowStr}" /></td>
                                <td><button type="button" class="btn btn-danger btn-sm remove-payment-row" ${rowIdx === 0 ? 'disabled' : ''}>×</button></td>
                                <input type="hidden" name="payments[${rowIdx}][payment_type]" value="cash" />
                            </tr>
                        `);
                    populatePaymentCurrencyDropdowns(); // Ensure new dropdowns are populated

                    // Initialize flatpickr for the new payment date field
                    const $newRow = $paymentsTableBody.find('tr:first-child');
                    if ($newRow.length) {
                        initPaymentDatePickersForElement($newRow.find('.payment-date'));
                        recalcConvertedIQD($newRow);
                    }
                }
                // Enable all fields in existing rows
                $paymentsTableBody.find('input, select').not('[name*="[converted_iqd]"]').prop('readonly', false).prop('disabled', false);
                $paymentsTableBody.find('.remove-payment-row').prop('disabled', $paymentsTableBody.find('tr').length <= 1);
                $paymentsTableBody.find('.payment-rate, .payment-converted-iqd-raw').prop('readonly', true); // Keep these calculated fields readonly
            }
        }).trigger('change');

        // On page load, trigger the change to set the correct state
        $('#payment_status_item').trigger('change');

        // Add summary row and currency selector below payments table
        if ($('#payments_summary_row').length === 0) {
            $('#payments_table').after(`
                <div id="payments_summary_row" style="margin-top:10px; display:none;">
                    <div class="form-inline">
                        <label for="summary_currency_selector" style="margin-right:8px;">Display in:</label>
                        <select id="summary_currency_selector" class="form-control" style="width:auto; margin-right:16px;"></select>
                        <span><strong>Total Payments:</strong> <span id="total_payments_display">0</span></span>
                        <span style="margin-left:24px;"><strong>Remaining:</strong> <span id="remaining_payments_display">0</span></span>
                    </div>
                </div>
            `);
        }

        // Populate summary currency selector
        function populateSummaryCurrencyDropdown() {
            const $selector = $('#summary_currency_selector');
            $selector.empty();
            if (baseCurrency) {
                $selector.append(`<option value="base">${baseCurrency.name} (${baseCurrency.code})</option>`);
            }
            currencies.forEach(currency => {
                const isBase = parseInt(currency.is_base) === 1 || currency.is_base === '1';
                if (!isBase) {
                    $selector.append(`<option value="${currency.id}">${currency.name} (${currency.code})</option>`);
                }
            });
        }

        // Calculate and update summary values
        function updatePaymentsSummary() {
            let totalIQD = 0;
            $('.payment-converted-iqd-raw').each(function() {
                totalIQD += parseFloat($(this).val()) || 0;
            });
            const purchaseTotal = parseFloat($('input[name="total"]').val()) || 0;
            let remainingIQD = purchaseTotal - totalIQD;
            if (remainingIQD < 0) remainingIQD = 0;

            // Get selected summary currency
            const selectedSummaryCurrency = $('#summary_currency_selector').val() || 'base';
            let totalDisplay = totalIQD;
            let remainingDisplay = remainingIQD;
            if (selectedSummaryCurrency !== 'base') {
                const rate = exchangeRates[selectedSummaryCurrency];
                if (rate && rate > 0) {
                    totalDisplay = (totalIQD / rate) * 100;
                    remainingDisplay = (remainingIQD / rate) * 100;
                }
            }
            $('#total_payments_display').text(formatCurrencyDisplay(totalDisplay, selectedSummaryCurrency));
            $('#remaining_payments_display').text(formatCurrencyDisplay(remainingDisplay, selectedSummaryCurrency));
        }

        // Update summary when payments or currency changes
        $(document).on('input change', '.payment-amount, .payment-currency, .payment-converted-iqd-raw', function() {
            updatePaymentsSummary();
        });
        $(document).on('change', '#summary_currency_selector', function() {
            updatePaymentsSummary();
        });
        $(document).on('change', '#currency_selector', function() {
            populateSummaryCurrencyDropdown();
            updatePaymentsSummary();
        });

        // Populate summary currency dropdown after currencies are loaded
        setTimeout(function() {
            populateSummaryCurrencyDropdown();
            updatePaymentsSummary();
        }, 1200);

        // --- PREFILL PRODUCTS/ITEMS TABLE AND PAYMENTS (EDIT MODE) ---
        function prefillProductsAndPaymentsIfReady() {
            // Prefill products
            if (window.prefillPurchaseItems && Array.isArray(window.prefillPurchaseItems)) {
                $('#purchase_order').bootstrapTable('removeAll');
                let variantData = [];
                window.prefillPurchaseItems.forEach(function(item, idx) {
                    let tableRow = {
                        state: false,
                        id: item.product_variant_id,
                        variant_ids: `<input type='hidden' name='variant_ids[]' value='${item.product_variant_id}'>`,
                        image: item.image ? `<img src='${site_url}${item.image}' width='60' height='60' class='img-thumbnail' alt='Product Image'>` : '',
                        name: item.product_name ? item.product_name + ' - ' + (item.variant_name || '') : (item.variant_name || ''),
                        quantity: `<input type='number' class='form-control qty' name='qty[${item.product_variant_id}]' value='${item.quantity}' min='1' step='1'>`,
                        price: `<input type='number' class='form-control price' name='price[${item.product_variant_id}]' value='${item.price}' min='0.01' step='0.01'>`,
                        sell_price: `<input type='number' class='form-control sell_price' name='sell_price[${item.product_variant_id}]' value='${item.sell_price || ''}' min='0' step='0.01'>`,
                        expire: `<input type='text' class='form-control expire' name='expire[${item.product_variant_id}]' value='${item.expire || ''}' placeholder='YYYY-MM-DD' autocomplete='off'>`,
                        discount: `<input type='number' class='form-control discount' name='discount[${item.product_variant_id}]' value='${item.discount || 0}' min='0' step='0.01'>`,
                        total: `<span class='table_price'>${(item.quantity * item.price - (item.discount || 0)).toFixed(2)}</span>`,
                        hidden_inputs: ''
                    };
                    $('#purchase_order').bootstrapTable('insertRow', {
                        index: idx,
                        row: tableRow
                    });
                    variantData.push({
                        id: item.product_variant_id,
                        name: item.product_name,
                        variant: item.variant_name,
                        price: parseFloat(item.price) || 0
                    });
                });
                $('input[name="products"]').val(JSON.stringify(variantData));
                setTimeout(function() {
                    purchase_total();
                }, 200);
            }
            // Prefill payments only if currencies are loaded
            if (window.prefillPurchasePayments && Array.isArray(window.prefillPurchasePayments) && typeof paymentCurrencies !== 'undefined' && paymentCurrencies.length > 0) {
                $('#payments_table tbody').html('');
                window.prefillPurchasePayments.forEach(function(payment, idx) {
                    let currencyOptions = '';
                    paymentCurrencies.forEach(function(currency) {
                        currencyOptions += `<option value='${currency.id}' ${(currency.id == payment.currency_id) ? 'selected' : ''}>${currency.name} (${currency.code})</option>`;
                    });
                    let row = `
                        <tr class='payment-row'>
                            <td><select class='form-control payment-currency' name='payments[${idx}][currency_id]'>${currencyOptions}</select></td>
                            <td><input type='text' class='form-control payment-amount' name='payments[${idx}][amount]' value='${payment.amount}' /></td>
                            <td><input type='number' step='any' class='form-control payment-rate' name='payments[${idx}][rate_at_payment]' value='${payment.rate_at_payment || 1}' readonly /></td>
                            <td>
                                <input type='hidden' class='payment-converted-iqd-raw' name='payments[${idx}][converted_iqd]' value='${payment.converted_iqd || ''}' />
                                <span class='form-control-static payment-converted-iqd-display'>${payment.converted_iqd || 0}</span>
                            </td>
                            <td><input type='text' class='form-control payment-date' name='payments[${idx}][paid_at]' value='${payment.paid_at || ''}' /></td>
                            <td><button type='button' class='btn btn-danger btn-sm remove-payment-row' ${(idx === 0) ? 'disabled' : ''}>×</button></td>
                            <input type='hidden' name='payments[${idx}][payment_type]' value='${payment.payment_type || 'cash'}' />
                        </tr>
                    `;
                    $('#payments_table tbody').append(row);
                });
                $('#payments_table tbody .remove-payment-row').prop('disabled', $('#payments_table tbody tr').length <= 1);

                // Show payment summary when there are existing payments
                if (window.prefillPurchasePayments.length > 0) {
                    $('#payments_summary_row').show();
                    updatePaymentsSummary();
                }
            }
        }

        // Call prefill logic after currencies are loaded
        function tryPrefillAfterCurrenciesLoaded() {
            if (typeof paymentCurrencies !== 'undefined' && paymentCurrencies.length > 0) {
                prefillProductsAndPaymentsIfReady();
            } else {
                setTimeout(tryPrefillAfterCurrenciesLoaded, 100);
            }
        }

        // On page load, try to prefill after currencies are loaded
        tryPrefillAfterCurrenciesLoaded();
    });

    document.addEventListener('DOMContentLoaded', function() {
        const validation = new JustValidate('#purchase_form', {
            focusInvalidField: true,
            validateBeforeSubmitting: true,
            lockForm: true,
        });

        validation
            .addField('[name="supplier_id"]', [{
                rule: 'required',
                errorMessage: 'Supplier is required'
            }])
            .addField('[name="warehouse_id"]', [{
                rule: 'required',
                errorMessage: 'Warehouse is required'
            }])
            .addField('[name="purchase_date"]', [{
                rule: 'required',
                errorMessage: 'Purchase date is required'
            }])
            .addField('[name="status"]', [{
                rule: 'required',
                errorMessage: 'Status is required'
            }])
            .addField('[name="payment_status"]', [{
                    rule: 'required',
                    errorMessage: 'Payment status is required'
                },
                {
                    validator: (value) => value !== 'pending',
                    errorMessage: 'Please select a valid payment status'
                }
            ])
            .onSuccess(async (event) => {
                // Prepare products data before submission
                const products = $('#products_input').val();

                // Custom validation for payments
                const total = parseFloat(document.querySelector('[name="total"]').value) || 0;
                let totalPaid = 0;

                // Check for payment-converted-iqd-raw elements (hidden inputs)
                document.querySelectorAll('.payment-converted-iqd-raw').forEach(function(input) {
                    totalPaid += parseFloat(input.value) || 0;
                });

                const status = document.getElementById('payment_status_item').value;

                // Skip payment validation for unpaid and cancelled statuses
                if (status === 'unpaid' || status === 'cancelled') {
                    // No payment validation needed for these statuses
                    // cant pay more than 50 IQD to be in partially paid status
                } else if (totalPaid > total + 50) {
                    showToastMessage('Total payments cannot exceed the purchase total.', 'error');
                    return;
                } else if (status === 'fully_paid') {
                    const $paymentsTableBody = $('#payments_table tbody');
                    const $row = $paymentsTableBody.find('tr.payment-row').first();
                    if ($row.length) {
                        $row.find('.payment-amount').val(total);
                        $row.find('.payment-converted-iqd-raw').val(total);
                        $row.find('.payment-converted-iqd-display').text(total);
                    }
                    totalPaid = total;
                } else if (status === 'partially_paid') {
                    const hasPayments = document.querySelectorAll('.payment-amount').length > 0;
                    const hasValidPayment = Array.from(document.querySelectorAll('.payment-amount')).some(input =>
                        parseFloat(removeCommas(input.value)) > 0
                    );

                    if (!hasPayments || !hasValidPayment) {
                        showToastMessage('For partially paid status, please enter at least one payment.', 'error');
                        return;
                    }
                }

                // Before form submit, ensure all payment amounts are properly formatted
                $('.payment-amount').each(function() {
                    const value = $(this).val();
                    if (value && value.includes(',')) {
                        $(this).val(removeCommas(value));
                    }
                });

                // Submit the form
                const form = event.target;
                const formData = new FormData(form);
                formData.append('products', products);
                try {
                    const response = await axios.post(form.action, formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data'
                        }
                    });

                    if (response.data.success) {
                        showToastMessage(response.data.message, 'success');
                        // Redirect to purchases list after successful submission
                        // setTimeout(() => {
                        //     window.location.href = base_url + '/admin/purchases';
                        // }, 1500);
                    } else {
                        if (typeof response.data.message === 'object') {
                            // Handle validation errors
                            Object.values(response.data.message).forEach(error => {
                                showToastMessage(error, 'error');
                            });
                        } else {
                            showToastMessage(response.data.message || 'An error occurred', 'error');
                        }
                    }
                } catch (error) {
                    console.error('Form submission error:', error);
                    if (error.response && error.response.data) {
                        if (typeof error.response.data.message === 'object') {
                            Object.values(error.response.data.message).forEach(errorMsg => {
                                showToastMessage(errorMsg, 'error');
                            });
                        } else {
                            showToastMessage(error.response.data.message || 'An error occurred during the request.', 'error');
                        }
                    } else {
                        showToastMessage('An error occurred during the request.', 'error');
                    }
                }
            });
    });

    // Patch: Always use the correct variant ID for each product in the products array
    function getProductsForSubmission() {
        let products = [];
        const tableData = $('#purchase_order').bootstrapTable('getData');
        tableData.forEach(function(row) {
            // The variant ID is always in row.id
            products.push({
                id: row.id, // This is product_variant_id
                name: row.name,
                price: parseFloat($(row.price).val ? $(row.price).val() : row.price) || 0,
                qty: parseFloat($(row.quantity).val ? $(row.quantity).val() : row.quantity) || 0,
                discount: parseFloat($(row.discount).val ? $(row.discount).val() : row.discount) || 0,
                sell_price: parseFloat($(row.sell_price).val ? $(row.sell_price).val() : row.sell_price) || 0,
                expire: $(row.expire).val ? $(row.expire).val() : row.expire
            });
        });
        return products;
    }

    // On form submit, serialize products using the above function
    $('#purchase_form').off('submit').on('submit', function(e) {
        // ... existing code ...
        const products = JSON.stringify(getProductsForSubmission());
        $('#products_input').val(products);
    });
</script>