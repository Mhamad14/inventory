<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBusinessIdToPositions extends Migration
{
    public function up()
    {
        $fields = [
            'business_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'after' => 'id',
            ],
            'description' => [
                'type' => 'varchar',
                'constraint' => '255',
                'null' => true,
                'after' => 'id',
            ],
        ];
        $this->forge->addColumn('positions', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('positions', 'business_id');
    }
}
