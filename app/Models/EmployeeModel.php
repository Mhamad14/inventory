<?php

namespace App\Models;

use CodeIgniter\Model;

class EmployeeModel extends Model
{
    protected $table            = 'employees';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

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
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function edit_employee($employee_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("employees as e");
        $builder->select('e.id,p.name as pname,e.name,e.address,e.salary,e.contact_number,e.position_id');
        $builder->where('e.id', $employee_id);
        $builder->where('e.deleted_at', null);
        $builder->join('positions as p', 'p.id = e.position_id', "left");
        return $builder->get()->getResultArray();
    }

    public function get_employees($business_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("employees as e");
        $builder->select('e.id,p.name as pname,e.name,e.address,e.salary,e.contact_number,e.position_id');
        $builder->where('e.busniess_id', $business_id);
        $builder->where('e.deleted_at', null);
        $builder->join('positions as p', 'p.id = e.position_id', "left");

        $totalRecords = $builder->countAllResults(false);

        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'e.id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $builder->orderBy($sort, $order);

        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $builder->limit($limit, $offset);

        $employees = $builder->get()->getResultArray();

        return ['total' => $totalRecords, 'data' => $employees];
    }

    public function search_employees($search_term = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("employees as e");
        $builder->select('e.id,e.name,e.salary,e.contact_number,e.business_id');
        $builder->join('positions as p', 'p.id = e.position_id ', "left");
        if (!empty($search_term)) {
            $multipleWhere = [
                'e.id' => $search_term,
                'e.name' => $search_term,
                'e.salary' => $search_term,
                'e.contact_number' => $search_term,
                'p.name' => $search_term,
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
