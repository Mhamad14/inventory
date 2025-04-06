<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWarehouseIdToPurchasesTable extends Migration
{
    public function up()
    {
        
        $this->forge->addColumn('purchases', [
            'warehouse_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'supplier_id',
            ],
        ]);
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropForeignKey('purchases','warehouse_id');
        $this->forge->dropColumn('purchases','warehouse_id');
    }
}
