<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWarehouseBatchesReturnsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'purchase_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'business_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'product_variant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'warehouse_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false,
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false,
            ],
            'quantity' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
            ],
            'cost_price' => [
                'type' => 'FLOAT',
                'null' => false,
            ],
            'return_price' => [
                'type' => 'FLOAT',
                'null' => true,
            ],
            'return_reason' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'return_date' => [
                'type' => 'DATE',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => false,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addForeignKey('business_id', 'businesses', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('product_variant_id', 'products_variants', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('purchase_item_id', 'purchases_items', 'id', '', 'CASCADE');
        $this->forge->addForeignKey('warehouse_id', 'warehouses', 'id', '', 'CASCADE');
        $this->forge->createTable('warehouse_batches_returns');
        $this->db->query("ALTER TABLE warehouse_batches_returns MODIFY created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP");
    }

    public function down()
    {
        $this->forge->dropTable('warehouse_batches_returns');
    }
}
