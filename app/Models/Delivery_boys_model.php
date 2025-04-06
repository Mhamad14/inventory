<?php

namespace App\Models;

use CodeIgniter\Model;

class Delivery_boys_model extends Model
{

    protected $table = 'delivery_boys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id','vendor_id', 'status', 'permissions'];


    public function save_permissions($data, $user_id, $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $this->db->table("delivery_boys");
        $builder->where(['user_id' => $user_id, 'business_id' => $business_id]);
        $builder->update($data);
    }
    public function update_permissions($data, $user_id, $business_id = "")
    {

        $db = \Config\Database::connect();
        $builder = $this->db->table("delivery_boys");
        $builder->set('permissions', $data['permissions']);
        $builder->where(['user_id' => $user_id, 'business_id' => $business_id]);
        $builder->update($data);
    }
    public function assigned_businesses($user_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("delivery_boys");
        $builder->select('business_id');
        $builder->where('user_id', $user_id);
        $business_ids = $builder->get()->getResultArray();
        return $business_ids;
    }

    public function count_of_delivery_boys($business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("delivery_boys");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $deliveryboys = $builder->get()->getResultArray();
        return $deliveryboys;
    }
    public function delivery_boys($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("delivery_boys as db");
        $builder->select('db.*,u.first_name,u.email,u.mobile,u.last_name,u.username');
        $builder->where('business_id ', $business_id);
        $builder->join('users as u', 'db.user_id = u.id ', "left");
        return $builder->get()->getResultArray();
    }
    public function get_delivery_boys($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("delivery_boys as db");
        $builder->select('db.*,u.first_name,u.email,u.mobile,u.last_name,u.username');
        $builder->where('business_id ', $business_id);
        $builder->join('users as u', 'db.user_id = u.id ', "left"); // added left here

        $condition  = [];

        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "u.id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'id') {
                $sort = "u.id";
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
                'u.`id`' => $search,
                'u.`first_name`' => $search,
                'u.`last_name`' => $search,
                'u.`email`' => $search,
                'u.`mobile`' => $search,
                'u.`active`' => $search,
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
        $deliveryboys = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $deliveryboys;
    }
}
