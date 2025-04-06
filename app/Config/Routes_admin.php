<?php

$routes->add('admin/home', 'admin\Home::index');
$routes->post('admin/get_status_list', 'admin\Home::get_status_list');
// $routes->add('admin/home', 'admin\Home::index',['filter' => 'checkpermissions:module=home,action=can_read']);

$routes->get('admin/languages/', "admin\Languages::index", ['filter' => 'checkRoles']);

$routes->post('admin/languages/create', "admin\Languages::create", ['filter' => 'checkRoles']);
$routes->post('admin/languages/set_labels', "admin\Languages::set_labels", ['filter' => 'checkRoles']);
$routes->get('admin/languages/change/(:any)', "admin\Languages::change/$1", ['filter' => 'checkRoles']);



$routes->get('admin/home/fetch_sales', 'admin\Home::fetch_sales');
$routes->get('admin/home/fetch_warehouse_sales', 'admin\Home::fetch_warehouse_sales');
$routes->get('admin/home/fetch_data', 'admin\Home::fetch_data');
$routes->get('admin/home/fetch_purchases', 'admin\Home::fetch_purchases');
$routes->get('admin/home/switch_businesses/(:any)', 'admin\Home::switch_businesses/$1');

$routes->get('admin/customers', 'admin\Customers::index', ['filter' => 'checkpermissions:module=customers,action=can_read']);
$routes->post('admin/customers/save_status', 'admin\Customers::save_status', ['filter' => 'checkpermissions:module=customers,action=can_update']);
$routes->get('admin/customers/customers_table', 'admin\Customers::customers_table', ['filter' => 'checkpermissions:module=customers,action=can_read']);

$routes->get('admin/delivery_boys', 'admin\Delivery_Boys::index', ['filter' => 'checkpermissions:module=delivery_boys,action=can_read']);
$routes->post('admin/delivery_boys/save', 'admin\Delivery_Boys::save', ['filter' => 'checkpermissions:module=delivery_boys,action=can_create']);
$routes->get('admin/delivery_boys/count/(:any)', 'admin\Delivery_Boys::count/$1', ['filter' => 'checkpermissions:module=delivery_boys,action=can_read']);
$routes->get('admin/delivery_boys/delivery_boys_table', 'admin\Delivery_Boys::delivery_boys_table', ['filter' => 'checkpermissions:module=delivery_boys,action=can_read']);
$routes->post('admin/delivery_boys/get-delivery-boy', 'admin\Delivery_Boys::get_delivery_boy', ['filter' => 'checkpermissions:module=delivery_boys,action=can_read']);

