<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderDetailsView extends Migration
{
    public function up()
    {
        $this->db->query("
            CREATE OR REPLACE VIEW order_details_view AS
            SELECT 
                o.*,
                ((o.final_total - o.returns_total) - o.amount_paid) AS debt,

                -- Customer name
                (
                    SELECT u.first_name
                    FROM customers c
                    JOIN users u ON u.id = c.user_id
                    WHERE c.id = o.customer_id
                    LIMIT 1
                ) AS customer_name,

                -- Creator name
                (
                    SELECT first_name
                    FROM users
                    WHERE users.id = o.created_by
                    LIMIT 1
                ) AS creator_name,

                -- Creator role
                (
                    SELECT g.name
                    FROM users_groups ug
                    JOIN groups g ON g.id = ug.group_id
                    WHERE ug.user_id = o.created_by
                    LIMIT 1
                ) AS creator_role

            FROM orders o
        ");
    }

    public function down()
    {
        $this->db->query("DROP VIEW IF EXISTS order_details_view");
    }
}
