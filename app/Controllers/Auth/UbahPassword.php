<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;

class UbahPassword extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UsersModel';

  public function index()
  {
    $user = $this->request->user;
    $validation =  \Config\Services::validation();
    $rules = [
      'user_password' => [
        'label'  => 'Password Baru',
        'rules'  => 'required|min_length[6]',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'min_length' => '{field} harus lebih dari sama dengan {param} karakter',
        ]
      ],
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_password' => htmlspecialchars($dataJson->user_password ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
    }
    if (password_verify($data['user_password'], $user->user_password)) {
      return $this->respond(["status" => 0, "message" => "password tidak boleh sama dengan yang sebelumnya, gunakan yang lain", "data" => []], 400);
    }
    $update = $this->model->updateUser($user->user_id, $data);
    if ($update) {
      return $this->respond(["status" => 1, "message" => "berhasil mengubah password", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal mengubah password", "data" => []], 400);
    }
  }
}
