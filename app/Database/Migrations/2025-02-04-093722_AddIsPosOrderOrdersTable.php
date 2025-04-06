<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIsPosOrderOrdersTable extends Migration
{
    public function up()
    {
        $fields = [
            'is_pos_order' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'null'       => false,
                'default'    => 0, // Default to 0 (false)
            ],
        ];
        $this->forge->addColumn('orders', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('orders', 'is_pos_order');
    }
}
