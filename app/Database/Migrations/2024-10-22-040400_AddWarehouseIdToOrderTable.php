<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWarehouseIdToOrderTable extends Migration
{
    public function up()
    {

        $this->forge->addColumn('orders', [
            'warehouse_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'customer_id',
            ],
        ]);
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('orders', 'warehouse_id');
        $this->forge->dropColumn('orders', 'warehouse_id');
    }
}
