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
    protected $business_id;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'customer']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
        $this->business_id = session('business_id') ?? "";

        $this->customerModel = new Customers_model();
    }

    public function index()
    {

        // Fetch customers and other data
        $customers = getCustomers($this->business_id);
        // Prepare data for the view
        $data = $this->getData('customers', $customers, FORMS . 'customers');
        

        return view("admin/template", $data);
    }

    public function payBackPartialDebt()
    {
        $payment_amount = $this->request->getPost('partial_amount');
        if (!is_numeric($payment_amount) || $payment_amount <= 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Invalid payment amount.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
            ]);
        }
        if ($this->customerModel->payBackPartialDebt($payment_amount)) {
            return $this->response->setJSON([
                'success'    => true,
                'message' => 'Partial payment applied successfully.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
                'user_id'    => session('current_customer_id'),
                // 'overallPayments' => $this->customerModel->getOverallPayments(session('current_customer_id'), $this->business_id),
            ]);
        } else {
            return $this->response->setJSON([
                'success'    => false,
                'message' => 'No eligible orders found or error occurred.',
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
                'user_id'    => session('current_customer_id'),
            ]);
        }
    }
    public function payBackAllDebt($id)
    {
        if ($this->customerModel->payBackAllDebt($this->business_id)) {
            return $this->response->setJSON([
                'success'    => true,
                'message'    => 'Paid all debt back!',
                'data'       => [],
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
                'user_id'    => $id,
                // 'overallPayments' => $this->customerModel->getOverallPayments(session('current_customer_id'), $this->business_id),
            ]);
        } else {
            return $this->response->setJSON([
                'success'    => false,
                'message'    => 'No unpaid or partially paid orders found, or something went wrong.',
                'data'       => [],
                'csrf_token' => csrf_token(),
                'csrf_hash'  => csrf_hash(),
                'user_id'    => $id,
            ]);
        }
    }

    public function update($user_id)
    {

        // Define common validation rules
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

        // Dynamically add password rule if submitted
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $rules['password'] = [
                'label' => 'Password',
                'rules' => 'min_length[8]',
                'errors' => [
                    'min_length' => 'The {field} must be at least {param} characters long.'
                ]
            ];
        }

        // Validate input
        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => implode('<br>', $this->validator->getErrors())
            ]);
        }

        $db = db_connect();
        $data = [
            'first_name' => $this->request->getPost('name'),
            'mobile'     => $this->request->getPost('mobile'),
            'email'      => $this->request->getPost('email'),
        ];

        // Update password only if provided
        if (!empty($password)) {
            $this->ionAuth->update($user_id, ['password' => $password]);
        }

        // Update users table
        $db->table('users')
            ->where('id', $user_id)
            ->update($data);

        // Update customers table
        $db->table('customers')->where('user_id', $user_id)->update([
            'status' => $this->request->getPost('status'),
        ]);

        // Return success JSON
        return $this->response->setJSON([
            'success'    => true,
            'message'    => 'Customer updated successfully!',
            'data'       => [],
            'csrf_token' => csrf_token(),
            'csrf_hash'  => csrf_hash(),
            'user_id'    => $user_id,
        ]);
    }
    // to get Customers/show.php
    public function edit($id)
    {

        $customer = $this->customerModel->getCustomerFullDetail($id);

        $data = $this->getData('customer', $customer,  FORMS . 'Customers/' . 'show');

        session()->set('current_customer_id', $customer['id']);
        $data['overallPayments'] = $this->customerModel->getOverallPayments($customer['id'], $this->business_id);
        return view('admin/template', $data);
    }

    // edit it a little
    public function customers_table()
    {

        $customers = $this->customerModel->get_customers_details($this->business_id);
        $total = $this->customerModel->count_of_customers($this->business_id);

        $rows = [];
        foreach ($customers as $customer) {
            $rows[] = prepareCustomerRow($customer);
        }

        $response = [
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ];

        return $this->response->setJSON($response);
    }

    public function customer_orders_table()
    {
        $current_customer_id = session("current_customer_id");

        // Fetch customer details and total count
        $rows = $this->customerModel->getCustomersOrderDetails($this->request, $this->business_id, $current_customer_id,);
        $total = $this->customerModel->getTotalCustomerOrders($this->request, $this->business_id, $current_customer_id,);

        return $this->response->setJSON([
            'total' => $total,
            'rows' => array_map('prepareCustomerOrdersRow', $rows)
        ]);
    }

    private function getData($tableName, $tableData, $page)
    {
        $settings = get_settings('general', true);
        $languages = getLanguages();
        return [
            'version' => getAppVersion(),
            'code' => session('lang') ?? 'en',
            'current_lang' => session('lang') ?? 'en',
            'languages_locale' => $languages,
            'business_id' => $this->business_id,
            'page' => $page,
            'title' => "Customers - " . $settings['title'] ?? "",
            'from_title' => 'Customer Details',
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems",
            $tableName => $tableData,
            'user' => $this->ionAuth->user(session('user_id'))->row(),
        ];
    }
}
