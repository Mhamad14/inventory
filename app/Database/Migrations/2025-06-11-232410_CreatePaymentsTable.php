<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePaymentsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'              => ['type' => 'INT', 'auto_increment' => true],
            'order_id'        => ['type' => 'INT', 'null' => true],
            'purchase_id'     => ['type' => 'INT', 'null' => true],
            'currency_id'     => ['type' => 'INT', 'null' => false],
            'amount'          => ['type' => 'DOUBLE', 'null' => false],
            'converted_iqd'   => ['type' => 'DOUBLE', 'null' => false],
            'rate_at_payment' => ['type' => 'DOUBLE', 'null' => false],
            'payment_type'    => ['type' => 'VARCHAR', 'constraint' => 64],
            'paid_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('payments');
    }

    public function down()
    {
        $this->forge->dropTable('payments');
    }
}
