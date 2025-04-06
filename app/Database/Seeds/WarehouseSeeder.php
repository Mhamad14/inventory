<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class WarehouseSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'id' => '1',
            'vendor_id' => '0',
            'business_id'    => '0',
            'name'=> 'Default Warehouse',
            'country' => 'Default Country',
            'city' => 'Default City',
            'zip_code' => '0000000',
            'address' => 'Default Warehouse Address'
        ];

        // Using Query Builder
        $this->db->table('warehouses')->insert($data);
    }
}
