<?php

namespace App\Models;

use CodeIgniter\Model;

class Profit_loss_model extends Model
{
    protected $table = 'customers_transactions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'business_id', 'vendor_id', 'payment_method', 'txn_id',   'amount',  'created_at'];


    public function get_profit_loss($vendor_id)
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";


        $where = $multipleWhere = $rows = $date_customer =  [];
        $offset = $i = 0;

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 100;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? trim($_GET['sort']) : "id";
        $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? trim($_GET['order']) : "DESC";

        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $date_customer = '((ct.created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (ct.created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }

        if (isset($_GET['id']) && $_GET['id'] != '') {
            $where = ['id' => $_GET['id']];
        }
        $settings = get_settings('general', true);

        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $currency = (isset($settings['currency_symbol'])) ? $settings['currency_symbol'] : '₹';

        $db      = \Config\Database::connect();
        $data['purchase_total']  = $db->table("purchases p ")->select('SUM(p.total) as purchase_total')->where('p.vendor_id', $vendor_id)->where('p.business_id', $business_id)->where($where)->get()->getResultArray()[0]['purchase_total'];

        $data['amount_collected']  = $db->table("orders o")->select('SUM(o.amount_paid) as sales_total')->where('o.vendor_id', $vendor_id)->where('o.business_id', $business_id)->where($where)->get()->getResultArray()[0]['sales_total'];

        $data['expenses_total']  = $db->table("expenses e")->select('SUM(e.amount) as expenses_total')->where('e.vendor_id', $vendor_id)->where('e.business_id', $business_id)->where($where)->get()->getResultArray()[0]['expenses_total'];

        $data['sales_total']  = $db->table("orders o")->select('SUM(o.  total) as sales_total')->where('o.vendor_id', $vendor_id)->where('o.business_id', $business_id)->where($where)->get()->getResultArray()[0]['sales_total'];

        $data['outstanding_total']  = $data['sales_total'] - $data['amount_collected'];

        $data['final_total'] = $data['sales_total'] - ($data['purchase_total'] + $data['expenses_total'] + $data['outstanding_total']);

        $rows = [];
        $i = '';
        $total = '1';

        if (decimal_points($data['final_total']) < 0) {
            $final_total = '<span class = " text fw-bolder text-danger">' . decimal_points($data['final_total']) . '</span>';
        } else {
            $final_total = '<span class = "fw-bolder text text-success">' . decimal_points($data['final_total']) . '</span>';
        }
        $i = 0;

        $rows[$i] = [
            'purchases' => currency_location (decimal_points($data['purchase_total'])),
            'sales' =>currency_location (decimal_points($data['sales_total'])),
            'expenses' => currency_location (decimal_points($data['expenses_total'])),
            'total' => currency_location ( ($final_total)),
            'vendor_id' => $vendor_id,
            'business_id' => $business_id,
            'outstanding_total' => currency_location ( decimal_points($data['outstanding_total'])),
            'amount_collected' =>currency_location (decimal_points($data['amount_collected'])),
        ];
        $i++;

        $array['rows'] = $rows;
        return $array;
    }
}
