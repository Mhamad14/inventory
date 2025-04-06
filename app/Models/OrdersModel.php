<?php

namespace App\Models;

use CodeIgniter\Model;

class OrdersModel extends Model
{
    protected $table = 'orders';
    protected $primaryKey = 'id';
    protected $allowedFields = ['returns_total', 'final_total'];
    protected $returnTable = 'order_returns';
    protected $itemsTable = 'order_items';
    
    public function get_order_by_id($order_id)
    {
        if (empty($order_id)) {
            return null;
        
        }
        return $this->db->table($this->table)
            ->where('id', (int)$order_id)
            ->get()
            ->getRowArray();
    }
    
    public function get_order_item($item_id)
    {
        if (empty($item_id)) {
            return null;
        }
        
        return $this->db->table($this->itemsTable)
            ->where('id', (int)$item_id)
            ->get()
            ->getRowArray();
    }
    
    public function get_returned_quantity($item_id)
    {
        if (empty($item_id)) {
            return 0;
        }
        
        $result = $this->db->table($this->returnTable)
            ->selectSum('quantity')
            ->where('item_id', (int)$item_id)
            ->get()
            ->getRowArray();
            
        return (int)($result['quantity'] ?? 0);
    }
    
    public function insert_return_items($return_items)
    {
        if (empty($return_items)) {
            return false;
        }
        
        try {
            return $this->db->table($this->returnTable)
                ->insertBatch($return_items);
        } catch (\Exception $e) {
            log_message('error', 'Failed to insert return items: ' . $e->getMessage());
            return false;
        }
    }
    
    public function update_order($data, $order_id)
    {
        if (empty($data) || empty($order_id)) {
            return false;
        }
        
        try {
            return $this->db->table($this->table)
                ->where('id', (int)$order_id)
                ->update($data);
        } catch (\Exception $e) {
            log_message('error', 'Failed to update order: ' . $e->getMessage());
            return false;
        }
    }
}