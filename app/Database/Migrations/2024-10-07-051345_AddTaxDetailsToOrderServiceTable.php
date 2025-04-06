<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTaxDetailsToOrderServiceTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('orders_services', [
            'tax_details' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // You can set to false if you want it to be NOT NULL
                'after'      => 'is_tax_included',  // Position the column after 'id'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('orders_services', 'tax_details');
    }
}
