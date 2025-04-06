<?php

namespace App\Models;

use CodeIgniter\Model;

class Tax_model extends Model
{

    protected $table = 'tax';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'percentage','status'];

    public function count_of_tax()
    {
        $db      = \Config\Database::connect();
        $builder = $db->table("tax");
        $builder->select('COUNT(tax.id) as `total`');
        $tax = $builder->get()->getResultArray();
        return $tax;
    }
}