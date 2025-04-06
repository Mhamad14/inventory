<div class="navbar-bg" id="navebar-bg"></div>
<nav class="navbar navbar-expand-lg main-navbar">
    <form class="form-inline mr-auto">
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

        <ul class="navbar-nav navbar-right">
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

    </form>
    <?php
    $first_name = $user->first_name; ?>
    <ul class="navbar-nav navbar-right">
        <div class="mx-2 " data-bs-toggle="tooltip" data-bs-placement="top" title="Calculator">
            <!-- Calculator Icon (Bootstrap icon) -->
            <button type="button" class="calculator-icon btn bg-body pt-0" id="calculatorIcon">
                <i class="fas fa-calculator"></i> <!-- Using FontAwesome for calculator icon -->
            </button>

            <!-- Dropdown Calculator -->
            <div class="dropdown-calculator" id="dropdownCalculator">
                <div class="calculator">
                    <input type="text" id="display" class="form-control mb-3" readonly>
                    <button class="btn btn-light" onclick="clearDisplay()">C</button>
                    <button class="btn btn-light" onclick="backspace()">←</button> <!-- Backspace button -->
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
        </div>
        <div><span class='badge badge-danger'><?= $version ?></span></div>
        <?= (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) ? "<div><span class='badge badge-info'>Demo Mode</span></div>" : ""  ?>

        <li class="dropdown">
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
        <li class="dropdown mr-5">
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