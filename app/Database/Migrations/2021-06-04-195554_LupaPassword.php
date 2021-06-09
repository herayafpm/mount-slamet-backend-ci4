<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class LupaPassword extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'kode_otp' => [
				'type'           => 'INT',
				'constraint'     => 6,
			],
			'lupa_password_created'       => [
				'type'           => 'TIMESTAMP',
				'default' => date('Y-m-d H:i:s')
			],
		]);
		$this->forge->createTable('lupa_password');
	}

	public function down()
	{
		$this->forge->dropTable('lupa_password');
	}
}
