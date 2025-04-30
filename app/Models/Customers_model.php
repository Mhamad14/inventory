<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_model extends Model
{

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'user_id', 'business_id', 'vendor_id', 'balance', 'created_by', 'status'];

    //Shahram: Added this line to get a customer full details
    public function getCustomerFullDetail($user_id)
    {
        $customer = $this->db->table('customers')
            ->select('customers.* ,users.first_name, users.email, users.mobile')
            ->join('users', 'customers.user_id = users.id')
            ->where('customers.user_id', $user_id)
            ->get()
            ->getRowArray();
        $customer['debt'] = $this->calculate_customer_debit($customer['id'],$customer['business_id']);
        return $customer;
    }

    public function count_of_customers($business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("customers");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $customers = $builder->get()->getResultArray();
        return $customers;
    }

    public function get_users($search_term = "", $business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("users as u");
        $builder->select('u.*,c.user_id,c.balance');
        $builder->where('c.business_id ',  $business_id);
        $builder->where('c.status', 1);
        $builder->join('customers as c', 'u.id = c.user_id ', "left");
        $multipleWhere = [];
        if (!empty($search_term)) {
            $multipleWhere = [
                'u.id' => $search_term,
                'u.`first_name`' => $search_term,
                'u.`mobile`' => $search_term,
                'u.`email`' => $search_term,
                'c.`balance`' => $search_term,

            ];
        }
        $builder->groupStart();
        $builder->orLike($multipleWhere);
        $builder->groupEnd();
        $users = $builder->get()->getResultArray();
        $data = array();
        foreach ($users as $user) {
            $data[] = array("id" => $user['id'], "text" => $user['first_name'], "number" => $user['mobile'], "email" => $user['email'], "balance" => $user['balance']);
        }
        $response['data'] = $data;
        return json_encode($response);
    }

    public function get_customer($user_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("customers");
        $builder->where('user_id ', $user_id);
        return $builder->get()->getResultArray();
    }
    //added this for knwing debit
    public function calculate_customer_debit($customer_id, $business_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders");

        $builder->select('SUM(final_total - amount_paid) as total_debit');
        $builder->where('customer_id', $customer_id); // Uses customers.id
        $builder->where('business_id', $business_id);
        $builder->groupStart()
            ->where('payment_status', 'unpaid')
            ->orWhere('payment_status', 'partially_paid')
            ->groupEnd();

        $result = $builder->get()->getRowArray();
        return $result['total_debit'] ?? 0;
    }
    //added this to get all the orders of a customer
    // $routes->get('(:any)/edit', 'admin\Customers::edit', ['action' => 'can_update']);
    public function get_customer_orders($user_id, $business_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->select('*');
        $builder->where('customer_id', $user_id);
        $builder->where('business_id', $business_id);
        $builder->where("(payment_status = 'unpaid' OR payment_status = 'partially_paid')");
        return $builder->get()->getResultArray();
    }
    public function get_customers_details($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("customers as c");
        $builder->select('c.*,u.first_name,u.email,u.mobile,u.last_name');
        $builder->where('business_id', $business_id);
        $builder->join('users as u', 'c.user_id = u.id', "left");

        $condition = [];
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
                '`c.balance`' => $search,
                '`u.first_name`' => $search,
                '`u.email`' => $search,
                '`u.mobile`' => $search,
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
        $customers = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();

        // Calculate debit for each customer
        foreach ($customers as &$customer) {
            $customer['debit'] = $this->calculate_customer_debit($customer['id'], $business_id);
        }

        return $customers;
    }
}
