<?php

namespace App\Validations;

use App\Models\BookingSeatsModel;
use App\Models\UsersModel;
use App\Models\LupaPasswordModel;
use App\Models\SettingsModel;

class MyRules
{
  public function cek_email_kode_exist(string $kode_otp, $user_email)
  {
    $lupaPasswordModel = new LupaPasswordModel();
    $data = [
      'user_email' => $user_email,
      'kode_otp' => $kode_otp
    ];
    return $lupaPasswordModel->cek_email_kode($data);
  }
  public function cek_email(string $user_email, $user_id = NULL)
  {
    $usersModel = new UsersModel();
    $user = $usersModel->getUserByEmail($user_email);
    if ($user_id == NULL) {
      if ($user) {
        return true;
      }
      return false;
    } else {
      $useraktif = $usersModel->getUserById($user_id);
      if (!$user || $user_email == $useraktif->user_email) {
        return true;
      }
      return false;
    }
  }
}
