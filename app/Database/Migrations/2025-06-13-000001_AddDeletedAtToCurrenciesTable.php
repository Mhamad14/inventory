<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDeletedAtToCurrenciesTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('currencies', [
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'updated_at'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('currencies', 'deleted_at');
    }
} 