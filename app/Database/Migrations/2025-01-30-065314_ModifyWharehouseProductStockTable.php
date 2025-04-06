<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyWharehouseProductStockTable extends Migration
{
    public function up()
    {
        $fields = [
            'stock' => [
                'type'       => 'DOUBLE',
                'default'    => 0,
            ],
            'qty_alert' => [
                'type'       => 'DOUBLE',
                'default'    => 0,
            ]
        ];

        $this->forge->modifyColumn('warehouse_product_stock', $fields);
    }

    public function down()
    {
        $fields = [
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'qty_alert' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ]
        ];

        $this->forge->modifyColumn('warehouse_product_stock', $fields);
    }
}
