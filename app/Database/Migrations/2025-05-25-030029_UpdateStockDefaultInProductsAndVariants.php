<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateStockDefaultInProductsAndVariants extends Migration
{
    public function up()
    {
         // Update 'products' table
        $this->forge->modifyColumn('products', [
            'stock' => [
                'type'       => 'INT',
                'default'    => 0,
                'null'       => false,
            ],
        ]);

        // Update 'products_variants' table
        $this->forge->modifyColumn('products_variants', [
            'stock' => [
                'type'       => 'INT',
                'default'    => 0,
                'null'       => false,
            ],
        ]);
    }

    public function down()
    {
         $this->forge->modifyColumn('products', [
            'stock' => [
                'type' => 'INT',
                'null' => true,
                'default' => null,
            ],
        ]);

        // Revert 'products_variants' table
        $this->forge->modifyColumn('products_variants', [
            'stock' => [
                'type' => 'INT',
                'null' => true,
                'default' => null,
            ],
        ]);
    }
}
