

<script>
document.addEventListener('alpine:init', () => {
    Alpine.magic('number', () => ({
        unmask(value) {
            if (!value) return null;
            return parseFloat(value.toString().replace(/,/g, ''));
        },
        
        format(value, decimals = 2) {
            return parseFloat(value || 0).toLocaleString(undefined, {
                minimumFractionDigits: decimals,
                maximumFractionDigits: decimals
            });
        },
        
        // Additional helper methods
        isNumeric(value) {
            return !isNaN(parseFloat(value)) && isFinite(value);
        },
        
        currency(value, symbol = '$', decimals = 2) {
            return `${symbol}${this.format(value, decimals)}`;
        }
    }));
});
</script>