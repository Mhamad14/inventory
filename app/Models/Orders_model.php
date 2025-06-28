<?php

namespace App\Models;

use CodeIgniter\Model;

class Orders_model extends Model
{

    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'customer_id', 'warehouse_id' ,'order_no', 'business_id', 'created_by', 'total', 'delivery_charges', 'discount', 'final_total', 'payment_status', 'amount_paid', 'order_type', 'message', 'payment_method' , 'is_pos_order'];

    public function get_orders($business_id = "")
    {

        $db      = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->where("business_id", $business_id);

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

        if (isset($_GET['order_type_filter'])) {
            $order_type_filter = $_GET['order_type_filter'];
            if ($order_type_filter == 'product') {
                $builder->where('order_type', $order_type_filter);
            } elseif ($order_type_filter == 'service') {
                $builder->where('order_type', $order_type_filter);
            }
        }
        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }

        $orders = $builder->getWhere()->getResultArray();
        return $orders;
    }

    public function count_of_orders($business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $orders = $builder->get()->getResultArray();
        return $orders;
    }

    public function get_delivery_boy_orders_list($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("orders as o");
        $builder->select('o.*, u.first_name, c.balance');
        $builder->join('users as u', 'u.id=o.customer_id', 'left');
        $builder->join('customers as c', 'c.user_id=o.customer_id', 'left');
        $builder->where("o.business_id", $business_id);
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
        $order = "DESC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'o.payment_status' => $search,
                'o.discount' => $search,
                'o.amount_paid' => $search,
                'o.delivery_charges' => $search,
                'o.message' => $search,
                'o.created_at' => $search,
                'o.payment_method' => $search,
                'u.first_name' => $search,
                'c.balance' => $search,

            ];
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $builder->where('((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))');
        }
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

        if (isset($_GET['order_type_filter'])) {
            $order_type_filter = $_GET['order_type_filter'];
            if ($order_type_filter == 'product') {
                $builder->where('order_type', $order_type_filter);
            } elseif ($order_type_filter == 'service') {
                $builder->where('order_type', $order_type_filter);
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

        $orders = $builder->orderBy($sort, $order)->limit($limit, $offset)->getWhere()->getResultArray();
        return $orders;
    }

    public function get_order_invoice($order_id = "", $business_id = "")
    {
        $db      = \Config\Database::connect();
        $customer_model = new Customers_model();
        $type =  fetch_details('orders', ['id' => $order_id, 'business_id' => $business_id], ['order_type','customer_id']);

        $customer_id = $type[0]['customer_id'];

        $customer_array = $customer_model->where('user_id', $customer_id)->get()->getResultArray();
        if (empty($customer_array)) {
            $customer_array = $customer_model->where('id', $customer_id)->get()->getResultArray();
        }
        $user_id =  $customer_array[0]['user_id'];

        if (isset($type) && !empty($type)) {
            if ($type[0]['order_type'] == "product") {
                $builder = $db->table("orders as o");
                $builder->select('o.created_at,b.contact,b.name as business_name,b.icon,b.address,b.tax_name as b_tax,b.tax_value,b.description,p.name as product_name,u.first_name,u.last_name,u.mobile,u.email,o.id,o.order_type,o.customer_id,o.total,o.final_total,o.payment_status,o.amount_paid,o.payment_method,o.delivery_charges,o.discount,o.warehouse_id,oi.product_name as order_name,oi.product_id,oi.quantity,oi.price,oi.tax_name,oi.tax_percentage,oi.sub_total,oi.tax_details,warehouses.name as warehouse_name');
                $builder->where(['o.id' => $order_id, 'o.business_id' => $business_id]);
                $builder->join('orders_items as oi', 'oi.order_id=o.id', 'left');
                $builder->join('warehouses', 'warehouses.id=o.warehouse_id', 'left');
                $builder->join('users as u', "u.id=$user_id", 'left');
                $builder->join('products as p', 'p.id=oi.product_id', 'left');
                $builder->join('businesses as b', 'b.id=o.business_id', 'left');
                return $builder->get()->getResultArray();
            }
            if ($type[0]['order_type'] == "service") {
                $builder = $db->table("orders as o");
                $builder->select('o.created_at,b.contact,b.name as business_name,b.icon,b.address,b.tax_name as b_tax,b.description,b.tax_value,u.first_name,u.last_name,u.mobile,u.email,o.id,o.order_type,o.customer_id,o.total,o.final_total,o.payment_status,o.amount_paid,o.payment_method,o.delivery_charges,o.discount,os.service_name ,os.price,os.quantity,os.unit_name,os.sub_total,os.tax_name,os.tax_percentage,os.tax_details');
                $builder->where(['o.id' => $order_id, 'o.business_id' => $business_id]);
                $builder->join('orders_services as os', 'os.order_id=o.id', 'left');
                $builder->join('users as u', "u.id=$user_id", 'left');
                $builder->join('businesses as b', 'b.id=o.business_id', 'left');
                return $builder->get()->getResultArray();
            }
        } else {
            return false;
        }
    }

    public function best_customers_table()
    {
        $db = \Config\Database::connect();
        $builder =    $where = $multipleWhere = $rows =  [];
        $offset = $i = 0;
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";

        $db      = \Config\Database::connect();
        $db = \Config\Database::connect();
        $builder = $db->table('orders as o');
        $builder->join('users as u', 'u.id = o.customer_id');
        $builder->where('o.business_id', $business_id);
        $builder->groupBy('u.id');
        $builder->select('SUM(o.final_total) as total_amount,u.mobile,u.email,u.first_name,u.id as customer_id,COUNT(o.customer_id) as total_sales');


        if (isset($_GET['offset']))
            $offset = $_GET['offset'];

        $limit = 100;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }

        $sort = (isset($_GET['sort']) && !empty(trim($_GET['sort']))) ? trim($_GET['sort']) : "total_sales";
        $order = (isset($_GET['order']) && !empty(trim($_GET['order']))) ? trim($_GET['order']) : "DESC";

        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`customer_id`' => $search,
                '`first_name`' => $search,
                '`email`' => $search,
                '`mobile`' => $search,
            ];
        }

        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {
            $end_date = $_GET['end_date'];
            $start_date = $_GET['start_date'];
        }
        if (isset($_GET['start_date']) and isset($_GET['start_date']) and ($_GET['end_date'] != '') and  ($_GET['end_date'] != '')) {
            $where = '((o.created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (o.created_at <= "' . $_GET['end_date'] . ' 12:00:00"))';
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
        $best_customers = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)->getWhere()->getResultArray();
        $total = count($best_customers);


        foreach ($best_customers as $reports) {

            $rows[$i] = [
                'customer_id' => $reports['customer_id'],
                'first_name' => $reports['first_name'],
                'email' => $reports['email'],
                'mobile' => $reports['mobile'],
                'total_sales' => $reports['total_sales'],
                'total_amount' => currency_location(decimal_points($reports['total_amount'])),
            ];
            $i++;
        }
        $array['total'] = $total;
        $array['rows'] = $rows;
        return $array;
    }

    public function payment_reminder($business_id)
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("orders");
        $builder->where("business_id", $business_id);
        $builder->where('payment_status', 'partially_paid')->orWhere('payment_status', 'unpaid');

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

        if (isset($_GET['order_type_filter'])) {
            $order_type_filter = $_GET['order_type_filter'];
            if ($order_type_filter == 'product') {
                $builder->where('order_type', $order_type_filter);
            } elseif ($order_type_filter == 'service') {
                $builder->where('order_type', $order_type_filter);
            }
        }

        if (isset($_GET['end_date']) && $_GET['end_date'] != '' && isset($_GET['start_date']) && $_GET['start_date'] != '') {

            $builder->where('((created_at >= "' . $_GET['start_date'] . ' 12:00:00") AND (created_at <= "' . $_GET['end_date'] . ' 12:00:00"))');
        }

        $orders = $builder->getWhere()->getResultArray();

        return $orders;
    }
}
