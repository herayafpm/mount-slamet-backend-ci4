<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;

class Profile extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UsersModel';

  public function index()
  {
    $user = $this->request->user;
    if ($user->role_id == 1) {
      $user->isDistributor = true;
    }
    return $this->respond(['status' => 1, "message" => "berhasil mengambil profile", 'data' => $user], 200);
  }
  public function ubah()
  {
    $user = $this->request->user;
    $validation =  \Config\Services::validation();
    $user_id = $this->request->user->user_id;
    $updateProfileRule = [
      'user_nama' => [
        'label'  => 'Nama Lengkap',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
      'user_email' => [
        'label'  => 'Email',
        'rules'  => 'required|cek_email[' . $user_id . ']',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'cek_email' => '{field} sudah digunakan',
        ]
      ],
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
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_nama' => htmlspecialchars(trim(strtolower($dataJson->user_nama ?? ''))),
      'user_email' => htmlspecialchars(trim($dataJson->user_email ?? '')),
      'user_alamat' => htmlspecialchars(trim($dataJson->user_alamat ?? '')),
      'user_no_telp' => htmlspecialchars(trim($dataJson->user_no_telp ?? '')),
    ];
    $validation->setRules($updateProfileRule);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "validasi error", "data" => $validation->getErrors()], 400);
    }
    if ($user->role_id != 1) {
      unset($data['user_nama']);
    }
    $update = $this->model->updateUser($user_id, $data);
    if ($update) {
      $user = $this->model->getUserWithRole($user->user_email);
      return $this->respond(["status" => 1, "message" => "Berhasil mengupdate profile", "data" => $user], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "Gagal mengupdate profile", "data" => []], 400);
    }
  }
}
