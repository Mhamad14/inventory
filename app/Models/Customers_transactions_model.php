<?php

namespace App\Models;

use CodeIgniter\Model;

class Customers_transactions_model extends Model
{

    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id','user_id' , 'business_id'  ,  'customer_id', 'supplier_id', 'order_id', 'transaction_type', 'order_type', 'created_by', 'payment_type', 'amount', 'opening_balance', 'closing_balance', 'message', 'transaction_id', 'payment_for'];

    public function     count_of_transactions($created_by = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("customers_transactions");
        $builder->select('COUNT(id) as `total`');
        $builder->where('created_by ', $created_by);
        $transactions = $builder->get()->getResultArray();
        return $transactions;
    }
    /**
     * Fetches transactions from the 'customers_transactions' table, 
     * incorporating the updated relationships and table structure.
     *
     * @param int $created_by The vendor ID to filter transactions.
     * @param array $params Parameters for search, sorting, and pagination.
     *
     * @return array An array containing the total count and rows of transactions.
     */
    public function get_transactions($business_id, $params = [])
    {
        $db = \Config\Database::connect();
        $builder = $db->table('customers_transactions');

        // Select fields from 'customers_transactions' and related tables
        $builder->select('
        customers_transactions.*,
        users.first_name AS user_first_name,
        users.last_name AS user_last_name,
        businesses.name AS business_name
    ');


        // Join with 'users' table to fetch user details based on 'user_id'
        $builder->join('users', 'customers_transactions.user_id = users.id', 'left');

        // Join with 'customers' table to fetch customer details based on 'customer_id'
        $builder->join('customers', 'customers_transactions.customer_id = customers.id', 'left');

        // Join with 'suppliers' table to fetch supplier details based on 'supplier_id'
        $builder->join('suppliers', 'customers_transactions.supplier_id = suppliers.id', 'left');

        // Join with 'businesses' table to fetch business details based on 'business_id'
        $builder->join('businesses', 'customers_transactions.business_id = businesses.id', 'left');

        // Filter by vendor (created_by) and join related tables
        $builder->where('customers_transactions.business_id', $business_id);
        // Handle search functionality
        if (!empty($params['search'])) {
            $search = $params['search'];
            $builder->groupStart()
                ->orLike('customers_transactions.id', $search)
                ->orLike('customers_transactions.amount', $search)
                ->orLike('customers_transactions.opening_balance', $search)
                ->orLike('customers_transactions.closing_balance', $search)
                ->orLike('customers_transactions.transaction_type', $search)
                ->orLike('customers_transactions.payment_for', $search)
                ->orLike('customers_transactions.payment_type', $search)
                ->orLike('customers_transactions.order_id', $search)
                ->orLike('customers_transactions.transaction_id', $search)
                ->orLike('users.first_name', $search)
                ->orLike('users.last_name', $search)
                ->orLike('businesses.name', $search) // Search in business name
                ->groupEnd();
        }

        // Apply sorting
        $sort = $params['sort'] ?? 'customers_transactions.id';
        $order = $params['order'] ?? 'ASC';
        $builder->orderBy($sort, $order);

        // Apply pagination
        $limit = $params['limit'] ?? 10;
        $offset = $params['offset'] ?? 0;

        $builder->limit($limit, $offset);

        // Execute query and fetch results
        $transactions = $builder->get()->getResultArray();

        return [
            'total' =>count($transactions),
            'rows' => $transactions,
        ];
    }
}
