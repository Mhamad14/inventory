<?php

namespace App\Models;

use CodeIgniter\Model;
use PhpParser\Builder\Function_;

class Purchases_model extends Model
{

    protected $table = 'purchases';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id', 'supplier_id', 'warehouse_id', 'order_no', 'order_type', 'purchase_date', 'tax_ids',  'delivery_charges', 'total', 'message', 'discount', 'payment_method', 'payment_status', 'amount_paid', 'status'];


    public function getPurchase($purchase_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("purchases as p");
        $builder->select('p.id, p.supplier_id,p.warehouse_id, p.status, p.delivery_charges, p.total, p.payment_status, p.amount_paid, p.message,
        p.discount,p.purchase_date, p.created_at, u.first_name as creator, 
        (select first_name from users where users.id = p.supplier_id) as supplier_name,
        (select name from warehouses where id = p.warehouse_id) as warehouse');
        $builder->join('users as u', 'u.id = p.vendor_id');
        $builder->where(['p.id' => $purchase_id]);
        $result = $builder->get()->getRowArray();
        return $result;
    }
    public function get_purchase_total($purchase_id)
    {
        return $this->db->table('purchases')
            ->select('total')
            ->where('id', $purchase_id)
            ->get()
            ->getRow()
            ->total ?? 0;
    }

    public function get_purchases($vendor_id, $business_id, $order_type)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("purchases as p");
        $builder->select('p.*, u.first_name, u.last_name');
        $builder->join('users as u', 'u.id = p.supplier_id');
        $builder->join('status as s', 's.id = p.status');
        $builder->where(['p.order_type' => $order_type, 'p.business_id' => $business_id]);
        $total = $this->total_purchases($business_id);
        // Handle limit and offset


        $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 10; // Default limit to 10
        $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0; // Default offset to 0

        // Handle sorting
        $sort_column = [
            "purchase_status" => "s.status",
            "supplier_name" => "u.first_name",
            "id" => "p.id",
            "purchase_date" => "p.purchase_date",
            "amount_paid" => "p.amount_paid",
            "status" => "p.status"
        ];

        $sort = $sort_column[$_GET['sort'] ?? 'id'] ?? 'p.id';
        $order = $_GET['order'] ?? 'DESC';

        // Handle search
        if (!empty($_GET['search'])) {
            $search = $_GET['search'];
            $multipleWhere = [
                'p.id' => $search,
                'p.status' => $search,
                'p.order_no' => $search,
                'p.purchase_date' => $search,
                'p.payment_status' => $search,
                'p.total' => $search,
                'u.first_name' => $search,
                'u.last_name' => $search,
                's.status' => $search,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        // Apply sorting, limit, and offset
        $builder->orderBy($sort, $order);
        $builder->limit($limit, $offset);


        // Get the results
        $purchases = $builder->get()->getResultArray();
        $array['total'] = $total;
        $array['rows'] = $purchases;
        return $array;
    }

    public function get_purchase_invoice($purchase_id = '', $business_id = '')
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("purchases as p");
        // Apply filters
        $builder->where(['p.id' => $purchase_id, 'p.business_id' => $business_id]);

        // Join tables
        $builder->join('purchases_items as pi', 'pi.purchase_id = p.id');
        $builder->join('warehouses', 'warehouses.id=p.warehouse_id');
        $builder->join('users as u', 'u.id = p.supplier_id');
        $builder->join('products_variants as pv', 'pv.id = pi.product_variant_id');
        $builder->join('products', 'pv.product_id = products.id');
        $builder->join('businesses as b', 'b.id = p.business_id');

        $builder->select('pv.variant_name, products.name as product_name, p.*,p.id as purchase_id,p.discount as purchase_discount,pi.price,pi.discount,pi.quantity,pi.id as purchase_items_id,b.contact,b.name,b.icon,b.address,b.tax_name as b_tax,b.description,b.tax_value,u.first_name,u.last_name,u.mobile,u.email, p.payment_status ,p.status , warehouses.name as warehouse_name');

        $res = $builder->get()->getResultArray();
        return $res;
    }

    public function purchases_report_table($business_id)
    {

        $builder =    $where = $multipleWhere = $rows =  [];
        $i = 0;
        $db = \config\Database::connect();
        $builder = $db->table("purchases p");
        $builder->select('p.id as purchase_id , p.supplier_id,u.first_name,u.mobile,p.purchase_date,p.amount_paid,p.total,p.payment_status ');
        $builder->join('users u ', 'u.id = p.supplier_id');
        $builder->where('p.business_id', $business_id);

        if (isset($_GET['payment_status_filter'])) {
            $payment_status_filter = $_GET['payment_status_filter'];
            if ($payment_status_filter == 'fully_paid') {
                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'partially_paid') {
                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'unpaid') {
                $builder->where('payment_status', $payment_status_filter);
            }
        }


        if (isset($_GET['supplier_id'])) {
            $supplier_filter = $_GET['supplier_id'];
            if ($supplier_filter = $_GET['supplier_id']) {
                $builder->where('supplier_id', $supplier_filter);
            }
        }

        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 100;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? trim($_GET['sort']) : "p.id";
        $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? trim($_GET['order']) : "DESC";

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'p.id' => $search,
                'p.supplier_id' => $search,
                'p.total' => $search,
                'p.payment_status' => $search,
                'p.purchase_date' => $search,
                'p.amount_paid' => $search,
                'u.first_name' => $search,
                'u.mobile' => $search,
            ];
        }

        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((p.created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (p.created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
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
        $purchases = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)->getWhere()->getResultArray();
        $total = $this->total_purchases($business_id);


        foreach ($purchases as $reports) {
            $payment_status = $reports['payment_status'];
            if ($payment_status == 'fully_paid') {
                $payment_status = '<span class = "text-dark badge badge-success">' . $payment_status . '</span>';
            }
            if ($payment_status == 'unpaid') {
                $payment_status = '<span class = "text-dark badge badge-danger">' . $payment_status . '</span>';
            }
            if ($payment_status == 'partially_paid') {
                $payment_status = '<span class = "text-dark badge badge-warning">' . $payment_status . '</span>';
            }


            $rows[$i] = [
                'purchase_id' => $reports['purchase_id'],
                'supplier_id' => $reports['supplier_id'],
                'first_name' => $reports['first_name'],
                'mobile' => $reports['mobile'],
                'purchase_date' => date_formats(strtotime($reports['purchase_date'])),
                'amount_paid' => currency_location(decimal_points($reports['amount_paid'])),
                'total' => currency_location(decimal_points($reports['total'])),
                'payment_status' => $payment_status,
                'remaining_amount' => currency_location(decimal_points($reports['total'] - $reports['amount_paid'])),
            ];
            $i++;
        }
        $array['total'] = $total;
        $array['rows'] = $rows;
        return $array;
    }

    public function supplier_details($business_id)
    {
        $db = \config\Database::connect();
        $builder = $db->table("purchases p");
        $builder->join('users u ', 'u.id = p.supplier_id');
        $builder->where('p.business_id', $business_id);
        $builder->select('supplier_id ,first_name');
        $builder->groupBy('supplier_id');
        return $builder->get()->getResultArray();
    }
    public function total_purchases($business_id)
    {
        $builder =    $where = $multipleWhere = $rows =  [];
        $i = 0;
        $db = \config\Database::connect();
        $builder = $db->table("purchases p");
        $builder->join('users u ', 'u.id = p.supplier_id');
        $builder->where('p.business_id', $business_id);
        $builder->select('p.id as purchase_id , p.supplier_id,u.first_name,u.mobile,p.purchase_date,p.amount_paid,p.total,p.payment_status ,(p.total - p.amount_paid) as remaining_amount');

        if (isset($_GET['payment_status_filter'])) {
            $payment_status_filter = $_GET['payment_status_filter'];
            if ($payment_status_filter == 'fully_paid') {
                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'partially_paid') {
                $builder->where('payment_status', $payment_status_filter);
            } elseif ($payment_status_filter == 'unpaid') {
                $builder->where('payment_status', $payment_status_filter);
            }
        }

        if (isset($_GET['supplier_id'])) {
            if ($supplier_filter = $_GET['supplier_id']) {
                $builder->where('supplier_id', $supplier_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((p.created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (p.created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
        }

        $total = count($builder->get()->getResultArray());
        return $total;
    }
}
