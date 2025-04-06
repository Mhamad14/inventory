<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;


class Migrate extends BaseController
{
    public $migrate;
    public $ionAuth;
    public $db;

    public function __construct()
    {
        $this->migrate =  \Config\Services::migrations();
        $this->ionAuth = new \App\Libraries\IonAuth();
        $this->db = db_connect();
    }

    public function index()
    {
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            if (!$this->is_dir_empty(FCPATH . "\\app\\Database\\Migrations")) {
                try {
                    echo "<pre>";
                    print_r($this->migrate->findMigrations());
                    if($this->migrate->latest()){
                        echo "<h1>Migrated</h1>";
                    }
                    $this->migrate->latest();
                } catch (\Throwable $e) {
                    print_r($e->getMessage());
                    echo $e->getMessage();
                }
                
            }
        } else {
            echo "Unauthorized to access this part";
        }
    }

    public function rollback(int $version = 0)
    {
        if ($this->ionAuth->loggedIn() && $this->ionAuth->isAdmin()) {
            if (!$this->is_dir_empty(FCPATH . "\\app\\Database\\Migrations")) {
                try {
                    if (!empty($version) && is_numeric($version)) {
                        $this->migrate->regress($version);
                    } else {
                        echo "Version not specified";
                    }
                } catch (\Throwable $e) {
                    echo $e->getMessage();
                }
            }
        } else {
            echo "Unauthorized to access this part";
        }
    }

    public function is_dir_empty($dir)
    {
        if (!is_readable($dir)) return NULL;
        return (count(scandir($dir)) == 2);
    }
}
