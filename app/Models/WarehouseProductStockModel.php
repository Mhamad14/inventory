<?php

namespace App\Models;

use CodeIgniter\Model;

class WarehouseProductStockModel extends Model
{
    protected $table            = 'warehouse_product_stock';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = ['id', 'vendor_id', 'business_id', 'warehouse_id', 'product_variant_id', 'stock', 'qty_alert', 'created_at', 'updated_at'];

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];



    public function increaseWarehouseStock($warehouse_id, $product_variant_id, $quantity)
    {
        $this->db->query("select ");

        $db = \Config\Database::connect();
        $builder = $db->table("warehouse_product_stock");

        $builder->set('stock', "stock + {$quantity}", false);
        $builder->where(['warehouse_id' => $warehouse_id, 'product_variant_id' => $product_variant_id]);
        return $builder->update();
    }

    public function get_warehouses_data_for_variants($variant_ids)
    {
        foreach ($variant_ids as $id) {
            $db      = \Config\Database::connect();

            $builder = $db->table("warehouse_product_stock");
            $builder->where(['warehouse_product_stock.product_variant_id' => $id]);
            $builder->join('warehouses', 'warehouses.id=warehouse_product_stock.warehouse_id', 'left');
            return $builder->get()->getResultArray();
        }
    }

    public function get_low_warehouse_stock($business_id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("warehouse_product_stock");

        $result = [];

        $builder->select('warehouse_product_stock.*,products.name as product_name , products_variants.*');
        $builder->where(['warehouse_product_stock.business_id' => $business_id,]);

        $builder->where('warehouse_product_stock.stock <= warehouse_product_stock.qty_alert');

        $builder->join('warehouses', 'warehouses.id = warehouse_product_stock.warehouse_id', 'left');
        $builder->join('products_variants', 'products_variants.id = warehouse_product_stock.product_variant_id', 'left');
        $builder->join('products', 'products.id = products_variants.product_id', 'left');

        $result = array_merge($result, $builder->get()->getResultArray());


        return $result;
    }
}
