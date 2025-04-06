<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->GET('/', 'Home::index');

// $routes->get('/', 'Home::index');
$routes->GET('/home', 'Home::index');
$routes->GET('/contact', 'Contact::index');
$routes->get('/test', 'Home::test');
$routes->get('/features', 'Features::index');
$routes->get('/pricing', 'Pricing::index'); 
$routes->post('admin/orders/process_return', 'Admin\Orders::process_return');

$routes->get('/forgot_password_back', 'Forgot_password_back::index'); 

$routes->get('/login_back', 'Login_back::index');

$routes->get('/about', 'About::index');
$routes->get('/privacy_policy', 'Privacy_policy::index');
$routes->get('/refundpolicy', 'RefundPolicy::index');
$routes->get('/terms_and_conditions', 'Terms_and_conditions::index');

$routes->get('system_pages', 'System_pages::index');
$routes->get('system_pages/about_us', 'System_pages::about_us');
$routes->get('system_pages/terms_and_conditions', 'System_pages::terms_and_conditions');
$routes->get('system_pages/refund_policy', 'System_pages::refund_policy');
$routes->get('system_pages/privacy_policy', 'System_pages::privacy_policy');

$routes->get('/auth/logout', 'Auth::logout');

$routes->get('login', 'Home::index');
$routes->POST('auth/login', 'Auth::login');
$routes->get('forgot_password','Forgot_password::index');
$routes->post('forgot_password/verify','Forgot_password::verify');
$routes->get('forgot_password/reset_password','Forgot_password::showResetForm');
$routes->post('forgot_password/update-password','Forgot_password::update_password');

// $routes->group('auth', ['namespace' => 'IonAuth\Controllers'], function ($routes) {
// 	$routes->add('login', 'Auth::login');
// 	$routes->get('logout', 'Auth::logout');
	// $routes->add('forgot_password', 'Auth::forgot_password');
// 	// $routes->get('/', 'Auth::index');
// 	// $routes->add('create_user', 'Auth::create_user');
// 	// $routes->add('edit_user/(:num)', 'Auth::edit_user/$1');
// 	// $routes->add('create_group', 'Auth::create_group');
// 	// $routes->get('activate/(:num)', 'Auth::activate/$1');
// 	// $routes->get('activate/(:num)/(:hash)', 'Auth::activate/$1/$2');
// 	// $routes->add('deactivate/(:num)', 'Auth::deactivate/$1');
// 	// $routes->get('reset_password/(:hash)', 'Auth::reset_password/$1');
// 	// $routes->post('reset_password/(:hash)', 'Auth::reset_password/$1');
// 	// ...
// });


require 'Routes_admin.php';
require 'Routes_Delivery_boy.php';
