<?php


namespace App\Models;

use CodeIgniter\Model;

class Team_members_model extends Model
{
    protected $table = 'team_members';
    protected $primaryKey = 'id';
    protected $allowedFields = ['id',	'user_id',	'vendor_id',	'business_id',	'created_at', 	'updated_at',	'deleted_at' ] ;

    public function insertUser($data)
    {
        $this->insert($data);
        return $this->getInsertID();
    }

    public function addUserPermissions($data)
    {
        $this->db->table('user_permissions')->insert($data);
        return $this->db->insertID();
    }

    public function deleteRecordFromUserPermissionsTable($user_id){
        $db = \Config\Database::connect();
        $db->table('user_permissions')->where('team_members_id',$user_id)->delete();
    }

    public function get_user_businesses($user_id){
        $db = db_connect();
        $builder = $db->table('team_members');
        $builder->select('team_members.business_ids');
        $builder->where('user_id', $user_id);
        return $builder->get()->getResultArray() ;
        
    }
    public function insertUserGroup($userGroupData)
    {
        $db = \Config\Database::connect();

        // Assuming 'user_groups' is the table name for user_groups
        $builder = $db->table('users_groups');

        // Insert the data into 'user_groups' table
        $builder->insert($userGroupData);

        // Return the inserted user group ID
        return $db->insertID();
    }
    
    

    public function getUsersWithPermissions()
    {
        $db = db_connect();
        $builder = $db->table('users');
        $builder->select('users.id, users.first_name, users.mobile, users.email, user_permissions.permissions');
        $builder->join('user_permissions', 'users.id = user_permissions.team_members_id', 'left');
        $query = $builder->get();

        return $query->getResultArray();
    }

    // public function get_user_edit($id)
    // {
    //     $query = $this->db->table('users');
    //     $query->select('users.*, user_permissions.permissions');
    //     $query->join('user_permissions', 'user_permissions.team_members_id = users.id');
    //     $query->where('users.id', $id);
    
    //     $result = $query->get()->getRow();
    //     return $result;
    // }
    public function saveUserPermissions($userId, $permissions)
    {
        $data = [
            'permissions' => $permissions
        ];

        $this->db->table('user_permissions')
            ->where('team_members_id', $userId)
            ->update($data);
    }
    

    public function edit_profile($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("users");
        $builder->select('*');
        $builder->where('id', $id);
        return $builder->get()->getRow();
    }



    public function get_users_list()
    {
        $offset = 0;
        $limit = 10;
        $sort = 'id';
        $order = 'ASC';
        $multipleWhere = '';
    
        $request = $this->request;
        $search = $request->getVar('search');
    
        if ($request->getVar('offset')) {
            $offset = $request->getVar('offset');
        }
        if ($request->getVar('limit')) {
            $limit = $request->getVar('limit');
        }
        if ($request->getVar('sort')) {
            $sort = $request->getVar('sort');
        }
        if ($request->getVar('order')) {
            $order = $request->getVar('order');
        }
    
        if ($search != '') {
            $multipleWhere = ['up.id' => $search, 'u.username' => $search, 'u.mobile' => $search];
        }
    
        $count_res = $this->db->table('user_permissions up')
            ->join('users u', 'up.team_members_id = u.id')
            ->selectCount('up.id', 'total');
    
        if (!empty($multipleWhere)) {
            $count_res->orWhere($multipleWhere);
        }
    
        $sys_user_count = $count_res->get()->getRow()->total;
    
        $search_res = $this->db->table('user_permissions up')
            ->join('users u', 'up.team_members_id = u.id')
            ->select('up.id, u.id as user_id, u.username, u.email, u.mobile, up.permissions, u.active');
    
        if (!empty($multipleWhere)) {
            $search_res->orWhere($multipleWhere);
        }
    
        $sys_search_res = $search_res->orderBy($sort, $order)->limit($limit, $offset)->get()->getResultArray();
    
        $bulkData = [];
        $bulkData['total'] = $sys_user_count;
        $rows = [];
        $current_user_id = $this->ion_auth->user()->row()->id;
        $userData = fetch_details('user_permissions', ['team_members_id' => $current_user_id]);
    
        // foreach ($sys_search_res as $row) {
        //     $operate = '';
    
        //     if ($current_user_id != $row['user_id'] && $userData[0]['role'] == 0) {
        //         $operate .= ' <a href="javascript:void(0)" class="edit_btn action-btn btn btn-success btn-xs mb-1 ml-1"  title="Edit" data-id="' . $row['id'] . '" data-url="admin/team_members/add_team_members"><i class="fa fa-pen"></i></a>';
        //         $operate .= ' <a  href="javascript:void(0)" class="btn btn-danger action-btn btn-xs mr-1 mb-1 ml-1"  title="Delete" id="delete-system-users" data-id="' . $row['user_id'] . '"  ><i class="fa fa-trash"></i></a>';
    
        //         if ($row['active'] == '1') {
        //             $tempRow['status'] = '<a class="badge badge-success text-white" >Active</a>';
        //             $operate .= '<a class="btn btn-warning btn-xs update_active_status action-btn mr-1 mb-1 ml-1" data-table="users" title="Deactivate" href="javascript:void(0)" data-id="' . $row['user_id'] . '" data-status="' . $row['active'] . '" ><i class="fa fa-eye-slash"></i></a>';
        //         } else {
        //             $tempRow['active'] = '<a class="badge badge-danger text-white" >Inactive</a>';
        //             $operate .= '<a class="btn btn-primary mr-1 mb-1 ml-1 btn-xs update_active_status action-btn" data-table="users" href="javascript:void(0)" title="Active" data-id="' . $row['user_id'] . '" data-status="' . $row['active'] . '" ><i class="fa fa-eye"></i></a>';
        //         }
        //     }
    
        //     $tempRow['id'] = $row['id'];
        //     $tempRow['username'] = ucfirst($row['username']);
        //     $tempRow['email'] = $row['email'];
        //     $tempRow['mobile'] = ucfirst($row['mobile']);
    
           
        //     $tempRow['permissions'] = $row['permissions'];
        //     $tempRow['operate'] = $operate;
        //     $rows[] = $tempRow;
        // }
    
        $bulkData['rows'] = $rows;
        echo json_encode($bulkData);
    }
    
}
