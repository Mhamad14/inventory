<?php

namespace App\Models;

use CodeIgniter\Model;

class PositionsModel extends Model
{
    protected $table            = 'positions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'id',
        'description',
        'business_id',
        'name',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = false;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    public function edit_position($position_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("positions as p");
        $builder->select('p.id, p.description, p.business_id,p.name');
        $builder->where('p.deleted_at', null);
        $builder->where('p.id', $position_id);
        return $builder->get()->getResultArray();
    }

    public function get_positions($business_id = '')
    {
        $db = \Config\Database::connect();
        $builder = $db->table("positions as p");
        $builder->select('p.id, p.description, p.business_id,p.name');
        $builder->where('p.business_id', $business_id);
        $builder->where('p.deleted_at', null);
        $builder->where('p.deleted_at', null);


        $totalRecords = $builder->countAllResults(false);

        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'p.id';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        $builder->orderBy($sort, $order);

        $offset = isset($_GET['offset']) ? $_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
        $builder->limit($limit, $offset);

        $positions = $builder->get()->getResultArray();

        return ['total' => $totalRecords, 'data' => $positions];
    }

    public function search_positions($search_term = "", $business_id = "")
    {
        $db = \Config\Database::connect();
        $builder = $db->table("positions as p");
        $builder->select('p.id, p.description, p.business_id, p.name');
        if (!empty($search_term)) {
            $multipleWhere = [
                'p.name' => $search_term,
                'p.description' => $search_term,
            ];
            $builder->groupStart();
            $builder->orLike($multipleWhere);
            $builder->groupEnd();
        }
        if (!empty($business_id)) {
            $builder->where('p.business_id', $business_id);
        }
        return $builder->get()->getResultArray();
    }
}
