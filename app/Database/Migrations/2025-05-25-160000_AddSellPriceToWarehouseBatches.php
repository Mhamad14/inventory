<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSellPriceToWarehouseBatches extends Migration
{
    public function up()
    {
        $this->forge->addColumn('warehouse_batches', [
            'sell_price' => [
                'type'       => 'FLOAT',
                'null'       => true,
                'after'      => 'cost_price' // Optional: place it after an existing column
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('warehouse_batches', 'sell_price');
    }
}
