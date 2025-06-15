<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;
use CodeIgniter\Database\RawSql;


class CreateWarehouseBatchesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true
            ],
            'purchase_item_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'business_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'product_variant_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'warehouse_id' => [
                'type' => 'INT',
                'constraint' => 10,
                'unsigned' => true,
                'null' => false
            ],
            'batch_number' => [
                'type' => 'VARCHAR',
                'constraint' => 50,
                'null' => false
            ],
            'quantity' => [
                'type' => 'INT',
                'null' => false
            ],
            'cost_price' => [
                'type' => 'FLOAT',
                'null' => false
            ],
            'expiration_date' => [
                'type' => 'DATE',
                'null' => true
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => false,
                'default' => new RawSql('CURRENT_TIMESTAMP'),
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey('batch_number', 'idx_batch_number'); // Add unique index
        $this->forge->createTable('warehouse_batches');

        // Add foreign keys
        $this->db->query('ALTER TABLE warehouse_batches 
            ADD CONSTRAINT fk_business 
            FOREIGN KEY (business_id) REFERENCES businesses(id)');

        $this->db->query('ALTER TABLE warehouse_batches 
            ADD CONSTRAINT fk_product_variant 
            FOREIGN KEY (product_variant_id) REFERENCES products_variants(id)');

        $this->db->query('ALTER TABLE warehouse_batches 
            ADD CONSTRAINT fk_purchase_items 
            FOREIGN KEY (purchase_item_id) REFERENCES purchases_items(id)');

        $this->db->query('ALTER TABLE warehouse_batches 
            ADD CONSTRAINT fk_warehouse 
            FOREIGN KEY (warehouse_id) REFERENCES warehouses(id)');
    }

    public function down()
    {
        $this->forge->dropTable('warehouse_batches');
    }
}
