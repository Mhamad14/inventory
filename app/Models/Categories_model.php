<?php

namespace App\Models;

use CodeIgniter\Model;

class Categories_model extends Model
{

    protected $table = 'categories';
    protected $primaryKey = 'id';
    protected $allowedFields = ['parent_id', 'vendor_id', 'name', 'status', 'business_id'];


    public function count_of_categories($vendor_id = "", $business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("categories");

        $builder->select('COUNT(categories.id) as `total`')
            ->where('name', 'general')
            ->orWhere('business_id = ' . $business_id . ' AND vendor_id =' . $vendor_id);

        $categories = $builder->get()->getResultArray();
        return $categories;
    }

    public function get_categories($vendor_id = "", $business_id = "")
    {
        $business_id = (isset($business_id) && is_numeric($business_id)) ? trim($business_id) : "";

        $db      = \Config\Database::connect();
        $builder = $db->table("categories");
        $condtions = [
            'business_id' => $business_id,
            'vendor_id' => $vendor_id
        ];
        $condition  = [];

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
                '`id`' => $search,
                '`vendor_id`' => $search,
                '`parent_id`' => $search,
                '`name`' => $search,
                '`status`' => $search
            ];
        }
        $builder->select(' COUNT(id) as `total`');
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
        if (!empty($vendor_id) && isset($vendor_id)) {
            $db      = \Config\Database::connect();

            $builder = $db->table("categories")
                ->select('*')
                ->where('name', 'general')
                ->orWhere('business_id = ' . $business_id . ' ');
        } else {
            $db      = \Config\Database::connect();
            $builder = $db->table("categories");
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
        $categories = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $categories;
    }
}
