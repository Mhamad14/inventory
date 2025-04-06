<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Customers_model;

class Customers extends BaseController
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
            $data['page'] = FORMS . "customers";
            $data['title'] = "Customers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $delivery_boy_businesses = fetch_details('delivery_boys', ['user_id' => $id]);
            foreach ($delivery_boy_businesses as $business) {
                $businesses[] = fetch_details('businesses', ['id' => $business['business_id']]);
            }
            $data['delivery_boy_id'] = $id;
            $data['businesses'] = $businesses;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("delivery-man/template", $data);
        }
    }
    public function customers_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $customers_model = new Customers_model();
        $customers = $customers_model->get_customers_details($business_id);
        $total = $customers_model->count_of_customers($business_id);
        $i = 0;
        foreach ($customers as $customer) {
            $customer_id = $customer['id'];
            if ($customer['status'] == 1) {
                $status = "<span class='badge badge-primary'>Active</span>";
            } else {
                $status = "<span class='badge ' style = 'background-color:#ed1307'>Deactive</span>";
            }
            $name =  ucwords($customer['first_name']);
            $edit_customer = "<a href='javascript:void(0)' data-id=" . $customer_id . " class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='Status update' data-bs-toggle='modal' data-bs-target='#customer_status'><i class='bi bi-pen'></i></a>";
            $rows[$i] = [
                'id' => $customer['id'],
                'name' => $name,
                'email' => $customer['email'],
                'mobile' => $customer['mobile'],
                'balance' => $customer['balance'],
                'status' => $status,
                'action' => $edit_customer
            ];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
}
