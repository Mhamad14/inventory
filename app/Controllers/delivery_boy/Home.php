<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Businesses_model;

class Home extends BaseController
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

            $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['currency'] =  $currency;
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = "index";
            $data['title'] = "Welcome to - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $db = \Config\Database::connect();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            $business_count = intval($db->table('delivery_boys')->select('count(id) as total')->where(['user_id' =>  $id])->get()->getResultArray()[0]['total']);
            $orders = $db->table('orders o')->select('count(id) as total')->where(["created_by" => $id, 'business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            $customers = $db->table('customers')->select('count(id) as total')->where(['business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            $data['orders_count'] = $orders;
            $data['business_count'] = $business_count;
            $data['customers_count'] = $customers;
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $permission = get_delivery_boy_permission($id, $business_id, 'permission');
            $data['permissions'] = $permission;
            if (isset($permission) && !empty($permission)) {
                $orders_permission = $permission['orders_permission'];
                if ($orders_permission == "1") {
                    $data['orders_permission'] = $orders_permission;
                } else {
                    $data['orders_permission'] = "0";
                }
                $customer_permission = $permission['customer_permission'];
                if ($customer_permission == "1") {
                    $data['customer_permission'] = $customer_permission;
                } else {
                    $data['customer_permission'] = "0";
                }

                $transaction_permission = $permission['transaction_permission'];
                if ($transaction_permission == "1") {
                    $data['transaction_permission'] = $transaction_permission;
                } else {
                    $data['transaction_permission'] = "0";
                }
            }
            $data['businesses'] = $businesses;
            $data['total_amount_paid'] = floatval(fetch_details('orders', ['payment_status' => 'fully_paid', 'business_id' => $business_id], 'SUM(amount_paid) as `total`')[0]['total']);
            $total_amount_left =  fetch_details('orders', ['business_id' => $business_id], 'payment_status,amount_paid,final_total');
            $amount_left = 0.00;
            if (isset($total_amount_left) && !empty($total_amount_left)) {
                foreach ($total_amount_left as $amount) {
                    if ($amount['payment_status'] == 'unpaid' || $amount['payment_status'] == 'partially_paid' || $amount['payment_status'] == 'cancelled') {
                        $amount_left += floatval($amount['final_total']) - floatval($amount['amount_paid']);
                    }
                }
            }
            $data['amount_left'] = floatval($amount_left);
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("delivery-man/template", $data);
        }
    }
    public function login()
    {
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['title'] = "Login to - " . $company_title;
        return view("login", $data);
    }
    public function switch_businesses($business_id = "")
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {
            check_data_in_table('businesses', $business_id);
            $business_model = new Businesses_model();
            $business = $business_model->find($business_id);
            $business_name = isset($business) && !empty($business) ? $business['name'] : "";
            $this->session->set('business_id', $business_id);
            $this->session->set('business_name', $business_name);
            return redirect()->back();
        }
    }
    public function fetch_sales()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('vendor/home/login');
        } else {

            $sales[] = array();
            $db = \Config\Database::connect();
            $id = $this->ionAuth->getUserId();

            $month_res = $db->table('orders')
                ->select('SUM(final_total) AS total_sale,DATE_FORMAT(created_at,"%b") AS month_name ')
                ->where(['created_by' => $id, 'business_id' => $_SESSION['business_id']])
                ->groupBy('year(CURDATE()),MONTH(created_at)')
                ->orderBy('year(CURDATE()),MONTH(created_at)')
                ->get()->getResultArray();

            $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));
            $month_wise_sales['month_name'] = array_column($month_res, 'month_name');

            $sales = $month_wise_sales;
            print_r(json_encode($sales));
        }
    }
}
