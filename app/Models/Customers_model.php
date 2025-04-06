<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_model extends Model
{

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id','vendor_id' , 'balance','created_by', 'status'];

    public function count_of_customers($business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("customers");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $customers = $builder->get()->getResultArray();
        return $customers;
    }

    public function get_users($search_term = "", $business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('u.*,c.user_id,c.balance');
        $builder->where('c.business_id ',  $business_id);
        $builder->where('c.status', 1);
        $builder->join('customers as c', 'u.id = c.user_id ', "left");
        $multipleWhere = [];
        if (!empty($search_term)) {
            $multipleWhere = [
                'u.id' => $search_term,
                'u.`first_name`' => $search_term,
                'u.`mobile`' => $search_term,
                'u.`email`' => $search_term,
                'c.`balance`' => $search_term,

            ];
        }
        $builder->groupStart();
        $builder->orLike($multipleWhere);
        $builder->groupEnd();
        $users = $builder->get()->getResultArray();
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['first_name'], "number" => $user['mobile'], "email" => $user['email'],"balance" => $user['balance']);
        }
        $response['data'] = $data;
        return json_encode($response);
    }
    public function get_customer($user_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("customers");
        $builder->where('user_id ', $user_id);
        return $builder->get()->getResultArray();
    }
    function get_customers_details( $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("customers as c");
        $builder->select('c.*,u.first_name,u.email,u.mobile,u.last_name');
        $builder->where('business_id ', $business_id);
        $builder->join('users as u', 'c.user_id = u.id ', "left"); // added left here

        $condition = [];
        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`c.balance`' => $search,
                '`u.first_name`' => $search,
                '`u.email`' => $search,
                '`u.mobile`' => $search,
            ];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        if (isset($_GET['id']) && $_GET['id'] != '') {
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
        $customers = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $customers;
    }
    
}
