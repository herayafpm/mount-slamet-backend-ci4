<?php

namespace App\Controllers;

use CodeIgniter\RESTful\ResourceController;

class Settings extends ResourceController
{

    protected $format       = 'json';
    protected $modelName    = 'App\Models\SettingsModel';

    public function index()
    {
        $user = $this->request->user;
        if ($user['is_admin'] == true) {
            $settings = $this->model->findAll();
        } else {
            $fields = ['key', 'value'];
            array_walk($fields, function (&$value, $key) {
                $value = "setting_" . $value;
            });
            $settings = $this->model->select(implode(",", $fields))->where(['setting_role' => $user['role']])->findAll();
        }
        return $this->respond(["status" => 1, "message" => "berhasil mendapatkan setting", "data" => $settings], 200);
    }
    public function update_setting()
    {
        $user = $this->request->user;
        $validation =  \Config\Services::validation();
        $rules = [
            'setting_key' => [
                'label'  => 'Kunci',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'setting_value' => [
                'label'  => 'Nilai',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
        ];
        $dataJson = $this->request->getJson();
        $data = [
            'setting_key' => htmlspecialchars($dataJson->setting_key ?? ""),
            'setting_value' => htmlspecialchars($dataJson->setting_value ?? ""),
        ];
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
        }
        $update = $this->model->where(['setting_key' => $data['setting_key']])->set($data)->update();
        if ($update) {
            return $this->respond(["status" => 1, "message" => "Berhasil mengupdate setting", "data" => []], 200);
        } else {
            return $this->respond(["status" => 0, "message" => "gagal mengupdate setting", "data" => []], 400);
        }
    }
}
