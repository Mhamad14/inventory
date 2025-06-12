<?php

namespace App\Models;

use CodeIgniter\Model;

class Currency_model extends Model
{
    protected $table = 'currencies';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'business_id', 
        'code', 
        'name', 
        'symbol', 
        'symbol_position', 
        'decimal_places', 
        'is_base', 
        'status',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function count_of_currencies()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $builder->where('deleted_at IS NULL');
        
        return $builder->get()->getResultArray();
    }

    public function get_currencies($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);
        
        $condition = [];
        $where = ['business_id' => $business_id];
        
        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "id";
        if (isset($_GET['sort'])) {
            $sort = $_GET['sort'];
        }

        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }

        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $builder->groupStart();
            $builder->orLike('id', $search);
            $builder->orLike('code', $search);
            $builder->orLike('name', $search);
            $builder->orLike('symbol', $search);
            $builder->groupEnd();
        }

        $builder->select('*');
        
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $builder->where($condition);
        }
        
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        
        $builder->where('deleted_at IS NULL');
        
        return $builder->orderBy($sort, $order)
                      ->limit($limit, $offset)
                      ->get()
                      ->getResultArray();
    }
    
    public function get_base_currency($business_id)
    {
        return $this->where('business_id', $business_id)
                   ->where('is_base', 1)
                   ->where('deleted_at IS NULL')
                   ->first();
    }
}