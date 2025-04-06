<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUserIdToCustomersTransactions extends Migration
{
    public function up()
    {
        // Add 'user_id' column to the 'customers_transactions' table
        $this->forge->addColumn('customers_transactions', [
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'after'      => 'id', // Position after 'id'
                'comment'    => 'References ID of the user',
            ],
            'business_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => false,
                'after'      => 'user_id', // Position after 'user_id'
                'comment'    => 'References ID of the business',
            ],
        ]);


        // Add comment for 'payment_for' column
        $this->db->query("ALTER TABLE `customers_transactions` MODIFY COLUMN `payment_for` INT(11) COMMENT '0 = sales, 1 = purchases, 2 = wallet'");

        /**
         * Updates the data in the 'customers_transactions' table to align with the new relationships and table structures.
         * This function ensures normalized and consistent data by mapping the appropriate IDs from related tables.
         *
         * Why this is done:
         * - Previously, 'customer_id' and 'supplier_id' in the 'customers_transactions' table stored the 'id' from the 'users' table.
         * - This caused ambiguity and did not follow normalized database practices.
         * - Now, 'customer_id' and 'supplier_id' reference the 'id' from the 'customers' and 'suppliers' tables, respectively.
         * - Also, 'user_id' and 'business_id' fields are added for better clarity and relationship handling.
         *
         * Steps:
         * 1. Update 'user_id' based on 'customer_id' (matching 'users.id').
         * 2. Update 'user_id' based on 'supplier_id' (matching 'users.id').
         * 3. Update 'business_id' based on 'vendor_id' (matching 'businesses.user_id').
         * 4. Update 'customer_id' to reference 'customers.id' based on 'user_id'.
         * 5. Update 'supplier_id' to reference 'suppliers.id' based on 'user_id'.
         * 6. Re-run 'business_id' update for consistency.
         */

        $db = \Config\Database::connect();

        // 1. Update 'user_id' for customer transactions
        // Maps 'user_id' in 'customers_transactions' based on 'customer_id' matching 'users.id'.
        $sql1 = "
        UPDATE customers_transactions
        JOIN users ON customers_transactions.customer_id = users.id
        SET customers_transactions.user_id = users.id
        ";
        $db->query($sql1);

            // 2. Update 'user_id' for supplier transactions
            // Maps 'user_id' in 'customers_transactions' based on 'supplier_id' matching 'users.id'.
            $sql2 = "
        UPDATE customers_transactions
        JOIN users ON customers_transactions.supplier_id = users.id
        SET customers_transactions.user_id = users.id
        ";
        $db->query($sql2);

            // 3. Update 'business_id' for vendor transactions
            // Maps 'business_id' in 'customers_transactions' based on 'vendor_id' matching 'businesses.user_id'.
            $sql3 = "
        UPDATE customers_transactions
        JOIN businesses ON customers_transactions.vendor_id = businesses.user_id
        SET customers_transactions.business_id = businesses.id
        ";
        $db->query($sql3);

            // 4. Update 'customer_id' to reference the correct 'customers' table ID
            // Maps 'customer_id' in 'customers_transactions' based on 'user_id' matching 'customers.user_id'.
        $sql4 = "
        UPDATE customers_transactions
        JOIN customers ON customers_transactions.customer_id = customers.user_id
        SET customers_transactions.customer_id = customers.id
        ";
            $db->query($sql4);

            // 5. Update 'supplier_id' to reference the correct 'suppliers' table ID
            // Maps 'supplier_id' in 'customers_transactions' based on 'user_id' matching 'suppliers.user_id'.
        $sql5 = "
        UPDATE customers_transactions
        JOIN suppliers ON customers_transactions.supplier_id = suppliers.user_id
        SET customers_transactions.supplier_id = suppliers.id
        ";
        $db->query($sql5);

            // 6. Re-run 'business_id' update for consistency (optional redundancy for safety)
            // Ensures 'business_id' is updated correctly based on 'vendor_id'.
        $sql6 = "
        UPDATE customers_transactions
        JOIN businesses ON customers_transactions.vendor_id = businesses.user_id
        SET customers_transactions.business_id = businesses.id
        ";
            $db->query($sql6);
    }

    public function down()
    {
        // Remove columns
        $this->forge->dropColumn('customers_transactions', 'user_id');
        $this->forge->dropColumn('customers_transactions', 'business_id');

        // Revert comment for 'payment_for' column if needed (optional)
        $this->db->query("ALTER TABLE `customers_transactions` MODIFY COLUMN `payment_for` INT(11) COMMENT ''");
    }
}
