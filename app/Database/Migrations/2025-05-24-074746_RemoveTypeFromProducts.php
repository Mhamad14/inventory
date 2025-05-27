<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveTypeFromProducts extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('products', ['type','stock_management', 'is_tax_included', 'qty_alert','unit_id']);
    }

    public function down()
    {
         $this->forge->addColumn('products', [
            'type' => [
                'type' => 'FLOAT',
                'null' => false,
                'after' => 'image',
            ],
              'qty_alert' => [
                'type' => 'INT',
                'null' => false,
                'after' => 'description',
              ],
                'stock_management' => [
                'type' => 'INT',
                'null' => false,
                'after' => 'type',
                ],
                  'is_tax_included' => [
                'type' => 'INT',
                'null' => false,
                'after' => 'unit_id',
                  ],
                    'unit_id' => [
                'type' => 'INT',
                'null' => false,
                'after' => 'stock',
            ]

        ]);
    }
}
