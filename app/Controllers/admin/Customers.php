<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Customers_model;
use App\Models\Orders_model;

class Customers extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $session;
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
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (! isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                // business id is not set 
                $business_model = new Businesses_model();
                $allbusiness = $business_model->findAll();
                if( empty($allbusiness)){
                    session()->setFlashdata('message', 'Please create a business !');
                    session()->setFlashdata('type', 'error');
                    return redirect()->to('admin/businesses');
                }else{
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'customers';
            $data['title'] = "Customers - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $customers = fetch_details("customers", ['business_id' => $business_id]);
            $data['customers'] = isset($customers) ? $customers : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function save_status()
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $status = subscription();
            if ($status == 'active') {
                if (isset($_POST) && !empty($_POST)) {
                    $this->validation->setRules([
                        'customer_id' => 'required|trim',
                        'status' => 'required|trim'
                    ]);
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

                    update_details(['status' => $_POST['status']], ['user_id' => $_POST['customer_id']], 'customers');
                    $response = [
                        'error' => false,
                        'message' => 'Customers status updated successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $_SESSION['toastMessage'] = 'Customers status updated successfully';
                    $_SESSION['toastMessageType']  = 'success';
                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                }
            }
            
        }
    }
// chnaged this function a bit
    public function customers_table()
{
    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
    $customers_model = new Customers_model();
    $customers = $customers_model->get_customers_details($business_id);
    $total = $customers_model->count_of_customers($business_id);
    
    $i = 0;
    foreach ($customers as $customer) {
        $customer_id = $customer['user_id'];
        if ($customer['status'] == 1) {
            $status = "<span class='badge badge-primary'>Active</span>";
        } else {
            $status = "<span class='badge' style='background-color:#ed1307'>Deactive</span>";
        }
        
        $name = ucwords($customer['first_name']);
        $edit_customer = "<a href='javascript:void(0)' data-id='{$customer_id}' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Status update' data-bs-toggle='modal' data-bs-target='#customer_status'><i class='bi bi-pen'></i></a>";
        $details_btn = "<a href='".base_url('admin/customers/details/'.$customer_id)."' class='btn btn-info btn-sm'><i class='bi bi-eye'></i></a>";
        
        $rows[$i] = [
            'id' => $customer['user_id'],
            'customer_id' => $customer['id'],
            'name' => $name,
            'email' => $customer['email'],
            'mobile' => $customer['mobile'],
            'balance' => currency_location(decimal_points($customer['balance'])),
            'debit' => currency_location(decimal_points($customer['debit'] ?? 0)),
            'status' => $status,
            'active' => $customer['status'],
            'action' => $details_btn . ' ' . $edit_customer
        ];
        $i++;
    }
    
    $array['total'] = $total[0]['total'];
    $array['rows'] = $rows ?? [];
    echo json_encode($array);
}
//added this function for knwing debit
public function details($user_id)
{
    if (!$this->ionAuth->loggedIn()) {
        return redirect()->to('login');
    }

    $business_id = $_SESSION['business_id'];
    $customer_model = new Customers_model();
    $orders_model = new Orders_model();

    $data = [
        'customer' => $this->ionAuth->user($user_id)->row(),
        'orders' => $orders_model->where('customer_id', $user_id)
                                 ->where('business_id', $business_id)
                                 ->where("(payment_status = 'unpaid' OR payment_status = 'partially_paid')")
                                 ->findAll(),
        'debit' => $customer_model->calculate_customer_debit($user_id, $business_id),
        'page' => 'admin/customers/details',
        'title' => 'Customer Details'
    ];

    return view('admin/template', $data);
}
}
