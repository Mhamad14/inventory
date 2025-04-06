<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Add_Order_No_To_OrdersTable extends Migration
{
    public function up()
    {
        // Add the 'order_no' column to the 'orders' table
        $this->forge->addColumn('orders', [
            'order_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,  // You can set to false if you want it to be NOT NULL
                'after'      => 'customer_id',  // Position the column after 'id'
            ],
        ]);
    }

    public function down()
    {
        // Remove the 'order_no' column
        $this->forge->dropColumn('orders', 'order_no');
    }
}
