<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExchangeRatesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'INT', 'auto_increment' => true],
            'currency_id'    => ['type' => 'INT', 'null' => false],
            'rate'           => ['type' => 'DOUBLE', 'null' => false], // 1 currency = x IQD
            'effective_date' => ['type' => 'DATE', 'null' => false],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('exchange_rates');
    }

    public function down()
    {
        $this->forge->dropTable('exchange_rates');
    }
}
