<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Delivery_boys_model;

class Delivery_Boys extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    protected $data;
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'delivery_boys';
            $data['title'] = "Delivery Boys - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['vendor_id'] = $id;
            $businesses = fetch_details("businesses", ['user_id' => $_SESSION['user_id']]);
            $data['businesses'] = isset($businesses) ? $businesses : "";
            $data['user'] = $this->ionAuth->user($id)->row();
            $data['business_id'] = $business_id;
            return view("admin/template", $data);
        }
    }

    protected $validationListTemplate = 'list';
    protected $ionAuthModel;
    public function save()
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {

            $id = $this->ionAuth->getUserId();



            if (isset($_POST) && !empty($_POST)) {
                if (isset($_POST['delivery_boy_id']) && !empty($_POST['delivery_boy_id'])) {
                    $delivery_boys_model = new Delivery_boys_model();
                    $id = $_POST['delivery_boy_id'];
                    $user = $this->ionAuth->user($id)->row();
                    $this->validation->setRule('first_name', lang('Auth.edit_user_validation_fname_label'), 'trim|required');
                    $this->validation->setRule('identity', "Mobile", 'trim|required');
                    $this->validation->setRule('email', lang('Auth.edit_user_validation_email_label'), 'trim|required');
                    $this->validation->setRule('business_id', 'business', 'required');

                    if ($this->request->getPost('password')) {
                        $this->validation->setRule('password', lang('Auth.edit_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
                    }

                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                        $data = [
                            'first_name' => $this->request->getPost('first_name'),
                            'email'  => $this->request->getPost('email'),
                            'identity'  => $this->request->getPost('identity'),
                        ];
                        // update the password if it was posted
                        if ($this->request->getPost('password')) {
                            $data['password'] = $this->request->getPost('password');
                        }
                        if (isset($_POST['status']) && !empty($_POST['status']) && $_POST['status'] == "on") {
                            $status = "1";
                        } else {
                            $status = "0";
                        }
                        
                        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {

                            $user_businesses =   fetch_details('delivery_boys', ['user_id' => $id]);
                            foreach ($user_businesses as $key) {
                                $delivery_boys_model->delete($key['id']);
                            }
                            for ($i = 0; $i < count($_POST['business_id']); $i++) {
                                $business_id = $_POST['business_id'][$i];
                                $permissions = [
                                    'customer_permission' => $this->request->getVar('customer_permission') == "on" ? "1" : "0",
                                    'transaction_permission' => $this->request->getVar('transaction_permission') == "on" ? "1" : "0",
                                    'orders_permission' => $this->request->getVar('orders_permission')  == "on" ? "1" : "0"
                                ];

                                $delivery_boys = [
                                    'vendor_id' => $_SESSION['user_id'],
                                    'business_id' => $business_id,
                                    'user_id' => $id,
                                    'status' => $status,
                                    'permissions' => json_encode($permissions)
                                ];

                                $delivery_boys_model->insert($delivery_boys);
                            }
                        }
                        $this->ionAuth->update($user->id, $data);
                        $response = [
                            'error' => false,
                            'message' => 'Delivery boy updated successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Delivery boy updated successfully';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                } else {

                    $tables                        = $this->configIonAuth->tables;
                    $identityColumn                = $this->configIonAuth->identity;
                    $this->data['identity_column'] = $identityColumn;

                    $this->validation->setRule('first_name', lang('Auth.create_user_validation_fname_label'), 'trim|required');
                    $this->validation->setRule('identity', lang('Auth.create_user_validation_identity_label'), 'trim|required|is_unique[' . $tables['users'] . '.' . $identityColumn . ']');


                    $this->validation->setRule('email', lang('Auth.create_user_validation_email_label'), 'required|trim|valid_email|is_unique[' . $tables['users'] . '.email]');

                    // $this->validation->setRule('phone', lang('Auth.create_user_validation_phone_label'), 'trim');
                    $this->validation->setRule('password', lang('Auth.create_user_validation_password_label'), 'required|min_length[' . $this->configIonAuth->minPasswordLength . ']');
                    $this->validation->setRule('business_id', 'business', 'required');

                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {

                        $email    =  strtolower($this->request->getPost('email'));
                        $identity = ($identityColumn === 'email') ? $email : $this->request->getPost('identity');
                        $password = $this->request->getPost('password');
                        $group_id_arry = fetch_details("groups", ['name' => 'delivery_boys'], "id");
                        $group_id = [$group_id_arry[0]['id']];
                        $additionalData = [
                            'first_name' => $this->request->getPost('first_name'),
                            'phone'      => $this->request->getPost('phone'),
                        ];
                    }
                    if ($this->request->getPost() && $this->validation->withRequest($this->request)->run()) {
                        $delivery_boys_model = new Delivery_boys_model();
                        $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);
                        if (isset($_POST['status']) && !empty($_POST['status'])) {
                            $status = "1";
                        } else {
                            $status = "0";
                        }

                        if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
                            for ($i = 0; $i < count($_POST['business_id']); $i++) {
                                $business_id = $_POST['business_id'][$i];
                                $permissions = [
                                    'customer_permission' => $this->request->getVar('customer_permission') == "on" ? "1" : "0",
                                    'transaction_permission' => $this->request->getVar('transaction_permission') == "on" ? "1" : "0",
                                    'orders_permission' => $this->request->getVar('orders_permission')  == "on" ? "1" : "0"
                                ];


                                $delivery_boys = [
                                    'vendor_id' => $_SESSION['user_id'],
                                    'business_id' => $business_id,
                                    'user_id' => $id,
                                    'status' => $status,
                                    'permissions' => json_encode($permissions),
                                ];
                                $delivery_boys_model->save($delivery_boys);
                            }
                        }

                        // check to see if we are creating the user
                        // redirect them back to the admin page  
                        $response = [
                            'error' => false,
                            'message' => 'Delivery boy added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Delivery boy added successfully';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                }
                $this->data['message'] = $this->validation->getErrors() ? $this->validation->listErrors($this->validationListTemplate) : ($this->ionAuth->errors($this->validationListTemplate) ? $this->ionAuth->errors($this->validationListTemplate) : $this->session->getFlashdata('message'));
                $response['error'] = true;
                $response['csrf_token'] =  csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['message'] = $this->validation->getErrors();
                return  $this->response->setJSON($response);
            } else {
                return redirect()->back()->withInput();
            }
        }
    }

    public function count($id = "")
    {
        if (!empty($_GET)) {
            $id = $_GET['id'];
            $delivery_boys_model = new Delivery_boys_model();
            $assigned_businesses = $delivery_boys_model->assigned_businesses($id);
            $response['business_id'] = $assigned_businesses;
            $response['error'] = false;
            return json_encode($response);
        }
    }

    public function get_delivery_boy()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $response['error'] = true;
            $response['csrf_token'] =  csrf_token();
            $response['csrf_hash'] = csrf_hash();
            $response['message'] = "Please login";
            return  $this->response->setJSON($response);
        } else {
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
            $id = $this->request->getPost('id');
            $db = \config\Database::connect();

            $delivery_boy = $db->table('delivery_boys')->where(['id' => $id])->get()->getResultArray();
            if (empty($delivery_boy)) {
                $response['error'] = true;
                $response['csrf_token'] =  csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['message'] = "Delivery Boy not found ";
                return  $this->response->setJSON($response);
            }
            $user_data = $db->table('users')->where(['id' => $delivery_boy[0]['user_id']])->get()->getResultArray();
            if (empty($user_data)) {
                $response['error'] = true;
                $response['csrf_token'] =  csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['message'] = "Delivery Boy not found ";
                return  $this->response->setJSON($response);
            }
            $business_list = $db->table('businesses')->where(['user_id' => $vendor_id])->get()->getResultArray();
            $business_data = [] ;
            foreach($business_list as $business){
                $business_data[]= [
                    "class_name" => "business_".$business['id']
                ];
            }
           
            $data = [
                'name' => $user_data[0]['first_name'],
                'mobile' => $user_data[0]['mobile'],
                'email' => $user_data[0]['email'],
                'permissions' =>  $delivery_boy[0]['permissions'],
                'buisness_ids' => $delivery_boy[0]['business_id'],
                'status' => $delivery_boy[0]['status'],
                'id' =>  $delivery_boy[0]['id'],
                'user_id' => $delivery_boy[0]['user_id'],
                'business_data' => $business_data
            ];

            $response['error'] = false;
            $response['csrf_token'] =  csrf_token();
            $response['csrf_hash'] = csrf_hash();
            $response['data'] = $data;
            $response['message'] = "Delivery Boy not found ";
            return  $this->response->setJSON($response);
        }
    }
    public function delivery_boys_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $delivery_boys_model = new Delivery_boys_model();
        $business_model = new Businesses_model();
        $total = $delivery_boys_model->count_of_delivery_boys($business_id);
        $delivery_boys = $delivery_boys_model->get_delivery_boys($business_id);
        $i = 0;
        foreach ($delivery_boys as $delivery_boy) {
            $delivery_boy_id = $delivery_boy['user_id'];
            $assigned_business_ids = $delivery_boys_model->assigned_businesses($delivery_boy_id);
            for ($i = 0; $i < count($assigned_business_ids); $i++) {
                $delivery_boy['assigned_business_ids'][$i] = isset($assigned_business_ids[$i]) ? $assigned_business_ids[$i]['business_id'] : "";
            }
            $name  = $delivery_boy['first_name'];
            $email = $delivery_boy['email'];
            if ($delivery_boy['status'] == 1) {
                $status = "<span class='badge badge-primary'>Active</span>";
            } else {
                $status = "<span class='badge ' style = 'background-color:#ed1307'>Deactive</span>";
            }
            $business_string = "";
            $business_names = [];
            foreach ($delivery_boy['assigned_business_ids'] as $business_id) {
                $business = $business_model->find($business_id);
                $business_names[] = $business['name'];
            }
            $business_string = implode(", ", $business_names);

            $permissions = json_decode($delivery_boy['permissions']);
            $permission_names = [];

            foreach ($permissions as $permission_name => $permission_value) {
                if ($permission_value == 1) {
                    $permission_names[] = $permission_name;
                }
            }

            $permissions_string = implode(", ", str_replace("_", " ", $permission_names));

            $rows[] = [
                'id' => $delivery_boy_id,
                'name' =>  ucwords($name),
                'mobile' => $delivery_boy['mobile'],
                'email' => $email,
                'assigned_b_id' => $business_string,
                'permissions' => $permissions_string,
                'status' => $status,
                'active' => $delivery_boy['status'],
                'action' => "
                    <div>
                        <button class=\"btn btn btn-primary set_field_delivery_boy\" data-id=\"" . $delivery_boy['id'] . "\" data-route=\"" .  base_url("admin/delivery_boys/get-delivery-boy")  . "\">
                            <i class=\"fa fa-pen\"></i>
                        </button>
                    </div>
                ",
            ];
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = [];
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
}
