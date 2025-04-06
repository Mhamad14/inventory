<?php

namespace App\Controllers\admin;

use App\Models\Businesses_model;
use App\Models\PermissionsModel;
use App\Models\Team_members_model;
use App\Controllers\BaseController;
use App\Models\UserPermissionModel;
use Config\Services;

class Team_members extends BaseController
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
        $this->session = \Config\Services::session();
        //   $this->session = \Config\Services::session();
    }

    public function index()
    {

        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
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
            $data['page'] = VIEWS . 'team_members';
            $data['title'] = "Team-members - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['user'] = $this->ionAuth->user($id)->row();
            $businesses = fetch_details("businesses", ['user_id' => $_SESSION['user_id']]);
            $data['businesses'] = isset($businesses) ? $businesses : "";

            return view("admin/template", $data);
        }
    }

    public function create()
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
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
            $data['page'] = FORMS . 'create-team-members';
            $data['title'] = "Team members - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $data['user'] = $this->ionAuth->user($id)->row();
            $businesses = fetch_details("businesses", ['user_id' => $_SESSION['user_id']]);
            $data['businesses'] = $businesses;
            // getting all the permissions
            $saved_permissions = config('Upbiz');

            $all_permissions = $saved_permissions->permissions;


            $data['all_permissions'] = $all_permissions;
            $actions = ['can_create', 'can_read', 'can_update', 'can_delete'];
            $data['actions'] = $actions;

            return view("admin/template", $data);
        }
    }

    public function save()
    {

        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {

            $id = session('user_id');
            $data['user'] = $this->ionAuth->user($id)->row();

            // Process the form submission
            if ($this->request->getMethod() === 'POST') {
                // Load necessary helpers and models;
                helper(['form', 'url']);
                $validation = Services::validation();

                // Set validation rules
                $this->validation->setRules([
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|valid_email|is_unique[users.email]',
                    'identity' => 'required',
                    'business_id' => 'required',
                    'password' => 'required|min_length[6]',
                    'password_confirm' => 'required|matches[password]',
                ], [
                    'first_name' => [
                        'required' => 'First name is required.'
                    ],
                    'last_name' => [
                        'required' => 'Last name is required.'
                    ],
                    'email' => [
                        'required'    => 'Email address is required.',
                        'valid_email' => 'Please enter a valid email address.',
                        'is_unique'   => 'This email address is already registered.'
                    ],
                    'identity' => [
                        'required' => 'Identity field is required.'
                    ],
                    'role' => [
                        'required' => 'Please select a role for the user.'
                    ],
                    'business_id' => [
                        'required' => 'Please select at least one business.'
                    ],
                    'password' => [
                        'required'    => 'Password is required.',
                        'min_length'  => 'Password must be at least 6 characters long.'
                    ],
                    'password_confirm' => [
                        'required' => 'Password confirmation is required.',
                        'matches'  => 'Password confirmation does not match the password.'
                    ]
                ]);

                if (!$validation->withRequest($this->request)->run()) {
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    return $this->response->setJSON($response);
                }


                // Save system user data in the 'users' table
                $mobile = $this->request->getPost('identity');
                $email = $this->request->getPost('email');
                $password = $this->request->getPost('password');
                $userData = [
                    'ip_address' => $this->request->getIPAddress(),
                    'username' => $mobile, // Set the username field to the mobile value
                    'mobile' => $mobile,
                    'password' => $password,
                    'email' => $email,
                    'created_on' => time(),
                    'active' => 1,
                    'first_name' => $this->request->getPost('first_name'),
                    'last_name' => $this->request->getPost('last_name'),
                ];


                $email    =  strtolower($userData['email']);
                $identity = $userData['mobile'];
                $password = trim($userData['password']);
                $group_id_arry = fetch_details("groups", ['name' => 'team_members'], "id");
                $group_id = [$group_id_arry[0]['id']];
                // echo "<pre> group_id  = ";
                // print_r($group_id);
                // die();
                $additionalData = [
                    'first_name' =>  trim($userData['first_name']),
                    'phone'      => trim($userData['mobile']),
                    'last_name' => trim($userData['last_name']),
                ];


                // here insert the data in users table.
                $new_user_id = $this->ionAuth->register($identity, $password, $email, $additionalData, $group_id);


                if ($new_user_id) {
                    $permissions_from_post = $this->request->getPost('permissions');
                    // Initializeing an array to store the formatted permissions for the database
                    $permission_to_store = [];

                    if ($permissions_from_post) {
                        foreach ($permissions_from_post as $permission_name => $permission) {

                            $actions_to_store = [];
                            foreach ($permission as $action => $value) {

                                $input_name = "permissions[$permission_name][$action]";


                                if ($this->request->getPost($input_name)) {
                                    $actions_to_store[] =  $action;
                                }
                            }

                            if (!empty($actions_to_store)) {
                                $permission_to_store[$permission_name] =     $actions_to_store;
                            }
                        }
                    }

                    // Converting the array to JSON for database storage
                    $permissions_json = json_encode($permission_to_store);

                    $business_ids = $this->request->getVar('business_id');
                    $vendor_id = $_SESSION['user_id'];

                    //id	user_id	vendor_id	business_ids
                    $team_memeber_data = [
                        'user_id' => $new_user_id,
                        'vendor_id' => $vendor_id,
                        'business_ids' => json_encode($business_ids),
                        'permissions' => $permissions_json,
                    ];
                    $db = \Config\Database::connect();
                    $db->table('team_members')->insert($team_memeber_data);


                    $_SESSION['toastMessage'] = 'Team member added successfully';
                    $_SESSION['toastMessageType']  = 'success';

                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['message'] = 'Team member added successfully';

                    $this->session->markAsFlashdata('toastMessage');
                    $this->session->markAsFlashdata('toastMessageType');
                    return $this->response->setJSON($response);
                } else {

                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    $response['message'] = 'Failed to create team member. Please try again.';
                    $response['error'] = true;
                    return $this->response->setJSON($response);
                }
            }


            return  $this->response->setJSON(
                [
                    'csrf_token' => csrf_token(),
                    'csrf_hash' => csrf_hash(),
                    'error' => true,
                    'message' => ['Invalid request method'],
                    'data' => []
                ]
            );
        }
    }

    public function view_team_members()
    {
        $team_members_model = new Team_members_model();
        $all_user_ids = $team_members_model->select('user_id, id')->get()->getResultArray();
        $data = [];
        foreach ($all_user_ids as $key => $value) {
            $user_id = $value['user_id'];
            $team_memeber_id = $value['id'];
            $user_data = fetch_details('users', ["id" => $user_id]);

            $data[]  = [
                'team_member_id' => $team_memeber_id,
                'user_id' => $user_id,
                'mobile' => $user_data[0]['mobile'],
                'first_name' => $user_data[0]['first_name'],
                'last_name' => $user_data[0]['last_name'],
                'email' => $user_data[0]['email'],
                'operate' =>
                '<a href="' . site_url('admin/team_members/edit_user/' . $user_id) . '" class="btn btn-primary">
                        <i class="fa fa-pen"></i> ' . labels('', "") . '
                    </a>'
            ];
        }

        return $this->response->setJSON($data);
    }

    public function edit_user($id)
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
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

            $data['page'] = FORMS . "edit-user";
            $data['title'] = "Edit User - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Create user - Welcome to Subscribers, a digital solution for your subscription-based daily problems";


            $userId = $id;

            $team_member_model = new Team_members_model();


            $user = fetch_details('users', ['id' => $userId]);
            $user = (!empty($user))  ? $user[0] : [];
            $data['user'] =  (object) $user;


            $team_member = $team_member_model->select()->where('user_id', $userId)->get()->getResultArray();
            $team_member = (!empty($team_member)) ? $team_member[0] : [];
            $team_member_id = $team_member['id'];

            $businesses = $team_member_model->get_user_businesses($userId);

            foreach ($businesses as $key) {
                $business_ids[] =  json_decode($key['business_ids']);
            }

            $data['business_ids'] =  isset($business_ids) ? $business_ids[0] : [];

            $team_member_permissions = json_decode($team_member['permissions'], true);


            $saved_permissions = config('Upbiz');
            $all_permissions = $saved_permissions->permissions;
            $data['all_permissions'] =  $all_permissions;
            $data['user_permissions'] =  $team_member_permissions;



            $actions = ['can_create', 'can_read', 'can_update', 'can_delete'];
            $data['actions'] = $actions;
            $businesses = fetch_details("businesses", ['user_id' => $_SESSION['user_id']]);
            $businesses_list = [];
            foreach ($businesses as $key => $value) {
                $businesses_list[] = [
                    'id' => $value['id'],
                    'name' => $value['name']
                ];
            }
            $data['businesses_list'] = $businesses_list;

            return view("admin/template", $data);
        }
    }


    public function update_user()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
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
            if (isset($_POST) && !empty($_POST)) {
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
                $company_title = isset($settings['title']) ? $settings['title'] : "";
                // $data['page'] = FORMS . 'create-system-users';
                $data['title'] = "Create User-" . $company_title;
                $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
                $data['meta_description'] = "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems";
                $id = session('user_id');
                $data['user'] = $this->ionAuth->user($id)->row();


                helper(['form', 'url']);
                $validation = Services::validation();

                $validation->setRules([
                    'id' => 'permit_empty', // Remove the "required" rule
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'required|valid_email',
                    'identity' => 'required',
                    'password' => 'permit_empty|min_length[6]',
                    'password_confirm' => 'permit_empty|matches[password]',
                ]);
                if (!$validation->withRequest($this->request)->run()) {
                    $errors = $this->validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    return $this->response->setJSON($response);
                    return redirect()->back()->with('message', $errorMessage);
                }

                $requestData = $this->request->getPost(); // Get the validated data from the request

                if (!$validation->run($requestData)) { // Pass the validated data to the run() method
                    $errors = $validation->getErrors();
                    $response = [
                        'error' => true,
                        'message' => $errors,
                        'data' => []
                    ];
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {


                    $systemUsersModel = new Team_members_model();
                    $user_id = $requestData['id'];
                    $business_ids_post  = $requestData['business_id'];


                    // Retrieve the user and permissions by the provided ID
                    $user = fetch_details('users', ['id' => $user_id]);

                    if (!$user) {
                        $response = [
                            'error' => true,
                            'message' => 'User not found',
                            'data' => []
                        ];
                        return $this->response->setJSON($response);
                    }

                    $user = $user[0];

                    // Prepare the data to be updated
                    $updateData = [
                        'id' => $user_id,
                        'first_name' => $requestData['first_name'] ?: $user->first_name,
                        'last_name' => $requestData['last_name'] ?: $user->last_name,
                        'mobile' => $requestData['identity'] ?: $user->identity,
                    ];

                    // Check if email is modified and not empty
                    $email = $requestData['email'];
                    if ($email !== $user['email'] && !empty($email)) {
                        // Check if the new email already exists for another user
                        $existingUser = $systemUsersModel->where('email', $email)->first();
                        if ($existingUser && $existingUser['id'] !== $user_id) {
                            $response = [
                                'error' => true,
                                'message' => 'Email is already taken',
                                'data' => []
                            ];
                            return $this->response->setJSON($response);
                        }
                        $updateData['email'] = $email;
                    } else {
                        $updateData['email'] = $user['email']; // Keep the existing email
                    }

                    // Check if password is modified and not empty
                    $password = $requestData['password'];
                    if (!empty($password)) {
                        $updateData['password'] = password_hash($password, PASSWORD_DEFAULT);
                    }

                    // $permissions_post =  $this->request->getVar('permissions');
                    $permissions_from_post = $this->request->getPost('permissions');

                    // Initializeing an array to store the formatted permissions for the database
                    $permission_to_store = [];

                    if ($permissions_from_post) {
                        foreach ($permissions_from_post as $permission_name => $permission) {

                            $actions_to_store = [];
                            foreach ($permission as $action => $value) {

                                $input_name = "permissions[$permission_name][$action]";


                                if ($this->request->getPost($input_name)) {
                                    $actions_to_store[] =  $action;
                                }
                            }

                            if (!empty($actions_to_store)) {
                                $permission_to_store[$permission_name] =     $actions_to_store;
                            }
                        }
                    }

                    $db = \Config\Database::connect();
                    $builder = $db->table('users');
                    $builder->where('id', $updateData['id']);
                    $builder->update($updateData);


                    $permissionsJson =  json_encode($permission_to_store);
                    $vendor_id = session('user_id');

                    // Prepare the data to insert into 'user_permissions' table
                    $data = [
                        'user_id'    => $user_id, // Assign the user ID
                        'vendor_id' => $vendor_id,
                        'business_ids' => json_encode($business_ids_post), // The business ID from the array
                        'permissions' => $permissionsJson // Permissions JSON, you can customize this per business if needed
                    ];


                    $db = \Config\Database::connect();
                    $updated = $db->table('team_members')->where('user_id', $user_id)->update($data);

                    if ($updated) {
                        $response = [
                            'error' => false,
                            'message' => 'Team member Updated  successfully',
                            'data' => []
                        ];
                    } else {
                        $response = [
                            'error' => true,
                            'message' => 'Failed to Update team member',
                            'data' => []
                        ];
                    }


                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } else {
                return redirect()->to('admin/team_members');
            }
        }
        return view("admin/template", $data);
    }
    // public function delete_user($user_id)
    // {
    //     // Ensure that $user_id is a valid integer value
    //     if (!ctype_digit($user_id)) {
    //         // Return an error response if the user_id is not valid
    //         $response = [
    //             'error' => true,
    //             'message' => 'Invalid user ID.',
    //             'csrf_token' => csrf_token(),
    //             'csrf_hash' => csrf_hash(),
    //             'data' => []
    //         ];
    //         return $this->response->setJSON($response);
    //     }

    //     // Assuming you have some validation checks for user existence before deleting.
    //     $systemUsersModel = new System_users_model();
    //     $user = $systemUsersModel->find($user_id);

    //     if (!$user) {
    //         // Return an error response if the user with the given ID doesn't exist
    //         $response = [
    //             'error' => true,
    //             'message' => 'User not found.',
    //             'csrf_token' => csrf_token(),
    //             'csrf_hash' => csrf_hash(),
    //             'data' => []
    //         ];
    //         return $this->response->setJSON($response);
    //     }

    //     // Delete the corresponding user permissions
    //     $db = \Config\Database::connect();
    //     $db->table('user_permissions')->where('user_id', $user_id)->delete();

    //     // Perform the deletion of the user
    //     $systemUsersModel->delete($user_id);

    //     $response = [
    //         'error' => false,
    //         'message' => 'User deleted successfully',
    //         'data' => []
    //     ];
    //     $response['csrf_token'] = csrf_token();
    //     $response['csrf_hash'] = csrf_hash();
    //     $_SESSION['toastMessage'] = 'User added successfully';
    //     $_SESSION['toastMessageType']  = 'success';
    //     $this->session->markAsFlashdata('toastMessage');
    //     $this->session->markAsFlashdata('toastMessageType');
    //     return $this->response->setJSON($response);

    //     return view("vendor/template", $data);
    // }










    // public function create()
    // {
    //     $data['system_modules'] = config('Upbiz')->system_modules;
    //     $data['actions'] = ['create', 'read', 'update', 'delete'];

    //     if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
    //         return redirect()->to('login');
    //     } else {
    //         $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
    //         $data['version'] = $version;
    //         $session = session();

    //         $lang = $session->get('lang');
    //         if (empty($lang)) {
    //             $lang = 'en';
    //         }
    //         $data['code'] = $lang;
    //         $data['current_lang'] = $lang;
    //         $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');

    //         $settings = get_settings('general', true);
    //         $company_title = isset($settings['title']) ? $settings['title'] : "";
    //         $data['page'] = FORMS . 'create-system-users';
    //         $data['title'] = "Create Vendor-" . $company_title;
    //         $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
    //         $data['meta_description'] = "Home - Welcome to Subscribers, a digital solution for your subscription-based daily problems";
    //         $id = session('user_id');
    //         $data['user'] = $this->ionAuth->user($id)->row();

    //         // Process the form submission
    //         if ($this->request->getMethod() === 'post') {
    //             $validationRules = [
    //                 'username' => 'required',
    //                 'mobile' => 'required',
    //                 'email' => 'required|valid_email|is_unique[users.email]',
    //                 'password' => 'required|min_length[6]',
    //                 'confirm_password' => 'required|matches[password]',
    //                 'role' => 'required'
    //             ];

    //             if (!$this->validate($validationRules)) {
    //                 $data['validation'] = $this->validator;
    //             } else {
    //                 $systemUsersModel = new SystemUsersModel();

    //                 $userData = [
    //                     'username' => $this->request->getPost('username'),
    //                     'mobile' => $this->request->getPost('mobile'),
    //                     'email' => $this->request->getPost('email'),
    //                     'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
    //                     'created_on' => time(),
    //                     'active' => 1,
    //                     'first_name' => $this->request->getPost('first_name'),
    //                     'last_name' => $this->request->getPost('last_name')
    //                 ];

    //                 $userId = $systemUsersModel->insert($userData);

    //                 if ($userId) {
    //                     $permissions = [];
    //                     $permissionsJson = $this->request->getPost('permissions');

    //                     if (!empty($permissionsJson)) {
    //                         $permissions = json_decode($permissionsJson, true);
    //                     }

    //                     foreach ($data['system_modules'] as $module => $module_actions) {
    //                         foreach ($data['actions'] as $action) {
    //                             $permissionValue = isset($permissions[$module][$action]) && $permissions[$module][$action] === 'on' ? 'on' : 'off';
    //                             $permissions[$module][$action] = $permissionValue;
    //                         }
    //                     }

    //                     $permissionsJson = json_encode($permissions);

    //                     $systemUsersModel->addUserPermissions([
    //                         'user_id' => $userId,
    //                         'role' => $this->request->getPost('role'),
    //                         'permissions' => $permissionsJson
    //                     ]);

    //                     return redirect()->to('admin/system_users')->with('message', 'User created successfully.');
    //                 } else {
    //                     return redirect()->back()->with('message', 'Failed to create user. Please try again.');
    //                 }
    //             }
    //         }

    //         return view("admin/template", $data);
    //     }
    // }
}
