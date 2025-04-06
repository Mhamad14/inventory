<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Customers_model;
use App\Models\Customers_transactions_model;

class Transactions extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "transaction";
            $data['title'] = "Transaction - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['delivery_boy_id'] = $id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['businesses'] = $businesses;
            return view("delivery-man/template", $data);
        }
    }
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
                '2' => "<span class='badge' style='background: #E1341E;'>wallet</span>",
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

        $customer = $builder->where('id', $customer_id)->get()->getRowArray();


        $user_id = 0;
        if (empty($customer)) {
            $user_id = $customer_id;
        } else {
            $user_id = $customer_id;
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
                    'amount' => currency_location(decimal_points(($transaction['amount']))),
                    'transaction_id' => $transaction_id,
                    'status' => $status,
                ];
                $i++;
            }
            $row = [
                'id' => "<strong>Paid</strong>",
                'amount' => "<span class='badge badge-success'>" . currency_location(decimal_points($previous_paid_amount)) . "</span>",
                'payment_type' => "<strong>Remaining Amount<strong>",
                'created_at' => "<span class='badge badge-danger'>" . currency_location(decimal_points(($amount_left))) . "</span>",
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
}
