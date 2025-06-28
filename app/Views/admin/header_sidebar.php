<div class="navbar-bg" id="navebar-bg"></div>
<?php $first_name = $user->first_name; ?>

<nav x-data="headerData" class="navbar navbar-expand-lg main-navbar">
    <!-- Left navbar -->
    <div class="d-flex align-items-center">
        <ul class="navbar-nav mr-3">
            <li class="dropdown"><a href="#" data-toggle="sidebar" class="nav-link nav-link-lg"><i class="fas fa-bars"></i></a></li>
        </ul>

        <div class="dropdown-menu business">
            <?php
            foreach ($businesses as $business) {
                $business_id = isset($business['id']) ? $business['id'] : "";
                echo "<a class='dropdown-item has-icon ' href='" . base_url('admin/home/switch_businesses') . "/" . $business_id . "'> <div class='icon-box'><img class='img-fluid-business' src='" . base_url($business['icon']) . "' > " . $business['name'] . "</div></a>";
            } ?>
        </div>

        <ul class="navbar-nav">
            <li class="dropdown d-inline">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <div class="d-sm-none d-lg-inline-block"></div><?= (isset($_SESSION['business_name']) && !empty($_SESSION['business_name'])) ? $_SESSION['business_name'] : "No business found" ?>
                </a>
                <div class="dropdown-menu dropdown-menu-left business-icon">
                    <div class="dropdown-title"> <?= labels('businessess', 'Businessess') ?></div>
                    <?php
                    if (!empty($businesses)) {
                        foreach ($businesses as $business) {
                            $business_id = isset($business['id']) ? $business['id'] : "";
                            echo "<a class='align-items-center d-flex dropdown-item has-icon' href='" . base_url('admin/home/switch_businesses') . "/" . $business_id . "'><div class='align-items-center d-flex icon-box justify-content-center'><img class='img-fluid-business' src='" . base_url($business['icon']) . "' > </div><p class='ml-2 mb-0'>" . $business['name'] . "</p></a>";
                        }
                    } else {
                        echo "<a href='" . base_url("admin/businesses") . "'><i class='fa-plus-circle fas'></i> Add your first business</a>";
                    }
                    ?>
                </div>
            </li>
        </ul>
    </div>

    <!-- Right navbar - collapsible -->
    <button class="navbar-toggler fas fa-bars" type="button" data-bs-toggle="collapse" data-bs-target="#rightNavbar">
        <i></i>
    </button>

    <div class="collapse navbar-collapse justify-content-end" id="rightNavbar">
        <ul class="navbar-nav">

            <!-- Exchange Rate Button with Custom Tooltip -->
            <li class="nav-item mx-2" x-data="{ showTooltip: false }">
                <button class="btn btn-info position-relative"
                    @click="openExchangeRateModal"
                    @mouseenter="showTooltip = true"
                    @mouseleave="showTooltip = false">
                    <span x-text="buttonText">Exchange Rate</span>

                    <!-- Custom tooltip -->
                    <div x-show="showTooltip" x-transition
                        class="position-absolute top-100 start-50 translate-middle-x mt-2 p-2 bg-dark text-white rounded shadow"
                        style="min-width: 200px; z-index: 1000;">
                        <template x-for="rate in rates" :key="rate.currency_id">
                            <div class="d-flex justify-content-between">
                                <!-- Fixed: Using component method properly -->
                                <span x-text="'100 ' + getCurrencyCode(rate.currency_id)"></span>
                                <span x-text="formatRate(rate.rate, rate.currency_id)"></span>
                            </div>
                        </template>
                    </div>
                </button>
            </li>
            <li class="nav-item mx-2" data-bs-toggle="tooltip" data-bs-placement="top" title="Calculator">
                <!-- Calculator Icon (Bootstrap icon) -->
                <button type="button" class="btn bf-body text-white pt-0" id="calculatorIcon">
                    <i class="bi bi-calculator fs-4"></i>
                </button>

                <!-- Dropdown Calculator -->
                <div class="dropdown-calculator" id="dropdownCalculator">
                    <div class="calculator">
                        <input type="text" id="display" class="form-control mb-3" readonly>
                        <button class="btn btn-light" onclick="clearDisplay()">C</button>
                        <button class="btn btn-light" onclick="backspace()">←</button>
                        <button class="btn btn-light" onclick="appendValue('(')">(</button>
                        <button class="btn btn-light" onclick="appendValue(')')">)</button>
                        <button class="btn btn-light" onclick="appendValue('/')">/</button>
                        <button class="btn btn-light" onclick="appendValue('7')">7</button>
                        <button class="btn btn-light" onclick="appendValue('8')">8</button>
                        <button class="btn btn-light" onclick="appendValue('9')">9</button>
                        <button class="btn btn-light" onclick="appendValue('*')">*</button>
                        <button class="btn btn-light" onclick="appendValue('4')">4</button>
                        <button class="btn btn-light" onclick="appendValue('5')">5</button>
                        <button class="btn btn-light" onclick="appendValue('6')">6</button>
                        <button class="btn btn-light" onclick="appendValue('-')">-</button>
                        <button class="btn btn-light" onclick="appendValue('1')">1</button>
                        <button class="btn btn-light" onclick="appendValue('2')">2</button>
                        <button class="btn btn-light" onclick="appendValue('3')">3</button>
                        <button class="btn btn-light" onclick="appendValue('+')">+</button>
                        <button class="btn btn-light" onclick="appendValue('0')">0</button>
                        <button class="btn btn-light" onclick="appendValue('.')">.</button>
                        <button class="btn btn-primary" onclick="calculateResult()" style="grid-column: span 2">=</button>
                    </div>
                </div>
            </li>

            <li class="nav-item"><span class='badge badge-danger'><?= $version ?></span></li>
            <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? "<li class='nav-item'><span class='badge badge-info'>Demo Mode</span></li>" : ""  ?>

            <li class="nav-item dropdown">
                <a href="#" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <?= strtoupper($current_lang) ?>
                </a>
                <div class="dropdown-menu dropdown-menu-left">
                    <?php foreach ($languages_locale as $language) { ?>
                        <span onclick="set_locale('<?= $language['code'] ?>')" class="dropdown-item has-icon <?= ($language['code'] == $current_lang) ? "text-primary" : "" ?>">
                            <?= strtoupper($language['code']) . " - "  . ucwords($language['language']) ?>
                        </span>
                    <?php } ?>
                </div>
            </li>
            <li class="nav-item dropdown mr-5">
                <a href="#" data-bs-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                    <?= labels('hello', alt: 'Hello') ?> 👋, <?= ucwords($first_name); ?>
                </a>
                <div class="dropdown-menu dropdown-menu-left">
                    <a href="<?= base_url('admin/profile');  ?>" class="dropdown-item has-icon">
                        <i class="far fa-user"></i> <?= labels('profile', 'Profile') ?>
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="<?= base_url('auth/logout') ?>" class="dropdown-item has-icon text-danger">
                        <i class="fas fa-sign-out-alt"></i> <?= labels('logout', 'Logout') ?>
                    </a>
                </div>
            </li>
        </ul>
    </div>
