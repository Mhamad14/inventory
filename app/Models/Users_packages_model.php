<?php

namespace App\Models;

use CodeIgniter\Model;

class Users_packages_model extends Model
{

    protected $table = 'users_packages';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'package_id', 'package_name', 'no_of_businesses', 'no_of_delivery_boys', 'no_of_products', 'no_of_customers', 'tenure', 'price', 'months', 'start_date', 'end_date', 'status'];

    public function get_package($id)
    {
        $db = \Config\Database::connect();
        $builder = $this->db->table("users_packages as up");
        $builder->where('up.user_id', $id);
        return $builder->get()->getResultArray();
    }
    public function get_users_packages($id = "")
    {
        $multipleWhere = '';
        $condition  = [];
        $db = \Config\Database::connect();

        $builder = $this->db->table("users_packages as up");
        $builder->select('up.*,u.first_name,u.last_name');
        $builder->whereIn('up.user_id ', $id);
        $builder->join('users as u', 'u.id = up.user_id ', "left"); // added left here

        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "up.user_id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "up.user_id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }

        date_default_timezone_set('Asia/Kolkata');
        $date = date('Y-m-d H:i:s');

        if (isset($_GET['subscription_type'])) {
            $subscription_type = $_GET['subscription_type'];
            if ($subscription_type == 'active') {
                $status = ['up.status' => 1, 'up.start_date <=' => $date, 'up.end_date >=' => $date];
                $builder->where('up.status', $status);
                $builder->where($status);
            } elseif ($subscription_type == 'upcoming') {
                $status = ['up.status' => 1, 'up.start_date >' => $date];
                $builder->where('up.status', $status);
                $builder->where($status);
            } elseif ($subscription_type == 'expired') {
                $status = ['up.status' => 0];
                $builder->where('up.status', $status);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '' && isset($_GET['date_filter_by']) && $_GET['date_filter_by'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
            $date_filter_by = $_GET['date_filter_by'];
            $array = [];
            if ($date_filter_by == "starts_from") {
                $array = ['up.start_date >=' => $start_date, 'up.start_date <=' => $end_date];
            }

            if ($date_filter_by == "expires_on") {
                $array = ['up.end_date >=' => $start_date, 'up.end_date <=' => $end_date];
            }
         
        }
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'up.`user_id`' => $search,
                'u.`first_name`' => $search,
                'u.`last_name`' => $search,
                'up.`package_name`' => $search,
                'up.`no_of_businesses`' => $search,
                'up.`no_of_delivery_boys`' => $search,
                'up.`no_of_products`' => $search,
                'up.`no_of_customers`' => $search,
                'up.`tenure`' => $search,
                'up.`price`' => $search,
                'up.`months`' => $search,
                'up.`start_date`' => $search,
                'up.`end_date`' => $search,
            ];
        }
        $builder->select(' COUNT(up.user_id) as `total`');
        if (isset($_GET['user_id']) && $_GET['user_id'] != '') {
            $builder->where($condition);
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
       
        /* Selecting actual data */
        $builder = $this->db->table("users_packages as up");
        $builder->select('up.*,u.first_name,u.last_name');
        $builder->whereIn('up.user_id ', $id);
        $builder->join('users as u', 'u.id = up.user_id ', "left"); // added left here
        if (isset($_GET['user_id']) && $_GET['user_id'] != '') {
            $builder->where($condition);
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($array) && !empty($array)) {
            $builder->groupStart();
            $builder->where($array);
            $builder->groupEnd();
        }
        if (isset($status) && !empty($status)) {
            $builder->where($status);
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        $vendor = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        return $vendor;
    }
}
