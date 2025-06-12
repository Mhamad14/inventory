<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\PositionsModel;

class positions extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $session;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'position']);
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
            'page' => VIEWS . 'positions_table',
            'title' => "positions - " . ($settings['title'] ?? ""),
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'currency' => $settings['currency_symbol'] ?? '₹',
            'user' => $this->ionAuth->user($id)->row(),
        ];

        return view("admin/template", $data);
    }

    public function positions_table()
    {
        $positions_model = new positionsModel();
        $business_id = $this->session->get('business_id');
        $result = $positions_model->get_positions($business_id);
        $rows = [];
        foreach ($result['data'] as $position) {
            $edit = "<a href=" . site_url('admin/positions/edit') . "/" . $position['id'] . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a> ";
            $rows[] = [
                'id' => $position['id'],
                'description' => ucwords($position['description']),
                'name' => $position['name'],
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
            'page' => FORMS . 'positions',
            'title' => "positions - " . ($settings['title'] ?? ""),
            'from_title' => "create_positions",
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
            'description',
            'name',
            'business_id'
        ]);
        $post = array_map('strip_tags', $post);

        $rules = [
            'description' => 'required|min_length[3]|max_length[255]',
            'name' => 'required|min_length[3]|max_length[100]',
            'business_id' => 'required|integer',

        ];

        if (!$this->validate($rules)) {
            $errors = implode('\n', $this->validator->getErrors());
            echo "<script>alert('Validation failed:\\n$errors');window.history.back();</script>";
            exit;
        }

        $sessionBusinessId = $this->session->get('business_id');
        if ((int)$post['business_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized business ID.');window.history.back();</script>";
            exit;
        }

        $positionModel = new positionsModel();

        if (isset($_POST['insert'])) {
            $existing = $positionModel->where([
                'name' => $post['name']
            ])->first();
            if ($existing) {
                echo "<script>alert('duplicate name.');window.history.back();</script>";
                exit;
            }
            $positionModel->insert($post);
            echo "<script>alert('position created successfully');window.location='" . site_url('/admin/positions') . "';</script>";
            exit;
        } elseif (isset($_POST['update'])) {
            $positionID = $this->request->getPost('edit_attribute_set');
            return $this->update($positionID);
        } elseif (isset($_POST['delete'])) {
            $positionID = $this->request->getPost('edit_attribute_set');
            return $this->delete($positionID);
        }
    }

    public function edit($position_id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $settings = get_settings('general', true);
        $user_id = $_SESSION['user_id'];
        $id = $this->ionAuth->isTeamMember() ? get_vendor_for_teamMember($user_id) : $user_id;

        $position_model = new positionsModel();
        $position = $position_model->edit_position($position_id)[0];

        $data = [
            'version' => fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'],
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => fetch_details('languages', [], [], null, '0', 'id', 'ASC'),
            'page' => FORMS . 'positions',
            'title' => "Edit positions - " . ($settings['title'] ?? ""),
            'from_title' => "edit_positions",
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems",
            'currency' => $settings['currency_symbol'] ?? '₹',
            'user' => $this->ionAuth->user($id)->row(),
            'positions' => (new PositionsModel())->findAll(),
            'position' => $position,
            'user_id' => $position_id,
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
            'description',
            'name',
            'business_id'
        ]);
        $post = array_map('strip_tags', $post);

        $rules = [
            'description' => 'required|min_length[3]|max_length[255]',
            'name' => 'required|min_length[3]|max_length[100]',
            'business_id' => 'required|integer',

        ];

        if (!$this->validate($rules)) {
            $errors = implode('\n', $this->validator->getErrors());
            echo "<script>alert('Validation failed:\\n$errors');window.history.back();</script>";
            exit;
        }

        $sessionBusinessId = $this->session->get('business_id');
        if ((int)$post['business_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized business ID.');window.history.back();</script>";
            exit;
        }

        $positionModel = new positionsModel();

        $existing = $positionModel->where([
            'name' => $post['name']
        ])->where('id !=', $id)->first();
        if ($existing) {
            echo "<script>alert('Duplicate name.');window.history.back();</script>";
            exit;
        }

        $positionModel->update($id, $post);
        echo "<script>alert('position updated successfully');window.location='" . site_url('/admin/positions') . "';</script>";
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

        $positionModel = new positionsModel();
        $position = $positionModel->find($id);
        $sessionBusinessId = $this->session->get('business_id');
        if (!$position || (int)$position['business_id'] !== (int)$sessionBusinessId) {
            echo "<script>alert('Unauthorized or invalid position.');window.history.back();</script>";
            exit;
        }

        $deleted = $positionModel->delete($id);
        if ($deleted) {
            echo "<script>alert('position deleted successfully');window.location='" . site_url('/admin/positions') . "';</script>";
        } else {
            echo "<script>alert('Failed to delete position.');window.history.back();</script>";
        }
        exit;
    }
}
