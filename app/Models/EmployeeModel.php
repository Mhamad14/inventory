<?php

namespace App\Models;
use CodeIgniter\Model;


class EmployeeModel extends Model
{

    protected $allowedFields = [
        'id',
        'busniess_id',
        'name',
        'address',
        'position_id',
        'salary',
        'contact_number',
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
 public function get_employees($business_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("employees as e");
        $builder->select('e.id,p.name as pname,e.name,e.address,e.salary,e.contact_number');
        $builder->where('e.busniess_id', $business_id);
        $builder->join('positions as p', 'p.id = e.position_id', "left");

        // Search functionality
        if (isset($_GET['search']) && $_GET['search'] != '') {
            $search = $_GET['search'];
            $multipleWhere = [
                'e.id' => $search,
                'e.name' => $search,
                'e.salary' => $search,
                'e.address' => $search,
                'e.contact_number' => $search,
                'p.name' => $search,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }

        // Get total count of all records (without pagination)
        $totalRecords = $builder->countAllResults(false);

        // Apply sorting
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'e.id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $builder->orderBy($sort, $order);

        // Apply pagination
        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $builder->limit($limit, $offset);

        // Get paginated data
        $employees = $builder->get()->getResultArray();

        return ['total' => $totalRecords, 'data' => $employees];
    }
    public function search_employees($search_term = "", $business_id = "")
    {
        // connect to the database
        $db = \Config\Database::connect();
        $builder = $db->table("employees as e");
        $builder->select('e.id,e.name,e.salary,e.contact_number,e.business_id');
        $builder->join('positions as p', 'p.id = e.position_id ', "left");
        if (!empty($search_term)) {
            $multipleWhere = [
                'e.`id`' => $search_term,
                'e.`name`' => $search_term,
                'e.`salary`' => $search_term,
                'e.`contact_number`' => $search_term,
                'p.`name`' => $search_term,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (!empty($business_id)) {
            $builder->where('e.business_id', $business_id);
        }
        return $builder->get()->getResultArray();
    }
}
