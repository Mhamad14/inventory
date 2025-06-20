<?php

namespace App\Models;

use CodeIgniter\Model;

class Payments_model extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'order_id',
        'purchase_id',
        'currency_id',
        'amount',
        'converted_iqd',
        'rate_at_payment',
        'payment_type',
        'paid_at',
    ];

    public function getPaymentsByPurchase($purchase_id)
    {
        return $this->where('purchase_id', $purchase_id)->findAll();
    }

    public function addPayment($data)
    {
        return $this->insert($data);
    }
} 