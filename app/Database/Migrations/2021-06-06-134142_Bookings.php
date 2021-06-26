<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Bookings extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'booking_id' => [
				'type' => 'INT',
				'constraint'     => 11,
				'unsigned'          => TRUE,
				'auto_increment' => true,
			],
			'booking_no_order' => [
				'type' => 'VARCHAR',
				'constraint'     => '255',
				'unique'				=> true
			],
			'user_nama'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'user_email'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'user_alamat'       => [
				'type'           => 'TEXT',
			],
			'user_no_telp'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'			=> true,
			],
			'user_no_telp_ot'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
				'null'			=> true,
			],
			'booking_nama' => [
				'type' => 'VARCHAR',
				'constraint'     => '255',
			],
			'booking_alamat' => [
				'type' => 'TEXT',
			],
			'booking_no_telp' => [
				'type' => 'VARCHAR',
				'constraint'     => '255',
			],
			'booking_jml_anggota' => [
				'type' => 'INT',
				'constraint'     => 11,
				'default' => 1
			],
			'booking_tgl_masuk'       => [
				'type'           => 'TIMESTAMP',
				'default'		=> '0000-00-00 00:00:00'
			],
			'booking_tgl_keluar'       => [
				'type'           => 'TIMESTAMP',
				'default'		=> '0000-00-00 00:00:00'
			],
			'booking_status' => [
				'type' => 'INT',
				'constraint'     => 1,
				'default' => 0
			],
			'booking_created'       => [
				'type'           => 'TIMESTAMP',
				'default' => date('Y-m-d H:i:s')
			],
		]);
		$this->forge->addKey('booking_id', true);
		$this->forge->createTable('bookings');
	}

	public function down()
	{
		$this->forge->dropTable('bookings');
	}
}
