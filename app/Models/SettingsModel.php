<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table      = 'settings';
    protected $primaryKey = 'setting_id';

    protected $returnType     = 'array';

    protected $allowedFields = ['setting_key', 'setting_value', 'setting_role'];

    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';
}
