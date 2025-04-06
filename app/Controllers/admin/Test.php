<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class Test extends BaseController
{
    public function index()
    {
        echo "12s";
    }
    public function sendmail()
    {
        $email_config = array(
            'charset' => 'utf-8',
            'mailType' => 'html'
        );
        $email = \Config\Services::email();

        $email_settings = get_settings('email_settings', true);
        $smtpUsername = $email_settings['smtpUsername'];
        $email_type = $email_settings['mailType'];

        $email->setFrom($smtpUsername, $email_type);
        $email->setTo("harshadpatel1507@outlook.com");
        $email->setSubject("Test message");
        $email->setMessage("Hello");
        if ($email->send()) {
            echo "Email sent!";
        } else {
            echo $email->printDebugger();
            return false;
        }
    }
}
