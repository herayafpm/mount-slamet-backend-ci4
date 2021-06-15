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
      'user_email' => htmlspecialchars($dataJson->user_email ?? ''),
      'user_password' => htmlspecialchars($dataJson->user_password ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
    }
    $user = $this->model->authenticate($data['user_email'], $data['user_password']);
    if ($user) {
      helper('create_token');
      $token = create_token($user);
      $user['token'] = $token;
      return $this->respond(["status" => true, "message" => "login berhasil", "data" => $user], 200);
    } else {
      return $this->respond(["status" => false, "message" => "email atau password salah", "data" => []], 200);
    }
  }
  public function loginWithSocial()
  {
    $validation =  \Config\Services::validation();
    $rules = [
      'user_auth_key' => [
        'label'  => 'Social Key',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
      'auth_tipe' => [
        'label'  => 'Tipe Social',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_auth_key' => htmlspecialchars(urldecode($dataJson->user_auth_key ?? "")),
      'auth_tipe' => htmlspecialchars($dataJson->auth_tipe ?? ""),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
    }
    if (!$this->model->sosialList($data['auth_tipe'])) {
      return $this->respond(["status" => false, "message" => "kunci salah", "data" => []], 200);
    }
    $user = $this->model->authenticateWithSocial($data['user_auth_key'], $data['auth_tipe']);
    if ($user) {
      helper('create_token');
      $token = create_token($user);
      $user['token'] = $token;
      return $this->respond(["status" => true, "message" => "login berhasil", "data" => $user], 200);
    } else {
      return $this->respond(["status" => false, "message" => "kunci salah", "data" => []], 200);
    }
  }
}
