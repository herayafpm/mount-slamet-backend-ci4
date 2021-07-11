<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Blogs extends Migration
{
	public function up()
	{
		$this->forge->addField([
			'blog_id'          => [
				'type'           => 'INT',
				'constraint'     => 11,
				'unsigned'       => true,
				'auto_increment' => true,
			],
			'blog_judul'       => [
				'type'           => 'VARCHAR',
				'constraint'     => '255',
			],
			'blog_isi'       => [
				'type'           => 'TEXT',
			],
			'blog_created_at' => [
				'type' => 'TIMESTAMP',
				'default' => "00-00-00 00:00:00"
			],
			'blog_updated_at' => [
				'type' => 'TIMESTAMP',
				'default' => "00-00-00 00:00:00"
			],
			'blog_deleted_at' => [
				'type' => 'TIMESTAMP',
				'null' => true
			],
		]);
		$this->forge->addKey('blog_id', true);
		$this->forge->createTable('blogs');
	}

	public function down()
	{
		$this->forge->dropTable('blogs');
	}
}
