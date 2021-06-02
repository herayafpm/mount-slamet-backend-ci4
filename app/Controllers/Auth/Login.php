<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;
use Firebase\JWT\JWT;

class Login extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UsersModel';

  public function index()
  {
    $validation =  \Config\Services::validation();
    $rules = [
      'user_email' => [
        'label'  => 'Email',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
      'user_password' => [
        'label'  => 'Password',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_email' => htmlspecialchars($dataJson->user_email),
      'user_password' => htmlspecialchars($dataJson->user_password),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
    }
    $user = $this->model->authenticate($data['user_email'], $data['user_password']);
    if ($user) {
      $jwt = JWT::encode($user, env('appJWTKey'));
      if ($user['role'] == 'Admin') {
        $user['is_admin'] = true;
      }
      $user['token'] = $jwt;
      return $this->respond(["status" => 1, "message" => "login berhasil", "data" => $user], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "username atau password salah", "data" => []], 400);
    }
  }
}
