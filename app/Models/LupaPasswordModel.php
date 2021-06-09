<?php

namespace App\Models;

use CodeIgniter\Model;

class LupaPasswordModel extends Model
{
	protected $table      = 'lupa_password';

	protected $returnType     = 'array';

	protected $allowedFields = ['user_email', 'kode_otp'];

	protected $useTimestamps = true;
	protected $createdField  = 'lupa_password_created';
	protected $updatedField  = '';

	public function saveLupaPassword($data)
	{
		$exist = $this->where('user_email', $data['user_email'])->get()->getRow();
		if ($exist) {
			$this->where('user_email', $data['user_email'])->delete();
		}
		return $this->save($data);
	}
	public function deleteLupaPassword($user_email)
	{
		return $this->where('user_email', $user_email)->delete();;
	}
	public function cek_email_kode($data)
	{
		$exist = $this->where($data)->get()->getRow();
		if ($exist) {
			return true;
		}
		return false;
	}
}