$routes->get('admin/orders', 'admin\Orders::index', ['filter' => 'checkpermissions:module=pos,action=can_create']);
$routes->get('admin/orders/sales_order', 'admin\Orders::sales_order', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->post('admin/orders/save_sales_order', 'admin\Orders::save_sales_order', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('admin/orders/orders', 'admin\Orders::orders', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/orders_table', 'admin\Orders::orders_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/view_orders/(:any)', 'admin\Orders::view_orders/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->post('admin/orders/save_order', 'admin\Orders::save_order', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('admin/orders/orders_table', 'admin\Orders::orders_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->post('admin/orders/create_status', 'admin\Orders::create_status', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('admin/orders/set_delivery_boy', 'admin\Orders::set_delivery_boy', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->add('admin/orders/update_order_status', 'admin\Orders::update_order_status', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/customer_balance', 'admin\Orders::customer_balance', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/get_users', 'admin\Orders::get_users', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->post('admin/orders/register', 'admin\Orders::register', ['filter' => 'checkpermissions:module=orders,action=can_create']);

$routes->post('admin/orders/save', 'admin\Orders::save', ['filter' => 'checkpermissions:module=customers,action=can_create']);

$routes->get('admin/orders/payment_reminder', 'admin\Orders::payment_reminder', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/payment_reminder_table', 'admin\Orders::payment_reminder_table', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/orders/send_reminder', 'admin\Orders::send_reminder', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/subscription', 'admin\Subscription::index', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('admin/subscription/packages', 'admin\Subscription::packages', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('admin/subscription/package_table', 'admin\Subscription::package_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('admin/subscription/(:any)', 'admin\Subscription::checkout/$1', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->get('admin/subscription/package_table', 'admin\Subscription::package_table', ['filter' => 'checkpermissions:module=subscription,action=can_read']);
$routes->post('admin/subscription/free_subscription', 'admin\Subscription::free_subscription', ['filter' => 'checkpermissions:module=subscription,action=can_create']);

$routes->get('admin/customers_subscription', 'admin\Customers_Subscription::index', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);
$routes->get('admin/customers_subscription/customers_subscription_table', 'admin\Customers_Subscription::customers_subscription_table', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);
$routes->get('admin/customers_subscription/customers_services_table', 'admin\Customers_Subscription::customers_services_table', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);
$routes->get('admin/customers_subscription/recursive_services_table', 'admin\Customers_Subscription::recursive_services_table', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);
$routes->get('admin/customers_subscription/customers_list_of_services_table', 'admin\Customers_Subscription::customers_list_of_services_table', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);
$routes->get('admin/customers_subscription/remove_subscription/(:any)', 'admin\Customers_Subscription::remove_subscription/$1', ['filter' => 'checkpermissions:module=customers_subscription,action=can_read']);

$routes->get('admin/transactions', 'admin\Transactions::index', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('admin/transactions/transactions_table', 'admin\Transactions::transactions_table', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('admin/transactions/customers_table', 'admin\Transactions::customers_table', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('admin/transactions/customer_transaction_table/(:any)/(:any)/', 'admin\Transactions::customer_transaction_table/$1/$2/', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->get('admin/transactions/purchase_transaction_table/(:any)', 'admin\Transactions::purchase_transaction_table/$1', ['filter' => 'checkpermissions:module=transactions,action=can_read']);
$routes->post('admin/transactions/save_payment', 'admin\Transactions::save_payment');

$routes->get('admin/products', 'admin\Products::index', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/add_products', 'admin\Products::Add_products', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->get('admin/products/json', 'admin\Products::json', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/scanned_barcode_items', 'admin\Products::scanned_barcode_items', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/products_table', 'admin\Products::products_table', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/update_variant_status', 'admin\Products::update_variant_status', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->get('admin/products/remove_variant/(:any)', 'admin\Products::remove_variant/$1', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->get('admin/products/variants_table', 'admin\Products::variants_table/$1', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/edit_product/(:any)', 'admin\Products::edit_product/$1', ['filter' => 'checkpermissions:module=products,action=can_update']);
$routes->post('admin/products/save_products', 'admin\Products::save_products', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->get('admin/products/stock', 'admin\Products::stock', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/manage_stock', 'admin\Products::manage_stock', ['filter' => 'checkpermissions:module=manage_stock,action=can_read']);
$routes->get('admin/products/fetch_stock', 'admin\Products::fetch_stock', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->post('admin/products/save_adjustment', 'admin\Products::save_adjustment', ['filter' => 'checkpermissions:module=manage_stock,action=can_create']);
$routes->get('admin/products/table', 'admin\Products::table', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/stock_alert', 'admin\Products::stock_alert', ['filter' => 'checkpermissions:module=products,action=can_read']);
$routes->get('admin/products/warehouse-stock-alert', 'admin\Products::warehouse_stock_alert');

$routes->get('admin/services', 'admin\Services::index', ['filter' => 'checkpermissions:module=services,action=can_read']);
$routes->get('admin/services/add_service', 'admin\Services::Add_service', ['filter' => 'checkpermissions:module=services,action=can_read']);
$routes->get('admin/services/service_table', 'admin\Services::service_table', ['filter' => 'checkpermissions:module=services,action=can_read']);
$routes->get('admin/services/edit_service/(:any)', 'admin\Services::edit_service/$1', ['filter' => 'checkpermissions:module=services,action=can_update']);
$routes->post('admin/services/save_services', 'admin\Services::save_services', ['filter' => 'checkpermissions:module=services,action=can_create']);
$routes->get('admin/services/json', 'admin\Services::json', ['filter' => 'checkpermissions:module=services,action=can_read']);

$routes->get('admin/units', 'admin\Units::index', ['filter' => 'checkpermissions:module=units,action=can_read']);
$routes->post('admin/units/save_unit', 'admin\Units::save_unit', ['filter' => 'checkpermissions:module=units,action=can_create']);
$routes->get('admin/units/unit_table', 'admin\Units::unit_table', ['filter' => 'checkpermissions:module=units,action=can_read']);
$routes->get('admin/units/edit_unit/(:any)', 'admin\Units::edit_unit/$1', ['filter' => 'checkpermissions:module=units,action=can_update']);

$routes->get('admin/categories', 'admin\Categories::index', ['filter' => 'checkpermissions:module=categories,action=can_read']);
$routes->post('admin/categories/save_categories', 'admin\Categories::save_categories', ['filter' => 'checkpermissions:module=categories,action=can_create']);
$routes->get('admin/categories/category_table', 'admin\Categories::category_table', ['filter' => 'checkpermissions:module=categories,action=can_read']);
$routes->get('admin/categories/edit_category/(:any)', 'admin\Categories::edit_category/$1', ['filter' => 'checkpermissions:module=categories,action=can_update']);


$routes->get('admin/businesses', 'admin\Businesses::index', ['filter' => 'checkpermissions:module=business,action=can_read']);
$routes->get('admin/businesses/business_table', 'admin\Businesses::business_table', ['filter' => 'checkpermissions:module=business,action=can_read']);
$routes->get('admin/businesses/edit_business/(:any)', 'admin\Businesses::edit_business/$1', ['filter' => 'checkRoles']);
$routes->post('admin/businesses/save_business', 'admin\Businesses::save_business', ['filter' => 'checkRoles']);
$routes->get('admin/businesses/edit_business', 'admin\Businesses::edit_business', ['filter' => 'checkRoles']);

$routes->get('admin/businesses/update_default_business', 'admin\Businesses::update_default_business', ['filter' => 'checkRoles']);

$routes->get('admin/subscription_transactions', 'admin\Subscription_Transactions::index', ['filter' => 'checkpermissions:module=businesses,action=can_read']);
$routes->get('admin/subscription_transactions/transactions_table', 'admin\Subscription_Transactions::transactions_table', ['filter' => 'checkpermissions:module=businesses,action=can_read']);

$routes->get('admin/payments', 'admin\Payments::index', ['filter' => 'checkpermissions:module=payments,action=can_read']);
$routes->post('admin/payments/pre_payment_setup', 'admin\Payments::pre_payment_setup', ['filter' => 'checkpermissions:module=payments,action=can_create']);
$routes->post('admin/payments/post_payment', 'admin\Payments::post_payment', ['filter' => 'checkpermissions:module=payments,action=can_create']);
$routes->get('admin/payments/payment_success', 'admin\Payments::payment_success', ['filter' => 'checkpermissions:module=payments,action=can_read']);
$routes->get('admin/payments/payment_failed', 'admin\Payments::payment_failed', ['filter' => 'checkpermissions:module=payments,action=can_read']);

$routes->get('admin/profile', 'admin\Profile::index');
$routes->post('admin/profile/update', 'admin\Profile::update');

$routes->get('admin/invoices', 'admin\Invoices::index', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/invoices/invoice/(:any)', 'admin\Invoices::invoice/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/invoices/view_invoice/(:any)', 'admin\Invoices::view_invoice/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->get('admin/invoices/invoice_table/(:any)', 'admin\Invoices::invoice_table/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);
$routes->post('admin/invoices/send', 'admin\Invoices::send', ['filter' => 'checkpermissions:module=orders,action=can_create']);
$routes->get('admin/invoices/thermal_print/(:any)', 'admin\Invoices::thermal_print/$1', ['filter' => 'checkpermissions:module=orders,action=can_read']);

$routes->get('admin/posprinter', 'admin\Posprinter::index', ['filter' => 'checkpermissions:module=posprinter,action=can_read']);

$routes->get('admin/purchases', 'admin\Purchases::index', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('admin/purchases/get_suppliers', 'admin\Purchases::get_suppliers', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('admin/purchases/purchase_table', 'admin\Purchases::purchase_table', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('admin/purchases/view_purchase/(:any)', 'admin\Purchases::view_purchase/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('admin/purchases/purchase_orders/(:any)', 'admin\Purchases::purchase_orders/$1', ['filter' => 'checkpermissions:module=purchases,action=can_create']);
$routes->post('admin/purchases/save', 'admin\Purchases::save', ['filter' => 'checkpermissions:module=purchases,action=can_create']);
$routes->post('admin/purchases/update_status_bulk', 'admin\Purchases::update_status_bulk', ['filter' => 'checkpermissions:module=purchases,action=can_update']);
$routes->get('admin/purchases/invoice/(:any)', 'admin\Purchases::invoice/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('admin/purchases/invoice_table/(:any)', 'admin\Purchases::invoice_table/$1', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('admin/purchases/update_order_status', 'admin\Purchases::update_order_status', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->get('admin/purchases/purchase_return', 'admin\Purchases::purchase_return', ['filter' => 'checkpermissions:module=purchases,action=can_read']);
$routes->get('admin/purchases/purchase_return_table', 'admin\Purchases::purchase_return_table', ['filter' => 'checkpermissions:module=purchases,action=can_read']);

$routes->post('admin/bulk_uploads/import_suppliers', 'admin\Bulk_Uploads::import_suppliers', ['filter' => 'checkpermissions:module=suppliers,action=can_read']);

$routes->post('admin/bulk_uploads/import_purchases', 'admin\Bulk_Uploads::import_purchases', ['filter' => 'checkpermissions:module=purchases,action=can_create']);

$routes->post('admin/bulk_uploads/import_products', 'admin\Bulk_Uploads::import_products', ['filter' => 'checkpermissions:module=products,action=can_create']);
$routes->post('admin/bulk_uploads/import_categories', 'admin\Bulk_Uploads::import_categories', ['filter' => 'checkpermissions:module=purchases,action=can_create']);
$routes->post('admin/bulk_uploads/import_stock', 'admin\Bulk_Uploads::import_stock', ['filter' => 'checkpermissions:module=purchases,action=can_create']);
$routes->post('admin/transactions/save_purchase_payment', 'admin\Transactions::save_purchase_payment', ['filter' => 'checkpermissions:module=purchases,action=can_create']);



$routes->get('admin/suppliers', 'admin\Suppliers::index', ['filter' => 'checkpermissions:module=suppliers,action=can_read']);
$routes->get('admin/suppliers/create', 'admin\Suppliers::create', ['filter' => 'checkpermissions:module=suppliers,action=can_create']);
$routes->post('admin/suppliers/save', 'admin\Suppliers::save', ['filter' => 'checkpermissions:module=suppliers,action=can_create']);
$routes->get('admin/suppliers/suppliers_table', 'admin\Suppliers::suppliers_table', ['filter' => 'checkpermissions:module=suppliers,action=can_read']);
$routes->get('admin/suppliers/edit/(:any)', 'admin\Suppliers::edit/$1', ['filter' => 'checkpermissions:module=suppliers,action=can_update']);


$routes->get('admin/webhooks', 'admin\Webhooks::index', ['filter' => 'checkpermissions:module=webhooks,action=can_read']);
$routes->get('admin/webhooks/stripe', 'admin\Webhooks::stripe', ['filter' => 'checkpermissions:module=webhooks,action=can_read']);
$routes->get("admin/webhooks/(:any)", "admin\Webhooks::$1", ['filter' => 'checkpermissions:module=webhooks,action=can_read']);
$routes->post("admin/webhooks/(:any)", "admin\Webhooks::$1", ['filter' => 'checkpermissions:module=webhooks,action=can_create']);

$routes->get('admin/bulk_uploads', 'admin\Bulk_Uploads::index', ['filter' => 'checkpermissions:module=bulk_uploads,action=can_read']);
$routes->post('admin/bulk_uploads/import_orders', 'admin\Bulk_Uploads::import_orders', ['filter' => 'checkpermissions:module=bulk_uploads,action=can_create']);

$routes->get('admin/expenses', 'admin\Expenses::index', ['filter' => 'checkpermissions:module=expenses,action=can_read']);
$routes->get('admin/get_expenses_type', 'admin\Expenses::get_expenses_type');
$routes->add('admin/expenses/add', 'admin\Expenses::add', ['filter' => 'checkpermissions:module=expenses,action=can_create']);
$routes->add('admin/expenses/expenses_table', 'admin\Expenses::expenses_table', ['filter' => 'checkpermissions:module=expenses,action=can_read']);
$routes->add('admin/expenses/edit_expenses/(:any)', 'admin\Expenses::edit/$1', ['filter' => 'checkpermissions:module=expenses,action=can_update']);
$routes->add('admin/expenses/save', 'admin\Expenses::save', ['filter' => 'checkpermissions:module=expenses,action=can_read']);


$routes->get('admin/expenses_type/', 'admin\Expenses_Type::index', ['filter' => 'checkpermissions:module=expenses_type,action=can_read']);
$routes->post('admin/expenses_type/save_expenses_type', 'admin\Expenses_Type::save_expenses_type', ['filter' => 'checkpermissions:module=expenses_type,action=can_read']);
$routes->get('admin/expenses_type/expenses_type_table', 'admin\Expenses_Type::expenses_type_table', ['filter' => 'checkpermissions:module=expenses_type,action=can_read']);
$routes->get('admin/expenses_type/edit_expenses_type/(:any)', 'admin\Expenses_Type::edit_expenses_type/$1', ['filter' => 'checkpermissions:module=expenses_type,action=can_update']);



$routes->get('admin/payment_reports', 'admin\Payment_reports::index', ['filter' => 'checkRoles']);
$routes->get('admin/payment_reports/payment_reports_table', 'admin\Payment_reports::payment_reports_table', ['filter' => 'checkRoles']);


$routes->get('admin/sales_summary', 'admin\Sales_summary::index', ['filter' => 'checkRoles']);
$routes->get('admin/sales_summary/sales_summary_table', 'admin\Sales_summary::sales_summary_table', ['filter' => 'checkRoles']);

$routes->get('admin/top_selling_products', 'admin\Top_Selling_Products::index', ['filter' => 'checkRoles']);
$routes->add(
    'admin/top_selling_products/top_selling_products_table',
    'admin\Top_Selling_Products::top_selling_products_table',
    ['filter' => 'checkRoles']
);

$routes->get('admin/best_customers', 'admin\Best_Customers::index', ['filter' => 'checkRoles']);
$routes->add(
    'admin/best_customers/best_customers_table',
    'admin\Best_Customers::best_customers_table',
    ['filter' => 'checkRoles']
);

$routes->get('admin/purchases_report', 'admin\Purchases_Report::index', ['filter' => 'checkpermissions:module=purchases_report,action=can_read']);
$routes->add(
    'admin/purchases_report/purchases_report_table',
    'admin\Purchases_Report::purchases_report_table',
    ['filter' => 'checkpermissions:module=purchases_report,action=can_read']
);


$routes->get('admin/profit_loss', 'admin\Profit_loss::index', ['filter' => 'checkRoles']);
$routes->get('admin/profit_loss/profit_loss_table', 'admin\Profit_loss::profit_loss_table', ['filter' => 'checkRoles']);

$routes->get('admin/settings/general', 'admin\Settings::general', ['filter' => 'checkRoles']);
$routes->post('admin/settings/save_settings', 'admin\Settings::save_settings', ['filter' => 'checkRoles']);
$routes->get('admin/settings/about_us', 'admin\Settings::about_us', ['filter' => 'checkRoles']);
$routes->get('admin/settings/payment_gateway', 'admin\Settings::payment_gateway', ['filter' => 'checkRoles']);
$routes->get('admin/settings/refund_policy', 'admin\Settings::refund_policy', ['filter' => 'checkRoles']);
$routes->get('admin/settings/terms_and_conditions', 'admin\Settings::terms_and_conditions', ['filter' => 'checkRoles']);
$routes->get('admin/settings/privacy_policy', 'admin\Settings::privacy_policy', ['filter' => 'checkRoles']);

$routes->get('admin/tax/', 'admin\Tax::index', ['filter' => 'checkRoles']);
$routes->post('admin/tax/save_tax', 'admin\Tax::save_tax', ['filter' => 'checkRoles']);
$routes->get('admin/tax/tax_table', 'admin\Tax::tax_table', ['filter' => 'checkRoles']);
$routes->get('admin/tax/edit_tax/(:any)', 'admin\Tax::edit_tax/$1', ['filter' => 'checkRoles']);


$routes->get('admin/updater', 'admin\Updater::index', ['filter' => 'checkRoles']);
$routes->post('admin/updater/upload_update_file', 'admin\Updater::upload_update_file', ['filter' => 'checkRoles']);
$routes->get('admin/settings/email', 'admin\Settings::email', ['filter' => 'checkRoles']);


$routes->get('admin/migrate/', 'admin\Migrate::index');
$routes->get('admin/migrate/rollback/(:any)', 'admin\Migrate::rollback/$1', ['filter' => 'checkRoles']);
$routes->get('admin/migrate/is_dir_empty/(:any)', 'admin\Migrate::is_dir_empty/$1', ['filter' => 'checkRoles']);

$routes->get('admin/Cron_job/', 'admin\Cron_job::renew_service', ['filter' => 'checkRoles']);
$routes->get('admin/Cron_job/test', 'admin\Cron_job::test', ['filter' => 'checkRoles']);

$routes->get('admin/updater', 'admin\Updater::index', ['filter' => 'checkpermissions:module=updater,action=can_read']);
$routes->post('admin/updater/upload_update_file', 'admin\Updater::upload_update_file', ['filter' => 'checkRoles']);

$routes->get('admin/generate_barcode', 'admin\Generate_barcode::index', ['filter' => 'checkpermissions:module=generate_barcode,action=can_create']);
$routes->post('admin/updater/upload_update_file', 'admin\Updater::upload_update_file', ['filter' => 'checkRoles']);
$routes->get('admin/database/backup', 'admin\Database::backup', ['filter' => 'checkRoles']);
$routes->post('admin/database/backup_database', 'admin\Database::backup_database', ['filter' => 'checkRoles']);
$routes->get('admin/database/backup_database', 'admin\Database::backup_database', ['filter' => 'checkRoles']);
$routes->post('admin/database/delete', 'admin\Database::delete', ['filter' => 'checkRoles']);

$routes->get('admin/database', 'admin\Database::index', ['filter' => 'checkRoles']);
$routes->post('admin/database/mail_database', 'admin\Database::mail_database', ['filter' => 'checkRoles']);
$routes->post('admin/database/download', 'admin\Database::download', ['filter' => 'checkRoles']);


$routes->post('admin/tax/get_taxs', 'admin\Products::get_taxs');

// $routes->group('admin/team_members', ['filter' => 'checkRoles'], function ($routes) {

//     $routes->get('admin/team_members', 'admin\Team_members::index');

//     $routes->get('admin/team_members/view_team_members', 'admin\Team_members::view_team_members');
//     $routes->get('admin/team_members/create', 'admin\Team_members::create');
//     $routes->get('admin/team_members/edit_user/(:any)', 'admin\Team_members::edit_user/$1');
// });

$routes->get('admin/team_members', 'admin\Team_members::index', ['filter' => 'checkRoles']);
$routes->get('admin/team_members/view_team_members', 'admin\Team_members::view_team_members', ['filter' => 'checkRoles']);
$routes->get('admin/team_members/create', 'admin\Team_members::create', ['filter' => 'checkRoles']);
$routes->post('admin/team_members/save', 'admin\Team_members::save', ['filter' => 'checkRoles']);
$routes->get('admin/team_members/edit_user/(:any)', 'admin\Team_members::edit_user/$1', ['filter' => 'checkRoles']);
$routes->post('admin/team_members/update_user', 'admin\Team_members::update_user', ['filter' => 'checkRoles']);

$routes->get('admin/warehouse', 'admin\Warehouse::index', ['filter' => 'checkRoles']);
$routes->post('admin/warehouse/save', 'admin\Warehouse::save', ['filter' => 'checkRoles']);
$routes->get('admin/warehouse/warehouse-table', 'admin\Warehouse::WarehouseTable');
$routes->post('admin/warehouse/get-warehouse/(:num)', 'admin\Warehouse::getWarehouse/$1');
$routes->post('admin/warehouse/get-all-warehouse', 'admin\Warehouse::getAllWarehouse');
$routes->post('admin/warehouse/sync-all-all-products', 'admin\Warehouse::syncAllProducts');


$routes->post('admin/product/save_transfer', 'admin\Products::save_transfer');

$routes->get('admin/get_todays_expense', 'admin\Home::todays_total_expense');
$routes->get('admin/todays_total_sales', 'admin\Home::todays_total_sales');
$routes->get('admin/todays_total_payment_resived', 'admin\Home::todays_total_payment_resived_form_orders');
$routes->get('admin/todays_total_payment_remaining', 'admin\Home::todays_total_payment_remaining_form_orders');
$routes->get('admin/todays_total_purchase', 'admin\Home::todays_total_purchase');
$routes->get('admin/todays_total_paids', 'admin\Home::todays_total_paids_resived_form_purchase');
$routes->get('admin/todays_total_remaining', 'admin\Home::todays_total_remaining_form_purchase');
$routes->get('admin/totdays_profit', 'admin\Home::totdays_profit');

$routes->get('admin/brands', 'admin\Brand::index', ['filter' => 'checkpermissions:module=brand,action=can_read']);
$routes->post('admin/brand/add', 'admin\Brand::Add'); // checked permission inside controler function;
$routes->get('admin/brand/brand-table', 'admin\Brand::table', ['filter' => 'checkpermissions:module=brand,action=can_read']);
$routes->post('admin/brand/get-brand', 'admin\Brand::get_brand'); // checked permission inside controler function;
$routes->post('admin/brand/update', 'admin\Brand::update'); // checked permission inside controler function;
$routes->post('admin/brand/delete-brand', 'admin\Brand::delete'); // checked permission inside controler function;

$routes->get('admin/cache-clear', function () {

    try {
        // Run the command using the shell
        $output = shell_exec('php spark cache:clear');
        return 'Cache cleared successfully! Output: ' . $output;
    } catch (\Throwable $e) {
        return 'Failed to clear cache: ' . $e->getMessage();
    }
});

$routes->get('admin/migrate', function () {

    try {
        // Run the command using the shell
        $output = shell_exec('php spark migrate');
        return 'Migrations completed successfully! Output: ' . $output;
    } catch (\Throwable $e) {
        return 'Failed to run migrations: ' . $e->getMessage();
    }
});

$routes->get('admin/seed/balance-warehouse-stock', function () {
    try {
        $seeder = \Config\Database::seeder();
        $seeder->call('WarehouseProductStockSeeder');
        return "Seeders ran successfully.";
    } catch (\Exception $e) {
        return "Error running seeder: " . $e->getMessage();
    }
});

$routes->get('admin/update-transaction-data', 'admin\Transactions::updateTransactionsData');  // this for correcting data in customers_transactions table;

$routes->get('admin/seed/warehouse', function () {
    // Get the database connection
    $db = \Config\Database::connect();
    $errorMessage = "";

    // Check if the 'warehouses' table exists
    if (!$db->tableExists('warehouses')) {
        $errorMessage =  "Error: 'warehouses' table does not exist.";
    }

    // Check if a warehouse with ID 1 exists
    $warehouseExists = $db->table('warehouses')->where('id', 1)->countAllResults() > 0;

    if ($warehouseExists) {
        $errorMessage = "Error: Warehouse with ID 1 already exists.";
    }

    if ($errorMessage) {
        return $errorMessage;
    }

    // Load the seeder
    $seeder = \Config\Database::seeder();

    // Run the seeders
    try {
        $seeder->call('WarehouseSeeder');
        $seeder->call('WarehouseProductStockSeeder');
        return "Seeders ran successfully.";
    } catch (\Exception $e) {
        return "Error running seeder: " . $e->getMessage();
    }
});
