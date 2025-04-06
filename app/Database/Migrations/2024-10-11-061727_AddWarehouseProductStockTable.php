<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWarehouseProductStockTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'warehouse_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'product_variant_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
            ],
            'stock' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'qty_alert' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],'vendor_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'business_id' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'TIMESTAMP',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);

        // Foreign Keys
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('product_variant_id', 'products_variants', 'id', 'CASCADE', 'CASCADE');


        $this->forge->createTable('warehouse_product_stock');
    }

    public function down()
    {
        $this->forge->dropTable('warehouse_product_stock');
    }
}