</nav>
<div class="main-sidebar sidebar-style-2">
    <aside id="sidebar-wrapper">
        <div class="sidebar-brand">
            <a href="<?= base_url('/admin/home') ?>"> <img src="<?php echo base_url($logo); ?>" class="sidebar_logo w-max-90 h-max-60px" alt=""></a>
        </div>
        <div class="sidebar-brand sidebar-brand-sm">
            <a href="#"><img src="<?php echo base_url($half_logo); ?>" class="h-50" alt=""></a>
        </div>

        <ul class="sidebar-menu">
            <li class="menu-header"><?= labels('dashboard', 'Dashboard') ?></li>
            <li><a class="nav-link active" href="<?= base_url('admin/home');  ?>"><i class="bi bi-house-door text-warning"></i> <span><?= labels('dashboard', 'Dashboard') ?></span></a></li>
            <li><a class="nav-link active" href="<?= base_url('admin/orders');  ?>"><i class="bi bi-calculator text-danger"></i> <span><?= labels('pos', 'POS') ?></span></a></li>

            <li class="menu-header"><?= labels('business', 'Business') ?></li>
            <li><a href="<?= base_url('admin/suppliers');  ?>" class="nav-link"><i class="bi bi-truck text-warning"></i><span><?= labels('suppliers', 'Suppliers') ?></span></a></li>
            <li><a href="<?= base_url('admin/currency');  ?>" class="nav-link"><i class="bi bi-currency-dollar text-success"></i><span><?= labels('currencies', 'Currencies') ?></span></a></li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-bag text-success"></i></i><span><?= labels('purchases', 'Purchases') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link " href="<?= base_url('admin/purchases') ?>"> <?= labels('purchases', 'Purchases') ?></a></li>
                    <li><a class="nav-link " href="<?= base_url('admin/purchases/purchase_return') ?>"> <?= labels('purchase_return', 'Purchase Return') ?></a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-cart3 text-info"></i><span><?= labels('orders', 'Orders') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url('admin/orders/orders'); ?>"><?= labels('orders', 'Orders') ?></a></li>

                    <li><a class="nav-link" href="<?= base_url('admin/customers_subscription'); ?>"><?= labels('subscriptions', 'Subscriptions') ?></a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-person-plus-fill text-primary"></i><span><?= labels('employees', 'Employees') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url('admin/employees/'); ?>"><?= labels('employees', 'Employees') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/positions/'); ?>"><?= labels('positions', 'Positions') ?></a></li>
                </ul>
            </li>
            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-border-bottom text-info"></i><span><?= labels('products', 'Products') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link" href="<?= base_url('admin/categories'); ?>"><?= labels('categories', 'Categories') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/brands'); ?>"><?= labels('brands', 'Brands') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/units'); ?>"><?= labels('units', 'Units') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/products'); ?>"><?= labels('products', 'Products') ?></a></li>
                    <li><a class="nav-link" href="<?= base_url('admin/generate_barcode'); ?>"><?= labels('generate_barcode', 'Generate Barcode') ?></a></li>

                </ul>
            </li>
            <li><a href="<?= base_url('admin/products/manage_stock');  ?>" class="nav-link"><i class="bi bi-box text-warning"></i><span><?= labels('manage_stock', 'Manage Stock') ?></span></a></li>
            <li><a href="<?= base_url('admin/services');  ?>" class="nav-link"><i class="bi bi-gear text-primary"></i><span><?= labels('services', 'Services') ?></span></a></li>
            <li><a href="<?= base_url('admin/customers');  ?>" class="nav-link"><i class="bi bi-people-fill text-primary"></i><span><?= labels('customers', 'Customers') ?></span></a></li>
            <li><a href="<?= base_url('admin/transactions');  ?>" class="nav-link"><i class="bi bi-currency-exchange text-success"></i></i><span><?= labels('transactions', 'Transactions') ?></span></a></li>

            <?php if (! is_team_member($_SESSION['user_id'])) { ?>
                <li><a href="<?= base_url('admin/delivery_boys');  ?>" class="nav-link"><i class="bi bi-person-rolodex text-danger"></i><span><?= labels('delivery_boys', 'Delivery Boys') ?></span></a></li>
            <?php } ?>


            <li class="dropdown">
                <a href="#" class="nav-link has-dropdown"><i class="bi bi-wallet2 text-warning"></i></i><span><?= labels('expenses', 'Expenses') ?></span></a>
                <ul class="dropdown-menu">
                    <li><a class="nav-link " href="<?= base_url('admin/expenses') ?>"> <?= labels('expenses', 'Expenses ') ?></a></li>
                    <li><a class="nav-link " href="<?= base_url('/admin/expenses_type') ?>"> <?= labels('expenses_type', 'Expenses Type ') ?></a></li>
                </ul>
            </li>

            <?php if (! is_team_member($_SESSION['user_id'])) { ?>
                <li class="dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="bi bi-clipboard-data text-danger"></i></i><span><?= labels('reports', 'Reports') ?></span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link " href="<?= base_url('admin/payment_reports') ?>"> <?= labels('payments_reports', 'Payment Reports ') ?></a></li>
                        <li><a class="nav-link " href="<?= base_url('admin/sales_summary') ?>"> <?= labels('sales_summary', 'Sales Summary ') ?></a></li>
                        <li><a class="nav-link " href="<?= base_url('admin/profit_loss') ?>"> <?= labels('profit_&_loss', 'Profit & Loss ') ?></a></li>
                        <li><a class="nav-link " href="<?= base_url('admin/top_selling_products') ?>"> <?= labels('top_selling_products', 'Top Selling Products ') ?></a></li>
                        <li><a class="nav-link " href="<?= base_url('admin/best_customers') ?>"> <?= labels('best_customers', 'Best Customers') ?></a></li>
                        <li><a class="nav-link " href="<?= base_url('admin/purchases_report') ?>"> <?= labels('purchases_report', 'Purchases Report') ?></a></li>
                    </ul>
                </li>
            <?php } ?>


            <?php if (! is_team_member($_SESSION['user_id'])) { ?>

                <li class="menu-header"><?= labels('admin', 'Admin') ?></li>

                <li class="nav-item">
                    <a href="<?= base_url('admin/team_members');  ?>" class="nav-link"><i class="bi bi-people-fill text-danger"></i></i><span><?= labels('Team_members', 'Team members') ?></span></a>
                </li>
                <li><a class="nav-link" href="<?= base_url('admin/businesses'); ?>"><i class="bi bi-gift-fill text-info"></i> <span><?= labels('business', 'Business') ?></span></a></li>
                <li><a class="nav-link" href="<?= base_url('admin/warehouse'); ?>"><i class="bi bi-shop-window text-info"></i> <span><?= labels('warehouse', 'Warehouse') ?></span></a></li>

                <li class="nav-item dropdown">
                    <a href="#" class="nav-link has-dropdown"><i class="bi bi-gear-fill text-primary"></i><span><?= labels('settings', 'Settings') ?></span></a>
                    <ul class="dropdown-menu">
                        <li><a class="nav-link" href="<?= base_url('admin/settings/general'); ?>"><?= labels('general', 'General') ?></a></li>
                        <!-- <li><a class="nav-link" href="<?= base_url('admin/settings/payment_gateway'); ?>"><?= labels('payment_gateway', 'Payment Gateway') ?></a></li> -->
                        <li><a class="nav-link" href="<?= base_url('admin/settings/email'); ?>"><?= labels('smtp_email', 'SMTP (EMAIL)') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/languages') ?>"> <?= labels('languages', "Languages") ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/database'); ?>"><?= labels('database_backup', 'Database Backup') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/settings/about_us'); ?>"><?= labels('about_us', 'About Us') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/settings/privacy_policy'); ?>"><?= labels('privacy_policy', 'Privacy Policy') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/settings/terms_and_conditions'); ?>"><?= labels('terms_and_conditions', 'Terms & Conditions') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/settings/refund_policy'); ?>"><?= labels('refund_policy', 'Refund Policy') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/tax'); ?>"><?= labels('tax', 'Tax') ?></a></li>
                        <li><a class="nav-link" href="<?= base_url('admin/updater'); ?>"><?= labels('system_updater', 'System Updater') ?></a></li>
                    </ul>
                </li>

            <?php } ?>
        </ul>
    </aside>
