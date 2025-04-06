<?php

namespace App\Models;

use CodeIgniter\Model;

class Units_model extends Model
{

    protected $table = 'units';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'parent_id', 'name', 'symbol', 'conversion'];

    public function count_of_units($vendor_id="")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("units");
        $builder->select('COUNT(units.id) as `total`');
        $builder->whereIn('vendor_id', [0, $vendor_id]);

        $units = $builder->get()->getResultArray();
        return $units;
    }
    // for all vendor+admin units
    public function get_units_for_forms($vendor_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("units")
            ->select('*')
            ->whereIn('vendor_id', [0, $vendor_id]);
        return $builder->get()->getResultArray();
    }
    public function unit_name($unit_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("units")
            ->select('name')
            ->whereIn('id', [$unit_id]);
        return $builder->get()->getResultArray();
    }
    // for pagination 
    public function get_units($vendor_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("units");

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
                '`symbol`' => $search,
                '`conversion`' => $search
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
            $builder = $db->table("units")
                ->select('*')
                ->whereIn('vendor_id', [0, $vendor_id]);
        } else {
            $db      = \Config\Database::connect();
            $builder = $db->table("units");
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
        $units = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $units;
    }
}
