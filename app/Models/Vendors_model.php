<?php

namespace App\Models;

use CodeIgniter\Model;

class Vendors_model extends Model
{

    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'mobile', 'email', 'first_name', 'last_name', 'active', 'created_on'];
   

    public function count_of_vendors()
    {
        $group =  get_group('vendors');
        $group_id = $group[0]['group_id'];
        $db      = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('COUNT(u.id) as `total`');
        $builder->where('ug.group_id ',  $group_id);
        $builder->join('users_groups as ug', 'u.id = ug.user_id ', "left");
        return $builder->get()->getResultArray();
    }
    
    public function getVendorByUserId($id)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("users");
        $builder->select('*');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }

    public function get_vendors()
    {
        $group =  get_group('vendors');
        $group_id = $group[0]['group_id'];
        $db      = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('u.*,ug.group_id');
        $builder->where('ug.group_id ',  $group_id);
        $builder->join('users_groups as ug', 'u.id = ug.user_id ', "left");
        $condition  = [];

        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])){
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
        $builder->select(' COUNT(u.id) as `total`');
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
        $tts_count = $builder->get()->getResultArray();
        $total = $tts_count[0]['total'];
        /* Selecting actual data */
        $builder = $db->table("users as u");
        $builder->select('u.*,ug.group_id');
        $builder->where('ug.group_id ',  $group_id);
        $builder->join('users_groups as ug', 'u.id = ug.user_id ', "left");
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
        $vendor = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResult();
        return $vendor;
    }
    public function edit_profile($id)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("users");
        $builder->select('*');
        $builder->where('id',$id);
        return $builder->get()->getRow();
    }
}
