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
    protected $business_id;

    protected $category_model;
    protected $warehouse_model;
    protected $brand_model;
    protected $tax_model;
    protected $units_model;
    protected $products_model;
    protected $products_variants_model;
    protected $warehouse_product_stock_model;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem', 'products']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();

        $this->business_id = session('business_id');
        $this->category_model = new Categories_model();
        $this->warehouse_model = new WarehouseModel();
        $this->brand_model = new BrandModel();
        $this->tax_model = new Tax_model();
        $this->units_model = new Units_model();
        $this->products_model = new Products_model();
        $this->products_variants_model = new products_variants_model();
        $this->warehouse_product_stock_model = new WarehouseProductStockModel();
    }
    public function index()
    {
        $data = $this->getdata('Products', 'brands', (new BrandModel())->findAll(), VIEWS . "products_table", 'categories', (new Categories_model())->get_categories(getUserId(), $this->business_id));
        return view("admin/template", $data);
    }

    private function getData($fromTitle, $tableName, $tableData, $page, $optionalData1 = '', $optionalData1Value = '', $optionalData2 = '', $optionalData2Value = '',)
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
            'title' => "Products - " . $settings['title'] ?? "",
            'from_title' => $fromTitle,
            'meta_keywords' => "subscriptions app, digital subscription, daily subscription, software, app, module",
            'meta_description' => "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems",
            $tableName => $tableData,
            'user' => $this->ionAuth->user(session('user_id'))->row(),
            'user_id' => getUserId(),
            'vendor_id' => getUserId(),
            'currency' => $settings['currency_symbol'] ?? '₹',
            $optionalData1 => $optionalData1Value,
            $optionalData2 => $optionalData2Value,
        ];
    }

    public function stock($flag = "")
    {
        // $data = $this->getdata('categories', $this->category_model->get_categories(getUserId(), $this->business_id), VIEWS . "stock_table");
        $data['flag'] = $flag;

        return view("admin/template", $data);
    }

    public function Add_products()
    {
        $user_id = getUserId();
        $data = $this->getData(
            'Add Products',
            'categories',
            $this->category_model->get_categories($user_id, $this->business_id),
            FORMS . "product",
            'brands',
            $this->brand_model->where('business_id', $this->business_id)->findAll(),
            'units',
            $this->units_model->get_units_for_forms($user_id),
        );


        return view("admin/template", $data);
    }

    public function save_products()
    {
        if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
            return $this->response->setJSON(csrfResponseData([
                'error' => true,
                'message' => [DEMO_MODE_ERROR],
                'data' => []
            ]));
        }

        if (empty($_POST)) {
            return false;
        }

        // validation rules
        $this->validation->setRules([
            'name' => 'required',
            'description' => 'required',
            'variant_name' => 'required',
            'unit_id.*' => 'required|numeric'
        ]);

        if (!$this->validation->withRequest($this->request)->run()) {
            return $this->response->setJSON(csrfResponseData([
                'error' => true,
                'message' => $this->validation->getErrors(),
                'data' => []
            ]));
        }

        $product_id = $this->request->getVar('product_id');
        if ($this->ionAuth->isTeamMember()) {
            $permission = $product_id ? 'can_update' : 'can_create';
            if (!userHasPermission('products', $permission, session('user_id'))) {
                return $this->response->setJSON(csrfResponseData([
                    'success' => false,
                    'message' => ['You do not have permission for this action'],
                    'data' => []
                ]));
            }
        }

        // image choosing
        $old_icon = $this->request->getVar('old_image');
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

        $status = isset($_POST['status']) ? "1" : "0";
        $brand_id = $this->request->getVar('brand_id') ?? null;

        $product_data = [
            'id' => $product_id,
            'vendor_id' => getUserId(),
            'business_id' => $this->business_id,
            'category_id' => $this->request->getVar('category_id'),
            'name' => $this->request->getVar('name'),
            'description' => $this->request->getVar('description'),
            'image' => $image,
            'brand_id' => $brand_id,
            'status' => $status
        ];

        $this->products_model->save($product_data);

        $product_id = $product_id ?: $this->products_model->getInsertID();
        $variantName = $this->request->getVar('variant_name');
        $barcodes =  $this->request->getVar('variant_barcode');

        if ($variantName) {
            $variant_count = count($variantName);
            $variant_ids = $this->request->getVar('variant_id');
            $variants = [];

            for ($i = 0; $i < $variant_count; $i++) {
                if (isset($variant_ids[$i])) {
                    $variants['id'] = $variant_ids[$i];
                }

                if (isset($this->request->getVar('qty_alert')[$i])) {
                    $variants['qty_alert'] = $this->request->getVar('qty_alert')[$i];
                } else {
                    $variants['qty_alert'] = '0';
                }
                $variants['unit_id']  = $this->request->getVar('unit_id')[$i];
                $variants['product_id'] = $product_id;
                $variants['variant_name'] = $this->request->getVar('variant_name')[$i];
                $variants['barcode'] = isset($barcodes[$i])  && !empty($barcodes[$i]) ? $barcodes[$i] : null;
                $variants['status'] = $status;
                $this->products_variants_model->save($variants);

                if (isset($variants['id'])) {
                    unset($variants['id']);
                }
            }
        }

        $response = [
            'success' => true,
            'message' => 'Product saved successfully',
            'data' => []
        ];
        $response['csrf_token'] = csrf_token();
        $response['csrf_hash'] = csrf_hash();
        return $this->response->setJSON($response);
    }
    public function products_table()
    {
        $rows = $this->products_model->get_all_products($this->business_id);
        return $this->response->setJSON([
            'rows' => array_map('prepareProductsRow', $rows['data']),
            'total' => $rows['total']
        ]);
    }

    public function edit_product($product_id = "")
    {

        $user_id = getUserId();

        $data = $this->getData(
            'edit_product',
            'categories',
            $this->category_model->get_categories($user_id, $this->business_id),
            FORMS . "product",
            'brands',
            $this->brand_model->where('business_id', $this->business_id)->findAll(),
            'units',
            $this->units_model->get_units_for_forms($user_id)
        );

        $products = get_products_with_variants($product_id);
        if (empty($products)) {
            return view("errors/html/error_404");
        }

        $product = $products[0];
        $data['products'] = $product;

        // Attach unit name to variants
        foreach ($product['variants'] as &$variant) {
            if (!empty($variant['unit_id'])) {
                $unit = $this->units_model->find($variant['unit_id']);
                $variant['variant_unit_name'] = $unit['name'] ?? '';
            }
        }
        $data['variants'] = $product['variants'];

        // Attach category name
        if (!empty($product['category_id'])) {
            $category = $this->category_model->find($product['category_id']);
            $data['category_name'] = $category['name'] ?? '';
        }

        // Attach brand name
        if (!empty($product['brand_id'])) {
            $brand = $this->brand_model->find($product['brand_id']);
            $data['brand_name'] = $brand['name'] ?? '';
        }

        return view("admin/template", $data);
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

    public function products_variants_list()
    {

        $data = $this->request->getGet();
        $search = $data['search'] ?? '';

        $variants = $this->products_variants_model->getProductVariants($search);
        log_message('debug', print_r($variants, true));
        return $this->response->setJSON([
            'variants' => $variants,
        ]);
    }

    public function json()
    {
        // 1. Ensure this is an AJAX request
        if (!$this->request->isAJAX()) {
            return redirect()->to('/');
        }
        // session('user_id')->set();
        // Use helper to safely fetch GET parameters
        $data = $this->request->getGet();

        $product_id = $data['id'] ?? '';
        $settings = get_settings('general', true);
        $currency = $settings['currency_symbol'] ?? '$';
        $data['business_id'] = $this->business_id;

        // Build validation rules dynamically
        $rules = [
            'business_id' => 'required|numeric',
        ];
        if (!empty($data['category_id'])) {
            $rules['category_id'] = 'numeric';
        }
        if (!empty($data['limit'])) {
            $rules['limit'] = 'numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if (!empty($data['offset'])) {
            $rules['offset'] = 'numeric|greater_than_equal_to[0]';
        }

        // Validate input
        $this->validation->setRules($rules);
        if (!$this->validation->run($data)) {
            return csrfResponseData([
                'error' => true,
                'message' => $this->validation->getErrors(),
                'data' => []
            ]);
        }

        // Assign validated values with defaults
        $business_id = $data['business_id'];
        $category_id = $data['category_id'] ?? '';
        $brand_id    = $data['brand_id'] ?? '';
        $limit       = $data['limit'] ?? 10;
        $offset      = $data['offset'] ?? 0;
        $sort        = $data['sort'] ?? 'id';
        $order       = $data['order'] ?? 'DESC';
        $search      = $data['search'] ?? '';

        // Fetch products
        $products = fetch_products($business_id, $category_id, $brand_id, $search, $limit, $offset, $sort, $order, '', [], ['product_id' => $product_id]);

        $final_product_list = [];
        $final_variants = [];

        // Process and transform product data
        foreach ($products['products'] ?? [] as $product) {
            $category_name = category_name($product['category_id']);
            foreach ($product['variants'] as &$variant) {
                $variant['image']    = $product['image'];
                $variant['name']     = $product['name'];
                $variant['category'] = $category_name;
                $final_variants[]    = $variant;
            }
            $final_product_list[] = $product;
        }
        // Build and return JSON response
        return $this->response->setJSON([
            'error'    => empty($products['products']),
            'message'  => !empty($products['products']) ? 'Products fetched successfully' : 'No products found!',
            'data'     => $final_product_list,
            'variants' => $final_variants,
            'total'    => $products['total'],
            'currency' => $currency,
        ]);
    }

    public function stock_table($flag = "")
    {
        $products = $this->products_model->get_product_details($this->business_id, $flag);
        $i = 0;
        if (!empty($products)) {
            foreach ($products as $product) {

                $rows[$i] = [
                    "product_id" => $product['product_id'],
                    "product" => $product['product'],
                    "variant_name" => $product['variant_name'],
                    "stock" => $product['stock'],
                    "action" => '<a type="button" class="btn btn-primary text-white" data-bs-toggle="modal"  data-stock ="' . $product['stock'] . '" data-product_id ="' . $product['product_id'] . '" data-bs-target="#new_stock"><i class="fas fa-edit"></i>'
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
    public function fetch_stock()
    {
        // $products = fetch_stock($this->business_id, 'stock_management', ['1', '2']);
        // $final_product_list = array();

        // foreach ($products as $product) {

        //     $stock = $product['stock_management'] == '1' ? $product['product_stock'] : $product['variant_stock'];
        //     $name =  $product['name'] . " - " . $product['variant_name'];
        //     $product_id = $product['stock_management'] == '1' ? $product['id'] : $product['variant_id'];
        //     $val['stock_management'] = $product['stock_management'];
        //     $val['image'] = $product['image'];
        //     $val['id'] = $product_id;
        //     $val['name'] = $name;
        //     $val['stock'] = $stock;
        //     $final_product_list[] = $val;
        // }
        // $response['data'] = $final_product_list;
        // return $this->response->setJSON($response);
    }

    //  new adjustment stock
    public function save_adjustment()
    {
        if (isset($_POST) && !empty($_POST)) {
            $this->validation->setRules([
                'product' => 'required|numeric',
                'variant_id' => 'required|numeric',
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

                update_details(['stock' => (string) $stock], ['id' => $_POST['product']], 'products_variants');

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

    public function table()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $products = fetch_stock($business_id);
        $i = 0;
        $warehouse_product_stock_model = new WarehouseProductStockModel();

        if (!empty($products)) {
            foreach ($products as $product) {


                $product['stock_management'] == "NA";

                $stock = $product['variant_stock'];
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
        try {
            $variantModel = new Products_variants_model();
            $lowVariantStock = $variantModel->get_low_variant_stock($this->business_id);

            $rows = [];
            $i = 0;

            // Process variant-level alerts
            foreach ($lowVariantStock as $variant) {
                $rows[$i] = [
                    "product_id" => $variant['product_id'],
                    "variant_id" => $variant['id'],
                    "product" => $variant['product_name'],
                    "variant_name" => $variant['variant_name'],
                    "stock" => $variant['stock'],
                    "qty_alert" => $variant['qty_alert'],

                    "action" => '<a type="button" class="btn btn-primary text-white" data-bs-toggle="modal" 
                                data-stock="' . $variant['stock'] . '" 
                                data-product_id="' . $variant['product_id'] . '" 
                                data-variant_id="' . $variant['id'] . '" 
                                data-stock_management="2" 
                                data-bs-target="#new_stock">
                                <i class="fas fa-edit"></i></a>'
                ];
                $i++;
            }

            return $this->response->setJSON([
                'rows' => $rows,
                'total' => count($rows)
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Stock alert error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'error' => true,
                'message' => 'An error occurred while fetching stock alerts'
            ]);
        }
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
