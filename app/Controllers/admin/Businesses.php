<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Users_packages_model;

use function PHPUnit\Framework\fileExists;

class Businesses extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
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
            $data['page'] = FORMS . "business";
            $data['title'] = "Businesses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $data["is_team_member"] = $this->ionAuth->isTeamMember();
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function save_business()
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


            $allow = '0';
            if (isset($_POST['business_id']) && !empty($_POST['business_id'])) {
                $allow = '1';
            }

            $users_package_model = new Users_packages_model();
            $id = $this->ionAuth->getUserId();
            $users_packages = $users_package_model->get_package($id);
            $no_of_buinesses = array_column($users_packages, 'no_of_businesses');
            $business_model = new Businesses_model();

            if (isset($_POST) && !empty($_POST)) {
                $icon =  $this->request->getVar('icon');
                $old_icon =  $this->request->getVar('old_icon');
                $business_id = (isset($_POST['business_id'])) ? $_POST['business_id'] : "";
                $this->validation->setRules([
                    'name' => 'required',
                    'description' => 'required|min_length[3]|max_length[255]',
                    'address' => 'required',
                    'email' => 'required|valid_email',
                    'contact' => 'required',
                    'tax_name' => 'required',
                    'tax_value' => 'required',
                    'bank_details' => 'required',
                ]);
                $path = './public/uploads/business/';
                if (!empty($_FILES['icon']) && isset($_FILES['icon'])) {
                    $file =  $this->request->getFile('icon');
                    if ($file->isValid()) {
                        if ($file->move($path)) {
                            if (fileExists($old_icon) && !empty($old_icon)) {
                                unlink($old_icon);
                            }
                            $icon = 'public/uploads/business/' . $file->getName();
                        }
                    } else {
                        $icon = $old_icon;
                    }
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
                    if (isset($_POST['status'])) {
                        $status = "1";
                    } else {
                        $status = "0";
                    }

                    $business = array(
                        'id' => $business_id,
                        'user_id' => $id,
                        'name' => $this->request->getVar('name'),
                        'icon' => $icon,
                        'description' => $this->request->getVar('description'),
                        'address' => $this->request->getVar('address'),
                        'email' => $this->request->getVar('email'),
                        'contact' => $this->request->getVar('contact'),
                        'website' => $this->request->getVar('website'),
                        'tax_name' => $this->request->getVar('tax_name'),
                        'tax_value' => $this->request->getVar('tax_value'),
                        'bank_details' => $this->request->getVar('bank_details'),
                        'status' => $status
                    );
                    $business_model->save($business);
                    $response = [
                        'error' => false,
                        'message' => 'Business Saved succesfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
        }
    }

    public function business_table()
    {
        $business_model = new Businesses_model();
        $user_id = $this->ionAuth->getUserId();
        $id = 0;
        if($this->ionAuth->isTeamMember()){
            $id = get_vendor_for_teamMember($user_id);
        }else{
            $id = session('user_id');
        }

        $total = $business_model->count_of_businesses($id);
        $businesses = $business_model->get_businesses($id);
        $data['businesses'] = (isset($businesses)) ? $businesses : "";
        $i = 0;
        foreach ($businesses as $business) {
            if ($business['status'] == "1") {
                $business['status'] = "<span class='badge badge-primary'>Active</span>";
            } else {
                $business['status'] = "<span class='badge ' style = 'background-color:#ed1307'>Deactive</span>";
            }
            $business_id = $business['id'];
            if ($business['default_business'] == "1") {
                $default_business = ' <label class="custom-switch default_business"><input type="radio" name="default_business" data-id = "' . $business_id . '"  value="' . $business_id . '" onclick = "update_default_business(this)" class="custom-switch-input" checked><span class="custom-switch-indicator"></span></label>';
            } else {
                $default_business = ' <label class="custom-switch default_business"><input type="radio" name="default_business" data-id = "' . $business_id . '" value="' . $business_id . '" onclick = "update_default_business(this)" class="custom-switch-input"><span class="custom-switch-indicator"></span></label>';
            }
            $business['icon'] = " <a href = '" . base_url('') . "/" . $business['icon'] . "' data-lightbox = 'image-1' ><img class='img-fluid' src='" . base_url('') . "/" . $business['icon'] . "'> </a> ";
            $edit_business = "<button onclick='edit_business(this)'  data-business_id=" . $business_id . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></button>";
            $rows[$i] = [
                'id' => $business['id'],
                'name' =>  $business['name'],
                'icon' => $business['icon'],
                'description' => $business['description'],
                'address' => $business['address'],
                'contact' => $business['contact'],
                'tax_name' => $business['tax_name'],
                'tax_value' => $business['tax_value'],
                'bank_details' => $business['bank_details'],
                'status' => $business['status'],
                'email' => $business['email'],
                'website' => $business['website'],
                'deafault_business' => $default_business,
                'action' => $edit_business
            ];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
    public function update_default_business()
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
        
        $business_id = $_GET['id'];
        $vendor_id = $this->ionAuth->getUserId();
        $default_business = $_GET['default_business'];
        $business_model = new Businesses_model();
        $businesses = $business_model->get_businesses($vendor_id);
        $default_business_id = 0 ;
        $default_business_name = '';
        if (is_array($businesses)) {
            foreach ($businesses as $business) {
                if ($business['default_business'] == 1) {
                    $default_id = $business['id'];
                    if ($business_id != $default_id) {
                        $default_business_id = $business_id;
                        $default_business_name = $business['name'];
                        update_details(['default_business' => $default_business], ['id' => $business_id], 'businesses');
                        update_details(['default_business' => "0"], ['id' => $default_id], 'businesses');
                    }
                } else {
                    update_details(['default_business' => $default_business], ['id' => $business_id], 'businesses');
                }
            }
            $response = [
                'error' => false,
                'message' => "Default business updated successfully!",
                'data' => []
            ];
            $this->session->set('business_id', $default_business_id);
            $this->session->set('business_name', $default_business_name);
        } else {

            $response = [
                'error' => true,
                'message' => "default business can't be updated",
                'data' => []
            ];
        }
        return $this->response->setJSON($response);
    }

    public function edit_business()
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $business_id = $_GET['id'];
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
            $data['page'] = FORMS . "business";
            $data['title'] = "Edit Business - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $business_array = fetch_details('businesses', ['id' => $business_id]);
            $business =  (isset($business_array[0])) ? $business_array[0] : $business_array = [];
            $data['business'] = $business;
            $data['user'] = $this->ionAuth->user($id)->row();
            $response = [
                'error' => false,
                'business' => $business,
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }
}
