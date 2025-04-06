<?php

namespace App\Models;

use CodeIgniter\Model;

class Expenses_Type_model extends Model
{

    protected $table = 'expenses_type';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'vendor_id', 'title', 'description', 'expenses_type_date'];


    public function count_of_expenses_type()
    {   
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";

        $db      = \Config\Database::connect();
        $builder = $db->table("expenses_type");
        $builder->select('COUNT(id) as `total`');
        
        $expenses_type = $builder->get()->getResultArray();
        return $expenses_type;
    }

    public function get_expenses_type($vendor_id = "")
    {   
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

       $db      = \Config\Database::connect();
        $builder = $db->table("expenses_type as et");
        $builder ->where('vendor_id' , $vendor_id);
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
                '`et.id`' => $search,
                '`et.vendor_id`' => $search,
                '`et.title`' => $search,
                '`et.description`' => $search,
                '`et.expenses_type_date`' => $search,
                
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
            $builder = $db->table("expenses_type et")
                ->select('*')
                ->whereIn('vendor_id', [0, $vendor_id]);
        } else {
            $db      = \Config\Database::connect();
            $builder = $db->table("expenses_type et");
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
        $expenses_type = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $expenses_type;

      
    }
}