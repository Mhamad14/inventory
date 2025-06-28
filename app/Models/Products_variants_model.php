<?php

namespace App\Models;

use CodeIgniter\Model;

class Products_variants_model extends Model
{

    protected $table = 'products_variants';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'product_id', 'variant_name', 'sale_price', 'purchase_price', 'stock', 'unit_id', 'qty_alert', 'expiry_alert_days', 'status', 'barcode'];
    protected $warehouse_product_stock_model;

    public function __construct()
    {
        parent::__construct();
        $this->warehouse_product_stock_model = new WarehouseProductStockModel();
    }

    public function count_of_variants()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("products_variants");
        $builder->select('COUNT(id) as `total`');
        $variants = $builder->get()->getResultArray();
        return $variants;
    }

    // public function decrease_stock($variant_id, $quantity)
    // {
    //     $builder = $this->db->table("products_variants");
    //     $builder->set('stock', "stock - $quantity", false);
    //     $builder->where('id', $variant_id);
    //     return $builder->update();
    // }

    public function remove_variant($variant_id, $warehouse_id)
    {

    }
    public function getProductVariants($search = '')
    {
        $builder = $this->db->table("products_variants pv");
        $builder->select('p.id, pv.id as variant_id, pv.variant_name, pv.stock, pv.qty_alert, p.name, p.image, c.name as category, (pv.stock - pv.qty_alert) as alert');

        $builder->join('products p', 'p.id = pv.product_id', 'left');
        $builder->join('categories c', 'c.id = p.category_id', 'left');
        $builder->where('p.business_id', session('business_id'));
        $builder->where('p.status', 1); // Only active products
        $builder->where('pv.status', 1); // Only active variants


        if (!empty($search)) {
            $builder->groupStart();
            $builder->orLike('pv.variant_name', $search);
            $builder->orLike('p.name', $search);
            $builder->orLike('c.name', $search);
            $builder->orLike('pv.id', $search);
            $builder->orLike('p.id', $search);
            $builder->groupEnd();
        }

        $builder->orderBy('alert', 'ASC');
        $builder->orderBy('p.name', 'ASC');
        $builder->orderBy('pv.variant_name', 'ASC');

        return $builder->get()->getResultArray();
    }
    public function get_product_variants($product_id = "")
    {
        $builder = $this->db->table("products_variants");

        $builder->where('product_id', $product_id);
        $condition  = [];

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
        $order = "ASC";
        if (isset($_GET['order'])) {
            $order = $_GET['order'];
        }
        if (isset($_GET['search']) and $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                '`id`' => $search,
                '`product_id`' => $search,
                '`variant_name`' => $search,
                '`sale_price`' => $search,
                '`purchase_price`' => $search,
                '`stock`' => $search,
                '`unit_id`' => $search,
                '`status`' => $search
            ];
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
        if (isset($where) && !empty($where)) {
            $builder->where($where);
        }
        $variants = $builder->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
        return $variants;
    }

    public function all_products_variants()
    {
        $builder = $this->db->table("products_variants as pv");
        $builder->select('pv.id,pv.variant_name,p.name, pv.barcode');
        $builder->join('products as p', 'p.id=pv.product_id', "left");
        $condition  = [];
        $offset = 0;
        if (isset($_GET['offset']))
            $offset = $_GET['offset'];
        $total = $this->count_of_variants();

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
                '`product_id`' => $search,
                '`variant_name`' => $search,
                '`sale_price`' => $search,
                '`purchase_price`' => $search,
                '`stock`' => $search,
                '`unit_id`' => $search,
                '`status`' => $search
            ];
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
        $variants = $builder->orderBy($sort, $order)->limit($offset)->get()->getResultArray();
        return $variants;
    }
    public function get_low_variant_stock($business_id)
    {
        return $this->db->table('products_variants pv')
            ->select('pv.*, p.name as product_name')
            ->join('products p', 'p.id = pv.product_id')
            ->where('p.business_id', $business_id)
            ->where('pv.qty_alert >', 0)
            ->where('pv.stock <=', 'pv.qty_alert', false)
            ->get()
            ->getResultArray();
    }

    public function upsert_warehouse_stock($warehouse_id, $variant_id, $quantity)
    {
        // 1. get products of warehouse_product_stock
        $isProductExist = $this->warehouse_product_stock_model
            ->where('product_variant_id', $variant_id)->where('warehouse_id', $warehouse_id)->get()->getResultArray();

        // 2. check: after insert or update stock in warehouse
        if (!$isProductExist) {

            $this->warehouse_product_stock_model->insert([
                'warehouse_id' => $warehouse_id,
                'product_variant_id' => $variant_id,
                'stock' => $quantity,
                'qty_alert' => null,
                'vendor_id' => session('user_id'),
                'business_id' => session('business_id'),
            ]);
        } else {
            $this->warehouse_product_stock_model
                ->where('product_variant_id', $variant_id)
                ->where('warehouse_id', $warehouse_id)
                ->set('stock', "stock + {$quantity}", false)
                ->update();
        }
    }

    public function getVariantWithProduct($variant_id)
    {
        $builder = $this->db->table('products_variants pv');
        $builder->select('pv.*, p.name as product_name, p.image');
        $builder->join('products p', 'p.id = pv.product_id', 'left');
        $builder->where('pv.id', $variant_id);
        return $builder->get()->getRowArray();
    }
}
