<?php

namespace App\Models;

use CodeIgniter\Model;

class UsersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $returnType     = 'array';

    protected $allowedFields = ['user_nama', 'user_email', 'user_alamat', 'user_no_telp', 'user_no_telp_ot', 'user_fcm', 'role', 'user_g_auth_key', 'user_password'];

    protected $useTimestamps = true;
    protected $createdField  = 'user_created_at';
    protected $updatedField  = 'user_updated_at';
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];
    protected function hashPassword(array $data)
    {

        if (!isset($data['data']['user_password'])) return $data;
        $data['data']['user_password'] = password_hash($data['data']['user_password'], PASSWORD_DEFAULT);
        return $data;
    }
    public function setPassword($user_email, $user_password)
    {
        return $this->where('user_email', $user_email)->set(['user_password' => $user_password])->update();
    }
    public function getUserByEmail($user_email)
    {
        $data = $this->where('user_email', $user_email)->get()->getRowArray();
        $data['role'] = $this->getRole($data['role']);
        return $data;
    }
    public function getRole($role)
    {
        if ($role == 1) {
            return 'Admin';
        } else {
            return 'User';
        }
    }
    public function authenticate($user_email, $user_password)
    {
        $auth = $this->getUserByEmail($user_email);
        if ($auth) {
            if (password_verify($user_password, $auth['user_password'])) {
                unset($auth['user_password']);
                unset($auth['user_created_at']);
                unset($auth['user_updated_at']);
                unset($auth['user_g_auth_key']);
                unset($auth['user_fb_auth_key']);
                return $auth;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
