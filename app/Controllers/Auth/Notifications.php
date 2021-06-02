<?php

namespace App\Controllers\Auth;

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
    $notifs = $this->model->filter($limit, $offset, ['where' => ['user_id' => $user->user_id]]);
    return $this->respond(["status" => 1, "message" => "berhasil mengupdate notifications", "data" => $notifs], 200);
  }
  public function baca_semua()
  {
    $user = $this->request->user;
    $baca = $this->model->baca_semua($user->user_id);
    if ($baca) {
      return $this->respond(["status" => 1, "message" => "berhasil membaca semua notif", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal membaca semua notif", "data" => []], 400);
    }
  }
  public function baca($id = null)
  {
    $baca = $this->model->baca($id);
    if ($baca) {
      return $this->respond(["status" => 1, "message" => "berhasil membaca notif", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal membaca notif", "data" => []], 400);
    }
  }
}
