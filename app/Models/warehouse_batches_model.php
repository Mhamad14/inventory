<?php

namespace App\Models;

use CodeIgniter\Model;

class warehouse_batches_model extends Model
{
    protected $table = 'warehouse_batches';
    protected $primaryKey = 'id';
    protected $useTimestamps = false;
    protected $allowedFields = [
        'id',
        'purchase_item_id',
        'business_id',
        'product_variant_id',
        'warehouse_id',
        'batch_number',
        'quantity',
        'cost_price',
        'sell_price',
        'expiration_date',
        'created_at',
    ];


    /**
     * Prepare a batch number based on the purchase item ID and expiration date.
     *
     * @param int $id The ID of the purchase item.
     * @param string|null $expire_date The expiration date of the item, if available.
     * @return string The formatted batch number.
     */
    function prepareBatchNumber($id, $expire_date)
    {
        $current_timestamp = time(); // Unix timestamp
        if (!empty($expire_date)) {
            $expire_date = date('Y-m-d', strtotime($expire_date));
        } else {
            $expire_date = '0000-00-00'; // Default value if no expiry date is provided
        }

        return "-{$id}-{$current_timestamp}-{$expire_date}";
    }

    /**
     * Save batch information to the database.
     *
     * @param int $purchase_items_id The ID of the purchase item.
     * @param int $warehouse_id The ID of the warehouse.
     * @param object $item The item object containing details like expire date, quantity, sell price, and cost price.
     * @return bool Returns true on success, false on failure.
     */
    public function saveBatch($purchase_items_id, $warehouse_id, $item, $orderType = 'order'): bool
    {
        $batch_prefix = $orderType == "order" ? "ORDER" : "RETURN";
        $batch_number = $this->prepareBatchNumber($purchase_items_id, $item->expire);
        $item->qty = $orderType == "order" ? $item->qty : -$item->qty;
      
        return $this->save([
            'purchase_item_id' => $purchase_items_id,
            'business_id' => session('business_id'),
            'product_variant_id' => $item->id,
            'warehouse_id' => $warehouse_id,
            'batch_number' => $batch_prefix . $batch_number,
            'expiration_date' => $item->expire ?? '',
            'quantity' => $item->qty,
            'sell_price' => $item->sell_price,
            'cost_price' => $item->price,
        ]) !== false;
    }
}
