<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\EmployeeModel;
use App\Models\PositionsModel;

class Employees extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $session;
    protected $configIonAuth;
    protected $data;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'employee']);
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
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . 'employees_table';
            $data['title'] = "employees - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function employees_table()
    {
        $employees_model = new EmployeeModel();
        $business_id = $this->session->get('business_id');
        // Get total records and paginated data
        $result = $employees_model->get_employees($business_id);
        $totalRecords = $result['total'];
        $employees = $result['data'];

        $rows = [];
        foreach ($employees as $employee) {
            $employee_id = $employee['id'];
            $edit = "<a href=" . site_url('admin/employees/edit') . "/" . $employee_id . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a>" . " ";
            $rows[] = [
                'id' => $employee['id'],
                'name' => ucwords($employee['name']),
                'address' => $employee['address'],
                'position' => $employee['pname'],
                'mobile' => $employee['contact_number'],
                'salary' => currency_location(decimal_points($employee['salary'])),
                'action' => $edit
            ];
        }


        // Return the total number of records and the rows for the current page
        echo json_encode([
            'total' => $totalRecords,
            'rows' => $rows
        ]);
    }
    public function new()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $session = session();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            if (isset($_GET['edit_id'])) {
                $this->data['fetched_data'] = fetch_details('employees', ['busniess_id' => $_GET['edit_id']]);
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . 'employees';
            $data['title'] = "employees - " . $company_title;
            $data['from_title'] = "create_employees";
            $positions_model = new positionsModel();
            $data['positions'] = $positions_model->findAll();
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }


    public function create()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            echo "<script>alert('Unauthorized access.');window.location='" . site_url('login') . "';</script>";
            exit;
        }

        // CSRF protection
        if (! $this->request->is('post') || ! $this->request->getPost(csrf_token())) {
            echo "<script>alert('Invalid request.');window.history.back();</script>";
            exit;
        }

        // Only allow AJAX or POST requests
        if (! $this->request->isAJAX() && ! $this->request->is('post')) {
            echo "<script>alert('Invalid request method.');window.history.back();</script>";
            exit;
        }

        // Sanitize input
        $post = $this->request->getPost([
            'name', 'position_id', 'address', 'contact_number', 'salary', 'busniess_id'
        ]);
        $post = array_map('strip_tags', $post);

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'position_id' => 'required|integer',
            'address' => 'required',
            'contact_number' => 'required|numeric',
            'salary' => 'required|decimal',
            'busniess_id' => 'required|integer',
        ];

        if (! $this->validate($rules)) {
            $errors = implode('\n', $this->validator->getErrors());
            echo "<script>alert('Validation failed:\\n$errors');window.history.back();</script>";
            exit;
        }

        // Prevent privilege escalation: check business_id matches session
        $sessionBusinessId = $this->session->get('business_id');
        if ((int)$post['busniess_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized business ID.');window.history.back();</script>";
            exit;
        }

        // Prevent duplicate employee (optional, based on your logic)
        $employeeModel = new EmployeeModel();
        $existing = $employeeModel->where([
            'contact_number' => $post['contact_number'],
            'busniess_id' => $post['busniess_id']
        ])->first();
        if ($existing) {
            echo "<script>alert('duplicate phone number.');window.history.back();</script>";
            exit;
        }

        // Insert employee
        $employeeModel->insert($post);

        echo "<script>alert('Employee created successfully');window.location='" . site_url('/admin/employees') . "';</script>";
        exit;
    }

    public function show($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Display a single employee's details
            return view('admin/employees/show', ['id' => $id]);
        }
    }

    public function edit($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Show form to edit an existing employee
            return view('admin/employees/edit', ['id' => $id]);
        }
    }

    public function update($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Handle the update of an existing employee
            $data = $this->request->getPost();
            // Update employee logic here
            return redirect()->to('/admin/employees');
        }
    }
    public function delete($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Handle the deletion of an employee
            // Delete employee logic here
            return redirect()->to('/admin/employees');
        }
    }
    private function validateUserPermission()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }
        if (isset($session['business_id'])) {
            return handleMissingBusiness();
        }
    }
    private function getData($tableName, $tableData, $page)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $business_id = session('business_id');
            $languages = getLanguages();
            return $data = [
                'version' => getAppVersion(),
                'code' => session('lang') ?? 'en',
                'current_lang' => session('lang') ?? 'en',
                'languages_locale' => $languages,
                'business_id' => $business_id,
                'page' => $page,
                'title' => "Customers - " . $company_title,
                'from_title' => 'Customer Details',
                'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
                'meta_description' => "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems",
                $tableName => $tableData,
                'user' => $this->ionAuth->user(session('user_id'))->row(),
            ];
        }
    }
}
