<script>
    function variantForm() {
        return {
            selected: null,
            form: {
                purchase_id: $purchase_id,
                warehouse_id: $warehouse_id,
                variant_id: '',
                quantity: 1,
                cost_price: '',
                sell_price: '',
                expire_date: '',
                discount: '',
                status: 'active'
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
                    if (this.selected && this.$refs.expireDateInput) {
                        flatpickr(this.$refs.expireDateInput, {
                            dateFormat: 'Y-m-d',
                            allowInput: true,
                            minDate: 'today',
                            onChange: (selectedDates, dateStr) => {
                                this.form.expire_date = dateStr;
                            }
                        });
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
                    this.form.variant_id = variant.id;
                });
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

            async submitForm() {
                try {
                    const base_url = '<?= site_url() ?>';
                    await axios.post(base_url + 'admin/batches/add_to_existing_purchase', this.form);
                    
                    this.resetForm();
                } catch (e) {
                    alert('Error saving data');
                    console.error(e);
                }
            },

            resetForm() {
                this.selected = null;
                this.form = {
                    purchase_id: $purchase_id,
                    warehouse_id: $warehouse_id,
                    variant_id: '',
                    quantity: '',
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