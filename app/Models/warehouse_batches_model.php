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
            wb.quantity, wb.cost_price, wb.sell_price,pi.discount, wb.expiration_date, pi.discount, s.status, pi.status as status_id, wb.warehouse_id'
        );
        $builder->join('purchases_items as pi', 'pi.id = wb.purchase_item_id');
        $builder->join('products_variants as pv', 'pv.id = wb.product_variant_id');
        $builder->join('products as p', 'p.id = pv.product_id');
        $builder->join('status as s', 's.id = pi.status');

        if (!empty($purchase_id)) {
            $builder->where(['pi.purchase_id' => $purchase_id]);
        }

        $result = $builder->get()->getResultArray();
        return $result;
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
        return $result;
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


        // 1- delete batch
        $db->table('warehouse_batches')->where('id', $batch_id)->delete();

        // 2- delete purchase item
        $db->table('purchases_items')->where('id', $purchase_item_id)->delete();

        // update purchase total
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

        // 2. find total
        $total = $db->table('purchases_items')
            ->select('SUM((price * quantity) - discount) as total')
            ->where('purchase_id', $purchase_id)
            ->get()
            ->getRow()
            ->total;

        // 3. update total
        $db->table('purchases')->where('id', $purchase_id)->update([
            'total' => $total ?? 0, // if no value then total = 0
        ]);
    }
}
