<?php

namespace App\Libraries;
/* 
    1. get_credentials()
    2. create_order($amount,$receipt='')
    3. fetch_payments($id ='')
    4. capture_payment($amount, $id, $currency = "INR")
    5. verify_payment($order_id, $razorpay_payment_id, $razorpay_signature)
    0. curl($url, $method = 'GET', $data = [])
*/



class Flutterwave
{
    private $secret_key, $public_key, $curl;
    public $temp = array();

    function __construct($mode = 'Test')
    {
        $settings = get_settings('payment_gateway', true);
        $this->public_key = (isset($settings['flutterwave_public_key'])) ? $settings['flutterwave_public_key'] : "";
        $this->secret_key = (isset($settings['flutterwave_secret_key'])) ? $settings['flutterwave_secret_key'] : "";
        $this->encryption_key = (isset($settings['flutterwave_encryption_key'])) ? $settings['flutterwave_encryption_key'] : "";
        $this->currency_code = (isset($settings['flutterwave_currency_symbol'])) ? $settings['flutterwave_currency_symbol'] : "";
    }
    public function get_credentials()
    {
        $credentials = array(
            'public_key' => $this->public_key,
            'secret_key' => $this->secret_key,
            'encryption_key' => $this->encryption_key,
            'currency_code' => $this->currency_code,
        );
        return $credentials;
    }

    function verify_transaction($transaction_id)
    {
    
        $url = "https://api.flutterwave.com/v3/transactions/$transaction_id/verify";
        $method = "GET";
        $create_transfer = $this->curl_request($url, $method);
        return $create_transfer;
    }

    function create_payment($data)
    {
        $url = "https://api.flutterwave.com/v3/payments";
        $method = "POST";
        $create_transfer = $this->curl_request($url, $method, $data);
        return $create_transfer;
    }

    public function curl_request($end_point, $method, $data = array())
    {
        $this->curl = curl_init();
        $data['seckey'] = $this->secret_key;
        curl_setopt_array($this->curl, array(
            CURLOPT_URL => $end_point,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => strtoupper($method),
            CURLOPT_POSTFIELDS => json_encode($data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json",
                "Authorization: Bearer " . $this->secret_key
            ),
        ));

        $response = curl_exec($this->curl);
        curl_close($this->curl);
        return $response;
    }
}
