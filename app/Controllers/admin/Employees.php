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

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'employee']);
        $this->session = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
            $business_model = new Businesses_model();
            $allbusiness = $business_model->findAll();
            $msg = empty($allbusiness) ? 'Please create a business !' : 'Please select a business !';
            session()->setFlashdata('message', $msg);
            session()->setFlashdata('type', 'error');
            return redirect()->to('admin/businesses');
        }

        $settings = get_settings('general', true);
        $user_id = $_SESSION['user_id'];
        $id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        $data = [
            'version' => fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'],
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'business_id' => $_SESSION['business_id'],
            'languages_locale' => fetch_details('languages', [], [], null, '0', 'id', 'ASC'),
            'page' => VIEWS . 'employees_table',
            'title' => "Employees - " . ($settings['title'] ?? ""),
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'currency' => $settings['currency_symbol'] ?? '₹',
            'user' => $this->ionAuth->user($id)->row(),
        ];

        return view("admin/template", $data);
    }

    public function employees_table()
    {
        $employees_model = new EmployeeModel();
        $business_id = $this->session->get('business_id');
        $result = $employees_model->get_employees($business_id);
        $rows = [];
        foreach ($result['data'] as $employee) {
            $edit = "<a href=" . site_url('admin/employees/edit') . "/" . $employee['id'] . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a> ";
            $rows[] = [
                'id' => $employee['id'],
                'name' => ucwords($employee['name']),
                'address' => $employee['address'],
                'position' => $employee['pname'],
                'position_id' => $employee['position_id'],
                'mobile' => $employee['contact_number'],
                'salary' => currency_location(decimal_points($employee['salary'])),
                'action' => $edit
            ];
        }
        echo json_encode([
            'total' => $result['total'],
            'rows' => $rows
        ]);
    }

    public function new()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $settings = get_settings('general', true);
        $user_id = $_SESSION['user_id'];
        $id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        $data = [
            'version' => fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'],
            'business_id' => $_SESSION['business_id'],
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => fetch_details('languages', [], [], null, '0', 'id', 'ASC'),
            'page' => FORMS . 'employees',
            'title' => "employees - " . ($settings['title'] ?? ""),
            'from_title' => "create_employees",
            'positions' => (new PositionsModel())->findAll(),
            'employee' => [],
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'currency' => $settings['currency_symbol'] ?? '₹',
            'user' => $this->ionAuth->user($id)->row(),
        ];

        return view("admin/template", $data);
    }

    public function create()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            echo "<script>alert('Unauthorized access.');window.location='" . site_url('login') . "';</script>";
            exit;
        }

        if (!$this->request->is('post') || !$this->request->getPost(csrf_token())) {
            echo "<script>alert('Invalid request.');window.history.back();</script>";
            exit;
        }

        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            echo "<script>alert('Invalid request method.');window.history.back();</script>";
            exit;
        }

        $post = $this->request->getPost([
            'name',
            'position_id',
            'address',
            'contact_number',
            'salary',
            'busniess_id'
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

        if (!$this->validate($rules)) {
            $errors = implode('\n', $this->validator->getErrors());
            echo "<script>alert('Validation failed:\\n$errors');window.history.back();</script>";
            exit;
        }

        $sessionBusinessId = $this->session->get('business_id');
        if ((int)$post['busniess_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized business ID.');window.history.back();</script>";
            exit;
        }

        $employeeModel = new EmployeeModel();

        if (isset($_POST['insert'])) {
            $existing = $employeeModel->where([
                'contact_number' => $post['contact_number']
            ])->first();
            if ($existing) {
                echo "<script>alert('duplicate phone number.');window.history.back();</script>";
                exit;
            }
            $employeeModel->insert($post);
            echo "<script>alert('Employee created successfully');window.location='" . site_url('/admin/employees') . "';</script>";
            exit;
        } elseif (isset($_POST['update'])) {
            $employeeID = $this->request->getPost('edit_attribute_set');
            return $this->update($employeeID);
        } elseif (isset($_POST['delete'])) {
            $employeeID = $this->request->getPost('edit_attribute_set');
            return $this->delete($employeeID);
        }
    }

    public function edit($employee_id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $settings = get_settings('general', true);
        $user_id = $_SESSION['user_id'];
        $id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        $employee_model = new EmployeeModel();
        $employee = $employee_model->edit_employee($employee_id)[0];

        $data = [
            'version' => fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'],
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => fetch_details('languages', [], [], null, '0', 'id', 'ASC'),
            'page' => FORMS . 'employees',
            'title' => "Edit employees - " . ($settings['title'] ?? ""),
            'from_title' => "edit_employees",
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'currency' => $settings['currency_symbol'] ?? '₹',
            'user' => $this->ionAuth->user($id)->row(),
            'positions' => (new PositionsModel())->findAll(),
            'employee' => $employee,
            'user_id' => $employee_id,
        ];

        return view("admin/template", $data);
    }

    public function update($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            echo "<script>alert('Unauthorized access.');window.location='" . site_url('login') . "';</script>";
            exit;
        }

        if (!$this->request->is('post') || !$this->request->getPost(csrf_token())) {
            echo "<script>alert('Invalid request.');window.history.back();</script>";
            exit;
        }

        if (!$this->request->isAJAX() && !$this->request->is('post')) {
            echo "<script>alert('Invalid request method.');window.history.back();</script>";
            exit;
        }

        $post = $this->request->getPost([
            'name',
            'position_id',
            'address',
            'contact_number',
            'salary',
            'busniess_id',
            'updated_at'
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

        if (!$this->validate($rules)) {
            $errors = implode('\n', $this->validator->getErrors());
            echo "<script>alert('Validation failed:\\n$errors');window.history.back();</script>";
            exit;
        }

        $sessionBusinessId = $this->session->get('business_id');
        if ((int)$post['busniess_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized business ID.');window.history.back();</script>";
            exit;
        }

        $employeeModel = new EmployeeModel();

        $existing = $employeeModel->where([
            'contact_number' => $post['contact_number']
        ])->where('id !=', $id)->first();
        if ($existing) {
            echo "<script>alert('Duplicate phone number.');window.history.back();</script>";
            exit;
        }

        $employeeModel->update($id, $post);
        echo "<script>alert('Employee updated successfully');window.location='" . site_url('/admin/employees') . "';</script>";
        exit;
    }

    public function delete($id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            echo "<script>alert('Unauthorized access.');window.location='" . site_url('login') . "';</script>";
            exit;
        }

        if (!$this->request->is('post') || !$this->request->getPost(csrf_token())) {
            echo "<script>alert('Invalid request.');window.history.back();</script>";
            exit;
        }

        $employeeModel = new EmployeeModel();
        $employee = $employeeModel->find($id);
        $sessionBusinessId = $this->session->get('business_id');
        if (!$employee || (int)$employee['busniess_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized or invalid employee.');window.history.back();</script>";
            exit;
        }

        $deleted = $employeeModel->delete($id);
        if ($deleted) {
            echo "<script>alert('Employee deleted successfully');window.location='" . site_url('/admin/employees') . "';</script>";
        } else {
            echo "<script>alert('Failed to delete employee.');window.history.back();</script>";
        }
        exit;
    }
}
