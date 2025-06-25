<?php

namespace App\Models;

use CodeIgniter\Model;

class ExchangeRatesModel extends Model
{
    protected $table            = 'exchange_rates';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    protected $allowedFields    = [
        'id',
        'currency_id',
        'rate',
        'effective_date',
        'created_at',
        'deleted_at'
    ];

    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // define validation rules
    // and messages
    protected $validationRules      = [
        'currency_id'     => 'required|integer',
        // make rate all numbers, including decimals

        'rate'            => 'required|decimal',
        'effective_date'  => 'required|valid_date',
        'created_at'      => 'permit_empty|valid_date',
        'deleted_at'      => 'permit_empty|valid_date'
    ];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    public function getLatestRates(array $currencies): array
    {
        $rates = [];

        foreach ($currencies as $currency) {
            if (!($currency['is_base'] ?? false)) {
                $rate = $this->where('currency_id', $currency['id'])
                    ->orderBy('effective_date', 'DESC')
                    ->first();

                if ($rate) {
                    $rates[$currency['id']] = $rate['rate'];
                } else {
                    log_message('warning', 'No exchange rate found for currency ID: ' . $currency['id']);
                }
            }
        }

        return $rates;
    }
}
