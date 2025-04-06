<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Purchases_items_model;
use App\Models\Purchases_model;
use App\Models\Status_model;
use App\Models\Suppliers_model;
use App\Models\Tax_model;
use App\Models\WarehouseModel;
use App\Models\WarehouseProductStockModel;

class Purchases extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $data;
    protected $settings;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (! isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->findAll();
                if (empty($allbusiness)) {
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('admin/businesses');
                } else {
                    session()->setFlashdata('message', 'Please select a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('admin/businesses');
                }
            }
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'purchases_table';
            $data['title'] = "Purchase List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }

            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();
            $data['order_type'] = 'order';

            $orders = fetch_details('purchases', ['business_id' => $business_id]);

            if (isset($orders) && !empty($orders)) {
                foreach ($orders as $order) {
                    if (floatval($order['amount_paid']) == floatval($order['total'])) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) < floatval($order['total'])) {
                        update_details(['payment_status' => 'partially_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) == 0.00) {
                        update_details(['payment_status' => 'unpaid'], ['id' => $order['id']], "purchases");
                    }
                }
            }
            return view("admin/template", $data);
        }
    }

    public function purchase_orders($type = '')

    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $uri = current_url(true);
            // $orderType = $uri->getSegment(6);
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'purchases';
            $data['title'] = "Purchase Order- " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $data['order_type'] = $type;
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();

            $warehouse_model = new WarehouseModel();
            $data['warehouses']  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();

            return view("admin/template", $data);
        }
    }
    public function get_suppliers()
    {
        $id = $_SESSION['user_id'];
        $suppliers_model = new Suppliers_model();
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $response = $suppliers_model->search_suppliers($search, $id);

            $response = json_decode($response);
            $data = [];
            foreach ($response->data as $supplier) {

                if ($supplier->status) {
                    $data[] = array("id" => $supplier->id, "text" => $supplier->text, "balance" => $supplier->balance, "status" => $supplier->status);
                }
            }
            $data['data'] = $data;
            echo json_encode($data);
        }
    }

    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $rules = [
                    'purchase_date' => [
                        'rules' => 'required',
                        'label' => 'Purchase Date',
                    ],
                    'supplier_id' => [
                        'rules' => 'required',
                        'label' => 'Supplier',
                    ],
                    'products' => [
                        'rules' => 'required',
                        'label' => 'Products',
                    ],
                    'status' => [
                        'rules' => 'required',
                        'label' => 'Status',
                    ],
                    'payment_status' => [
                        'rules' => 'required',
                        'label' => 'Payment Status',
                    ],
                    'warehouse_id' => [
                        'rules' => 'required',
                        'label' => 'Warehouse',
                    ],
                ];
                
                // Add conditional rules for "partially_paid"
                if ($this->request->getVar('payment_status') == "partially_paid") {
                    $rules['amount_paid'] = [
                        'rules' => 'required',
                        'label' => 'Amount Paid',
                    ];
                }
                
                // Add conditional rules for other statuses
                if ( $this->request->getVar('payment_status') != "unpaid" && $this->request->getVar('payment_status') != "cancelled"  ) {
                    $rules['payment_method'] = [
                        'rules' => 'required',
                        'label' => 'Payment Method',
                    ];
                }
                
                // Set the validation rules
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
                    $tax_ids = '[]';
                    $warehouse_id = $this->request->getVar('warehouse_id');

                    $tax_ids_input = $this->request->getVar('order_taxes');
                    if ($tax_ids_input) {
                        $tax_ids_input = json_decode($tax_ids_input);
                        $tax_ids = [];
                        if (is_array($tax_ids_input)) {
                            foreach ($tax_ids_input as $tax) {
                                $tax_ids[] = $tax->id;
                            }
                        }
                        $tax_ids = json_encode($tax_ids);
                    }
                    $purchase_model = new Purchases_model();
                    $user_id = session('user_id');
                    $vendor_id = session('user_id');
                    $business_id = session('business_id');
                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($user_id);
                        if (! userHasPermission('expenses', 'can_update',  $user_id)) {
                            session()->setFlashdata("permission_error", "You do not have permission to access");
                            session()->setFlashdata("type", "error");
                            return json_encode([
                                'total' => 0,
                                'rows' => [],
                            ]);
                        }
                    }

                    $payment_status = $this->request->getVar('payment_status');
                    $payment_method = $this->request->getVar('payment_method');
                    if ( $payment_status == "unpaid" || $payment_status == "cancelled"  ) {
                        $payment_method = null;
                    }else{
                        $payment_method = $payment_method[0];
                    }
                    
                    $amount_paid = 0;

                    if ($payment_status == "fully_paid") {
                        $amount_paid = $this->request->getVar('total');
                    } else if ($payment_status == "partially_paid") {
                        $amount_paid = $this->request->getVar('amount_paid');
                    }

                    $purchase =  array(
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'order_no' => $this->request->getVar('order_no'),
                        'order_type' => $this->request->getVar('order_type'),
                        'warehouse_id' => $warehouse_id,
                        'purchase_date' => $this->request->getVar('purchase_date'),
                        'supplier_id' => $this->request->getVar('supplier_id'),
                        'tax_ids' =>  $tax_ids,
                        'discount' => $this->request->getVar('order_discount'),
                        'delivery_charges' => $this->request->getVar('shipping'),
                        'payment_method' => $payment_method,
                        'payment_status' => $payment_status,
                        'amount_paid' => $amount_paid,
                        'total' => $this->request->getVar('total'),
                        'status' => $this->request->getVar('status'),
                        'message' => $this->request->getVar('message'),
                    );

                    $purchase_model->save($purchase);
                    $purchase_id = $purchase_model->getInsertID();

                    $products = json_decode($_POST['products']);
                    $count = count($products);
                    for ($i = 0; $i < $count; $i++) {
                        $products[($count - 1) - $i]->qty = $_POST['qty'][$i];
                        $products[($count - 1) - $i]->discount = $_POST['discount'][$i];
                    }
                    foreach ($products as $item) {
                        $purchase_items = array(
                            'purchase_id' => $purchase_id,
                            'product_variant_id' => $item->id,
                            'quantity' => $item->qty,
                            'price' => $item->price,
                            'discount' => $item->discount,
                            'status' => $this->request->getVar('status')
                        );
                        $Purchases_items_model = new Purchases_items_model();
                        $Purchases_items_model->save($purchase_items);
                        $order_type = $this->request->getVar('order_type');
                        if ($order_type == "order") {
                            update_stock(product_variant_ids: $item->id, qtns: $item->qty, type: 'plus');
                            updateWarehouseStocks(warehouse_id: $warehouse_id,  product_variant_id: $item->id,  warehouse_stock: $item->qty, type: 1);
                        } elseif ($order_type == "return") {
                            update_stock(product_variant_ids: $item->id, qtns: $item->qty);
                            updateWarehouseStocks(warehouse_id: $warehouse_id,  product_variant_id: $item->id,  warehouse_stock: $item->qty, type: 0);
                        }
                    }
                    $response = [
                        'error' => false,
                        'message' => 'Purchase Order saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->back();
            }
        }
    }

    public function purchase_table()
    {
        $purchase_model = new Purchases_model();
        $user_id = $_SESSION['user_id'];
        $id = 0;

        if ($this->ionAuth->isTeamMember()) {
            $id = get_vendor_for_teamMember($user_id);
        } else {
            $id = $user_id;
        }

        $vendor_id =  $id;
        $business_id = $_SESSION['business_id'];

        // Get sorting and ordering parameters from query string
        $sort = $_GET['sort'] ?? 'id'; // Default sort by 'id'
        $order = $_GET['order'] ?? 'DESC'; // Default order is ascending (ASC)

        // Fetch purchases with sorting and pagination
        $purchases = $purchase_model->get_purchases($vendor_id, $business_id, 'order');

        $currency = (isset($settings['currency_symbol'])) ? $this->settings['currency_symbol'] : '₹';

        $i = 0;
        $status = "<span class='badge badge-success'> payment status </span>";
        foreach ($purchases['rows'] as $purchase) {
            // Set payment status badges
            if ($purchase['payment_status'] == "fully_paid") {
                $status = "<span class='badge badge-success'>Fully Paid</span>";
            } elseif ($purchase['payment_status'] == "partially_paid") {
                $status = "<span class='badge badge-primary'>Partially Paid</span>";
            } elseif ($purchase['payment_status'] == "unpaid") {
                $status = "<span class='badge badge-warning'>Unpaid</span>";
            } elseif ($purchase['payment_status'] == "cancelled") {
                $status = "<span class='badge badge-danger'>Cancelled</span>";
            }

            // Action buttons for editing/viewing
            $purchase_id = $purchase['id'];
            $edit = "<a href=" . base_url('admin/purchases/view_purchase') . "/" . $purchase_id . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='View'><i class='bi bi-eye'></i></a>";
            $edit .= " <a href='" . base_url("admin/purchases/invoice") . "/" . $purchase_id . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";

            // Populate rows for Bootstrap Table
            $rows[$i] = [
                'id' => $purchase['id'],
                'supplier_name' => ucwords($purchase['first_name'] . " " . $purchase['last_name']),
                'purchase_date' => date_formats(strtotime($purchase['purchase_date'])),
                'payment_status' => $status,
                'amount_paid' => currency_location(number_format($purchase['amount_paid'], 2)),
                'total' => currency_location(number_format($purchase['total'], 2)),
                'action' => $edit,
            ];
            $i++;
        }

        // Return the result as JSON if rows are present
        if (isset($rows) && !empty($rows)) {
            $array['total'] = $purchases['total'];
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function view_purchase($purchase_id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'view_purchase';
            $data['title'] = "Purchase Order Details - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['status'] = isset($status) ? $status : "";
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['has_transactions'] = true;
            $purchase = get_purchase_items($purchase_id)[0];
            $supplier = fetch_details("suppliers", ['user_id' => $purchase['supplier_id']]);
            if (empty($supplier)) {
                // If no customer is found by "user_id", assume "customer_id" refers directly to the "customers" table "id"
                $supplier = fetch_details("suppliers", ['id' => $purchase['supplier_id']]);
                $supplier_id = $supplier[0]['id'];   // Update customer_id to the correct "customers" table ID
            } else {
                $supplier_id = $supplier[0]['id']; // Update customer_id to the correct "customers" table ID
            }



            if (!empty($purchase['payment_status'])  && $purchase['payment_status'] == "fully_paid" &&  $purchase['payment_status'] != "unpaid" && $purchase['payment_status'] != "partially_paid" && $purchase['payment_status'] != "cancelled") {
                if (!empty($purchase['payment_method']) && $purchase['payment_method'] != "cash" && $purchase['payment_method'] != "wallet") {
                    $db = \config\Database::connect();
                    $order_transaction_id = $db->table('customers_transactions')->select('*')->where(['order_id' => $purchase_id, 'supplier_id' => $supplier_id])->get()->getResultArray();
                    $order_transaction_id = (isset($order_transaction_id[0]) && !empty($order_transaction_id[0]['transaction_id'])) ? $order_transaction_id[0]['transaction_id'] : '';
                    $data['order']['order_transaction_id']  = $order_transaction_id;
                }
                $data['has_transactions'] = false;
            }

            // echo "<pre>";
            // print_r($purchase);
            // echo "<br>" ;
            // print_r($data);
            // die();
            $data['order'] = $purchase;
            $data['order']['supplier_name'] = get_supplier($supplier[0]['user_id']);
            $data['items'] = $purchase['items'];


            return view("admin/template", $data);
        }
    }

    public function update_status_bulk()
    {

        $status = $_POST['status'];
        $item_id = $_POST['item_ids'];
        $msg = "";
        for ($i = 0; $i < count($item_id); $i++) {
            update_details(['status' => $status], ['id' => $item_id[$i]], 'purchases_items');
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
        if ($this->request->getGet('status')) {
            $rules['status'] = 'required';
        }
        if ($this->request->getGet('order_id')) {
            $rules['order_id'] = 'numeric';
        }


        $this->validation->setRules($rules);
        if (!$this->validation->run($_GET)) {
            $errors = $this->validation->getErrors();
            $response = [
                'error' => true,
                'message' => $errors,
                'data' => []
            ];
            return $this->response->setJSON($response);
        } else {
            update_details(['status' => $status], ['id' => $order_id], 'purchases_items');
            $response = [
                'error' => false,
                'message' => "Order status updated successfully!",
            ];
            return $this->response->setJSON($response);
        }
    }

    public function invoice($purchase_id)

    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "purchase_invoice";

            $data['title'] = "Invoice - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $purchase_model = new Purchases_model();
            $tax_model = new Tax_model();
            $order = $purchase_model->get_purchase_invoice($purchase_id, $business_id);
            $subTotal = 0;
            foreach ($order as $order_item) {
                $subTotal += floatval($order_item['quantity'] * $order_item['price']) - floatval($order_item['discount']);
            }
            if (isset($order) && !empty($order)) {
                $data['order'] = $order[0];
                if (gettype(value: json_decode($order[0]['tax_ids']))  != 'array') {
                    $tax = $tax_model->find($order[0]['tax_ids']);
                    $taxes = [];
                    if (! empty($tax)) {
                        $taxes[] = [
                            'id' => $tax['id'],
                            'name' => $tax['name'],
                            'percentage' => $tax['percentage'],
                        ];
                    }
                    $data['tax'] = $taxes;
                } else {
                    $taxes = [];
                    foreach (json_decode($order[0]['tax_ids']) as $tax_id) {
                        $tax = $tax_model->find($tax_id);
                        $taxes[] = [
                            'id' => $tax['id'],
                            'name' => $tax['name'],
                            'percentage' => $tax['percentage'],
                        ];
                    }
                    $data['tax'] = $taxes;
                }
                $data['sub_total'] = $subTotal;
            } else {
                $order = [];
            }
            
            return view("admin/template", $data);
        }
    }

    public function invoice_table($purchase_id)
    {
        $orders_model = new Purchases_model();
        $settings = get_settings('general', true);

        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $orders = $orders_model->get_purchase_invoice($purchase_id, $business_id);
        $total =  count($orders);
        $order_total = 0.00;
        if (!empty($orders)) {
            $i = 0;
            foreach ($orders as $order) {


                $sub_total = floatval($order['quantity'] * $order['price']) - floatval($order['discount']);
                $rows[$i] = [
                    'name' =>  ucwords($order['product_name'] . "/" . $order['variant_name']),
                    'price' => currency_location(number_format($order['price'], 2)),
                    'quantity' => ucwords($order['quantity']),
                    'discount' => ucwords($order['discount']),
                    'subtotal' => currency_location(number_format($sub_total, 2))
                ];

                $i++;
                $order_total += $sub_total;
            }
            $row = [
                'name' => "",
                'price' => "",
                'quantity' => "",
                'discount' => "<strong>Total</strong>",
                'subtotal' => "<strong>" . currency_location(number_format($order_total, 2)) . "</strong>",
            ];

            array_push($rows, $row);
            $array['total'] = $total;
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function purchase_return()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (! isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->findAll();
                if (empty($allbusiness)) {
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('admin/businesses');
                } else {
                    session()->setFlashdata('message', 'Please select a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('admin/businesses');
                }
            }

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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'purchases_return';
            $data['title'] = "Purchase Return List - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $status_model = new Status_model();
            $status = $status_model->get_status($business_id);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['status'] = isset($status) ? $status : "";
            $tax_model = new Tax_model();
            $data['taxes'] = $tax_model->findAll();
            $data['order_type'] = 'return';
            $orders = fetch_details('purchases', ['business_id' => $business_id]);

            if (isset($orders) && !empty($orders)) {
                foreach ($orders as $order) {
                    if (floatval($order['amount_paid']) == floatval($order['total'])) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) < floatval($order['total'])) {
                        update_details(['payment_status' => 'partially_paid'], ['id' => $order['id']], "purchases");
                    }
                    if (floatval($order['amount_paid']) == 0.00) {
                        update_details(['payment_status' => 'unpaid'], ['id' => $order['id']], "purchases");
                    }
                }
            }

            return view("admin/template", $data);
        }
    }

    public function purchase_return_table()
    {
        $purchase_model = new Purchases_model();
        $vendor_id = $_SESSION['user_id'];
        $business_id = $_SESSION['business_id'];
        $purchases =  $purchase_model->get_purchases($vendor_id,  $business_id, 'return');
        $i = 0;
        $currency = (isset($settings['currency_symbol'])) ? $this->settings['currency_symbol'] : '₹';
        foreach ($purchases['rows'] as $purchase) {

            if ($purchase['payment_status'] == "fully_paid") {
                $status = "<span class='badge badge-success'>Fully Paid</span>";
            }
            if ($purchase['payment_status'] == "partially_paid") {
                $status = "<span class='badge badge-primary'>Partially Paid</span>";
            }
            if ($purchase['payment_status'] == "unpaid") {
                $status = "<span class='badge badge-info'>Unpaid</span>";
            }
            if ($purchase['payment_status'] == "cancelled") {
                $status = "<span class='badge badge-danger'>Cancelled</span>";
            }

            $order_status =  "<span class='badge badge-custom'>" . status_name($purchase['status']) . "</span>";
            $supplier_name = get_supplier($purchase['supplier_id']);
            $purchase_id = $purchase['id'];
            $edit = "<a href=" . site_url('admin/purchases/view_purchase') . "/" . $purchase_id . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='View   '><i class='bi bi-eye'></i></a>" . " ";
            $edit .= " <a href='" . base_url("admin/purchases/invoice") . "/" . $purchase_id . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";

            $purchase_status = status_name($purchase['status']);

            $rows[$i] = [
                'id' => $purchase['id'],
                'supplier_name' => ucwords($supplier_name),
                'purchase_date' => date_formats(strtotime($purchase['purchase_date'])),
                'purchase_status' => ucwords($purchase_status),
                'payment_status' => $status,
                'amount_paid' => currency_location(number_format($purchase['amount_paid'], 2)),
                'total' => currency_location(number_format($purchase['total'], 2)),
                'status' => $order_status,
                'action' => $edit,
            ];
            $i++;
        }
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
            $array['total'] = count($purchases);
            echo json_encode($array);
        }
    }
}
