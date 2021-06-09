<?php

namespace App\Database\Seeds;

class InitSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $this->call('SettingsSeeder');
        $this->call('UsersSeeder');
    }
}
