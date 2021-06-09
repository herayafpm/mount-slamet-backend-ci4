<?php

namespace App\Database\Seeds;

use App\Models\SettingsModel;

class SettingsSeeder extends \CodeIgniter\Database\Seeder
{
    public function run()
    {
        $initDatas = [
            [
                'setting_key'       => "jumlah_pendaki",
                'setting_value'       => "50",
                'setting_role'       => "user",
            ],
        ];
        $settings_model = new SettingsModel();
        $settings_model->insertBatch($initDatas);
    }
}
