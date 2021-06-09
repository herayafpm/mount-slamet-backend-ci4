<?php

namespace App\Models;

use CodeIgniter\Model;

class UserFcmsModel extends Model
{
    protected $table      = 'user_fcms';
    protected $primaryKey = 'user_fcm_id';

    protected $returnType     = 'array';

    protected $allowedFields = ['user_fcm', 'user_email'];

    protected $useTimestamps = false;
    protected $createdField  = '';
    protected $updatedField  = '';
    protected $beforeInsert = [];
    protected $beforeUpdate = [];
}
