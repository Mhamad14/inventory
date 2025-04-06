<?php

namespace App\Models;

use CodeIgniter\Model;

class Settings_model extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['variable', 'value'];

    public function save_settings($setting_type, $data)
    {
        $db = \Config\Database::connect();
        $builder = $db->table("settings");
        $builder->where('variable', $setting_type);
        $builder->update($data);
    }
}
