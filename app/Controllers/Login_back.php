<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Login_back extends BaseController
{
    protected $ionAuth;
    public function __construct()
    {
        $this->ionAuth = new \IonAuth\Libraries\IonAuth();
            $settings = get_settings('general', true);

    }
    public function index()
    {
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            $data['admin'] = true;
        } else {
            $data['admin'] = false;
        }
        $company_title = (isset($settings['title'])) ? $this->settings['title'] : "UpBiz";
        $data['company'] = $company_title;
        $data['page'] = "login_auth";
        $data['title'] = "Login - " . $company_title;
        $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
        $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
        if ($this->ionAuth->loggedIn()) {
            $data['logged'] = true;
        }
        $setting = get_settings('about_us', true);
        $setting = get_settings('refund_policy', true);
        $setting = get_settings('privacy_policy', true);
        $setting = get_settings('terms_and_conditions', true);
        $data['about_us'] = isset($setting['about_us']) ? $setting['about_us'] : '';
        $data['refund_policy'] = isset($setting['refund_policy']) ? $setting['refund_policy'] : '';
        $data['privacy_policy'] = isset($setting['privacy_policy']) ? $setting['privacy_policy'] : '';
        $data['terms_and_conditions'] = isset($setting['terms_and_conditions']) ? $setting['terms_and_conditions'] : '';

        $settings = get_settings('general', true);
        $data['logo'] = (isset($settings['logo'])) ? $settings['logo'] : "";
        $data['half_logo'] = (isset($settings['half_logo'])) ? $settings['half_logo'] : "";
        $data['favicon'] = (isset($settings['favicon'])) ? $settings['favicon'] : "";
        $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
        $data['version'] = $version;
        return view("auth/template", $data);
    }
}
