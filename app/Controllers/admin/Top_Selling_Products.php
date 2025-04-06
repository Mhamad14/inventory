<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Orders_items_model;

class Top_Selling_Products extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $business_id = (isset($_SESSION['business_id']) && is_numeric($_SESSION['business_id'])) ? trim($_SESSION['business_id']) : "";

            if (empty($business_id) || check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("admin/businesses");
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
            $data['page'] = VIEWS . "top_selling_products";
            $data['title'] = "Top Selling Products - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $orders_items_model = new Orders_items_model();

            return view("admin/template", $data);
        }
    }

    public function top_selling_products_table()
    {
        $orders_items_model = new Orders_items_model();
        $top_selling_products = $orders_items_model->top_selling_products();
        echo json_encode($top_selling_products,);
    }
}
