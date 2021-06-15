<?php

namespace App\Database\Seeds;

use App\Models\UsersModel;

class UsersSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        // Users
        $password = password_hash("123456", PASSWORD_DEFAULT);
        $initDatas = [
            [
                'user_nama'       => "admin",
                'user_email'       => "mountslametapp@gmail.com",
                'user_alamat'       => "purwokerto",
                'user_no_telp'       => "0895378036536",
                'user_no_telp_ot'       => "0895378036536",
                'role'       => 1,
                'user_password'       => $password,
            ],
            [
                'user_nama'       => "heraya",
                'user_email'       => "heraya71@gmail.com",
                'user_alamat'       => "purwokerto",
                'user_no_telp'       => "0895378036536",
                'user_no_telp_ot'       => "0895378036536",
                'role'       => 2,
                'user_password'       => $password,
            ],
        ];
        $users_model = new UsersModel();
        $users_model->insertBatch($initDatas);
    }
}
