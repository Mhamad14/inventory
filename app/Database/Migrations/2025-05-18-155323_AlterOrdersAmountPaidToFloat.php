<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AlterOrdersAmountPaidToFloat extends Migration
{
    public function up()
    {
        // In your controller or model
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE orders MODIFY amount_paid FLOAT not null default 0");
    }

    public function down()
    {
        // In your controller or model
        $db = \Config\Database::connect();
        $db->query("ALTER TABLE orders MODIFY amount_paid DOUBLE not null default 0");
    }
}
