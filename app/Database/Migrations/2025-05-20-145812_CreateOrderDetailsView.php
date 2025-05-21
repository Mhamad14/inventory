<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateOrderDetailsView extends Migration
{
    public function up()
    {
        $this->db->query("
            DROP PROCEDURE IF EXISTS getOrderDetails;
        ");

        $this->db->query("
            CREATE PROCEDURE getOrderDetails(IN order_id INT)
            BEGIN
                SELECT 
                    o.*,
                    ((o.final_total - o.returns_total) - o.amount_paid) AS debt,
                    
                    (SELECT u.first_name 
                     FROM customers c 
                     JOIN users u ON u.id = c.user_id 
                     WHERE c.id = o.customer_id 
                     LIMIT 1) AS customer_name,

                    (SELECT first_name 
                     FROM users 
                     WHERE users.id = o.created_by 
                     LIMIT 1) AS creator_name,

                    (SELECT g.name 
                     FROM users_groups ug 
                     JOIN groups g ON g.id = ug.group_id 
                     WHERE ug.user_id = o.created_by 
                     LIMIT 1) AS creator_role

                FROM orders o
                WHERE o.id = order_id;
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP PROCEDURE IF EXISTS getOrderDetails");
    }
}
