<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Categories_model;
use App\Models\Delivery_boys_model;
use App\Models\Expenses_Type_model;
use App\Models\Orders_items_model;
use App\Models\Orders_model;
use App\Models\Orders_services_model;
use App\Models\Products_model;
use App\Models\Products_variants_model;
use App\Models\Purchases_items_model;
use App\Models\Purchases_model;
use App\Models\Services_model;
use App\Models\Suppliers_model;
use App\Models\Tax_model;
use App\Models\Vendors_model;
use CodeIgniter\Session\Session;

class Bulk_Uploads extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }

    private function isValidTaxString($taxString)
    {
        // Define the regex pattern for validation
        $pattern = '/^\["\d+"(,"[\d]+")*\]$/';

        // Use preg_match to check if the tax string matches the pattern
        return preg_match($pattern, $taxString) === 1;
    }
    //done
    public function import_products()
    {

        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            $id = $this->ionAuth->getUserId();


            if (isset($_POST) && !empty($_POST)) {

                $this->validation->setRules([
                    'business_id' => 'required',
                ]);

                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }
                if (!$this->validation->withRequest($this->request)->run()) {

                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'] = $e;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                    }
                    return $this->response->setJSON($response);
                } else {

                    $oration_type = $this->request->getVar('type');

                    if (empty($oration_type)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Select Type  !';
                        return $this->response->setJSON($response);
                    }

                    $file =  $this->request->getFile('file');
                    // $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $type = $this->request->getVar('type');
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    if ($file->isValid() && !$file->hasMoved()) {
                        $filePath = WRITEPATH . 'uploads/' . $file->getName();
                        $file->move(WRITEPATH . 'uploads/');

                        if ($oration_type == "upload") {
                            if (($handle = fopen($filePath, 'r')) !== false) {
                                $productModel = new Products_model();
                                $productVariantModel = new Products_variants_model();

                                fgetcsv($handle); // Skip header

                                $products = [];
                                $rowCount = 1;
                                $business_id  = $_SESSION['business_id'];
                                $vendor_id = $_SESSION['user_id'];
                                if ($this->ionAuth->isTeamMember()) {
                                    $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                                } else {
                                    $vendor_id = $_SESSION['user_id'];
                                }

                                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                                    $productName = $data[4];
                                    if (!isset($products[$productName])) {
                                        // Insert product

                                        $productData = [
                                            'category_id' => $data[0],
                                            'business_id' =>  $business_id,
                                            'vendor_id' => $vendor_id,
                                            'tax_ids' => $data[3],
                                            'name' => $productName,
                                            'description' => $data[5],
                                            'qty_alert' => $data[6],
                                            'image' => $data[7],
                                            'type' => $data[8],
                                            'stock_management' => $data[9],
                                            'stock' => $data[10],
                                            'unit_id' => $data[11],
                                            'is_tax_included' => $data[12],
                                            'status' => $data[13],
                                        ];
                                        if (empty($productData['tax_ids'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'tax_ids is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        } {
                                            $taxString = $productData['tax_ids'];
                                            if (! $this->isValidTaxString($taxString)) {
                                                // The tax string is invalid;

                                                $response['error'] = true;
                                                $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (empty($productData['category_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Category id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['business_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Business id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['vendor_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Vendor id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (! $productData['stock_management'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['stock_management'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Stock Management is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if ($productData['stock_management'] == 0) {
                                            $productData['stock'] = 0;
                                            $productData['unit_id'] = 0;
                                        }

                                        if ($productData['stock_management'] ==  2) {
                                            $productData['type'] = "variable";
                                            $productData['stock'] = 0;
                                            $productData['unit_id'] = 0;
                                            $productData['qty_alert'] == "";
                                        }

                                        if (! $productData['stock'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['stock'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Stock is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['unit_id'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['unit_id'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Unit Id is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['qty_alert'] == "") { // if the value is empty string it will ignore the validation.
                                            if (empty($productData['qty_alert'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Qty Alert is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['is_tax_included'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['is_tax_included'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Is tax included Id is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (empty($productData['name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Name is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['description'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Description is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        if (empty($productData['image'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Image is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['type'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Type is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        if (empty($productData['status'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Status is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }


                                        $productId = $productModel->insert($productData);

                                        // Store product ID to use for variants
                                        $products[$productName] = $productId;
                                    } else {
                                        $productId = $products[$productName];
                                    }

                                    // Insert variant
                                    if ($productData['stock_management'] == 2) {
                                        $variantData = [
                                            'product_id' => $productId,
                                            'variant_name' => $data[14],
                                            'sale_price' => $data[15],
                                            'purchase_price' => $data[16],
                                            'stock' => $data[17],
                                            'qty_alert' => $data[18],
                                            'unit_id' => $data[19],
                                            'status' => $data[20],
                                        ];
                                        $productVariantModel->insert($variantData);
                                    }
                                }

                                fclose($handle);
                                $response['error'] = false;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Products Uploaded successfully!';
                                return $this->response->setJSON($response);
                            } else {
                                $response['error'] = true;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Failed to open the file.';
                                return $this->response->setJSON($response);
                            }
                        } else {

                            if (($handle = fopen($filePath, 'r')) !== false) {
                                $productModel = new Products_model();
                                $productVariantModel = new Products_variants_model();

                                fgetcsv($handle); // Skip header

                                $products = [];
                                $rowCount = 1;
                                $business_id  = $_SESSION['business_id'];
                                $vendor_id = $_SESSION['user_id'];

                                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                                    $productName = $data[5];
                                    if (!isset($products[$productName])) {
                                        // Insert product

                                        $productData = [
                                            'id' => $data[0],
                                            'category_id' => $data[1],
                                            'business_id' =>  $business_id,
                                            'vendor_id' => $vendor_id,
                                            'tax_id' => $data[4],
                                            'name' => $productName,
                                            'description' => $data[6],
                                            'qty_alert' => $data[7],
                                            'image' => $data[8],
                                            'type' => $data[9],
                                            'stock_management' => $data[10],
                                            'stock' => $data[11],
                                            'unit_id' => $data[12],
                                            'is_tax_included' => $data[13],
                                            'status' => $data[14],
                                        ];

                                        /*
                                            expected data;
                                            data
                                            (
                                                data[0] => id (product id)
                                                data[1] => category_id
                                                data[2] => business_id
                                                data[3] => vendor_id
                                                data[4] => tax_ids
                                                data[5] => name
                                                data[6] => description
                                                data[7] => qty_alert
                                                data[8] => image
                                                data[9] => type
                                                data[10] => stock_management
                                                data[11] => stock
                                                data[12] => unit_id
                                                data[13] => is_tax_included
                                                data[14] => status
                                                data[15] => variant_id
                                                data[16] => variant_name
                                                data[17] => sale_price
                                                data[18] => purchase_price
                                                data[19] => variant_stock
                                                data[20] => variant_qty_alert
                                                data[21] => variant_unit_id
                                                data[22] => variant_status
                                            )
                                            $productData
                                            (
                                                $productData[id] => id (product id)
                                                $productData[category_id] => category_id
                                                $productData[business_id] => 3
                                                $productData[vendor_id] => 1
                                                $productData[tax_ids] => tax_ids
                                                $productData[name] => tax_id
                                                $productData[description] => description
                                                $productData[qty_alert] => qty_alert
                                                $productData[image] => image
                                                $productData[type] => type
                                                $productData[stock_management] => stock_management
                                                $productData[stock] => stock
                                                $productData[unit_id] => unit_id
                                                $productData[is_tax_included] => is_tax_included
                                                $productData[status] => status
                                            )
                                        */


                                        if (empty($productData['tax_ids'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'tax_ids is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        } {
                                            if (! isValidTaxString($taxString)) {
                                                // The tax string is invalid;

                                                $response['error'] = true;
                                                $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (empty($productData['category_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Category id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['business_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Business id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['vendor_id'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Vendor id is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (! $productData['stock_management'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['stock_management'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Stock Management is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if ($productData['stock_management'] == 0) {
                                            $productData['stock'] = 0;
                                            $productData['unit_id'] = 0;
                                        }

                                        if ($productData['stock_management'] ==  2) {
                                            $productData['type'] = "variable";
                                            $productData['stock'] = 0;
                                            $productData['unit_id'] = 0;
                                            $productData['qty_alert'] == "";
                                        }

                                        if (! $productData['stock'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['stock'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Stock is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['unit_id'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['unit_id'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Unit Id is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['qty_alert'] == "") { // if the value is empty string it will ignore the validation.
                                            if (empty($productData['qty_alert'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Qty Alert is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }

                                        if (! $productData['is_tax_included'] == 0) { // if the value is zero it will ignore the validation.
                                            if (empty($productData['is_tax_included'])) {
                                                $response['error'] = true;
                                                $response['message'] = 'Is tax included Id is empty at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            }
                                        }





                                        if (empty($productData['name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Name is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($productData['description'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Description is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        if (empty($productData['image'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Image is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        } else {
                                            $productData['image'] = "public/uploads/products/" . $productData['image'];
                                        }
                                        if (empty($productData['type'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Type is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }

                                        if (empty($productData['status'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Status is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }


                                        $productModel->update($productData['id'], $productData);
                                        $productId =  $productData['id'];

                                        // Store product ID to use for variants
                                        $products[$productName] = $productId;
                                    } else {
                                        $productId = $products[$productName];
                                    }

                                    // Insert variant
                                    if ($productData['stock_management'] == 2) {
                                        $variantId = $data[15];
                                        $variantData = [
                                            'variant_name' => $data[16],
                                            'sale_price' => $data[17],
                                            'purchase_price' => $data[18],
                                            'stock' => $data[19],
                                            'qty_alert' => $data[20],
                                            'unit_id' => $data[21],
                                            'status' => $data[22],
                                        ];

                                        $productVariantModel->update($variantId, $variantData);
                                    }

                                    $rowCount++;
                                }

                                fclose($handle);
                                $response['error'] = false;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Products updated successfully!';
                                return $this->response->setJSON($response);
                            } else {
                                $response['error'] = true;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                $response['message'] = 'Failed to open the file.';
                                return $this->response->setJSON($response);
                            }
                        }
                    } else {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] =  'Failed to upload the file.';
                        return $this->response->setJSON($response);
                    }
                }
            }
        }
    }
    //done
    public function import_categories()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            $id = $this->ionAuth->getUserId();

            if (isset($_POST) && !empty($_POST)) {
                $category_model = new Categories_model();
                $this->validation->setRules([
                    'vendor_id' => 'required',
                ]);
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }
                if (!$this->validation->withRequest($this->request)->run()) {

                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'] = $e;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                    }
                    return $this->response->setJSON($response);
                } else {
                    //   insert operation here
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }
                    $csv = $_FILES['file']['tmp_name'];
                    $temp = 0;
                    $temp1 = 0;
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    if ($type == 'upload') {
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                        {

                            if ($temp != 0) {

                                if (empty($row[1])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Vendor ID is empty at row ' . $temp;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($row[2])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Category Name is empty at row ' . $temp;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            $temp++;
                        }

                        fclose($handle);
                        $handle = fopen($csv, "r");
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                        {
                            if ($temp1 != 0) {
                                if (!empty($row[0])) {
                                    $data['parent_id'] = $row[0];
                                }
                                if (!empty($row[1])) {
                                    $data['vendor_id'] = $row[1];
                                }
                                $data['name'] = $row[2];
                                if (!empty($row[3])) {
                                    $data['status'] = $row[3];
                                }
                                $category_model->save($data);
                            }
                            $temp1++;
                        }
                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Categories uploaded successfully!';

                        return $this->response->setJSON($response);
                    } else {
                        // update operation here
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                        {
                            if ($temp != 0) {
                                if (empty($row[2])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Vendor ID is empty at row ' . $temp;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                                if (empty($row[3])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Name is empty at row ' . $temp;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            $temp++;
                        }

                        fclose($handle);
                        $handle = fopen($csv, "r");
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                        {
                            if ($temp1 != 0) {
                                $category_id = $row[0];
                                $data['id'] = $category_id;
                                $category = fetch_details('categories', ['id' => $category_id]);
                                if (isset($category[0]) && !empty($category[0])) {
                                    if (!empty($row[1])) {
                                        $data['parent_id'] = $row[1];
                                    } else {
                                        $data['parent_id'] = $category[0]['parent_id'];
                                    }
                                    if (!empty($row[2])) {
                                        $data['vendor_id'] = $row[2];
                                    } else {
                                        $data['vendor_id'] = $category[0]['vendor_id'];
                                    }
                                    if (!empty($row[3])) {
                                        $data['name'] = $row[3];
                                    } else {
                                        $data['name'] = $category[0]['name'];
                                    }
                                    if (!empty($row[4])) {
                                        $data['status'] = $row[4];
                                    } else {
                                        $data['status'] = $category[0]['status'];
                                    }

                                    $category_model->save($data);
                                }
                            }
                            $temp1++;
                        }
                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Categories updated successfully!';
                        return $this->response->setJSON($response);
                    }
                }
            } else {
                return redirect()->to('admin/categories');
            }
        }
    }

    //done
    public function import_stock()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'business_id' => 'required',
                ]);
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }
                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'] = $e;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['data'] = [];
                    }
                    return $this->response->setJSON($response);
                } else {
                    //   insert operation here
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }
                    $csv = $_FILES['file']['tmp_name'];
                    $temp = 0;
                    $temp1 = 0;
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    // update operation here
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row vales
                    {
                        if ($temp != 0) {
                            if (empty($row[0])) {
                                $response['error'] = true;
                                $response['message'] = 'Product ID is empty at row ' . $temp;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($row[1])) {
                                $response['error'] = true;
                                $response['message'] = 'Stock Management Type is empty at row ' . $temp;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($row[2])) {
                                $response['error'] = true;
                                $response['message'] = 'Current Stock is empty at row ' . $temp;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($row[3])) {
                                $response['error'] = true;
                                $response['message'] = 'Quantity is empty at row ' . $temp;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                        }
                        $temp++;
                    }

                    fclose($handle);
                    $handle = fopen($csv, "r");
                    while (($row = fgetcsv($handle, 10000, ",")) != FALSE) //get row values
                    {
                        /*
                            $row[0] => product_id(mandatory)
                            $row[1] => stock_management(mandatory)
                            $row[2] => current_stock(mandatory)
                            $row[3] => quantity(mandatory)
                            $row[4] => type(add / subtract)
                            $row[5] => note
                         */
                        if ($temp1 != 0) {
                            $product_id = $row[0];
                            if (trim($row[4]) == 'add') {
                                $stock = floatval($row[2]) + floatval($row[3]);
                            }
                            if (trim($row[4]) == 'subtract') {
                                $stock = floatval($row[2]) - floatval($row[3]);
                            }
                            if ($row[1] == '1') {
                                update_details(['stock' => $stock], ['id' => $product_id], 'products');
                            }
                            if ($row[1] == '2') {

                                update_details(['stock' => $stock], ['id' => $product_id], 'products_variants');
                            }
                        }
                        $temp1++;
                    }
                    fclose($handle);
                    $response['error'] = false;
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['message'] = 'Stock updated successfully!';
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('admin/products/manage_stock');
            }
        }
    }

    // done
    public function import_orders()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        // Check if the user is an admin
        if ($this->ionAuth->isAdmin()) {
            // Proceed with the order import process for admins without subscription check
            if (isset($_POST) && !empty($_POST)) {
                $vendors_model = new Vendors_model();
                $order_model = new Orders_model();
                $order_items_model = new Orders_items_model();
                $order_service_model = new Orders_services_model();

                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for order data
                ]);

                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle orders
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv', 'text/plain');
                    $mime = $file->getMimeType();

                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    if ($type == 'upload') {
                        $rowCount = 1;
                        $lastOrderNo = null;
                        $Tax_model = new Tax_model();
                        // Inside the while loop where orders are processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            $orderNo = $row[2];

                            // Fetch the logged-in user's data
                            $user_data = $this->ionAuth->user()->row();

                            // Assuming Vendors_model has a method to fetch vendor data by user ID
                            $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                            // Extract the vendor_id from the fetched data
                            if ($vendor_data) {
                                $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                            } else {
                                // Handle the case where vendor data is not found for the user
                                // You may throw an error, redirect, or handle it as per your application logic
                            }



                            /* expected data from file
                                Array
                                (
                                    $row[0] => vendor_id
                                    $row[1] => customer_id
                                    $row[2] => order_no
                                    $row[3] => business_id
                                    $row[4] => created_by
                                    $row[5] => total
                                    $row[6] => delivery_charges
                                    $row[7] => discount
                                    $row[8] => final_total
                                    $row[9] => payment_status
                                    $row[10] => amount_paid
                                    $row[11] => order_type
                                    $row[12] => message
                                    $row[13] => payment_method
                                    $row[14] => transaction_id
                                    $row[15] => created_at
                                    $row[16] => updated_at
                                    $row[17] => product_id
                                    $row[18] => product_variant_id
                                    $row[19] => product_name
                                    $row[20] => quantity
                                    $row[21] => price
                                    $row[22] => tax_name
                                    $row[23] => tax_percentage
                                    $row[24] => is_tax_included
                                    $row[25] => tax_details
                                    $row[26] => sub_total
                                    $row[27] => status
                                    $row[28] => delivery_boy
                                    $row[29] => service_id
                                    $row[30] => service_name
                                    $row[31] => price
                                    $row[32] => quantity
                                    $row[33] => unit_name
                                    $row[34] => unit_id
                                    $row[35] => sub_total
                                    $row[36] => tax_name
                                    $row[37] => tax_percentage
                                    $row[38] => is_tax_included
                                    $row[39] => tax_details
                                    $row[40] => is_recursive
                                    $row[41] => recurring_days
                                    $row[42] => starts_on
                                    $row[43] => ends_on
                                    $row[44] => delivery_boy
                                    $row[45] => status
                                )
                            */

                            // Insert orders into database
                            $order_data = [
                                'vendor_id' => $vendor_id,
                                'customer_id' => $row[1],
                                'order_no' => $row[2],
                                'business_id' => $row[3],
                                'created_by' => $row[4],
                                'total' => $row[5],
                                'delivery_charges' => $row[6],
                                'discount' => $row[7],
                                'final_total' => $row[8],
                                'payment_status' => $row[9],
                                'amount_paid' => $row[10],
                                'order_type' => $row[11],
                                'message' => $row[12],
                                'payment_method' => $row[13],
                            ];


                            if (empty($order_data['order_no'])) {
                                $response['error'] = true;
                                $response['message'] = 'Order no is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($order_data['customer_id'])) {
                                $response['error'] = true;
                                $response['message'] = 'Customer id is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($order_data['business_id'])) {
                                $response['error'] = true;
                                $response['message'] = 'Business id is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($order_data['created_by'])) {
                                $response['error'] = true;
                                $response['message'] = 'Created By is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($order_data['total'])) {
                                $response['error'] = true;
                                $response['message'] = 'Total  is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if ($order_data['delivery_charges'] != 0) {
                                if (empty($order_data['delivery_charges'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Delivery Charges  is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            if ($order_data['discount'] != 0) {
                                if (empty($order_data['discount'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Discount  is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            } else {
                                if ($order_data['total']  < $order_data['discount']) {

                                    $response['error'] = true;
                                    $response['message'] = 'Discount cannot be greater  Total at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }

                            if (empty($order_data['final_total'])) {
                                $response['error'] = true;
                                $response['message'] = 'Final Total  is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            } else {
                                if ($order_data['final_total']  < $order_data['discount']) {

                                    $response['error'] = true;
                                    $response['message'] = 'Discount cannot be greater Final total at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }

                            if (empty($order_data['payment_status'])) {
                                $response['error'] = true;
                                $response['message'] = 'Payment Status is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }

                            if ($order_data['amount_paid'] != 0) {
                                if (empty($order_data['amount_paid'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Amount paid is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }

                            if (empty($order_data['order_type'])) {
                                $response['error'] = true;
                                $response['message'] = 'Order type is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            } else {
                                if (trim($order_data['order_type']) != 'product' && trim($order_data['order_type'])   != 'service') {
                                    $response['error'] = true;
                                    $response['message'] = 'Invalid value in Order type at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }

                            if ($order_data['amount_paid'] != 0) {
                                if (! empty($order_data['amount_paid'])) {
                                    if (empty($order_data['payment_method'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Payment Method is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                }
                            }


                            try {
                                $existingOrder = $order_model->where('order_no', $orderNo)->first();
                                if (!$existingOrder) {

                                    $order_model->insert($order_data);
                                    $orderId = $order_model->getInsertID();
                                } else {
                                    $orderId = $existingOrder['id'];
                                }

                                $lastOrderNo = $orderNo; // Track the last inserted order_no

                                // Check order type
                                if (trim($order_data['order_type'])  === 'service') {
                                    $delivery_boy = isset($row[44]) ? $row[44] : null;
                                    // Insert order service
                                    $order_service_data = [
                                        'order_id' => $orderId,
                                        'service_id' => $row[29],
                                        'service_name' => $row[30],
                                        'price' => $row[31],
                                        'quantity' => $row[32],
                                        'unit_name' => $row[33],
                                        'unit_id' => $row[34],
                                        'sub_total' => $row[35],
                                        'tax_name'  => $row[36],
                                        'tax_percentage' => $row[37],
                                        'is_tax_included' => $row[38],
                                        'is_recursive' => $row[40],
                                        'recurring_days' => $row[41],
                                        'tax_details' => $row[39],
                                        'starts_on' => $row[42],
                                        'ends_on' => $row[43],
                                        'delivery_boy' => $delivery_boy,
                                        'status' => $row[45]
                                        // Add more fields to $order_service_data array as needed
                                    ];

                                    if ($order_service_data['is_tax_included'] == 0) {
                                        if (empty($order_service_data['tax_details'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'tax_details is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        } {
                                            $taxString = $order_service_data['tax_details'];
                                            if (! $this->isValidTaxString($taxString)) {
                                                // The tax string is invalid;

                                                $response['error'] = true;
                                                $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            } else {
                                                $tax_ids = json_decode($order_service_data['tax_details']);
                                                $tax_details = [];
                                                foreach ($tax_ids as $tax_id) {
                                                    $tax = $Tax_model->find($tax_id);
                                                    if (!empty($tax)) {
                                                        $tax_details[] =  [
                                                            'tax_id' => $tax_id,
                                                            'name' => $tax['name'],
                                                            'percentage' => $tax['percentage']
                                                        ];
                                                    } else {
                                                        $response['error'] = true;
                                                        $response['message'] = 'There is no such tax with id ' .  $tax_id . ' at row ' . $rowCount;
                                                        $response['csrf_token'] = csrf_token();
                                                        $response['csrf_hash'] = csrf_hash();
                                                        return $this->response->setJSON($response);
                                                    }
                                                }
                                                $tax_details = empty($tax_details) ? "[]" :  json_encode($tax_details);

                                                $order_service_data['tax_details'] =  $tax_details;
                                            }
                                        }
                                    }



                                    if (empty($order_service_data['service_id'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Service id is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['service_name'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Service Name is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['price'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Price id is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['quantity'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Quantity id is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['unit_name'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Unit name  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['unit_id'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Unit id  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['sub_total'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Sub total is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if ($order_service_data['is_tax_included'] == 0) {
                                        if (empty($order_service_data['tax_name'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Tax name is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                        if (empty($order_service_data['tax_percentage'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'Tax percentage is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        }
                                    }
                                    if (empty($order_service_data['starts_on'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Starts on is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['ends_on'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Ends on is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_service_data['status'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Status is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }

                                    $order_service_model->insert($order_service_data);
                                } elseif (trim($order_data['order_type']) === 'product') {
                                    $delivery_boy = isset($row[28]) ? $row[28] : null;
                                    // Insert order item
                                    $order_item_data = [
                                        'order_id' => $orderId,
                                        'product_id' => $row[17],
                                        'product_variant_id' => $row[18],
                                        'product_name' => $row[19],
                                        'quantity' => $row[20],
                                        'price' => $row[21],
                                        'tax_name' => $row[22],
                                        'tax_percentage' => $row[23],
                                        'is_tax_included' => $row[24],
                                        'tax_details' => $row[25],
                                        'sub_total' => $row[26],
                                        'status' => $row[27],
                                        'delivery_boy' => $delivery_boy
                                        // Add more fields to $order_item_data array as needed
                                    ];



                                    if ($order_item_data['is_tax_included'] == 0) {
                                        if (empty($order_item_data['tax_details'])) {
                                            $response['error'] = true;
                                            $response['message'] = 'tax_details is empty at row ' . $rowCount;
                                            $response['csrf_token'] = csrf_token();
                                            $response['csrf_hash'] = csrf_hash();
                                            return $this->response->setJSON($response);
                                        } {
                                            $taxString = $order_item_data['tax_details'];
                                            if (! $this->isValidTaxString($taxString)) {
                                                // The tax string is invalid;

                                                $response['error'] = true;
                                                $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                                $response['csrf_token'] = csrf_token();
                                                $response['csrf_hash'] = csrf_hash();
                                                return $this->response->setJSON($response);
                                            } else {
                                                $tax_ids = json_decode($order_item_data['tax_details']);
                                                $tax_details = [];
                                                foreach ($tax_ids as $tax_id) {
                                                    $tax = $Tax_model->find($tax_id);
                                                    if (!empty($tax)) {
                                                        $tax_details[] =  [
                                                            'tax_id' => $tax_id,
                                                            'name' => $tax['name'],
                                                            'percentage' => $tax['percentage']
                                                        ];
                                                    } else {
                                                        $response['error'] = true;
                                                        $response['message'] = 'There is no such tax with id ' .  $tax_id . ' at row ' . $rowCount;
                                                        $response['csrf_token'] = csrf_token();
                                                        $response['csrf_hash'] = csrf_hash();
                                                        return $this->response->setJSON($response);
                                                    }
                                                }
                                                $tax_details = empty($tax_details) ? "[]" :  json_encode($tax_details);

                                                $order_item_data['tax_details'] =  $tax_details;
                                            }
                                        }
                                    }

                                    if (empty($order_item_data['product_id'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Product id  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_item_data['product_variant_id'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Product variant id  is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_item_data['product_name'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Product name is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_item_data['quantity'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Quantity is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_item_data['price'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Price is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    if (empty($order_item_data['sub_total'])) {
                                        $response['error'] = true;
                                        $response['message'] = 'Sub total is empty at row ' . $rowCount;
                                        $response['csrf_token'] = csrf_token();
                                        $response['csrf_hash'] = csrf_hash();
                                        return $this->response->setJSON($response);
                                    }
                                    // if ($order_item_data['is_tax_included'] == 0) {
                                    //     if (empty($order_item_data['tax_name'])) {
                                    //         $response['error'] = true;
                                    //         $response['message'] = 'Tax name is empty at row ' . $rowCount;
                                    //         $response['csrf_token'] = csrf_token();
                                    //         $response['csrf_hash'] = csrf_hash();
                                    //         return $this->response->setJSON($response);
                                    //     }
                                    //     if (empty($order_item_data['tax_percentage'])) {
                                    //         $response['error'] = true;
                                    //         $response['message'] = 'Tax percentage is empty at row ' . $rowCount;
                                    //         $response['csrf_token'] = csrf_token();
                                    //         $response['csrf_hash'] = csrf_hash();
                                    //         return $this->response->setJSON($response);
                                    //     }
                                    // }


                                    $order_items_model->insert($order_item_data);
                                } else {
                                    // Handle other order types or invalid types
                                }
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                            $rowCount++;
                        }

                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Orders uploaded successfully!';

                        return $this->response->setJSON($response);
                    } else {
                        // Update operation
                        // Similar logic as upload, but updating existing orders
                    }
                }
            } else {
                // Redirect to order page if no POST data is present
                return redirect()->to('vendor/orders');
            }
        } else {
            // For non-admin users, perform the subscription check
            $status = subscription();

            if ($status == 'active') {
                // Subscription is active, proceed with the order import process
                if (isset($_POST) && !empty($_POST)) {
                    // Your existing order import logic goes here

                } else {
                    // Redirect to order page if no POST data is present
                    return redirect()->to('vendor/orders');
                }
            } elseif ($status == 'upcoming') {
                // Subscription has not started yet
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
            } elseif ($status == 'expired') {
                // Subscription has expired
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
            }

            // Set CSRF token and return JSON response
            $response['csrf_token'] = csrf_token();
            $response['csrf_hash'] = csrf_hash();
            return $this->response->setJSON($response);
        }
    }


    //done
    public function import_business()

    {

        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        }
        if ($this->ionAuth->isAdmin()) {


            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for business data
                ]);

                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle business data
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );

                    $mime = $file->getMimeType();

                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    // Instantiate Business model
                    $business_model = new Businesses_model();

                    if ($type == 'upload') {
                        // Inside the while loop where business data is processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            // Fetch the logged-in user's data
                            $user_data = $this->ionAuth->user()->row();

                            // Assuming Vendors_model has a method to fetch vendor data by user ID
                            $vendors_model = new Vendors_model();
                            $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                            // Extract the vendor_id from the fetched data
                            if ($vendor_data) {
                                $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                            } else {
                                // Handle the case where vendor data is not found for the user
                                // You may throw an error, redirect, or handle it as per your application logic
                            }

                            // Check if 'id' field exists in the CSV file
                            $id_index = array_search('id', $header);
                            if ($id_index !== false) {
                                $business_id = $row[$id_index];

                                // Check if business with this ID exists in the database
                                $existing_business = $business_model->find($business_id);
                                if ($existing_business) {
                                    // Update existing business
                                    $business_data = [
                                        //'vendor_id' => $vendor_id,
                                        // Map CSV columns to database fields
                                        // Example: 'column_name_in_csv' => $row[index_of_column_in_csv]
                                        'user_id' => $row[1],
                                        'name' => $row[2],
                                        'icon' => $row[3],
                                        'description' => $row[4],
                                        'address' => $row[5],
                                        'contact' => $row[6],
                                        'tax_name' => $row[7],
                                        'tax_value' => $row[8],
                                        'bank_details' => $row[9],
                                        'default_business' => $row[10],
                                        'status' => $row[11],
                                        'email' => $row[12],
                                        'website' => $row[13],
                                        'created_at' => $row[14],
                                        'updated_at' => $row[15],

                                        // Add more fields as needed
                                    ];

                                    try {
                                        $business_model->update($business_id, $business_data);
                                        // Additional processing if needed
                                    } catch (\Exception $e) {
                                        // Log or display the error message
                                        $response['error'] = true;
                                        $response['message'] = 'Error: ' . $e->getMessage();
                                        return $this->response->setJSON($response);
                                    }

                                    continue; // Move to the next iteration, skipping insertion
                                }
                            }


                            /*
                                $row[0] => user_id
                                $row[1] => name
                                $row[2] => icon
                                $row[3] => description
                                $row[4] => address
                                $row[5] => contact
                                $row[6] => tax_name
                                $row[7] => tax_value
                                $row[8] => bank_details
                                $row[9] => default_business
                                $row[10] => status
                                $row[11] => email
                                $row[12] => website
                                $row[13] => created_at
                                $row[14] => updated_at
                            */
                            // Insert new business data into the database
                            $business_data = [
                                'vendor_id' => $vendor_id,
                                // Map CSV columns to database fields
                                // Example: 'column_name_in_csv' => $row[index_of_column_in_csv]
                                'user_id' => $row[0],
                                'name' => $row[1],
                                'icon' => $row[2],
                                'description' => $row[3],
                                'address' => $row[4],
                                'contact' => $row[5],
                                'tax_name' => $row[6],
                                'tax_value' => $row[7],
                                'bank_details' => $row[8],
                                'default_business' => $row[9],
                                'status' => $row[10],
                                'email' => $row[11],
                                'website' => $row[12],
                                'created_at' => $row[13],
                                'updated_at' => $row[14],
                                // Add more fields as needed
                            ];
                            try {
                                $business_id = $business_model->insert($business_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }

                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Business data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        // Update operation for business data
                        // Similar logic as upload, but updating existing business data
                    }
                }
            } else {
                return redirect()->to('admin/business');
            }
        }
    }

    // done
    public function import_service()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {

            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for service data
                ]);

                // Validate uploaded file
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle service data
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    // Instantiate Service model
                    $service_model = new Services_model();

                    if ($type == 'upload') {
                        // Inside the while loop where service data is processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            // Fetch the logged-in user's data
                            $user_data = $this->ionAuth->user()->row();

                            // Assuming Vendors_model has a method to fetch vendor data by user ID
                            $vendors_model = new Vendors_model();
                            $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                            // Extract the vendor_id from the fetched data
                            if ($vendor_data) {
                                $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                            } else {
                                // Handle the case where vendor data is not found for the user
                                // You may throw an error, redirect, or handle it as per your application logic
                            }

                            // Check if 'id' field exists in the CSV file
                            $id_index = array_search('id', $header);
                            if ($id_index !== false) {
                                $service_id = $row[$id_index];

                                // Check if service with this ID exists in the database
                                $existing_service = $service_model->find($service_id);
                                if ($existing_service) {
                                    // Update existing service
                                    $service_data = [
                                        // Map CSV columns to database fields
                                        // Example: 'column_name_in_csv' => $row[index_of_column_in_csv]
                                        'vendor_id' => $vendor_id,
                                        'business_id' => $row[2],
                                        'tax_id' => $row[3],
                                        'unit_id' => $row[4],
                                        'name' => $row[5],
                                        'description' => $row[6],
                                        'image' => $row[7],
                                        'price' => $row[8],
                                        'cost_price' => $row[9],
                                        'is_tax_included' => $row[10],
                                        'is_recursive' => $row[11],
                                        'recurring_days' => $row[12],
                                        'recurring_price' => $row[13],
                                        'status' => $row[14],
                                        'created_at' => $row[15],
                                        'updated_at' => $row[16],

                                        // Add more fields as needed
                                    ];

                                    try {
                                        $service_model->update($service_id, $service_data);
                                        // Additional processing if needed
                                    } catch (\Exception $e) {
                                        // Log or display the error message
                                        $response['error'] = true;
                                        $response['message'] = 'Error: ' . $e->getMessage();
                                        return $this->response->setJSON($response);
                                    }

                                    continue; // Move to the next iteration, skipping insertion
                                }
                            }

                            // Insert new service data into the database
                            $service_data = [
                                'vendor_id' => $vendor_id,
                                // Map CSV columns to database fields
                                // Example: 'column_name_in_csv' => $row[index_of_column_in_csv]
                                'business_id' => $row[2],
                                'tax_id' => $row[3],
                                'unit_id' => $row[4],
                                'name' => $row[5],
                                'description' => $row[6],
                                'image' => $row[7],
                                'price' => $row[8],
                                'cost_price' => $row[9],
                                'is_tax_included' => $row[10],
                                'is_recursive' => $row[11],
                                'recurring_days' => $row[12],
                                'recurring_price' => $row[13],
                                'status' => $row[14],
                                'created_at' => $row[15],
                                'updated_at' => $row[16],
                                // Add more fields as needed
                            ];

                            try {
                                $service_id = $service_model->insert($service_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }

                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Service data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        // Update operation for service data
                        // Similar logic as upload, but updating existing service data
                    }
                }
            } else {
                return redirect()->to('admin/services');
            }
        }
    }

    // done
    public function import_delivery_boys()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        // Check if the logged-in user is an admin
        if ($this->ionAuth->isAdmin()) {

            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for delivery boy data
                ]);

                // Validate uploaded file
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle delivery boy data
                    $file =  $this->request->getFile('file');
                    // $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }



                    $response['message'] = '';
                    $type = $_POST['type'];
                    $csv = $file->getTempName();
                    $handle = fopen($csv, "r");

                    $header = fgetcsv($handle, 10000, ",");

                    $delivery_boy_model = new Delivery_boys_model();

                    if ($type == 'upload') {
                        // Inside the while loop where delivery boy data is processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            // Fetch the logged-in user's data
                            $user_data = $this->ionAuth->user()->row();
                            // Assuming Vendors_model has a method to fetch vendor data by user ID
                            $vendors_model = new Vendors_model();
                            $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                            // Extract the vendor_id from the fetched data
                            if ($vendor_data) {
                                $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                            } else {
                                // Handle the case where vendor data is not found for the user
                                // You may throw an error, redirect, or handle it as per your application logic
                            }

                            // Check if 'id' field exists in the CSV file

                            // Check if 'id' field exists in the CSV file
                            $has_id = true;
                            if (in_array("id", $header)) {
                                $has_id =  true;
                            } else {
                                $has_id =  false;
                            }

                            try {

                                /*
                                    (
                                        $row[0] => email
                                        $row[1] => first_name
                                        $row[2] => phone
                                        $row[3] => password
                                        $row[4] => vendor_id
                                        $row[5] => business_id
                                        $row[6] => permissions
                                        $row[7] => status
                                        $row[8] => created_at
                                        $row[9] => updated_at 
                                    )
                                */

                                $tables                        = $this->configIonAuth->tables;
                                $identityColumn                = $this->configIonAuth->identity;

                                $email    =  strtolower(trim($row[0]));
                                $identity = ($identityColumn === 'email') ? $email : trim($row[2]);
                                $password = trim($row[3]);
                                $group_id_arry = fetch_details("groups", ['name' => 'delivery_boys'], "id");
                                $group_id = [$group_id_arry[0]['id']];
                                $additionalData = [
                                    'first_name' =>  trim($row[1]),
                                    'phone'      => trim($row[2]),
                                ];

                                $id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);

                                // Insert new delivery boy data into the database
                                $delivery_boy_data = [
                                    'vendor_id' => $vendor_id,
                                    // Map CSV columns to database fields
                                    'user_id' => $id,
                                    'business_id' => $row[5],
                                    'permissions' => $row[6],
                                    'status' =>  $row[7],
                                    'created_at' =>  $row[8],
                                    'updated_at' => $row[9]
                                    // Add more fields as needed
                                ];


                                $delivery_boy_id = $delivery_boy_model->insert($delivery_boy_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }
                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Delivery boys data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        // Update operation for delivery boy data
                        // Similar logic as upload, but updating existing delivery boy data
                    }
                }
            } else {
                return redirect()->to('admin/delivery_boys');
            }
        }
    }

    //done
    public function import_expenses_types()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        }

        // Check if the logged-in user is an admin
        if ($this->ionAuth->isAdmin()) {

            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for expenses type data
                ]);

                // Validate uploaded file
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle expenses type data
                    $file =  $this->request->getFile('file');
                    // $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    // Instantiate ExpensesType model
                    $expenses_type_model = new Expenses_Type_model();

                    if ($type == 'upload') {
                        // Inside the while loop where expenses type data is processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            // Fetch the logged-in user's data
                            $user_data = $this->ionAuth->user()->row();

                            // Assuming Vendors_model has a method to fetch vendor data by user ID
                            $vendors_model = new Vendors_model();
                            $vendor_data = $vendors_model->getVendorByUserId($user_data->id);

                            // Extract the vendor_id from the fetched data
                            if ($vendor_data) {
                                $vendor_id = $vendor_data->id; // Assuming vendor_id is a field in your vendor table
                            } else {
                                // Handle the case where vendor data is not found for the user
                                // You may throw an error, redirect, or handle it as per your application logic
                            }

                            // Remove BOM from the first element of the header if it exists
                            if (isset($header[0])) {
                                $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);
                            }


                            // Check if 'id' field exists in the CSV file
                            $id_index = null;
                            if (in_array("id", $header)) {
                                $id_index =  true;
                            } else {
                                $id_index =  false;
                            }


                            if ($id_index !== false) {
                                $expenses_type_id = $row[0];


                                // Check if expenses type with this ID exists in the database
                                $existing_expenses_type = $expenses_type_model->find($expenses_type_id);
                                if ($existing_expenses_type) {
                                    // Update existing expenses type
                                    $expenses_type_data = [
                                        // Map CSV columns to database fields
                                        // Example: 'column_name_in_csv' => $row[index_of_column_in_csv]
                                        'vendor_id' => $vendor_id,
                                        'title' => $row[2],
                                        'description' => $row[3],
                                        'expenses_type_date' => $row[4],
                                        'created_at' => $row[5],
                                        'updated_at' => $row[6]
                                        // Add more fields as needed
                                    ];

                                    try {
                                        $expenses_type_model->update($expenses_type_id, $expenses_type_data);
                                        // Additional processing if needed
                                    } catch (\Exception $e) {
                                        // Log or display the error message
                                        $response['error'] = true;
                                        $response['message'] = 'Error: ' . $e->getMessage();
                                        return $this->response->setJSON($response);
                                    }

                                    continue; // Move to the next iteration, skipping insertion
                                }
                            }

                            // Insert new expenses type data into the database
                            $expenses_type_data = [
                                'vendor_id' => $vendor_id,
                                // Map CSV columns to database fields
                                'title' => $row[2],
                                'description' => $row[3],
                                'expenses_type_date' => $row[4],
                                'created_at' => $row[5],
                                'updated_at' => $row[6]
                                // Add more fields as needed

                            ];

                            try {
                                $expenses_type_id = $expenses_type_model->insert($expenses_type_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }

                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Expenses types data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        // Update operation for expenses type data
                        // Similar logic as upload, but updating existing expenses type data
                    }
                }
            } else {
                return redirect()->to('admin/expenses_types');
            }
        }
    }

    //done
    public function import_purchases()
    {
        // Check if the user is logged in
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (isset($_POST) && !empty($_POST)) {
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for purchase data
                ]);

                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                        'type' => 'required',
                    ]);
                }

                if (!$this->validation->withRequest($this->request)->run()) {
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle purchase data
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array('text/x-comma-separated-values', 'text/comma-separated-values', 'application/x-csv', 'text/x-csv', 'text/csv', 'application/csv');
                    $mime = $file->getMimeType();

                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    // Instantiate Purchases model
                    $purchases_model = new Purchases_model();
                    $purchase_items_model = new Purchases_items_model();
                    if ($type === 'upload') {

                        // Inside the while loop where purchase data is processed
                        $rowCount = 1;

                        $lastOrderNo = null;
                        $purchaseId = null;
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            $orderNo = $row[3];

                            /*
                            expected data
                            Array
                            ( 
                                $row[0] => business_id
                                $row[1] => vendor_id
                                $row[2] => supplier_id
                                $row[3] => order_no
                                $row[4] => purchase_date
                                $row[5] => tax_id
                                $row[6] => status
                                $row[7] => order_type
                                $row[8] => message
                                $row[9] => product_variant_id
                                $row[10] => purchases_item_quantity
                                $row[11] => purchases_item_price
                                $row[12] => discount_on_item
                                $row[13] => status
                                $row[14] => delivery_charges
                                $row[15] => discount_on_deal
                                $row[16] => amount_paid
                                $row[17] => total_of_deal
                                $row[18] => payment_method
                                $row[19] => payment_status
                                $row[20] => created_at
                                $row[21] => updated_at
                            )

                            */
                            // Update existing purchase
                            $purchase_data = [
                                'business_id' =>  $row[0],
                                'vendor_id' =>  $row[1],
                                'supplier_id' => $row[2],
                                'order_no' =>  $row[3],
                                'purchase_date' => $row[4],
                                'tax_ids' => $row[5],
                                'status' => $row[6],
                                'delivery_charges' => $row[14],
                                'order_type' => $row[7],
                                'total' => $row[17],
                                'payment_method' => $row[18],
                                'payment_status' => $row[19],
                                'amount_paid' => $row[16],
                                'message' => $row[8],
                                'discount' => $row[15],
                                'created_at' => $row[20],
                                'updated_at' => $row[21],
                            ];



                            if (empty($purchase_data['tax_ids'])) {
                                $response['error'] = true;
                                $response['message'] = 'tax_ids is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            } {
                                $taxString = $purchase_data['tax_ids'];
                                if (! $this->isValidTaxString($taxString)) {
                                    // The tax string is invalid;

                                    $response['error'] = true;
                                    $response['message'] = 'tax_ids is not in correct format at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }

                            if (empty($purchase_data['business_id'])) {
                                $response['error'] = true;
                                $response['message'] = 'Business id is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($purchase_data['vendor_id'])) {
                                $response['error'] = true;
                                $response['message'] = 'Vendor id is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }

                            if (empty($purchase_data['supplier_id'])) {
                                $response['error'] = true;
                                $response['message'] = 'Supplier id is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($purchase_data['order_no'])) {
                                $response['error'] = true;
                                $response['message'] = 'Order no is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($purchase_data['purchase_date'])) {
                                $response['error'] = true;
                                $response['message'] = 'Purchase date is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }

                            if (empty($purchase_data['status'])) {
                                $response['error'] = true;
                                $response['message'] = 'Status is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (! $purchase_data['delivery_charges'] == 0) { // if the value is zero it will ignore the validation.
                                if (empty($purchase_data['delivery_charges'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Delivery charges is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            if (empty($purchase_data['order_type'])) {
                                $response['error'] = true;
                                $response['message'] = 'Order type is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($purchase_data['total'])) {
                                $response['error'] = true;
                                $response['message'] = 'Total is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            /*
                                 if (empty($purchase_data['payment_method'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Payment method is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            */
                            if (empty($purchase_data['payment_status'])) {
                                $response['error'] = true;
                                $response['message'] = 'Payment status is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (! $purchase_data['amount_paid'] == 0) { // if the value is zero it will ignore the validation.
                                if (empty($purchase_data['amount_paid'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Amount paid is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            if (empty($purchase_data['message'])) {
                                $response['error'] = true;
                                $response['message'] = 'Message is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (! $purchase_data['discount'] == 0) { // if the value is zero it will ignore the validation.
                                if (empty($purchase_data['discount'])) {
                                    $response['error'] = true;
                                    $response['message'] = 'Discount is empty at row ' . $rowCount;
                                    $response['csrf_token'] = csrf_token();
                                    $response['csrf_hash'] = csrf_hash();
                                    return $this->response->setJSON($response);
                                }
                            }
                            if (empty($purchase_data['created_at'])) {
                                $response['error'] = true;
                                $response['message'] = 'Created at is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }
                            if (empty($purchase_data['updated_at'])) {
                                $response['error'] = true;
                                $response['message'] = 'Updated at is empty at row ' . $rowCount;
                                $response['csrf_token'] = csrf_token();
                                $response['csrf_hash'] = csrf_hash();
                                return $this->response->setJSON($response);
                            }

                            try {
                                $existingPurchase = $purchases_model->where('order_no', $orderNo)->first();

                                if (!$existingPurchase) {
                                    $purchases_model->insert($purchase_data);
                                    $purchaseId = $purchases_model->getInsertID();
                                } else {
                                    $purchaseId = $existingPurchase['id'];
                                }

                                $lastOrderNo = $orderNo; // Track the last inserted order_no

                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }

                            // Handle purchase items
                            // Assuming 'purchase_items' is the name of the table for purchase items
                            // Assuming each row in CSV represents a purchase item

                            $purchase_item_data = [
                                'purchase_id' => $purchaseId,
                                'product_variant_id' => $row[9],
                                'quantity' => $row[10],
                                'price' => $row[11],
                                'discount' => $row[12],
                                'status' => $row[13],
                                'created_at' => $row[20],
                                'updated_at' => $row[21],
                                // Add more fields as needed
                            ];

                            try {
                                $purchase_items_model->insert($purchase_item_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }

                            $rowCount++;
                        }


                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Purchases uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid type, Please select valid type';
                        return $this->response->setJSON($response);
                    }
                }
            } else {
                return redirect()->to('admin/purchases');
            }
        }
    }


    //done
    public function import_suppliers()
    {
        // Check if the user is logged in and is an admin
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            // Check if form data is submitted
            if (isset($_POST) && !empty($_POST)) {
                // Set validation rules for form fields
                $this->validation->setRules([
                    'vendor_id' => 'required',
                    // Add more validation rules as needed for supplier data
                ]);

                // Check if file is uploaded
                if (empty($_FILES['file']['name'])) {
                    $this->validation->setRules([
                        'file' => 'required',
                    ]);
                }

                // Run validation
                if (!$this->validation->withRequest($this->request)->run()) {
                    // If validation fails, return error response
                    $errors  = $this->validation->getErrors();
                    $response['error'] = true;
                    foreach ($errors as $e) {
                        $response['message'][] = $e;
                    }
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['data'] = [];
                    return $this->response->setJSON($response);
                } else {
                    // Process uploaded file and handle supplier data
                    $file =  $this->request->getFile('file');
                    $allowed_mime_type_arr = array(
                        'text/x-comma-separated-values',
                        'text/comma-separated-values',
                        'application/x-csv',
                        'text/x-csv',
                        'text/csv',
                        'application/csv',
                        'application/vnd.ms-excel', // For older Excel files (.xls)
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', // For newer Excel files (.xlsx)
                        'text/plain' // Allow .csv files that are identified as text/plain
                    );
                    $mime = $file->getMimeType();

                    // Validate MIME type
                    if (!in_array($mime, $allowed_mime_type_arr)) {
                        $response['error'] = true;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Invalid file format!';
                        return $this->response->setJSON($response);
                    }

                    $csv = $_FILES['file']['tmp_name'];
                    $handle = fopen($csv, "r");
                    $response['message'] = '';
                    $type = $_POST['type'];
                    $header = fgetcsv($handle, 10000, ",");

                    // Get the vendor ID
                    $vendor_id = $this->request->getPost('vendor_id');

                    // Instantiate Suppliers model
                    $suppliers_model = new Suppliers_model();
                    $ionAuthModel = new \IonAuth\Libraries\IonAuth();

                    if ($type == 'upload') {
                        // Inside the while loop where supplier data is processed
                        while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                            // Check if 'id' field exists in the CSV file
                            $id_index = array_search('id', $header);
                            if ($id_index !== false) {
                                $supplier_id = $row[$id_index];

                                // Check if supplier with this ID exists in the database
                                $existing_supplier = $suppliers_model->find($supplier_id);
                                if ($existing_supplier) {
                                    // Update existing supplier
                                    $supplier_data = [
                                        'vendor_id' => $vendor_id,
                                        // Map CSV columns to database fields
                                        'user_id' => $row[1],
                                        'balance' => $row[3],
                                        'billing_address' => $row[4],
                                        'shipping_address' => $row[5],
                                        'credit_period' => $row[6],
                                        'credit_limit' => $row[7],
                                        'tax_name' => $row[8],
                                        'tax_num' => $row[9],
                                        'status' => $row[10],
                                        'created_at' => $row[11],
                                        'updated_at' => $row[12],
                                        // Add more fields as needed
                                    ];

                                    try {
                                        $suppliers_model->update($supplier_id, $supplier_data);
                                        // Additional processing if needed
                                    } catch (\Exception $e) {
                                        // Log or display the error message
                                        $response['error'] = true;
                                        $response['message'] = 'Error: ' . $e->getMessage();
                                        return $this->response->setJSON($response);
                                    }

                                    continue; // Move to the next iteration, skipping insertion
                                }
                            }

                            /*
                                $row[0] => name
                                $row[1] => mobile
                                $row[2] => email
                                $row[3] => Opening Balance
                                $row[4] => vendor_id
                                $row[5] => balance
                                $row[6] => billing_address
                                $row[7] => shipping_address
                                $row[8] => credit_period
                                $row[9] => credit_limit
                                $row[10] => tax_name
                                $row[11] => tax_num
                                $row[12] => status
                                $row[13] => created_at
                                $row[14] => updated_at
                            */

                            $tables                        = $this->configIonAuth->tables;
                            $identityColumn                = $this->configIonAuth->identity;

                            $email    =  strtolower(trim($row[2]));
                            $identity = ($identityColumn === 'email') ? $email : trim($row[1]);
                            $group_id_arry = fetch_details("groups", ['name' => 'suppliers'], "id");
                            $group_id = [$group_id_arry[0]['id']];
                            $additionalData = [
                                'first_name' => trim($row[0]),
                                'phone'      => trim($row[1]),
                            ];

                            try {
                                $id = $this->ionAuth->register($identity, '12345678', $email, $additionalData, $group_id);
                                if (!$id) {
                                    $errors = $this->ionAuth->errors();
                                    $response['error'] = true;
                                    $response['message'] = 'Registration failed: ' . ($errors);
                                    return $this->response->setJSON($response);
                                }
                            } catch (\Exception $e) {
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }

                            // Insert new supplier data into the database
                            $supplier_data = [
                                'vendor_id' => $vendor_id,
                                // Map CSV columns to database fields
                                'user_id' => $id,
                                'balance' => $row[5],
                                'billing_address' => $row[6],
                                'shipping_address' => $row[7],
                                'credit_period' => $row[8],
                                'credit_limit' => $row[9],
                                'tax_name' => $row[10],
                                'tax_num' => $row[11],
                                'status' => $row[12],
                                'created_at' => $row[13],
                                'updated_at' => $row[14],

                                // Add more fields as needed
                            ];

                            try {
                                $suppliers_model->insert($supplier_data);
                                // Additional processing if needed
                            } catch (\Exception $e) {
                                // Log or display the error message
                                $response['error'] = true;
                                $response['message'] = 'Error: ' . $e->getMessage();
                                return $this->response->setJSON($response);
                            }
                        }

                        fclose($handle);
                        $response['error'] = false;
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $response['message'] = 'Suppliers data uploaded successfully!';
                        return $this->response->setJSON($response);
                    } else {
                        // Update operation for suppliers
                        // Similar logic as upload, but updating existing suppliers
                    }
                }
            } else {
                return redirect()->to('admin/suppliers');
            }
        }
    }
    
}
