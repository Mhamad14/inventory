<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class User_permissions extends Migration
{
   
    public function up()
    {
        
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'auto_increment' => true,
            ],
            'user_id' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'role' => [
                'type' => 'INT',
                'constraint' => '11',
                'null' => false
            ],
            'permissions' => [
                'type' => 'TEXT',
                'constraint' => '1048',
                'null' => false
            ],
            
            'created_by' =>
            [
                'type' => 'TIMESTAMP'
            ],
            'created_by TIMESTAMP on update CURRENT_TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
           
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('user_permissions');

    }

    public function down()
    {
        $this->forge->dropTable('user_permissions');
       
    }
}


