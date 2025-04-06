<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Businesses_model;
use App\Models\Services_model;
use App\Models\Tax_model;
use App\Models\Units_model;


class Services extends BaseController
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
            $data['page'] = VIEWS . "services_table";
            $data['title'] = "Services - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $this->ionAuth->getUserId();
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function Add_service()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("admin/businesses");
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
                $data['page'] = FORMS . "services";
                $data['title'] = "Add Services - " . $company_title;
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
                $user_id = $_SESSION['user_id'];
                $vendor_id  = 0;
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($user_id);
                } else {
                    $vendor_id = $user_id;
                }
                $data['user'] = $this->ionAuth->user($user_id)->row();
                $units_model = new Units_model();
                $data['units'] =  $units_model->get_units_for_forms($vendor_id);
                $tax_model = new Tax_model();
                $data['taxes'] = $tax_model->findAll();
                return view("admin/template", $data);
            }
        }
    }
    public function save_services()
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {


            if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                return redirect()->to("admin/businesses");
            } else {
                if (isset($_POST) && !empty($_POST)) {

                    $this->validation->setRules([
                        'name' => 'required',
                        'description' => 'required',
                        'price' => 'required',
                        'cost_price' => 'required',
                    ]);
                    $path = './public/uploads/services/';
                    $old_image = $this->request->getVar('old_image');
                    if (!empty($_FILES['image']) && isset($_FILES['image'])) {
                        $file =  $this->request->getFile('image');
                        if ($file->isValid()) {
                            if ($file->move($path)) {
                                if (file_exists($old_image) && !empty($old_image)) {
                                    unlink($old_image);
                                }
                                $image = 'public/uploads/services/' . $file->getName();
                            }
                        } else {
                            $image = $old_image;
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
                        $service_model = new Services_model();
                        $user_id = $_SESSION['user_id'];
                        $vendor_id  = 0;
                        if ($this->ionAuth->isTeamMember()) {
                            $vendor_id = get_vendor_for_teamMember($user_id);
                        } else {
                            $vendor_id = $user_id;
                        }
                        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
                        $is_tax_included = isset($_POST['is_tax_inlcuded']) ? "1" : "0";

                        $tax_ids = '[]';

                        if ($is_tax_included === "0") {
                            $tax_ids_input = $this->request->getVar('service_taxes');
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


                        if (isset($_POST['is_recursive'])) {
                            $is_recursive = "1";
                        } else {
                            $is_recursive = "0";
                        }
                        if (isset($_POST['status'])) {
                            $status = "1";
                        } else {
                            $status = "0";
                        }
                        $edit_service_id = isset($_POST['service_id']) ? $_POST['service_id'] : "";
                        $service = array(
                            'id' => $edit_service_id,
                            'vendor_id' => $vendor_id,
                            'business_id' => $business_id,
                            'name' => $this->request->getVar('name'),
                            'description' => $this->request->getVar('description'),
                            'price' => $this->request->getVar('price'),
                            'cost_price' => $this->request->getVar('cost_price'),
                            'tax_ids' =>  $tax_ids,
                            'unit_id' => $this->request->getVar('unit_id'),
                            'is_tax_included' => $is_tax_included,
                            'is_recursive' => $is_recursive,
                            'recurring_days' => $this->request->getVar('recurring_days'),
                            'recurring_price' => $this->request->getVar('recurring_price'),
                            'image' => $image,
                            'status' => $status,
                        );

                        $service_model->save($service);
                        $response = [
                            'error' => false,
                            'message' => 'Service added successfully',
                            'data' => []
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Service added successfully';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');
                        return $this->response->setJSON($response);
                    }
                } else {
                    return redirect()->back();
                }
            }
        }
    }
    public function service_table()
    {
        $service_model = new Services_model();
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $business_name = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "";
        $services = $service_model->get_services($business_id);
        $total = $service_model->count_of_services($business_id);
        $units_model = new Units_model();
        $tax_model = new Tax_model();
        $i = 0;
        foreach ($services as $service) {
            $unit_id = $service['unit_id'];
            $product_unit = $units_model->find($unit_id);
            $product_unit_name = isset($product_unit['name']) ? ($product_unit['name']) : "";
            $tax_name = "";
            if (isset($service['tax_id']) && !empty($service['tax_id'])) {
                $tax = $tax_model->find($service['tax_id']);
                $tax_name = $tax['name'];
            }
            if ($service['status'] == 1) {
                $status = "<span class='badge badge-primary'>Active</span>";
            } else {
                $status = "<span class='badge ' style = 'background-color:#ed1307'>Deactive</span>";
            }
            if ($service['is_tax_included'] == 1) {
                $service['is_tax_included'] = "Included";
            } else {
                $service['is_tax_included'] = "Excluded";
            }
            if ($service['is_recursive'] == 1) {
                $service['is_recursive'] = "Yes";
            } else {
                $service['is_recursive'] = "No";
            }
            $service_id = $service['id'];
            $edit_product = "<a href=" . base_url('admin/services/edit_service/' . $service_id) . " class='btn btn-primary btn-sm' data-toggle='tooltip' data-placement='bottom' title='Edit'><i class='bi bi-pencil'></i></a>" . " ";
            $rows[$i] = [
                'id' => $service['id'],
                'vendor_id' => $service['vendor_id'],
                'service_name' =>  ucwords($service['service_name']),
                'description' => $service['description'],
                'price' => currency_location(decimal_points($service['price'])),
                'cost_price' => currency_location(decimal_points($service['cost_price'])),
                'recurring_days' => $service['recurring_days'],
                'recurring_price' => currency_location(decimal_points($service['recurring_price'])),
                'is_recursive' => $service['is_recursive'],
                'is_tax_included' => $service['is_tax_included'],
                'status' => $status,
                'business_name' => $business_name,
                'vendor_id' => $service['vendor_id'],
                'unit_id' => $product_unit_name,
                'tax_id' => $tax_name,
                'action' => $edit_product
            ];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = [];
        if (isset($rows) && !empty($rows)) {
            $array['rows'] = $rows;
        }
        echo json_encode($array);
    }
    public function edit_service($service_id = "")
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
            $service_model = new Services_model();
            $services = $service_model->find($service_id);
            $data['services'] = $services;
            $settings = get_settings('general', true);
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = FORMS . "services";
            $data['title'] = "Edit Services - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            $units_model = new Units_model();
            $data['units'] =  $units_model->get_units_for_forms($id);
            if (isset($services['unit_id']) && !empty($services['unit_id'])) {
                $service_unit_row = $units_model->find($services['unit_id']);
                $data['product_unit_name'] = $service_unit_row['name'];
            }
            $tax_model = new Tax_model();
            $service_unit_row = [];
            $services_tax_ids = json_decode($services['tax_ids']);
            $services_tax_value  = [];

            if (gettype($services_tax_ids) != "array") {
                if ($services_tax_ids != 0) {
                    $tax = $tax_model->find($services_tax_ids);
                    $services_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            } else {
                foreach ($services_tax_ids as $tax_id) {
                    $tax = $tax_model->find($tax_id);
                    $services_tax_value[] = [
                        'value' => $tax['name'],
                        'id' => $tax['id'],
                    ];
                }
            }
            $data['services_tax_value'] = json_encode($services_tax_value);

            $tax_model = new Tax_model();
            if (isset($services['tax_id']) && !empty($services['tax_id'])) {
                $tax_name = $tax_model->find($services['tax_id']);
                $data['tax_name'] = $tax_name['name'];
                $data['percentage'] = $tax_name['percentage'];
                $data['taxes'] = $tax_model->findAll();
            }
            return view("admin/template", $data);
        }
    }
    public function json()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $data = $_GET;

        $data['business_id'] = $business_id;
        $rules = [
            'business_id' => 'required|trim|numeric',
            'search' => 'trim',
        ];
        if ($this->request->getGet('limit')) {
            $rules['limit'] = 'trim|numeric|greater_than_equal_to[1]|less_than[250]';
        }
        if ($this->request->getGet('offset')) {
            $rules['offset'] = 'trim|numeric|greater_than_equal_to[0]';
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
            $limit = (!empty($data['limit'])) ? $data['limit'] : 10;
            $offset = (!empty($data['offset'])) ? $data['offset'] : 0;
            $sort = (!empty($data['sort'])) ? $data['sort'] : 'id';
            $order = (!empty($data['order'])) ? $data['order'] : 'DESC';
            $search = (!empty($data['search'])) ? $data['search'] : '';
            $services = fetch_services($business_id, $search, $limit, $offset, $sort, $order);
            $final_product_list = array();
            $temp_arr = $services['services'];
            if (isset($temp_arr) && !empty($temp_arr)) {
                foreach ($temp_arr as $val) {
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
                        // if tax_ids is array then get get percentage;
                        foreach ($tax_ids as $tax) {
                            $taxes = fetch_details("tax", ['id' => $tax]);
                            $per = isset($taxes[0]['percentage']) ? $taxes[0]['percentage'] : "1";
                            $percentages[] = $per;
                        }
                    }

                    $is_tax_inlcuded = $val['is_tax_included'];
                    if ($is_tax_inlcuded != "1") {

                        $sale_price = $val['price'];
                        $taxable_amount_price = 0;
                        if (! empty($percentages)) {

                            foreach ($percentages as $prec) {

                                $taxable_amount_price += floatval($sale_price) * (floatval($prec) / 100);
                            }
                        } else {
                            $taxable_amount_price = floatval($sale_price) * (floatval($percentage) / 100);
                        }

                        $price = floatval($sale_price) + $taxable_amount_price;
                        $val['price'] = $price;
                    } else {
                        $val;
                    }
                    $final_product_list[] = $val;
                }
            }

            $response['error'] = (!empty($services['services'])) ? false : true;
            $response['message'] = (!empty($services['services'])) ? "Services fetched successfully" : "No service found!";
            $response['total'] = $services['total'];
            $response['data'] = $final_product_list;
            return $this->response->setJSON($response);
        }
    }
}
