<?php

namespace App\Models;

use CodeIgniter\Model;

class Orders_services_model extends Model
{

    protected $table = 'orders_services';
    protected $primaryKey = 'id';
    protected $allowedFields = ['order_id', 'service_id', 'service_name', 'price','quantity','unit_name','unit_id','sub_total','tax_name','tax_percentage','is_tax_included','tax_details'  ,'is_recursive','recurring_days','starts_on','ends_on','status','delivery_boy'];

    function get_services($order_id = "")
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("orders_services");
        $builder->where("order_id",$order_id);
        return $builder->get()->getResultArray();
    }


}