<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\Orders_model;
use App\Libraries\PdfLibrary;
use  TCPDF;

class Invoices extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
        // $settings = get_settings('general', true);
    }
    public function index()
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            return redirect()->to('admin/orders/orders');
        }
    }
    public function invoice($order_id)
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $data['version'] = $version;
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $data['code'] = $lang;
            $data['current_lang'] = $lang;
            $data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            $settings = get_settings('general', true);
            $data['currency'] = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';
            $company_title = (isset($settings['title'])) ? $settings['title'] : "";
            $data['page'] = VIEWS . "invoices";
            $data['title'] = "Invoice - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $transactions_array = [] ;
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $orders_model = new Orders_model();
            $order = $orders_model->get_order_invoice($order_id, $business_id);
            if (isset($order) && !empty($order)) {
                $data['order'] = $order[0];
                $customer_id = $order[0]['customer_id'];
                // Get the submitted customer ID (initially referencing the "users" table)
                // Try to find a customer record where "user_id" matches the submitted "customer_id"
                $customer = fetch_details("customers", ['user_id' => $customer_id]);

                // Initialize $user_id to 0
                $user_id = 0;

                if (empty($customer)) {
                    // If no customer is found by "user_id", assume "customer_id" refers directly to the "customers" table "id"
                    $customer = fetch_details("customers", ['id' => $customer_id]);
                    $customer_id = $customer[0]['id'];   // Update customer_id to the correct "customers" table ID
                    $user_id = $customer[0]['user_id']; // Get the associated "user_id"

                } else {
                    // If a customer is found by "user_id", extract the relevant data
                    $user_id = $customer[0]['user_id']; // Get the associated "user_id"
                    $customer_id = $customer[0]['id']; // Update customer_id to the correct "customers" table ID
                }
                $transactions = fetch_details("customers_transactions", ["order_id" => $order_id, "user_id" => $user_id]);

                foreach( $transactions as $transaction){
                    $transactions_array [] =  [
                        'payment_type'=> $transaction['payment_type'],
                        'amount'=> $transaction['amount'],
                        'transaction_id'=> $transaction['transaction_id'],
                        'message'=> $transaction['message'],
                        'created_at'=> $transaction['created_at'],
                    ];
                }
                $data['transactions_array'] = $transactions_array;
            } else {
                $order = [];
            }
            return view("admin/template", $data);
        }
    }

    public function send()
    {
        $setting = get_settings('email', true);
        $company_title = get_settings('general', true);
        $email_id = $_POST['email_id'];
        $order_id = $_POST['order_id'];
        $myFile = FCPATH . 'public\invoice\invoice' . $order_id . '.pdf';
        $pdf = new PdfLibrary(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        if (file_exists($myFile)) {
            $pdf->Output($myFile, 'E');
        } else {
            $path = FCPATH . "public\invoice\invoice";
            $pdf->Output($path . $order_id . '.pdf', 'F');
        }
        $email_con = [
            'protocol'  => 'smtp',
            'SMTPHost'  => $setting['smtp_host'],
            'SMTPPort'  => (int) $setting['smtp_port'],
            'SMTPUser'  => $setting['email'],
            'SMTPPass'  => $setting['password'],
            'SMTPCrypto' => $setting['smtp_encryption'],
            'mailType'  => $setting['mail_content_type'],
            'charset'   => 'utf-8',
            'wordWrap'  => true,
        ];

        $subject = "Invoice";
        $email = \Config\Services::email();
        $email->initialize(config: $email_con);
        $email->setFrom(from: $setting['email'], name: $company_title['title']);
        $email->setTo(to: trim(string: $email_id));
        $email->setSubject(subject: $subject);
        $email->setMessage(body: 'invoice');
        $email->attach(file: $myFile);

        if ($email->send()) {
            return $this->response->setJSON([
                "error" => false,
                "message" => "Email sent!",
                "data" => [],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                "error" => true,
                "message" => "Something went wrong Please try again after some time.",
                "data" => [
                    'console' => "console.log(" . $email->printDebugger() . ");"
                ],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        }
    }
    public function invoice_table($order_id = "")
    {
        $orders_model = new Orders_model();
        $settings = get_settings('general', true);
        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '$';
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $orders = $orders_model->get_order_invoice($order_id, $business_id);

        $total =  count($orders);
        if (!empty($orders)) {
            $i = 0;
            foreach ($orders as $order) {
                if (isset($order['service_name']) && !empty($order['service_name'])) {
                    $name = $order['service_name'];
                }
                if (isset($order['product_name']) && !empty($order['product_name'])) {
                    $name = $order['product_name'];
                }
                $tax_details = json_decode($order['tax_details'], true);
                $tax_amount = empty($order['tax_name']) ? 0 : $order['price'] / (1 + $order['tax_percentage']);
                $tax_amount_html = '<span>' . htmlspecialchars($order['tax_name']) . ' : ' . currency_location(number_format($tax_amount, 2)) . '</span><br>';
                $tax_amount_html .= '<span><strong>Total Tax: ' . currency_location(number_format($tax_amount, 2)) . '</strong></span>';

                $price =  $order['price'] -   $tax_amount;

                if (empty($tax_details)) {
                    $rows[$i] = [
                        'order_type' => ucwords($order['order_type']),
                        'name' => ucwords($name),
                        'price' => currency_location(number_format($price, 2)),
                        'tax' => empty($order['tax_name']) ?  "- - -"    : $order['tax_name'] . " - " . $order['tax_percentage'] . "%",
                        'tax_amount' =>  empty($order['tax_name'])  ? "- - -"  : $tax_amount_html,  // Tax amounts in HTML
                        'quantity' => ucwords($order['quantity']),
                        'subtotal' => currency_location(number_format($order['sub_total'], 2))
                    ];
                } else {

                    $tax_name = '';
                    $tax_amount_html = '';
                    $total_tax_percentage = 0;

                    // Calculate total tax percentage
                    foreach ($tax_details as $tax) {
                        $total_tax_percentage += $tax['percentage'];
                        $tax_name .= '<span>' . htmlspecialchars($tax['name']) . ' :  ' . htmlspecialchars($tax['percentage']) . '%</span><br>';
                    }

                    // Calculate original amount (before tax)
                    $original_amount = $order['price'] / (1 + $total_tax_percentage / 100);
                    $total_tax_amount = 0;

                    // Calculate each tax amount and append HTML
                    foreach ($tax_details as $tax) {
                        $tax_percentage = $tax['percentage'];
                        $tax_amount = $original_amount * $tax_percentage / 100;
                        $tax_amount_html .= '<span>' . htmlspecialchars($tax['name']) . ' : ' . currency_location(number_format($tax_amount, 2)) . '</span><br>';
                        $total_tax_amount += $tax_amount;
                    }

                    // Add total tax HTML
                    $tax_amount_html .= '<span><strong>Total Tax: ' . currency_location(number_format($total_tax_amount, 2)) . '</strong></span>';

                    // Populate $rows array
                    $price = $order['price'] -  $total_tax_amount;
                    $rows[$i] = [
                        'order_type' => ucwords($order['order_type']),
                        'name' => ucwords($name),
                        'price' => currency_location(number_format($price, 2)),
                        'tax' => $tax_name,  // Existing tax details as a label
                        'tax_amount' => $tax_amount_html,  // Tax amounts in HTML
                        'quantity' => ucwords($order['quantity']),
                        'subtotal' => currency_location(number_format($order['sub_total'], 2))
                    ];
                }


                $i++;
            }
            $row = [
                'order_type' => "",
                'name' => "",
                'tax_percentage' => "",
                'quantity' => "<strong>Total</strong>",
                'price' => "",
                'subtotal' => "<strong>" . currency_location(number_format($order['total'], 2)) . "</strong>",
            ];

            array_push($rows, $row);
            $array['total'] = $total;
            $array['rows'] = $rows;
            echo json_encode($array);
        }
    }
    public function view_invoice($invoice_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $settings = get_settings('general', true);
            $email_settings = get_settings('email', true);
            $data['email'] = isset($mail) && !empty($email) ? $email : [];
            $currency = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '₹';
            if ($currency == '₹') {
                $currency = "Rs.";
            }
            $data['currency'] = $currency;
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $model = new Orders_model();
            $order = $model->get_order_invoice($invoice_id, $business_id);
            $data['order'] = $order;
            $data['name'] = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "Business Name";
            return view("admin/pages/views/view_invoice", $data);
        }
    }
    public function thermal_print($order_id = "")
    {
        if (!$this->ionAuth->loggedIn() || (!$this->ionAuth->isAdmin() && !$this->ionAuth->isTeamMember())) {
            return redirect()->to('login');
        } else {
            $settings = get_settings('general', true);
            $email_settings = get_settings('email', true);
            $data['email'] = isset($mail) && !empty($email) ? $email : [];
            $currency = isset($settings['currency_symbol']) ? $settings['currency_symbol'] : '$';
            $data['currency'] = $currency;
            $user_id = $_SESSION['user_id'];
            $id = 0;
            if ($this->ionAuth->isTeamMember()) {
                $id = get_vendor_for_teamMember($user_id);
            } else {
                $id = $user_id;
            }
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            $model = new Orders_model();
            $order = $model->get_order_invoice($order_id, $business_id);
            $data['order'] = $order;
            $data['name'] = isset($_SESSION['business_name']) ? $_SESSION['business_name'] : "Business Name";

            return view("admin/pages/views/thermal_print", $data);
        }
    }
}
