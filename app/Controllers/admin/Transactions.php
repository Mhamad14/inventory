<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;

class Transactions extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
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
            $data['page'] = FORMS . "transaction";
            $data['title'] = "Transaction - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['vendor_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function save_payment()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];

            return $this->response->setJSON($response);
        }
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {
            // Check if the POST request is set and not empty
            if (isset($_POST) && !empty($_POST)) {
                // Initialize the model for customer transactions
                $customers_transaction_model = new Customers_transactions_model();
                // Set validation rules based on whether 'order_id' is present in the POST request
                if (isset($_POST['order_id'])) {
                    $rules = [
                        'payment_type' => [
                            'rules' => 'required|trim',
                            'label' => 'Payment Type',
                        ],
                        'created_by' => [
                            'rules' => 'required|trim',
                            'label' => 'Created By',
                        ],
                        'amount' => [
                            'rules' => 'required|trim',
                            'label' => 'Amount',
                        ],
                    ];

                    if (isset($_POST['payment_type']) && $_POST['payment_type'] == 'other') {
                        $rules['payment_method_name'] = [
                            'rules' => 'required',
                            'label' => 'Payment Method Name',
                        ];
                    }

                    if ($_POST['payment_type'] != 'cash' && $_POST['payment_type'] != 'wallet') {
                        $rules['transaction_id'] = [
                            'rules' => 'required',
                            'label' => 'Transaction Id',
                        ];
                    }
                    $this->validation->setRules($rules);
                } else {
                    $rules = [
                        'payment_type' => [
                            'rules' => 'required|trim',
                            'label' => 'Payment Type'
                        ],
                        'created_by' => [
                            'rules' => 'required|trim',
                            'label' => 'Created By'
                        ],
                        'amount' => [
                            'rules' => 'required|trim',
                            'label' => 'Amount'
                        ],
                        'customer_id' => [
                            'rules' => 'required|trim',
                            'label' => 'Customer'
                        ],
                        'type' => [
                            'rules' => 'required|trim',
                            'label' => 'Type'
                        ],
                    ];

                    if (isset($_POST['payment_type']) && $_POST['payment_type'] == 'other') {
                        $rules['payment_method_name'] = [
                            'rules' => 'required',
                            'label' => 'Payment Method Name',
                        ];
                    }
                    if (isset($_POST['payment_type']) != 'cash') {
                        $rules['transaction_id'] = [
                            'rules' => 'required',
                            'label' => 'Transaction Id',
                        ];
                    }
                    $this->validation->setRules($rules);
                }
                // Run validation rules
                if (!$this->validation->withRequest($this->request)->run()) {
                    // Validation failed, return error response
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token(); // Add CSRF token
                    $response['csrf_hash'] = csrf_hash();   // Add CSRF hash
                    return $this->response->setJSON($response);
                } else {

                    // Validation passed, process the transaction
                    $opening_balance = ""; // Initialize opening balance
                    $amount = $this->request->getVar('amount'); // Get the amount from the request
                    $transaction_type = $this->request->getVar('type'); // Get the transaction type
                    $payment_for = '';
                    $transaction_id =  !empty($this->request->getVar('transaction_id'))  ? $this->request->getVar('transaction_id') : '';

                    /**
                     * Explanation of Changes:
                     * Previously, the "customer_id" column in the "orders" table represented the "id" from the "users" table 
                     * for sales orders. This has been updated so that "customer_id" now represents the "id" from the "customers" table.
                     * 
                     * To handle this transition, the following logic determines whether "customer_id" refers to a user or a customer 
                     * and retrieves the correct details accordingly.
                     */

                    // Get the submitted customer ID (initially referencing the "users" table)
                    $customer_id = $_POST['customer_id'];

                    // Try to find a customer record where "user_id" matches the submitted "customer_id"
                    $customer = fetch_details("customers", ['user_id' => $customer_id]);

                    // Initialize $user_id to 0
                    $user_id = 0;

                    if (empty($customer)) {
                        // If no customer is found by "user_id", assume "customer_id" refers directly to the "customers" table "id"
                        $customer = fetch_details("customers", ['id' => $customer_id]);
                        $customer_id = $customer[0]['id'];   // Update customer_id to the correct "customers" table ID
                        $user_id = $customer[0]['user_id']; // Get the associated "user_id"

                    } else {
                        // If a customer is found by "user_id", extract the relevant data
                        $user_id = $customer[0]['user_id']; // Get the associated "user_id"
                        $customer_id = $customer[0]['id']; // Update customer_id to the correct "customers" table ID
                    }


                    $balance = isset($customer[0]['balance']) ? $customer[0]['balance'] : "";
                    $update_payment_status_for_order = false;
                    if (isset($_POST['order_id'])) {
                        // this section is used for recoding payments that are made for sales order;
                        $payment_for = '0'; // for sales transaction;
                        // Handle transactions related to orders

                        $order_id = $this->request->getVar('order_id');
                        $order = fetch_details("orders", ['id' => $order_id]);
                        // Fetch previous paid amount and final total of the order
                        $previous_paid_amount = isset($order[0]) ? $order[0]['amount_paid'] : "0";
                        $final_total = isset($order[0]) ? $order[0]['final_total'] : "0";
                        $amount_left = floatval($final_total) - floatval($previous_paid_amount); // Calculate remaining amount
                        $type = '1'; // Default type for order transaction
                        if ($amount_left == 0) {
                            // If the order is already fully paid
                            update_details(['payment_status' => 'fully_paid'], ['id' => $order_id], 'orders');
                            $response['error'] = true;
                            $response['csrfName'] = csrf_token();
                            $response['csrfHash'] = csrf_hash();
                            $response['message'] = ['Your order has been paid'];
                            return $this->response->setJSON($response);
                        }

                        if ($amount > $amount_left) {
                            // Prevent overpayment
                            $response['error'] = true;
                            $response['csrfName'] = csrf_token();
                            $response['csrfHash'] = csrf_hash();
                            $response['message'] = ['Amount should not be greater than remaining amount - ' . $amount_left];
                            return $this->response->setJSON($response);
                        }

                        $payment_type = $this->request->getVar('payment_type');

                        if ($payment_type == "wallet" && $transaction_type != "debit") {
                            $response['error'] = true;
                            $response['csrfName'] = csrf_token();
                            $response['csrfHash'] = csrf_hash();
                            $response['message'] = ['Please select proper Transaction Type'];
                            return $this->response->setJSON($response);
                        }


                        if ($transaction_type == "debit") {
                            // deducting amount form current balance;
                            /**
                             * if balance is 130 and amount is 20
                             *  then balance = 130 - 20 and
                             */

                            if (floatval($balance) < floatval($amount)) {
                                $response['error'] = true;
                                $response['csrfName'] = csrf_token();
                                $response['csrfHash'] = csrf_hash();
                                $response['message'] = ['Customer does not have enough balance , Current customer balance = ' . $balance];
                                return $this->response->setJSON($response);
                            }
                            //setting balance after deduction;
                            $balance = floatval($balance) - floatval($amount);
                        }

                        // if remaining amount is paid fully then update payment_status ;
                        if ($final_total == ($previous_paid_amount + $amount)) {
                            $update_payment_status_for_order = true;
                        }
                    } else {
                        // this section is used for wallet transactions;

                        // Handle general transactions not related to orders
                        $payment_for = '2'; // for wallet transaction;
                        $order_id = "0"; // No associated order

                        $customer_id = $_POST['customer_id']; // here customer_id is "id" of users table";
                        $customer = fetch_details("customers", ['user_id' => $customer_id]);
                        $user_id = 0;
                        if (empty($customer)) {
                            $user_id = $customer_id;
                            $customer = fetch_details("customers", ['id' => $customer_id]);
                            $customer_id = $customer[0]['id'];
                        } else {
                            $user_id = $customer[0]['user_id'];
                            $customer_id = $customer[0]['id'];
                        }

                        $balance = isset($customer[0]['balance']) ? $customer[0]['balance'] : "";
                        $opening_balance = $balance;
                        // Update balance based on transaction type
                        if ($transaction_type == "debit") {
                            if ($balance < $amount) {
                                $response = [
                                    'error' => true,
                                    'message' => ['name' => "Not enough Wallet Balance for Debit !"],
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            $balance = floatval($balance) - floatval($amount);
                        }
                        if ($transaction_type == "credit") {
                            $balance = floatval($balance) + floatval($amount);
                        }
                    }

                    // Prepare transaction details for saving
                    $vendor_id = $this->ionAuth->getUserId(); // Get current vendor ID


                    $transaction = [
                        'customer_id' => $customer_id,
                        'order_id' => $order_id,
                        'vendor_id' => $vendor_id,
                        'user_id' => $user_id,
                        'business_id' => session('business_id'),
                        'created_by' => $this->request->getVar('created_by'),
                        'payment_type' => $this->request->getVar('payment_type'),
                        'payment_for' => $payment_for,
                        'transaction_type' => $transaction_type,
                        'transaction_id' => $transaction_id,
                        'amount' => $this->request->getVar('amount'),
                        'opening_balance' => (string)  $opening_balance,
                        'closing_balance' =>  (string) $amount,
                        'message' => $this->request->getVar('message')
                    ];

                    // Save the transaction in the database
                    $customers_transaction_model->save($transaction);

                    $db = \Config\Database::connect();
                    if (isset($_POST['order_id'])) {
                        // Update order's paid amount if order ID is present
                        $amount = floatval($previous_paid_amount) + floatval($amount);
                        update_details(['amount_paid' =>  (string) $amount], ['id' => $order_id], "orders");
                        if ($update_payment_status_for_order) {
                            update_details(['payment_status' => 'fully_paid'], ['id' => $order_id], 'orders');
                        }
                    }

                    // Update customer's balance
                    $db->table('customers')->where(['user_id' => $user_id])->update(['balance' => $balance]);

                    // Return success response
                    $response = [
                        'error' => false,
                        'message' => 'Payment added successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();

                    // Set success toast messages
                    $_SESSION['toastMessage'] = 'Payment added successfully';
                    $_SESSION['toastMessageType'] = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');

                    return $this->response->setJSON($response);
                }
            } else {
                // Redirect back if no POST data is present
                return redirect()->back();
            }
        }
    }

    public function save_purchase_payment()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            $response = [
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];

            return $this->response->setJSON($response);
        }
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {


            if (isset($_POST) && !empty($_POST)) {
                $customers_transaction_model = new Customers_transactions_model();
                if (isset($_POST['order_id'])) {
                    $this->validation->setRules([
                        'payment_type' => 'required',
                        'created_by' => 'required',
                        'amount' => 'required|greater_than[-1]|numeric',
                        'supplier_id' => 'required',

                    ]);
                } else {
                    $this->validation->setRules([
                        'payment_type' => 'required',
                        'created_by' => 'required',
                        'amount' => 'required',
                        'type' => 'required',

                    ]);
                }
                if (isset($_POST['payment_type']) && $_POST['payment_type'] == 'other') {
                    $rules['payment_method_name'] = 'required';
                }
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

                    $transaction_type = $this->request->getVar('type');
                    $transaction_id =  !empty($this->request->getVar('transaction_id'))  ? $this->request->getVar('transaction_id') : '';

                    $opening_balance = "";
                    $amount = $this->request->getVar('amount');
                    if (isset($_POST['order_id'])) {
                        $order_id = $this->request->getVar('order_id');
                        $order = fetch_details("purchases", ['id' => $order_id]);
                        $previous_paid_amount = isset($order[0]) ? $order[0]['amount_paid'] : "0";
                        $final_total = isset($order[0]) ? $order[0]['total'] : "0";
                        $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                        $supplier_id  = $this->request->getVar('supplier_id');
                        $supplier = fetch_details("suppliers", ['user_id' => $supplier_id]);
                        $user_id = 0;
                        if (empty($supplier)) {
                            $user_id = $supplier_id;
                        } else {
                            $user_id = $supplier[0]['user_id'];
                        }
                        $supplier_id = $supplier[0]['id'];

                        if ($amount_left == 0) {
                            update_details(['payment_status' => 'fully_paid'], ['id' => $order_id], 'purchases');
                            $response['error'] = true;
                            $response['csrfName'] = csrf_token();
                            $response['csrfHash'] = csrf_hash();
                            $response['message'] = ['Your order has been paid'];

                            return $this->response->setJSON($response);
                        }

                        if ($amount > $amount_left) {
                            $response['error'] = true;
                            $response['csrfName'] = csrf_token();
                            $response['csrfHash'] = csrf_hash();
                            $response['message'] = ['Amount should not be greater than remaining amount - ' . $amount_left];
                            return $this->response->setJSON($response);
                        }
                    }

                    $vendor_id  = $this->ionAuth->getUserId();
                    $business_id = session('business_id');

                    $transaction = array(
                        'user_id' => $user_id,
                        'business_id' => $business_id,
                        'order_id' => $order_id,
                        'supplier_id' => $supplier_id,
                        'vendor_id' => $vendor_id,
                        'transaction_type' => $transaction_type,
                        'transaction_id' => $transaction_id,
                        'order_type' => $this->request->getVar('order_type'),
                        'created_by' => $this->request->getVar('created_by'),
                        'payment_type' => $this->request->getVar('payment_type'),
                        'amount' => $this->request->getVar('amount'),
                        'message' => $this->request->getVar('message'),
                        'payment_for' =>  "1"  // payment for purchase ,
                    );
                    $customers_transaction_model->save($transaction);
                    if (isset($_POST['order_id'])) {
                        $amount = floatval($previous_paid_amount) + floatval($amount);
                        update_details(['amount_paid' => (string) $amount], ['id' => $order_id], "purchases");
                    }
                    if ($final_total == ($previous_paid_amount + $amount)) {
                        update_details(['payment_status' => 'fully_paid'], ['id' => $order_id], 'purchases');
                    }

                    $response = [
                        'error' => false,
                        'message' => 'Payment added successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Payment added successfully';
                    $_SESSION['toastMessageType']  = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->back();
            }
        }
    }
    // view-order transaction table
    public function customer_transaction_table($order_id = "", $customer_id = "")
    {
        /**
         * here variable "$customer_id" will reflect "id" column of "users" table if order is created by pos,
         * and variable "$customer_id" will reflect "id" column of "customer" table if order is created by sales order.
         * But in "customers_transactions" table we will search with "user_id" column.
         * So to handle this in-consistency we will assign proper data for to it.
         */

        $db = \Config\Database::connect();
        $builder = $db->table('customers');

        /**
         * Explanation of Changes:
         * Previously, the "customer_id" column in the "orders" table represented the "id" from the "users" table 
         * for sales orders. This has been updated so that "customer_id" now represents the "id" from the "customers" table.
         * 
         * To handle this transition, the following logic determines whether "customer_id" refers to a user or a customer 
         * and retrieves the correct details accordingly.
         */

        // Get the submitted customer ID (initially referencing the "users" table)
        // Try to find a customer record where "user_id" matches the submitted "customer_id"
        $customer = fetch_details("customers", ['user_id' => $customer_id]);

        // Initialize $user_id to 0
        $user_id = 0;

        if (empty($customer)) {
            // If no customer is found by "user_id", assume "customer_id" refers directly to the "customers" table "id"
            $customer = fetch_details("customers", ['id' => $customer_id]);
            $customer_id = $customer[0]['id'];   // Update customer_id to the correct "customers" table ID
            $user_id = $customer[0]['user_id']; // Get the associated "user_id"

        } else {
            // If a customer is found by "user_id", extract the relevant data
            $user_id = $customer[0]['user_id']; // Get the associated "user_id"
            $customer_id = $customer[0]['id']; // Update customer_id to the correct "customers" table ID
        }


        $transactions = fetch_details("customers_transactions", ["order_id" => $order_id, "user_id" => $user_id]);
        $i = 0;
        $rows = [];
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                if ($transaction['transaction_type'] == "2") {
                    $order =  fetch_details("purchases", ['id' => $order_id]);
                    $final_total = $order[0]['total'];
                } else {
                    $order = fetch_details("orders", ['id' => $order_id]);
                    $final_total = $order[0]['final_total'];
                }
                $previous_paid_amount = isset($order) ? ($order[0]['amount_paid']) : floatval(0.00);
                $transaction_amount = $transaction['amount'];
                $payment_status = $order[0]['payment_status'];
                $amount_left = "0";
                $transaction_id = isset($transaction['transaction_id']) ? $transaction['transaction_id'] : "";
                if ($payment_status == "fully_paid") {
                    $status = "<span class='badge badge-success'>Fully Paid</span>";
                    $amount_left = "0.00";
                }
                if ($payment_status == "partially_paid") {
                    $status = "<span class='badge badge-primary'>Partially Paid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "unpaid") {
                    $status = "<span class='badge badge-warning'>Unpaid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "cancelled") {
                    $status = "<span class='badge badge-danger'>Cancelled</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                $rows[$i] = [
                    'id' => $transaction['id'],
                    'payment_type' => $transaction['payment_type'],
                    'created_at' => date_formats(strtotime($transaction['created_at'])),
                    'amount' => currency_location(decimal_points($transaction['amount'])),
                    'transaction_id' => $transaction_id,
                    'status' => $status,
                ];
                $i++;
            }
            $row = [
                'id' => "<strong>Paid</strong>",
                'amount' => "<span class='badge badge-success'>" . currency_location(decimal_points($previous_paid_amount)) . "</span>",
                'payment_type' => "<strong>Remaining Amount<strong>",
                'created_at' => "<span class='badge badge-danger'>" . currency_location(decimal_points($amount_left)) . "</span>",
                'transaction_id' => "",
                'status' => "",
            ];

            if (is_array($transactions)) {
                $array['total'] = count($transactions);
            }
            array_push($rows, $row);
        }
        $array['rows'] = $rows;
        echo json_encode($array);
    }

    public function   purchase_transaction_table($purchase_id = "")
    {
        $transactions = fetch_details("customers_transactions", ["order_id" => $purchase_id, "payment_for" => "1"]);

        $i = 0;
        $rows = [];
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        if (!empty($transactions)) {
            foreach ($transactions as $transaction) {
                if ($transaction['payment_for'] == "1") {
                    $order =  fetch_details("purchases", ['id' => $purchase_id]);
                    $final_total = $order[0]['total'];
                } else {
                    $order = fetch_details("orders", ['id' => $purchase_id]);
                    $final_total = $order[0]['final_total'];
                }
                $previous_paid_amount = isset($order) ? ($order[0]['amount_paid']) : floatval(0.00);
                $transaction_amount = $transaction['amount'];
                $payment_status = $order[0]['payment_status'];
                $amount_left = 0;

                if ($payment_status == "fully_paid") {
                    $status = "<span class='badge badge-success'>Fully Paid</span>";
                }
                if ($payment_status == "partially_paid") {
                    $status = "<span class='badge badge-primary'>Partially Paid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "unpaid") {
                    $status = "<span class='badge badge-warning'>Unpaid</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                if ($payment_status == "cancelled") {
                    $status = "<span class='badge badge-danger'>Cancelled</span>";
                    $amount_left = floatval($final_total) - floatval($previous_paid_amount);
                }
                $rows[$i] = [
                    'id' => $transaction['id'],
                    'payment_type' => $transaction['payment_type'],
                    'created_at' => date_formats(strtotime($transaction['created_at'])),
                    'amount' => currency_location(decimal_points($transaction['amount'])),
                    'status' => $status,
                ];
                $i++;
            }
            $row = [
                'id' => "<strong>Paid</strong>",
                'amount' => "<span class='badge badge-success'>" . currency_location(decimal_points($previous_paid_amount)) . "</span>",
                'payment_type' => "<strong>Remaining Amount<strong>",
                'created_at' => "<span class='badge badge-danger'>" . currency_location(decimal_points($amount_left)) . "</span>",
                'status' => "",
            ];

            if (is_array($transactions)) {
                $array['total'] = count($transactions);
            }
            array_push($rows, $row);
        }
        $array['rows'] = $rows;

        echo json_encode($array);
    }
    // transaction customers table
    public function customers_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $customers_model = new Customers_model();
        $customers = $customers_model->get_customers_details($business_id);
        $i = 0;
        $rows = [];

        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        foreach ($customers as $customer) {
            $rows[$i] = [
                'id' => $customer['user_id'],
                'customer_name' => $customer['first_name'],
                'email' => $customer['email'],
                'balance' => currency_location(decimal_points(number_format($customer['balance']))),
            ];
            $i++;
        }
        if (is_array($customers)) {
            $array['total'] = count($customers);
        }
        $array['rows'] = $rows;
        echo json_encode($array);
    }
    // transaction transaction table
    public function transactions_table()
    {
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        $vendor_id = $_SESSION['user_id'];
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $customers_transaction_model = new Customers_transactions_model();

        // Get search term from Bootstrap Table
        $search = $this->request->getGet('search');

        // Pass search parameter to model
        $result = $customers_transaction_model->get_transactions($business_id, [
            'search' => $search,
            'sort' => $this->request->getGet('sort'),
            'order' => $this->request->getGet('order'),
            'offset' => $this->request->getGet('offset'),
            'limit' => $this->request->getGet('limit')
        ]);

        $transactions = $result['rows'];
        $total = $result['total'];
        $rows = [];

        foreach ($transactions as $transaction) {


            $type = match ($transaction['transaction_type']) {
                'credit' => "<span class='badge' style='background: #B0B0B0;'>Credit</span>",
                'debit' => "<span class='badge' style='background: #8B4513;'>Debit</span>",
                default => "<span class='badge badge-success'>wallet</span>"
            };

            $payment_for = match ($transaction['payment_for']) {
                '0' => "<span class='badge' style='background: #EBCD14;'>Purchase</span>",
                '1' => "<span class='badge' style='background: #E1341E;'>Sales</span>",
                '2' => "<span class='badge' style='background: #24db98;'>wallet</span>",
                default => "N/A"
            };

            $rows[] = [
                'id' => $transaction['id'],
                'transaction_type' => $type,
                'payment_for' => $payment_for,
                'first_name' => $transaction['user_first_name'],
                'last_name'  => $transaction['user_last_name'],
                'user_id' => $transaction['user_id'],
                'supplier_id' => $transaction['supplier_id'],
                'customer_id' => $transaction['customer_id'],
                'order_id' => $transaction['order_id'],
                'created_by' => $transaction['created_by'],
                'payment_type' => $transaction['payment_type'],
                'transaction_id' => $transaction['transaction_id'],
                'amount' => currency_location(decimal_points(number_format($transaction['amount']))),
                'opening_balance' => currency_location(decimal_points(number_format($transaction['opening_balance']))),
                'closing_balance' => currency_location(decimal_points(number_format($transaction['closing_balance'])))
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $rows
        ]);
    }

    /**
     * Updates the data in the 'customers_transactions' table to align with the new relationships and table structures.
     * This function ensures normalized and consistent data by mapping the appropriate IDs from related tables.
     *
     * Why this is done:
     * - Previously, 'customer_id' and 'supplier_id' in the 'customers_transactions' table stored the 'id' from the 'users' table.
     * - This caused ambiguity and did not follow normalized database practices.
     * - Now, 'customer_id' and 'supplier_id' reference the 'id' from the 'customers' and 'suppliers' tables, respectively.
     * - Also, 'user_id' and 'business_id' fields are added for better clarity and relationship handling.
     *
     * Steps:
     * 1. Update 'user_id' based on 'customer_id' (matching 'users.id').
     * 2. Update 'user_id' based on 'supplier_id' (matching 'users.id').
     * 3. Update 'business_id' based on 'vendor_id' (matching 'businesses.user_id').
     * 4. Update 'customer_id' to reference 'customers.id' based on 'user_id'.
     * 5. Update 'supplier_id' to reference 'suppliers.id' based on 'user_id'.
     * 6. Re-run 'business_id' update for consistency.
     */
    public function updateTransactionsData()
    {
        $db = \Config\Database::connect();

        // 1. Update 'user_id' for customer transactions
        // Maps 'user_id' in 'customers_transactions' based on 'customer_id' matching 'users.id'.
        $sql1 = "
        UPDATE customers_transactions
        JOIN users ON customers_transactions.customer_id = users.id
        SET customers_transactions.user_id = users.id
    ";
        $db->query($sql1);

        // 2. Update 'user_id' for supplier transactions
        // Maps 'user_id' in 'customers_transactions' based on 'supplier_id' matching 'users.id'.
        $sql2 = "
        UPDATE customers_transactions
        JOIN users ON customers_transactions.supplier_id = users.id
        SET customers_transactions.user_id = users.id
    ";
        $db->query($sql2);

        // 3. Update 'business_id' for vendor transactions
        // Maps 'business_id' in 'customers_transactions' based on 'vendor_id' matching 'businesses.user_id'.
        $sql3 = "
        UPDATE customers_transactions
        JOIN businesses ON customers_transactions.vendor_id = businesses.user_id
        SET customers_transactions.business_id = businesses.id
    ";
        $db->query($sql3);

        // 4. Update 'customer_id' to reference the correct 'customers' table ID
        // Maps 'customer_id' in 'customers_transactions' based on 'user_id' matching 'customers.user_id'.
        $sql4 = "
        UPDATE customers_transactions
        JOIN customers ON customers_transactions.customer_id = customers.user_id
        SET customers_transactions.customer_id = customers.id
    ";
        $db->query($sql4);

        // 5. Update 'supplier_id' to reference the correct 'suppliers' table ID
        // Maps 'supplier_id' in 'customers_transactions' based on 'user_id' matching 'suppliers.user_id'.
        $sql5 = "
        UPDATE customers_transactions
        JOIN suppliers ON customers_transactions.supplier_id = suppliers.user_id
        SET customers_transactions.supplier_id = suppliers.id
    ";
        $db->query($sql5);

        // 6. Re-run 'business_id' update for consistency (optional redundancy for safety)
        // Ensures 'business_id' is updated correctly based on 'vendor_id'.
        $sql6 = "
        UPDATE customers_transactions
        JOIN businesses ON customers_transactions.vendor_id = businesses.user_id
        SET customers_transactions.business_id = businesses.id
    ";
        $db->query($sql6);

        return "All updates completed successfully!";
    }
}
