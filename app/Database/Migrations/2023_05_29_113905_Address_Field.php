<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Address_Field extends Migration
{
    public function up()
    {
        $fields = [
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'after' => 'email', 
                'null' => true, 
            ],
        ];
        $this->forge->addColumn('users', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['address']);
    }
}
