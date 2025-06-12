<script>
    document.addEventListener('alpine:init', () => {
        Alpine.store('purchase', {
            finalTotal: <?= decimal_points($purchase['total']) ?? 0 ?>
        });
    });


    function variantForm() {
        return {
            selected: null,
            errors: {},
            form: {
                purchase_id: $purchase_id,
                warehouse_id: $warehouse_id,
                variant_id: '',
                quantity: 1,
                cost_price: '',
                sell_price: '',
                expire_date: '',
                discount: '',
                status: 'active',
                cost_total: 0,
                sell_total: 0,
                profit: 0,
            },
            choices: null,

            async init() {
                const select = document.getElementById('variant-select');


                // Initialize Choices only once
                if (!select.classList.contains('choices-initialized')) {
                    this.choices = new Choices(select, {
                        searchEnabled: true,
                        placeholderValue: 'Type to search...',
                        shouldSort: false,
                        removeItemButton: true,

                        // ✅ Custom template with image
                        callbackOnCreateTemplates: function(template) {
                            return {
                                item: (classNames, data) => {
                                    const label = data?.label ?? 'Unnamed';
                                    const imageUrl = data?.customProperties?.image ?? '<?= site_url('public/favicon.ico') ?>';

                                    return template(`
                                    <div class="${classNames.item} choices__item--custom"
                                        data-item
                                        data-id="${data.id}"
                                        data-value="${data.value}"
                                        ${data.active ? 'aria-selected="true"' : ''}
                                        ${data.disabled ? 'aria-disabled="true"' : ''}>
                                        <img src="${imageUrl}" alt="no image" style="height: 25px; margin-right: 8px; vertical-align: middle; object-fit: cover;" />
                                        <span>${label}</span>
                                    </div>
                                `);
                                },
                                choice: (classNames, data) => {
                                    const label = data?.label ?? 'Unnamed';
                                    const imageUrl = data?.customProperties?.image ?? '<?= site_url('public/favicon.ico') ?>';
                                    const stock = data?.customProperties?.stock ?? '-';
                                    return template(`
                                        <div class="${classNames.item} choices__item--selectable"
                                            data-select-text="${label}"
                                            data-choice 
                                            ${data?.disabled ? 'data-choice-disabled aria-disabled="true"' : 'data-choice-selectable'}
                                            data-id="${data?.id}" 
                                            data-value="${data?.value}" 
                                            ${data?.groupId > 0 ? 'role="treeitem"' : 'role="option"}'}>
                                            <img src="${imageUrl}" alt = 'no image' style="height: 60px; object-fit: cover; margin-right: 8px; vertical-align: middle;" />
                                            <span>${label}</span> | 
                                            <small>stock: ${stock}</small>

                                            
                                        </div>
                                    `);
                                }

                            };
                        }
                    });

                    select.classList.add('choices-initialized');
                }

                // ✅ Load initial variants
                await this.fetchVariants('');

                Alpine.effect(() => {
                    this.calculateTotals();

                    if (this.selected && this.$refs.expireDateInput) {
                        flatpickr(this.$refs.expireDateInput, {
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                            minDate: 'today',
                            onChange: (selectedDates, dateStr) => {
                                this.form.expire_date = dateStr;
                            }
                        });
                        // run initValidation only once!
                        if (!this.validationInitialized) {
                            this.initValidation();
                            this.validationInitialized = true;
                        }
                    }
                });



                // ✅ Handle typing/search
                const searchInput = select.closest('.choices').querySelector('.choices__input--cloned');
                let searchTimeout;
                searchInput.addEventListener('input', (e) => {
                    clearTimeout(searchTimeout);
                    const value = e.target.value;
                    if (value.length < 2) return;

                    searchTimeout = setTimeout(() => {
                        this.fetchVariants(value);
                    }, 300);
                });

                // ✅ Handle selection
                select.addEventListener('change', (e) => {
                    const value = e.target.value;
                    if (!value) return;

                    const variant = JSON.parse(value);
                    this.selected = {
                        id: variant.id,
                        text: `${variant.name} - ${variant.variant_name}`
                    };
                    this.form.variant_id = variant.variant_id;
                });

            },

            calculateTotals() {
                const qty = parseFloat(this.form.quantity) || 0;
                const cost = parseFloat(this.form.cost_price) || 0;
                const sell = parseFloat(this.form.sell_price) || 0;
                const discount = parseFloat(this.form.discount) || 0;

                this.form.cost_total = parseFloat((qty * cost) - discount);
                this.form.sell_total = parseFloat((qty * sell));
                this.form.profit = parseFloat((this.form.sell_total - this.form.cost_total));
            },

            // validation rules using justValidate framework
            initValidation() {
                this.validator = new JustValidate('#add_variant_form', {
                    validateBeforeSubmitting: true,
                    lockForm: true, // 👈 prevents form submission until validation is complete
                    validateOnInput: true, // 👈 enables instant validation while typing
                    focusInvalidField: true, // 👈 focuses on the first invalid field
                    errorFieldCssClass: 'is-invalid', // 👈 adds this class to invalid fields
                    successFieldCssClass: 'is-valid', // 👈 adds this class to valid fields
                    errorLabelStyle: {
                        color: '#dc3545', // Bootstrap danger color
                        fontSize: '0.875rem', // Bootstrap small text size
                    },
                    errorLabelCssClass: 'invalid-feedback', // 👈 adds this class to error labels
                });

                this.validator
                    .addField('#variant_id', [{
                        rule: 'required',
                        errorMessage: 'Please select a product variant.',
                    }, ])
                    .addField('#add_quantity', [{
                            rule: 'required',
                            errorMessage: 'Quantity is required.',
                        },
                        {
                            rule: 'minNumber',
                            value: 1,
                            errorMessage: 'Quantity must be at least 1.',
                        }
                    ])
                    .addField('#add_cost_price', [{
                            rule: 'required',
                            errorMessage: 'Cost price is required.',
                        },
                        {
                            rule: 'minNumber',
                            value: 0,
                            errorMessage: 'Cost price must be 0 or higher.',
                        }
                    ])
                    .addField('#add_discount', [{
                            rule: 'required',
                            errorMessage: 'Quantity is required.',
                        },
                        {
                            rule: 'minNumber',
                            value: 0,
                            errorMessage: 'Cost price must be 0 or higher.',
                        }
                    ])
                    .addField('#add_sell_price', [{
                            rule: 'required',
                            errorMessage: 'Sell price is required.',
                        },
                        {
                            rule: 'minNumber',
                            value: 0,
                            errorMessage: 'Sell price must be 0 or higher.',
                        }
                    ])
                    .addField('#add_expire_date', [{
                            rule: 'required',
                            errorMessage: 'Expire date is required.',
                        },
                    ])
                    .addField('#add_status', [{
                        rule: 'required',
                        errorMessage: 'Please select a status.',
                    }, ]);
            },

            async fetchVariants(search) {
                try {
                    const res = await axios.get('<?= site_url('variants/products_variants_list') ?>', {
                        params: {
                            search
                        }
                    });

                    const baseUrl = '<?= site_url() ?>';
                    const options = (res.data.variants || [])
                        .map(v => {
                            try {
                                const label = `${v.name} - ${v.variant_name}`;
                                const value = JSON.stringify(v);
                                const stock = v.stock || '-';
                                const image = v.image && v.image.trim() !== '' ?
                                    `${baseUrl}${v.image}` :
                                    `${baseUrl}public/favicon.ico`;

                                if (!v.name || !v.variant_name) throw new Error('Missing name/variant_name');

                                return {
                                    value,
                                    label,
                                    customProperties: {
                                        image,
                                        stock
                                    }
                                };
                            } catch (e) {
                                console.warn('Invalid variant skipped:', v, e.message);
                                return null;
                            }
                        })
                        .filter(Boolean); // removes nulls


                    this.choices.clearChoices();
                    this.choices.setChoices(options, 'value', 'label', true);
                } catch (err) {
                    console.error('Search error:', err);
                }
            },
            // validation

            async submitForm() {
                try {

                    const isValid = await this.validator.revalidate();
                    document.querySelectorAll('.just-validate-error-label').forEach(e => e.remove());
                    if (!isValid) {
                        throw new Error('Please fix the errors in the form.');
                    }
                    console.log('Response:', this.form);
                    return;
                    const base_url = '<?= site_url() ?>';
                    const response = await axios.post(base_url + 'admin/batches/add_to_existing_purchase', this.form);
                    
                    if (response.data.success) {
                        showToastMessage(response.data.message, 'success');
                        this.resetForm(); // ✅ Reset form only if success
                        this.choices.clearChoices();
                        const new_total = response.data.new_total || 0;
                        Alpine.store('purchase').finalTotal = new_total;

                        setTimeout(() => {
                            // window.location.reload();
                            $('#form_batches_items').bootstrapTable('refresh');
                        }, 500);
                    } else {
                        if (typeof response.data.message === 'object') {
                            for (const field in response.data.message) {
                                showToastMessage(response.data.message[field], 'error');
                            }
                        } else {
                            showToastMessage(response.data.message, 'error');
                        }
                    }

                } catch (e) {
                    showToastMessage(e.message, 'error');
                }
            },

            resetForm() {
                this.selected = null;
                this.form = {
                    purchase_id: $purchase_id,
                    warehouse_id: $warehouse_id,
                    variant_id: '',
                    quantity: 1,
                    cost_price: '',
                    sell_price: '',
                    expire_date: '',
                    discount: '',
                    status: ''
                };
                if (this.choices) {
                    this.choices.clearChoices();
                }
            }
        };
    }
</script>