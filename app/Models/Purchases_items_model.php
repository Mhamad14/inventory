<?php

namespace App\Models;

use CodeIgniter\Model;

class Purchases_items_model extends Model
{

    protected $table = 'purchases_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['purchase_id', 'product_variant_id', 'quantity', 'price', 'discount', 'status'];

    public function savePurchaseItem($purchase_id, $status, $item)
    {
        $this->save(
            [
                'purchase_id' => $purchase_id,
                'product_variant_id' => $item->id,
                'quantity' => $item->qty,
                'price' => $item->price,
                'discount' => $item->discount,
                'status' => $status,
            ]
        ) !== false;
    }
}
