<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Currency_model;
use DateTime; // Add this line
use Exception; // Also add this since you're using it

class Currency extends BaseController
{
    protected $db;

    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $currency_model;
    protected $exchange_rates_model;

    public function __construct()
    {
        $this->db = \Config\Database::connect();

        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'common']);
        $this->configIonAuth = config('IonAuth');
        $this->session = \Config\Services::session();
        $this->currency_model = new Currency_model();
        $this->exchange_rates_model = new \App\Models\ExchangeRatesModel();
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
            ->set(['deleted_at' => date(format: 'Y-m-d H:i:s')])
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

    // Get current exchange rates (for AJAX)
    public function get_exchange_rates()
    {
        $businessId = session('business_id');

        // Get all currencies with decimal places
        $currencies = $this->currency_model
            ->where('business_id', $businessId)
            ->where('status', 1)
            ->findAll();

        // Get current rates
        $rates = [];
        foreach ($currencies as $currency) {
            if (!$currency['is_base']) {
                $rate = $this->exchange_rates_model
                    ->where('currency_id', $currency['id'])
                    ->orderBy('effective_date', 'DESC')
                    ->first();

                if ($rate) {
                    // Include currency details with each rate
                    $rate['currency_decimal_places'] = $currency['decimal_places'];
                    $rate['currency_symbol'] = $currency['symbol'];
                    $rates[] = $rate;
                }
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'currencies' => $currencies,
            'rates' => $rates
        ]);
    }

    public function save_exchange_rates()
    {
        // Verify AJAX request
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(405)->setJSON([
                'success' => false,
                'message' => 'Method not allowed'
            ]);
        }

        // Verify CSRF token
        $headerToken = $this->request->getHeaderLine('X-CSRF-TOKEN');
        if (!hash_equals($headerToken, csrf_hash())) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Invalid CSRF token'
            ]);
        }

        $businessId = session('business_id');
        $data = $this->request->getJSON(true);

        // Validate input
        if (!isset($data['rates']) || !is_array($data['rates']) || empty($data['effective_date'])) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid input data'
            ]);
        }

        // Validate datetime format
        try {
            $effectiveDate = new \DateTime($data['effective_date']);
            $effectiveDateFormatted = $effectiveDate->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return $this->response->setStatusCode(400)->setJSON([
                'success' => false,
                'message' => 'Invalid datetime format'
            ]);
        }

        // Start transaction
        $this->db->transStart();
        $updatedCount = 0;

        try {
            foreach ($data['rates'] as $rateData) {
                // Validate data structure
                if (!isset($rateData['currency_id']) || !isset($rateData['rate'])) {
                    continue;
                }

                $currencyId = (int)$rateData['currency_id'];
                $newRate = $rateData['rate'];

                // Validate data values
                if ($currencyId <= 0 || !is_numeric($newRate) || $newRate <= 0) {
                    continue;
                }

                // Get currency details including decimal places
                $currency = $this->currency_model
                    ->where('id', $currencyId)
                    ->where('business_id', $businessId)
                    ->where('is_base', 0)
                    ->first();

                if (!$currency) {
                    continue;
                }

                // Get decimal places for this currency
                $decimalPlaces = (int)$currency['decimal_places'];
                $precision = 10 ** $decimalPlaces;

                // Get current rate
                $currentRate = $this->exchange_rates_model
                    ->where('currency_id', $currencyId)
                    ->orderBy('effective_date', 'DESC')
                    ->first();

                // Compare rates with proper decimal precision
                $rateChanged = true;
                if ($currentRate) {
                    $currentRateValue = (float)$currentRate['rate'];
                    $newRateValue = (float)$newRate;
                    $rateChanged = (round($currentRateValue * $precision) !== round($newRateValue * $precision));
                }

                // Only update if rate has changed
                if ($rateChanged) {
                    $this->exchange_rates_model->insert([
                        'currency_id' => $currencyId,
                        'rate' => round($newRate, $decimalPlaces), // Store with proper decimal places
                        'effective_date' => $effectiveDateFormatted,
                        'created_at' => date('Y-m-d H:i:s')
                    ]);
                    $updatedCount++;
                }
            }

            $this->db->transComplete();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Exchange rates updated successfully',
                'updated_count' => $updatedCount, // Actual number of rates changed
                'new_token' => csrf_hash() // Return new CSRF token
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exchange rate update failed: ' . $e->getMessage());

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Failed to update rates: ' . $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
