<?php

namespace App\Models;

use CodeIgniter\Model;

class Purchases_items_model extends Model
{

    protected $table = 'purchases_items';
    protected $primaryKey = 'id';
    protected $allowedFields = ['purchase_id','product_variant_id','quantity','price','discount','status'];
}
