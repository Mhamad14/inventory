<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AddGroupInGroupTable extends Seeder
{
    public function run()
    {
        $data = [
            'name' => 'team_members',
            'description'    => 'Team Members',
        ];

        // Using Query Builder
        $this->db->table('groups')->insert($data);
    }
}
