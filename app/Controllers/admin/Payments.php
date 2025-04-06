<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Libraries\Flutterwave;
use App\Libraries\Razorpay;
use App\Libraries\Stripe;

class Payments extends BaseController
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


    public function pre_payment_setup()
    {

        $razorpay = new Razorpay;
        $flutterwave = new Flutterwave;
        if ($this->ionAuth->loggedIn()) {

            if ($_POST['payment_method'] == "Razorpay") {
                $amount = $_POST['amount'];
                $order = $razorpay->create_order(($amount * 100));

                if (!isset($order['error'])) {
                    $response['order_id'] = $order['id'];
                    $response['error'] = false;
                    $response['message'] = "Client Secret Get Successfully.";
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                } else {
                    $response['error'] = true;
                    $response['message'] = $order['error']['description'];
                    $response['details'] = $order;
                    $response['csrf_token'] = csrf_token();
                    $response['csrf_hash'] = csrf_hash();
                    return $this->response->setJSON($response);
                }
            } elseif ($_POST['payment_method'] == "stripe") {
                $amount = $_POST['amount'];
                $stripe = new Stripe;
                $payload = [
                    'amount' => ($amount * 100),
                    'metadata' => [
                        'user_id' => $_POST['user_id'],
                        'amount' => $amount,
                        'plan_id' => $_POST['plan_id'],
                        'tenure' => $_POST['tenure']
                    ]
                ];

                $order = $stripe->create_payment_intent($payload);
                $response['client_secret'] = $order['client_secret'];
                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['id'] = $order['id'];
                return $this->response->setJSON($response);
            } elseif ($_POST['payment_method'] == "Flutterwave") {

                $response['csrf_token'] = csrf_token();
                $response['csrf_hash'] = csrf_hash();
                $response['amount'] = $_POST['amount'];
                $response['error'] = false;

                return $this->response->setJSON($response);
            }
        }
    }
    public function post_payment()
    {
        if ($provider = $this->request->getPost('provider')) {
            $txn_id = $this->request->getPost('txn_id');
            $db = \Config\Database::connect();
            $tenure_id = $this->request->getPost('tenure_id');
            $plan_id = $this->request->getPost('plan_id');
            $tenure = $db->table('packages_tenures')->where(['id' => $tenure_id, 'package_id' => $plan_id])->get()->getResultArray()[0];
          
            $price = ( !empty($tenure['discounted_price']) && $tenure['discounted_price'] !="0" &&  $tenure['discounted_price'] != "") ? $tenure['discounted_price'] : $tenure['price'];
            $tenure_name = $tenure['tenure'];
            $id = $this->ionAuth->user()->row()->id;
            $insert_id = add_transaction($txn_id, $price, $provider, $id);
            if ($provider == 'razorpay') {
                $razorpay = verify_payment_transaction($txn_id, 'razorpay');
                if ($razorpay['error']) {
                    $response['error'] = true;
                    $response['message'] = $razorpay['message'];
                    update_details([
                        'message' => $response['message'],
                        'status' => $razorpay['status'],
                        'amount' => $razorpay['amount']
                    ], [
                        'id' => $insert_id
                    ], 'transactions');
                    return $this->response->setJSON($response);
                } elseif ($razorpay['amount'] >= $price) {
                    if ($sub_id = add_subscription($id, $plan_id, $tenure['months'], $txn_id, $price, $tenure_name)) {
                        $response['error'] = false;
                        $response['message'] = "Order Placed Successfully";
                        $response['data'] = $razorpay;
                        $response['plan'] = $plan_id;
                        update_details(
                            [
                                'message' => $response['message'],
                                'status' => $razorpay['status'],
                                'amount' => $price
                            ],
                            [
                                'id' => $insert_id
                            ],
                            'transactions'
                        );
                        update_details(
                            [
                                'transaction_id' => $insert_id,
                            ],
                            [
                                'id' => $sub_id,
                            ],
                            'users_packages'
                        );

                        return $this->response->setJSON($response);
                    }

                    $response['error'] = true;
                    $response['message'] = "something went wrong";
                    $response['data'] = $razorpay;
                    return $this->response->setJSON($response);
                } else {

                    $data['error'] = true;
                    $data['message'] = 'Paid amount ' . $razorpay['amount'] . ' is Lower than Package Price which is ' . $price;
                    $data['data'] = array();
                    return $this->response->setJSON($data);
                }
            } else if ($provider == 'flutterwave') {
                $flutterwave = verify_payment_transaction($txn_id, 'flutterwave', $insert_id);
                if ($flutterwave['error']) {
                    update_details([
                        'message' => $flutterwave['message'],
                        'status' => $flutterwave['data']['data']['status'],
                        'amount' => $flutterwave['amount']
                    ], [
                        'id' => $insert_id
                    ], 'transactions');

                    return $this->response->setJSON($flutterwave);
                } elseif ($flutterwave['amount'] >= $price) {
                    if ($sub_id = add_subscription($id, $plan_id, $tenure['months'], $txn_id, $price, $tenure_name)) {
                        $response['error'] = false;
                        $response['message'] = "Order Placed Successfully";
                        $response['data'] = $flutterwave;
                        $response['plan'] = $plan_id;
                        update_details(
                            [
                                'message' => $flutterwave['message'],
                                'status' => $flutterwave['status'],
                                'amount' => $price
                            ],
                            [
                                'id' => $insert_id
                            ],
                            'transactions'
                        );
                        update_details(
                            [

                                'transaction_id' => $insert_id,
                            ],
                            [
                                'id' => $sub_id,

                            ],
                            'users_packages'
                        );
                        return $this->response->setJSON($response);
                    }
                    $response['error'] = true;
                    $response['message'] = "something went wrong";
                    $response['data'] = $flutterwave;

                    return $this->response->setJSON($response);
                }
            } else if ($provider == "Stripe") {
                $stripe = new Stripe;
                $order = $stripe->create_payment_intent(array('amount' => ($price*100)));
                $this->response['client_secret'] = $order['client_secret'];
                $this->response['id'] = $order['id'];
            } else {
                $data['error'] = true;
                $data['message'] = "Invalid Provider.";
                $data['data'] = array();
                return $this->response->setJSON($data);
            }
        }
    }
    public function payment_success()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {

            $data['vendor'] = false;
        } else {
            $data['vendor'] = true;
        }
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        $data['business_id'] = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";


        $session = session();
        $lang = $session->get('lang');
        if (empty($lang)) {
            $lang = 'en';
        }
        $data['code'] = $lang;
        $data['current_lang'] = $lang;
        $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');

        $settings = get_settings('general', true);
        $id = $this->ionAuth->getUserId();
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['page'] = VIEWS . "payment_status";
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['title'] = "Payment Status - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        $data['status'] = true;
        return view("admin/template", $data);
    }

    public function payment_failed()
    {
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            $data['vendor'] = true;
        } else {
            $data['vendor'] = false;
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
        $data['business_id'] = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $settings = get_settings('general', true);
        $id = $this->ionAuth->getUserId();
        $company_title = (isset($settings['title'])) ? $settings['title'] : "";
        $data['page'] = VIEWS . "payment_status";
        $data['user'] = $this->ionAuth->user($id)->row();
        $data['title'] = "Payment Status - " . $company_title;;
        $data['status'] = false;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        return view("admin/template", $data);
    }
}
