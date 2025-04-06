<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BrandModel;
use App\Models\Businesses_model;
use CodeIgniter\HTTP\ResponseInterface;

class Brand extends BaseController
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
            $data['page'] = FORMS . 'brand';
            $data['title'] = "Warehouse - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();

            return view("admin/template", $data);
        }
    }

    public function add()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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

            if ($this->ionAuth->isTeamMember()) {

                if (! userHasPermission('brand', 'can_create', session('user_id'))) {
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

            $this->validation->setRules([
                'name' => [
                    'rules' => 'required',
                    'label' => 'Name'
                ],
                'description' => [
                    'rules' => 'required',
                    'label' => 'Description'
                ]
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $name = $this->request->getPost('name');
                $description = $this->request->getPost('description');
                $db = \Config\Database::connect();

                $builder = $db->table('brands');

                $vendor_id = 0;
                if ($this->ionAuth->isTeamMember()) {
                    $vendor_id = get_vendor_for_teamMember($this->ionAuth->getUserId());
                } else {
                    $vendor_id = session('user_id');
                }

                $data = [
                    'name'        => $name,
                    'description' => $description,
                    'business_id' => session('business_id '),
                    'vendor_id' => $vendor_id,
                    'created_at'  => date('Y-m-d H:i:s'), // Current timestamp
                    'updated_at'  => date('Y-m-d H:i:s'), // Current timestamp
                ];

                $builder->insert($data);

                // Get the inserted ID if needed
                $insertID = $db->insertID();

                if ($insertID) {
                    $response = [
                        'error' => false,
                        'message' => "Brand added successfully",
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response = [
                        'error' => true,
                        'message' => "Faild to add brand",
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

    public function get_brand()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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

            if ($this->ionAuth->isTeamMember()) {
                if (! userHasPermission('brand', 'can_update', session('user_id'))) {
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

            $this->validation->setRules([
                'id' => [
                    'rules' => 'required',
                    'label' => 'Brand'
                ],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->request->getPost('id');
                $business_id  = session('business_id');
                $brand_model = new BrandModel();
                $brand  =  $brand_model->where(['business_id' => $business_id, 'id' => $id])->get()->getResultArray();
                if (empty($brand)) {
                    $response = [
                        'error' => true,
                        'message' => ["Brand not found !"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
                $brand = $brand[0];
                $response = [
                    'error' => false,
                    'message' => "Success !",
                    'data' => [$brand]
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
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

    public function update()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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

            if ($this->ionAuth->isTeamMember()) {
                if (! userHasPermission('brand', 'can_update', session('user_id'))) {
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

            $this->validation->setRules([
                'brand_id' => [
                    'rules' => 'required',
                    'label' => 'Brand'
                ],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->request->getPost('brand_id');
                $business_id  = session('business_id');
                $brand_model = new BrandModel();
                $brand  =  $brand_model->where(['business_id' => $business_id, 'id' => $id])->get()->getResultArray();
                if (empty($brand)) {
                    $response = [
                        'error' => true,
                        'message' => ["Brand not found !"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }

                $name = $this->request->getPost('name');
                $description = $this->request->getPost('description');
                $data = [
                    'name'        => $name,
                    'description' => $description,
                    'updated_at'  => date('Y-m-d H:i:s'),
                ];

                $updated = $brand_model->update($id, $data);

                if ($updated) {
                    $response = [
                        'error' => false,
                        'message' => ["Brand updated successfully"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response = [
                        'error' => true,
                        'message' => ["Unabled to Update Barnd"],
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
    public function table()
    {
        $limit = (int) $this->request->getGet('limit') ?? 10; // default limit
        $offset = (int) $this->request->getGet('offset') ?? 0; // default offset
        $search = $this->request->getGet('search') ?? ''; // search term

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $brand_model = new BrandModel();

        // Apply filters based on business_id and search term
        $brand_model->where('business_id', $business_id);

        if (!empty($search)) {
            $brand_model->like('name', $search)
                ->orLike('description', $search);
        }

        // Retrieve total count for pagination
        $total = $brand_model->countAllResults(false);

        // Apply limit and offset
        $barnds = $brand_model->limit($limit, $offset)->findAll();
        $data = [];
        foreach ($barnds as $barnd) {
            $route = base_url('admin/brand/get-brand');
            $delete_route = base_url('admin/brand/delete-brand');
            $action = "
                    <div class=\"d-flex gap-4\">
                        <button type='button' class='btn btn-primary btn-sm editBrand' data-toggle='tooltip' data-bs-placement='bottom' title='Edit Brand' onclick='editBrand(" . $barnd['id'] . ", \"" . $route . "\")'> <i class='bi bi-pencil'></i> </button>
                        <button type='button' class='btn btn-danger btn-sm ' data-toggle='tooltip' data-bs-placement='bottom' title='Edit Brand' onclick='deleteBrand(" . $barnd['id'] . ", \"" . $delete_route . "\")'> <i class='bi bi-trash'></i> </button>
                    </div>";

            $data[] = [
                'id' => $barnd['id'],
                'name' => $barnd['name'],
                'description' => $barnd['description'],
                'action' => $action
            ];
        }

        return $this->response->setJSON([
            'total' => $total,
            'rows' => $data
        ]);
    }

    public function delete()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            $response = [
                'error' => true,
                'message' => "Please login !",
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
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

            if ($this->ionAuth->isTeamMember()) {
                if (! userHasPermission('brand', 'can_delete', session('user_id'))) {
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

            $this->validation->setRules([
                'id' => [
                    'rules' => 'required',
                    'label' => 'Brand'
                ],
            ]);

            if ($this->validation->withRequest($this->request)->run()) {
                $id = $this->request->getPost('id');
                $business_id  = session('business_id');
                $brand_model = new BrandModel();
                
                $brand  =  $brand_model->where(['business_id' => $business_id, 'id' => $id])->get()->getResultArray();
                if (empty($brand)) {
                    $response = [
                        'error' => true,
                        'message' => ["Brand not found !"],
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }

                // make null for barnd_id in products table
                $db = \config\Database::connect();
                $db->table('products')->where(['brand_id' => $id])->update(['brand_id' => null]);

                $brand_model->where('id', $id)->delete();

                $response = [
                    'error' => false,
                    'message' => ["Brand deleted Successfully"],
                    'data' => []
                ];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                return $this->response->setJSON($response);
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
}
