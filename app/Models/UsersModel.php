<?php

namespace App\Models;

use CodeIgniter\Model;
use Google_Client;
use Google_Service_Oauth2;

class UsersModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $returnType     = 'array';

    protected $allowedFields = ['user_nama', 'user_email', 'user_alamat', 'user_no_telp', 'user_no_telp_ot', 'user_g_auth_key', 'user_fb_auth_key', 'role', 'user_password'];

    protected $useTimestamps = true;
    protected $createdField  = 'user_created_at';
    protected $updatedField  = 'user_updated_at';
    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    protected $social_list = [
        'google' => 'g',
        // 'facebook' => 'fb',
    ];

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
        if ($data) {
            if ($data['role'] == 1) {
                $data['is_admin'] = true;
            } else {
                $data['is_admin'] = false;
            }
            $data['role'] = $this->getRole($data['role']);
        }
        return $data;
    }
    public function getRole($role)
    {
        if ($role == 1) {
            return 'admin';
        } else {
            return 'user';
        }
    }
    public function authenticate($user_email, $user_password)
    {
        $auth = $this->getUserByEmail($user_email);
        if ($auth) {
            if (password_verify($user_password, $auth['user_password'])) {
                unset($auth['user_id']);
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
    public function authenticateWithSocial($auth_key, $auth_tipe, $user_email = "")
    {
        $user_nama = "";
        if (empty($user_email)) {
            if ($auth_tipe == 'google') {
                $google_client = new Google_Client();
                //Set the OAuth 2.0 Client ID
                $google_client->setClientId('261294496538-n2i66pjniqa04jo6o5hncn25n1u10vl3.apps.googleusercontent.com');
                //Set the OAuth 2.0 Client Secret key
                $google_client->setClientSecret('plD8Qv7lfX82lo6lwQ1YhIwv');
                //Set the OAuth 2.0 Redirect URI
                $google_client->setRedirectUri("http://localhost");
                $google_client->setAccessType("offline");
                $google_client->setApprovalPrompt('force');
                $google_client->addScope('email');
                $google_client->addScope('profile');
                try {
                    $token = $google_client->fetchAccessTokenWithAuthCode($auth_key);
                    if (isset($token['error'])) {
                        return false;
                    }
                    $google_client->setAccessToken($token);
                } catch (\Exception $th) {
                    return false;
                }
                $google_service = new Google_Service_Oauth2($google_client);
                $data = $google_service->userinfo->get();
                $user_nama = $data['given_name'] . " " . $data['family_name'];
                $user_email = $data['email'];
            } else if ($auth_tipe == 'facebook') {
                return false;
            } else {
                return false;
            }
        }
        $auth = $this->getUserByEmail($user_email);
        if ($auth) {
            unset($auth['user_id']);
            unset($auth['user_password']);
            unset($auth['user_created_at']);
            unset($auth['user_updated_at']);
            unset($auth['user_g_auth_key']);
            unset($auth['user_fb_auth_key']);
            return $auth;
        } else {
            $this->register(['user_nama' => $user_nama, 'user_email' => $user_email]);
            return $this->authenticateWithSocial($auth_key, $auth_tipe, $user_email);
        }
    }
    public function register($data)
    {
        $data['role'] = 2;
        if (!isset($data['user_password'])) {
            $data['user_password'] = mt_rand(100000, 999999);
        }
        return $this->save($data);
    }
    public function sosialList($auth_tipe)
    {
        if (array_key_exists($auth_tipe, $this->social_list)) {
            return true;
        }
        return false;
    }
}
