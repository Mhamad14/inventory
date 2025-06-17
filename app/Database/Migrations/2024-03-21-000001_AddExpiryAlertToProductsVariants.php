<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddExpiryAlertToProductsVariants extends Migration
{
    public function up()
    {
        $this->forge->addColumn('products_variants', [
            'expiry_alert_days' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => true,
                'default' => null,
                'after' => 'qty_alert',
                'comment' => 'Number of days before expiry to send alert'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('products_variants', 'expiry_alert_days');
    }
} 