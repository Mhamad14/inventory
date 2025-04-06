<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ChangeTaxIdToTaxIdsToServiceTable extends Migration
{
    public function up()
    {
        $fields = [
            'tax_id' => [
                'name'       => 'tax_ids', // Renaming the column to tax_ids
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // Allow NULL, adjust as per your requirements
            ],
        ];

        $this->forge->modifyColumn('services', $fields);
    }

    public function down()
    {
        $fields = [
            'tax_ids' => [
                'name'       => 'tax_id', // Renaming it back to tax_id
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true, // Adjust as per your requirements
            ],
        ];

        // Modify the table to revert the changes
        $this->forge->modifyColumn('services', $fields);
    }
}
