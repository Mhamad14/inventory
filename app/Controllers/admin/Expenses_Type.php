<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Expenses_Type_model;

class Expenses_Type extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
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
            $data['page'] = FORMS . 'expenses_type';
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['title'] = "Expenses Type - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['id'] = $id;
            $expenses_type_model = new Expenses_Type_model();
            $data['expenses_type'] = $expenses_type_model->get_expenses_type($id);
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function save_expenses_type()
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
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'title' => 'required',
                    'description' => 'required',
                    'expenses_type_date' => 'required',
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

                $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";
                $id = isset($_POST['id']) ? $_POST['id'] : "";
                $expenses_type = array(
                    'id' => $id,
                    'title' => $this->request->getVar('title'),
                    'vendor_id' => $vendor_id,
                    'description' => $this->request->getVar('description'),
                    'expenses_type_date' => $this->request->getVar('expenses_type_date'),
                );
                $expenses_type_model = new Expenses_Type_model();
                $expenses_type_model->save($expenses_type);
                $response = [
                    'error' => false,
                    'message' => 'Expense Type added successfully',
                    'data' => []
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $_SESSION['toastMessage'] = 'Expenses Type added successfully';
                $_SESSION['toastMessageType']  = 'success';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return $this->response->setJSON($response);
            }
        }
    }

    public function expenses_type_table()
    {
        $expenses_type_model = new Expenses_Type_model();
        $id = $_SESSION['user_id'];
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";
        $expenses_type =  $expenses_type_model->get_expenses_type($vendor_id);
        $total =  $expenses_type_model->count_of_expenses_type();
        $i = 0;
        foreach ($expenses_type as $type) {
            $type_id = $type['id'];
            $edit_type = ($type['vendor_id'] == $id) ? "<a href=" . site_url('admin/expenses_type/edit_expenses_type') . "/" . $type['id'] . " class='btn btn-primary btn-sm' ><i class='bi bi-pencil'></i></a>" : "";
            $rows[$i] = ['id' => $type['id'], 'vendor_id' => $type['vendor_id'], 'expenses_type_date' => date_formats(strtotime($type['expenses_type_date'])), 'title' => ucwords($type['title']), 'description' => $type['description'], 'action' => $edit_type];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = $rows;

        echo json_encode($array);
    }
    public function edit_expenses_type($type_id = "")
    {
        if(!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
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
            $data['page'] = FORMS . "expenses_type";
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['title'] = "Edit EXpenses Type - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $data['id'] = $_SESSION['user_id'];
            $id = $data['id'];
            $expenses_type_model = new Expenses_Type_model();
            $data['type'] = $expenses_type_model->find($type_id);
            $data['expenses_type'] = $expenses_type_model->get_expenses_type($id);
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
}
