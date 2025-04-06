<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class Languages extends BaseController
{
    protected $ionAuth;
    protected $validation;
    protected $configIonAuth;
    protected $session;
    protected $data;
    public function __construct()
    {
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->validation = \Config\Services::validation();
        helper(['form', 'url', 'function']);
        $this->configIonAuth = config('IonAuth');
        $this->session       = \Config\Services::session();
        $session = session();

        $lang = $session->get('lang');
        if (empty($lang)) {
            $lang = 'en';
        }

        $this->data['current_lang'] = $lang;
        $this->data['languages_locale'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
    }

    public function index()
    {
        if ($this->ionAuth->loggedIn()  || $this->ionAuth->isAdmin()) {
            $version = fetch_details('updates', [], ['version'], '1', '0', 'id', 'DESC')[0]['version'];
            $this->data['version'] = $version;
            $session = session();
            $lang = $session->get('lang');
            if (empty($lang)) {
                $lang = 'en';
            }
            $this->data['code'] = $lang;
            $this->data['title'] = 'Language';
            $this->data['page'] = FORMS . 'languages';
            $this->data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $this->data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            if (isset($_SESSION['user_id'])) {
                $id = $_SESSION['user_id'];
                $this->data['user'] = $this->ionAuth->user($id)->row();
            }

            $this->data['languages'] = fetch_details('languages', [], [], null, '0', 'id', 'ASC');
            return view("admin/template", $this->data);
        } else {
            return redirect('login');
        }
    }

    public function change($lang)
    {
        $session = session();
        $session->remove('lang');
        $session->set('lang', $lang);

        return redirect()->to("admin/languages/");
    }
    public function set_labels()
    {
        if ($this->ionAuth->loggedIn()  || $this->ionAuth->isAdmin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $_SESSION['toastMessage'] = DEMO_MODE_ERROR;
                $_SESSION['toastMessageType']  = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return redirect()->to('admin/languages')->withCookies();
            }
            helper('files');
            helper('filesystem');
            $my_lang = trim($_POST['code']);
            $labels = $_POST;
            $langstr = "\$lang['label_language'] = \"$my_lang\";" . "\n";

            $langstr_final = "<?php 
/**
*
*
* Descriptions :  " . $my_lang . " language file for general labels
*
*/" . "\n\n\n" . $langstr;
            foreach ($labels as $key => $val) {
                $langstr_final .= "\$lang['$key'] = \"$val\";" . "\n";
            }
            $langstr_final .= 'return $lang;';
            if (!is_dir('./app/Language/' . $my_lang . '/')) {
                mkdir('./app/Language/' . $my_lang . '/', 0777, TRUE);
            }

            if (file_exists('./app/Language/' . $my_lang . '/Text.php')) {
                delete_files('./app/Language/' . $my_lang . '/Text.php');
                write_file('./app/Language/' . $my_lang . '/Text.php', $langstr_final);
            } else {
                write_file('./app/Language/' . $my_lang . '/Text.php', $langstr_final);
            }
            $_SESSION['toastMessage'] = 'Labels Updated successfully';
            $_SESSION['toastMessageType']  = 'success';
            $this->session->markAsFlashdata('toastMessage');
            $this->session->markAsFlashdata('toastMessageType');
            return redirect()->to("admin/languages/change/" . $my_lang)->withCookies();
        } else {
            return redirect('login');
        }
    }
    public function create()
    {
        if ($this->ionAuth->loggedIn()  || $this->ionAuth->isAdmin()) {
            if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
                $_SESSION['toastMessage'] = DEMO_MODE_ERROR;
                $_SESSION['toastMessageType']  = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return redirect()->to('admin/languages')->withCookies();
            }
            helper('files');
            helper('filesystem');

            $db      = \Config\Database::connect();
            $language = (trim($_POST['language']));
            $code = str_replace(' ', '-', strtolower(trim($_POST['code'])));
            $check = fetch_details('languages', ['code' => $code]);
            if (count($check) > 0) {
                $_SESSION['toastMessage'] = "Language code already exists.";
                $_SESSION['toastMessageType']  = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return redirect()->to('admin/languages')->withCookies();
            }
            $check = $db->table('languages')->insert($data = ['language' => $language, 'code' => $code]);
            $my_lang = $code;
            if ($check) {
                $langstr = "\$lang['label_language'] = \"$my_lang\";" . "\n";

                $langstr_final = "<?php 
/**
*
*
* Description:  " . $my_lang . " language file for general labels
*
*/" . "\n\n\n" . $langstr;
                $langstr_final .= 'return $lang;';

                if (!is_dir('./app/Language/' . $my_lang . '/')) {
                    mkdir('./app/Language/' . $my_lang . '/', 0777, TRUE);
                }

                if (file_exists('./app/Language/' . $my_lang . '/Text.php')) {
                    delete_files('./app/Language/' . $my_lang . '/Text.php');
                    write_file('./app/Language/' . $my_lang . '/Text.php', $langstr_final);
                } else {
                    write_file('./app/Language/' . $my_lang . '/Text.php', $langstr_final);
                }

                $_SESSION['toastMessage'] = "Language added..";
                $_SESSION['toastMessageType']  = 'success';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return redirect()->to('admin/languages')->withCookies();
            } else {
                $_SESSION['toastMessage'] = 'something went wrong..';
                $_SESSION['toastMessageType']  = 'error';
                $this->session->markAsFlashdata('toastMessage');
                $this->session->markAsFlashdata('toastMessageType');
                return redirect()->to('admin/languages')->withCookies();
            }
        } else {
            return redirect('login');
        }
    }
}
