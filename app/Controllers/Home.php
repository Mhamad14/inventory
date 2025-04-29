<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class Home extends Frontend
{
    protected $ionAuth;
    public function __construct()
    {
        parent::__construct();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }

    
    public function index()
    {
        if ($this->ionAuth->loggedIn()){
            return redirect('login');
        }
        $data['title'] = "Home &mdash; $this->appName";
        $data['page'] = VIEWS . 'login';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";

        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            $data['vendor'] = true;
        }
        $currency = get_settings('general', true);
        $currency = (isset($currency['currency_symbol'])) ? $currency['currency_symbol'] : '₹';
        $data['currency'] =  $currency;
        return view("frontend/template", $data);
    }
    public function test(){
    
    echo date_default_timezone_get();
    }

}
