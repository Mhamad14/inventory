<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ModifyOrdersTable extends Migration
{
    public function up()
    {
        //payment_method 
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('orders', $fields);
    }

    public function down()
    {
        //
        $fields = [
            'payment_method' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => false,
            ],
        ];

        $this->forge->modifyColumn('orders', $fields);
    }
}
