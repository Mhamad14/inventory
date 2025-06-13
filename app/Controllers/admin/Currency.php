<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Currency_model;

class Currency extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $currency_model;

    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'common']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
        $this->currency_model = new Currency_model();
    }

    public function index()
    {
        $data = getdata(
            'currencies',
            '',
            FORMS . "Currencies/currency_table",
        );

        return view("admin/template", $data);
    }

    public function add()
    {

        $data = getdata(
            'currency',
            '',
            FORMS . "Currencies/currency",
        );
        return view("admin/template", $data);
    }

    public function save()
    {
        $this->validation->setRules([
            'code' => [
                'label' => 'Currency Code',
                'rules' => 'required|max_length[3]|is_unique[currencies.code,business_id,' . $_SESSION['business_id'] . ']',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'max_length' => 'The {field} must be exactly 3 characters.',
                    'is_unique' => 'This {field} already exists for your business.'
                ]
            ],
            'name' => [
                'label' => 'Currency Name',
                'rules' => 'required',
                'errors' => [
                    'required' => 'The {field} field is required.'
                ]
            ],
            'symbol' => [
                'label' => 'Symbol',
                'rules' => 'required',
                'errors' => [
                    'required' => 'The {field} field is required.'
                ]
            ],
            'decimal_places' => [
                'label' => 'Decimal Places',
                'rules' => 'required|numeric',
                'errors' => [
                    'required' => 'The {field} field is required.',
                    'numeric' => 'The {field} must be a number.'
                ]
            ]
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
        }

        try {
            $id = $this->request->getPost('id');
            $is_base = $this->request->getPost('is_base') ? 1 : 0;

            // If setting as base currency, unset any existing base currency
            if ($is_base) {
                $this->currency_model->where('business_id', $_SESSION['business_id'])
                    ->set(['is_base' => 0])
                    ->update();
            }

            $currency_data = [
                'id' => $id,
                'business_id' => $_SESSION['business_id'],
                'code' => strtoupper($this->request->getPost('code')),
                'name' => $this->request->getPost('name'),
                'symbol' => $this->request->getPost('symbol'),
                'symbol_position' => $this->request->getPost('symbol_position'),
                'decimal_places' => $this->request->getPost('decimal_places'),
                'is_base' => $is_base,
                'status' => $this->request->getPost('status') ? 1 : 0,
                'updated_at' => date('Y-m-d H:i:s')
            ];

            if (empty($id)) {
                $currency_data['created_at'] = date('Y-m-d H:i:s');
            }

            $this->currency_model->save($currency_data);

            $response = [
                'error' => false,
                'message' => 'Currency saved successfully',
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();

            $_SESSION['toastMessage'] = 'Currency saved successfully';
            $_SESSION['toastMessageType'] = 'success';
            $this->session->markAsFlashdata('toastMessage');
            $this->session->markAsFlashdata('toastMessageType');

            return $this->response->setJSON($response);
        } catch (\Exception $e) {
            log_message('error', '[Currency::save] ' . $e->getMessage());
            $response = [
                'error' => true,
                'message' => 'An error occurred while saving the currency: ' . $e->getMessage(),
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }

    public function edit($currency_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;

        $session = session();
        $lang = $session->get('lang') ?? 'en';
        $data['code'] = $lang;
        $data['current_lang'] = $lang;
        $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');

        $settings = get_settings('general', true);
        $company_title = $settings['title'] ?? "";

        $data['page'] = FORMS . "Currencies/currency";
        $data['from_title'] = 'edit_currency';
        $data['title'] = "Edit Currency - " . $company_title;
        $data['meta_keywords'] = "currencies, edit currency";
        $data['meta_description'] = "Edit currency details";

        $business_id = $_SESSION['business_id'] ?? "";
        $user_id = $_SESSION['user_id'] ?? "";
        $data['user'] = $this->ionAuth->user($user_id)->row();

        $data['currency'] = $this->currency_model->find($currency_id);

        return view("admin/template", $data);
    }

    public function currency_table()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return json_encode([
                'total' => 0,
                'rows' => [],
            ]);
        }

        $business_id = $_SESSION['business_id'] ?? "";
        $currencies = $this->currency_model->get_currencies($business_id);
        $total = $this->currency_model->count_of_currencies();

        $i = 0;
        $rows = [];

        foreach ($currencies as $currency) {
            $id = $currency['id'];

            $edit_btn = "<a href='" . site_url('admin/currency/edit/') . $id . "' class='btn btn-primary btn-sm'><i class='bi bi-pencil'></i></a>";
            $delete_btn = "<button type='button' class='btn btn-danger btn-sm delete-currency' data-id='" . $id . "'><i class='bi bi-trash'></i></button>";

            $status = $currency['status'] ? "<span class='badge badge-success'>Active</span>" : "<span class='badge badge-danger'>Inactive</span>";
            $is_base = $currency['is_base'] ? "<span class='badge badge-info'>Base</span>" : "";

            $rows[$i] = [
                'id' => $id,
                'code' => $currency['code'],
                'name' => $currency['name'],
                'symbol' => $currency['symbol'],
                'symbol_position' => $currency['symbol_position'] ? 'After' : 'Before',
                'decimal_places' => $currency['decimal_places'],
                'status' => $status,
                'is_base' => $is_base,
                'action' => $edit_btn . ' ' . $delete_btn
            ];

            $i++;
        }

        $array = [
            'total' => $total[0]['total'] ?? 0,
            'rows' => $rows
        ];

        echo json_encode($array);
    }

    public function delete($id)
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $response = [
                'error' => true,
                'message' => 'Unauthorized access',
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }

        // Check if currency exists and belongs to the business
        $currency = $this->currency_model->where('id', $id)
            ->where('business_id', $_SESSION['business_id'])
            ->first();

        if (!$currency) {
            $response = [
                'error' => true,
                'message' => 'Currency not found',
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }

        // Check if it's the base currency
        if ($currency['is_base']) {
            $response = [
                'error' => true,
                'message' => 'Cannot delete base currency',
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }

        // Soft delete
        $this->currency_model->where('id', $id)
            ->set(['deleted_at' => date('Y-m-d H:i:s')])
            ->update();

        $response = [
            'error' => false,
            'message' => 'Currency deleted successfully',
            'data' => []
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }

    public function debug()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        }

        $business_id = $_SESSION['business_id'] ?? "";
        $currencies = $this->currency_model->where('business_id', $business_id)
            ->findAll();

        echo "<pre>";
        print_r($currencies);
        echo "</pre>";
        die();
    }
}
