<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBrandToProductsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products', [
            'brand_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'after'      => 'category_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products', 'brand_id');
    }
}
