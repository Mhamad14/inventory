<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\BrandModel;
use App\Models\Categories_model;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;
use App\Models\Delivery_boys_model;
use App\Models\Orders_items_model;
use App\Models\Orders_model;
use App\Models\OrderReturnsModel;
use App\Models\Orders_services_model;
use App\Models\Status_model;
use App\Models\OrdersModel;
use App\Models\Subscription_model;
use App\Models\Tax_model;
use App\Models\WarehouseModel;
use App\Models\WarehouseProductStockModel;
use App\Models\Products_model;
use App\Models\Products_variants_model;

class Orders extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $data;
    protected $validationListTemplate = 'list';
    protected $ionAuthModel;

    protected $business_id;
    protected OrdersModel $ordersModel;
    protected Orders_items_model $ordersItemsModel;


    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'order']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
        $this->business_id = session('business_id') ?? "";
        $this->ordersModel = new OrdersModel();
        $this->ordersItemsModel = new Orders_items_model();
    }

    public function index()
    {
        $data = $this->setViewData(FORMS . "orders", "Create Order");
        return view("admin/template", $data);
    }

    public function view_orders($order_id = "")
    {
        $data = $this->getData('order', $this->ordersModel->getOrderDetails($order_id), FORMS . "orders/show");
        $data['status_list'] = fetch_details("status",[],['id','status']); // get status list for order item update
        $data['warehouses'] = fetch_details("warehouses",[],['id','name']); // get status list for order item update
        session()->set('order_id', $data['order']['id']);

        return view("admin/template", $data);
    }

    private function getData($tableName, $tableData, $page)
    {
        $settings = get_settings('general', true);
        $languages = getLanguages();
        return [
            'version' => getAppVersion(),
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => $languages,
            'business_id' => $this->business_id,
            'page' => $page,
            'title' => "Orders - " . $settings['title'] ?? "",
            'from_title' => 'Customer Details',
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems",
            $tableName => $tableData,
            'user' => $this->ionAuth->user(session('user_id'))->row(),
        ];
    }

    public function orders_items_table()
    {
        $rows = $this->ordersItemsModel->getOrderItemsWithDetails(session('order_id'));
        return $this->response->setJSON(['rows' => array_map('prepareOrdersItemsRow', $rows)]);
    }

    protected function getOrderItems($order_id)
    {
        $orders_items = fetch_details("orders_items", ["order_id" => $order_id]);

        if (!empty($orders_items)) {
            foreach ($orders_items as $key => $item) {
                $product = fetch_details("products", ['id' => $item['product_id']]);
                $orders_items[$key]['image'] = $product[0]['image'] ?? "";

                if (!empty($item['delivery_boy'])) {
                    $delivery_boy = fetch_details('users', ['id' => $item['delivery_boy']]);
                    $orders_items[$key]['delivery_boy_name'] = $delivery_boy[0]['first_name'] ?? "";
                }

                $status = fetch_details("status", ['id' => $item['status']]);
                $orders_items[$key]['status_name'] = $status[0]['status'] ?? null;
            }
        }

        return $orders_items ?? "";
    }

    public function process_return()
    {
        $method = strtoupper($this->request->getMethod());
        log_message('debug', 'Framework method: ' . $method);

        if ($method !== 'POST') {
            log_message('error', 'Rejected method: ' . $method);
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Only POST requests are accepted. Received: ' . $method,
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        }

        try {
            $db = \Config\Database::connect();
            $db->query('SELECT 1');
            log_message('debug', 'Database connection successful');
        } catch (\Exception $e) {
            log_message('error', 'Database connection failed: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => 'Database connection error',
                'details' => $e->getMessage()
            ]);
        }

        $db->transStart();

        try {
            $orderReturnsModel = new OrderReturnsModel();
            $ordersModel = new OrdersModel();
            $orderItemsModel = new Orders_items_model();
            $productsModel = new Products_model();
            $variantsModel = new Products_variants_model();
            $warehouseStockModel = new WarehouseProductStockModel();

            $order_id = $this->request->getPost('order_id');
            $return_reason = $this->request->getPost('return_reason');
            $return_quantities = $this->request->getPost('return_quantity') ?? [];

            $total_return_amount = 0;
            $return_items = [];

            $order = $ordersModel->find($order_id);
            if (!$order) {
                throw new \RuntimeException("Order not found");
            }
            $warehouse_id = $order['warehouse_id'] ?? null;

            foreach ($return_quantities as $item_id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity <= 0) {
                    continue;
                }

                $item = $orderItemsModel->find($item_id);
                if (!$item || $item['order_id'] != $order_id) {
                    throw new \RuntimeException("Invalid item ID: $item_id");
                }

                $already_returned = $orderReturnsModel->getReturnedQuantity($item_id);
                $returnable_qty = $item['quantity'] - $already_returned;

                if ($quantity > $returnable_qty) {
                    throw new \RuntimeException("Quantity exceeds returnable amount for item ID $item_id");
                }

                $return_amount = $quantity * $item['price'];
                $total_return_amount += $return_amount;

                $return_items[] = [
                    'order_id' => $order_id,
                    'item_id' => $item_id,
                    'quantity' => $quantity,
                    'price' => $item['price'],
                    'total' => $return_amount,
                    'return_date' => date('Y-m-d H:i:s'),
                    'return_reason' => $return_reason,
                    'status' => 'processed',
                    'processed_by' => $this->ionAuth->getUserId(),
                    'business_id' => $_SESSION['business_id'] ?? null
                ];

                $product = $productsModel->find($item['product_id']);
                if (!$product) {
                    throw new \RuntimeException("Product not found for item ID $item_id");
                }

                if ($product['stock_management'] == 1) {
                    $new_stock = $product['stock'] + $quantity;
                    $productsModel->update($item['product_id'], ['stock' => $new_stock]);

                    if ($warehouse_id) {
                        $this->updateWarehouseStock(
                            $warehouseStockModel,
                            $warehouse_id,
                            $item['product_id'],
                            null,
                            $quantity,
                            $product['business_id'] ?? null,
                            $product['vendor_id'] ?? null
                        );
                    }
                } elseif ($product['stock_management'] == 2 && !empty($item['product_variant_id'])) {
                    $variant = $variantsModel->find($item['product_variant_id']);
                    if ($variant) {
                        $new_stock = $variant['stock'] + $quantity;
                        $variantsModel->update($item['product_variant_id'], ['stock' => $new_stock]);

                        if ($warehouse_id) {
                            $this->updateWarehouseStock(
                                $warehouseStockModel,
                                $warehouse_id,
                                $item['product_id'],
                                $item['product_variant_id'],
                                $quantity,
                                $product['business_id'] ?? null,
                                $product['vendor_id'] ?? null
                            );
                        }
                    }
                } else {
                    throw new \RuntimeException("Invalid stock management configuration for product ID {$item['product_id']}");
                }

                $new_returned_qty = $already_returned + $quantity;
                $orderItemsModel->update($item_id, ['returned_quantity' => $new_returned_qty]);
            }

            if (empty($return_items)) {
                throw new \RuntimeException('No valid items to process');
            }

            if (!$orderReturnsModel->insertBatch($return_items)) {
                $error = $db->error();
                log_message('error', 'Failed to insert return records: ' . print_r($error, true));
                throw new \RuntimeException('Failed to insert return records');
            }

            if (!$ordersModel->updateOrderTotals($order_id, $total_return_amount)) {
                $error = $db->error();
                log_message('error', 'Failed to update order totals: ' . print_r($error, true));
                throw new \RuntimeException('Failed to update order totals');
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaction failed');
            }

            return $this->response->setJSON([
                'error' => false,
                'message' => 'Return processed successfully',
                'return_amount' => $total_return_amount,
                'returned_items_count' => count($return_items),
                'inserted_ids' => $orderReturnsModel->getInsertID(),
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Return processing error: ' . $e->getMessage());
            return $this->response->setJSON([
                'error' => true,
                'message' => $e->getMessage(),
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash()
            ]);
        }
    }

    protected function updateWarehouseStock($warehouseStockModel, $warehouse_id, $product_id, $variant_id, $quantity, $business_id, $vendor_id)
    {
        $currentDateTime = date('Y-m-d H:i:s');
        $where = [
            'warehouse_id' => $warehouse_id,
            'product_id' => $product_id,
            'product_variant_id' => $variant_id
        ];

        $warehouse_stock = $warehouseStockModel->where($where)->first();

        $data = [
            'warehouse_id' => $warehouse_id,
            'product_id' => $product_id,
            'product_variant_id' => $variant_id,
            'stock' => ($warehouse_stock['stock'] ?? 0) + $quantity,
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'updated_at' => $currentDateTime
        ];

        if ($warehouse_stock) {
            $data['id'] = $warehouse_stock['id'];
        } else {
            $data['created_at'] = $currentDateTime;
            $data['qty_alert'] = 0;
        }

        $warehouseStockModel->save($data);
    }

    public function sales_order()
    {
        

        $data = $this->setViewData(FORMS . "create_orders", "Create Order");
        $warehouse_model = new WarehouseModel();
        $data['warehouses'] = $warehouse_model->where('business_id', $data['business_id'])->get()->getResultArray();

        return view("admin/template", $data);
    }

    public function orders()
    {

        $data = $this->setViewData(VIEWS . "orders_list", 'Orders List');

        $orders = fetch_details('orders', ['business_id' => $this->business_id]);
        if (!empty($orders)) {
            foreach ($orders as $order) {
                $payment_status = $this->determinePaymentStatus($order['amount_paid'], $order['final_total']);
                update_details(['payment_status' => $payment_status], ['id' => $order['id']], "orders");
            }
        }
        return view("admin/template", $data);
    }

    protected function determinePaymentStatus($amount_paid, $final_total)
    {
        if (floatval($amount_paid) == floatval($final_total)) {
            return 'fully_paid';
        }
        if (floatval($amount_paid) < floatval($final_total)) {
            return 'partially_paid';
        }
        if (floatval($amount_paid) == 0.00) {
            return 'unpaid';
        }
        return 'unpaid';
    }

    public function orders_table()
    {
        $orders_model = new Orders_model();
        $orders = $orders_model->get_delivery_boy_orders_list($this->business_id);
        $total = $orders_model->count_of_orders($this->business_id);

        $rows = [];
        foreach ($orders as $order) {
            $order_id = $order['id'];
            $delivery_boy_name = $this->getDeliveryBoyName($order['order_type'], $order_id);

            $customer_id = $order['customer_id'];
            $customer_model = new Customers_model();
            $customer_array = $customer_model->where('user_id', $customer_id)->get()->getResultArray();
            if (empty($customer_array)) {
                $customer_array = $customer_model->where('id', $customer_id)->get()->getResultArray();
            }

            $user_id = $customer_array[0]['user_id'];
            $balance = $this->getCustomerBalance($customer_model, $user_id);
            $customer_name = $this->ionAuth->user($user_id)->row()->first_name;

            $status = $this->getPaymentStatusBadge($order['payment_status']);
            $view_order = $this->getOrderActionButtons($order_id);

            $rows[] = [
                'id' => $order['id'],
                'order_type' => ucwords($order['order_type']),
                'created_at' => date_formats(strtotime($order['created_at'])),
                'vendor_id' => $order['vendor_id'],
                'first_name' => ucwords($customer_name),
                'business_id' => $order['business_id'],
                'total' => currency_location(decimal_points($order['total'])),
                'balance' => currency_location(decimal_points($balance)),
                'delivery_charges' => currency_location(decimal_points($order['delivery_charges'])),
                'discount' => currency_location(decimal_points($order['discount'])),
                'final_total' => currency_location(decimal_points($order['final_total'])),
                'payment_status' => $status,
                'message' => $order['message'],
                'payment_method' => str_replace("_", " ", $order['payment_method']),
                'action' => $view_order
            ];
        }

        $array = [
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ];

        if (count($array['rows']) < 1) {
            $array['total'] = 0;
        }

        echo json_encode($array);
    }

    protected function getDeliveryBoyName($order_type, $order_id)
    {
        $table = $order_type == "service" ? 'orders_services' : 'orders_items';
        $item = fetch_details($table, ['order_id' => $order_id]);

        if (!empty($item) && !empty($item[0]['delivery_boy'])) {
            $delivery_boy_details = $this->ionAuth->user($item[0]['delivery_boy'])->row();
            return $delivery_boy_details->first_name ?? "";
        }
        return "";
    }

    protected function getCustomerBalance($customer_model, $user_id)
    {
        $customer_wallet = $customer_model->get_customer($user_id);
        return $customer_wallet[0]['balance'] ?? 0.00;
    }

    protected function getPaymentStatusBadge($status)
    {
        switch ($status) {
            case 'fully_paid':
                return "<span class='badge badge-success'>Fully Paid</span>";
            case 'partially_paid':
                return "<span class='badge badge-primary'>Partially Paid</span>";
            case 'unpaid':
                return "<span class='badge badge-warning'>Unpaid</span>";
            case 'cancelled':
                return "<span class='badge badge-danger'>Cancelled</span>";
            default:
                return "<span class='badge badge-secondary'>Unknown</span>";
        }
    }

    protected function getOrderActionButtons($order_id)
    {
        $buttons = "<a href='" . base_url("admin/orders/view_orders") . "/" . $order_id . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View Orders'><i class='bi bi-eye'></i></a>";
        $buttons .= " <a href='" . base_url("admin/invoices/invoice") . "/" . $order_id . "' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice'><i class='bi bi-receipt-cutoff'></i></a>";
        $buttons .= " <a href='" . base_url("admin/invoices/view_invoice") . "/" . $order_id . "' class='btn btn-danger btn-sm' target='_blank' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Invoice PDF'><i class='bi bi-file-earmark-pdf'></i></a>";
        return $buttons;
    }

    function setViewData($page, $title)
    {
        $settings = get_settings('general', true);
        return [
            'version' => getAppVersion() ?? '2.0',

            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => fetch_details('languages', [], [], null, '0', 'id', 'ASC'),
            'business_id' => $this->business_id,
            'currency' => $settings['currency_symbol'] ?? '$',
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'page' => $page,
            'company_title' => $title,
            'title' => $title . " - " . $settings['title'] ?? "",
            'user_id' => getUserId(),
            'brands' => (new BrandModel())->findAll(),
            'categories' => getActiveCategories(getUserId(), $this->business_id),
            'status' => (new Status_model())->get_status($this->business_id) ?? "",
            'customers' => getCustomers($this->business_id),

            'user' => $this->ionAuth->user(getUserId())->row(),
            'users' => $this->ionAuth->users()->result(),

        ];
    }

    protected function getOrderServices($order_id)
    {
        $orders_services = fetch_details("orders_services", ["order_id" => $order_id]);

        if (!empty($orders_services)) {
            foreach ($orders_services as $key => $service) {
                $services = fetch_details("services", ['id' => $service['service_id']]);
                $orders_services[$key]['image'] = $services[0]['image'] ?? "";

                if (!empty($service['delivery_boy'])) {
                    $delivery_boy = fetch_details('users', ['id' => $service['delivery_boy']]);
                    $orders_services[$key]['delivery_boy_name'] = $delivery_boy[0]['first_name'] ?? "";
                }

                $status = fetch_details("status", ['id' => $service['status']]);
                $orders_services[$key]['status_name'] = $status[0]['status'] ?? null;
            }
        }

        return $orders_services ?? "";
    }

    public function save_order()
    {
        

        if (!isset($_POST['data']) || empty($_POST['data'])) {
            return $this->jsonErrorResponse('Please add order item');
        }

        if (!isset($_POST['customer_id']) || empty($_POST['customer_id'])) {
            return $this->jsonErrorResponse("Please select the customer!");
        }

        $rules = [
            'total' => 'required|trim',
            'final_total' => 'required|trim',
            'payment_status' => 'required|trim',
            'amount_paid' => 'trim',
            'status' => 'required|trim',
        ];

        $payment_method = $this->request->getVar('payment_method');
        if ($this->request->getPost('payment_status') != "unpaid" && $this->request->getPost('payment_status') != "cancelled") {
            $payment_method = $payment_method;
            if (isset($_POST['payment_method']) && $_POST['payment_method'] != 'cash' && $_POST['payment_method'] != 'wallet') {
                $rules['transaction_id'] = 'trim|required';
            }
            if (isset($_POST['payment_method']) && $_POST['payment_method'] == 'other') {
                $rules['payment_method_name'] = 'trim|required';
            }
        } else {
            $payment_method = null;
        }

        if ($this->request->getPost('quantity')) {
            $rules['quantity'] = 'trim|numeric|greater_than_equal_to[0]';
        }

        $this->validation->setRules($rules);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $vendor_id = $this->ionAuth->getUserId();
        $payment_type = $payment_method;
        $payment_status = $this->request->getVar('payment_status');
        $final_total = floatval($this->request->getVar('final_total'));
        $amount_paid = $this->request->getVar('amount_paid');

        $customer = $this->getCustomerDetailsByUserId($_POST['customer_id']);
        $customer_wallet_balance = !empty($customer) ? floatval($customer['balance']) : "0";
        $customer_id = $customer['id'];

        if ($payment_type == "wallet") {
            if ($payment_status == "fully_paid" && $customer_wallet_balance < $final_total) {
                return $this->jsonErrorResponse("customer don't have sufficient wallet balance,Please recharge wallet!");
            }
            if ($payment_status == "partially_paid" && $customer_wallet_balance < $amount_paid) {
                return $this->jsonErrorResponse("customer don't have sufficient wallet balance,Please recharge wallet!");
            }
        }

        $order_items = json_decode($_POST['data']);
        $product_variant_id = array_column($order_items, "product_variant_id");
        $quantity = array_column($order_items, "quantity");
        $stock_status = $this->validateStock($product_variant_id, $quantity);

        if ($stock_status['error'] == true) {
            return $this->jsonErrorResponse($stock_status['message']);
        }

        $order_data = $this->prepareOrderData($vendor_id, $this->business_id, $customer_id, $payment_type, $payment_status, $final_total, $amount_paid);
        $order_model = new Orders_model();
        $order_model->save($order_data);
        $order_id = $order_model->getInsertID();

        $this->processPayment($payment_type, $payment_status, $order_id, $customer_id, $vendor_id, $final_total, $amount_paid, $customer_wallet_balance);

        $tax_model = new Tax_model();
        $orders_items_model = new Orders_items_model();
        $orders_services_model = new Orders_services_model();
        $warehouse_product_stock_model = new WarehouseProductStockModel();

        foreach ($order_items as $item) {
            $tax_details = $this->prepareTaxDetails($tax_model, $item);
            $sub_total = (floatval($item->price)) * (floatval($item->quantity));

            if (isset($item->product_id) && !empty($item->product_id)) {
                $this->saveOrderItem($orders_items_model, $item, $order_id, $tax_details, $sub_total);
                $this->updateStockAndWarehouse($item, $warehouse_product_stock_model);
            }

            if (isset($item->service_id) && !empty($item->service_id)) {
                $this->saveServiceOrder($orders_services_model, $item, $order_id, $tax_details, $sub_total);
            }

            if (isset($item->is_recursive) && $item->is_recursive == "1") {
                $this->saveSubscription($item, $vendor_id, $this->business_id, $customer_id);
            }
        }

        return $this->jsonSuccessResponse('order placed successfully', ['order_id' => $order_id]);
    }

    protected function getCustomerDetailsByUserId($user_id)
    {
        $customer = fetch_details('customers', ['user_id' => $user_id]);
        return $customer[0] ?? [];
    }

    protected function validateStock($product_variant_id, $quantity)
    {
        return validate_stock($product_variant_id, $quantity);
    }

    protected function prepareOrderData($vendor_id, $business_id, $customer_id, $payment_type, $payment_status, $final_total, $amount_paid)
    {
        return [
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'customer_id' => $customer_id,
            'warehouse_id' => null,
            'created_by' => $_SESSION['user_id'],
            'final_total' => $this->request->getVar('final_total'),
            'total' => $this->request->getVar('total'),
            'delivery_charges' => $this->request->getVar('delivery_charges'),
            'discount' => $this->request->getVar('discount'),
            'payment_status' => $payment_status,
            'payment_method' => $payment_type,
            'order_type' => $this->request->getVar('order_type'),
            'amount_paid' => $amount_paid,
            'message' => $this->request->getVar('message'),
            'is_pos_order' => 1
        ];
    }

    protected function processPayment($payment_type, $payment_status, $order_id, $customer_id, $vendor_id, $final_total, $amount_paid, $customer_wallet_balance)
    {
        if (empty($payment_type)) return;

        $customers_transactions_model = new Customers_transactions_model();
        $transaction_data = [
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'vendor_id' => $vendor_id,
            'created_by' => $vendor_id,
            'payment_type' => $payment_type,
            'transaction_id' => $this->request->getVar('transaction_id'),
        ];

        if ($payment_type == "wallet") {
            $transaction_data['amount'] = ($payment_status == "fully_paid") ? $final_total : $amount_paid;
            $customers_transactions_model->save($transaction_data);

            $balance = $customer_wallet_balance - (($payment_status == "fully_paid") ? $final_total : $amount_paid);
            $db = \Config\Database::connect();
            $db->table('customers')->where(['id' => $customer_id])->update(['balance' => $balance]);
        } elseif ($payment_type == "cash" && $payment_status == "partially_paid") {
            $transaction_data['amount'] = $amount_paid;
            $customers_transactions_model->save($transaction_data);
        } elseif ($payment_type != "cash" && $payment_type != "wallet") {
            $transaction_data['amount'] = $amount_paid;
            $customers_transactions_model->save($transaction_data);
        }
    }

    protected function prepareTaxDetails($tax_model, $item)
    {
        $tax_ids = (isset($item->tax_id) && $item->tax_id != 0) ? (array) $item->tax_id : [];
        $tax_details = [];

        foreach ($tax_ids as $tax_id) {
            $tax = $tax_model->find($tax_id);
            $tax_details[] = [
                'tax_id' => $tax_id,
                'name' => $tax['name'],
                'percentage' => $tax['percentage']
            ];
        }

        return empty($tax_details) ? "[]" : json_encode($tax_details);
    }

    protected function saveOrderItem($orders_items_model, $item, $order_id, $tax_details, $sub_total)
    {
        $orders_items = [
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
        ];

        $orders_items_model->save($orders_items);
    }

    protected function updateStockAndWarehouse($item, $warehouse_product_stock_model)
    {
        update_stock($item->product_variant_id, $item->quantity);

        $warehouse_stock = $warehouse_product_stock_model->where('product_variant_id', $item->product_variant_id)->get()->getResultArray();
        $warehouse_item_max_stock = $warehouse_stock[0];
        $max_stock = $warehouse_stock[0]['stock'];

        foreach ($warehouse_stock as $warehouse_stock_item) {
            if ($max_stock < $warehouse_stock_item['stock']) {
                $max_stock = $warehouse_stock_item['stock'];
                $warehouse_item_max_stock = $warehouse_stock_item;
            }
        }

        updateWarehouseStocks($warehouse_item_max_stock['warehouse_id'], $warehouse_item_max_stock['product_variant_id'], $item->quantity, 0);
    }

    protected function saveServiceOrder($orders_services_model, $item, $order_id, $tax_details, $sub_total)
    {
        $recurring_days = find_days($item->starts_on, $item->ends_on);
        $orders_items = [
            'order_id' => $order_id,
            'service_id' => $item->service_id,
            'service_name' => $item->service_name,
            'price' => $item->price,
            'quantity' => $item->quantity,
            'unit_name' => $item->unit_name,
            'unit_id' => $item->unit_id,
            'tax_percentage' => '',
            'is_tax_included' => $item->is_tax_included,
            'tax_name' => '',
            'tax_details' => $tax_details,
            'is_recursive' => $item->is_recursive,
            'recurring_days' => $recurring_days,
            'starts_on' => $item->starts_on,
            'ends_on' => $item->ends_on,
            'sub_total' => $sub_total,
            'status' => $this->request->getVar('status'),
        ];
        $orders_services_model->save($orders_items);
    }

    protected function saveSubscription($item, $vendor_id, $business_id, $customer_id)
    {
        $subscription_model = new Subscription_model();
        $data = $subscription_model->if_exist($item->service_id, $customer_id);
        $sub_id = $data[0]['id'] ?? "";

        $subscription = [
            'id' => $sub_id,
            'service_id' => $item->service_id,
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'customer_id' => $customer_id,
            'created_by' => $vendor_id,
        ];
        $subscription_model->save($subscription);
    }

    protected function jsonErrorResponse($message, $data = [])
    {
        $response = [
            'error' => true,
            'message' => is_array($message) ? $message : [$message],
            'data' => $data
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }

    protected function jsonSuccessResponse($message, $data = [])
    {
        $response = [
            'error' => false,
            'message' => $message,
            'data' => $data
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }

    public function create_status()
    {
        if (!isset($_POST) || empty($_POST)) {
            return redirect()->back()->withInput();
        }

        $this->validation->setRules([
            'status' => 'required|trim',
            'operation' => 'required',
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $status_model = new Status_model();
        $status_model->save([
            'vendor_id' => $this->ionAuth->getUserId(),
            'business_id' => $this->business_id,
            'status' => $this->request->getVar('status'),
            'operation' => $this->request->getVar('operation')
        ]);

        return $this->jsonSuccessResponse('Status Created successfully');
    }

    public function set_delivery_boy()
    {
        $deliveryboy = $this->request->getGet('deliveryboy');
        $order_id = $this->request->getGet('order_id');
        $type = $this->request->getGet('type');

        $rules = [];
        if ($this->request->getGet('delivery_boy')) $rules['delivery_boy'] = 'trim';
        if ($this->request->getGet('order_id')) $rules['order_id'] = 'trim';
        if ($this->request->getGet('type')) $rules['type'] = 'trim';

        $this->validation->setRules($rules);
        if (!$this->validation->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $table = $type == "product" ? 'orders_items' : 'orders_services';
        update_details(['delivery_boy' => $deliveryboy], ['id' => $order_id], $table);

        return $this->jsonSuccessResponse("DeliveryBoy Assigned successfully!");
    }

    public function update_order_status()
    {
        $status = $this->request->getGet('status');
        $order_id = $this->request->getGet('order_id');
        $type = $this->request->getGet('type');

        $rules = [];
        if ($this->request->getGet('status')) $rules['status'] = 'required';
        if ($this->request->getGet('order_id')) $rules['order_id'] = 'numeric';
        if ($this->request->getGet('type')) $rules['type'] = 'required';

        $this->validation->setRules($rules);
        if (!$this->validation->run($_GET)) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $table = $type == "product" ? 'orders_items' : 'orders_services';
        update_details(['status' => $status], ['id' => $order_id], $table);

        return $this->jsonSuccessResponse("Order status updated successfully!");
    }

    public function customer_balance()
    {
        $user_id = $this->request->getGet('user_id');
        $customer = fetch_details("customers", ['user_id' => $user_id, 'status' => 1]);
        $balance = $customer[0]['balance'] ?? "0";

        return $this->response->setJSON([
            'error' => false,
            'balance' => $balance
        ]);
    }

    public function get_users()
    {
        $search = $this->request->getGet('search');
        if (!empty($search)) {
            $customer_model = new Customers_model();
            $response = $customer_model->get_users($search, $this->business_id);
            echo $response;
        }
    }

    public function save()
    {
        

        if (!isset($_POST) || empty($_POST)) {
            return redirect()->back()->withInput();
        }

        $ionAuthModel = new \IonAuth\Libraries\IonAuth();
        $business_id = $this->business_id;

        if (isset($_POST['user_id']) && !empty($_POST['user_id'])) {
            return $this->updateCustomer($ionAuthModel, $business_id);
        } else {
            return $this->createCustomer($ionAuthModel, $business_id);
        }
    }

    protected function updateCustomer($ionAuthModel, $business_id)
    {
        $this->validation->setRule('first_name', lang('Auth.edit_user_validation_fname_label'), 'required');
        $this->validation->setRule('identity', "Mobile", 'required');
        $this->validation->setRule('email', lang('Auth.edit_user_validation_email_label'), 'required');
        $this->validation->setRule('business_id', 'business', 'required');

        if ($this->request->getPost('password')) {
            $this->validation->setRule('password', lang('Auth.edit_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
        }

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'email' => $this->request->getPost('email'),
            'identity' => $this->request->getPost('identity'),
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = $this->request->getPost('password');
        }

        $status = isset($_POST['status']) && $_POST['status'] == "on" ? "1" : "0";
        $user_id = $_POST['user_id'];

        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
            $customers_model = new Customers_model();
            $customers = [
                'vendor_id' => $_SESSION['user_id'],
                'business_id' => $business_id,
                'user_id' => $user_id,
                'status' => $status,
            ];
            $customers_model->update($_POST['customer_id'], $customers);
        }

        $ionAuthModel->update($user_id, $data);
        return $this->jsonSuccessResponse('Customer updated successfully');
    }

    protected function createCustomer($ionAuthModel, $business_id)
    {
        $tables = $this->configIonAuth->tables;
        $identityColumn = $this->configIonAuth->identity;
        $this->data['identity_column'] = $identityColumn;

        $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'trim|required');
        $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');
        $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'required|trim|valid_email|is_unique[' . $tables['users'] . '.email]');
        $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
        $this->validation->setRule('business_id', 'business', 'required');

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $email = strtolower($this->request->getPost('email'));
        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
        $password = $this->request->getPost('password');

        $group_id_arry = fetch_details("groups", ['name' => 'customers'], "id");
        $group_id = [$group_id_arry[0]['id']];

        $additionalData = [
            'first_name' => $this->request->getPost('first_name'),
        ];

        $insert_id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
        $status = isset($_POST['status']) && !empty($_POST['status']) ? "1" : "0";

        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
            $customers_model = new Customers_model();
            $customers = [
                'vendor_id' => $_SESSION['user_id'],
                'business_id' => $business_id,
                'user_id' => $insert_id,
                'status' => $status,
            ];
            $customers_model->save($customers);
        }

        return $this->jsonSuccessResponse('Customer added successfully');
    }

    public function register()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        }

        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->jsonErrorResponse(DEMO_MODE_ERROR);
        }

        if (!isset($_POST) || empty($_POST)) {
            return redirect()->back()->withInput();
        }

        $tables = $this->configIonAuth->tables;
        $identityColumn = $this->configIonAuth->identity;
        $this->data['identity_column'] = $identityColumn;

        $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'required');
        $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');

        if (!empty($_POST['email'])) {
            $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'valid_email|is_unique[' . $tables['users'] . '.email]');
        }

        $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $email = strtolower($this->request->getPost('email'));
        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
        $password = $this->request->getPost('password');

        $group_id_arry = fetch_details("groups", ['name' => 'customers'], "id");
        $group_id = [$group_id_arry[0]['id']];

        $additionalData = [
            'first_name' => $this->request->getPost('first_name'),
            'phone' => $this->request->getPost('phone'),
        ];

        $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
        $business_id = $this->business_id;
        $balance = $_POST['balance'] ?? "";
        $status = $_POST['status'] ?? "1";

        $customer = [
            'user_id' => $id,
            'business_id' => $business_id,
            'created_by' => $_SESSION['user_id'],
            'vendor_id' => $_SESSION['user_id'],
            'balance' => $balance,
            'status' => $status
        ];

        $customer_model = new Customers_model();
        $customer_model->save($customer);

        return $this->jsonSuccessResponse('Customer added successfully');
    }

    public function save_sales_order()
    {
        

        if (!isset($_POST) || empty($_POST)) {
            return redirect()->back();
        }

        $rules = [
            'total' => ['rules' => 'required', 'label' => 'Total'],
            'payment_status' => ['rules' => 'required|trim', 'label' => 'Payment Status'],
            'amount_paid' => 'trim',
            'status' => ['rules' => 'trim', 'label' => 'Status'],
            'warehouse_id' => ['rules' => 'required', 'label' => 'Warehouse']
        ];

        if ($this->request->getVar('payment_status') == "partially_paid" || $this->request->getVar('payment_status') == "fully_paid") {
            $rules = array_merge($rules, [
                'sales_date' => ['rules' => 'required', 'label' => 'Sales Date'],
                'customer_id' => ['rules' => 'required', 'label' => 'Customer'],
                'sale_product_id' => ['rules' => 'required', 'label' => 'Products'],
                'status' => ['rules' => 'required', 'label' => 'Status'],
                'payment_method' => ['rules' => 'required', 'label' => 'Payment Method'],
                'warehouse_id' => ['rules' => 'required', 'label' => 'Warehouse']
            ]);
        }

        $this->validation->setRules($rules);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->jsonErrorResponse($this->validation->getErrors());
        }

        $customer_id = $this->request->getVar('customer_id');
        $customer = fetch_details('customers', ['user_id' => $customer_id]);
        $customer_id = $customer[0]['id'];

        $quantity = $_POST['qty'];
        $price = $_POST['price'];
        $sub_total = array_map(function ($q, $p) {
            return (int)$q * (int)$p;
        }, $quantity, $price);
        $total = array_sum($sub_total);

        $payment_status = $this->request->getVar('payment_status');
        $delivery_charges = $this->request->getVar('delivery_charge');
        $discount = $this->request->getVar('order_discount') ?? 0;
        $customer_wallet_balance = !empty($customer) ? floatval($customer[0]['balance']) : "0";
        $warehouse_id = $this->request->getVar('warehouse_id');

        $products = json_decode($_POST['sale_product_id']);
        $warehouse_product_stock = new WarehouseProductStockModel();

        foreach ($products as $item) {
            $warehouse_product_list = $warehouse_product_stock->where([
                'warehouse_id' => $warehouse_id,
                'product_variant_id' => $item->variant_id
            ])->get()->getResultArray();

            if (empty($warehouse_product_list)) {
                return $this->jsonErrorResponse("" . ucfirst($item->name) . " - $item->variant_name is not available in selected warehouse !");
            }
        }

        $final_total = $this->request->getVar('total');
        $payment_type = $this->request->getVar('payment_method[0]') ?? " ";
        $amount_paid = $this->determineAmountPaid($payment_status, $payment_type, $final_total, $this->request->getVar('amount_paid'));

        if ($payment_type == "wallet" && $customer_wallet_balance < $amount_paid) {
            return $this->jsonErrorResponse("Customer doesn't have sufficient wallet balance, please recharge wallet!");
        }

        $orders_model = new Orders_model();
        $vendor_id = $_SESSION['user_id'];
        $order = [
            'vendor_id' => $vendor_id,
            'created_by' => $vendor_id,
            'business_id' => $this->business_id,
            'customer_id' => $customer_id,
            'warehouse_id' => $warehouse_id,
            'order_no' => $this->request->getVar('order_no'),
            'order_type' => $this->request->getVar('order_type'),
            'sales_date' => $this->request->getVar('sales_date'),
            'payment_method' => $payment_type,
            'tax_id' => $this->request->getVar('tax_id'),
            'discount' => $discount,
            'delivery_charges' => $delivery_charges,
            'payment_status' => $payment_status,
            'amount_paid' => $amount_paid,
            'final_total' => $final_total,
            'total' => $total,
            'message' => $this->request->getVar('message'),
        ];

        $orders_model->save($order);
        $orders_id = $orders_model->getInsertID();

        $this->processSalesPayment($payment_type, $payment_status, $orders_id, $customer_id, $vendor_id, $amount_paid, $customer_wallet_balance);

        $tax_model = new Tax_model();
        $products = $this->prepareSalesProducts($products, $quantity, $price, $tax_model);

        foreach ($products as $item) {
            $tax_details = $this->prepareTaxDetails($tax_model, $item);
            $orders_items = [
                'product_id' => $item->product_id,
                'product_name' => $item->product_name,
                'tax_name' => $item->tax_name,
                'tax_percentage' => '',
                'tax_id' => '',
                'is_tax_included' => $item->is_tax_included,
                'tax_details' => $tax_details,
                'order_id' => $orders_id,
                'product_variant_id' => $item->variant_id,
                'quantity' => $item->qty,
                'price' => $item->price,
                'sub_total' => $item->qty * $item->price,
                'discount' => $item->discount ?? 0,
                'status' => $this->request->getVar('status'),
            ];

            $orders_items_model = new Orders_items_model();
            $orders_items_model->save($orders_items);
            update_stock($item->variant_id, $item->qty);
            updateWarehouseStocks($warehouse_id, $item->variant_id, $item->qty, 0);
        }

        return $this->jsonSuccessResponse('Order saved successfully');
    }

    protected function determineAmountPaid($payment_status, $payment_type, $final_total, $amount_paid)
    {
        if ($payment_status == "fully_paid") {
            return $final_total;
        }
        if ($payment_status == "partially_paid") {
            if ($final_total < $amount_paid) {
                return $final_total;
            }
            return $amount_paid;
        }
        if ($payment_type == "wallet") {
            return $final_total;
        }
        return 0;
    }

    protected function prepareSalesProducts($products, $quantity, $price, $tax_model)
    {
        $count = count($products);
        for ($i = 0; $i < $count; $i++) {
            $tax = fetch_details('products', ['id' => $products[$i]->product_id], ['tax_ids', 'is_tax_included']);
            $tax_details = fetch_details('tax', ['id' => $tax[0]['tax_ids']]);

            $products[($count - 1) - $i]->qty = $quantity[$i];
            $products[($count - 1) - $i]->product_id = $products[$i]->product_id;
            $products[($count - 1) - $i]->product_name = $products[$i]->name;
            $products[($count - 1) - $i]->tax_id = $tax[0]['tax_ids'];
            $products[($count - 1) - $i]->is_tax_included = $tax[0]['is_tax_included'];
            $products[($count - 1) - $i]->tax_name = $tax_details[0]['name'] ?? '';
            $products[($count - 1) - $i]->tax_percentage = $tax_details[0]['percentage'] ?? '';
            $products[($count - 1) - $i]->discount = $_POST['discount'][$i] ?? '0';
        }

        return $products;
    }

    protected function processSalesPayment($payment_type, $payment_status, $order_id, $customer_id, $vendor_id, $amount_paid, $customer_wallet_balance)
    {
        $customers_transactions_model = new Customers_transactions_model();
        $transaction = [
            'order_id' => $order_id,
            'customer_id' => $customer_id,
            'vendor_id' => $vendor_id,
            'created_by' => $vendor_id,
            'payment_type' => $payment_type,
            'amount' => $amount_paid,
            'transaction_id' => $this->request->getVar('transaction_id'),
        ];

        if ($payment_type == "wallet") {
            $customers_transactions_model->save($transaction);
            $balance = $customer_wallet_balance - $amount_paid;
            update_details(['balance' => $balance], ['user_id' => $customer_id], "customers");
        } elseif ($payment_type == "cash" && $payment_status == "partially_paid") {
            $customers_transactions_model->save($transaction);
        } elseif ($payment_type != "cash" && $payment_type != "wallet") {
            $customers_transactions_model->save($transaction);

            // For non-cash/non-wallet payments, we might want to log additional details
            $this->logNonStandardPayment($order_id, $payment_type, $amount_paid);
        }
    }
    protected function logNonStandardPayment($order_id, $payment_type, $amount_paid)
    {
        // Additional logging for non-standard payment methods
        log_message('info', "Processed $payment_type payment for order $order_id - Amount: $amount_paid");

        // You could also update some audit table here if needed
        // $this->updatePaymentAuditLog($order_id, $payment_type, $amount_paid);
    }
    public function payment_reminder()
    {
        if (empty($this->business_id) || check_data_in_table('businesses', $_SESSION['business_id'])) {
            return redirect()->to("vendor/businesses");
        }

        $data = $this->setViewData(VIEWS . "payment_reminder", "Payment Reminder");
        return view("admin/template", $data);
    }

    public function payment_reminder_table()
    {
        $orders_model = new Orders_model();
        $orders = $orders_model->payment_reminder($this->business_id);
        $total = count($orders);

        $rows = [];
        foreach ($orders as $order) {
            $order_id = $order['id'];
            $delivery_boy_name = $this->getDeliveryBoyName($order['order_type'], $order_id);

            $customer_id = $order['customer_id'];
            $customer_model = new Customers_model();
            $customer_wallet = $customer_model->get_customer($customer_id);
            $balance = $customer_wallet[0]['balance'] ?? 0.00;
            $customer_name = $this->ionAuth->user($customer_id)->row()->first_name;

            $status = $this->getPaymentStatusBadge($order['payment_status']);
            $view_order = $this->getPaymentReminderButton($order['id']);

            $rows[] = [
                'id' => $order['id'],
                'order_type' => ucwords($order['order_type']),
                'Order_date' => date_formats(strtotime($order['created_at'])),
                'vendor_id' => $order['vendor_id'],
                'customer_name' => $customer_name,
                'business_id' => $order['business_id'],
                'total' => currency_location(decimal_points($order['total'])),
                'remaining_amount' => currency_location(decimal_points(($order['total'] - $order['amount_paid']))),
                'balance' => currency_location(decimal_points($balance)),
                'delivery_charges' => currency_location(decimal_points($order['delivery_charges'])),
                'discount' => currency_location(decimal_points($order['discount'])),
                'final_total' => currency_location(decimal_points($order['final_total'])),
                'payment_status' => $status,
                'message' => $order['message'],
                'amount_paid' => currency_location(decimal_points($order['amount_paid'])),
                'payment_method' => $order['payment_method'],
                'delivery_boy' => $delivery_boy_name,
                'action' => $view_order
            ];
        }

        $array = [
            'total' => $total,
            'rows' => $rows
        ];

        echo json_encode($array);
    }

    protected function getPaymentReminderButton($order_id)
    {
        return '<button type="button" class="btn btn-info btn-sm payment_reminder_button" data-id="' . $order_id . '" title="Send Remainder" onclick="payment_reminder(' . $order_id . ')"><i class="bi bi-bell"></i></button>';
    }

    public function send_reminder()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->jsonErrorResponse(DEMO_MODE_ERROR);
        }

        $order_id = $_GET['order_id'];
        $order_details = fetch_details('orders', ['id' => $order_id]);
        $customer_details = $this->ionAuth->user($order_details[0]['customer_id'])->row();
        $setting = get_settings('email', true);
        $company_title = get_settings('general', true);
        $icon = get_business_icon($this->business_id);

        $message = $this->getReminderEmailTemplate($order_details, $customer_details, $company_title, $icon);

        $subject = "Payment Reminder";
        $email = \Config\Services::email();
        $email_con = [
            'protocol' => 'smtp',
            'SMTPHost' => $setting['smtp_host'],
            'SMTPPort' => (int) $setting['smtp_port'],
            'SMTPUser' => $setting['email'],
            'SMTPPass' => $setting['password'],
            'SMTPCrypto' => $setting['smtp_encryption'],
            'mailType' => $setting['mail_content_type'],
            'charset' => 'utf-8',
            'wordWrap' => true,
        ];
        $email->initialize($email_con);
        $email->setFrom($setting['email'], $company_title['title']);
        $email->setTo(trim($customer_details->email));
        $email->setSubject($subject);
        $email->setMessage($message);

        if ($email->send()) {
            return $this->jsonSuccessResponse("Email sent!");
        } else {
            return $this->jsonErrorResponse("Something went wrong Please try again after some time.", [
                'console' => "console.log(" . $email->printDebugger() . ");"
            ]);
        }
    }

    protected function getReminderEmailTemplate($order_details, $customer_details, $company_title, $icon)
    {
        return '<!DOCTYPE html>
                <html>
                <head>
                <meta charset="UTF-8">
                <title>Payment Reminder</title>
                <meta name="viewport" content="width=device-width, initial-scale=1">
                <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 14px;
                    line-height: 1.5;
                    color: #333333;
                    background-color: #F7F7F7;
                    padding: 0;
                    margin: 0;
                }
                .container {
                    box-shadow: 0px -1px 6px 3px #0000000d;
                    max-width: 600px;
                    margin: 0 auto;
                    background-color: #FFFFFF;
                    padding: 30px;
                    border-radius: 5px;
                    border: 1px solid #efefef;
                    margin-top: 10px;
                    box-shadow: -1px 1px 4px 2px #40404021;
                }
                p {
                    font-size: large;
                    margin-bottom: 10px;
                }
                .invoice {
                    margin-top: 30px;
                    border-collapse: collapse;
                    width: 100%;
                }
                .invoice th, .invoice td {
                    padding: 10px;
                    border: 1px solid #CCCCCC;
                    text-align: left;
                    font-size: 14px;
                    line-height: 1.5;
                }
                .invoice th {
                    background-color: #F7F7F7;
                    font-weight: bold;
                    font-size: medium
                }
                .invoice td {
                    background-color: #FFFFFF;
                }
                .button {
                    display: inline-block;
                    padding: 10px 20px;
                    background-color: #2E7D32;
                    color: #FFFFFF;
                    font-size: 14px;
                    font-weight: bold;
                    text-decoration: none;
                    border-radius: 5px;
                }
                .button:hover {
                    background-color: #4CAF50;
                }
                .h1 {
                    color: ' . $company_title['primary_color'] . ';
                    font-size: 24px;
                }
                .th {
                    background-color: #ffffff;
                    color: #f00;
                    text-align: left;
                    padding: 10px;
                }
                .image-box-100 {
                    display: flex;
                    flex-wrap: nowrap;
                    align-content: center;
                    justify-content: center;
                    align-items: center;
                    width: 250px;
                    height: 80px;
                }
                .image-box-100 img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                }
                @media screen and (max-width: 600px) {
                    .container {
                        padding: 20px;
                    }
                    h1 {
                        font-size: 20px;
                    }
                    .invoice th, .invoice td {
                        font-size: 12px;
                    }
                }
                </style>
                </head>
                <body>
                <div class="container">
                    <div class="card"> 
                        <div class="d-flex justify-content-center">
                            <img class="image-box-100" src="' . base_url($icon['icon']) . '">
                        </div>
                        <h1 class="h1">Payment Reminder for Order #[' . $order_details[0]['id'] . ']</h1>
                        <p>Dear ' . $customer_details->first_name . ',</p>
                        <p>I hope this email finds you well. I am writing to remind you that the payment for the above-mentioned invoice is now overdue. According to our records, the payment was due from ' . $order_details[0]['created_at'] . '.</p>
                        <p>Please note that late payment can cause significant problems for us as a company, and we would appreciate it if you could settle the outstanding balance as soon as possible.</p>
                        <table class="invoice">
                            <thead class="th">
                                <tr>
                                    <th>Order Date</th>
                                    <th>Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>' . $order_details[0]['created_at'] . '</td>
                                    <td><strong>' . currency_location(decimal_points(($order_details[0]['total'] - $order_details[0]['amount_paid']))) . '</strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <p>If you have already made the payment, kindly ignore this email.</p>
                        <p>If you have any questions or concerns regarding this payment, please do not hesitate to contact us. <br><br>Best regards,<br> ' . $company_title['title'] . '</p>
                    </div>
                </div>
                </body>
                </html>';
    }
}
