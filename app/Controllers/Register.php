<?php

namespace App\Controllers;

use App\Controllers\BaseController;


class Register extends BaseController
{
    protected $ionAuth;
    protected $session;
    protected $validation;
    protected $configIonAuth;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'function']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
    }
    public function index()
    {
        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $company_title = (isset($settings['title'])) ? $settings['title'] : "UpBiz";
        $data['company'] = $company_title;
        $data['page'] = "register";
        $data['title'] = "Sign up - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        return view("auth/template", $data);
    }
}
