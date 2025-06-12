<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrdersItemsView extends Migration
{
    public function up()
    {
        $this->db->query("DROP VIEW IF EXISTS order_items_view");
        $sql = "CREATE OR REPLACE VIEW order_items_view AS
               SELECT 
                    oi.id as orders_items_id,
                    c.name as category,
                    p.name as brand,
                    p.image,
                    w.id as warehouse_id,
                    w.name as warehouse_name,
                    oi.product_name,
                    oi.quantity,
                    oi.price,
                    oi.returned_quantity,
                    oi.order_id,
                    oi.product_id,
                    oi.product_variant_id,
                    s.status,
                    oi.status as status_id
                FROM orders_items oi
                LEFT JOIN products p ON oi.product_id = p.id
                LEFT JOIN products_variants pv ON oi.product_variant_id = pv.id
                LEFT JOIN categories c ON p.category_id = c.id
                left JOIN status s on s.id = oi.status
                LEFT JOIN warehouses w on w.id = oi.warehouse_id
                ";

        $this->db->query($sql);
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS order_items_view");
    }
}
