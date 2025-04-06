<?php
// delivery boys routes

$routes->get('delivery_boy/home', 'delivery_boy\Home::index');
$routes->get('delivery_boy/home/switch_businesses/(:any)', 'delivery_boy\Home::switch_businesses/$1');
$routes->get('delivery_boy/home/login', 'delivery_boy\Home::login');
$routes->get('delivery_boy/home/fetch_sales', 'delivery_boy\Home::fetch_sales');

$routes->get('delivery_boy/customers', 'delivery_boy\Customers::index');
$routes->get('delivery_boy/customers/customers_table', 'delivery_boy\Customers::customers_table');

$routes->get('delivery_boy/orders', 'delivery_boy\Orders::index');
$routes->get('delivery_boy/orders/create', 'delivery_boy\Orders::create');
$routes->post('delivery_boy/orders/save_order', 'delivery_boy\Orders::save_order');
$routes->post('delivery_boy/orders/update_status_bulk', 'delivery_boy\Orders::update_status_bulk');
$routes->get('delivery_boy/orders/orders_table', 'delivery_boy\Orders::orders_table');
$routes->add('delivery_boy/orders/update_order_status', 'delivery_boy\Orders::update_order_status');

$routes->post('delivery_boy/orders/register', 'delivery_boy\Orders::register');
$routes->get('delivery_boy/orders/details/(:any)', 'delivery_boy\Orders::details/$1');

$routes->get('delivery_boy/transactions', 'delivery_boy\Transactions::index');
$routes->get('delivery_boy/transactions/transactions_table', 'delivery_boy\Transactions::transactions_table');
$routes->get('delivery_boy/transactions/customer_transaction_table/(:any)/(:any)/', 'delivery_boy\Transactions::customer_transaction_table/$1/$2/');
$routes->get('delivery_boy/transactions/customers_table', 'delivery_boy\Transactions::customers_table');

?>
