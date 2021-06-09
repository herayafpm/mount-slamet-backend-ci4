<?php

namespace App\Controllers;

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
        return $this->respond(["status" => 1, "message" => "berhasil mendapatkan semua booking", "data" => $riwayat_booking], 200);
    }
    public function detail($booking_no_order)
    {
        $user = $this->request->user;
        $booking = $this->model->where(['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order])->first();
        unset($booking['booking_id']);
        return $this->respond(["status" => 1, "message" => "berhasil mendapatkan data booking", "data" => $booking], 200);
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
            return $this->respond(["status" => 0, "message" => "Validasi gagal", "data" => $validation->getErrors()], 400);
        }
        helper('locale');
        $tgl_masuk_text = tgl_indo($data['booking_tgl_masuk'] ?? "");
        $tgl_keluar_text = tgl_indo($data['booking_tgl_keluar'] ?? "");
        try {
            helper("booking");
            cek_ketersediaan((int)$data['booking_jml_anggota'], $data['booking_tgl_masuk'], $data['booking_tgl_keluar']);
        } catch (\Exception $th) {
            return $this->respond(["status" => 0, "message" => $th->getMessage(), "data" => []], 400);
        }
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
            return $this->respond(["status" => 1, "message" => "Berhasil booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => 0, "message" => "gagal booking", "data" => []], 400);
        }
    }
    public function batalkan($booking_no_order)
    {
        $user = $this->request->user;
        $booking_no_order = urldecode($booking_no_order);
        $booking = $this->model->where(['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order])->first();
        if (!$booking) {
            return $this->respond(["status" => 1, "message" => "booking tidak ditemukan", "data" => []], 200);
        }
        if ($booking['booking_status'] == 2) {
            return $this->respond(["status" => 1, "message" => "booking sudah dibatalkan", "data" => []], 200);
        }
        $update = $this->model->where(['user_email' => $user['user_email'], 'booking_no_order' => $booking_no_order])->set(['booking_status' => 2, 'booking_jml_anggota' => $booking['booking_jml_anggota'], 'booking_tgl_masuk' => $booking['booking_tgl_masuk'], 'booking_tgl_keluar' => $booking['booking_tgl_keluar']])->update();
        if ($update) {
            helper('locale');
            $tgl_masuk_text = tgl_indo($booking['booking_tgl_masuk'] ?? "");
            $tgl_keluar_text = tgl_indo($booking['booking_tgl_keluar'] ?? "");
            helper('notification');
            $user_model = new UsersModel();
            $user_emails = $user_model->where(['role' => 1])->findColumn('user_email');
            notif($user_emails, "Pembatalan Booking", "{$booking_no_order}, tanggal {$tgl_masuk_text} sampai {$tgl_keluar_text} dibatalkan.");
            return $this->respond(["status" => 1, "message" => "berhasil membatalkan booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => 0, "message" => "gagal membatalkan booking", "data" => []], 400);
        }
    }
}