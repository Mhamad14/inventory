<?php

namespace App\Models;

use CodeIgniter\Model;

class Products_model extends Model
{

    protected $table = 'products';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id', 'category_id', 'brand_id', 'business_id', 'vendor_id', 'tax_ids', 'name', 'description', 'image', 'type', 'stock_management', 'stock', 'unit_id', 'qty_alert', 'is_tax_included', 'status'];

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
            ->where('qty_alert >', 0)     // Only products with alert level set
            ->where('stock <= qty_alert')  // Current stock is at or below alert level
            ->where('stock >', 0)         // Exclude out-of-stock items
            ->get()
            ->getResultArray();
    }

    public function get_all_products($business_id): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('view_product_details');

        // Filters
        $business_id = $_SESSION['business_id'] ?? "";
        $category_id = $_GET['category_id'] ?? "";
        $brand_id = $_GET['brand_id'] ?? "";
        $limit = $_GET['limit'] ?? 10;
        $offset = $_GET['offset'] ?? 0;
        $sort = $_GET['sort'] ?? "product_id"; // Use correct column from view
        $order = $_GET['order'] ?? "DESC";
        $search = $_GET['search'] ?? '';

        // WHERE conditions
        if (!empty($business_id)) {
            $builder->where('business_id', $business_id);
        }
        if (!empty($category_id)) {
            $builder->where('category_id', $category_id);
        }
        if (!empty($brand_id)) {
            $builder->where('brand_id', $brand_id);
        }
        if (!empty($search)) {
            $builder->groupStart()
                ->like('product_name', $search)
                ->orLike('description', $search)
                ->orLike('category_name', $search)
                ->orLike('brand_name', $search)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false); // false: don't reset query
        $builder->orderBy($sort, $order);
        $builder->limit($limit, $offset);

        $results = $builder->get()->getResultArray();
        
        return [
            'data'=> $results,
            'total'=>$total
        ];
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
