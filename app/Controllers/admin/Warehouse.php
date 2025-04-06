<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\WarehouseModel;
use CodeIgniter\HTTP\ResponseInterface;

class Warehouse extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }

    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {

            if (!isset($_SESSION['business_id']) || empty($_SESSION['business_id'])) {
                $business_model = new Businesses_model();

                if ($this->ionAuth->isTeamMember()) {
                    $team_member = fetch_details('team_members', ['user_id' => session('user_id')]);

                    if (empty($team_member)) {
                        return redirect()->to('login');
                    }

                    $business_ids = json_decode($team_member[0]['business_ids']);
                    if (empty($business_ids)) {
                        return redirect()->to('login'); // Handle if no business_ids are found
                    }

                    $default_business = null;

                    // Find the default business
                    foreach ($business_ids as $key) {
                        $default_business = fetch_details('businesses', ['id' => $key, 'default_business' => 1]);
                        if (!empty($default_business)) {
                            break; // Exit loop once a default business is found
                        }
                    }

                    // If no default business is found, use the first business in the list
                    if (empty($default_business)) {
                        $default_business = fetch_details('businesses', ['id' => $business_ids[0]]);
                    }

                    if (!empty($default_business)) {
                        $this->session->set('business_id', $default_business[0]['id']);
                        $this->session->set('business_name', $default_business[0]['name']);
                    } else {
                        // Handle case where no valid business is found
                        return redirect()->to('login');
                    }
                } else {
                    $allbusiness = $business_model->select()->where(['user_id' => session('user_id')])->get()->getResultArray();

                    if (empty($allbusiness)) {
                        session()->setFlashdata('message', 'Please create a business!');
                        session()->setFlashdata('type', 'error');
                        return redirect()->to('admin/businesses');
                    }

                    $default_business_id = null;
                    $default_business_name = null;

                    foreach ($allbusiness as $business) {
                        if (!empty($business['default_business']) && (bool)$business['default_business']) { // Ensure it's boolean
                            $default_business_id = $business['id'];
                            $default_business_name = $business['name'];
                            break; // Exit loop once default business is found
                        }
                    }

                    if (empty($default_business_id)) {
                        session()->setFlashdata('message', 'Please select a business!');
                        session()->setFlashdata('type', 'error');
                        return redirect()->to('admin/businesses');
                    } else {
                        $this->session->set('business_id', $default_business_id);
                        $this->session->set('business_name', $default_business_name);
                    }
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
            $data['page'] = FORMS . 'warehouse';
            $data['title'] = "Warehouse - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            $warehouse_model = new WarehouseModel();
            $data['warehouses']  =  $warehouse_model->where('business_id', session('business_id'))->get()->getResultArray();
            return view("admin/template", $data);
        }
    }

    public function save()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin())) {
            return redirect()->to('admin/home');
        } else {
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

            $this->validation->setRules([
                'name' => [
                    'rules' => 'required',
                    'label' => 'Name'
                ],
                'country' => [
                    'rules' => 'required',
                    'label' => 'Country'
                ],
                'city' => [
                    'rules' => 'required',
                    'label' => 'City'
                ],
                'zip_code' => [
                    'rules' => 'required',
                    'label' => 'Zip Code'
                ],
                'address' => [
                    'rules' => 'required',
                    'label' => 'Address'
                ]
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $warehouse_model = new WarehouseModel();
                $user_id = $this->ionAuth->getUserId();
                $vendor_id = 0;
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($user_id);
                } else {
                    $vendor_id = session('user_id');
                }
                $business_id = session('business_id');

                $warehouse_id = $this->request->getVar('id');

                $data =  [
                    'name' => $this->request->getVar('name'),
                    'country' => $this->request->getVar('country'),
                    'city' => $this->request->getVar('city'),
                    'zip_code' => $this->request->getVar('zip_code'),
                    'address' => $this->request->getVar('address'),
                    'vendor_id' => $vendor_id,
                    'business_id' => $business_id,
                ];

                if (! empty($warehouse_id)) {
                    $data['id'] = $warehouse_id;
                }

                $saved = $warehouse_model->save($data);

                if ($saved) {
                    $response = [
                        'error' => false,
                        'message' => ["Warehouse saved successfully"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response = [
                        'error' => true,
                        'message' => ["Failed to create Warehouse"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
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
        }
    }

    public function WarehouseTable()
    {
        $limit = (int) $this->request->getGet('limit') ?? 10; // default limit
        $offset = (int) $this->request->getGet('offset') ?? 0; // default offset
        $search = $this->request->getGet('search') ?? ''; // search term

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $warehouse_model = new WarehouseModel();

        // Apply filters based on business_id and search term
        $warehouse_model->where('business_id', $business_id);

        if (!empty($search)) {
            $warehouse_model->like('name', $search)
                ->orLike('city', $search)
                ->orLike('country', $search)
                ->orLike('zip_code', $search)
                ->orLike('address', $search);
        }

        // Retrieve total count for pagination
        $total = $warehouse_model->countAllResults(false);

        // Apply limit and offset
        $warehouses = $warehouse_model->limit($limit, $offset)->findAll();
        $data = [];
        foreach ($warehouses as $warehouse) {
            $route = base_url('/admin/warehouse/get-warehouse/');
            $action = "<button type='button' class='btn btn-primary btn-sm' data-toggle='tooltip' data-bs-placement='bottom' title='Edit Warehouse' onclick='editWarehouse(" . $warehouse['id'] . ", \"" . $route . "\")'> <i class='bi bi-pencil'></i> </button>";

            $data[] = [
                'id' => $warehouse['id'],
                'name' => $warehouse['name'],
                'city' => $warehouse['city'],
                'country' => $warehouse['country'],
                'zip_code' => $warehouse['zip_code'],
                'address' => $warehouse['address'],
                'action' => $action
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $data
        ]);
    }

    public function getWarehouse($id)
    {
        $warehouse_model = new WarehouseModel();

        $warehouse = $warehouse_model->find($id);


        if (empty($warehouse)) {
            $response = [
                'error' => true,
                'message' => ["Warehouse not Found !"],
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $response = [
                'error' => false,
                'message' => [],
                'data' => $warehouse
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }

    public function syncAllProducts()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $this->validation->setRules([
                'warehouse_id' => [
                    'rules' => 'required',
                    'label' => 'Warehouse'
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
            } else {

                $warehouse_id = $this->request->getVar("warehouse_id");
                $business_id = session('business_id');

                $warehouse_model = new WarehouseModel();
                $warehouse  =  $warehouse_model->where(['business_id' => $business_id, 'id' => $warehouse_id])->get()->getResultArray();
                if (empty($warehouse)) {
                    $response = [
                        'error' => true,
                        'message' => "Warehouse not found !",
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }

                $db = \Config\Database::connect();

                // Fetch all products
                $products = $db->table('products')->where('business_id', $business_id)->get()->getResult();

                foreach ($products as $product) {
                    // Stock is managed through variants, get all variants for this product
                    $variants = $db->table('products_variants')
                        ->where('product_id', $product->id)
                        ->get()->getResult();

                    foreach ($variants as $variant) {
                        $data = [
                            'vendor_id' => $product->vendor_id,
                            'business_id' => $product->business_id,
                            'warehouse_id' => $warehouse_id,
                            'product_variant_id' => $variant->id, // Use the variant ID
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ];

                        if ($product->stock_management == 1) {
                            $data['stock'] = $product->stock;
                            $data['qty_alert'] = $product->qty_alert;
                        } elseif ($product->stock_management == 2) {
                            $data['stock'] = $variant->stock;
                            $data['qty_alert'] = $variant->qty_alert;
                        }

                        // Check if the record already exists in the warehouse_product_stock table
                        $existingRecord = $db->table('warehouse_product_stock')
                            ->where('vendor_id', $data['vendor_id'])
                            ->where('business_id', $data['business_id'])
                            ->where('warehouse_id', $data['warehouse_id'])
                            ->where('product_variant_id', $data['product_variant_id'])
                            ->get()
                            ->getRow();

                        // If no record exists, insert the new data
                        if (!$existingRecord) {
                            $db->table('warehouse_product_stock')->insert($data);
                        }
                    }
                }

                $response = [
                    'error' => false,
                    'message' => "All product Synced Successfully !",
                    'data' => []
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
    public function getAllWarehouse()
    {
        $business_id = session('business_id');
        $warehouse_model = new WarehouseModel();
        $all_warehouses  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
        if (empty($all_warehouses)) {
            $response = [
                'error' => true,
                'message' => ["Warehouse not Found !"],
                'data' => []
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        } else {
            $response = [
                'error' => false,
                'message' => [],
                'data' => $all_warehouses
            ];
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }
}
