<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSpPartialCustomerPayment extends Migration
{
    public function up()
    {
        $this->db->query("DROP PROCEDURE IF EXISTS sp_PartialCustomerPayment");

        $this->db->query("
            CREATE PROCEDURE sp_PartialCustomerPayment(
                IN in_payment_amount DECIMAL(10,2),
                IN in_customer_id INT,
                IN in_business_id INT
            )
            BEGIN
                DECLARE done INT DEFAULT 0;
                DECLARE v_order_id INT;
                DECLARE v_final_total DECIMAL(10,2);
                DECLARE v_amount_paid DECIMAL(10,2);
                DECLARE v_remaining DECIMAL(10,2);

                DECLARE cur CURSOR FOR 
                    SELECT id, final_total, amount_paid
                    FROM orders
                    WHERE customer_id = in_customer_id
                      AND business_id = in_business_id
                      AND payment_status IN ('unpaid', 'partially_paid')
                    ORDER BY created_at ASC;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO v_order_id, v_final_total, v_amount_paid;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    SET v_remaining = v_final_total - v_amount_paid;

                    IF in_payment_amount >= v_remaining THEN
                        UPDATE orders
                        SET amount_paid = final_total,
                            payment_status = 'fully_paid'
                        WHERE id = v_order_id;

                        SET in_payment_amount = in_payment_amount - v_remaining;
                    ELSE
                        UPDATE orders
                        SET amount_paid = amount_paid + in_payment_amount,
                            payment_status = 'partially_paid'
                        WHERE id = v_order_id;

                        SET in_payment_amount = 0;
                        LEAVE read_loop;
                    END IF;
                END LOOP;

                CLOSE cur;
            END
        ");
    }

    public function down()
    {
        $this->db->query("DROP PROCEDURE IF EXISTS sp_PartialCustomerPayment");
    }
}
