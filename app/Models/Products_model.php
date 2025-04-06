<?php

namespace App\Models;

use CodeIgniter\Model;

class Products_model extends Model
{

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id','category_id', 'brand_id' ,'business_id', 'vendor_id', 'tax_ids', 'name', 'description', 'image', 'type', 'stock_management', 'stock', 'unit_id', 'qty_alert', 'is_tax_included', 'status'];

    public function count_of_products($business_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("products");
        $builder->select('COUNT(id) as `total`');
        $builder->where('business_id', $business_id);
        $products = $builder->get()->getResultArray();
        return $products;
    }
    public function get_product_details($business_id = '', $flag = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("products as p");
        $builder->select('p.id,pv.id,pv.stock , pv.variant_name, pv.qty_alert,pv.product_id,pv.variant_name,p.name as product,p.stock_management,p.stock,p.qty_alert,p.business_id');
        $builder->where('p.business_id', $business_id);
        $builder->whereIn('p.stock_management', [1, 2]);
        $builder->join('products_variants as pv', 'p.id = pv.product_id ', "left"); // added left here
        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }

        if (isset($flag) && $flag == "out") {
            $builder->where('((p.stock = 0) AND (pv.stock = 0 ))');
        }
        if (isset($flag) && $flag == "low") {
            $builder->where('((p.stock < p.qty_alert AND p.stock > 0 AND p.stock_management = "1" ) OR (pv.stock < pv.qty_alert AND pv.stock > 0 AND p.stock_management = "2")) ');
            $builder->groupBy('p.id');
        }

        if (isset($_GET['offset'])){
            $offset = $_GET['offset'];
        }

        $limit = 20;
        if (isset($_GET['limit'])) {
            $limit = $_GET['limit'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'p.name' => $search,
                'pv.variant_name' => $search,
                'pv.stock' => $search,
                'p.stock' => $search,
                'p.qty_alert' => $search,
                'p.stock_management' => $search,
            ];
        }
        if (isset($multipleWhere) && !empty($multipleWhere)) {
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        $sort = "p.id";
        if (isset($_GET['sort'])) {
            if ($_GET['sort'] == 'p.id') {
                $sort = "p.id";
            } else {
                $sort = $_GET['sort'];
            }
        }
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        $products = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $products;
    }
    public function get_products($business_id = "", $flag = null)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("products as p");
        $builder->select('p.*,pv.id as product_variant_id,pv.product_id,pv.variant_name,pv.sale_price,pv.purchase_price,pv.stock,pv.unit_id');
        $builder->where('p.business_id', $business_id);
        $builder->join('products_variants as pv', 'p.id = pv.product_id ', "left"); // added left here


        $offset = 0;
        if (isset($_GET['offset'])) {
            $offset = $_GET['offset'];
        }

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
                '`id`' => $search,
                '`category_id`' => $search,
                '`business_id`' => $search,
                '`tax_id`' => $search,
                '`name`' => $search,
                '`description`' => $search,
                '`image`' => $search,
                '`type`' => $search,
                '`stock_management`' => $search,
                '`stock`' => $search,
                '`unit_id`' => $search,
                '`is_tax_included`' => $search,
                '`status`' => $search,

            ];
        }
        $builder->select(' COUNT(id) as `total`');
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
        $products = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $products;
    }

    public function edit_product($product_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $this->db->table("products as p");
        $builder->select('p.*,pv.id as product_variant_id,pv.product_id,pv.variant_name,pv.sale_price,pv.purchase_price,pv.stock,pv.unit_id');
        $builder->where('p.id', $product_id);
        $builder->join('products_variants as pv', 'p.id = pv.product_id ', "left"); // added left here
        return $builder->get()->getResultArray();
    }
}
