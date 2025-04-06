<?php

namespace App\Models;

use CodeIgniter\Model;

class Sales_summary_model extends Model
{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'payment_method', 'txn_id',   'amount',  'created_at'];

    public function count_of_sales_summary($where = [], $multipleWhere = [])
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = $_SESSION['user_id'];

        $db      = \Config\Database::connect();
        $builder = $db->table("orders o")
            ->join('users u', 'u.id = o.customer_id');
        $builder->where('o.business_id', $business_id);
        $builder->where('o.vendor_id' , $vendor_id);

        $builder->select('COUNT(o.id) as `total`');

        if (isset($_GET['payment_type_filter'])) {
            $payment_type_filter = $_GET['payment_type_filter'];
            if ($payment_type_filter == 'cash') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'card_payment') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'wallet') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'online_payment') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'net_banking') {
                $builder->where('payment_method', $payment_type_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }
         if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }

        if (isset($where) && $where != '') {
            $builder->where($where);
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        $sales_summary = $builder->get()->getResultArray();
        return $sales_summary;
    }

    public function get_sales_summary()
    {
        $db = \Config\Database::connect();
        $final_total_amount = $final_amount_paid = '';

        $builder =    $where = $multipleWhere = $rows =  [];
        $offset = $i = 0;
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";

        $builder = $db->table('orders o ')->select('u.id as users_id , o.id as order_id , o.total , u.first_name , o.amount_paid , o.payment_method , o.payment_status');
        $builder->where('o.business_id', $business_id);
        $builder->join('users u ', 'u.id = o.customer_id');

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 100;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? trim($_GET['sort']) : "o.id";
        $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? trim($_GET['order']) : "DESC";

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`u.first_name`' => $search,
                '`u.email`' => $search,
                '`amount_paid`' => $search,
                '`payment_method`' => $search,
                '`payment_status`' => $search,
                '`total`' => $search,
                '`o.id' => $search,
                '`u.id`' => $search,

            ];
        }
        if (isset($_GET['payment_type_filter'])) {
            $payment_type_filter = $_GET['payment_type_filter'];
            if ($payment_type_filter == 'cash') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'card_payment') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'wallet') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'online_payment') {
                $builder->where('payment_method', $payment_type_filter);
            } elseif ($payment_type_filter == 'net_banking') {
                $builder->where('payment_method', $payment_type_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }
         if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }

        if (isset($where) && $where != '') {
            $builder->where($where);
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($_GET['id']) && $_GET['id'] != '') {
            $where = ['id' => $_GET['id']];
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        $sales_summary = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)->getWhere()->getResultArray();
        $total = $this->count_of_sales_summary($where, $multipleWhere);



        $final_total_amount = array_sum(array_column($sales_summary, 'total'));
        $final_amount_paid = array_sum(array_column($sales_summary, 'amount_paid'));

        foreach ($sales_summary as $reports) {

            $rows[$i] = [
                'order_id' => $reports['order_id'],
                'users_id' => $reports['users_id'],
                'username' => $reports['first_name'],
                'business_id' => $business_id,
                'payment_method' => $reports['payment_method'],
                'payment_status' => $reports['payment_status'],
                'total' => currency_location(decimal_points($reports['total'])),
                'amount_paid' => currency_location(decimal_points($reports['amount_paid'])),

            ];

            $i++;

            if (!empty($reports)) {
            }
            $rows[$i] = [
                'order_id' => '<div class = "fw-bold">Total</div>',
                'users_id' => '-',
                'username' => '-',
                'business_id' => '-',
                'payment_method' => '-',
                'payment_status' => '-',
                'total' => '<span class = " badge bg-primary ">' . currency_location(decimal_points($final_total_amount)) . '</span>',
                'amount_paid' => '<span class = "badge bg-primary text-wrap"> ' . currency_location(decimal_points($final_amount_paid)) . '</span>',

            ];
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = $rows;

        return $array;
    }
}
