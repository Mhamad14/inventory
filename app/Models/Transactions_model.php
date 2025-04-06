<?php

namespace App\Models;

use CodeIgniter\Model;

class Transactions_model extends Model
{

    protected $table = 'transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'amount', 'txn_id', 'payment_method', 'status', 'message'];

    public function count_of_transactions()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("transactions");
        $builder->select('COUNT(transactions.id) as `total`');
        $transactions = $builder->get()->getResultArray();
        return $transactions;
    }

    public function get_transactions($user_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("transactions as t");
        $builder->select('t.*,u.first_name,u.last_name ,u.email,u.mobile,up.package_id,p.title');
        $builder->join('users as u', 't.user_id = u.id ', "left");
        $builder->join('users_packages as up', 't.id = up.transaction_id', "left");
        $builder->join('packages as p', 'up.package_id = p.id', "left");

        if ($user_id != "" || !empty($user_id)) {
            $db      = \Config\Database::connect();
            $builder = $db->table("transactions as t");
            $builder->select('t.*,u.first_name,u.last_name ,u.email,u.mobile');
            $builder->where('t.user_id', $user_id);
            $builder->join('users as u', 't.user_id = u.id ', "left");
        }

        $condition = [];
        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 10;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = "t.id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 't.id') {
                $sort = "t.id";
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
                "u.first_name" => $search,
                "u.last_name" => $search,
                "u.email" => $search,
                "u.phone" => $search,
                "t.id" => $search,
                "t.payment_method" => $search,
                "t.txn_id" => $search,
                "t.amount" => $search,
                "t.status" => $search,
                "t.created_at" => $search,
            ];
        }

        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $builder->where('((t.created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (t.created_at <= "' . $_GET['end_date'] . ' 12:00:00"))');
        }
        if (isset($_GET['transaction_status'])) {
            $transaction_status = $_GET['transaction_status'];
            if ($transaction_status == 'success') {
                $builder->where('((t.status = "success") OR (t.status = "successful") OR (t.status = "authorized") OR (t.status = "captured"))');
            } elseif ($transaction_status == 'failed') {
                $builder->where('t.status', $transaction_status);
            } elseif ($transaction_status == 'pending') {
                $builder->where('t.status', $transaction_status);
            }
        }

        if (isset($_GET['txn_provider'])) {
            $txn_provider = $_GET['txn_provider'];
            if ($txn_provider == 'razorpay') {
                $builder->where('t.payment_method', $txn_provider);
            } elseif ($txn_provider == 'Stripe') {
                $builder->where('t.payment_method', $txn_provider);
            } elseif ($txn_provider == 'flutterwave') {
                $builder->where('t.payment_method', $txn_provider);
            }
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

        $transactions = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        return $transactions;
    }
}
