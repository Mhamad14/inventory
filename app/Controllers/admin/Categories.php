<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Categories_model;

class Categories extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
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
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'categories';
            $data['title'] = "Categories - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $data['id'] = $id;
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

            $category_model = new Categories_model();
            $data['categories'] = $category_model->get_categories($id, $business_id);
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function save_categories()
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
           

            if (isset($_POST) && !empty($_POST)) {
                if (isset($_POST['vendor_id']) && $_POST['vendor_id'] == $_SESSION['user_id']) {
                    $category_model = new Categories_model();
                    if (isset($_POST['category_id']) && !empty($_POST['category_id'])) {
                        $this->validation->setRules([
                            'name' => 'required',
                        ]);
                    } else {
                        $this->validation->setRules([
                            'name' => 'required|is_unique[categories.name]',
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
                        $category_id = isset($_POST['category_id']) ? $_POST['category_id'] : "";
                        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";
                        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

                     
                        if( strlen($category_id) > 0){
                            $categories =  $category_model->findAll();
                            $category = $category_model->find($category_id);
                            

                            $parent_category_id = $category['parent_id'];
                            $parent_category =  $category_model->find($parent_category_id);
                            $parent_category_name =  ($parent_category)  ? trim( $parent_category['name']) : "";

                            $current_category_name = trim( $this->request->getVar('name'));

                            foreach ($categories as $key ) {
                                if($key['id'] == $category_id ){
                                    if($parent_category_name === $current_category_name ){
                                        $response = [
                                            'error' => true,
                                            'message' => ['name' => "The name field must contain a unique value." ],
                                            'data' => []
                                        ];
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }   
                                }else{
                                    if( trim($key['name']) === $current_category_name ){
                                        $response = [
                                            'error' => true,
                                            'message' => ['name' => "The name field must contain a unique value." ],
                                            'data' => []
                                        ];
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }   
                                }
                            }  
                            
                        }

                        if (isset($_POST['status']) && !empty($_POST['status'])) {
                            $status = "1";
                        } else {
                            $status = "0";
                        }
                        $categories = array(
                            'id' => $category_id,
                            'parent_id' => $this->request->getVar('parent_id'),
                            'vendor_id' => $vendor_id,
                            'business_id' => $business_id,
                            'name' => $this->request->getVar('name'),
                            'status' => $status,
                        );

                        $category_model->save($categories);
                        $response = [
                            'error' => false,
                            'message' => 'Category added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Category added successfully';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                }
            } else {
                return redirect()->to('admin/categories');
            }
        }
    }
    public function category_table()
    {
        $category_model = new Categories_model();
        $user_id = $_SESSION['user_id'];
        $id = 0;
        if($this->ionAuth->isTeamMember()){
            $id = get_vendor_for_teamMember($user_id);
        }else{
            $id = $user_id;
        }
        $business_id = isset($_SESSION['business_id']) ? $_SESSION[ 'business_id'] : "";
        $rows = [];
        $categories =  $category_model->get_categories($id, $business_id);
        $total =  $category_model->count_of_categories($id, $business_id);
        $i = 0;
        foreach ($categories as $category) {
            $category_id = $category['id'];
            if ($category['status'] == 1) {
                $status = "<span class='badge badge-primary' >Active</span>";
            } else {
                $status = "<span class='badge badge-danger' >Deactive</span>";
            }
            $edit_category = ($category['vendor_id'] == $id) ? "<a href=" . site_url('admin/categories/edit_category') . "/" . $category_id . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></a>" : "";
            $rows[$i] = ['id' => $category['id'], 'vendor_id' => $category['vendor_id'], 'parent_id' => $category['parent_id'], 'name' => ucwords($category['name']), 'status' => $status, 'action' => $edit_category];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = $rows;
        if(count($array['rows']) <= 0){
            $array['total'] = 0;
        }
        echo json_encode($array);
    }
    public function edit_category($category_id = "")
{
    if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
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
        $data['page'] = FORMS . "categories";
        $data['title'] = "Edit Category - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $user_id = $_SESSION['user_id'];
        $id = 0;
        if ($this->ionAuth->isTeamMember()) {
            $id = get_vendor_for_teamMember($user_id);
        } else {
            $id = $user_id;
        }
        $data['id'] = $id; // Set the 'id' key here
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        $category_model = new Categories_model();
        $data['category'] = $category_model->find($category_id);
        $data['category_status'] = $data['category']['status'];

        $data['categories'] = $category_model->get_categories($id, $business_id);
        $parent_id = $data['category']['parent_id'];
        $data['parent_category'] = $category_model->find($parent_id);
        $data['user'] = $this->ionAuth->user($id)->row();
        return view("admin/template", $data);
    }
}
}
