<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CreateOrderDetailsProcedure extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        // Drop the procedure if it exists
        $db->query("DROP PROCEDURE IF EXISTS GetOrderDetails");
        
        // Create the procedure
        $db->query("
            CREATE PROCEDURE GetOrderDetails(IN order_id INT)
            BEGIN
                SELECT 
                    o.*,
                    ((o.final_total - COALESCE(o.returns_total, 0)) - COALESCE(o.amount_paid, 0)) AS debt,
                    
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
        
        echo "GetOrderDetails stored procedure created successfully!\n";
    }
} 