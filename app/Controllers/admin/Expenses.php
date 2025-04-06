<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Expenses_model;
use App\Models\Expenses_Type_model;
use App\Models\Vendors_model;

class Expenses extends BaseController
{
    public $expenses_model;
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
        $this->expenses_model = new Expenses_model();
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

            $user_id = session('user_id');
            $vendor_id = session('user_id');
            if ($this->ionAuth->isTeamMember()) {
                $vendor_id = get_vendor_for_teamMember($user_id);
                if (! userHasPermission('expenses', 'can_create',  $user_id)) {
                    session()->setFlashdata("permission_error", "You do not have permission to access");
                    session()->setFlashdata("type", "error");
                    return redirect()->back();
                }
            }

            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($user_id)->row();

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
            $data['page'] = VIEWS . "expenses_table";
            $data['title'] = "Expenses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";

            $expenses_type_model = new Expenses_Type_model();
            $data['expenses_type'] =  $expenses_type_model->get_expenses_type($vendor_id);
            return view("admin/template", $data);
        }
    }

    public function add()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("admin/businesses");
            } else {
                if (isset($_SESSION['business_id'])) {
                    if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                        return redirect()->to("admin/businesses");
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
                $data['page'] = FORMS . "expenses";
                $data['title'] = "Add Expenses - " . $company_title;
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
                $vendor_id = $_SESSION['user_id'];
                $id = $_SESSION['user_id'];
                $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                $data['business_id'] = $business_id;
                $data['user'] = $this->ionAuth->user($id)->row();
                $expenses_type_model = new Expenses_Type_model();
                $data['expenses_type'] =  fetch_details('expenses_type', ['vendor_id' => $vendor_id], ['id', 'title']);
                $expenses = new Expenses_model();
                $data['expenses'] =  $expenses->get_expenses($business_id);
                return view("admin/template", $data);
            }
        }
    }

    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {


            if (isset($_POST) && !empty($_POST)) {

                $this->validation->setRules([
                    'expenses_type' => [
                        'label' => 'Expenses Type',
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'The {field} field is required.'
                        ]
                    ],
                    'expenses_date' => [
                        'label' => 'Expenses Date',
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'The {field} field is required.'
                        ]
                    ],
                    'amount' => [
                        'label' => 'Amount',
                        'rules' => 'required|decimal',
                        'errors' => [
                            'required' => 'The {field} field is required.',
                            'decimal' => 'The {field} field must contain a valid decimal number.'
                        ]
                    ],
                    'note' => [
                        'label' => 'Note',
                        'rules' => 'required',
                        'errors' => [
                            'required' => 'The {field} field is required.'
                        ]
                    ],
                ]);

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

                    $id = isset($_POST['id']) ? $_POST['id'] : "";
                    $expenses = [
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'id' => $id,
                        'expenses_title' => $this->request->getVar('expenses_title'),
                        'note' => $this->request->getVar('note'),
                        'expenses_id' => $this->request->getVar('expenses_type'),
                        'expenses_date' => $this->request->getVar('expenses_date'),
                        'amount' => $this->request->getVar('amount'),
                    ];

                    $this->expenses_model->save($expenses);

                    $response = [
                        'error' => false,
                        'message' => 'Expense saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            }
        }

        return false;
    }



    public function expenses_table()
    {
        $user_id = session('user_id');
        $vendor_id = session('user_id');
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

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $expenses_model = new Expenses_model();
        $expenses_type_model = new Expenses_Type_model();
        $vendor_model = new Vendors_model();
        $business_model = new Businesses_model();
        $expenses = $expenses_model->get_expenses($vendor_id);
        $total = $expenses_model->count_of_expenses($where = [], $multipleWhere = []);
        $i = 0;
        $data['expenses_type'] =  fetch_details('expenses_type', ['vendor_id' => $vendor_id], ['id', 'title']);


        foreach ($expenses as $expense) {

            $id = $expense['id'];

            $edit_expense = "<a href=" . site_url('admin/expenses/edit_expenses') . "/" . $id . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a>" . " ";
            $expenses = $expenses_type_model->find($id);
            $expenses_name = $expense['title'];
            $business = $business_model->find($expense['business_id']);
            $business_name = $business['name'];

            $vendor = $vendor_model->find($expense['vendor_id']);
            $vendor_name = $vendor['first_name'];
            $rows[$i] = [

                'id' => $expense['id'],
                'note' => $expense['note'],
                'expenses_name' => $expenses_name,
                'amount' => currency_location(decimal_points($expense['amount'])),
                'expenses_date' => date_formats(strtotime($expense['expenses_date'])),
                'business_name' => $business_name,
                'vendor_name' => $vendor_name,
                'action' => $edit_expense,
                'expenses_type' => $expense['title'],
            ];
            $i++;
        }

        $array['total'] = $total[0]['total'];
        $array['rows'] = [];
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }


        echo json_encode($array);
    }

    public function edit($expenses_id = "")
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
            $user_id = session('user_id');
            $vendor_id = session('user_id');
            if ($this->ionAuth->isTeamMember()) {
                $vendor_id = get_vendor_for_teamMember($user_id);
                if (! userHasPermission('expenses', 'can_update',  $user_id)) {
                    session()->setFlashdata("permission_error", "You do not have permission to access");
                    session()->setFlashdata("type", "error");
                    return redirect()->back();
                }
            }

            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "expenses";
            $data['from_title'] = "edit_expenses";
            $data['title'] = "Edit Expenses - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";

            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['user'] = $this->ionAuth->user($user_id)->row();
            $expenses_type_model = new Expenses_Type_model();
            $data['expenses_type'] =  fetch_details('expenses_type', ['vendor_id' => $vendor_id], ['id', 'title']);
            $expenses = new Expenses_model();
            $data['expenses'] = $expenses->find($expenses_id);
            $data['selected_expenses_id'] = $data['expenses']['id'];
            return view("admin/template", $data);
        }
    }

    public function get_expenses_type()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $rules = [
                'search' => "max_length[255]"
            ];

            if ($this->request->getGet('limit')) {
                $rules['limit'] = 'numeric|greater_than_equal_to[1]|less_than[250]';
            }
            if ($this->request->getGet('offset')) {
                $rules['offset'] = 'numeric|greater_than_equal_to[0]';
            }

            $this->validation->setRules($rules);
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
                $user_id = session('user_id');
                $vendor_id = session('user_id');
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($user_id);
                    if (! userHasPermission('expenses', 'can_create',  $user_id)) {
                        session()->setFlashdata("permission_error", "You do not have permission to access");
                        session()->setFlashdata("type", "error");
                        return redirect()->back();
                    }
                }
                $limit = (!empty($this->request->getVar('limit'))) ? $this->request->getVar('limit') : 10;
                $offset = (!empty($this->request->getVar('offset'))) ? $this->request->getVar('offset') : 0;
                $sort = (!empty($this->request->getVar('sort'))) ? $this->request->getVar('sort') : 'id';
                $order = (!empty($this->request->getVar('order'))) ? $this->request->getVar('order') : 'DESC';
                $search = (!empty($this->request->getVar('search'))) ? $this->request->getVar('search') : '';

                $db = \Config\Database::connect();
                $builder = $db->table('	expenses_type');

                if (isset($search) && !empty($search)) {
                    $builder->groupStart();
                    $builder->orLike([
                        'title' => $search
                    ]);
                    $builder->groupEnd();
                }

                if ($limit != null && $limit != "") {
                    $builder = $builder->limit($limit, $offset);
                }


                $builder = $builder->where(['vendor_id' => $vendor_id]);
                $builder = $builder->orderBy($sort, $order);
                $res = $builder->get()->getResultArray();

                $response = [
                    'error' => true,
                    'message' => "Success",
                    'data' => $res
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
}
