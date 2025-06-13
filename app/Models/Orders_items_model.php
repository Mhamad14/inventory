<?php

namespace App\Models;

use CodeIgniter\Model;
use Countable;

class Orders_items_model extends Model
{

    protected $table = 'orders_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_id', 'product_id', 'product_variant_id', 'product_name', 'quantity', 'price', 'tax_name', 'tax_percentage', 'tax_details', 'is_tax_included', 'sub_total', 'status', 'delivery_boy', 'returned_quantity'];



    public function getOrderItemsWithDetails($order_id)
    {
        return $this->db->table('order_items_view')->where('order_id', $order_id)->get()->getResultArray();
    }

    function get_items($order_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("orders_items");
        $builder->where("order_id", $order_id);
        return $builder->get()->getResultArray();
    }

    function top_selling_products()
    {
        $db = \Config\Database::connect();
        $builder =    $where = $multipleWhere = $rows =  [];
        $offset = $i = 0;
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";

        $db      = \Config\Database::connect();
        $builder = $db->table("orders_items as ot");
        $builder->join('orders o', 'o.id = ot.order_id');
        $builder->join('products p', 'p.id = ot.product_id');
        $builder->where('p.business_id', $business_id);
        $builder->groupBy('product_name');
        $builder->select('COUNT(order_id) as total_sales, SUM(price) as total_amount , ot.product_id ,price , product_name , stock');


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
                '`p.id`' => $search,
                '`product_name`' => $search,
                '`price`' => $search,
            ];
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
        $sells = $builder->orderBy($sort, $order)
            ->limit($limit, $offset)->getWhere()->getResultArray();
        $total = $this->total_selling($where, $multipleWhere);

        foreach ($sells as $reports) {

            $rows[$i] = [
                'product_id' => $reports['product_id'],
                'product_name' => $reports['product_name'],
                'price' => currency_location(decimal_points($reports['price'])),
                'total_sales' => $reports['total_sales'],
                'total_amount' => currency_location(decimal_points($reports['total_amount'])),
            ];
            $i++;
        }
        $array['total'] = $total;
        $array['rows'] = $rows;
        return $array;
    }

    public function getReturnedQuantity($item_id)
    {
        $builder = $this->db->table('order_returns');
        $builder->selectSum('quantity');
        $builder->where('item_id', $item_id);
        $result = $builder->get()->getRowArray();
        return ($result && isset($result['quantity'])) ? $result['quantity'] : 0;
    }

    function total_selling()
    {
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";

        $builder =    $where = $multipleWhere = $rows =  [];
        $offset = $i = 0;
        $business_id = isset($_SESSION['business_id']) ? $_SESSION['business_id'] : "";
        $vendor_id = isset($_POST['vendor_id']) ? $_POST['vendor_id'] : "";

        $db      = \Config\Database::connect();
        $builder = $db->table("orders_items as ot");
        $builder->join('orders o', 'o.id = ot.order_id');
        $builder->join('products p', 'p.id = ot.product_id');
        $builder->where('p.business_id', $business_id);
        $builder->groupBy('product_name');
        $builder->select('COUNT(order_id) as total_sales, SUM(price) as total_amount , ot.product_id ,price , product_name , stock');
        $builder->orderBy('total_sales', 'DESC');

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

        $total = count($builder->get()->getResultArray());

        return $total;
    }
}
