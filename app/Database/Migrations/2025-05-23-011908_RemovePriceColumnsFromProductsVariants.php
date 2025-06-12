<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RemovePriceColumnsFromProductsVariants extends Migration
{
    public function up()
    {
        $this->forge->dropColumn('products_variants', ['sale_price', 'purchase_price']);
    }

    public function down()
    {
        $this->forge->addColumn('products_variants', [
            'sale_price' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'variant_name',
            ],
            'purchase_price' => [
                'type' => 'FLOAT',
                'null' => true,
                'after' => 'sale_price',
            ],
        ]);
    }
}
