<?php

namespace App\Models;

use CodeIgniter\Model;

class NotificationsModel extends Model
{
	protected $table      = 'notifications';
	protected $primaryKey = 'notification_id';

	protected $returnType     = 'array';

	protected $allowedFields = ['user_email', 'notification_title', 'notification_body', 'notification_read'];

	protected $useTimestamps = true;
	protected $createdField  = 'notification_created';
	protected $updatedField  = '';
	public function filter($limit, $start, $params = [])
	{
		$builder = $this->db->table($this->table);
		$builder->orderBy($this->primaryKey, 'desc'); // Untuk menambahkan query ORDER BY
		$builder->limit($limit, $start); // Untuk menambahkan query LIMIT
		$fields = $this->allowedFields;
		array_push($fields, $this->primaryKey);
		array_push($fields, $this->createdField);
		array_walk($fields, function (&$value, $key) {
			$value = "{$this->table}." . $value;
		});
		unset($fields[0]);
		$builder->select(implode(",", $fields));
		if (isset($params['where'])) {
			$builder->where($params['where']);
		}
		$datas = $builder->get()->getResultArray();
		return $datas; // Eksekusi query sql sesuai kondisi diatas
	}
	public function count_all($params = [])
	{
		$builder = $this->db->table($this->table);
		if (isset($params['where'])) {
			$builder->where($params['where']);
		}
		$data = $builder->countAllResults();
		return $data;
	}

	public function baca($notification_id, $user_email)
	{
		return $this->where([$this->primaryKey => $notification_id, 'user_email' => $user_email])->set(['notification_read' => 1])->update();
	}
	public function baca_semua($user_email)
	{
		return $this->where(['user_email' => $user_email])->set(['notification_read' => 1])->update();
	}
}
