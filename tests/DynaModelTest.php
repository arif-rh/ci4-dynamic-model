<?php 

declare(strict_types=1);

use Arifrh\DynaModel\DB;
use Arifrh\DynaModelTests\DynaModelTestCase as TestCase;

class DynaModelTest extends TestCase
{
	public function testInitFromClass()
	{
		$authors = new Arifrh\DynaModelTests\Models\AuthorModel();
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);
	}

	public function testInitDynamically()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);
	}

	public function testFindAll()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->findAll();

		$this->assertSame(4, $authors->countAllResults());
	}

	public function testFind()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->find();

		$this->assertSame(4, $authors->countAllResults());
	}

	public function testFindOne()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->find(1, false);

		$this->assertSame(1, $authors->countAllResults());
	}

	public function testFindTwo()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->find([1,2], false);

		$this->assertSame(2, $authors->countAllResults());
	}

	public function testBelongsTo()
	{
		$posts = Arifrh\DynaModel\DB::table('posts');
		$posts->belongsTo('authors');

		$postAuthor = $posts->with('authors')
							->asObject()
							->find(2);

		$this->assertSame('Tante Ais', $postAuthor->name);
		
		// give alias name for name field
		$postAuthor = $posts->with('authors', ['name as author_name'])
							->asObject()
							->find(2);

		$this->assertSame('Tante Ais', $postAuthor->author_name);
	}

	public function testBelongsToWhereRelation()
	{
		$posts = Arifrh\DynaModel\DB::table('posts');

		$allPosts = $posts->findAll();
		$this->assertSame(5, $posts->countAllResults());

		$posts->belongsTo('authors');

		$postAuthor = $posts->with('authors')
							->whereRelation('authors', ['active' => 1])
							->findAll();

		$this->assertSame(4, count($postAuthor));

		$postAuthor = $posts->with('authors')
							->whereRelation('authors', ['active' => 0])
							->findAll();

		$this->assertSame(1, count($postAuthor));

		// filter based on array conditions
		$postAuthor = $posts->with('authors')
							->whereRelation('authors', ['email' => ['pakdhe@world.com','budhe@world.com']])
							->findAll();

		$this->assertSame(3, count($postAuthor));
	}

	public function testHasMany()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->hasMany('posts');

		$authorPosts = $authors->with('posts')->find(1);

		$this->assertSame(2, count($authorPosts['posts']));
	}
}
