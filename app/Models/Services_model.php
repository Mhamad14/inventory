<?php

namespace App\Models;

use CodeIgniter\Model;

class Services_model extends Model
{

    protected $table = 'services';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id','tax_ids','name', 'unit_id', 'description','image','price','cost_price','is_tax_included','is_recursive','recurring_days','recurring_price','status'];

    public function count_of_services($business_id= "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("services");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id' , $business_id);
        $products = $builder->get()->getResultArray();
        return $products;
    }
    public function get_services($business_id)
    {
          $db = \Config\Database::connect();
        $builder = $db->table("services as s");
        $builder->select('s.*,s.name as service_name, b.name as business_name ,u.name');
        $builder->join('businesses as b', 'b.id=s.business_id', 'left');
        $builder->join('units as u', 'u.id=s.unit_id', 'left');
        $builder->where('s.business_id',$business_id);
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
                's.id' => $search,
                's.name' => $search,
                's.description' => $search,
                's.price' => $search,
                's.recurring_days' => $search,
                's.recurring_price' => $search,
                's.is_recursive' => $search,
                's.vendor_id' => $search,
                'b.name' => $search,
                'u.name' => $search,
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
        $services = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $services;
    }
}