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
    /**
 * Improved get_product_details method with better stock alert handling
 */
public function get_product_details($business_id = '', $flag = '')
{
    $builder = $this->db->table("products as p");
    $builder->select('p.*, pv.id as variant_id, pv.variant_name, pv.stock as variant_stock, 
                     pv.qty_alert as variant_qty_alert, pv.status as variant_status');
    $builder->where('p.business_id', $business_id);
    
    // Join with variants
    $builder->join('products_variants as pv', 'p.id = pv.product_id', 'left');
    
    // Handle different flag cases
    switch ($flag) {
        case 'out':
            $builder->groupStart()
                ->where('p.stock', 0)
                ->orWhere('pv.stock', 0)
                ->groupEnd();
            break;
            
        case 'low':
            $builder->groupStart()
                // Product-level low stock
                ->groupStart()
                    ->where('p.stock_management', 1)
                    ->where('p.qty_alert >', 0)
                    ->where('p.stock <= p.qty_alert', null, false)
                    ->where('p.stock >', 0)
                ->groupEnd()
                // Variant-level low stock
                ->orGroupStart()
                    ->where('p.stock_management', 2)
                    ->where('pv.qty_alert >', 0)
                    ->where('pv.stock <= pv.qty_alert', null, false)
                    ->where('pv.stock >', 0)
                ->groupEnd()
                ->groupEnd();
            break;
    }

    return $builder->get()->getResultArray();
}
public function get_low_product_stock($business_id)
{
    return $this->db->table('products')
        ->where('business_id', $business_id)
        ->where('stock_management', 1) // Only product-level stock management
        ->where('qty_alert >', 0)     // Only products with alert level set
        ->where('stock <= qty_alert')  // Current stock is at or below alert level
        ->where('stock >', 0)         // Exclude out-of-stock items
        ->get()
        ->getResultArray();
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
