<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyPurchasesTable extends Migration
{
    public function up()
    {
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR', 
                'constraint' => 255,      
                'null'       => true,    
            ],
        ];

        $this->forge->modifyColumn('purchases', $fields);
    }

    public function down()
    {
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('purchases', $fields);
    }
}
