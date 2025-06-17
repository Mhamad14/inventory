<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class ImproveExchangeRate extends Migration
{
    public function up()
    {
        /** @var Connection $db */
        $db = $this->db;

        // 1. Check if column exists (alternative method)
        $columns = $db->getFieldNames('exchange_rates');
        if (in_array('effective_date', $columns)) {
            $db->query("ALTER TABLE exchange_rates 
                       MODIFY COLUMN effective_date DATETIME NOT NULL 
                       COMMENT 'Exact datetime when rate became effective'");
        }

        // 2. Add index (safe check)
        $indexes = $db->getIndexData('exchange_rates');
        $hasIndex = false;
        foreach ($indexes as $index) {
            if (isset($index->fields) && in_array('effective_date', $index->fields)) {
                $hasIndex = true;
                break;
            }
        }
        if (!$hasIndex) {
            $this->forge->addKey('effective_date');
        }

        // 3. Add foreign key (safe check)
        $foreignKeys = $db->getForeignKeyData('exchange_rates');
        $hasForeignKey = false;
        foreach ($foreignKeys as $fk) {
            if ($fk->constraint_name === 'exchange_rates_currency_id_foreign') {
                $hasForeignKey = true;
                break;
            }
        }
        if (!$hasForeignKey) {
            $this->forge->addForeignKey(
                'currency_id',
                'currencies',
                'id',
                'CASCADE',
                'CASCADE',
                'exchange_rates_currency_id_foreign'
            );
        }
    }

    public function down()
    {
        /** @var Connection $db */
        $db = $this->db;

        // 1. Remove foreign key if exists
        try {
            $this->forge->dropForeignKey('exchange_rates', 'exchange_rates_currency_id_foreign');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        // 2. Remove index if exists
        try {
            $this->forge->dropKey('exchange_rates', 'effective_date');
        } catch (\Exception $e) {
            // Ignore if doesn't exist
        }

        // 3. Revert to DATE type
        $columns = $db->getFieldNames('exchange_rates');
        if (in_array('effective_date', $columns)) {
            $this->forge->modifyColumn('exchange_rates', [
                'effective_date' => [
                    'type' => 'DATE',
                    'null' => false,
                    'comment' => 'Date when rate became effective'
                ]
            ]);
        }
    }
}
