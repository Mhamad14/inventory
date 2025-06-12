<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCurrenciesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'               => ['type' => 'INT', 'unsigned' => true, 'auto_increment' => true],
            'business_id'      => ['type' => 'INT', 'unsigned' => true],
            'code'             => ['type' => 'VARCHAR', 'constraint' => 3, 'comment' => 'ISO 4217 code (IQD, USD, etc)'],
            'name'             => ['type' => 'VARCHAR', 'constraint' => 50],
            'symbol'           => ['type' => 'VARCHAR', 'constraint' => 10],
            'symbol_position'  => ['type' => 'TINYINT', 'default' => 0, 'comment' => '0 = before, 1 = after'],
            'decimal_places'   => ['type' => 'TINYINT', 'default' => 2],
            'is_base'          => ['type' => 'TINYINT', 'default' => 0, 'comment' => '1 = base currency (IQD)'],
            'status'           => ['type' => 'TINYINT', 'default' => 1],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey(['business_id', 'code']);
        $this->forge->createTable('currencies');
    }

    public function down()
    {
        $this->forge->dropTable('currencies');
    }
}
