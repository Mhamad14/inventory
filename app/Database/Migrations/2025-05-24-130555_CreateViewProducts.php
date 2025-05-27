<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateViewProducts extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE OR REPLACE VIEW view_product_details AS
            SELECT 
                p.id AS product_id,
                p.name AS product_name,
                p.description,
                p.image,
                p.stock,
                p.status,
                p.category_id,
                p.business_id,
                categories.name AS category_name,
                brands.name AS brand_name,
                u.first_name AS creator,
                p.brand_id
            FROM products p
            LEFT JOIN brands ON brands.id = p.brand_id
            LEFT JOIN categories ON categories.id = p.category_id
            LEFT JOIN users u ON u.id = p.vendor_id
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS view_product_details");
    }
}
