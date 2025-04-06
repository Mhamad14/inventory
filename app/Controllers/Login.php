<?php

namespace App\Controllers;

use App\Controllers\Frontend;

class Login extends Frontend
{
    protected $ionAuth ;
    public function __construct()
    {
        parent::__construct();
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
    }
    public function index()
    {
        
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            $data['admin'] = true;
        } else {
            $data['admin'] = false;
        }
        $data['title'] = "Sign In &mdash;$this->appName ";
        $data['page'] = VIEWS . 'login';
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "$this->appName an digital solution for your subscription based daily problems";
        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }
        $setting = get_settings('about_us', true);
        $data['text'] = isset($setting['about_us']) ? $setting['about_us'] : '';
        return view("frontend/template", $data);
    }
}
