<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingsModel extends Model
{
	protected $DBGroup              = 'default';
	protected $table                = 'bookings';
	protected $primaryKey           = 'booking_id';
	protected $useAutoIncrement     = true;
	protected $insertID             = 0;
	protected $returnType           = 'array';
	protected $useSoftDelete        = false;
	protected $protectFields        = true;
	protected $allowedFields        = ['booking_no_order', 'user_nama', 'user_email', 'user_alamat', 'user_no_telp', 'user_no_telp_ot', 'booking_nama', 'booking_alamat', 'booking_no_telp', 'booking_jml_anggota', 'booking_tgl_masuk', 'booking_tgl_keluar', 'booking_status'];

	// Dates
	protected $useTimestamps        = true;
	protected $dateFormat           = 'datetime';
	protected $createdField         = 'booking_created';
	protected $updatedField         = '';
	protected $deletedField         = '';

	// Validation
	protected $validationRules      = [];
	protected $validationMessages   = [];
	protected $skipValidation       = false;
	protected $cleanValidationRules = true;

	// Callbacks
	protected $allowCallbacks       = true;
	protected $beforeInsert         = ['addNoOrder'];
	protected $afterInsert          = [];
	protected $beforeUpdate         = [];
	protected $afterUpdate          = ['insertBookingSeat'];
	protected $beforeFind           = [];
	protected $afterFind            = [];
	protected $beforeDelete         = [];
	protected $afterDelete          = [];
	function addNoOrder(array $datas)
	{
		$builder = $this->db->table($this->table);
		$noUrut = "BK" . date('Ymd');
		$data = $builder->select('booking_no_order')->like('booking_no_order', $noUrut)->orderBy($this->primaryKey, 'DESC')->get()->getRowArray();
		if ($data) {
			$noUrut = $data['booking_no_order'];
			$str = substr($noUrut, 0, 2);
			$date = substr($noUrut, 2, 8);
			$no = substr($noUrut, 10);
			$no = (int) $no + 1;
			$datas['data']['booking_no_order'] = $str . $date . $no;
			return $datas;
		} else {
			$datas['data']['booking_no_order'] = $noUrut . "1";
			return $datas;
		}
	}
	protected function insertBookingSeat($data)
	{
		$status = $data['data']['booking_status'] ?? 0;
		if (in_array($status, [1, 2])) {
			$tgl_masuk = date("Y-m-d", strtotime($data['data']['booking_tgl_masuk']));
			$tgl_keluar = date("Y-m-d", strtotime($data['data']['booking_tgl_keluar']));
			helper('my_date');
			$dates = displayDates($tgl_masuk, $tgl_keluar);
			$insert_booking_seat_data = [];
			$booking_seat_model = new BookingSeatsModel();
			foreach ($dates as $date) {
				$jml = (int) $data['data']['booking_jml_anggota'];
				$date = date("Y-m-d H:i:s", strtotime($date));
				$booking_data = [
					'booking_seat_tgl' => $date,
					'booking_seat_jml' => $jml,
				];
				$booking_seat = $booking_seat_model->where(['booking_seat_tgl' => $date])->first();
				if ($booking_seat) {
					if ((int)$booking_seat['booking_seat_jml'] != 0) {
						if ($status == 1) {
							$jml = (int) $booking_seat['booking_seat_jml'] + $jml;
						}
						if ($status == 2) {
							$jml = (int) $booking_seat['booking_seat_jml'] - $jml;
						}
						$booking_data['booking_seat_jml'] = $jml;
						$booking_seat_model->update($booking_seat['booking_seat_id'], $booking_data);
					}
				} else {
					if ($status == 1) {
						array_push($insert_booking_seat_data, $booking_data);
					}
				}
			}
			if (sizeof($insert_booking_seat_data) > 0) {
				$booking_seat_model->insertBatch($insert_booking_seat_data);
			}
		} else if (in_array($status, [3])) {
			$tgl_masuk = date("Y-m-d", strtotime($data['data']['booking_tgl_masuk']));
			$tgl_keluar = date("Y-m-d", strtotime($data['data']['booking_tgl_keluar']));
			helper('my_date');
			$dates = displayDates($tgl_masuk, $tgl_keluar);
			$booking_seat_model = new BookingSeatsModel();
			foreach ($dates as $date) {
				$jml = (int) $data['data']['booking_jml_anggota'];
				$date = date("Y-m-d H:i:s", strtotime($date));
				$booking_data = [
					'booking_seat_tgl' => $date,
					'booking_seat_jml' => $jml,
				];
				$booking_seat = $booking_seat_model->where(['booking_seat_tgl' => $date])->first();
				if ($booking_seat) {
					if ((int)$booking_seat['booking_seat_jml'] != 0) {
						$jml = (int) $booking_seat['booking_seat_jml'] - $jml;
						$booking_data['booking_seat_jml'] = $jml;
						$booking_seat_model->update($booking_seat['booking_seat_id'], $booking_data);
					}
				}
			}
		}
		return $data['result'];
	}
	public function filter($limit, $start, $params = [])
	{
		$builder = $this->db->table($this->table);
		$builder->orderBy($this->primaryKey, 'desc');
		if ($limit > 0) {
			$builder->limit($limit, $start);
		}
		$fields = $this->allowedFields;
		array_push($fields, $this->createdField);
		array_walk($fields, function (&$value, $key) {
			$value = "{$this->table}." . $value;
		});
		// unset($fields[0]);
		$builder->select(implode(",", $fields));
		if (isset($params['where'])) {
			$builder->where($params['where']);
		}
		if (isset($params['like'])) {
			foreach ($params['like'] as $key => $value) {
				$builder->like($key, $value);
			}
		}
		if (isset($params['where_in'])) {
			foreach ($params['where_in'] as $key => $value) {
				$builder->whereIn($key, $value);
			}
		}
		$datas = $builder->get()->getResultArray();
		return $datas;
	}
}
