<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateWarehouseBatchesTriggersStock extends Migration
{
    public function up()
    {
        // Drop existing triggers to avoid duplication error
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_batch_delete');
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_batch_insert');
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_update');

        $trigger1 = <<<SQL
    CREATE OR REPLACE TRIGGER update_product_variant_stock_after_batch_delete
    AFTER DELETE ON warehouse_batches
    FOR EACH ROW
    BEGIN
        UPDATE products_variants
        SET stock = (
            SELECT IFNULL(SUM(quantity), 0)
            FROM warehouse_batches
            WHERE product_variant_id = OLD.product_variant_id
        )
        WHERE id = OLD.product_variant_id;
    END;
    SQL;

        $trigger2 = <<<SQL
    CREATE TRIGGER update_product_variant_stock_after_batch_insert
    AFTER INSERT ON warehouse_batches
    FOR EACH ROW
    BEGIN
        UPDATE products_variants
        SET stock = (
            SELECT IFNULL(SUM(quantity), 0)
            FROM warehouse_batches
            WHERE product_variant_id = NEW.product_variant_id
        )
        WHERE id = NEW.product_variant_id;
    END;
    SQL;

        $trigger3 = <<<SQL
    CREATE TRIGGER update_product_variant_stock_after_update
    AFTER UPDATE ON warehouse_batches
    FOR EACH ROW
    BEGIN
        UPDATE products_variants
        SET stock = (
            SELECT IFNULL(SUM(quantity), 0)
            FROM warehouse_batches
            WHERE product_variant_id = NEW.product_variant_id
        )
        WHERE id = NEW.product_variant_id;
    END;
    SQL;

        $this->db->query($trigger1);
        $this->db->query($trigger2);
        $this->db->query($trigger3);
    }

    public function down()
    {
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_batch_delete');
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_batch_insert');
        $this->db->query('DROP TRIGGER IF EXISTS update_product_variant_stock_after_update');
    }
}
