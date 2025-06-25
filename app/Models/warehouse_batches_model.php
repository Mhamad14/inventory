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


    public function getBatches($purchase_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("warehouse_batches as wb");
        $builder->select(
            'wb.id,wb.product_variant_id, wb.purchase_item_id, p.image, pv.variant_name , p.name as product_name, wb.batch_number, 
            wb.quantity, wb.cost_price, wb.sell_price,pi.discount, wb.expiration_date, s.status, pi.status as status_id, wb.warehouse_id'
        );
        $builder->join('purchases_items as pi', 'pi.id = wb.purchase_item_id');
        $builder->join('products_variants as pv', 'pv.id = wb.product_variant_id');
        $builder->join('products as p', 'p.id = pv.product_id');
        $builder->join('status as s', 's.id = pi.status');
        $builder->where('pi.quantity >', 0);
        if (!empty($purchase_id)) {
            $builder->where(['pi.purchase_id' => $purchase_id]);
        }


        $result = $builder->get()->getResultArray();
        $count = count($result);

        return [
            'result' => $result,
            'total' => $count,
        ];
    }

    public function getReturnedBatches($purchase_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("warehouse_batches_returns as wb");
        $builder->select(
            'wb.id,wb.product_variant_id, wb.purchase_item_id, p.image, pv.variant_name, p.name as product_name, 
            wb.quantity, wb.cost_price, wb.return_price, wb.return_reason, wb.return_date,'
        );
        $builder->join('purchases_items as pi', 'pi.id = wb.purchase_item_id', 'right');
        $builder->join('products_variants as pv', 'pv.id = wb.product_variant_id');
        $builder->join('products as p', 'p.id = pv.product_id');

        if (!empty($purchase_id)) {
            $builder->where(['pi.purchase_id' => $purchase_id]);
        }

        $result = $builder->get()->getResultArray();
        $count = count($result);
        return [
            'result' => $result,
            'total' => $count,
        ];
    }
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

    public function update_batch($batch_id, $purchase_item_id, $quantity, $cost_price, $sell_price, $discount, $expire, $variant_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $purchase_id = $db->table('purchases_items')->where('id', $purchase_item_id)->get()->getRow()->purchase_id;

        // 1. Update purchases_items row
        $db->table('purchases_items')->where('id', $purchase_item_id)->update([
            'price' => $cost_price,
            'quantity' => $quantity,
            'discount' => $discount,
        ]);

        // 2. update purchase total
        $this->update_purchase_total($purchase_id);

        // 3. Update warehouse_batches with quantity and cost_price (cost_price = price here)
        $batchUpdate = $db->table('warehouse_batches')->where('id', $batch_id)->update([
            'cost_price' => $cost_price,
            'sell_price' => $sell_price,
            'quantity' => $quantity,
            'expiration_date' => $expire,
        ]);
        if (!$batchUpdate) {
            $db->transRollback();
            return false;
        }

        $db->transComplete();
        return $db->transStatus(); // returns true on success, false on failure
    }

    public function delete_batch($batch_id, $purchase_item_id)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $purchase_id = $db->table('purchases_items')->where('id', $purchase_item_id)->get()->getRow()->purchase_id;
        // 1 update warehouse product stock
        // 1.1 get batch data
        $batch = $db->table('warehouse_batches')->where('id', $batch_id)->get()->getRow();

        // 1.2 update warehouse_product_stock
        $db->table('warehouse_product_stock')
            ->set('stock', "stock - {$batch->quantity}", false) // false = do not escape
            ->where('warehouse_id', $batch->warehouse_id)
            ->where('product_variant_id', $batch->product_variant_id)
            ->update();

        // 2- delete batch
        $db->table('warehouse_batches')->where('id', $batch_id)->delete();

        // 3- delete purchase item
        $db->table('purchases_items')->where('id', $purchase_item_id)->delete();

        // 4 delete warehouse_batches_returns
        $db->table('warehouse_batches_returns')->where('purchase_item_id', $purchase_item_id)->delete();




        // 4 update purchase total
        $this->update_purchase_total($purchase_id);

        $db->transComplete();
        return $db->transStatus(); // returns true on success, false on failure
    }
    public function delete_returned_batch($id)
    {
        $db = \Config\Database::connect();
        $deleted = $db->table('warehouse_batches_returns')->where('id', $id)->delete();
        return $deleted;
    }

    public function return_batch($return_data, $return_price, $return_reason, $return_quantity, $return_date)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        $return_data = json_decode($return_data, true);

        // 1. update batch quantity (decrease)
        $db->table('warehouse_batches')->where('id', $return_data['id'])->update([
            'quantity' => ((int) $return_data['quantity'] - (int) $return_quantity),
        ]);

        // 2. insert return batch
        $insertData = [
            'purchase_item_id' => $return_data['purchase_item_id'],
            'business_id' => session('business_id'),
            'product_variant_id' => $return_data['product_variant_id'],
            'warehouse_id' => $return_data['warehouse_id'],
            'batch_number' => $return_data['batch_number'],
            'quantity' => $return_quantity,
            'cost_price' => $return_data['cost_price'],
            'return_price' => $return_price,
            'return_reason' => $return_reason,
            'return_date' => $return_date,
        ];
        $db->table('warehouse_batches_returns')->insert($insertData);

        // 3 update purchases_items
        $db->table('purchases_items')->where('id', $return_data['purchase_item_id'])->update([
            'quantity' => ((int) $return_data['quantity'] - (int) $return_quantity),
        ]);

        // 4 decrease quantity in warehouse_product_stock
        $db->table('warehouse_product_stock')
            ->set('stock', "stock - {$return_quantity}", false) // false = do not escape
            ->where('warehouse_id', $return_data['warehouse_id'])
            ->where('product_variant_id', $return_data['product_variant_id'])
            ->update();

        // 5 update total in purchases
        // 5.1 get purchase id
        $purchase_id = $db->table('purchases_items')->where('id', $return_data['purchase_item_id'])->get()->getRow()->purchase_id;
        // 5.2 update purchase total
        $this->update_purchase_total($purchase_id);

        $db->transComplete();
        return $db->transStatus(); // returns true on success, false on failure
    }

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

    public function update_purchase_total($purchase_id)
    {
        $db = \Config\Database::connect();

        // 1. Calculate total
        $totalRow = $db->table('purchases_items')
            ->select('SUM((price * quantity) - discount) as total')
            ->where('purchase_id', $purchase_id)
            ->get()
            ->getRow();

        $total = $totalRow && isset($totalRow->total) ? $totalRow->total : 0;

        // 2. Update total
        $builder = $db->table('purchases')->where('id', $purchase_id);
        $updateResult = $builder->update([
            'total' => $total,
        ]);

        // 3. Return true/false
        return $updateResult;
    }

    /**
     * Update warehouse_id for all batches in a purchase
     */
    public function updateWarehouseIdByPurchase($purchase_id, $new_warehouse_id)
    {
        $db = \Config\Database::connect();
        // Find all batches for this purchase (via purchases_items)
        $builder = $db->table('warehouse_batches wb');
        $builder->join('purchases_items pi', 'pi.id = wb.purchase_item_id');
        $builder->where('pi.purchase_id', $purchase_id);
        $batches = $builder->select('wb.id')->get()->getResultArray();
        $batch_ids = array_column($batches, 'id');
        if (!empty($batch_ids)) {
            $db->table('warehouse_batches')->whereIn('id', $batch_ids)->update(['warehouse_id' => $new_warehouse_id]);
        }
        return true;
    }
    public function get_expiring_batches($business_id)
    {
        try {
            if (empty($business_id)) {
                log_message('error', 'Business ID is empty in get_expiring_batches');
                throw new \Exception('Business ID is required');
            }

            $db = \Config\Database::connect();
            if (!$db) {
                log_message('error', 'Database connection failed in get_expiring_batches');
                throw new \Exception('Database connection failed');
            }

            $builder = $db->table('warehouse_batches wb');
            
            // Join with products_variants to get expiry_alert_days
            $builder->select('wb.*, p.name as product_name, pv.variant_name, pv.expiry_alert_days, w.name as warehouse_name, DATEDIFF(wb.expiration_date, CURDATE()) as days_remaining')
                    ->join('products_variants pv', 'pv.id = wb.product_variant_id')
                    ->join('products p', 'p.id = pv.product_id')
                    ->join('warehouses w', 'w.id = wb.warehouse_id')
                    ->where('wb.business_id', $business_id)
                    ->where('wb.expiration_date IS NOT NULL')
                    ->where('wb.quantity >', 0)
                    ->where('pv.expiry_alert_days IS NOT NULL')
                    ->where('pv.expiry_alert_days >', 0)
                    ->where('wb.expiration_date >', date('Y-m-d'))
                    ->where('DATEDIFF(wb.expiration_date, CURDATE()) <= pv.expiry_alert_days')
                    ->where('DATEDIFF(wb.expiration_date, CURDATE()) > 0');

            // Log the query we're about to execute
            $query = $builder->getCompiledSelect(false);
            log_message('info', 'Executing expiry query: ' . $query);

            // Execute the query
            $result = $builder->get()->getResultArray();
            
            // Log the results
            log_message('info', 'Query returned ' . count($result) . ' rows');
            if (empty($result)) {
                log_message('info', 'No expiring batches found for business_id: ' . $business_id);
            } else {
                log_message('info', 'Sample batch data: ' . json_encode(array_slice($result, 0, 1)));
            }

            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Error in get_expiring_batches: ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            throw $e;
        }
    }

    /**
     * Get available batches for a product variant in FIFO order (oldest first, quantity > 0)
     */
    public function getAvailableBatchesFIFO($product_variant_id, $business_id)
    {
        return $this->where('product_variant_id', $product_variant_id)
                    ->where('business_id', $business_id)
                    ->where('quantity >', 0)
                    ->orderBy('created_at', 'ASC')
                    ->findAll();
    }
}
