<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemoveUnsignFromBrandID extends Migration
{
    public function up()
    {
        $fields = [
            'brand_id' => [
                'type'       => 'BIGINT',
                'constraint' => 20,
                'unsigned'   => false,
                'null'       => true,
            ],
        ];

        $this->forge->modifyColumn('products', $fields);
    }

    public function down()
    {
        $this->forge->modifyColumn('products', [
            'brand_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'category_id',
            ],
        ]);
    }
}
