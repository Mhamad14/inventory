<?php
namespace App\Models;

use CodeIgniter\Model;

class OrderReturnsModel extends Model
{
    protected $table = 'order_returns';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id', 
        'item_id',
        'quantity',
        'price',
        'total',
        'return_date',
        'return_reason',
        'status',
        'processed_by',
        
    ];
    protected $useTimestamps = false;
    //protected $createdField = 'created_at';
    //protected $updatedField = 'updated_at';

    public function getReturnedQuantity($item_id)
    {
        return $this->where('item_id', $item_id)
                   ->selectSum('quantity')
                   ->get()
                   ->getRow()->quantity ?? 0;
    }
}