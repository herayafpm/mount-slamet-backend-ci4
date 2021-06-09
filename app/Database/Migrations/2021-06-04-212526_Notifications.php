<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Notifications extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'notification_id' => [
				'type' => 'INT',
				'constraint'     => 11,
				'unsigned'          => TRUE,
				'auto_increment' => true,
			],
			'user_email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'notification_title' => [
				'type' => 'VARCHAR',
				'constraint'     => '255',
			],
			'notification_body' => [
				'type' => 'TEXT',
			],
			'notification_read' => [
				'type' => 'INT',
				'constraint'     => 1,
				'default' => 0
			],
			'notification_created'       => [
				'type'           => 'TIMESTAMP',
				'default' => date('Y-m-d H:i:s')
			],
		]);
		$this->forge->addKey('notification_id', true);
		$this->forge->createTable('notifications');
	}

	public function down()
	{
		$this->forge->dropTable('notifications');
	}
}
