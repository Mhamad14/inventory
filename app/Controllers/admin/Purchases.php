<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Products_variants_model;
use App\Models\Purchases_items_model;
use App\Models\Purchases_model;
use App\Models\Status_model;
use App\Models\Suppliers_model;
use App\Models\Tax_model;
use App\Models\warehouse_batches_model;
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
    protected $business_id;

    protected $status_model;
    protected $warehouse_model;
    protected $tax_model;
    protected $Purchases_items_model;
    protected $purchase_model;
    protected $warehouse_batches_model;
    protected $products_variants_model;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'purchase', 'common']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
        $this->business_id = session('business_id') ?? "";
        $this->status_model = new Status_model();
        $this->warehouse_model = new WarehouseModel();
        $this->tax_model = new Tax_model();
        $this->Purchases_items_model = new Purchases_items_model();
        $this->purchase_model = new Purchases_model();
        $this->warehouse_batches_model = new warehouse_batches_model();
        $this->products_variants_model = new Products_variants_model();
    }
    public function index()
    {
        $data = getData('purchases', fetch_details('purchases', ['business_id' => $this->business_id]), VIEWS . 'purchases_table');
        $data['order_type'] = 'order';
        return view("admin/template", $data);
    }

    public function return_purchase_orders($purchase_id = '')
    {
        $data = getdata(
            'status',
            $this->status_model->get_status($this->business_id),
            FORMS . 'Purchases/return',
            'warehouses',
            $this->warehouse_model->where('business_id', $this->business_id)->get()->getResultArray(),
            'purchase',
            $this->purchase_model->getPurchase($purchase_id)
        );
        $data['order_type'] = 'return';
        $data['purchase_id'] = $purchase_id;
        session('purchase_id')->set($purchase_id);
        $batches = $this->warehouse_batches_model->getBatches($purchase_id);

        return view("admin/template", $data);
    }
    public function purchase_orders($type = '')
    {
        $data = getdata(
            'status',
            $this->status_model->get_status($this->business_id),
            FORMS . 'purchases',
            'warehouses',
            $this->warehouse_model->where('business_id', $this->business_id)->get()->getResultArray()
        );
        $data['order_type'] = $type;

        return view("admin/template", $data);
    }

    public function save()
    {
        $products = json_decode($this->request->getPost('products'));
        $qtys = $this->request->getVar('qty');
        $discounts = $this->request->getVar('discount');
        $expiry_dates = $this->request->getVar('expire');
        $sell_prices = $this->request->getVar('sell_price');
        $prices = $this->request->getVar('price');
        $order_type = $this->request->getVar('order_type');


        // validation
        $rules =  getPurchaseValidationRules($this->request);
        $this->validation->setRules($rules);
        if (!$this->validation->withRequest($this->request)->run()) {
            $errors = $this->validation->getErrors();
            return $this->response->setJSON(csrfResponseData([
                'success' => false,
                'message' => $errors,
                'data' => []
            ]));
        }
        if ($this->ionAuth->isTeamMember()) {
            if (! userHasPermission('purchases', 'can_update',  session('user_id'))) {
                session()->setFlashdata("permission_error", "You do not have permission to access");
                session()->setFlashdata("type", "error");
                return json_encode([
                    'total' => 0,
                    'rows' => [],
                ]);
            }
        }

        $warehouse_id = $this->request->getVar('warehouse_id');
        $payment_status = $this->request->getVar('payment_status');
        $amount_paid = $payment_status === 'fully_paid'
            ? $this->request->getVar('total')
            : ($payment_status === 'partially_paid' ? ($this->request->getVar('amount_paid') ?? 0) : 0);

        $purchase =  array(
            'vendor_id' => getUserId(),
            'business_id' => $this->business_id,
            'warehouse_id' => $warehouse_id,
            'order_type' => $this->request->getVar('order_type') ?? "",
            'purchase_date' => $this->request->getVar('purchase_date') ?? "",
            'supplier_id' => $this->request->getVar('supplier_id') ?? "",
            'discount' => $this->request->getVar('order_discount') ?? 0,
            'delivery_charges' => $this->request->getVar('shipping') ?? 0,
            'payment_status' => $payment_status,
            'amount_paid' => $amount_paid ?? 0,
            'total' => $this->request->getVar('total'),
            'status' => $this->request->getVar('status'),
            'message' => $this->request->getVar('message'),
        );
        $this->purchase_model->save($purchase);

        $purchase_id = $this->purchase_model->getInsertID();
        // add products variants to purchase items
        foreach ($products as $product) {
            $id = $product->id;
            $product->qty = $qtys[$id];
            $product->price = $prices[$id];
            $product->discount = $discounts[$id];
            $product->sell_price = $sell_prices[$id];
            $product->expire = $expiry_dates[$id];
        }
        foreach ($products as $item) {
            // save purchase
            $this->Purchases_items_model->savePurchaseItem($purchase_id, $this->request->getVar('status'), $item);
            $purchase_items_id = $this->Purchases_items_model->getInsertID();

            if ($order_type == "order") {
                // save a batch
                $this->warehouse_batches_model->saveBatch($purchase_items_id, $warehouse_id, $item, 'order');
                $this->products_variants_model->upsert_warehouse_stock($warehouse_id, $item->id, $item->qty);
                // update_stock(product_variant_ids: $item->id, qtns: $item->qty, type: 'plus');
                // updateWarehouseStocks(warehouse_id: $warehouse_id,  product_variant_id: $item->id,  warehouse_stock: $item->qty, type: 1);
            } elseif ($order_type == "return") {
                $this->warehouse_batches_model->saveBatch($purchase_items_id, $warehouse_id, $item, 'return');
                // update_stock(product_variant_ids: $item->id, qtns: $item->qty);
                // updateWarehouseStocks(warehouse_id: $warehouse_id,  product_variant_id: $item->id,  warehouse_stock: $item->qty, type: 0);
            }
        }

        return $this->response->setJSON(csrfResponseData([
            'success' => true,
            'message' => 'Purchase Order saved successfully',
            'data' => []
        ]));
    }

    public function get_suppliers()
    {
        $suppliers_model = new Suppliers_model();
        $search = $this->request->getGet('search');

        // Validate input
        if (empty($search) || strlen($search) < 2) {
            return $this->response->setJSON(['data' => []]);
        }

        try {
            // Get and decode the response
            $response = $suppliers_model->search_suppliers($search);
            $suppliers = json_decode($response);

            // Process results
            $results = [];
            if (!empty($suppliers->data)) {
                foreach ($suppliers->data as $supplier) {
                    if ($supplier->status) { // Only active suppliers
                        $results[] = [
                            "id"      => $supplier->id,
                            "text"    => $supplier->name ?? $supplier->text, // Fallback to 'name' if 'text' doesn't exist
                            "balance" => $supplier->balance,
                            "status"  => $supplier->status,
                        ];
                    }
                }
            }

            return $this->response->setJSON(['data' => $results]);
        } catch (\Exception $e) {
            log_message('error', 'Supplier search failed: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON(['error' => 'Search failed']);
        }
    }
    public function purchase_table()
    {
        $purchase_model = new Purchases_model();
        $id = getUserId();

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
            $edit .= " <a href='" . base_url("admin/batches/return") . "/" . $purchase_id . "' class='btn btn-info btn-sm' data-toggle='tooltip' data-placement='bottom' title='Return'><i class='bi bi-box-arrow-down'></i></a>";

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

    public function purchase_return_table()
    {
        $vendor_id = $_SESSION['user_id'];
        $business_id = $_SESSION['business_id'];
        $purchases =  $this->purchase_model->get_purchases($vendor_id,  $business_id, 'return');
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
