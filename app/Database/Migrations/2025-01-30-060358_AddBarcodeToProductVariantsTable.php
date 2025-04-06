<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddBarcodeToProductVariantsTable extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products_variants', [
            'barcode' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true, // Set to false if barcode is required
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products_variants', 'barcode');
    }
}
