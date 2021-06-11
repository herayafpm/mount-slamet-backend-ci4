<?php

use App\Models\SettingsModel;
use App\Models\BookingSeatsModel;

function cek_ketersediaan(int $jumlah, $tgl_masuk, $tgl_keluar)
{
    $setting_model = new SettingsModel();
    $jumlah_pendaki = (int) $setting_model->select('setting_value')->where('setting_key', 'jumlah_pendaki')->first()['setting_value'];
    $tgl_masuk = date("Y-m-d", strtotime($tgl_masuk));
    $tgl_keluar = date("Y-m-d", strtotime($tgl_keluar));
    if ($tgl_keluar < $tgl_masuk || $tgl_masuk > $tgl_keluar) {
        throw new \Exception("Maaf Tanggal keluar harus lebih dari tgl masuk");
    }
    helper('my_date');
    $dates = displayDates($tgl_masuk, $tgl_keluar);
    $booking_seat_model = new BookingSeatsModel();
    foreach ($dates as $d) {
        $d = date("Y-m-d H:i:s", strtotime($d));
        $d_text = tgl_indo(date("Y-m-d", strtotime($d)));
        $booking_seat = $booking_seat_model->where(['booking_seat_tgl' => $d])->first();
        if ($booking_seat) {
            $jml = $jumlah_pendaki - (int) $booking_seat['booking_seat_jml'];
            if ($jml == 0) {
                throw new \Exception("Maaf Tanggal {$d_text} sudah penuh");
            }
            if (($jml - $jumlah) < 0) {
                throw new \Exception("Maaf Tanggal {$d_text} hanya tersisa {$jml} orang");
            }
        }
    }
}
