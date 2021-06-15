<?php

namespace App\Controllers\User;

use CodeIgniter\RESTful\ResourceController;

class Fcm extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\UserFcmsModel';

  public function index()
  {
    $user = $this->request->user;
    $validation =  \Config\Services::validation();
    $rules = [
      'user_fcm' => [
        'label'  => 'FCM Token',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
    ];
    $dataJson = $this->request->getJson();
    $data = [
      'user_fcm' => htmlspecialchars($dataJson->user_fcm ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
    }
    $data['user_email'] = $user['user_email'];
    $fcm_exist = $this->model->where($data)->findColumn('user_email');
    if ($fcm_exist) {
      return $this->respond(["status" => true, "message" => "tidak ada perubahan fcm token", "data" => []], 200);
    }
    $create = $this->model->save($data);
    if ($create) {
      return $this->respond(["status" => true, "message" => "berhasil menambah fcm token", "data" => []], 200);
    } else {
      return $this->respond(["status" => false, "message" => "gagal menambah fcm token", "data" => []], 200);
    }
  }
}
