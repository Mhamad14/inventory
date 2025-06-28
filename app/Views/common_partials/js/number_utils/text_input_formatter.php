<script>
    document.addEventListener('alpine:init', () => {
        Alpine.directive('numberformat', (el, {

            expression
        }, {
            evaluate
        }) => {
            const defaults = {
                decimals: 2,
                allowNegative: true,
                allowDecimal: true
            };
            const options = {
                ...defaults,
                ...(expression ? evaluate(expression) : {})
            };

            const formatNumber = (value) => {
                if (value === '' || value === null || value === undefined) return '';

                let num = value.replace(new RegExp(`[^\\d${options.allowNegative ? '-' : ''}${options.allowDecimal ? '.' : ''}]`, 'g'), '');

                if (options.allowDecimal) {
                    const parts = num.split('.');
                    if (parts.length > 2) {
                        num = parts[0] + '.' + parts.slice(1).join('');
                    }
                }

                const [integer, decimal] = num.split('.');
                let formattedInteger = integer.replace(/\B(?=(\d{3})+(?!\d))/g, ',');

                // Only limit decimal places if decimal exists and allowDecimal is true
                if (options.allowDecimal && decimal !== undefined) {
                    // Don't limit the decimal places during input
                    // Only format when not actively typing (e.g., on blur)
                    const isTypingDecimal = el === document.activeElement && value.endsWith('.');

                    if (!isTypingDecimal) {
                        return `${formattedInteger}.${decimal}`;
                    }
                    return `${formattedInteger}.${decimal}`;
                }

                return formattedInteger;
            };
            el.addEventListener('blur', () => {
                const [integer, decimal] = el.value.split('.');
                if (decimal && decimal.length > options.decimals) {
                    // Truncate to specified decimal places on blur
                    el.value = `${integer.replace(/,/g, '')}.${decimal.substring(0, options.decimals)}`;
                    el.value = formatNumber(el.value);
                }
            });
            // ... rest of the directive code ...
        });
    });
</script>