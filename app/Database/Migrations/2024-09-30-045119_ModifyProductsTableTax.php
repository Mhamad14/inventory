<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyProductsTableTax extends Migration
{
    public function up()
    {

        // Alter the existing 'tax_id' column to become 'tax_ids' with a VARCHAR type
        $fields = [
            'tax_id' => [
                'name'       => 'tax_ids', // Renaming the column to tax_ids
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // Allow NULL, adjust as per your requirements
            ],
        ];

        // Modify the table to apply the changes
        $this->forge->modifyColumn('products', $fields);
    }

    public function down()
    {
        // Alter the 'tax_ids' column back to 'tax_id' with INT type
        $fields = [
            'tax_ids' => [
                'name'       => 'tax_id', // Renaming it back to tax_id
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // Adjust as per your requirements
            ],
        ];

        // Modify the table to revert the changes
        $this->forge->modifyColumn('products', $fields);

        // You may also want to add the foreign key constraint back
        $this->db->query("ALTER TABLE products ADD CONSTRAINT tax_id FOREIGN KEY (tax_id) REFERENCES tax(id)");
    }
}
