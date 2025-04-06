<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\BrandModel;
use App\Models\Businesses_model;
use App\Models\Categories_model;
use App\Models\Products_model;
use App\Models\Products_variants_model;
use App\Models\Tax_model;
use App\Models\Units_model;


use App\Models\Vendors_model;
use App\Models\WarehouseModel;
use App\Models\WarehouseProductStockModel;
use function PHPUnit\Framework\fileExists;

class Products extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $data;
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
            $data['page'] = VIEWS . "products_table";
            $data['title'] = "Products - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $category_model = new Categories_model();

            $data['brands'] = (new BrandModel())->findAll();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['categories'] =  $category_model->get_categories($id, $business_id);

            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function stock($flag = "")
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
            $data['flag'] = $flag;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "stock_table";
            $data['title'] = "Products - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;

            $category_model = new Categories_model();
            $data['categories'] =  $category_model->get_categories($id, $business_id);

            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }

    public function Add_products()
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
                $data['page'] = FORMS . "product";
                $data['title'] = "Add Products - " . $company_title;
                $data['from_title'] = "add_product";
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
                $user_id = $_SESSION['user_id'];
                $id = 0;
                if ($this->ionAuth->isTeamMember()) {
                    $id = get_vendor_for_teamMember($user_id);
                } else {
                    $id = $user_id;
                }
                $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

                $data['user'] = $this->ionAuth->user($id)->row();
                // get all units
                $units_model = new Units_model();
                $data['units'] =  $units_model->get_units_for_forms($id);
                // get all warehouses 
                $warehouse_model = new WarehouseModel();
                $all_warehouses  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
                $data['all_warehouses'] = $all_warehouses; // this for setting all warehouse in input tag , So that it can be use in js for adding warehouse check for class=addWarehouseBtn click event in custom.vendor.js  and id=add_variant click event ; 

                // get all categories
                $category_model = new Categories_model();
                $data['categories'] = $category_model->get_categories($id, $business_id);
                // get all taxies
                $tax_model = new Tax_model();
                $data['taxes'] = $tax_model->findAll();

                // get all brands
                $brand_model = new BrandModel();
                $data['brands'] = $brand_model->findAll();


                $warehouse_model = new WarehouseModel();
                $data['warehouses']  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();

                return view("admin/template", $data);
            }
        }
    }
    public function save_products()
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
                $products_model = new products_model();
                $old_icon = $this->request->getVar('old_image');

                if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                    $this->validation->setRules([
                        'name' => [
                            'rules' => 'required',
                            'label' => 'Name'
                        ],
                        'description' => [
                            'rules' => 'required',
                            'label' => 'Description'
                        ],
                        'product_type' => [
                            'rules' => 'required',
                            'label' => 'Product type'
                        ]
                    ]);
                } else {
                    $this->validation->setRules([
                        'name' => [
                            'rules' => 'required',
                            'label' => 'Name'
                        ],
                        'description' => [
                            'rules' => 'required',
                            'label' => 'Description'
                        ],
                        'product_type' => [
                            'rules' => 'required',
                            'label' => 'Product type'
                        ],
                        'variant_name' => [
                            'rules' => 'required',
                            'label' => 'Variant name'
                        ]
                    ]);
                }

                $path = './public/uploads/products/';
                if (!empty($_FILES['image']) && isset($_FILES['image'])) {
                    $file =  $this->request->getFile('image');
                    if ($file->isValid()) {
                        if ($file->move($path)) {
                            if (fileExists($old_icon) && !empty($old_icon)) {
                                unlink($old_icon);
                            }
                            $image = 'public/uploads/products/' . $file->getName();
                        }
                    } else {
                        $image = $old_icon;
                    }
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
                    if ($this->ionAuth->isTeamMember()) {
                        $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                    } else {
                        $vendor_id = $_SESSION['user_id'];
                    }

                    // checking manually whether warehouse is selected or not for each variant.
                    $checking_warehouses = $this->request->getVar('warehouses');
                    foreach ($checking_warehouses as $warehouse => $keys) {
                        foreach ($keys['warehouse_ids'] as $warehouse_id) {

                            if (empty($warehouse_id)) {
                                $response = [
                                    'error' => true,
                                    'message' => ["Please select Warehouse for all variants "],
                                    'data' => []
                                ];
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
                    }


                    $status = isset($_POST['status']) ? "1" : "0";
                    $is_tax_included = isset($_POST['is_tax_inlcuded']) ? "1" : "0";

                    $tax_ids = '[]';

                    if ($is_tax_included === "0") {
                        $tax_ids_input = $this->request->getVar('tax_ids');
                        if ($tax_ids_input) {
                            $tax_ids_input = json_decode($tax_ids_input);
                            $tax_ids = [];
                            if (is_array($tax_ids_input)) {
                                foreach ($tax_ids_input as $tax) {
                                    $tax_ids[] = $tax->id;
                                }
                            }
                            $tax_ids = json_encode($tax_ids);
                        }
                    }

                    $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                    $edit_product_id = isset($_POST['product_id']) ? $_POST['product_id'] : "";
                    $stock_management_type = $this->request->getVar('stock_management_type');
                    $brand_id = $this->request->getVar('brand_id');
                    $brand_id = isset($brand_id) && ! empty($brand_id) ? $brand_id : null;

                    if (isset($_POST['product_id'])) {
                        if ($this->ionAuth->isTeamMember()) {
                            if (!empty($this->request->getVar(index: 'product_id'))) {
                                if (! userHasPermission('products', 'can_update', session('user_id'))) {
                                    $response = [
                                        'error' => true,
                                        'message' => ['You do not have permission for this action'],
                                        'data' => []
                                    ];
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            } else {
                                if (! userHasPermission('products', 'can_create', session('user_id'))) {
                                    $response = [
                                        'error' => true,
                                        'message' => 'You do not have permission for this action',
                                        'data' => []
                                    ];
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                        }
                    }

                    $products = array(
                        'id' => $edit_product_id,
                        'vendor_id' => $vendor_id,
                        'business_id' => $business_id,
                        'category_id' => $this->request->getVar('category_id'),
                        'tax_ids' => $tax_ids,
                        'name' => $this->request->getVar('name'),
                        'description' => $this->request->getVar('description'),
                        'image' => $image,
                        'type' => $this->request->getVar('product_type'),
                        'stock_management' => $stock_management_type,
                        'stock' => $this->request->getVar('simple_product_stock'),
                        'qty_alert' => $this->request->getVar('simple_product_qty_alert'),
                        'unit_id' => $this->request->getVar('simple_product_unit_id'),
                        'brand_id' => $brand_id,
                        'is_tax_included' => $is_tax_included,
                        'status' => $status,
                    );
                    $products_model->save($products);
                    $products_variants_model = new products_variants_model();
                    if (isset($_POST['product_id']) && !empty($_POST['product_id'])) {
                        $product_id = $_POST['product_id'];
                    } else {
                        $product_id = $products_model->getInsertID();
                    }

                    $variant_id = "";

                    $warehouse_product_stock_model = new WarehouseProductStockModel();
                    $currentDateTime = date('Y-m-d H:i:s');

                    $variantName = $this->request->getVar('variant_name');
                    $warehouses = $this->request->getVar('warehouses');
                    $barcodes =  $this->request->getVar('variant_barcode');

                    if ($variantName) {
                        $variant_count = count($variantName);
                        $variant_ids = $this->request->getVar('variant_id');
                        $variants = [];

                        for ($i = 0; $i < $variant_count; $i++) {

                            if (isset($variant_ids[$i])) {
                                $variants['id'] = $variant_ids[$i];
                            }

                            if ($stock_management_type == 1) {
                                $variants['stock'] =  '0';
                                $variants['qty_alert'] = '0';
                                $variants['unit_id'] = '0';
                            } else {
                                if (isset($this->request->getVar('qty_alert')[$i])) {
                                    $variants['qty_alert'] = $this->request->getVar('qty_alert')[$i];
                                } else {
                                    $variants['qty_alert'] = '0';
                                }
                                $variants['stock'] = $this->request->getVar('stock')[$i];
                                $variants['unit_id']  = $this->request->getVar('unit_id')[$i];
                            }


                            $variants['product_id'] = $product_id;
                            $variants['variant_name'] = $this->request->getVar('variant_name')[$i];
                            $variants['barcode'] = isset($barcodes[$i])  && !empty($barcodes[$i]) ? $barcodes[$i] : null;
                            $variants['sale_price'] = $this->request->getVar('sale_price')[$i];
                            $variants['purchase_price'] = $this->request->getVar('purchase_price')[$i];

                            $products_variants_model->save($variants);
                            $products_variants_id = isset($variants['id']) ? $variants['id'] :  $products_variants_model->getInsertID();

                            if (isset($warehouses[$i])) {
                                $warehouse_ids = $warehouses[$i]['warehouse_ids'];
                                $warehouse_stock = $warehouses[$i]['warehouse_stock'];
                                $warehouse_qty_alert = $warehouses[$i]['warehouse_qty_alert'];
                                // Loop through each warehouse related to this variant
                                for ($k = 0; $k < count($warehouse_ids); $k++) {
                                    $warehouse_id = empty($warehouse_ids[$k]) ? "1" : $warehouse_ids[$k];
                                    $stock = empty($warehouse_stock[$k]) ? "0" : $warehouse_stock[$k];
                                    $qty_alert = empty($warehouse_qty_alert[$k])  ? "0" : $warehouse_qty_alert[$k];
                                    // Prepare the data for storage
                                    $data = [
                                        'warehouse_id' => $warehouse_id,
                                        'product_variant_id' =>  $products_variants_id,  // Correct variant ID
                                        'stock' =>  $stock,
                                        'qty_alert' => $qty_alert,
                                        'vendor_id' => $vendor_id,
                                        'business_id' => $business_id,
                                        'updated_at' => $currentDateTime,
                                    ];

                                    $existing_recode =  $warehouse_product_stock_model->where(['warehouse_id' => $warehouse_id, 'product_variant_id' => $products_variants_id])->get()->getResultArray();

                                    if (!empty($existing_recode)) {
                                        $data['id'] = $existing_recode[0]['id'];
                                        $data['updated_at'] = $currentDateTime;
                                    } else {
                                        $data['created_at'] = $currentDateTime;
                                    }

                                    $warehouse_product_stock_model->save($data);
                                }
                            }
                            if (isset($variants['id'])) {
                                unset($variants['id']);
                            }
                        }
                    }

                    $response = [
                        'error' => false,
                        'message' => 'Product saved successfully',
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return false;
            }
        }
    }
    public function products_table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $category_id = (!empty($_GET['category_id'])) ? $_GET['category_id'] : "";
        $brand_id = (!empty($_GET['brand_id'])) ? $_GET['brand_id'] : "";
        $limit = (!empty($_GET['limit'])) ? $_GET['limit'] : 10;
        $offset = (!empty($_GET['offset'])) ? $_GET['offset'] : 0;
        $sort = (!empty($_GET['sort'])) ? $_GET['sort'] : "id";
        $order = (!empty($_GET['order'])) ? $_GET['order'] : "DESC";
        $search = (!empty($_GET['search'])) ? $_GET['search'] : '';

        // echo "<pre>";
        // print_R($brand_id);
        // die();

        $products = fetch_products($business_id, $category_id, $brand_id, $search, $limit, $offset, $sort, $order);

        $business_name = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "";
        $total = $products['total'];
        $units_model = new Units_model();
        $category_model = new Categories_model();
        $tax_model = new Tax_model();
        $i = 0;

        foreach ($products['products'] as $product) {
            $product_unit_name = "";
            $category_name = "";
            $tax_name = "";
            $tax_percentage = 0.00;

            if (isset($product['unit_id']) && $product['unit_id'] != "0") {
                $unit_id = $product['unit_id'];
                $product_unit = $units_model->find($unit_id);
                $product_unit_name = $product_unit['name'];
            }

            if (isset($product['category_id']) && $product['category_id'] != "0") {
                $category_id = $product['category_id'];
                $category = $category_model->find($category_id);
                $category_name = $category['name'];
            }

            if (isset($product['tax_id']) && $product['tax_id'] != "0") {
                $tax = $tax_model->find($product['tax_id']);
                $tax_name = $tax['name'];
                $tax_percentage = $tax['percentage'];
            }

            $status = ($product['status'] == 1)
                ? "<span class='badge badge-primary'>Active</span>"
                : "<span class='badge' style='background-color:#ed1307'>Deactive</span>";

            $product['image'] = '<div class="image-box-100 "><a href="' . base_url($product['image'])  . '" data-lightbox="image-1">
             <img src="' . base_url($product['image']) . '" class="image-100 image-box-100 img-fluid" />
            </a></div>';

            $product['is_tax_included'] = ($product['is_tax_included'] == 1) ? "Included" : "Excluded";

            if ($product['stock_management'] == 1) {
                $product['stock_management'] = "<p class='badge badge-success'>on</p>";
                if ($product['stock'] == "0") {
                    $product['stock'] = "<p class='badge badge-danger'>Out of stock</p>";
                } elseif ($product['stock'] <= $product['qty_alert']) {
                    $product['stock'] = $product['stock'] . "<br><p class='badge badge-info'>Low stock</p>";
                } else {
                    $product['stock'] = $product['stock'] . "<br><p class='badge badge-success'>In stock</p>";
                }
            } elseif ($product['stock_management'] == 2) {
                $product['stock_management'] = "<p class='badge badge-success'>on</p>";
                $product['stock'] = "<p class='badge badge-info'>View variants table for stock information</p>";
            } else {
                $product['stock_management'] = "<p class='badge badge-secondary'>off</p>";
                $product['stock'] = "<p class='badge badge-secondary'>NA</p>";
                $product['qty_alert'] = "<p class='badge badge-secondary'>NA</p>";
            }
            $vendor_model = new Vendors_model;
            $vendor = $vendor_model->find($product['vendor_id']);
            $vendor_first_name = $vendor['first_name'];


            $product_id = $product['id'];
            $edit_product = "<a href='" . site_url('admin/products/edit_product') . "/" . $product_id . "' class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a> ";
            $edit_product .= "<a href='javascript:void(0)' data-id='" . $product_id . "' class='btn btn-warning btn-sm' data-toggle='tooltip' data-placement='bottom' title='View' data-bs-toggle='modal' data-bs-target='#variants_Modal'><i class='bi bi-eye'></i></a>";
            $edit_product .= "<a href='javascript:void(0)' onclick='generate_barcode(" . $product_id . ")' class='btn btn-info btn-sm m-1' data-toggle='tooltip' data-placement='bottom' title='Barcode' data-bs-toggle='modal' data-bs-target='#barcode_Modal'><i class='bi bi-upc-scan'></i></a>";

            $rows[$i] = [
                'id' => $product['id'],
                'name' => ucwords($product['name']),
                'description' => $product['description'],
                'image' => $product['image'],
                'type' => $product['type'],
                'stock_management' => $product['stock_management'],
                'stock' => $product['stock'],
                'qty_alert' => $product['qty_alert'],
                'unit_id' => $product_unit_name,
                'is_tax_included' => $product['is_tax_included'],
                'status' => $status,
                'category_id' => $category_name,
                'vendor_id' => $vendor_first_name,
                'business_name' => $business_name,
                'tax_id' => $tax_percentage . '% ' . $tax_name,
                'action' => $edit_product
            ];
            $i++;
        }

        $array = [
            'total' => $total,
            'rows' => $rows ?? []
        ];

        echo json_encode($array);
    }

    public function edit_product($product_id = "")
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
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "product";
            $data['title'] = "Edit Products - " . $company_title;
            $data['from_title'] = "edit_product";
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $data['user'] = $this->ionAuth->user($id)->row();
            $units_model = new Units_model();
            $data['units'] =  $units_model->get_units_for_forms($id);
            $business_id = session('business_id');
            $warehouse_model = new WarehouseModel();
            $all_warehouses  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
            $data['all_warehouses'] = $all_warehouses;
            $category_model = new Categories_model();
            $data['categories'] = $category_model->get_categories($id, $business_id);
            $tax_model = new Tax_model();

            // get all brands
            $data['brands'] = (new BrandModel())->findAll();

            $products = get_products_with_variants($product_id);
            // if product not found then return 404 page
            if (empty($products)) {
                return view("errors/html/error_404");
            }

            $data['products'] = isset($products[0]) ? $products[0] : "";
            $products_tax_ids = json_decode($data['products']['tax_ids']);
            $products_tax_value  = [];

            if (gettype($products_tax_ids) != "array") {
                if ($products_tax_ids != 0) {
                    $tax = $tax_model->find($products_tax_ids);
                    $products_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            } else {
                foreach ($products_tax_ids as $tax_id) {
                    $tax = $tax_model->find($tax_id);
                    $products_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            }
            $data['products_tax_value'] = json_encode($products_tax_value);


            $data['warehouses']  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
            $warehouse_product_stock_model = new WarehouseProductStockModel();

            $variants = isset($products[0]['variants']) ? $products[0]['variants'] : "";

            foreach ($variants as $key => $variant) {

                // Fetch the warehouse product data based on product variant ID
                $warehouse_product = $warehouse_product_stock_model->where('product_variant_id', $variant['id'])->get()->getResultArray();
                foreach ($warehouse_product as $row) {
                    // Assign warehouse data to the current variant
                    $variants[$key]['warehouse_data'][] = $row;
                }

                // Check if unit_id is set and not "0"
                if (isset($variant['unit_id']) && $variant['unit_id'] != "0") {
                    $variant_unit = $units_model->find($variant['unit_id']);
                    $variant_unit_name = $variant_unit['name'];
                    $variants[$key]['variant_unit_name'] = $variant_unit_name;
                }
            }

            $data['variants'] = $variants;
            if (isset($products[0]['unit_id']) && $products[0]['unit_id'] != "0") {

                $unit_id = $products[0]['unit_id'];
                $product_unit = $units_model->find($unit_id);
                $data['product_unit_name'] = $product_unit['name'];
            }
            if (isset($products[0]['tax_id']) && $products[0]['tax_id'] != "0") {
                $tax_name = $tax_model->find($products[0]['tax_id']);
                $data['tax_name'] = $tax_name['name'];
                $data['percentage'] = $tax_name['percentage'];
            }
            if (isset($products[0]['category_id']) && $products[0]['category_id'] != "0") {
                $category_id = $products[0]['category_id'];
                $category = $category_model->find($category_id);
                $data['category_name'] = $category['name'];
            }
            return view("admin/template", $data);
        }
    }
    public function update_variant_status()
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

        $id = $_GET['id'];
        $status = $_GET['status'];
        update_details(['status' => $status], ['id' => $id], 'products_variants');
        $response = [
            'error' => false,
            'data' => []
        ];
        return $this->response->setJSON($response);
    }

    public function variants_table($product_id = "")
    {
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : "";
        $products_variants_model = new products_variants_model();
        $units_model = new Units_model();
        $products_variants = $products_variants_model->get_product_variants($product_id);
        $variants = isset($products_variants) ? $products_variants : "";
        $total = count($variants);
        $i = 0;
        foreach ($variants as $variant) {

            $product_unit_name = "";
            if (isset($variant['unit_id']) &&  $variant['unit_id'] != "0") {
                $unit_id = $variant['unit_id'];
                $product_unit = $units_model->find($unit_id);
                $product_unit_name = $product_unit['name'];
            }
            if ($variant['status'] == 1) {
                $status = "<span class='badge badge-primary'>Active</span>";
                $edit_product = "<label for='variant_status" . $variant['id'] . "' class='custom-switch p-0'><input type='checkbox' name='variant_status[]' data-id='" . $variant['id'] . "' onclick = 'update_status(this)'  id = 'variant_status" . $variant['id'] . "' class='custom-switch-input variant_status' checked><span class='custom-switch-indicator'></span></label>";
            } else {
                $status = "<span class='badge ' style = 'background-color:#ed1307'>Deactive</span>";
                $edit_product = "<label for='variant_status" . $variant['id'] . "' class='custom-switch p-0'><input type='checkbox' name='variant_status[]' data-id='" . $variant['id'] . "' onclick = 'update_status(this)' id = 'variant_status" . $variant['id'] . "' class='custom-switch-input variant_status'><span class='custom-switch-indicator'></span></label>";
            }
            if ($variant['stock'] <=  $variant['qty_alert']) {
                $variant['stock'] = $variant['stock'] . "<br><p class='badge badge-info'>Low stock</p>";
            } else {
                $variant['stock'] = $variant['stock'] . "<br><p class='badge badge-success'>In stock</p>";
            }

            $rows[$i] = [
                'id' => $variant['id'],
                'product_id' =>  $variant['product_id'],
                'variant_name' => ucwords($variant['variant_name']),
                'sale_price' =>  decimal_points($variant['sale_price']),
                'purchase_price' =>  decimal_points($variant['purchase_price']),
                'stock' =>  $variant['stock'],
                'qty_alert' =>  $variant['qty_alert'],
                'unit_id' =>  $product_unit_name,
                'status' =>  $status,
                'action' => $edit_product
            ];
            $i++;
        }
        $array['total'] = $total;
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
    public function remove_variant($variant_id)
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            $variant_model = new products_variants_model();
            $status = $variant_model->where("id", $variant_id)->delete();
            if ($status) {
                $response = [
                    'error' => false,
                    'message' => 'Product variant removed succesfully',
                    'data' => []
                ];
            } else {
                $response = [
                    'error' => true,
                    'message' => 'variant does not exist...',
                    'data' => []
                ];
            }
            return $this->response->setJSON($response);
        }
    }

    public function json()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $product_id = ($this->request->getGet('id') != '') ? $this->request->getGet('id') : '';

        $settings = get_settings('general', true);
        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '$';
        $data = $_GET;
        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|numeric',
        ];
        if ($this->request->getGet('category_id')) {
            $rules['category_id'] = 'numeric';
        }
        if ($this->request->getGet('limit')) {
            $rules['limit'] = 'numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if ($this->request->getGet('offset')) {
            $rules['offset'] = 'numeric|greater_than_equal_to[0]';
        }
        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
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
            $business_id = $data['business_id'];
            $category_id = (!empty($data['category_id'])) ? $data['category_id'] : "";
            $brand_id = (!empty($data['brand_id'])) ? $data['brand_id'] : "";
            $limit = (!empty($data['limit'])) ? $data['limit'] : 10;
            $offset = (!empty($data['offset'])) ? $data['offset'] : 0;
            $sort = (!empty($data['sort'])) ? $data['sort'] : 'id';
            $order = (!empty($data['order'])) ? $data['order'] : 'DESC';
            $search = (!empty($data['search'])) ? $data['search'] : '';
            $products = fetch_products($business_id, $category_id,  $brand_id, $search, $limit, $offset, $sort, $order, '', [], ['product_id' => $product_id]);
            $final_product_list = array();
            $final_vars = [];
            $temp_arr = $products['products'];

            $variants_array = array();
            $tax_model = new Tax_model();
            if (isset($temp_arr) && !empty($temp_arr)) {
                foreach ($temp_arr as $val) {
                    $variants = count($val['variants']);
                    for ($i = 0; $i < $variants; $i++) {
                        $val['variants'][$i]['image'] = $val['image'];
                        $val['variants'][$i]['name'] = $val['name'];
                        $val['variants'][$i]['category'] = category_name($val['category_id']);
                    }

                    $tax_ids = json_decode($val['tax_ids']);
                    // Note percentage and percentages are different ;
                    $percentage = 1;
                    $percentages = [];

                    // checking if the tax_ids is array or int
                    if (gettype($tax_ids) != "array") {
                        if ($tax_ids != 0) {
                            $taxes = fetch_details("tax", ['id' => $tax_ids]);
                            $percentage = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                        }
                    } else {
                        // if tax_ids is array then get get percentage 
                        foreach ($tax_ids as $tax) {
                            $taxes = fetch_details("tax", ['id' => $tax]);
                            $per = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                            $percentages[] = $per;
                        }
                    }
                    $is_tax_inlcuded = $val['is_tax_included'];
                    if ($is_tax_inlcuded != "1") {
                        for ($i = 0; $i < $variants; $i++) {
                            $sale_price = $val['variants'][$i]['sale_price'];
                            $taxable_amount_price = 0;
                            if (! empty($percentages)) {

                                foreach ($percentages as $prec) {

                                    $taxable_amount_price += floatval($sale_price) * (floatval($prec) / 100);
                                }
                            } else {
                                $taxable_amount_price = floatval($sale_price) * (floatval($percentage) / 100);
                            }

                            $price = floatval($sale_price) + $taxable_amount_price;
                            $val['variants'][$i]['sale_price'] = $price;

                            $purchase_price = $val['variants'][$i]['purchase_price'];
                            $taxable_amount = 0;
                            if (! empty($percentages)) {
                                foreach ($percentages as $prec) {
                                    $taxable_amount += floatval($purchase_price) * (floatval($prec) / 100);
                                }
                            } else {
                                $taxable_amount = floatval($purchase_price) * (floatval($percentage) / 100);
                            }

                            $purchase = floatval($purchase_price) + $taxable_amount;
                            $val['variants'][$i]['purchase_price'] = $purchase;
                        }
                    } else {
                        $val['variants'] = $val['variants'];
                    }
                    $final_product_list[] = $val;
                }
            }
            // getting only varients array for the select2 search. 

            $variants_array = array_column($final_product_list, 'variants');
            $count = count($variants_array);
            for ($i = 0; $i < $count; $i++) {
                foreach ($variants_array[$i] as $row) {
                    array_push($final_vars, $row);
                }
            }

            $response['variants'] = $final_vars;
            $response['error'] = (!empty($products['products'])) ? false : true;
            $response['message'] = (!empty($products['products'])) ? "Products fetched successfully" : "No products found!";
            $response['total'] = $products['total'];
            $response['data'] = $final_product_list;
            $response['currency'] = $currency;
            return $this->response->setJSON($response);
        }
    }

    public function stock_table($flag = "")
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $model = new Products_model();
        $products = $model->get_product_details($business_id, $flag);
        $i = 0;
        if (!empty($products)) {
            foreach ($products as $product) {
                if ($product['stock_management'] == "1") {
                    $product['stock_management'] == "Product";
                } elseif ($product['stock_management'] == "2") {
                    $product['stock_management'] == "Variable";
                } else {
                    $product['stock_management'] == "NA";
                }

                $rows[$i] = [
                    "product_id" => $product['product_id'],
                    "product" => $product['product'],
                    "variant_name" => $product['variant_name'],
                    "stock" => $product['stock'],
                    "qty_alert" => $product['qty_alert'],
                    "stock_management" => $product['stock_management'],
                    "action" => '<a type="button" class="btn btn-primary text-white" data-bs-toggle="modal"  data-stock ="' . $product['stock'] . '" data-product_id ="' . $product['product_id'] . '"  data-stock_management ="' . $product['stock_management'] . '" data-bs-target="#new_stock"><i class="fas fa-edit"></i>'
                ];
                $i++;
            }
            $array['total'] = count($products);
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function manage_stock()
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
            $data['page'] = VIEWS . "manage_stock";
            $data['title'] = "Stock Management - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $warehouse_model = new WarehouseModel();
            $data['warehouses']  =  $warehouse_model->where('business_id', $business_id)->get()->getResultArray();
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("admin/template", $data);
        }
    }
    public function fetch_stock()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $products = fetch_stock($business_id, 'stock_management', ['1', '2']);
        $final_product_list = array();

        foreach ($products as $product) {

            $stock = $product['stock_management'] == '1' ? $product['product_stock'] : $product['variant_stock'];
            $name =  $product['name'] . " - " . $product['variant_name'];
            $product_id = $product['stock_management'] == '1' ? $product['id'] : $product['variant_id'];
            $val['stock_management'] = $product['stock_management'];
            $val['image'] = $product['image'];
            $val['id'] = $product_id;
            $val['name'] = $name;
            $val['stock'] = $stock;
            $final_product_list[] = $val;
        }
        $response['data'] = $final_product_list;
        return $this->response->setJSON($response);
    }

    //  new adjustment stock
    public function save_adjustment()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'product' => 'required|numeric',
                    'variant_id' => 'required|numeric',
                    'stock_management' => 'required|numeric',
                    'current_stock' => 'required|numeric',
                    'quantity' => 'required|numeric|greater_than[0]',
                    'type' => 'required',
                ]);
                if (isset($_POST['warehouse_id']) &&  empty($_POST['warehouse_id'])) {
                    $errors = ["Warehouse is required !"];
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
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
                    // product is post is a product id
                    $stock = 0;
                    $variant_id = $this->request->getVar('variant_id');
                    $quantity = $this->request->getVar('quantity');
                    $warehouse_id = $this->request->getVar('warehouse_id');
                    // check if the selected products_variants are in selected warehouse or not.
                    $warehouse_product_stock =  new WarehouseProductStockModel();

                    $warehouse_product_list  = $warehouse_product_stock->where([
                        'warehouse_id' => $warehouse_id,
                        'product_variant_id' => $variant_id
                    ])->get()->getResultArray();

                    if (empty($warehouse_product_list)) {
                        $response = [
                            'error' => true,
                            'message' =>  ["Product is not available in selected warehouse !"],
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        return $this->response->setJSON($response);
                    }


                    if ($_POST['type'] == 'add') {
                        $stock = floatval($_POST['current_stock']) + floatval($_POST['quantity']);
                        updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $variant_id, warehouse_stock: $quantity, type: 1);
                    }
                    if ($_POST['type'] == 'subtract') {
                        $current_stock = floatval($_POST['current_stock']);
                        $current_quantity = floatval($_POST['quantity']);
                        if ($current_stock  < $current_quantity) {
                            $response = [
                                'error' => true,
                                'message' => ['name' => "Quantity must be less than Current Stock fo Subtraction  !"],
                                'data' => []
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            return $this->response->setJSON($response);
                        }
                        $stock = floatval($_POST['current_stock']) - floatval($_POST['quantity']);
                        updateWarehouseStocks(warehouse_id: $warehouse_id, product_variant_id: $variant_id, warehouse_stock: $quantity, type: 0);
                    }
                    if ($_POST['stock_management'] == '1') {
                        update_details(['stock' => (string) $stock], ['id' => $_POST['product']], 'products');
                    }
                    if ($_POST['stock_management'] == '2') {
                        update_details(['stock' => (string) $stock], ['id' => $_POST['product']], 'products_variants');
                    }
                    $response = [
                        'error' => false,
                        'message' => 'Product Stock Updated Successfully',
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();

                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->back();
            }
        }
    }

    public function table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $products = fetch_stock($business_id, 'stock_management', ['1', '2']);
        $i = 0;
        $warehouse_product_stock_model = new WarehouseProductStockModel();

        if (!empty($products)) {
            foreach ($products as $product) {

                if ($product['stock_management'] == "1") {
                    $product['stock_management'] == "Product";
                } elseif ($product['stock_management'] == "2") {
                    $product['stock_management'] == "Variable";
                } else {
                    $product['stock_management'] == "NA";
                }
                $stock = $product['stock_management'] == '1' ? $product['product_stock'] : $product['variant_stock'];
                $name =  $product['name'] . " - " . $product['variant_name'];
                $product_id = $product['stock_management'] == '1' ? $product['id'] : $product['variant_id'];
                $variant_id = $product['variant_id'];
                // getting stocks in warehouse 
                $warehouses_data = $warehouse_product_stock_model->get_warehouses_data_for_variants([$variant_id]);
                $warehouse_stock = "";
                foreach ($warehouses_data as $data) {

                    $warehouse_stock .= "Warehouse name : " . $data['name'] . " | Warehouse stock : "  . $data["stock"] . "<br>";
                }

                $product['image'] =  '<div class = "image-box-100"><a class="align-items-center d-flex icon-box justify-content-center" href=" ' . base_url($product['image'])  . '" data-lightbox="image-1"> 
             <img src=" ' . base_url($product['image']) . ' "" class="image-100 image-box-100 img-fluid" /> 
            </a></div>';
                $rows[$i] = [
                    "id" => $product_id,
                    "image" => $product['image'],
                    "name" => $name,
                    "stock" => $stock,
                    "warehouse_stock" => $warehouse_stock,
                    "action" => '
                    <div class="d-flex gap-2">
                            <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-name ="' . $name . '"  data-stock ="' . $stock . '" data-product_id ="' . $product_id . '"  data-stock_management ="' . $product['stock_management'] . '" data-variant_id=' . $variant_id  . '    data-bs-target="#new_stock"><i class="fas fa-edit"></i></a>
                            <a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-name ="' . $name . '"  data-stock ="' . $stock . '" data-product_id ="' . $product_id . '"  data-stock_management ="' . $product['stock_management'] . '" data-variant_id=' . $variant_id  . '    data-bs-target="#transfer_stock"  data-bs-toggle="tooltip" data-bs-placement="right" title="Tooltip on right" ><i class="fas fa-exchange-alt"></i> </a>
                    </div>
                    '
                ];
                $i++;
            }
            $array['total'] = count($products);
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }

    public function scanned_barcode_items()
    {

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        $settings = get_settings('general', true);
        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '$';
        $data = $_GET;
        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|numeric',
            'variant_id' => 'required',
        ];
        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
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
            $product_id = "";
            $value = ($this->request->getGet('variant_id') != '') ? $this->request->getGet('variant_id') : '';
            $is_barcode_column = true;
            if (!empty($value)) {
                // find with barcode column 
                $product_id = fetch_details("products_variants", ['barcode' => "$value"], ['product_id']);
                if (empty($product_id)) {
                    $is_barcode_column = false;
                    $product_id = fetch_details("products_variants", ['id' => "$value"], ['product_id']);
                }
                if ($is_barcode_column) {
                    $_id = fetch_details("products_variants", ['barcode' => "$value"], ['id']);
                } else {
                    $_id = fetch_details("products_variants", ['id' => "$value"], ['id']);
                }
                $_id = $_id[0]['id'];
                $product_id = (isset($product_id[0]['product_id'])) ? $product_id[0]['product_id'] : "";
            }
            if (!empty($product_id)) {
                $products = fetch_products($business_id, "", "", "", 1, 0, "id", "DESC", "id", ["$product_id"]);

                if (!empty($products)) {
                    /** filter or remove other variants */
                    $products['products'][0]['variants'] = array_filter($products['products'][0]['variants'], function ($variant) use ($_id) {
                        return ($variant['id'] == $_id);
                    });

                    $response = [
                        'error' => false,
                        'message' => "Product retrieved successfully!",
                        'data' => $products['products'][0]
                    ];
                } else {
                    $response = [
                        'error' => true,
                        'message' => "Sorry, No product found!",
                        'data' => ""
                    ];
                }
            } else {
                $response = [
                    'error' => true,
                    'message' => "Sorry, No product found!",
                    'data' => ""
                ];
            }
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            $response['currency'] = $currency;
            return $this->response->setJSON($response);
        }
    }

    public function stock_alert()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $model = new Products_model();
        $products = $model->get_product_details($business_id);

        // Get low stock from warehouses
        $warehouseStockModel = new WarehouseProductStockModel();
        $lowWarehouseStock = $warehouseStockModel->get_low_warehouse_stock($business_id); // Get warehouse stock alerts

        $i = 0;
        $rows = [];

        // Iterate through the products from get_product_details
        if (!empty($products)) {
            foreach ($products as $product) {
                if ($product['qty_alert'] > $product['stock']) {
                    $product['stock_management'] = $product['stock_management'] == "1" ? "Product" : ($product['stock_management'] == "2" ? "Variable" : "NA");

                    $rows[$i] = [
                        "product_id" => $product['product_id'],
                        "product" => $product['product'],
                        "variant_name" => $product['variant_name'],
                        "stock" => $product['stock'],
                        "qty_alert" => $product['qty_alert'],
                        "stock_management" => $product['stock_management'],
                        "action" => '<a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-stock ="' . $product['stock'] . '" data-product_id ="' . $product['product_id'] . '" data-stock_management ="' . $product['stock_management'] . '" data-bs-target="#new_stock"><i class="fas fa-edit"></i></a>'
                    ];
                    $i++;
                }
            }
        }

        // Add low warehouse stock products to the same rows array
        if (!empty($lowWarehouseStock)) {
            foreach ($lowWarehouseStock as $warehouseProduct) {
                $rows[$i] = [
                    "product_id" => $warehouseProduct['product_variant_id'],
                    "product" => $warehouseProduct['product_name'],
                    "variant_name" => $warehouseProduct['variant_name'],
                    "stock" => $warehouseProduct['stock'],
                    "qty_alert" => $warehouseProduct['qty_alert'],
                    "stock_management" => "Warehouse",
                    "action" => '<a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" data-stock ="' . $warehouseProduct['stock'] . '" data-product_id ="' . $warehouseProduct['product_variant_id'] . '" data-stock_management ="Warehouse" data-bs-target="#new_stock"><i class="fas fa-edit"></i></a>'
                ];
                $i++;
            }
        }
        $array['rows'] = !empty($rows) ? $rows : [];
        return $this->response->setJSON($array);
    }

    public function get_taxs()
    {
        $tax_model = new Tax_model();
        $taxs = $tax_model->where('status', 1)->get()->getResultArray();

        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        $response['taxs'] = $taxs;
        return $this->response->setJSON($response);
    }

    public function save_transfer()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        $this->validation->setRules([
            'ts_variant_id' => ['rules' => 'required', 'label' => 'Product'],
            'ts_name' => ['rules' => 'required', 'label' => 'Product'],
            'ts_from_warehouse_id' => ['rules' => 'required', 'label' => 'From warehouse'],
            'ts_to_warehouse_id' => ['rules' => 'required', 'label' => 'To warehouse'],
            'ts_quantity' => ['rules' => 'required|greater_than[0]', 'label' => 'Quantity'],
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON([
                'error' => true,
                'message' => $this->validation->getErrors(),
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $from_warehouse_id = $this->request->getVar('ts_from_warehouse_id');
        $to_warehouse_id = $this->request->getVar('ts_to_warehouse_id');
        $qty = $this->request->getVar('ts_quantity');
        $variant_id = $this->request->getVar('ts_variant_id');
        $vendor_id = 0;
        if ($this->ionAuth->isTeamMember()) {
            $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
        } else {
            $vendor_id = session('user_id');
        }
        $business_id = session('business_id');

        // Validate the warehouses are different
        if ($from_warehouse_id == $to_warehouse_id) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ["Cannot transfer stock to the same warehouse!"],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $warehouse_product_stock_model = new WarehouseProductStockModel();
        $currentDateTime = date('Y-m-d H:i:s');

        $from_warehouse = $warehouse_product_stock_model->where([
            'warehouse_id' => $from_warehouse_id,
            'product_variant_id' => $variant_id,
        ])->get()->getResultArray();

        if (empty($from_warehouse)) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ["Product is not available in the 'From warehouse'!"],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        $current_from_warehouse_stock = $from_warehouse[0]['stock'];

        // Check for sufficient stock before proceeding
        if ($qty > $current_from_warehouse_stock) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ["Insufficient stock in the 'From warehouse'!"],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        // Proceed to stock transfer using a transaction
        $db = \Config\Database::connect();
        $db->transStart();

        // Update the from warehouse stock
        $updated_from_stock = $current_from_warehouse_stock - $qty;
        $warehouse_product_stock_model->update($from_warehouse[0]['id'], ['stock' => $updated_from_stock]);

        // Handle the to warehouse stock update
        $to_warehouse = $warehouse_product_stock_model->where([
            'warehouse_id' => $to_warehouse_id,
            'product_variant_id' => $variant_id,
        ])->get()->getResultArray();

        $updated_to_stock = (!empty($to_warehouse) ? $to_warehouse[0]['stock'] : 0) + $qty;

        $data = [
            'warehouse_id' => $to_warehouse_id,
            'product_variant_id' => $variant_id,
            'stock' => $updated_to_stock,
            'qty_alert' => 0,
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'updated_at' => $currentDateTime,
            'created_at' => !empty($to_warehouse) ? $to_warehouse[0]['created_at'] : $currentDateTime,
        ];

        if (!empty($to_warehouse)) {
            $data['id'] = $to_warehouse[0]['id'];  // Include the ID if it's an update
        }

        $warehouse_product_stock_model->save($data);

        // Complete the transaction
        $db->transComplete();

        if ($db->transStatus() === false) {
            return $this->response->setJSON([
                'error' => true,
                'message' => ['Stock transfer failed due to a database error.'],
                'csrf_token' => csrf_token(),
                'csrf_hash' => csrf_hash(),
            ]);
        }

        return $this->response->setJSON([
            'error' => false,
            'message' => 'Stock transfer completed successfully.',
            'csrf_token' => csrf_token(),
            'csrf_hash' => csrf_hash(),
        ]);
    }
}
