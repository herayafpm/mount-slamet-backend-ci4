<?php

namespace App\Controllers\Auth;

use CodeIgniter\RESTful\ResourceController;
use App\Models\UsersModel;

class LupaPassword extends ResourceController
{

  protected $format       = 'json';
  protected $modelName    = 'App\Models\LupaPasswordModel';

  public function index()
  {
    $validation =  \Config\Services::validation();
    $dataJson = $this->request->getJson();
    $rules = [
      'user_username' => [
        'label'  => 'Username',
        'rules'  => 'required|cek_username_exist',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'cek_username_exist' => "{field} {$dataJson->user_username} tidak ditemukkan",
        ]
      ],
    ];
    $data = [
      'user_username' => htmlspecialchars($dataJson->user_username ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
    }
    $usersModel = new UsersModel();
    $user = $usersModel->getUserByUsername($data['user_username']);
    $data['user_email'] = $user->user_email;
    $data['kode_otp'] = mt_rand(100000, 999999);
    $save = $this->model->saveLupaPassword($data);
    if ($save) {
      helper('my_email');
      $send = sendEmail("Lupa Kata Sandi", $data['user_email'], view('emails/lupa_password_email', $data));
      return $this->respond(["status" => 1, "message" => "silahkan cek email {$data['user_email']}, di kotak pesan atau spam, untuk verifikasi lebih lanjut", "data" => ['user_email' => $user->user_email]], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal mengirim email", "data" => []], 400);
    }
  }
  public function cek_kode()
  {
    $validation =  \Config\Services::validation();
    $dataJson = $this->request->getJson();
    $rules = [
      'user_email' => [
        'label'  => 'Email',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong',
        ]
      ],
      'kode_otp' => [
        'label'  => 'Kode OTP',
        'rules'  => "required|cek_email_kode_exist[{$dataJson->user_email}]",
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'cek_email_kode_exist' => "{field} yang anda masukkan salah, coba lagi",
        ]
      ],
    ];
    $data = [
      'user_email' => htmlspecialchars($dataJson->user_email ?? ''),
      'kode_otp' => htmlspecialchars($dataJson->kode_otp ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
    }
    return $this->respond(["status" => 1, "message" => "token lupa benar", "data" => []], 200);
  }
  public function ubah_password()
  {
    $validation =  \Config\Services::validation();
    $dataJson = $this->request->getJson();
    $rules = [
      'user_email' => [
        'label'  => 'Email',
        'rules'  => 'required',
        'errors' => [
          'required' => '{field} tidak boleh kosong'
        ]
      ],
      'kode_otp' => [
        'label'  => 'Kode OTP',
        'rules'  => "required|cek_email_kode_exist[{$dataJson->user_email}]",
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'cek_email_kode_exist' => "{field} yang anda masukkan salah, coba lagi",
        ]
      ],
      'user_password' => [
        'label'  => 'Password Baru',
        'rules'  => "required|min_length[6]",
        'errors' => [
          'required' => '{field} tidak boleh kosong',
          'min_length' => '{field} harus lebih dari sama dengan {param} karakter',
        ]
      ],
    ];
    $data = [
      'user_email' => htmlspecialchars($dataJson->user_email ?? ''),
      'kode_otp' => htmlspecialchars($dataJson->kode_otp ?? ''),
      'user_password' => htmlspecialchars($dataJson->user_password ?? ''),
    ];
    $validation->setRules($rules);
    if (!$validation->run($data)) {
      return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
    }
    $usersModel = new UsersModel();
    $ubah = $usersModel->lupaPassword($data['user_email'], $data['user_password']);
    if ($ubah) {
      $this->model->deleteLupaPassword($data['user_email']);
      return $this->respond(["status" => 1, "message" => "berhasil mengubah password, silahkan login", "data" => []], 200);
    } else {
      return $this->respond(["status" => 0, "message" => "gagal mengubah password, silahkan coba lagi", "data" => []], 200);
    }
  }
}
