<?php
helper('form')
?>

<!-- Main Content -->
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1><?= labels("languages", "Languages") ?>
            </h1>
            <div class="section-header-breadcrumb">
                <button class="btn btn-primary btn-rounded no-shadow" data-bs-toggle="modal" data-bs-target="#exampleModal"><?= labels('add_language', "Add Language") ?></button>
            </div>

        </div>
        <div class="section-body">
            <div id="output-status"></div>
            <div class="row">
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header">
                            <h4><?= labels('switch', "Switch") ?></h4>
                        </div>
                        <div class="card-body">
                            <ul class="nav nav-pills flex-column" id="myTab4" role="tablist">
                                <?php

                                foreach ($languages as $lang) {
                                    if ($lang['code'] == $code) { ?>
                                        <li class="nav-item">
                                            <a class="nav-link active" href='<?= base_url("admin/languages/change/" . $lang['code']); ?>'><?= strtoupper($lang['code']) . " - " . ucfirst($lang['language']) ?></a>
                                        </li>
                                    <?php } else { ?>
                                        <li class="nav-item">
                                            <a class="nav-link" href='<?= base_url("admin/languages/change/" . $lang['code']); ?>'><?= strtoupper($lang['code']) . " - " . ucfirst($lang['language']) ?></a>
                                        </li>
                                <?php }
                                } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="tab-content no-padding" id="myTab2Content">
                        <div class="tab-pane fade show active" id="languages-settings" role="tabpanel" aria-labelledby="languages-tab4">
                            <?= form_open(base_url('admin/languages/set_labels'), [], ['code' => $code]) ?>

                            <div class="card" id="languages-settings-card">
                                <div class="card-header">
                                    <h4>Labels</h4>
                                </div>
                                <div class="card-body">
                                    <div class="row">

                                        <!-- labels -->
                                        <?= create_label('order_details', "Order Details") ?>
                                        <?= create_label('employees', "Employees") ?>
                                        <?= create_label('positions', "Positions") ?>
                                        <?= create_label('brands', "Brands") ?>
                                        <?= create_label('admin', "Admin") ?>
                                        <?= create_label('Team_members', 'Team members') ?>
                                        <?= create_label('warehouse', 'Warehouse') ?>
                                        <?= create_label('add_payment', "Add Payment") ?>
                                        <?= create_label('no_payments_found', "No payments found") ?>
                                        <?= create_label('payment_date', "Payment Date") ?>
                                        <?= create_label('create_payment', "Create Payment") ?>
                                        <?= create_label('payment_summary', "Payment Summary") ?>
                                        <?= create_label('order_total', "Order Total") ?>
                                        <?= create_label('payment_details', "Payment Details") ?>
                                        <?= create_label('no', "No") ?>
                                        <?= create_label('yes', "Yes") ?>
                                        <?= create_label('order_status', "Order Status") ?>
                                        <?= create_label('bulk_update', "Bulk Update") ?>
                                        <?= create_label('bulk_update_label', "Select status and square box of item which you want to update") ?>
                                        <?= create_label('customer_details', "Customer Details") ?>
                                        <?= create_label('debit_wallet_balance', "Debit wallet balance") ?>
                                        <?= create_label('credit_wallet_balance', "Credit wallet balance") ?>
                                        <?= create_label('do_nothing', "Do nothing") ?>
                                        <?= create_label('what_to_do_with_wallet_balance', "what to do with wallet balance?") ?>
                                        <?= create_label('operation', "Operation") ?>
                                        <?= create_label('status_name', "Status Name") ?>
                                        <?= create_label('create_status', "Create Status") ?>
                                        <?= create_label('register_user', "Register User") ?>
                                        <?= create_label('amount_paid', "Amount Paid") ?>
                                        <?= create_label('order_type', "Order Type") ?>
                                        <?= create_label('businesses', "Businesses") ?>
                                        <?= create_label('old_password', "Old Password") ?>
                                        <?= create_label('new_password', "new Password") ?>
                                        <?= create_label('my_profile', 'My Profile') ?>
                                        <?= create_label('reurring_price', 'Recurring Price') ?>
                                        <?= create_label('is_recursive', 'is recursive?') ?>
                                        <?= create_label('cost_price', 'Cost Price') ?>
                                        <?= create_label('add_service', 'Add Service') ?>
                                        <?= create_label('tax_name', 'tax name') ?>
                                        <?= create_label('vendor_id', 'Vendor id') ?>
                                        <?= create_label('category_name', 'Category name') ?>
                                        <?= create_label('unit_name', 'Unit Name') ?>
                                        <?= create_label('select_unit', 'Select Unit') ?>
                                        <?= create_label('purchase_price', 'Purchase Price') ?>
                                        <?= create_label('sale_price', 'Sale Price') ?>
                                        <?= create_label('variant_name', 'Variant Name') ?>
                                        <?= create_label('add_variant', 'Add Variant') ?>
                                        <?= create_label('product_details', 'Product details') ?>
                                        <?= create_label('unit', 'Unit') ?>
                                        <?= create_label('product_level', 'Product Level') ?>
                                        <?= create_label('variant_level', 'Variant Level') ?>
                                        <?= create_label('select_level', 'Select Level') ?>
                                        <?= create_label('type', 'Type') ?>
                                        <?= create_label('stock_management', 'Stock Management') ?>
                                        <?= create_label('stock', 'Stock') ?>
                                        <?= create_label('simple_product', 'Simple Product') ?>
                                        <?= create_label('variable_product', 'Variable Product') ?>
                                        <?= create_label('product_type', 'Product Type') ?>
                                        <?= create_label('image', 'Image') ?>
                                        <?= create_label('is_tax_included', 'Is tax included?') ?>
                                        <?= create_label('none', 'None') ?>
                                        <?= create_label('category', 'Category') ?>
                                        <?= create_label('product_name', 'Product Name') ?>
                                        <?= create_label('add_product', 'Add Product') ?>
                                        <?= create_label('default_business', 'Default Business') ?>
                                        <?= create_label('subscription_end_date', 'Subscription End Date') ?>
                                        <?= create_label('subscription_start_date', 'Subscription Start Date') ?>
                                        <?= create_label('closing_balance', 'Closing Balance') ?>
                                        <?= create_label('opening_balance', 'Opening Balance') ?>
                                        <?= create_label('order_id', 'Order Id') ?>
                                        <?= create_label('select', 'Select') ?>
                                        <?= create_label('filter_transaction', 'Filter Transaction') ?>
                                        <?= create_label('customer_wallet_transaction', 'Customer Wallet Transaction') ?>
                                        <?= create_label('customers_details', 'Customers Details') ?>
                                        <?= create_label('add', 'Add') ?>
                                        <?= create_label('you', 'You') ?>
                                        <?= create_label('created_by', 'Created by') ?>
                                        <?= create_label('credit', 'Credit') ?>
                                        <?= create_label('debit', 'Debit') ?>
                                        <?= create_label('transaction_type', 'Transaction Type') ?>
                                        <?= create_label('payment_mode', 'Payment Mode') ?>
                                        <?= create_label('create_wallet_payment', 'Create Wallet Payment') ?>
                                        <?= create_label('close', 'Close') ?>
                                        <?= create_label('to', 'To') ?>
                                        <?= create_label('from', 'From') ?>
                                        <?= create_label('recurring_days', 'Recurring Days') ?>
                                        <?= create_label('is_renewable', 'Is Renewable ?') ?>
                                        <?= create_label('customers_count', 'Customers Count') ?>
                                        <?= create_label('service_name', 'Service Name') ?>
                                        <?= create_label('service_id', 'Service ID') ?>
                                        <?= create_label('total_subscription', 'Total Subscription') ?>
                                        <?= create_label('all_subscriptions', 'All Subscriptions') ?>
                                        <?= create_label('add_to_cart', 'Add to Cart') ?>
                                        <?= create_label('ends_on', 'Ends On') ?>
                                        <?= create_label('starts_from', 'Starts From') ?>
                                        <?= create_label('create_order', 'Create Order') ?>
                                        <?= create_label('clear_cart', 'Clear Cart') ?>
                                        <?= create_label('add_status', 'Add Status') ?>
                                        <?= create_label('enter_transaction_id', 'Enter Transaction ID') ?>
                                        <?= create_label('other', 'Other') ?>
                                        <?= create_label('enter_payment_method_name', 'Enter Payment method Name') ?>
                                        <?= create_label('online_payment', 'Online Payment') ?>
                                        <?= create_label('net_banking', 'Net Banking') ?>
                                        <?= create_label('Bar_code_qR_code_scan', 'Bar Code / QR Code Scan') ?>
                                        <?= create_label('card_payment', 'Card Payment') ?>
                                        <?= create_label('wallet_balance', 'wallet balance') ?>
                                        <?= create_label('wallet', 'Wallet') ?>
                                        <?= create_label('cash', 'Cash') ?>
                                        <?= create_label('cancelled', 'Cancelled') ?>
                                        <?= create_label('unpaid', 'Unpaid') ?>
                                        <?= create_label('partially_paid', 'Partially Paid') ?>
                                        <?= create_label('fully_paid', 'Fully Paid') ?>
                                        <?= create_label('if_any', 'if any') ?>
                                        <?= create_label('shipping_charge', 'Shipping charge') ?>
                                        <?= create_label('subtotal', 'Subtotal') ?>
                                        <?= create_label('item', 'Item') ?>
                                        <?= create_label('quantity', 'Quantity') ?>
                                        <?= create_label('current_orders', 'Current Orders') ?>
                                        <?= create_label('dont_have_account', 'Dont Have An Account? Register Here') ?>
                                        <?= create_label('already_registered', 'Already Registered') ?>
                                        <?= create_label('delivery_boy', 'Delivery Boy') ?>
                                        <?= create_label('total', 'Total') ?>
                                        <?= create_label('final_total', 'Final Total') ?>
                                        <?= create_label('discount', 'Discount') ?>
                                        <?= create_label('delivery_charges', 'Delivery Charges') ?>
                                        <?= create_label('customer_name', 'Customer Name') ?>
                                        <?= create_label('order_date', 'Order Date') ?>
                                        <?= create_label('permissions', 'Permissions') ?>
                                        <?= create_label('assigned_businesses', 'Assigned Businesses') ?>
                                        <?= create_label('check_business', 'Check Business for Delivery Boy') ?>
                                        <?= create_label('permission_for_order', 'Do you want to allow delivery boy to create orders?') ?>
                                        <?= create_label('permission_for_transaction', 'Do you want to allow delivery boy to create transactions?') ?>
                                        <?= create_label('permission_for_customer', 'Do you want to allow delivery boy to create customers?') ?>
                                        <?= create_label('password_delivery_boy_text', 'Enter new password if you want to update current password') ?>
                                        <?= create_label('identity', 'Identity') ?>
                                        <?= create_label('register_delivery_boy_here', 'Register Delivery Boy Here!') ?>
                                        <?= create_label('balance', 'Balance') ?>
                                        <?= create_label('balance-', 'Balance-') ?>
                                        <?= create_label('total_order_payment', 'Total Order Payment') ?>
                                        <?= create_label('total_orders', 'Total Orders') ?>
                                        <?= create_label('delivery_boys', 'Delivery Boys') ?>
                                        <?= create_label('profile', 'Profile') ?>
                                        <?= create_label('save', 'Save') ?>
                                        <?= create_label('package_statistics', 'Package Statics') ?>
                                        <?= create_label('package_name', 'Package Name') ?>
                                        <?= create_label('total_packages', 'Total Packages') ?>
                                        <?= create_label('sold_packages_statistics', 'Sold Packages Statistics') ?>
                                        <?= create_label('sold_packages', 'Sold Packages') ?>
                                        <?= create_label('vendor_statistics', 'Vendor Statistics') ?>
                                        <?= create_label('total_vendors', 'Total Vendors') ?>
                                        <?= create_label('earning_statistics', 'Earings Statistics') ?>
                                        <?= create_label('total_package_payment', 'Total Package Payment') ?>
                                        <?= create_label('remaining_payments_amount', 'Remaining Payment Amount') ?>
                                        <?= create_label('amount', 'Amount') ?>
                                        <?= create_label('products_sales', 'Product Sales') ?>
                                        <?= create_label('business_insights', 'Business Insights') ?>
                                        <?= create_label('add_unit', 'Add Unit') ?>
                                        <?= create_label('units', 'Units') ?>
                                        <?= create_label('name', 'Name') ?>
                                        <?= create_label('symbol', 'Symbol') ?>
                                        <?= create_label('parent_unit', 'Parent Unit') ?>
                                        <?= create_label('conversion', 'Conversion') ?>
                                        <?= create_label('submit', 'Submit') ?>
                                        <?= create_label('id', 'ID') ?>
                                        <?= create_label('add_categories', 'Add Categories') ?>
                                        <?= create_label('parent_id', 'Parent ID') ?>
                                        <?= create_label('categories', 'Categories') ?>
                                        <?= create_label('add_tax', 'Add Tax') ?>
                                        <?= create_label('percentage', 'Percentage') ?>
                                        <?= create_label('tax', 'Tax') ?>
                                        <?= create_label('packages', 'Packages') ?>
                                        <?= create_label('active_packages', 'Active packages') ?>
                                        <?= create_label('create_package', 'Create Package') ?>
                                        <?= create_label('title', 'Title') ?>
                                        <?= create_label('no_of_businesses', 'No. of businesses') ?>
                                        <?= create_label('No_of_delivery_boys', 'No. of delivery boys') ?>
                                        <?= create_label('No_of_products', 'No. of products') ?>
                                        <?= create_label('No_of_customers', 'No. of customers') ?>
                                        <?= create_label('description', 'Description') ?>
                                        <?= create_label('status', 'Status') ?>
                                        <?= create_label('tenure_details', 'Tenure details') ?>
                                        <?= create_label('tenure', 'Tenure') ?>
                                        <?= create_label('remaining_days', 'Remaining Days') ?>
                                        <?= create_label('months', 'Month(s)') ?>
                                        <?= create_label('price', 'Price') ?>
                                        <?= create_label('discounted_price', 'Discounted Price(₹)') ?>
                                        <?= create_label('action', 'Action') ?>
                                        <?= create_label('language', 'Language') ?>
                                        <?= create_label('subscriptions', 'Subscriptions') ?>
                                        <?= create_label('add_subscription', 'Add subscription') ?>
                                        <?= create_label('select_user', 'Select User') ?>
                                        <?= create_label('users_full_name', 'Users full name ') ?>
                                        <?= create_label('select_package', 'Select Package') ?>
                                        <?= create_label('select_packag_enure', 'Select Packag Tenure') ?>
                                        <?= create_label('subscription_start_from', 'Subscription Start From') ?>
                                        <?= create_label('subscription_end_at', 'Subscription End At') ?>
                                        <?= create_label('filter_by_subscription_type', 'Filter by subscription Type') ?>
                                        <?= create_label('vendors', 'Vendors') ?>
                                        <?= create_label('create_vendor', 'Create Vendor') ?>
                                        <?= create_label('first_name', 'First Name') ?>
                                        <?= create_label('last_name', 'Last Name') ?>
                                        <?= create_label('mobile_number', 'Mobile') ?>
                                        <?= create_label('password', 'Password') ?>
                                        <?= create_label('password_confirmation', 'Password Confirmation') ?>
                                        <?= create_label('register', 'Register') ?>
                                        <?= create_label('transactions', 'Transactions') ?>
                                        <?= create_label('transaction_id', 'Transaction ID') ?>

                                        <!-- from here start tomorrow -->
                                        <?= create_label('vendors_transaction', 'Vendors Transaction') ?>
                                        <?= create_label('payment_method', 'Payment Method') ?>
                                        <?= create_label('transaction_date', 'Transaction Date') ?>
                                        <?= create_label('filter_by_status', 'Filter by status') ?>
                                        <?= create_label('subscription', 'Subscription') ?>
                                        <?= create_label('buy_renew_plan', 'Buy/Renew Plan') ?>
                                        <?= create_label('business', 'Business') ?>
                                        <?= create_label('add_business', 'Add Business') ?>
                                        <?= create_label('business_name', 'Business Name') ?>
                                        <?= create_label('icon', 'Icon') ?>
                                        <?= create_label('email', 'Email') ?>
                                        <?= create_label('contact', 'Contact') ?>
                                        <?= create_label('website', 'Website') ?>
                                        <?= create_label('tax_value', 'Tax Value') ?>
                                        <?= create_label('bank_details', 'Bank Details') ?>
                                        <?= create_label('business_access_statistics', 'Business Access Statistics') ?>
                                        <?= create_label('businss_statistics', 'Business Statistics') ?>
                                        <?= create_label('delivered_order_statistics', 'Delivered Order Statistics') ?>
                                        <?= create_label('customers_statistics', 'Customers Statistics') ?>
                                        <?= create_label('delivery_boys_statistics', 'Delivery Boys Statistics') ?>
                                        <?= create_label('order_statistics', 'Order Statistics') ?>
                                        <?= create_label('products_statistics', 'Products Statistics') ?>
                                        <?= create_label('customers', 'Customers') ?>
                                        <?= create_label('customer_id', 'Customer ID') ?>
                                        <?= create_label('register_customer_here', 'Register Customer Here') ?>
                                        <?= create_label('customers_subscription', 'Customers Subscription') ?>
                                        <?= create_label('created_on', 'Created On') ?>
                                        <?= create_label('orders', 'Orders') ?>

                                        <?= create_label('filter_orders', 'Filter Orders') ?>
                                        <?= create_label('filter_by_payment_status', 'Filter by Payment Status') ?>
                                        <?= create_label('products', 'Products') ?>
                                        <?= create_label('services', 'Services') ?>
                                        <?= create_label('all_products', 'All Products') ?>
                                        <?= create_label('all_services', 'All Services') ?>

                                        <?= create_label('all_categories', 'All Categories') ?>
                                        <?= create_label('primary_color', 'Primary Color') ?>
                                        <?= create_label('secondary_color', 'Secondary Color') ?>
                                        <?= create_label('primary_shadow_color', 'Primary Shadow Color') ?>
                                        <?= create_label('address', 'Address') ?>
                                        <?= create_label('reset', 'Reset') ?>
                                        <?= create_label('short_description', 'Short Description') ?>

                                        <?= create_label('copyright', 'Copyright') ?>
                                        <?= create_label('support_hours', 'Support hours') ?>
                                        <?= create_label('settings', 'Settings') ?>
                                        <?= create_label('general', 'General') ?>
                                        <?= create_label('company_title', 'Company Title') ?>
                                        <?= create_label('support_email', 'Support Email') ?>
                                        <?= create_label('logo', 'Logo') ?>
                                        <?= create_label('favicon', 'Favicon') ?>
                                        <?= create_label('half_logo', 'Half Logo') ?>
                                        <?= create_label('currency_symbol', 'Currency Symbol') ?>
                                        <?= create_label('select_time_zone', 'Select Time Zone') ?>
                                        <?= create_label('phone', 'Phone') ?>
                                        <?= create_label('update', 'Update') ?>
                                        <?= create_label('copyright_details', 'Copyright Details') ?>
                                        <?= create_label('general_settings', 'General Settings') ?>
                                        <?= create_label('smtp_email', 'SMTP (EMAIL)') ?>

                                        <?= create_label('languages', 'Languages') ?>
                                        <?= create_label('clear', 'Clear') ?>
                                        <?= create_label('email_description', 'Email SMTP settings, notifications and others related to email.') ?>
                                        <?= create_label('password_email', 'Password of above given email.') ?>

                                        <?= create_label('email_settings', 'Email Settings') ?>
                                        <?= create_label('mail_protocol', 'Mail Protocol') ?>
                                        <?= create_label('mail_host', 'SMTP Host') ?>
                                        <?= create_label('smtp_username', 'SMTP Username') ?>
                                        <?= create_label('smtp_password', 'SMTP Password') ?>
                                        <?= create_label('smtp_port', 'SMTP Port Number') ?>
                                        <?= create_label('smtp_encryption', 'SMTP Encryption') ?>
                                        <?= create_label('choose_mail_type', 'Choose Mail Type') ?>
                                        <?= create_label('email_content_type', 'Email Content Type') ?>
                                        <?= create_label('update_email_Setting', 'Update Email Settings') ?>

                                        <?= create_label('leave_blank', 'Leave it blank to disable it') ?>
                                        <?= create_label('app_heading', 'App Heading') ?>
                                        <?= create_label('app_sub_heading', 'App Sub Heading') ?>
                                        <?= create_label('android_link', 'Android Link') ?>
                                        <?= create_label('ios_link', 'IOS Link') ?>
                                        <?= create_label('app_settings', 'App settings') ?>
                                        <?= create_label('about_us', 'About Us') ?>
                                        <?= create_label('terms_and_conditions', 'Terms and Conditions') ?>
                                        <?= create_label('privacy_policy', 'Privacy Policy') ?>
                                        <?= create_label('refund_policy', 'Refund Policy') ?>
                                        <?= create_label('payment_gateway', 'Payment Gateway') ?>
                                        <?= create_label('tts', 'TTS') ?>
                                        <?= create_label('configurations', 'Configurations') ?>
                                        <?= create_label('system_updater', 'System Updater') ?>
                                        <?= create_label('dashboard', 'Dashboard') ?>
                                        <?= create_label('no_active', 'No active subscription found') ?>
                                        <?= create_label('purchase_date', 'Purchase Date') ?>
                                        <?= create_label('select_payment_type', 'Select Payment type') ?>
                                        <?= create_label('buy_now', 'Buy Now') ?>
                                        <?= create_label('checkout', 'checkout') ?>
                                        <?= create_label('subscribe', 'Subscribe') ?>
                                        <?= create_label('active_plan', 'Active Package') ?>
                                        <?= create_label('logout', 'Logout') ?>
                                        <?= create_label('hello', 'Hello') ?>
                                        <?= create_label('started_from', 'Started From') ?>
                                        <?= create_label('expires_on', 'Expires On') ?>
                                        <?= create_label('add_language', 'Add Language') ?>
                                        <?= create_label('switch', 'Switch') ?>
                                        <?= create_label('character_based', 'Character Based') ?>
                                        <?= create_label('service_provider_based', 'Service Provider Based') ?>
                                        <?= create_label('featured', 'Featured') ?>
                                        <?= create_label('featured_text', 'Featured Text') ?>
                                        <?= create_label('characters_max', 'Characters Max') ?>
                                        <?= create_label('edit', 'Edit') ?>
                                        <?= create_label('delete', 'Delete') ?>
                                        <?= create_label('filter_date_by', 'Filter Date by') ?>
                                        <?= create_label('all', 'all') ?>
                                        <?= create_label('date_range_filter', 'Date Range') ?>
                                        <?= create_label('active', 'Active') ?>
                                        <?= create_label('expired', 'Expired') ?>
                                        <?= create_label('apply_filters', 'Apply filters') ?>
                                        <?= create_label('apply', 'Apply') ?>
                                        <?= create_label('success', 'Success') ?>
                                        <?= create_label('failed', 'Failed') ?>
                                        <?= create_label('pending', 'Pending') ?>
                                        <?= create_label('message', 'Message') ?>
                                        <?= create_label('plan_order', 'Plan Order') ?>
                                        <?= create_label('manage_stock', 'Manage Stock') ?>
                                        <?= create_label('purchases', 'Purchases') ?>
                                        <?= create_label('suppliers', 'Suppliers') ?>
                                        <?= create_label('expenses', 'Expenses') ?>
                                        <?= create_label('expenses_type', 'Expenses Type') ?>
                                        <?= create_label('reports', 'Reports') ?>
                                        <?= create_label('stock_statistics', 'Stock Statistics') ?>
                                        <?= create_label('total_products', 'Total Products') ?>
                                        <?= create_label('low_in_stock', 'Low in Stock') ?>
                                        <?= create_label('out_of_stock', 'Out of Stock') ?>
                                        <?= create_label('quantity_alert', 'Quantity Alert') ?>
                                        <?= create_label('sales', 'Sales') ?>
                                        <?= create_label('profit', 'Profit') ?>
                                        <?= create_label('loss', 'Loss') ?>
                                        <?= create_label('payment', 'Payment') ?>
                                        <?= create_label('summary', 'Summary') ?>
                                        <?= create_label('payments_reports', 'Payments Reports') ?>
                                        <?= create_label('sales_summary', 'Sales Summary') ?>
                                        <?= create_label('profit_&_loss', 'Profit & Loss') ?>
                                        <?= create_label('return', 'Return') ?>
                                        <?= create_label('pos_point_of_sale', 'POS - Point Of Sale') ?>
                                        <?= create_label('pos', 'POS') ?>
                                        <?= create_label('total_sales', 'Total Sales') ?>
                                        <?= create_label('purchases_sales', 'Purchases vs Sales') ?>
                                        <?= create_label('top_selling_products', 'Top Selling Products') ?>
                                        <?= create_label('best_customers', 'Best Customers') ?>
                                        <?= create_label('purchases_report', 'Purchases Report') ?>
                                        <?= create_label('purchase_return', 'Purchase Return') ?>
                                        <?= create_label('generate_barcode', 'Generate Barcode') ?>
                                        <?= create_label('purchase_return_list', 'Puchases Return List') ?>
                                        <?= create_label('return_date', 'Return Date') ?>
                                        <?= create_label('return_status', 'Return Status') ?>
                                        <?= create_label('current_stock', 'Current Stock') ?>
                                        <?= create_label('expenses_date', 'Expenses Date') ?>
                                        <?= create_label('user_id', 'User ID') ?>
                                        <?= create_label('username', 'Username') ?>
                                        <?= create_label('filter_payments', 'Filter Payments') ?>
                                        <?= create_label('total_amount', 'Total Amount') ?>
                                        <?= create_label('supplier_name', 'Supplier Name') ?>
                                        <?= create_label('product_id', 'Product ID') ?>
                                        <?= create_label('purchase_id', 'Purchase ID') ?>
                                        <?= create_label('edit_product', 'Edit Product') ?>
                                        <?= create_label('create_suppliers', 'Create Suppliers') ?>
                                        <?= create_label('edit_suppliers', 'Edit Suppliers') ?>
                                        <?= create_label('edit_expenses', 'Edit Expenses') ?>
                                        <!-- labels -->
                                    </div>
                                </div>
                                <div class="card-footer bg-whitesmoke text-md-right">
                                    <button class="btn btn-primary" id="languages-save-btn"><?= labels('save', "Save") ?></button>
                                </div>
                            </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <?= form_open(base_url('admin/languages/create'), ['id="modal-add-language-part"', 'class="modal-part"']); ?>
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Add Languages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label>Langugae Name</label>
                    <div class="input-group">
                        <?= form_input(['name' => 'language', 'placeholder' => 'For Ex: English', 'class' => 'form-control']) ?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Langugae Code</label>
                    <div class="input-group">
                        <?= form_input(['name' => 'code', 'placeholder' => 'For Ex: en', 'class' => 'form-control']) ?>
                    </div>
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?= labels('close', 'Close') ?></button>
                <button type="submit" class="btn btn-primary">Save changes</button>
            </div>
        </div>
        </form>
    </div>
</div>