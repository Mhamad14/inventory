<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Categories_model;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;
use App\Models\Delivery_boys_model;
use App\Models\Orders_items_model;
use App\Models\Orders_model;
use App\Models\Orders_services_model;
use App\Models\Status_model;
use App\Models\Subscription_model;
use App\Models\Tax_model;
use App\Models\WarehouseProductStockModel;

class Orders extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    protected $data;

    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['products'] = get_products_of_business($business_id);
            $data['variants'] = isset($data['products'][0]['variants']) ? $data['products'][0]['variants'] : "";
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "orders_list";
            $data['title'] = "Orders List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['businesses'] = $businesses;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();

            $permission = get_delivery_boy_permission($id, $business_id);
            $orders_permission = $permission['orders_permission'];
            if ($orders_permission == "1") {
                $data['orders_permission'] = $orders_permission;
            } else {
                $data['orders_permission'] = "0";
            }
            return view("delivery-man/template", $data);
        }
    }
    public function create()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);

            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "create_orders";
            $data['title'] = "Create Orders - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();

            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['businesses'] = $businesses;
            $category_model = new Categories_model();
            $data['categories'] =  $category_model->get_categories($id, $business_id);
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['status'] = isset($status) ? $status : "";
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();

            return view("delivery-man/template", $data);
        }
    }

    public function save_order()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {

            if (isset($_POST) && !empty($_POST)) {
                if (!isset($_POST['data']) || empty($_POST['data'])) {
                    $response = [
                        'error' => true,
                        'message' => 'Please add order item',
                        'csrf_token' => csrf_token(),
                        'csrf_hash' => csrf_hash(),
                        'data' => []
                    ];
                    return $this->response->setJSON($response);
                }

                $amount_paid = '';
                $order_items = json_decode($_POST['data']);
                if (!isset($_POST['customer_id']) || empty($_POST['customer_id'])) {
                    $response = [
                        'error' => true,
                        'message' => "Please select the customer!",
                        'csrf_token' => csrf_token(),
                        'csrf_hash' => csrf_hash(),
                        'data' => []
                    ];

                    return $this->response->setJSON($response);
                }
                $rules = [
                    'total' => 'required|trim',
                    'final_total' => 'required|trim',
                    'payment_status' => 'required|trim',
                    'amount_paid' => 'trim',
                    'status' => 'required|trim',
                ];
                if ($this->request->getPost('quantity')) {
                    $rules['quantity'] = 'trim|numeric|greater_than_equal_to[0]';
                }
                $payment_method = $this->request->getVar('payment_method');
                if ($this->request->getPost('payment_status') != "unpaid" &&  $this->request->getPost('payment_status') != "cancelled") {
                    if (isset($_POST['payment_method']) && $_POST['payment_method'] != 'cash' && $_POST['payment_method'] != 'wallet') {
                        $rules['transaction_id'] = 'trim|required';
                    }
                    if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'other') {
                        $rules['payment_method_name'] = 'trim|required';
                    }
                } else {
                    $payment_method = null;
                }

                $this->validation->setRules($rules);
                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                    $vendor = get_vendor_of_delivery_boy($_SESSION['user_id']);

                    $vendor_id = $vendor;
                    $customer_id = $_POST['customer_id'];
                    $payment_type = $this->request->getVar('payment_method');
                    $payment_status = $this->request->getVar('payment_status');
                    $customer = fetch_details('customers', ['user_id' => $customer_id]);
                    $customer_wallet_balance = !empty($customer) ? floatval($customer[0]['balance']) : "0";
                    $final_total = floatval($this->request->getVar('final_total'));
                    if ($payment_type == "wallet") {
                        if ($customer_wallet_balance < $final_total) {
                            $response = [
                                'error' => true,
                                'message' => 'You dont have sufficient wallet balance,Please recharge wallet!',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'You dont have sufficient wallet balance,Please recharge wallet!';
                            $_SESSION['toastMessageType']  = 'error';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');
                            return $this->response->setJSON($response);
                        }
                    }
                    // save order frome here
                    if ($payment_status == "fully_paid") {
                        $amount_paid = $this->request->getVar('final_total');
                    }
                    if ($payment_status == "partially_paid") {
                        $amount_paid = $this->request->getVar('amount_paid');
                        if ($final_total > $amount_paid) {
                            $amount_paid =  $this->request->getVar('amount_paid');
                        } else {
                            $response = [
                                'error' => true,
                                'message' => 'Amount is more than order total please check!',
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'Amount is more than order total please check!';
                            $_SESSION['toastMessageType']  = 'error';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');
                            return $this->response->setJSON($response);
                        }
                    }
                    if ($payment_type == "wallet") {
                        $amount_paid = $this->request->getVar('final_total');
                    }

                    $order = array(
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'customer_id' => $customer_id,
                        'warehouse_id' => null,  // saving null for pos orders as pos orders may have multiple variants with multiple warehouses ;
                        'created_by' => $_SESSION['user_id'],
                        'final_total' => $this->request->getVar('final_total'),
                        'total' => $this->request->getVar('total'),
                        'delivery_charges' => $this->request->getVar('delivery_charges'),
                        'discount' => $this->request->getVar('discount'),
                        'payment_status' => $payment_status,
                        'discount' => $this->request->getVar('discount'),
                        'payment_method' => $this->request->getVar('payment_method'),
                        'order_type' => $this->request->getVar('order_type'),
                        'amount_paid' =>  $amount_paid,
                    );
                    $product_variant_id = array_column($order_items, "product_variant_id");
                    $quantity = array_column($order_items, "quantity");
                    $check_current_stock_status = validate_stock($product_variant_id, $quantity);

                    if ($check_current_stock_status['error'] == true) {
                        $response['error'] = true;
                        $response['message'] = [$check_current_stock_status['message']];
                        $response['data'] = array();
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }

                    $order_model = new Orders_model();
                    $order_model->save($order);
                    $order_id = $order_model->getInsertID();
                    if (!empty($payment_type)) {
                        if ($payment_type == "wallet") {
                            $customers_transactions_model =  new Customers_transactions_model();
                            $transaction = array(
                                'order_id' => $order_id,
                                'customer_id' => $customer_id,
                                'vendor_id' => $vendor_id,
                                'created_by' => $_SESSION['user_id'],
                                'payment_type' => $payment_type,
                                'amount' => $amount_paid,
                                'transaction_id' =>  $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                            $balance = $customer_wallet_balance - $final_total;
                            update_details(['balance' => $balance], ['user_id' => $customer_id], "customers");
                        }
                        if ($payment_type == "cash" && $payment_status == "partially_paid") {
                            $customers_transactions_model =  new Customers_transactions_model();
                            $transaction = array(
                                'order_id' => $order_id,
                                'customer_id' => $customer_id,
                                'vendor_id' => $vendor_id,
                                'created_by' => $_SESSION['user_id'],
                                'payment_type' => $payment_type,
                                'amount' => $amount_paid,
                                'transaction_id' =>  $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                        }
                        if ($payment_type != "cash" && $payment_type != "wallet") {
                            $customers_transactions_model =  new Customers_transactions_model();
                            $transaction = array(
                                'order_id' => $order_id,
                                'customer_id' => $customer_id,
                                'vendor_id' => $vendor_id,
                                'created_by' => $_SESSION['user_id'],
                                'payment_type' => $payment_type,
                                'amount' => $amount_paid,
                                'transaction_id' =>  $this->request->getVar('transaction_id'),

                            );
                            $customers_transactions_model->save($transaction);
                        }
                    }
                    $tax_model = new Tax_model();
                    $orders_items_model = new Orders_items_model();
                    $orders_services_model = new Orders_services_model();
                    $warehouse_product_stock_model = new WarehouseProductStockModel();
                    foreach ($order_items as $item) {

                        $tax_ids = (isset($item->tax_id) && $item->tax_id != 0) ? $item->tax_id : [];
                        $tax_details = [];

                        if (gettype($tax_ids) == "string") {
                            $tax_ids = (array) $tax_ids;
                            $tax_ids = $tax_ids[0];
                        }
                        foreach ($tax_ids as $tax_id) {
                            $tax = $tax_model->find($tax_id);
                            $tax_details[] =  [
                                'tax_id' => $tax_id,
                                'name' => $tax['name'],
                                'percentage' => $tax['percentage']
                            ];
                        }
                        $tax_details = empty($tax_details) ? "[]" :  json_encode($tax_details);

                        $sub_total = (floatval($item->price)) * (floatval($item->quantity));
                        if (isset($item->product_id) && !empty($item->product_id)) {
                            $orders_items = array(
                                'order_id' => $order_id,
                                'product_id' => $item->product_id,
                                'product_variant_id' => $item->product_variant_id,
                                'product_name' => $item->variant_name,
                                'quantity' => $item->quantity,
                                'price' => $item->price,
                                'tax_name' => '',
                                'tax_percentage' => '',
                                'is_tax_included' => $item->is_tax_included,
                                'tax_details' => $tax_details,
                                'sub_total' => $sub_total,
                                'status' => $this->request->getVar('status'),
                            );
                            $orders_items_model->save($orders_items);
                            $order_item_id = $orders_items_model->getInsertID();

                            update_stock($item->product_variant_id, $item->quantity);
                            $warehouse_stock = $warehouse_product_stock_model->where('product_variant_id', $item->product_variant_id)->get()->getResultArray();
                            $warehouse_item_max_stock = $warehouse_stock[0];
                            $max_stock = $warehouse_stock[0]['stock'];

                            foreach ($warehouse_stock as $warehouse_stock_item) {
                                if ($max_stock < $warehouse_stock_item['stock']) {
                                    $max_stock = $warehouse_stock_item['stock'];
                                    $warehouse_item_max_stock  = $warehouse_stock_item;
                                }
                            }

                            updateWarehouseStocks(warehouse_id: $warehouse_item_max_stock['warehouse_id'],  product_variant_id: $warehouse_item_max_stock['product_variant_id'],  warehouse_stock: $item->quantity, type: 0);
                        }
                        if (isset($item->service_id) && !empty($item->service_id)) {
                            $recurring_days = find_days($item->starts_on, $item->ends_on);
                            $orders_items = array(
                                'order_id' => $order_id,
                                'service_id' => $item->service_id,
                                'service_name' => $item->service_name,
                                'price' => $item->price,
                                'quantity' => $item->quantity,
                                'unit_name' => $item->unit_name,
                                'unit_id' => $item->unit_id,
                                'tax_percentage' =>  '',
                                'is_tax_included' => $item->is_tax_included,
                                'tax_name' => '',
                                'tax_details' => $tax_details,
                                'is_recursive' => $item->is_recursive,
                                'recurring_days' => $recurring_days,
                                'starts_on' => $item->starts_on,
                                'ends_on' => $item->ends_on,
                                'sub_total' => $sub_total,
                                'status' => $this->request->getVar('status'),
                            );
                            $orders_services_model->save($orders_items);
                        }
                        $subscription_model = new Subscription_model();
                        if (isset($item->is_recursive) && $item->is_recursive == "1") {

                            $data = $subscription_model->if_exist($item->service_id, $_POST['customer_id']);
                            $sub_id = isset($data[0]['id']) && !empty($data[0]['id']) ? $data[0]['id'] : "";
                            $subscription = array(
                                'id' => $sub_id,
                                'service_id' => $item->service_id,
                                'vendor_id' => $vendor_id,
                                'business_id' => $business_id,
                                'customer_id' => $customer_id,
                                'created_by' => $vendor_id,
                            );
                            $subscription_model->save($subscription);
                        }
                    }

                    $response = [
                        'error' => false,
                        'message' => 'order placed successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'order placed successfully';
                    $_SESSION['toastMessageType']  = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('delivery_boy/orders');
            }
        }
    }

    public function orders_table()
    {


        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $orders_model = new Orders_model();
        $orders = $orders_model->get_delivery_boy_orders_list($business_id);
        $total = $orders_model->count_of_orders($business_id);
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        $i = 0;
        $rows = [];
        if (!empty($orders)) {
            foreach ($orders as $order) {

                $customer_id = $order['customer_id'];
                $customer_model = new Customers_model();
                $customer_array = $customer_model->where('user_id', $customer_id)->get()->getResultArray();
                if (empty($customer_array)) {
                    $customer_array = $customer_model->where('id', $customer_id)->get()->getResultArray();
                }
                $user_id =  $customer_array[0]['user_id'];

                $customer_wallet = $customer_model->get_customer($user_id);
                $balance = 0.00;
                foreach ($customer_wallet as $customer) {
                    $balance = $customer['balance'];
                }
                $customers = $this->ionAuth->user($user_id)->row();
                $customer_name = $customers->first_name;
                $order_id = $order['id'];
                if ($order['payment_status'] == "fully_paid") {
                    $status = "<span class='badge badge-success'>Fully Paid</span>";
                }
                if ($order['payment_status'] == "partially_paid") {
                    $status = "<span class='badge badge-primary'>Partially Paid</span>";
                }
                if ($order['payment_status'] == "unpaid") {
                    $status = "<span class='badge badge-warning'>Unpaid</span>";
                }
                if ($order['payment_status'] == "cancelled") {
                    $status = "<span class='badge badge-danger'>Cancelled</span>";
                }
                $view_order = "<a href='" . base_url("delivery_boy/orders/details") . "/" . $order_id . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View Orders'><i class='bi bi-eye'></i></a>" . " ";

                $rows[$i] = [
                    'id' => $order['id'],
                    'customer_name' => $customer_name,
                    'order_date' => date_formats(strtotime($order['created_at'])),
                    'order_type' =>  $order['order_type'],
                    'final_total' => currency_location(decimal_points($order['final_total'])),
                    'payment_status' =>  $status,
                    'amount_paid' => currency_location(decimal_points($order['amount_paid'])),
                    'message' =>  $order['message'],
                    'action' => $view_order

                ];
                $i++;
            }

            $array['total'] = $total[0]['total'];
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }
    public function update_status_bulk()
    {

        $delivery_boy_id = $_SESSION['user_id'];
        $type = $_POST['type'];
        $status = $_POST['status'];
        $item_id = $_POST['item_ids'];
        $msg = "";
        for ($i = 0; $i < count($item_id); $i++) {
            if ($type == "product") {
                $order = is_delivery_boy_assigned($type, $item_id[$i]);
                if ($order[0]['delivery_boy'] != null && !empty($order[0]['delivery_boy']) && $order[0]['delivery_boy'] != $delivery_boy_id) {
                    $delivery_boy_details = $this->ionAuth->user($order[0]['delivery_boy'])->row();
                    $name = $delivery_boy_details->first_name;
                    $msg = "This order is already accepted by " . $name . "!";
                } else {
                    update_details(['status' => $status], ['id' => $item_id[$i]], 'orders_items');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $item_id[$i]], 'orders_items');
                }
                if ($order[0]['delivery_boy'] == $delivery_boy_id) {
                    update_details(['status' => $status], ['id' => $item_id[$i]], 'orders_items');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $item_id[$i]], 'orders_items');
                }
            }
            if ($type == "service") {
                $order = is_delivery_boy_assigned($type, $item_id[$i]);
                if ($order[0]['delivery_boy'] != null && !empty($order[0]['delivery_boy']) && $order[0]['delivery_boy'] != $delivery_boy_id) {
                    $delivery_boy_details = $this->ionAuth->user($order[0]['delivery_boy'])->row();
                    $name = $delivery_boy_details->first_name;
                    $msg = "This order is already accepted by " . $name . "!";
                } else {
                    update_details(['status' => $status], ['id' => $item_id[$i]], 'orders_services');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $item_id[$i]], 'orders_services');
                }
                if ($order[0]['delivery_boy'] == $delivery_boy_id) {
                    update_details(['status' => $status], ['id' => $item_id[$i]], 'orders_services');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $item_id[$i]], 'orders_services');
                }
            }
        }
        if ($msg != "" && !empty($msg)) {
            $response = [
                'error' => true,
                'type' => 'error',
                'message' => $msg,
            ];
            return $this->response->setJSON($response);
        }
        if ($msg == "") {
            $response = [
                'error' => false,
                'type' => 'success',
                'message' => "Order status updated successfully!",
            ];
            return $this->response->setJSON($response);
        }
    }
    public function update_order_status()
    {


        $status = $this->request->getGet('status');
        $order_id = $this->request->getGet('order_id');
        $type = $this->request->getGet('type');
        $delivery_boy_id = $_SESSION['user_id'];
        if ($this->request->getGet('status')) {
            $rules['status'] = 'trim';
        }
        if ($this->request->getGet('order_id')) {
            $rules['order_id'] = 'trim';
        }
        if ($this->request->getGet('type')) {
            $rules['type'] = 'trim';
        }

        $this->validation->setRules($rules);
        if (!$this->validation->run()) {
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'type' => 'error',
                'data' => []
            ];
            return $this->response->setJSON($response);
        } else {

            if ($type == "product") {
                $order = is_delivery_boy_assigned($type, $order_id);
                if (($order[0]['delivery_boy'] != null) && !empty($order[0]['delivery_boy']) && $order[0]['delivery_boy'] != $delivery_boy_id) {
                    $delivery_boy_details = $this->ionAuth->user($order[0]['delivery_boy'])->row();
                    $name = $delivery_boy_details->first_name;
                    $msg = "This order is already accepted by " . $name . "!";
                    $response = [
                        'error' => true,
                        'type' => 'error',
                        'message' => $msg,
                    ];
                    return $this->response->setJSON($response);
                }
                if ($order[0]['delivery_boy'] == $delivery_boy_id) {

                    update_details(['status' => $status], ['id' => $order_id], 'orders_items');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $order_id], 'orders_items');
                    $response = [
                        'error' => false,
                        'type' => 'success',
                        'message' => "Order status updated successfully!",
                    ];
                    return $this->response->setJSON($response);
                }
            }
            if ($type == "service") {
                $order = is_delivery_boy_assigned($type, $order_id);

                if ($order[0]['delivery_boy'] != null && !empty($order[0]['delivery_boy']) && $order[0]['delivery_boy'] != $delivery_boy_id) {
                    $delivery_boy_details = $this->ionAuth->user($order[0]['delivery_boy'])->row();
                    $name = $delivery_boy_details->first_name;
                    $msg = "This order is already accepted by " . $name . "!";
                    $response = [
                        'error' => true,
                        'type' => 'error',
                        'message' => $msg,
                    ];
                    return $this->response->setJSON($response);
                }
                if ($order[0]['delivery_boy'] == $delivery_boy_id) {
                    update_details(['status' => $status], ['id' => $order_id], 'orders_services');
                    update_details(['delivery_boy' => $delivery_boy_id], ['id' => $order_id], 'orders_services');
                    $response = [
                        'error' => false,
                        'type' => 'success',
                        'message' => "Order status updated successfully!",
                    ];
                    return $this->response->setJSON($response);
                }
            }
        }
    }
    public function details($order_id = "")
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $id = $this->ionAuth->getUserId();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $data['businesses'] = $businesses;

            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "order_details";
            $data['title'] = "Order Details - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $data['delivery_boy_id'] = $id;
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $this->data['users'] = $this->ionAuth->users()->result();
            $order = fetch_details("orders", ["id" => $order_id]);

            $permission = get_delivery_boy_permission($id, $business_id);
            $transaction_permission = $permission['transaction_permission'];
            if ($transaction_permission == "1") {
                $data['transaction_permission'] = $transaction_permission;
            } else {
                $data['transaction_permission'] = "0";
            }

            if ($order[0]['business_id'] == $business_id) {
                $customer_id = $order[0]['customer_id'];
                /**
                 * here variable "$customer_id" will reflect "id" column of "users" table if order is created by pos,
                 * and variable "$customer_id" will reflect "id" column of "customer" table if order is created by sales order.
                 * But in "customers_transactions" table we will search with "user_id" column.
                 * So to handle this in-consistency we will assign proper data for to it.
                 */
                $db = \Config\Database::connect();
                $builder = $db->table('customers');
                $customer = $builder->where('id', $customer_id)->get()->getRowArray();
                $user_id = 0;
                if (empty($customer)) {
                    $user_id = $customer_id;
                } else {
                    $user_id = $customer['user_id'];
                }
                $customers = $this->ionAuth->user($user_id)->row();
                $customer_name = $customers->first_name;
                $customer_mobile = $customers->mobile;
                $order[0]['customer_name'] = $customer_name;
                $order[0]['customer_mobile'] = $customer_mobile;
                $customer_model = new Customers_model();
                $customer = $customer_model->get_customer($customer_id);
                $balance = isset($customer[0]['balance']) ? $customer[0]['balance'] : "";
                $order[0]['balance'] = $balance;
                $data['order'] = $order[0];
                $status_model = new Status_model();
                $status = $status_model->get_status($business_id);
                $data['status'] = isset($status) ? $status : "";

                $delivery_boy_model = new Delivery_boys_model();
                $delivery_boys = $delivery_boy_model->delivery_boys($business_id);
                $data['delivery_boys'] = isset($delivery_boys) ? $delivery_boys : "";
                $order_transaction_id = '';
                if (!empty($order['payment_status'])  && $order['payment_status'] == "fully_paid" &&  $order['payment_status'] != "unpaid" && $order['payment_status'] != "partially_paid" && $order['payment_status'] != "cancelled") {
                    if (!empty($order['payment_method']) && $order['payment_method'] != "cash" && $order['payment_method'] != "wallet") {
                        $db = \config\Database::connect();

                        $order_transaction_id = $db->table('customers_transactions')->select('*')->where(['order_id' => $order_id, 'customer_id' => $customer[0]['id']])->get()->getResultArray();
                    }
                }


                $orders_items = fetch_details("orders_items", ["order_id" => $order_id]);
                if (isset($orders_items)) {

                    foreach ($orders_items as $key => $item) {
                        $product_id = $item['product_id'];
                        $product = fetch_details("products", ['id' => $product_id]);
                        $product_image = $product[0]['image'];
                        $orders_items[$key]['image'] = $product_image;
                        $status_id = $item['status'];
                        $statuses = fetch_details("status", ['id' => $status_id]);
                        $status_name = $statuses[0]['status'];
                        $orders_items[$key]['status_name'] = $status_name;
                    }
                    $data['items'] = isset($orders_items) ? $orders_items : "";
                }
                $orders_services = fetch_details("orders_services", ["order_id" => $order_id]);
                if (isset($orders_services)) {
                    foreach ($orders_services as $key => $service) {
                        $service_id = $service['service_id'];
                        $services = fetch_details("services", ['id' => $service_id]);
                        $orders_services[$key]['image'] = $services[0]['image'];
                        $status_id = $service['status'];
                        $statuses = fetch_details("status", ['id' => $status_id]);
                        $status_name = $statuses[0]['status'];
                        $orders_services[$key]['status_name'] = $status_name;
                    }
                    $data['services'] = isset($orders_services) ? $orders_services : "";
                }
            } else {

                $data['items'] = "";
                $data['services'] = "";
                $this->session->setFlashdata('message', 'you dont have order of this business!');
            }
            return view("delivery-man/template", $data);
        }
    }

    protected $validationListTemplate = 'list';
    protected $ionAuthModel;

    public function register()
    {



        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('delivery-man/home/login');
        } else {
            if (isset($_POST) && !empty($_POST)) {

                $tables                        = $this->configIonAuth->tables;
                $identityColumn                = $this->configIonAuth->identity;
                $this->data['identity_column'] = $identityColumn;

                $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'required');
                $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');
                if (!empty($_POST['email'])) {
                    $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'required|valid_email|is_unique[' . $tables['users'] . '.email]');
                }
                // $this->validation->setRule('phone', lang('Auth.create_user_validation_phone_label'), 'required');
                $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');

                if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                    $email    = strtolower($this->request->getPost('email'));
                    $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
                    $password = $this->request->getPost('password');
                    $group_id_arry = fetch_details("groups", ['name' => 'customers'], "id");
                    $group_id = [$group_id_arry[0]['id']];
                    $additionalData = [
                        'first_name' => $this->request->getPost('first_name'),
                        'phone'      => $this->request->getPost('phone'),
                    ];
                }
                if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                    $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                    $balance = isset($_POST['balance']) ? $_POST['balance'] : "";
                    $status =  isset($_POST['status']) ? $_POST['status'] : "1";
                    $customer = [
                        'user_id' => trim($id),
                        'business_id' => trim($business_id),
                        'balance' => trim($balance),
                        'status' =>  trim($status)
                    ];
                    $customer_model = new Customers_model();
                    $customer_model->save($customer);
                    // check to see if we are creating the user
                    // redirect them back to the admin page  
                    $response = [
                        'error' => false,
                        'message' => 'Customer added successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Customer added successfully';
                    $_SESSION['toastMessageType']  = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                } else {
                    // display the create user form
                    // set the flash data error message if there is one

                    $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                    $response['error'] = true;
                    $response['csrf_token'] =  csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['message'] = $this->validation->getErrors();
                    return  $this->response->setJSON($response);
                }
            } else {
                return redirect()->back()->withInput();
            }
        }
    }
}
