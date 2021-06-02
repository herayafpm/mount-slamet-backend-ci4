<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;

class UbahFcmToken extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UsersModel';

  public function index()
  {
    $user = $this->request->user;
    $dataJson = $this->request->getJson();
    $data = [
      'user_fcm' => htmlspecialchars($dataJson->user_fcm ?? ''),
    ];
    if ($data['user_fcm'] == $user->user_fcm) {
      return $this->respond(["status" => 0, "message" => "tidak ada perubahan fcm token", "data" => []], 200);
    }
    $update = $this->model->updateUser($user->user_id, $data);
    if ($update) {
      return $this->respond(["status" => 1, "message" => "berhasil mengupdate fcm token", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal mengupdate fcm token", "data" => []], 400);
    }
  }
}
