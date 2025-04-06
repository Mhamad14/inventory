<?php

namespace App\Models;

use CodeIgniter\Model;

class Status_model extends Model
{

    protected $table = 'status';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_id', 'business_id','status','operation'];

    public function get_status($business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("status");
        $builder->whereIn('business_id',[$business_id,'0']);
        return $builder->get()->getResultArray();
    }
}
