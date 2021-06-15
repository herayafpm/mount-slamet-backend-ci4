<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;

class Notifications extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\NotificationsModel';

  public function index()
  {
    $user = $this->request->user;
    $dataGet = $this->request->getGet();
    $limit = $dataGet["limit"] ?? 10;
    $offset = $dataGet["offset"] ?? 0;
    $notifs = $this->model->filter($limit, $offset, ['where' => ['user_email' => $user['user_email']]]);
    return $this->respond(["status" => true, "message" => "berhasil mendapatkan semua notif", "data" => $notifs], 200);
  }
  public function baca_semua()
  {
    $user = $this->request->user;
    $baca = $this->model->baca_semua($user['user_email']);
    if ($baca) {
      return $this->respond(["status" => true, "message" => "berhasil membaca semua notif", "data" => []], 200);
    } else {
      return $this->respond(["status" => false, "message" => "gagal membaca semua notif", "data" => []], 200);
    }
  }
  public function baca($id = null)
  {
    $user = $this->request->user;
    $baca = $this->model->baca($id, $user['user_email']);
    if ($baca) {
      return $this->respond(["status" => true, "message" => "berhasil membaca notif", "data" => []], 200);
    } else {
      return $this->respond(["status" => false, "message" => "gagal membaca notif", "data" => []], 200);
    }
  }
}
