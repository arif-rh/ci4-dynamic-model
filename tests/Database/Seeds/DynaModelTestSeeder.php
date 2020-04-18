<?php namespace Arifrh\DynaModelTests\Database\Seeds;

class DynaModelTestSeeder extends \CodeIgniter\Database\Seeder
{
	public function run()
	{
		// Job Data
		$data = [
			'authors' => [
				[
					'name'    => 'Pakdhe Sani',
					'email'   => 'pakdhe@world.com',
					'active'  => 1,
				],
				[
					'name'    => 'Budhe Ana',
					'email'   => 'budhe@world.com',
					'active'  => 0,
				],
				[
					'name'    => 'Simbah Mas',
					'email'   => 'simbah@world.com',
					'active'  => 1,
				],
				[
					'name'    => 'Tante Ais',
					'email'   => 'tante@world.com',
					'active'  => 1,
				],
			],
			'posts'  => [
				[
					'title'     => 'CI4 Dynamic Model is Awesome',
					'content'   => 'Awesome job, but sometimes makes you bored.',
					'author_id' => 1,
					'status'    => 'publish'
				],
				[
					'title'     => 'CI4 Dynamic Model is Coming',
					'content'   => 'Awesome Codeigniter 4 Model Library is Coming out.',
					'author_id' => 4,
					'status'    => 'publish'
				],
				[
					'title'     => 'CI4 Dynamic Model Released',
					'content'   => 'Awesome Release, use it.',
					'author_id' => 1,
					'status'    => 'draft'
				],
				[
					'title'     => 'CI4 Dynamic Model Docs',
					'content'   => 'It need a lot of work to make full documentation. Help wanted.',
					'author_id' => 2,
					'status'    => 'publish'
				],
				[
					'title'     => 'Awesome',
					'content'   => 'Test is awesome.',
					'author_id' => 3,
					'status'    => 'publish'
				]
			],
			'comments' => [
				[
					'name'     => 'Hanan',
					'content'  => 'Good job. Keep Rocking!',
					'post_id'  => 1,
					'status'   => 'approved'
				],
				[
					'name'     => 'Hanan',
					'content'  => 'Can\'t wait to try it.',
					'post_id'  => 2,
					'status'   => 'pending'
				],
				[
					'name'     => 'Hanif',
					'content'  => 'Absolutely awesome.',
					'post_id'  => 1,
					'status'   => 'approved'
				],
				[
					'name'     => 'Hanan',
					'content'  => 'Good job. Keep Rocking!',
					'post_id'  => 1,
					'status'   => 'approved'
				]
			],
		];

		foreach ($data as $table => $dummy_data)
		{
			$this->db->table($table)->truncate();

			foreach ($dummy_data as $single_dummy_data)
			{
				$this->db->table($table)->insert($single_dummy_data);
			}
		}
	}

	//--------------------------------------------------------------------

}
