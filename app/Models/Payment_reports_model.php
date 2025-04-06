<?php

namespace App\Models;

use CodeIgniter\Model;

class Payment_reports_model extends Model
{

    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'customer_id', 'business_id', 'created_by', 'total', 'delivery_charges', 'discount', 'final_total', 'payment_status', 'amount_paid', 'order_type', 'message', 'payment_method'];

    public function get_payment_reports($business_id = "")
    {
        $vendor_id = $this->ionAuth->getUserId();


        $db      = \Config\Database::connect();
        $builder = $db->table("customers_transactions");
        $builder->where("business_id", $business_id);
        $builder->where('vendor_id', $vendor_id);

        if (isset($_GET['payment_type_filter'])) {
            $payment_type_filter = $_GET['payment_type_filter'];
            if ($payment_type_filter == 'cash') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'card_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'wallet') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'online_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'net_banking') {
                $builder->where('payment_type', $payment_type_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }

        $payment_reports = $builder->getWhere()->getResultArray();
        return $payment_reports;
    }

    public function count_of_payment_reports($where = [], $multipleWhere = [])
    {
        $vendor_id = $_SESSION['user_id'];

        $db      = \Config\Database::connect();
        $builder = $db->table("customers_transactions ct")
            ->join('users u', 'u.id = ct.customer_id')
            ->where('ct.vendor_id', $vendor_id);

        $builder->select('COUNT(ct.id) as `total`');

        if (isset($_GET['payment_type_filter'])) {
            $payment_type_filter = $_GET['payment_type_filter'];
            if ($payment_type_filter == 'cash') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'card_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'wallet') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'online_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'net_banking') {
                $builder->where('payment_type', $payment_type_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }


        if (isset($where) && $where != '') {
            $builder->where($where);
        }

        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        $payment_reports = $builder->get()->getResultArray();
        return $payment_reports;
    }

    public function get_payment_reports_list()
    {

        $vendor_id = $_SESSION['user_id'];

    
        $db = \Config\Database::connect();
        $builder = $db->table("customers_transactions as ct")
            ->select('ct.*, u.id as  user_id, u.username,u.email , u.first_name,u.last_name')
            ->join('users u', 'u.id = ct.customer_id')
            ->where('ct.vendor_id' , $vendor_id);

        $where = $multipleWhere = $rows = [];
        $offset = $i = 0;
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 100;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? trim($_GET['sort']) : "id";
        $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? trim($_GET['order']) : "DESC";

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`ct.customer_id`' => $search,
                '`u.username`' => $search,
                '`u.email`' => $search,
                '`u.first_name`' => $search,
                '`u.last_name`' => $search,
                '`ct.amount`' => $search,
                '`payment_type`' => $search,
                '`created_at`' => $search
            ];
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }

        if (isset($_GET['payment_type_filter'])) {
            $payment_type_filter = $_GET['payment_type_filter'];
            if ($payment_type_filter == 'cash') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'card_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'wallet') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'bar_code') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'online_payment') {
                $builder->where('payment_type', $payment_type_filter);
            } elseif ($payment_type_filter == 'net_banking') {
                $builder->where('payment_type', $payment_type_filter);
            }
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

        $payment_reports = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        $total = $this->count_of_payment_reports($where, $multipleWhere);

        foreach ($payment_reports as $reports) {
            $payment_report_id = $reports['id'];

            $rows[$i] = [
                'id' => $reports['id'],
                'customer_id' => ($reports['customer_id']),
                'vendor_id' => $reports['vendor_id'],
                'username' => $reports['username'],
                'business_id' => $business_id,
                'payment_type' => str_replace("_"," ", $reports['payment_type']),
                'amount' => currency_location(decimal_points($reports['amount'])),
                'email' => $reports['email'],
                'created_at' => date_formats(strtotime($reports['created_at'])),
                'name' => ucfirst($reports['first_name'] . $reports['last_name']),
            ];
            $i++;
        }
        $array['total'] = $total[0]['total'];
        $array['rows'] = $rows;
        return $array;
    }
}
