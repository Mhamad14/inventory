<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\WarehouseModel;


class Home extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'function']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }

    public function index()
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                $business_model = new Businesses_model();
                $warehouse_model = new WarehouseModel();

                if ($this->ionAuth->isTeamMember()) {

                    $team_member = fetch_details('team_members', ['user_id' => session('user_id')]);

                    if (empty($team_member)) {
                        return redirect()->to('login');
                    }

                    $business_ids = json_decode($team_member[0]['business_ids']);
                    $default_business = null;

                    // Find the default business
                    foreach ($business_ids as $key) {
                        $default_business = fetch_details('businesses', ['id' => $key, 'default_business' => 1]);
                        if (!empty($default_business)) {
                            break; // Exit loop once a default business is found
                        }
                    }

                    // If no default business is found, use the first business in the list
                    if (empty($default_business)) {
                        $default_business = fetch_details('businesses', ['id' => $business_ids[0]]);
                    }

                    if (!empty($default_business)) {
                        $default_business_id = $default_business[0]['id'];
                        $default_business_name = $default_business[0]['name'];
                        $this->session->set('business_id', $default_business_id);
                        $this->session->set('business_name', $default_business_name);

                        // checking if default warehouse is having business_id if not then setting it.
                        checkAndUpdateWarehouse($warehouse_model, $business_model);
                    }
                } else {
                    $allbusiness = $business_model->select()->where(['user_id' => session('user_id')])->get()->getResultArray();

                    if (empty($allbusiness)) {
                        session()->setFlashdata('message', 'Please create a business!');
                        session()->setFlashdata('type', 'error');
                    } else {

                        $default_business_id = null;
                        $default_business_name = null;


                        foreach ($allbusiness as $business) {
                            if ($business['default_business']) {
                                $default_business_id = $business['id'];
                                $default_business_name = $business['name'];
                            }
                        }

                        if (empty($default_business_id)) {
                            session()->setFlashdata('message', 'Please select a business!');
                            session()->setFlashdata('type', 'error');
                            return redirect()->to('admin/businesses');
                        } else {
                            $this->session->set('business_id', $default_business_id);
                            $this->session->set('business_name', $default_business_name);

                            checkAndUpdateWarehouse($warehouse_model, $business_model);
                        }
                    }
                }
            } else {
                // checking if default warehouse is having business_id if not then setting it.
                $warehouse_model = new WarehouseModel();
                $business_model = new Businesses_model();

                $business_id = session('business_id');
                $warehouse = $warehouse_model->where('business_id', $business_id)->first(); // Find warehouse by business_id

                if (empty($warehouse)) {
                    checkAndUpdateWarehouse($warehouse_model, $business_model);
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
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = "index";
            $data['title'] = "Welcome to - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";

            $id = $this->ionAuth->getUserId();

            $db = \Config\Database::connect();
            $business_id = null;

            $is_user_team_member = fetch_details('team_members', ['user_id' => $id]);

            if (empty($is_user_team_member)) {
                // user is admin.
                if (!isset($_SESSION['business_id'])) {
                    $default_business = fetch_details('businesses', ['default_business' => "1", 'user_id' => $id]);
                    $business_id = isset($default_business[0]['id']) ? $default_business[0]['id'] : "";
                    check_data_in_table('businesses', $business_id);
                    $business_name = isset($default_business[0]['name']) ? $default_business[0]['name'] : "";
                    $this->session->set('business_id', $business_id);
                    $this->session->set('business_name', $business_name);
                } else {
                    $business_id = $_SESSION['business_id'];
                }

                $business_count = $db->table('businesses')->select('count(id) as total')->where(["user_id" => $id])->get()->getResultArray()[0]['total'];
                $data['business_count'] = $business_count;
            } else {
                // user is team member, So get the vendor_id for team member and then select or search for business in business with vendor_id.

                $vendor_id = $is_user_team_member[0]['vendor_id'];

                if (!isset($_SESSION['business_id'])) {
                    $default_business = fetch_details('businesses', ['default_business' => "1", 'user_id' => $vendor_id]);

                    $business_id = isset($default_business[0]['id']) ? $default_business[0]['id'] : "";
                    check_data_in_table('businesses', $business_id);
                    $business_name = isset($default_business[0]['name']) ? $default_business[0]['name'] : "";

                    $this->session->set('business_id', $business_id);
                    $this->session->set('business_name', $business_name);
                } else {
                    $business_id = $_SESSION['business_id'];
                }

                $business_count = $db->table('businesses')->select('count(id) as total')->where(["user_id" => $vendor_id])->get()->getResultArray()[0]['total'];
                $data['business_count'] = $business_count;
            }


            $orders = $db->table('orders o')->select('count(id) as total')->where(["vendor_id" => $id, 'business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            $products = fetch_products($business_id)['total'];
            $customers = $db->table('customers')->select('count(id) as total')->where(['business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            $overall_customers = $db->table('customers')->select('count(id) as total')->where(["vendor_id" => $id])->get()->getResultArray()[0]['total'];
            $overall_products = $db->table('products p')->select('count(id) as total')->where(["vendor_id" => $id])->get()->getResultArray()[0]['total'];
            $overall_orders = $db->table('orders o')->select('count(id) as total')->where(["vendor_id" => $id])->get()->getResultArray()[0]['total'];

            $delivery_boys = $db->table('delivery_boys')->select('count(id) as total')->where(['business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            // $user_packages = $db->table('users_packages up')->select('up.*')->where(['user_id' => $id])->get()->getResultArray();
            if (!empty($user_packages)) {
                foreach ($user_packages as $p) {
                    $status  = subscription_status($p['id']);
                    if ($status == 'active') {
                        $data['package_name'] = $p['package_name'];
                        $data['months'] = $p['months'];
                        $data['started_from'] = date_formats(strtotime($p['start_date']));
                        $data['expires_on'] = date_formats(strtotime($p['end_date']));
                        $date  = date('Y-m-d');
                        $today = strtotime($date);
                        $end_date = strtotime($p['end_date']);
                        $timeleft = $end_date - $today;
                        $daysleft = round((($timeleft / 24) / 60) / 60);
                        if ($daysleft <= 10 || $daysleft <= 10.00) {
                            $data['daysleft'] = "<span class='badge badge-warning'>" . $daysleft . " Days</span>";
                        }
                        if ($daysleft == 0 || $daysleft == 0.00) {
                            $data['daysleft'] = "<span class='badge badge-danger'>Expired</span>";
                        }
                        $data['daysleft'] = "<span class='badge badge-success'>" . $daysleft . " Days</span>";
                        $data['no_of_businesses'] = $p['no_of_businesses'];
                        $data['no_of_delivery_boys'] = $p['no_of_delivery_boys'];
                        $data['no_of_products'] = $p['no_of_products'];
                        $data['no_of_customers'] = $p['no_of_customers'];
                    }
                }
            }
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

            $data['orders_count'] = $orders;
            $data['products_count'] = $products;
            $data['customers_count'] = $customers;
            $data['overall_orders'] = $overall_orders;
            $data['overall_products'] = $overall_products;
            $data['overall_customers'] = $overall_customers;
            $data['delivery_boys_count'] = $delivery_boys;
            $data['user'] = $this->ionAuth->user($id)->row();
            $data['sales_purchase'] = fetch_purchases();
            $products_stock = product_stock($business_id);
            // print_r( $products_stock);
            // die();
            $data['product_stock'] = $products_stock ? $products_stock['message'] : [];
            $data['low'] = $products_stock && isset($products_stock['low']) ? $products_stock['low'] : "0";
            $data['out'] = $products_stock && isset($products_stock['out']) ? $products_stock['out'] : "0";
            return view("admin/template", $data);
        }
    }
    public function fetch_sales()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {

            $sales[] = array();
            $db = \Config\Database::connect();
            $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";
            $month_res = $db->table('orders')
                ->select('SUM(final_total) AS total_sale,DATE_FORMAT(created_at,"%b") AS month_name ')

                ->where('business_id', $business_id)
                ->groupBy('year(CURDATE()),MONTH(created_at)')
                ->orderBy('year(CURDATE()),MONTH(created_at)')
                ->get()->getResultArray();

            $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));
            $month_wise_sales['month_name'] = array_column($month_res, 'month_name');
            $sales = $month_wise_sales;
            print_r(json_encode($sales));
        }
    }
    public function fetch_warehouse_sales()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {
            $sales = [];
            $db = \Config\Database::connect();

            // Check if the business_id is set in the session
            $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id']))
                ? trim($_SESSION['business_id'])
                : "";

            // Fetch total sales per warehouse for the business
            $warehouse_sales_res = $db->table('orders')
                ->select('warehouses.id as warehouse_id, warehouses.name as warehouse_name, SUM(orders.final_total) AS total_sale, DATE_FORMAT(orders.created_at, "%b") AS month_name')
                ->join('warehouses', 'warehouses.id = orders.warehouse_id', 'inner')
                ->where('orders.business_id', $business_id)
                ->groupBy('warehouses.id, warehouses.name, YEAR(orders.created_at), MONTH(orders.created_at)')
                ->orderBy('YEAR(orders.created_at), MONTH(orders.created_at)')
                ->get()
                ->getResultArray();

            // Format the results
            $sales = [];
            foreach ($warehouse_sales_res as $row) {
                $warehouse_id = $row['warehouse_id'];
                if (!isset($sales[$warehouse_id])) {
                    $sales[$warehouse_id] = [
                        'warehouse_name' => $row['warehouse_name'],
                        'total_sales' => [],
                        'month_name' => []
                    ];
                }
                $sales[$warehouse_id]['total_sales'][] = intval($row['total_sale']);
                $sales[$warehouse_id]['month_name'][] = $row['month_name'];
            }

            // Output the sales data in JSON format
            echo json_encode($sales);
        }
    }

    public function fetch_purchases()
    {
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        } else {
            $db = \Config\Database::connect();
            $purchase[] = array();
            $sales[] = array();
            $month_res = $db->table('orders')
                ->select('SUM(final_total) AS total_sale,DATE_FORMAT(created_at,"%b") AS month_name ')
                ->where('business_id', $_SESSION['business_id'])
                ->groupBy('year(CURDATE()),MONTH(created_at)')
                ->orderBy('year(CURDATE()),MONTH(created_at)')
                ->get()->getResultArray();
            $month_wise_sales['total_sale'] = array_map('intval', array_column($month_res, 'total_sale'));
            $month_wise_sales['month_name'] = array_column($month_res, 'month_name');


            $month_res_purchase = $db->table('purchases')
                ->select('SUM(total) AS total_purchases,DATE_FORMAT(created_at,"%b") AS purchase_month_name ')
                ->where('business_id', $_SESSION['business_id'])
                ->groupBy('year(CURDATE()),MONTH(created_at)')
                ->orderBy('year(CURDATE()),MONTH(created_at)')
                ->get()->getResultArray();

            $month_wise_sales['total_purchases'] = array_map('intval', array_column($month_res_purchase, 'total_purchases'));
            $month_wise_sales['purchase_month_name'] = array_column($month_res_purchase, 'purchase_month_name');

            $purchase = $month_wise_sales;
            print_r(json_encode($purchase));
        }
    }
    public function fetch_data()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $sales[] = array();
            $db = \Config\Database::connect();
            $id = $this->ionAuth->getUserId();

            $business_id = $_SESSION['business_id'];
            $orders = $db->table('orders o')->select('count(id) as total')->where(["vendor_id" => $id, 'business_id' =>  $business_id])->get()->getResultArray()[0]['total'];
            $customers = $db->table('customers')->select('count(id) as total')->where(['business_id' =>  $business_id])->get()->getResultArray()[0]['total'];

            $sales = $db->table('orders')
                ->select('sum(final_total) as total')
                ->where('business_id', $_SESSION['business_id'])
                ->get()->getResultArray()[0]['total'];

            $count['orders'] = $orders;
            $count['customer'] = $customers;
            $count['sales'] = $sales;
            $sales = $count;
            print_r(json_encode($sales));
        }
    }
    public function login()
    {
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['title'] = "Login - " . $company_title;
        return view("login", $data);
    }

    public function switch_businesses($business_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            check_data_in_table('businesses', $business_id);
            $business_model = new Businesses_model();
            $business = $business_model->find($business_id);
            $business_name = isset($business) && !empty($business) ? $business['name'] : "";
            $this->session->set('business_id', $business_id);
            $this->session->set('business_name', $business_name);
            return redirect()->to('admin/home');
        }
    }

    public function get_status_list()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return [
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ];
        } else {
            $user_id = session('user_id');
            $vendor_id = session('user_id');
            $business_id = session('business_id');
            if ($this->ionAuth->isTeamMember()) {
                $vendor_id = get_vendor_for_teamMember($user_id);
            }

            $db = \Config\Database::connect();
            $result = $db->table('status')->where(['vendor_id' => $vendor_id, 'business_id' => $business_id])->get()->getResultArray();
            return  print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }

    public function todays_total_expense()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {
            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('expenses')
                ->select('SUM(amount) as total_amount')
                ->where('DATE(expenses_date)', $today_date)
                ->get()
                ->getResultArray();
            if (!empty($result)) {
                $result = [
                    'total_amount' => isset($result[0]['total_amount']) && !empty($result[0]['total_amount']) ? $result[0]['total_amount'] :  "0.00",
                ];
            }
            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }
    public function todays_total_sales()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {
            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('orders')
                ->select('SUM(final_total) as Total_amount')
                ->where('DATE(created_at)', $today_date)
                ->get()
                ->getResultArray();
            if (!empty($result)) {
                $result = ["total_amount"  => number_format($result[0]['Total_amount'], 2)];
            }

            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }

    public function todays_total_purchase()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {
            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('purchases')
                ->select('SUM(total) as Total_amount')
                ->where('DATE(created_at)', $today_date)
                ->get()
                ->getResultArray();
            if (!empty($result)) {
                $result = ["total_amount"  => number_format($result[0]['Total_amount'], 2)];
            }

            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }
    public function todays_total_payment_resived_form_orders()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {

            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('orders')->select('SUM(amount_paid) as total_payment')
                ->where([
                    'DATE(created_at)' => $today_date,
                ])->get()
                ->getResultArray();
            if (!empty($result)) {
                $result = ["total_amount"  => number_format($result[0]['total_payment'], 2)];
            }

            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }
    public function todays_total_payment_remaining_form_orders()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {

            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('orders')->select('SUM(final_total) as total_amount , SUM(amount_paid) as total_resived_amount')
                ->where([
                    'DATE(created_at)' => $today_date,
                ])->get()
                ->getResultArray();


            if (!empty($result)) {
                $result = [
                    "total_amount"  => number_format($result[0]['total_amount'], 2),
                    "total_resived_amount"  => number_format($result[0]['total_resived_amount'], 2),
                    "diffrence" =>  number_format($result[0]['total_amount'] - $result[0]['total_resived_amount'], 2)
                ];
            }

            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }
    public function todays_total_paids_resived_form_purchase()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {

            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('purchases')->select('SUM(amount_paid) as total_paid')
                ->where([
                    'DATE(created_at)' => $today_date,
                ])->get()
                ->getResultArray();
            if (!empty($result)) {
                $result = ["total_amount"  => number_format($result[0]['total_paid'], 2)];
            }

            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }
    public function todays_total_remaining_form_purchase()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return print_r(json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]));
        } else {

            $today_date = date('Y-m-d');
            $db = \config\Database::connect();
            $result = $db->table('purchases')->select('SUM(total) as total_amount , SUM(amount_paid) as total_amount_to_pay')
                ->where([
                    'DATE(created_at)' => $today_date,
                ])->get()
                ->getResultArray();

            if (!empty($result)) {
                $result = [
                    "total_amount"  => number_format($result[0]['total_amount'], 2),
                    "total_amount_to_pay"  => number_format($result[0]['total_amount_to_pay'], 2),
                    "diffrence" =>  number_format($result[0]['total_amount'] - $result[0]['total_amount_to_pay'], 2)
                ];
            }
            return print_r(json_encode([
                'is_error' => false,
                'message' => "Success",
                'data' => $result
            ]));
        }
    }

    public function totdays_profit()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return json_encode([
                'is_error' => true,
                'message' => "Please login",
                'data' => []
            ]);
        }

        $today_date = date('Y-m-d');
        $db = \Config\Database::connect();

        // Fetch expense
        $expense = $db->table('expenses')
            ->select('SUM(amount) as total_amount')
            ->where('DATE(expenses_date)', $today_date)
            ->get()
            ->getRowArray();

        $expense_amount = isset($expense['total_amount']) ? (float)$expense['total_amount'] : 0.0;

        // Fetch sales
        $sales = $db->table('orders')
            ->select('SUM(final_total) as total_amount')
            ->where('DATE(created_at)', $today_date)
            ->get()
            ->getRowArray();

        $sales_amount = isset($sales['total_amount']) ? (float)$sales['total_amount'] : 0.0;

        // Fetch purchases
        $purchase = $db->table('purchases')
            ->select('SUM(total) as total_amount')
            ->where('DATE(created_at)', $today_date)
            ->get()
            ->getRowArray();

        $purchase_amount = isset($purchase['total_amount']) ? (float)$purchase['total_amount'] : 0.0;

        $num = $sales_amount - $purchase_amount - $expense_amount;
        $num = $num < 0 ? 0 : $num;

        $profit =  number_format($num, 2);


        return json_encode([
            'is_error' => false,
            'message' => "Success",
            'data' => [
                "profit" => $profit,
            ]
        ]);
    }
}
