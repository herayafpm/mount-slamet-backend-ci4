<?php

namespace App\Controllers\Admin;

use CodeIgniter\RESTful\ResourceController;
use Dompdf\Dompdf;
use Dompdf\Options;

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
        $booking_status = $dataGet["booking_status"] ?? 0;
        $cari = $dataGet["cari"] ?? "";
        $semua = $dataGet["semua"] ?? 1;
        if ($semua == 1) {
            $riwayat_booking = $this->model->filter($limit, $offset, ['where' => ['booking_status' => $booking_status], 'like' => ['booking_no_order' => $cari]]);
        } else {
            $tgl_awal = date("Y-m-d H:i:s", strtotime($dataGet["booking_tgl_masuk"] ?? date("Y-m-d") . " 00:00:00"));
            $tgl_akhir = date("Y-m-d", strtotime($dataGet["booking_tgl_keluar"] ?? date("Y-m-d") . " 23:59:59"));
            $riwayat_booking = $this->model->filter($limit, $offset, ['where' => ['booking_status' => $booking_status, 'booking_tgl_masuk >=' => $tgl_awal, 'booking_tgl_masuk <=' => $tgl_akhir], 'like' => ['booking_no_order' => $cari]]);
        }
        return $this->respond(["status" => true, "message" => "berhasil mendapatkan semua booking", "data" => $riwayat_booking], 200);
    }
    public function detail($booking_no_order)
    {
        $user = $this->request->user;
        $booking = $this->model->where(['booking_no_order' => $booking_no_order])->first();
        unset($booking['booking_id']);
        return $this->respond(["status" => true, "message" => "berhasil mendapatkan data booking", "data" => $booking], 200);
    }
    public function konfirmasi($booking_no_order)
    {
        $user = $this->request->user;
        $booking_no_order = urldecode($booking_no_order);
        $booking = $this->model->where(['booking_no_order' => $booking_no_order])->first();
        if (!$booking) {
            return $this->respond(["status" => false, "message" => "booking tidak ditemukan", "data" => []], 200);
        }
        $update = $this->model->where(['booking_no_order' => $booking_no_order])->set(['booking_status' => 1, 'booking_jml_anggota' => $booking['booking_jml_anggota'], 'booking_tgl_masuk' => $booking['booking_tgl_masuk'], 'booking_tgl_keluar' => $booking['booking_tgl_keluar']])->update();
        if ($update) {
            helper('locale');
            $tgl_masuk_text = tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_masuk'])) ?? "");
            $tgl_keluar_text = tgl_indo(date("Y-m-d", strtotime($booking['booking_tgl_keluar'])) ?? "");
            helper('notification');
            notif([$booking['user_email']], "Konfirmasi Booking", "{$booking_no_order}, tanggal {$tgl_masuk_text} sampai {$tgl_keluar_text} telah dikonfirmasi admin, terima kasih.");
            return $this->respond(["status" => true, "message" => "berhasil mengkonfirmasi booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => false, "message" => "gagal mengkonfirmasi booking", "data" => []], 200);
        }
    }
    public function selesai($booking_no_order)
    {
        $user = $this->request->user;
        $booking_no_order = urldecode($booking_no_order);
        $booking = $this->model->where(['booking_no_order' => $booking_no_order])->first();
        if (!$booking) {
            return $this->respond(["status" => false, "message" => "booking tidak ditemukan", "data" => []], 200);
        }
        if ($booking['booking_status'] != 1) {
            return $this->respond(["status" => false, "message" => "silahkan konfirmasi booking terlebih dahulu", "data" => []], 200);
        }
        $update = $this->model->where(['booking_no_order' => $booking_no_order])->set(['booking_status' => 2, 'booking_jml_anggota' => $booking['booking_jml_anggota'], 'booking_tgl_masuk' => $booking['booking_tgl_masuk'], 'booking_tgl_keluar' => $booking['booking_tgl_keluar']])->update();
        if ($update) {
            helper('notification');
            notif([$booking['user_email']], "Terima Kasih", "Terima kasih sudah menggunakan jasa kami.");
            return $this->respond(["status" => true, "message" => "berhasil menyelesaikan booking", "data" => []], 200);
        } else {
            return $this->respond(["status" => false, "message" => "gagal menyelesaikan booking", "data" => []], 200);
        }
    }
    public function laporan()
    {
        helper('locale');
        $dataJson = $this->request->getJson();
        $now = date("Y-m-d") . " 00:00:00";
        $now_add = date("Y-m-d H:i:s", strtotime($now . "+1 day" . " 23:59:59"));
        $tgl_awal = $dataJson->tgl_awal ?? $now;
        $tgl_akhir = $dataJson->tgl_akhir ?? $now_add;
        $tgl_awal = date("Y-m-d H:i:s", strtotime($tgl_awal));
        $tgl_akhir = date("Y-m-d H:i:s", strtotime($tgl_akhir . " 23:59:59"));
        if ($tgl_awal <= $tgl_akhir && $tgl_akhir >= $tgl_awal) {
            $bookings = $this->model->filter(0, 0, ['where' => ['booking_tgl_masuk >=' => $tgl_awal, 'booking_tgl_masuk <=' => $tgl_akhir], 'where_in' => ['booking_status' => [1, 2]]]);
            $tanggal_text = tgl_indo(date("Y-m-d", strtotime($tgl_awal))) . " sampai " . tgl_indo(date("Y-m-d", strtotime($tgl_akhir)));
            if ($tgl_awal === $tgl_akhir) {
                $tanggal_text = tgl_indo(date("Y-m-d", strtotime($tgl_awal)));
            }
            $data['_bookings'] = $bookings;
            $data['_view'] = 'print_laporan';
            $options = new Options();
            $options->set('isRemoteEnabled', true);
            $dompdf = new Dompdf($options);
            $title_laporan = "Laporan $tanggal_text";
            $data['title'] = $title_laporan;
            $dompdf->loadHtml(view($data['_view'], $data));
            $dompdf->setPaper('A4', 'landscape');
            $file_name = "$title_laporan.pdf";
            $dompdf->render();
            $output = $dompdf->output();
            header('Content-Type: application/pdf');
            header("Content-Transfer-Encoding: Binary");
            header("Content-disposition: attachment; filename=\"" . $file_name . "\"");
            header("Filename: $file_name");
            return $output;
        } else {
            return $this->respond(["status" => false, "message" => "tanggal awal harus kurang atau sama dengan dari tanggal akhir", "data" => []], 200);
        }
    }
}
