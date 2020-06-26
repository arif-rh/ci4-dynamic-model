<?php namespace Arifrh\DynaModelTests\Database\Migrations;

class Migration_Create_test_tables extends \CodeIgniter\Database\Migration
{
	/**
	 * Unique/AutoIncrement field
	 *
	 * @var string $uniqueAutoIncrement
	 */
	var $uniqueAutoIncrement = '';

	public function up()
	{
		// SQLite3 uses auto increment different
		$this->uniqueAutoIncrement = $this->db->DBDriver === 'SQLite3' ? 'unique' : 'auto_increment';

		$this->createAuthorTable();
		$this->createPostTable();
		$this->createCommentTable();
	}

	/**
	 * Create Author Table
	 *
	 * @return void
	 */
	protected function createAuthorTable()
	{
		// Author Table
		$this->forge->addField([
			'id'         => [
				'type'                     => 'INTEGER',
				'constraint'               => 3,
				$this->uniqueAutoIncrement => true,
			],
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => 80,
			],
			'email'      => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
			],
			'active'     => [
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
	}

	/**
	 * Create Post Table
	 *
	 * @return void
	 */
	protected function createPostTable()
	{
		// Post Table
		$this->forge->addField([
			'id'         => [
				'type'                     => 'INTEGER',
				'constraint'               => 3,
				$this->uniqueAutoIncrement => true,
			],
			'title'      => [
				'type'       => 'VARCHAR',
				'constraint' => 100,
			],
			'content'    => [
				'type' => 'TEXT',
				'null' => true,
			],
			'author_id'  => [
				'type'       => 'INTEGER',
				'constraint' => 3,
			],
			'status'     => [
				'type'       => 'ENUM',
				'constraint' => [
					'publish',
					'draft',
				],
				'default'    => 'draft',
			],
			'created_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'updated_at' => [
				'type' => 'DATETIME',
				'null' => true,
			],
			'deleted_at' => [
				'type'       => 'INTEGER',
				'constraint' => 11,
				'null'       => true,
			],
		]);
		$this->forge->addKey('id', true);
		$this->forge->createTable('posts', true);
	}

	/**
	 * Create Comment Table
	 *
	 * @return void
	 */
	protected function createCommentTable()
	{
		// Comment Table
		$this->forge->addField([
			'id'         => [
				'type'                     => 'INTEGER',
				'constraint'               => 3,
				$this->uniqueAutoIncrement => true,
			],
			'name'       => [
				'type'       => 'VARCHAR',
				'constraint' => 40,
			],
			'content'    => [
				'type' => 'TEXT',
				'null' => true,
			],
			'post_id'    => [
				'type'       => 'INTEGER',
				'constraint' => 3,
			],
			'status'     => [
				'type'       => 'ENUM',
				'constraint' => [
					'approved',
					'pending',
				],
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

	/**
	 * Migration Rollback
	 */
	public function down()
	{
		$this->forge->dropTable('authors', true);
		$this->forge->dropTable('posts', true);
		$this->forge->dropTable('comments', true);
	}

}
