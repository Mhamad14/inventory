<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdersModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['returns_total', 'final_total','order_id','item_id','quantity','price','total','return_date','return_reason','status','processed_by','processed_date'];
    protected $returnTable = 'order_returns';
    protected $itemsTable = 'order_items';
    
    public function get_order_by_id($order_id)
    {
        if (empty($order_id)) {
            return null;
        }
        
        try {
            $query = $this->db->query("SELECT * FROM {$this->table} WHERE id = ?", [(int)$order_id]);
            $result = $query->getRowArray();
            log_message('debug', "Order query result: " . ($result ? 'Found' : 'Not found'));
            return $result;
        } catch (\Exception $e) {
            log_message('error', "Error getting order: " . $e->getMessage());
            return null;
        }
    }
    
    public function get_order_item($item_id)
    {
        if (empty($item_id)) {
            return null;
        }
        
        try {
            $query = $this->db->query("SELECT * FROM {$this->itemsTable} WHERE id = ?", [(int)$item_id]);
            $result = $query->getRowArray();
            log_message('debug', "Item query result: " . ($result ? 'Found' : 'Not found'));
            return $result;
        } catch (\Exception $e) {
            log_message('error', "Error getting order item: " . $e->getMessage());
            return null;
        }
    }
    
    public function insert_return_items($return_items)
    {
        if (empty($return_items)) {
            return false;
        }
        
        try {
            // Use a simple insert to avoid field name issues
            $keys = array_keys($return_items[0]);
            $columns = implode(', ', $keys);
            
            $insertValues = [];
            $placeholders = [];
            
            foreach ($return_items as $item) {
                $itemPlaceholders = [];
                foreach ($keys as $key) {
                    $insertValues[] = $item[$key];
                    $itemPlaceholders[] = '?';
                }
                $placeholders[] = '(' . implode(', ', $itemPlaceholders) . ')';
            }
            
            $sql = "INSERT INTO {$this->returnTable} ({$columns}) VALUES " . implode(', ', $placeholders);
            log_message('debug', "Insert SQL: {$sql}");
            log_message('debug', "Insert values: " . print_r($insertValues, true));
            
            $result = $this->db->query($sql, $insertValues);
            return $result;
        } catch (\Exception $e) {
            log_message('error', 'Failed to insert return items: ' . $e->getMessage());
            return false;
        }
    }



public function get_returned_quantity($item_id) 
{
    $db = \Config\Database::connect();
    $builder = $db->table('order_returns');
    $builder->selectSum('quantity');
    $builder->where('item_id', $item_id);
    $result = $builder->get()->getRowArray();
    return ($result && isset($result['quantity'])) ? $result['quantity'] : 0;
}



public function update_order($data, $order_id) 
{
    $db = \Config\Database::connect();
    $builder = $db->table('orders');
    $builder->where('id', $order_id);
    return $builder->update($data);
}
// Add this method to your existing OrdersModel
public function updateOrderTotals($order_id, $return_amount)
{
    $db = \Config\Database::connect();
    $db->transStart();
    
    try {
        // Get current order values
        $order = $this->find($order_id);
        if (!$order) {
            throw new \RuntimeException('Order not found');
        }

        // Calculate new totals
        $new_returns_total = ($order['returns_total'] ?? 0) + $return_amount;
        $new_final_total = $order['final_total'] - $return_amount;

        // Update order
        $update_data = [
            'returns_total' => $new_returns_total,
            'final_total' => $new_final_total
        ];

        $this->update($order_id, $update_data);
        
        $db->transComplete();
        
        return $db->transStatus();
    } catch (\Exception $e) {
        $db->transRollback();
        log_message('error', 'Failed to update order totals: ' . $e->getMessage());
        return false;
    }
}
}