<?php

use GuzzleHttp\Client;
use App\Models\NotificationsModel;
use App\Models\UserFcmsModel;

function notif($user_emails, $title = "", $body = "", $data = ["foo" => "bar"])
{
  $notificationModel = new NotificationsModel();
  $insert_notifs = [];
  $user_fcms = [];
  $user_fcms_model = new UserFcmsModel();
  foreach ($user_emails as $user_email) {
    array_push($insert_notifs, [
      'user_email' => $user_email,
      'notification_title' => $title,
      'notification_body' => $body,
      'notification_created' => date("Y-m-d H:i:s"),
    ]);
    $fcms = $user_fcms_model->where(['user_email' => $user_email])->findColumn('user_fcm');
    if ($fcms) {
      array_push($user_fcms, ...$fcms);
    }
  }
  $notificationModel->insertBatch($insert_notifs);
  try {
    $client = new Client();
    $response = $client->post('https://onesignal.com/api/v1/notifications', [
      \GuzzleHttp\RequestOptions::JSON => [
        "app_id" => env("oneSignalId"), "include_player_ids" => $user_fcms,
        "data" => $data,
        "headings" => ["en" => "Notifikasi " . config('app')->appName],
        "contents" => ["en" => $body],
        "subtitle" => ["en" => $body],
        "large_icon" => "ic_stat_onesignal_default",
        "small_icon" => "ic_stat_onesignal_default",
      ]
    ]);
  } catch (\Exception $th) {
    //throw $th;
    var_dump($th->getMessage());
    die();
  }
  // return $response;
}
