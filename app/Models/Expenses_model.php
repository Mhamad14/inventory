<?php

namespace App\Models;

use CodeIgniter\Model;

class Expenses_model extends Model
{

    protected $table = 'expenses';
    protected $primaryKey = 'id';
    protected $allowedFields = ['expenses_id', 'business_id', 'vendor_id', 'expenses_type', 'title',  'note', 'amount',  'expenses_date'];

    public function count_of_expenses($where = [], $multipleWhere = [])
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        $db      = \Config\Database::connect();
        $builder = $db->table("expenses e");
        $builder->select('COUNT(e.id) as `total`');
        $builder->where('e.business_id', $business_id);


        $expenses_type = $builder->get()->getResultArray();
        return $expenses_type;
    }

    public function get_expenses($vendor_id = "")
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $db      = \Config\Database::connect();
        $builder = $db->table("expenses e");

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
                '`e.id`' => $search,
                '`e.vendor_id`' => $search,
                '`e.expenses_id`' => $search,
                '`e.note`' => $search,
                '`e.expenses_date`' => $search,
                '`e.amount`' => $search,
                '`e.business_id`' => $search,
            ];
        }
        $builder->select(' COUNT(e.id) as `total`')->join('expenses_type et', 'et.id = e.expenses_id');
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
            $builder = $db->table("expenses e")
                ->select('e.*, et.id as  expenses_type_id, et.title')
                ->join('expenses_type et', 'et.id = e.expenses_id')
                ->whereIn('e.vendor_id', [0, $vendor_id]);
            $builder->where('e.business_id', $business_id);
        } else {
            $db      = \Config\Database::connect();
            $builder = $db->table("expenses");
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
        $expenses = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        $total = $this->count_of_expenses();
        return $expenses;
    }
}