</div>

<?= view('common_partials/js/number_utils/text_input_formatter'); ?>
<?= view('common_partials/js/number_utils/unmask'); ?>


<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('headerData', () => ({
            // Initialize with empty rates data
            rates: [],
            currencies: [],
            baseCurrency: null,
            buttonText: 'Exchange Rate',

            // Fetch exchange rates and currencies on Alpine init
            init() {
                this.fetchExchangeRates();
            },
            async fetchExchangeRates() {
                try {
                    const response = await axios.get('<?= base_url('admin/currency/get_exchange_rates') ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    });

                    this.currencies = response.data.currencies || [];
                    this.rates = response.data.rates || [];
                    this.baseCurrency = this.currencies.find(c => parseInt(c.is_base) === 1);

                    // Update button display
                    this.updateButtonText();
                } catch (error) {
                    showToastMessage('Error fetching exchange rates', 'error');
                    console.error('Error fetching exchange rates:', error);
                }
            },
            // Helper methods
            getCurrency(currencyId) {
                return this.currencies.find(c => c.id == currencyId);
            },

            formatRate(rate, currencyId) {
                const currency = this.getCurrency(currencyId);
                const decimals = currency ? currency.decimal_places : 2;
                return parseFloat(rate).toLocaleString(undefined, {
                    minimumFractionDigits: decimals,
                    maximumFractionDigits: decimals
                });
            },
            getCurrencyCode(currencyId) {
                const currency = this.currencies.find(c => c.id == currencyId);
                return currency ? currency.code : '';
            },
            async openExchangeRateModal() {
                try {
                    // Show loading indicator
                    Swal.fire({
                        title: 'Loading exchange rates',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    // Fetch current rates
                    const response = await axios.get('<?= base_url('admin/currency/get_exchange_rates') ?>', {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                        }
                    });

                    this.currencies = response.data.currencies || [];
                    this.rates = response.data.rates || [];
                    this.baseCurrency = this.currencies.find(c => parseInt(c.is_base) === 1);

                    // Update button display
                    this.updateButtonText();

                    // Close loading and show modal
                    Swal.close();
                    this.showExchangeRateModal();
                } catch (error) {
                    console.error('Error fetching exchange rates:', error);
                    Swal.fire('Error', 'Failed to load exchange rates', 'error');
                }
            },

            updateButtonText() {
                if (this.rates.length > 0 && this.baseCurrency) {
                    const mainRate = this.rates[0];
                    const currency = this.getCurrency(mainRate.currency_id);
                    this.buttonText = currency ?
                        `100${currency.symbol} : ${this.formatRate(mainRate.rate, mainRate.currency_id)}` :
                        'Exchange Rates';
                } else {
                    this.buttonText = 'Exchange Rates';
                }
            },

            showExchangeRateModal() {
                if (!this.baseCurrency) {
                    Swal.fire('Error', 'Base currency not found', 'error');
                    return;
                }

                // Build form HTML
                let formHtml = `
            <form id="exchangeRateForm">
                <input type="hidden" name="<?= csrf_token() ?>" value="<?= csrf_hash() ?>">
                <div class="mb-3">
                    <label class="form-label">Base Currency</label>
                    <input type="text" class="form-control" 
                           value="${this.baseCurrency.name} (${this.baseCurrency.code})" readonly>
                </div>
            `;

                // Add currency fields with proper decimal places
                this.currencies.filter(c => !parseInt(c.is_base)).forEach(currency => {
                    const rate = this.rates.find(r => r.currency_id == currency.id);
                    const rateValue = rate ? this.formatRate(rate.rate, currency.id) : '';
                    const step = (0.1 ** currency.decimal_places).toFixed(currency.decimal_places);

                    formHtml += `
        <div class="mb-3">
            <label for="rate_${currency.id}" class="form-label">
                ${currency.name} (${currency.code})
            </label>
            <input type="text" 
                x-numberformat="{ decimals: ${currency.decimal_places}, allowDecimal: true }"
                id="rate_${currency.id}" 
                name="rates[${currency.id}]" 
                class="form-control" 
                step="${step}" 
                min="0" 
                value="${rateValue}"
                placeholder="Enter rate"
                required>
            <small class="text-muted">100 ${currency.code} = ${rateValue || '?'} ${this.baseCurrency.code}</small>
        </div>
    `;
                });

                formHtml += `</form>`; // ✅ Close the form AFTER loop

                // ✅ Now show modal once
                Swal.fire({
                    title: 'Update Exchange Rates',
                    html: formHtml,
                    width: '600px',
                    showCancelButton: true,
                    confirmButtonText: 'Save Rates',
                    focusConfirm: false,
                    preConfirm: () => {
                        const formData = new FormData(document.getElementById('exchangeRateForm'));
                        const rates = [];
                        const currentRates = {};

                        // Create map of current rates
                        this.rates.forEach(rate => {
                            currentRates[rate.currency_id] = rate.rate;
                        });

                        // Collect changed rates
                        for (let [key, value] of formData.entries()) {
                            if (key.startsWith('rates[')) {
                                const currencyId = key.match(/\[(\d+)\]/)[1];
                                const currency = this.getCurrency(currencyId);
                                const decimalPlaces = currency ? currency.decimal_places : 2;
                                const precision = 10 ** decimalPlaces;

                                const newRate = parseFloat(this.$number.unmask(value));
                                const oldRate = currentRates[currencyId] || 0;

                                // Compare with proper decimal precision
                                if (Math.round(newRate * precision) !== Math.round(oldRate * precision)) {
                                    rates.push({
                                        currency_id: currencyId,
                                        rate: newRate
                                    });
                                }
                            }
                        }

                        return {
                            base_currency_id: this.baseCurrency.id,
                            rates: rates,
                            effective_date: new Date().toISOString()
                        };
                    }
                }).then(async (result) => {
                    if (result.isConfirmed) {
                        try {
                            const response = await axios.post(
                                '<?= base_url('admin/currency/save_exchange_rates') ?>',
                                result.value, {
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': '<?= csrf_hash() ?>'
                                    }
                                }
                            );

                            Swal.fire('Success', 'Exchange rates updated successfully', 'success');
                            this.openExchangeRateModal(); // Refresh data
                        } catch (error) {
                            console.error('Error saving rates:', error);
                            let errorMsg = error.response?.data?.message || 'Failed to update rates';
                            Swal.fire('Error', errorMsg, 'error');
                        }
                    } // if (result.isConfirmed)
                }); // close Swal.fire
            } // showExchangeRateModal method

        })); // close Alpine.data
    }); // close document.addEventListener
</script>