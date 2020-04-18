<?php namespace Arifrh\DynaModelTests\Migrations;

class Migration_Create_test_tables extends \CodeIgniter\Database\Migration
{
	public function up()
	{
		// SQLite3 uses auto increment different
		$unique_or_auto = $this->db->DBDriver === 'SQLite3' ? 'unique' : 'auto_increment';

		// Author Table
		$this->forge->addField([
			'id'         => [
				'type'          => 'INTEGER',
				'constraint'    => 3,
				$unique_or_auto => true,
			],
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => 80,
			],
			'email'      => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
			],
			'active'    => [
				'type'       => 'TINYINT',
				'constraint' => 1,
			],
			'deleted_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('authors', true);

		// Post Table
		$this->forge->addField([
			'id'          => [
				'type'          => 'INTEGER',
				'constraint'    => 3,
				$unique_or_auto => true,
			],
			'title'        => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
			],
			'content' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'author_id'   => [
				'type'          => 'INTEGER',
				'constraint'    => 3
			],
			'status'  => [
				'type'       => 'ENUM',
				'constraint' => ['publish', 'draft'],
				'default'    => 'draft',
			],
			'deleted_at'  => [
				'type'       => 'INTEGER',
				'constraint' => 11,
				'null'       => true,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('posts', true);

		// Comment Table
		$this->forge->addField([
			'id'         => [
				'type'          => 'INTEGER',
				'constraint'    => 3,
				$unique_or_auto => true,
			],
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => 40,
			],
			'content' => [
				'type' => 'TEXT',
				'null' => true,
			],
			'post_id'   => [
				'type'          => 'INTEGER',
				'constraint'    => 3
			],
			'status'  => [
				'type'       => 'ENUM',
				'constraint' => ['approved', 'pending'],
				'default'    => 'pending',
			],
			'deleted_at' => [
				'type' => 'DATE',
				'null' => true,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('comments', true);
	}

	//--------------------------------------------------------------------

	public function down()
	{
		$this->forge->dropTable('authors', true);
		$this->forge->dropTable('posts', true);
		$this->forge->dropTable('comments', true);
	}

	//--------------------------------------------------------------------

}
