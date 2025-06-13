<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_model extends Model
{

    protected $table = 'customers';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'user_id', 'business_id', 'vendor_id', 'balance', 'created_by', 'status'];

    public function payBackPartialDebt($payment_amount){
        
        $customer_id = session('current_customer_id');
        $business_id = session('business_id');

        $this->db->query("CALL sp_PartialCustomerPayment(?, ?, ?)", [$payment_amount, $customer_id, $business_id]);

        return $this->db->affectedRows() > 0;

    }
    public function payBackAllDebt( $business_id)
    {

        $customer_id = session('current_customer_id');

        $builder = $this->db->table('orders');
        $builder->set('amount_paid', 'final_total', false)
            ->where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->whereIn('payment_status', ['unpaid', 'partially_paid']);

        $builder->update(['payment_status' => 'fully_paid']);

        // log_message('debug', 'User ID is: ' . $customer->id);

        return $this->db->affectedRows() > 0;
    }

    public function getOverallPayments($customer_id, $business_id)
    {
        $result = $this->db->table('orders')
            ->select('customer_id, SUM(total) as sub_total, SUM(discount) as discount
            , SUM(delivery_charges) as delivery_charges, SUM(final_total) as final_total,
            SUM(amount_paid) as amount_paid,
            SUM(returns_total) as returns_total')
            ->where('customer_id', $customer_id)
            ->where('business_id', $business_id)
            ->get()
            ->getRowArray();

        $result['debt'] = $this->calculate_customer_debit($result['customer_id'], $business_id);
        return $result;
    }
    
    public function getCustomerFullDetail($user_id)
    {
        $customer = $this->db->table('customers')
            ->select('customers.* ,users.first_name, users.email, users.mobile')
            ->join('users', 'customers.user_id = users.id')
            ->where('customers.user_id', $user_id)
            ->get()
            ->getRowArray();
        $customer['debt'] = $this->calculate_customer_debit($customer['id'], $customer['business_id']);

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

        $builder->select('((SUM(final_total) - SUM(amount_paid)) ) as total_debit');
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
    public function getTotalCustomerOrders($request, $business_id = "", $customer_id = "",)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->where('business_id', $business_id);
        $builder->where('customer_id', $customer_id);

        $filters = [
            'search' => $request->getGet('search'),
            'start_date' => $request->getGet('start_date'),
            'end_date' => $request->getGet('end_date'),
            'payment_status_filter' => $request->getGet('payment_status_filter'),
            'customer_orders_type_filter' => $request->getGet('customer_orders_type_filter'),
        ];

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->orLike('payment_status', $search)
                ->orLike('discount', $search)
                ->orLike('amount_paid', $search)
                ->orLike('delivery_charges', $search)
                ->orLike('message', $search)
                ->orLike('created_at', $search)
                ->groupEnd();
        }

        // Date range
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->where('created_at >=', $filters['start_date'] . ' 00:00:00');
            $builder->where('created_at <=', $filters['end_date'] . ' 23:59:59');
        }

        // Payment status filter
        if (!empty($filters['payment_status_filter'])) {
            $builder->where('payment_status', $filters['payment_status_filter']);
        }

        // Order type filter
        if (!empty($filters['customer_orders_type_filter'])) {
            $builder->where('order_type', $filters['customer_orders_type_filter']);
        }

        // Return the total count
        return $builder->countAllResults();
    }
    public function calculateOrderDebt($order_id)
    {
        $result = $this->db->table('orders')
            ->select('(final_total - amount_paid) as debt')
            ->where('id', $order_id)
            ->whereIn('payment_status', ['partially_paid', 'unpaid'])
            ->get()
            ->getRowArray();


        return $result['debt'] ?? 0;
    }

    public function getCustomersOrderDetails($request, $business_id = "", $customer_id = "",)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->select('orders.*');
        $builder->where('orders.business_id', $business_id);
        $builder->where('orders.customer_id', $customer_id);

        $filters = [
            'search' => $request->getGet('search'),
            'start_date' => $request->getGet('start_date'),
            'end_date' => $request->getGet('end_date'),
            'payment_status_filter' => $request->getGet('payment_status_filter'),
            'customer_orders_type_filter' => $request->getGet('customer_orders_type_filter'),
        ];

        $limit = $request->getGet('limit') ?? 10;
        $offset = $request->getGet('offset') ?? 0;
        $sort = $request->getGet('sort') ?? 'id';
        $order = $request->getGet('order') ?? 'DESC';

        // Search
        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $builder->groupStart()
                ->orLike('payment_status', $search)
                ->orLike('discount', $search)
                ->orLike('amount_paid', $search)
                ->orLike('created_at', $search)
                ->orLike('id', $search)
                ->groupEnd();
        }

        // Date range
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $builder->where('created_at >=', $filters['start_date'] . ' 00:00:00');
            $builder->where('created_at <=', $filters['end_date'] . ' 23:59:59');
        }

        // Payment status filter
        if (!empty($filters['payment_status_filter'])) {
            $builder->where('payment_status', $filters['payment_status_filter']);
        }

        // Order type filter
        if (!empty($filters['customer_orders_type_filter'])) {
            $builder->where('order_type', $filters['customer_orders_type_filter']);
        }

        // Final query
        $customerOrders = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        // Add debt calculation for each order
        foreach ($customerOrders as &$customerOrder) { // Note the & for reference
            $customerOrder['debt'] = $this->calculateOrderDebt($customerOrder['id']);
        }

        return $customerOrders;
    }

    public function get_customers_details($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("customers as c");

        $builder->select('c.*, u.first_name, u.email, u.mobile, u.last_name');
        $builder->join('users as u', 'c.user_id = u.id', 'left');
        $builder->where('business_id', $business_id);

        // Handle pagination
        $offset = $_GET['offset'] ?? 0;
        $limit  = $_GET['limit']  ?? 10;

        // Handle sorting
        $sort  = $_GET['sort']  ?? 'id';
        $order = $_GET['order'] ?? 'ASC';

        // Handle search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];

            $builder->groupStart()
                ->orLike('c.balance', $search)
                ->orLike('u.first_name', $search)
                ->orLike('u.email', $search)
                ->orLike('u.mobile', $search)
                ->groupEnd();
        }

        // Apply additional where filter by ID if needed
        if (!empty($_GET['id'])) {
            $builder->where('c.id', $_GET['id']);
        }

        // Finalize query
        $customers = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)
            ->get()
            ->getResultArray();

        // Post-processing: calculate debit for each customer
        foreach ($customers as &$customer) {
            $customer['debit'] = $this->calculate_customer_debit($customer['id'], $business_id);
        }

        return $customers;
    }
}
