<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEmployeesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => false,
            ],
            'position_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'address' => [
                'type' => 'VARCHAR',
                'constraint' => 150,
                'null' => false
            ],
            'contact_number' => [
                'type' => 'VARCHAR',
                'constraint' => 15,
                'null' => false
            ],
            'salary' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'busniess_id' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'deleted_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('employees');
    $this->forge->addForeignKey('position_id', 'positions', 'id', 'CASCADE', 'CASCADE');
    }

    public function down()
    {
        $this->forge->dropTable('employees');
        $this->forge->dropForeignKey('employees', 'position_id');
    }
}
