// Function to update totals
function updateBatchTotals() {
    let subtotal = 0;
    let sellTotal = 0;
    let profitTotal = 0;

    // Get all rows from the batches table
    $('#form_batches_items tbody tr').each(function() {
        const quantity = parseFloat($(this).find('td[data-field="quantity"]').text()) || 0;
        const costPrice = parseFloat($(this).find('td[data-field="cost_price"]').text().replace(/[^0-9.-]+/g, '')) || 0;
        const sellPrice = parseFloat($(this).find('td[data-field="sell_price"]').text().replace(/[^0-9.-]+/g, '')) || 0;
        const discount = parseFloat($(this).find('td[data-field="discount"]').text()) || 0;

        const rowTotal = (quantity * costPrice) - discount;
        const rowSellTotal = quantity * sellPrice;
        
        subtotal += rowTotal;
        sellTotal += rowSellTotal;
    });

    profitTotal = sellTotal - subtotal;

    // Update the totals in the view
    $('#sub_total').text(currency_location(subtotal.toFixed(2)));
    $('#sell_total').text(currency_location(sellTotal.toFixed(2)));
    $('#profit_total').text(currency_location(profitTotal.toFixed(2)));

    // Update hidden inputs
    $('#total').val(subtotal.toFixed(2));
    $('#sell_total_input').val(sellTotal.toFixed(2));
    $('#profit_total_input').val(profitTotal.toFixed(2));

    // Update final total with shipping and discount
    const orderDiscount = parseFloat($('#batch_discount').val()) || 0;
    const shipping = parseFloat($('#batch_shipping').val()) || 0;
    const finalTotal = subtotal - orderDiscount + shipping;

    // Also update the display and hidden input for compatibility
    $('#final_total').text(currency_location(finalTotal.toFixed(2)));
    $('input[name="final_total"]').val(finalTotal.toFixed(2));

    // Try to update Alpine store if available
    try {
        if (typeof Alpine !== 'undefined') {
            Alpine.store('purchase').updateFinalTotal(finalTotal);
        }
    } catch (e) {
        console.warn('Alpine.js store update failed:', e);
    }
}

// Bind events to update totals
$(document).ready(function() {
    // Update totals when the table is loaded or refreshed
    $('#form_batches_items').on('load-success.bs.table refresh.bs.table', function() {
        updateBatchTotals();
    });

    // Update totals when discount or shipping changes
    $('#batch_discount, #batch_shipping').on('input', function() {
        updateBatchTotals();
    });
});