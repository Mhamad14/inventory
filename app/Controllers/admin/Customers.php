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

    protected Customers_model $customerModel;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'customer']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();

        $this->customerModel = new Customers_model();
    }

    public function index()
    {

        $business_id = session('business_id');
        $this->validateUserPermission();

        // Fetch customers and other data
        $customers = getCustomers($business_id);
        // Prepare data for the view
        $data = $this->getData('customers', $customers, FORMS . 'customers');
        

        return view("admin/template", $data);
    }


    // to update customer
    public function update($user_id)
    {

        $rules = [
            'name' => [
                'label' => 'Name',
                'rules' => 'required|min_length[3]',
                'errors' => [
                    'required'   => 'The {field} field is required.',
                    'min_length' => 'The {field} must be at least {param} characters long.'
                ]
            ],
            'email' => [
                'label' => 'Email',
                'rules' => 'required|valid_email',
                'errors' => [
                    'required'    => 'The {field} field is required.',
                    'valid_email' => 'Please enter a valid {field} address.'
                ]
            ],
            'mobile' => [
                'label' => 'Mobile Number',
                'rules' => 'required|numeric|min_length[7]|max_length[20]',
                'errors' => [
                    'required'    => 'The {field} field is required.',
                    'numeric'     => 'The {field} must contain only numbers.',
                    'min_length'  => 'The {field} must be at least {param} digits long.',
                    'max_length'  => 'The {field} must not be more than {param} digits long.'
                ]
            ],
        ];
        $isValid = $this->validate($rules);
        if (!$isValid) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
            // return redirect()->back()->withInput()->with('shahram_errors', $this->validator->getErrors());
        }

        // Check if password is entered
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = [
                'label' => 'Password',
                'rules' => 'min_length[8]',
                'errors' => [
                    'min_length' => 'The {field} must be at least {param} characters long.'
                ]
            ];
            $this->ionAuth->update($user_id, ['password' => $password]);
        }

        $db = db_connect();

        $this->validateUserPermission();

        // update users table
        $db->table('users')
            ->where('id', $user_id)
            ->update([
                'first_name' => $this->request->getPost('name'),
                'mobile' => $this->request->getPost('mobile'),
                'email' => $this->request->getPost('email'),
            ]);

        // update Customers table
        $db->table('customers')
            ->where('user_id', $user_id)
            ->update([
                'status' => $this->request->getPost('status'),
            ]);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Customer Updated successfully!',
            'data' => [],
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
            'user_id' => $user_id,
        ]);
    }
    // to get Customers/show.php
    public function edit($id)
    {
        $this->validateUserPermission();

        $customer = $this->customerModel->getCustomerFullDetail($id);

        $data = $this->getData('customer', $customer,  FORMS . 'Customers/' . 'show');

        session()->set('current_customer_id', $customer['id']);


        return view('admin/template', $data);
    }


    public function save_status()
    {
        // Check if modifications are allowed
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return setJSON($this->response, true, [DEMO_MODE_ERROR]);
        }

        // Check if the user is logged in and is an admin
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        }

        if (subscription() !== 'active') {
            return setJSON($this->response, true, ['Subscription is not active.']);
        }
        // Validate the input
        $this->validation->setRules([
            'customer_id' => 'required|trim',
            'status' => 'required|trim'
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return setJSON($this->response, true, $this->validation->getErrors());
        }

        // Get the input data
        $customer_id = $this->request->getPost('customer_id');
        $status = $this->request->getPost('status');

        // Update the customer's status
        $update = update_details(['status' => $status], ['user_id' => $customer_id], 'customers');

        if (!$update) {
            return setJSON($this->response, true, ['Failed to update customer status. Please try again.']);
        }

        // Set success response
        session()->setFlashdata('toastMessage', 'Customer status updated successfully.');
        session()->setFlashdata('toastMessageType', 'success');
        return setJSON($this->response, false, 'Customer status updated successfully');
    }



    // edit it a little
    public function customers_table()
    {
        // Get the business ID from the session
        $business_id = session('business_id') ?? "";

        // Fetch customer details and total count
        $customers = $this->customerModel->get_customers_details($business_id);
        $total = $this->customerModel->count_of_customers($business_id);

        // Prepare rows for the response
        $rows = [];
        foreach ($customers as $customer) {
            $rows[] = prepareCustomerRow($customer);
        }
        // Prepare the response array
        $response = [
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ];

        // Return the response as JSON
        return $this->response->setJSON($response);
    }

    public function customer_orders_table()
    {
        // Get the business ID from the session
        $business_id = session('business_id') ?? "";
        $current_customer_id = session("current_customer_id");

        // Fetch customer details and total count
        $customerOrders = $this->customerModel->getCustomersOrderDetails($business_id, $current_customer_id);

        $filters = [
            'search' => $this->request->getGet('search'),
            'start_date' => $this->request->getGet('start_date'),
            'end_date' => $this->request->getGet('end_date'),
            'payment_status_filter' => $this->request->getGet('payment_status_filter'),
            'customer_orders_type_filter' => $this->request->getGet('customer_orders_type_filter'),
        ];
        $limit = $this->request->getGet('limit') ?? 10;
        $offset = $this->request->getGet('offset') ?? 0;
        $sort = $this->request->getGet('sort') ?? 'id';
        $order = $this->request->getGet('order') ?? 'DESC';

        $rows = $this->customerModel->getCustomersOrderDetails($business_id, $current_customer_id, $limit, $offset, $sort, $order, $filters);
        $total = $this->customerModel->getTotalCustomerOrders($business_id, $current_customer_id, $filters);

        return $this->response->setJSON([
            'total' => $total,
            'rows' => array_map('prepareCustomerOrdersRow', $rows)
        ]);
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
