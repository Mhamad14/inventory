<?php

namespace App\Models;

use CodeIgniter\Model;

class Businesses_model extends Model
{

    protected $table = 'businesses';
    protected $primaryKey = 'id';

    protected $allowedFields = ['user_id', 'name', 'icon', 'description', 'address', 'contact', 'tax_name', 'tax_value', 'bank_details', 'status','website','email' ];
    
    public function count_of_businesses($id="")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("businesses");
        $builder->select('COUNT(businesses.user_id) as `total`');
        $builder->where('user_id',  $id);
        $res = $builder->get()->getResultArray();
        return $res;
    }
    public function get_businesse_count($id)
    {
        $business_count = $this->table('businesses')->select('count(id) as total')->where(["user_id" => $id])->get()->getResultArray()[0]['total'];
        return $business_count;
    }

    public function get_businesses($id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("businesses");
        $builder->where('user_id',  $id);
        $condition  = [];

        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])){
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
                '`user_id`' => $search,
                '`name`' => $search,
                '`icon`' => $search,
                '`description`' => $search,
                '`address`' => $search,
                '`contact`' => $search,
                '`tax_name`' => $search,
                '`tax_value`' => $search,
                '`bank_details`' => $search,
                '`email`' => $search,
                '`website`' => $search,
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
        $db      = \Config\Database::connect();
        $builder = $db->table("businesses");
        $builder->where('user_id',  $id);
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
        $businesses = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $businesses;
    }

    public function get_business($business_id)
    {
        return $this->find($business_id);
    }
    
}
