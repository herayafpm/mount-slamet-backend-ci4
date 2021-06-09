<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Settings extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'setting_id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'setting_key' => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'unique' 		 => true,
			],
			'setting_value'       => [
				'type'           => 'TEXT',
			],
			'setting_role' => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'			=> true
			],
		]);
		$this->forge->addKey('setting_id', true);
		$this->forge->createTable('settings');
	}

	public function down()
	{
		$this->forge->dropTable('settings');
	}
}
