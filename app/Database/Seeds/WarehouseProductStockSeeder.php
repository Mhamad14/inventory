<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarehouseProductStockSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // Set the warehouse_id to 1 as per the requirement
        $warehouse_id = 1;

        // Fetch all products
        $products = $db->table('products')->get()->getResult();

        foreach ($products as $product) {
            // Stock is managed through variants, get all variants for this product
            $variants = $db->table('products_variants')
                ->where('product_id', $product->id)
                ->get()->getResult();

            foreach ($variants as $variant) {
                $data = [
                    'vendor_id' => $product->vendor_id,
                    'business_id' => $product->business_id,
                    'warehouse_id' => $warehouse_id,
                    'product_variant_id' => $variant->id, // Use the variant ID
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ];

                if ($product->stock_management == 1) {
                    $data['stock'] = $product->stock;
                    $data['qty_alert'] = $product->qty_alert;
                } elseif ($product->stock_management == 2) {
                    $data['stock'] = $variant->stock;
                    $data['qty_alert'] = $variant->qty_alert;
                }

                // Check if the record already exists in the warehouse_product_stock table
                $existingRecord = $db->table('warehouse_product_stock')
                    ->where('vendor_id', $data['vendor_id'])
                    ->where('business_id', $data['business_id'])
                    ->where('warehouse_id', $data['warehouse_id'])
                    ->where('product_variant_id', $data['product_variant_id'])
                    ->get()
                    ->getRow();

                // If no record exists, insert the new data
                if (!$existingRecord) {
                    $db->table('warehouse_product_stock')->insert($data);
                }
            }
        }
    }
}
