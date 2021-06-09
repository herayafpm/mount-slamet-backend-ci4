<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;

class Profile extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UsersModel';

  public function index()
  {
    $user = $this->request->user;
    unset($user['user_id']);
    return $this->respond(['status' => 1, "message" => "berhasil mengambil profile", 'data' => $user], 200);
  }
  public function ubah()
  {
    $user = $this->request->user;
    $validation =  \Config\Services::validation();
    $user_id = $user['user_id'];
    $updateProfileRule = [
      'user_nama' => [
        'label'  => 'Nama Lengkap',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
      // 'user_email' => [
      //   'label'  => 'Email',
      //   'rules'  => "required|is_unique[users.user_email,user_id,{$user_id}]",
      //   'errors' => [
      //     'required' => '{field} tidak boleh kosong',
      //     'is_unique' => '{field} sudah digunakan',
      //   ]
      // ],
      'user_alamat' => [
        'label'  => 'Alamat',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
      'user_no_telp' => [
        'label'  => 'No Telephone',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
      'user_no_telp_ot' => [
        'label'  => 'No Telephone Orang Tua',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_nama' => htmlspecialchars(trim(strtolower($dataJson->user_nama ?? ''))),
      // 'user_email' => htmlspecialchars(trim($dataJson->user_email ?? '')),
      'user_alamat' => htmlspecialchars(trim($dataJson->user_alamat ?? '')),
      'user_no_telp' => htmlspecialchars(trim($dataJson->user_no_telp ?? '')),
      'user_no_telp_ot' => htmlspecialchars(trim($dataJson->user_no_telp_ot ?? '')),
    ];
    $validation->setRules($updateProfileRule);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "validasi error", "data" => $validation->getErrors()], 400);
    }
    if ($user['is_admin']) {
      unset($data['user_nama']);
    }
    $update = $this->model->update($user_id, $data);
    if ($update) {
      $user = array_merge($user, $data);
      unset($user['user_id']);
      return $this->respond(["status" => 1, "message" => "Berhasil mengupdate profile", "data" => $user], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "Gagal mengupdate profile", "data" => []], 400);
    }
  }
  public function ubahPassword()
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
    if (password_verify($data['user_password'], $user['user_password'])) {
      return $this->respond(["status" => 0, "message" => "password tidak boleh sama dengan yang sebelumnya, gunakan yang lain", "data" => []], 400);
    }
    $update = $this->model->update($user['user_id'], $data);
    if ($update) {
      return $this->respond(["status" => 1, "message" => "berhasil mengubah password", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal mengubah password", "data" => []], 400);
    }
  }
}
