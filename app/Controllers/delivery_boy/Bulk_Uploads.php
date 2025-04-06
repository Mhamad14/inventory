<?php

namespace App\Controllers\delivery_boy;

use App\Controllers\BaseController;
use App\Models\Categories_model;
use App\Models\Orders_items_model;
use App\Models\Products_model;
use App\Models\Orders_model;
use App\Models\Orders_services_model;
use App\Models\Products_variants_model;
use App\Models\Vendors_model;

class Bulk_Uploads extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'filesystem']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
  
    public function import_orders()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isDeliveryBoy()) {
            return redirect()->to('login');
        } else {
           
            $status = subscription();
    
            if ($status == 'active') {
                
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
    
                        if ($type == 'upload') {
                            // Inside the while loop where orders are processed
                            while (($row = fgetcsv($handle, 10000, ",")) != FALSE) {
                                
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
    
                                // Insert orders into database
                                $order_data = [
                                    'vendor_id' => $vendor_id,
                                    'customer_id' => $row[2],
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
                                    'created_at' => $row[14],
                                    'updated_at' => $row[15]
                                ];
    
                                try {
                                    $order_id = $order_model->insert($order_data);
    
                                    // Check order type
                                    if ($row[11] === 'service') {
                                    
                                        // Insert order service
                                        $order_service_data = [
                                            'order_id' => $order_id,
                                            'service_id' => $row[28],
                                            'service_name' => $row[29],
                                            'price' => $row[30],
                                            'quantity' => $row[31],
                                            'unit_name' => $row[32],
                                            'unit_id' => $row[33],
                                            'sub_total' => $row[34],
                                            'tax_name'  => $row[35],
                                            'tax_percentage' => $row[36],
                                            'is_tax_included' => $row[37],
                                            'is_recursive' => $row[38],
                                            'recurring_days' => $row[39],
                                            'starts_on' => $row[40],
                                            'ends_on' => $row[41],
                                            'delivery_boy' => $row[42],
                                            'status' => $row[43]
                                            // Add more fields to $order_service_data array as needed
                                        ];
    
                                    
                                        $order_service_model->insert($order_service_data);
                                    } elseif ($row[11] === 'product') {
                                        $delivery_boy = isset($row[27]) ? $row[27] : null;
    
                                        // Insert order item
                                        $order_item_data = [
                                            'order_id' => $order_id,
                                            'product_id' => $row[17],
                                            'product_variant_id' => $row[18],
                                            'product_name' => $row[19],
                                            'quantity' => $row[20],
                                            'price' => $row[21],
                                            'tax_name' => $row[22],
                                            'tax_percentage' => $row[23],
                                            'is_tax_included' => $row[24],
                                            'sub_total' => $row[25],
                                            'status' => $row[26],
                                            'delivery_boy' => $delivery_boy
                                            // Add more fields to $order_item_data array as needed
                                        ];
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
                    return redirect()->to('vendor/orders');
                }
            }
    
            if ($status == 'upcoming') {
                $response = [
                    'error' => true,
                    'message' => ['Your subscription has not started yet!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
    
            if ($status == 'expired') {
                $response = [
                    'error' => true,
                    'message' => ['Please Buy Subscription to proceed ahead!'],
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
            }
        }
    }
    
}
