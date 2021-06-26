<?php

namespace App\Controllers;

use App\Models\BookingSeatsModel;
use App\Models\SettingsModel;
use App\Models\UsersModel;
use CodeIgniter\RESTful\ResourceController;

class Booking extends ResourceController
{

    protected $format       = 'json';
    protected $modelName    = 'App\Models\BookingsModel';

    public function index()
    {
        $user = $this->request->user;
        $dataGet = $this->request->getGet();
        $limit = $dataGet["limit"] ?? 10;
        $offset = $dataGet["offset"] ?? 0;
        $riwayat_booking = $this->model->filter($limit, $offset, ['where' => ['user_email' => $user['user_email']]]);
        return $this->respond(["status" => true, "message" => "berhasil mendapatkan semua booking", "data" => $riwayat_booking], 200);
    }
    public function detail($booking_no_order)
    {
        $user = $this->request->user;
        $booking = $this->model->where(['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order])->first();
        unset($booking['booking_id']);
        return $this->respond(["status" => true, "message" => "berhasil mendapatkan data booking", "data" => $booking], 200);
    }
    public function create()
    {
        $validation =  \Config\Services::validation();
        $dataJson = $this->request->getJson();
        $data = [
            'booking_nama' => htmlspecialchars($dataJson->booking_nama ?? ''),
            'booking_alamat' => htmlspecialchars($dataJson->booking_alamat ?? ''),
            'booking_no_telp' => htmlspecialchars($dataJson->booking_no_telp ?? ''),
            'booking_jml_anggota' => htmlspecialchars($dataJson->booking_jml_anggota ?? ''),
            'booking_tgl_masuk' => htmlspecialchars($dataJson->booking_tgl_masuk ?? ''),
            'booking_tgl_keluar' => htmlspecialchars($dataJson->booking_tgl_keluar ?? ''),
        ];

        $rules = [
            'booking_nama' => [
                'label'  => 'Nama',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_alamat' => [
                'label'  => 'Alamat',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_no_telp' => [
                'label'  => 'No Telp',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_jml_anggota' => [
                'label'  => 'Jumlah Anggota',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_tgl_masuk' => [
                'label'  => 'Tanggal Masuk',
                'rules'  => "required",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_tgl_keluar' => [
                'label'  => 'Tanggal Keluar',
                'rules'  => "required",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
        ];

        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
        }
        helper('locale');
        $tgl_masuk_text = tgl_indo($data['booking_tgl_masuk'] ?? "");
        $tgl_keluar_text = tgl_indo($data['booking_tgl_keluar'] ?? "");
        try {
            helper("booking");
            cek_ketersediaan((int)$data['booking_jml_anggota'], $data['booking_tgl_masuk'], $data['booking_tgl_keluar']);
        } catch (\Exception $th) {
            return $this->respond(["status" => false, "message" => $th->getMessage(), "data" => []], 200);
        }
        $data['booking_tgl_masuk'] = date("Y-m-d", strtotime($data['booking_tgl_masuk']));
        $data['booking_tgl_keluar'] = date("Y-m-d", strtotime($data['booking_tgl_keluar']));
        // $data['booking_status'] = 1;
        $user = $this->request->user;
        $data = array_merge($data, $user);
        $create = $this->model->save($data);
        if ($create) {
            $booking = $this->model->find($this->model->getInsertID());
            helper('notification');
            $user_model = new UsersModel();
            $user_emails = $user_model->where(['role' => 1])->findColumn('user_email');
            notif($user_emails, "Konfirmasi Booking", "{$booking['booking_no_order']}, tanggal {$tgl_masuk_text} sampai {$tgl_keluar_text} silahkan untuk melakukan konfirmasi.");
            return $this->respond(["status" => true, "message" => "Berhasil booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => false, "message" => "gagal booking", "data" => []], 200);
        }
    }
    public function batalkan($booking_no_order)
    {
        $user = $this->request->user;
        $booking_no_order = urldecode($booking_no_order);
        if ($user['is_admin']) {
            $booking = $this->model->where(['booking_no_order' => $booking_no_order])->first();
        } else {
            $booking = $this->model->where(['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order])->first();
        }
        if (!$booking) {
            return $this->respond(["status" => false, "message" => "booking tidak ditemukan", "data" => []], 200);
        }
        if ($booking['booking_status'] == 2) {
            return $this->respond(["status" => true, "message" => "booking sudah dibatalkan", "data" => []], 200);
        }
        $where = ['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order];
        if ($user['is_admin']) {
            unset($where['user_email']);
        }
        $update = $this->model->where($where)->set(['booking_status' => 2, 'booking_jml_anggota' => $booking['booking_jml_anggota'], 'booking_tgl_masuk' => $booking['booking_tgl_masuk'], 'booking_tgl_keluar' => $booking['booking_tgl_keluar']])->update();
        if ($update) {
            helper('locale');
            $tgl_masuk_text = tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_masuk'])) ?? "");
            $tgl_keluar_text = tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_keluar'])) ?? "");
            helper('notification');
            $user_model = new UsersModel();
            $user_emails = $user_model->where(['role' => 1])->findColumn('user_email');
            notif($user_emails, "Pembatalan Booking", "{$booking_no_order}, tanggal {$tgl_masuk_text} sampai {$tgl_keluar_text} dibatalkan.");
            return $this->respond(["status" => true, "message" => "berhasil membatalkan booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => false, "message" => "gagal membatalkan booking", "data" => []], 200);
        }
    }
    public function bookingHariIni()
    {
        $booking_seat_model = new BookingSeatsModel();
        $setting_model = new SettingsModel();
        $booking_seat = $booking_seat_model->where(['booking_seat_tgl' => date("Y-m-d") . " 00:00:00"])->first();
        $jumlah_pendaki = $setting_model->where(['setting_key' => 'jumlah_pendaki'])->first()['setting_value'];
        $sisa_seat = 0;
        $jumlah_booking = 0;
        if ($booking_seat) {
            $sisa_seat = (int)$jumlah_pendaki - (int) $booking_seat['booking_seat_jml'];
            $jumlah_booking = (int) $booking_seat['booking_seat_jml'];
        }
        return $this->respond(["status" => true, "message" => "Ketersediaan hari ini", "data" => ['jumlah_booking' => $jumlah_booking, 'sisa_seat' => $sisa_seat, 'jumlah_pendaki' => (int)$jumlah_pendaki]], 200);
    }
    public function cekKetersediaan()
    {
        $validation =  \Config\Services::validation();
        $dataJson = $this->request->getJson();
        $data = [
            'booking_jml_anggota' => htmlspecialchars($dataJson->booking_jml_anggota ?? ''),
            'booking_tgl_masuk' => htmlspecialchars($dataJson->booking_tgl_masuk ?? ''),
            'booking_tgl_keluar' => htmlspecialchars($dataJson->booking_tgl_keluar ?? ''),
        ];
        $rules = [
            'booking_jml_anggota' => [
                'label'  => 'Jumlah Anggota',
                'rules'  => 'required',
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_tgl_masuk' => [
                'label'  => 'Tanggal Masuk',
                'rules'  => "required",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
            'booking_tgl_keluar' => [
                'label'  => 'Tanggal Keluar',
                'rules'  => "required",
                'errors' => [
                    'required' => '{field} tidak boleh kosong',
                ]
            ],
        ];

        $validation->setRules($rules);
        if (!$validation->run($data)) {
            return $this->respond(["status" => false, "message" => "Validasi gagal", "data" => $validation->getErrors()], 200);
        }
        helper('locale');
        $tgl_masuk_text = tgl_indo($data['booking_tgl_masuk'] ?? "");
        $tgl_keluar_text = tgl_indo($data['booking_tgl_keluar'] ?? "");
        try {
            helper("booking");
            cek_ketersediaan((int)$data['booking_jml_anggota'], $data['booking_tgl_masuk'], $data['booking_tgl_keluar']);
            $message = "tersedia untuk tanggal ";
            if ($data['booking_tgl_masuk'] == $data['booking_tgl_keluar']) {
                $message .= $tgl_masuk_text;
            } else {
                $message .= "{$tgl_masuk_text} sampai {$tgl_keluar_text}";
            }
            return $this->respond(["status" => true, "message" => $message, "data" => []], 200);
        } catch (\Exception $th) {
            return $this->respond(["status" => false, "message" => $th->getMessage(), "data" => []], 200);
        }
    }
}
