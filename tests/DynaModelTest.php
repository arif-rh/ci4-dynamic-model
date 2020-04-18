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
		$table = 'authors';

		$authors = Arifrh\DynaModel\DB::table($table);
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);

		$this->assertSame($table, $authors->getTableName());
	}

	public function testSetPrimaryKey()
	{
		$primaryKey = 'email';

		$authors = Arifrh\DynaModel\DB::table('authors', $primaryKey);

		$this->assertSame($primaryKey, $authors->getPrimaryKey());
	}

	public function testSoftDelete()
	{
		$table = 'authors';

		$authors = Arifrh\DynaModel\DB::table($table);
		$authors->useSoftDelete(true, 'deleted_at');

		$authors->delete(1);

		$authors->find();

		$this->assertSame(3, $authors->countAllResults());

		$authors->withDeleted()->find();

		$this->assertSame(4, $authors->withDeleted()->countAllResults());

		$authors->onlyDeleted()->find();

		$this->assertSame(1, $authors->onlyDeleted()->countAllResults());

		
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

		// an alias to findAll
		$authors->find();

		$this->assertSame(4, $authors->countAllResults());
	
		// find one key
		$authors->find(1, false);

		$this->assertSame(1, $authors->countAllResults());
	
		// find using array
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
	}

	public function testBelongsToCustom()
	{
		$posts = Arifrh\DynaModel\DB::table('posts');

		$allPosts = $posts->findAll();
		$this->assertSame(5, $posts->countAllResults());

		$parentTable = 'authors';

		$posts->belongsTo($parentTable);

		// give alias name for name field
		$postAuthor = $posts->with($parentTable, ['name as author_name'])
							->asObject()
							->find(2);

		$this->assertSame('Tante Ais', $postAuthor->author_name);

		// filter only active author
		$postAuthor = $posts->with($parentTable)
							->whereRelation($parentTable, ['active' => 1])
							->findAll();

		$this->assertSame(4, count($postAuthor));

		$postAuthor = $posts->with($parentTable)
							->whereRelation($parentTable, ['active' => 0])
							->findAll();

		$this->assertSame(1, count($postAuthor));

		// filter based on array conditions
		$postAuthor = $posts->with($parentTable)
							->whereRelation($parentTable, ['email' => ['pakdhe@world.com','budhe@world.com']])
							->findAll();

		$this->assertSame(3, count($postAuthor));
	}

	public function testHasMany()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');
		$authors->hasMany('posts');

		$authorPosts = $authors->with('posts')->find(1);

		$this->assertSame(2, count($authorPosts['posts']));

		$authorPosts = $authors->with('posts')
								->asObject()
								->find(1);

		$this->assertSame(2, count($authorPosts->posts));
	}

	public function testHasManyCustom()
	{
		$authors = Arifrh\DynaModel\DB::table('authors');

		// set relation with alias and ordering
		$alias = 'article';
		$authors->hasMany('posts', 'author_id', $alias, ['status' => 'asc']);

		$authorArticles = $authors->with($alias)
								  ->find([1,3]);

		$article_status = dot_array_search('*.article.*.status', $authorArticles);
		
		$this->assertSame('draft', $article_status);

		// set relation with alias and ordering
		$authors->hasMany('posts', 'author_id', $alias, ['status' => 'desc']);

		$authorArticles = $authors->with($alias)
								   ->find([1,3]);

		$article_status = dot_array_search('*.article.*.status', $authorArticles);
		
		$this->assertSame('publish', $article_status);

		// filtered based on publish status
		$publish_articles = $authors->with($alias)
									->whereRelation($alias, ['status' => 'publish'])
									->asObject()
									->findAll();

		$this->assertSame(4, count($publish_articles));
	}
}
