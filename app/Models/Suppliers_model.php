<?php

namespace App\Models;

use CodeIgniter\Model;

class Suppliers_model extends Model
{

    protected $table = 'suppliers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'vendor_id', 'balance', 'billing_address', 'shipping_address', 'credit_period', 'credit_limit', 'tax_name', 'tax_num', 'status'];


    public function edit_supplier($supplier_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('s.*,s.id as sup_id,u.id,u.first_name,u.email,u.mobile');
        $builder->where('s.user_id ', $supplier_id);
        $builder->join('users as u', 'u.id = s.user_id ', "left"); // added left here
        return $builder->get()->getResultArray();
    }
    public function get_suppliers($vendor_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('s.balance,s.status,s.id,s.user_id,u.id,u.first_name as name, u.email,u.mobile');
        $builder->where('s.vendor_id', $vendor_id);
        $builder->join('users as u', 'u.id = s.user_id', "left");

        // Search functionality
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                's.user_id' => $search,
                's.balance' => $search,
                's.id' => $search,
                'u.email' => $search,
                'u.first_name' => $search,
                'u.last_name' => $search,
                'u.mobile' => $search,
                's.billing_address' => $search,
                's.shipping_address' => $search,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        // Get total count of all records (without pagination)
        $totalRecords = $builder->countAllResults(false);

        // Apply sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'u.id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $builder->orderBy($sort, $order);

        // Apply pagination
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $builder->limit($limit, $offset);

        // Get paginated data
        $suppliers = $builder->get()->getResultArray();

        return ['total' => $totalRecords, 'data' => $suppliers];
    }


    public function search_suppliers($search_term = "", $vendor_id = "")
    {
        // Connect to the database
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('u.id, u.first_name, s.vendor_id, s.balance, s.status');
        $builder->join('users as u', 'u.id = s.user_id', 'left');
        $builder->where('s.status', 1); // Only active suppliers
        $builder->orderBy('u.first_name', 'ASC'); // Order by name
        $builder->limit(10);

        if (!empty($search_term)) {
            $builder->groupStart(); // Start grouping OR LIKEs
            $builder->orLike('u.id', $search_term);
            $builder->orLike('u.first_name', $search_term);
            $builder->orLike('s.balance', $search_term);
            $builder->groupEnd(); // End grouping
        }

        $users = $builder->get()->getResultArray();

        $data = [];
        foreach ($users as $user) {
            $data[] = [
                "id" => $user['id'],
                "text" => $user['first_name'],
                "balance" => $user['balance'],
                "status" => $user['status']
            ];
        }

        $response['data'] = $data;
        return json_encode($response);
    }

    public function get_supplier($user_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("suppliers as s");
        $builder->select('s.*, u.first_name, u.last_name, u.email, u.mobile');
        $builder->join('users as u', 'u.id = s.user_id', 'left');
        $builder->where('s.user_id', $user_id);
        return $builder->get()->getRowArray();
    }
}
