<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;

class Register extends ResourceController
{

    protected $format       = 'json';
    protected $modelName    = 'App\Models\UsersModel';

    public function index()
    {
        $validation =  \Config\Services::validation();
        $rules = [
            'user_nama' => [
                'label'  => 'Nama',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'user_email' => [
                'label'  => 'Email',
                'rules'  => 'required|is_unique[users.user_email]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'is_unique' => '{field} sudah terdaftar.'
                ]
            ],
            'user_password' => [
                'label'  => 'Password',
                'rules'  => 'required|min_length[6]',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                    'min_length' => '{field} harus lebih dari sama dengan {param} karakter',
                ]
            ],
        ];
        $dataJson = $this->request->getJson();
        $data = [
            'user_nama' => htmlspecialchars($dataJson->user_nama ?? ''),
            'user_email' => htmlspecialchars($dataJson->user_email ?? ''),
            'user_password' => htmlspecialchars($dataJson->user_password ?? ''),
        ];
        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
        }
        $create = $this->model->register($data);
        if ($create) {
            return $this->respond(["status" => true, "message" => "Berhasil mendaftar silahkan login", "data" => ['create' => true]], 200);
        } else {
            return $this->respond(["status" => false, "message" => "gagal mendaftar, silahkan coba kembali", "data" => []], 200);
        }
    }
}
