<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;

class Database extends BaseController
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
        if (!$this->ionAuth->loggedIn() || !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            if (isset($_SESSION['business_id'])) {
                if (check_data_in_table('businesses', $_SESSION['business_id'])) {
                    return redirect()->to("admin/businesses");
                }
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
            $data['page'] = VIEWS . "backup";
            $data['title'] = "Database Backup - " . $company_title;
            $data['meta_keywords'] = "subscriptions app, digital subscription, daily subscription, software, app, module";
            $data['meta_description'] = "Home - Welcome to Subscribers, an digital solution for your subscription based daily problems";
            $id = $_SESSION['user_id'];
            $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
            $data['business_id'] = $business_id;
            $data['user'] = $this->ionAuth->user($id)->row();
            return view("admin/template", $data);
        }
    }
    public function backup()
    {
        // if (defined('ALLOW_MODIFICATION') && ALLOW_MODIFICATION == 0) {
        //     $response = [
        //         'error' => true,
        //         'message' => [DEMO_MODE_ERROR],
        //         'csrfName' => csrf_token(),
        //         'csrfHash' => csrf_hash(),
        //         'data' => []
        //     ];
        //     return $this->response->setJSON($response);
        // }
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $rows = [];
            $i = 0;
            $j = 1;
            $path = FCPATH . 'public/database_backup/';
            $maps = get_dir_file_info($path);

            foreach ($maps as $files) {


                // $action = "<a href=" . site_url('admin/database/download') . "/" . $files['name'] . " class='btn btn-info btn-sm'  title='Download'><i class='bi bi-download'></i></a>" . " ";

                $action = "<button id='" . $files['name'] . "' onclick='download_backup(this)' class='btn btn-info btn-sm m-1'  title='Download'><i class='bi bi-download'></i></button>" . " ";

                $action .= "<button id='" . $files['name'] . "' data-bs-toggle='modal' data-bs-target='#mail_DBbackup' onClick='mail_backup(this)' class='btn btn-warning btn-sm m-1'  title='Mail'><i class='bi bi-envelope'></i></button>" . " ";

                $action .= "<button id='" . $files['name'] . "' class='btn btn-danger btn-sm m-1' onClick='delete_backup(this)'
                      title='Delete'><i class='bi bi-trash'></i>
                    </button>  ";

                $date = $files['date'];
                $rows[$i] = [
                    'no_of_files' => $j,
                    'file' => $files['name'],
                    'date' => date("d-m-Y h:i:s", $date),
                    'server_path' => $files['server_path'],
                    'relative_path' => $files['relative_path'],
                    'action' => $action,
                ];
                $i++;
                $j++;
            }
        }

        $array['rows'] = ($rows);
        $array['total'] = count($rows);

        echo json_encode($array);
    }

    public function backup_database()
    {
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $db = \Config\Database::connect();
            $database = $db->database;
            $host = $db->hostname;
            $password = $db->password;
            $user = $db->username;

            backup_tables($host, $user, $password, $database);
            $response = [
                'error' => false,
                'message' => [],
                'csrfName' => csrf_token(),
                'csrfHash' => csrf_hash(),
                'data' => []
            ];
            return $this->response->setJSON($response);
        }
    }
    public function mail_database()
    {
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
        if (!$this->ionAuth->loggedIn() && !$this->ionAuth->isAdmin()) {
            return redirect()->to('login');
        } else {
            $setting = get_settings('email', true);
            $email_id = $_POST['email'];
            $message = $_POST['message'];
            $company_title = get_settings('general', true);
            $path = FCPATH . 'public/database_backup/' . $_POST['file_name'] . '';

            $email_con =[
                'protocol'  => 'smtp',
                'SMTPHost'  => $setting['smtp_host'],
                'SMTPPort'  => $setting['smtp_port'],
                'SMTPUser'  => $setting['email'],
                'SMTPPass'  => $setting['password'],
                'SMTPCrypto'=> $setting['smtp_encryption'],
                'mailType'  => $setting['mail_content_type'],
                'charset'   => 'utf-8',
                'wordWrap'  => true,
            ];

            $subject = "Database Backup";
            $email = \Config\Services::email();
            $email->initialize($email_con);
            $email->setFrom($setting['email'], $company_title['title']);
            $email->setTo(trim($email_id));
            $email->setSubject($subject);
            $email->setMessage($message);
            $email->attach($path);
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
    }
    public function delete()
    {
        $path = FCPATH . 'public/database_backup/' . $_POST['file_name'] . '';

        if (unlink($path)) {
            return $this->response->setJSON([
                "error" => false,
                "message" => "Backup Deleted Successfully!",
                "data" => [],
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        } else {
            return $this->response->setJSON([
                "error" => true,
                "message" => "Something went wrong Please try again after some time.",
                "csrf_token" => csrf_token(),
                "csrf_hash" => csrf_hash()
            ]);
        }
        return redirect()->to('/admin/database');
    }
}
