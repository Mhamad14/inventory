<?php

namespace App\Models;
use CodeIgniter\Model;


class EmployeeModel extends Model
{

    protected $allowedFields = [
        'id',
        'business_id',
        'name',
        'email',
        'position',
        'salary',
        'created_at',
        'updated_at'
    ]; // Fields that can be inserted/updated
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected bool $allowEmptyInserts = false;
    protected bool $updateOnlyChanged = true;

    protected array $casts = [];
    protected array $castHandlers = [];

    // Dates
    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules      = [];
    protected $validationMessages   = [];
    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];



    public function getEmployeePositions($employeeId)
{
    return $this->db->table('positions')
        ->join('employees', 'employees.position_id = positions.id')
        ->where('employees.id', $employeeId)
        ->get()
        ->getResultArray();
}
}
