<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BookingSeats extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'booking_seat_id' => [
				'type' => 'INT',
				'constraint'     => 11,
				'unsigned'          => TRUE,
				'auto_increment' => true,
			],
			'booking_seat_tgl'       => [
				'type'           => 'TIMESTAMP',
			],
			'booking_seat_jml' => [
				'type' => 'INT',
				'constraint'     => 11,
			],

		]);
		$this->forge->addKey('booking_seat_id', true);
		$this->forge->createTable('booking_seats');
	}

	public function down()
	{
		$this->forge->dropTable('booking_seats');
	}
}
