<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UserFcms extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'user_fcm_id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'user_email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'user_fcm'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
		]);
		$this->forge->addKey('user_fcm_id', true);
		$this->forge->createTable('user_fcms');
	}

	public function down()
	{
		$this->forge->dropTable('user_fcms');
	}
}
