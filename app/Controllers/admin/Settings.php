<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Settings_model;

use function PHPUnit\Framework\fileExists;

class Settings extends BaseController
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
        if (!$this->ionAuth->loggedIn()) {
            return redirect()->to('login');
        }
    }
    public function general()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
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
        $data['page'] = FORMS . 'general-settings';
        $data['title'] = "General Settings - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['general'] = get_settings('general', true);
        return view("admin/template", $data);
    }
    public function about_us()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'about_us';
        $data['title'] = "About Us - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['about_us'] = get_settings('about_us', true);

        return view("admin/template", $data);
    }
    public function payment_gateway()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'payment_gateway';
        $data['title'] = "Payment Gateways - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['payment_gateway'] = get_settings('payment_gateway', true);

        return view("admin/template", $data);
    }
    public function refund_policy()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'refund_policy';
        $data['title'] = "Refund Policy - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['refund_policy'] = get_settings('refund_policy', true);

        return view("admin/template", $data);
    }
    public function terms_and_conditions()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'terms_and_conditions';
        $data['title'] = "T&C - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['terms_and_conditions'] = get_settings('terms_and_conditions', true);

        return view("admin/template", $data);
    }
    public function privacy_policy()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'privacy_policy';
        $data['title'] = "Privacy Policy - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['privacy_policy'] = get_settings('privacy_policy', true);

        return view("admin/template", $data);
    }
    public function system_updater()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
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
        $data['page'] = FORMS . 'system_update';
        $data['title'] = "System Update - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();

        return view("admin/template", $data);
    }
    public function email()
    {
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
            exit();
        }
        $session = session();

        $lang = $session->get('lang');
        if (empty($lang)) {
            $lang = 'en';
        }
        $data['code'] = $lang;
        $data['current_lang'] = $lang;
        $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');

        $email = get_settings('email', true);
        $data['email'] = $email;
        $settings = get_settings('general', true);
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['page'] = FORMS . 'email';
        $data['title'] = "Email Setting - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $id = $_SESSION['user_id'];
        $data['user'] = $this->ionAuth->user($id)->row();

        return view("admin/template", $data);
    }
    private function update_setting($variable, $value)
    {
        $this->builder->where('variable', $variable);
        if (exists(['variable' => $variable], 'settings')) {
            $this->db->transStart();
            $this->builder->update(['value' => $value]);
            $this->db->transComplete();
        } else {
            $this->db->transStart();
            $this->builder->insert(['variable' => $variable, 'value' => $value]);
            $this->db->transComplete();
        }

        return $this->db->transComplete() ? true : false;
    }
    public function save_settings()
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
            if (isset($_POST) && !empty($_POST)) {
                $setting_type = $this->request->getVar('setting_type');
                if ($setting_type == 'general') {

                    $settings = get_settings('general', true);
                    $logo = (isset($settings['logo'])) ? $settings['logo'] : "";
                    $half_logo = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
                    $favicon = (isset($settings['favicon'])) ? $settings['favicon'] : "";

                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'title' => 'required',
                            'support_email' => 'required|min_length[3]|max_length[255]',
                            'currency_symbol' => 'required',
                            'select_time_zone' => 'required',
                            'phone' => 'required',
                            'primary_color' => 'required',
                            'secondary_color' => 'required',
                            'primary_shadow' => 'required',
                            'copyright_details' => 'required',
                            'address' => 'required',
                            'short_description' => 'required',
                            'support_hours' => 'required',
                            'logo' => 'ext_in[logo,png,jpg,gif,jpeg] |is_image[logo]|max_size[logo, 1024]',
                            'half_logo' => 'ext_in[half_logo,jpg,png,jpeg,gif]|is_image[half_logo]|max_size[half_logo, 50]',
                            'favicon' => 'ext_in[favicon,jpg,png,jpeg,gif] |is_image[favicon]|max_size[favicon, 50]'
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
                            $settings_model = new Settings_model();
                            $path = './public/uploads/';
                            $path1 = '/public/uploads/';
                            if (!empty($_FILES['logo']) && isset($_FILES['logo'])) {
                                $file =  $this->request->getFile('logo');
                                if ($file->isValid()) {
                                    if ($file->move($path)) {
                                        if (fileExists($logo))
                                            unlink($logo);
                                        $logo = $path . $file->getName();
                                    }
                                }
                            }
                            if (!empty($_FILES['half_logo']) && isset($_FILES['half_logo'])) {
                                $file =  $this->request->getFile('half_logo');
                                if ($file->isValid()) {
                                    $newName = $file->getRandomName();
                                    if ($file->move($path, $newName)) {
                                        $half_logo = $path . $newName;
                                    }
                                }
                            }
                            if (!empty($_FILES['favicon']) && isset($_FILES['favicon'])) {
                                $file =  $this->request->getFile('favicon');
                                if ($file->isValid()) {
                                    $newName = $file->getRandomName();
                                    if ($file->move($path, $newName)) {
                                        $favicon = $path . $newName;
                                    }
                                }
                            }
                            $general_setting = array(
                                'title' => $this->request->getVar('title'),
                                'support_email' => $this->request->getVar('support_email'),
                                'currency_symbol' => $this->request->getVar('currency_symbol'),
                                'currency_locate' => $this->request->getVar('currency_locate'),
                                'date_format' => $this->request->getVar('date_format'),
                                'time_format' => $this->request->getVar('time_format'),
                                'decimal_points'=> $this->request->getVar('decimal_points'),
                                'mysql_timezone'=> $this->request->getVar('mysql_timezone'),
                                'select_time_zone' => $this->request->getVar('select_time_zone'),
                                'phone' => $this->request->getVar('phone'),
                                'primary_color' => $this->request->getVar('primary_color'),
                                'secondary_color' => $this->request->getVar('secondary_color'),
                                'primary_shadow' => $this->request->getVar('primary_shadow'),
                                'address' => $this->request->getVar('address'),
                                'short_description' => $this->request->getVar('short_description'),
                                'support_hours' => $this->request->getVar('support_hours'),
                                'logo' => $logo,
                                'half_logo' => $half_logo,
                                'favicon' => $favicon,
                                'copyright_details' => $this->request->getVar('copyright_details'),
                            );
                            
                            $data = [
                                'variable' => "general",
                                'value' => json_encode($general_setting)
                            ];


                            
                             $settings_model->save_settings('general', $data);
                           
                            $response = [
                                'error' => false,
                                'message' => ['Setting updated successfully'],
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                          

                            return $this->response->setJSON($response);
                        }
                    }
                }
                if ($setting_type == 'about_us') {
                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'about_us' => 'required',
                        ]);
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
                        $settings_model = new Settings_model();
                        $about_us = array(
                            'about_us' => $this->request->getVar('about_us')
                        );
                        $data = [
                            'variable' => "about_us",
                            'value' => json_encode($about_us)
                        ];
                        $settings_model->save_settings($setting_type, $data);
                        $response = [
                            'error' => false,
                            'message' => ['Setting updated successfully'],
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Setting updated';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');

                        return $this->response->setJSON($response);
                    }
                }
                if ($setting_type == 'refund_policy') {
                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'refund_policy' => 'required',
                        ]);
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
                        $settings_model = new Settings_model();
                        $refund_policy = array(
                            'refund_policy' => $this->request->getVar('refund_policy')
                        );
                        $data = [
                            'variable' => "refund_policy",
                            'value' => json_encode($refund_policy)
                        ];
                        $settings_model->save_settings($setting_type, $data);
                        $response = [
                            'error' => false,
                            'message' => ['Setting updated successfully'],
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Setting updated';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');

                        return $this->response->setJSON($response);
                    }
                }
                if ($setting_type == 'privacy_policy') {
                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'privacy_policy' => 'required',
                        ]);
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
                        $settings_model = new Settings_model();
                        $privacy_policy = array(
                            'privacy_policy' => $this->request->getVar('privacy_policy')
                        );
                        $data = [
                            'variable' => "privacy_policy",
                            'value' => json_encode($privacy_policy)
                        ];
                        $settings_model->save_settings($setting_type, $data);
                        $response = [
                            'error' => false,
                            'message' => ['Setting updated successfully'],
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Setting updated';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');

                        return $this->response->setJSON($response);
                    }
                }
                if ($setting_type == 'terms_and_conditions') {
                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'terms_and_conditions' => 'required',
                        ]);
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
                        $settings_model = new Settings_model();
                        $terms_and_conditions = array(
                            'terms_and_conditions' => $this->request->getVar('terms_and_conditions')
                        );
                        $data = [
                            'variable' => "terms_and_conditions",
                            'value' => json_encode($terms_and_conditions)
                        ];
                        $settings_model->save_settings($setting_type, $data);
                        $response = [
                            'error' => false,
                            'message' => ['Setting updated successfully'],
                        ];
                        $response['csrf_token'] = csrf_token();
                        $response['csrf_hash'] = csrf_hash();
                        $_SESSION['toastMessage'] = 'Setting updated';
                        $_SESSION['toastMessageType']  = 'success';
                        $this->session->markAsFlashdata('toastMessage');
                        $this->session->markAsFlashdata('toastMessageType');

                        return $this->response->setJSON($response);
                    }
                }
                if ($setting_type == 'system_updater') {

                    print_r($_FILES);
                }
                if ($setting_type == 'payment_gateway') {
                    if (isset($_POST) && !empty($_POST)) {

                        $this->validation->setRules([
                            'razorpay_payment_mode' => 'required|trim',
                            'razorpay_secret_key' => 'required|trim',
                            'razorpay_api_key' => 'required|trim',
                            'razorpay_status' => 'required|trim',
                            'stripe_payment_mode' => 'required|trim',
                            'stripe_currency_symbol' => 'required|trim',
                            'stripe_publishable_key' => 'required|trim',
                            'stripe_secret_key' => 'required|trim',
                            'stripe_webhook_secret_key' => 'required|trim',
                            'stripe_status' => 'required|trim',
                            'flutterwave_payment_mode' => 'required|trim',
                            'flutterwave_currency_symbol' => 'required|trim',
                            'flutterwave_public_key' => 'required|trim',
                            'flutterwave_secret_key' => 'required|trim',
                            'flutterwave_encryption_key' => 'required|trim',
                            'flutterwave_status' => 'required|trim',
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
                            $settings_model = new Settings_model();

                            $payment_gateway = array(
                                'razorpay_payment_mode' => $this->request->getVar('razorpay_payment_mode'),
                                'razorpay_secret_key' => $this->request->getVar('razorpay_secret_key'),
                                'razorpay_api_key' => $this->request->getVar('razorpay_api_key'),
                                'razorpay_status' => $this->request->getVar('razorpay_status'),
                                'stripe_payment_mode' => $this->request->getVar('stripe_payment_mode'),
                                'stripe_currency_symbol' => $this->request->getVar('stripe_currency_symbol'),
                                'stripe_publishable_key' => $this->request->getVar('stripe_publishable_key'),
                                'stripe_secret_key' => $this->request->getVar('stripe_secret_key'),
                                'stripe_webhook_secret_key' => $this->request->getVar('stripe_webhook_secret_key'),
                                'stripe_status' => $this->request->getVar('stripe_status'),
                                'flutterwave_payment_mode' => $this->request->getVar('flutterwave_payment_mode'),
                                'flutterwave_currency_symbol' => $this->request->getVar('flutterwave_currency_symbol'),
                                'flutterwave_public_key' => $this->request->getVar('flutterwave_public_key'),
                                'flutterwave_secret_key' => $this->request->getVar('flutterwave_secret_key'),
                                'flutterwave_encryption_key' => $this->request->getVar('flutterwave_encryption_key'),
                                'flutterwave_status' => $this->request->getVar('flutterwave_status')
                            );

                            $data = [
                                'variable' => "payment_gateway",
                                'value' => json_encode($payment_gateway)
                            ];
                            $settings_model->save_settings($setting_type, $data);
                            $response = [
                                'error' => false,
                                'message' => ['Setting updated successfully'],
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'Setting updated';
                            $_SESSION['toastMessageType']  = 'success';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');

                            return $this->response->setJSON($response);
                        }
                    } else {
                        return redirect()->back();
                    }
                }
                if ($setting_type == 'email') {
                    $settings = get_settings('email', true);
                    if (isset($_POST) && !empty($_POST)) {
                        $this->validation->setRules([
                            'email' => 'required|trim|valid_email',
                            'password' => 'required|min_length[8]',
                            'smtp_host' => 'required|trim',
                            'smtp_port' => 'required|trim',
                            'mail_content_type' => 'required|trim',
                            'smtp_encryption' => 'required|trim',
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
                            $settings_model = new Settings_model();

                            $email = array(
                                'email' => $this->request->getVar('email'),
                                'password' => $this->request->getVar('password'),
                                'smtp_host' => $this->request->getVar('smtp_host'),
                                'smtp_port' => $this->request->getVar('smtp_port'),
                                'mail_content_type' => $this->request->getVar('mail_content_type'),
                                'smtp_encryption' => $this->request->getVar('smtp_encryption'),
                            );

                            $data = [
                                'variable' => "email",
                                'value' => json_encode($email)
                            ];

                            $settings_model->save_settings($setting_type, $data);
                            $response = [
                                'error' => false,
                                'message' => ['Setting updated successfully'],
                            ];
                            $response['csrf_token'] = csrf_token();
                            $response['csrf_hash'] = csrf_hash();
                            $_SESSION['toastMessage'] = 'Setting updated';
                            $_SESSION['toastMessageType']  = 'success';
                            $this->session->markAsFlashdata('toastMessage');
                            $this->session->markAsFlashdata('toastMessageType');

                            return $this->response->setJSON($response);
                        }
                    }
                }
            }
        }
    }
}
