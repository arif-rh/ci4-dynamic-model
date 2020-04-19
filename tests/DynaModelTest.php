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

		$authors = DB::table($table);
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);

		$this->assertSame($table, $authors->getTableName());
	}

	public function testSetPrimaryKey()
	{
		$primaryKey = 'email';

		$authors = DB::table('authors', $primaryKey);

		$this->assertSame($primaryKey, $authors->getPrimaryKey());
	}

	public function testResetQuery()
	{
		$comments = DB::table('comments');

		$comments->orderBy('id', 'asc');
		$row = $comments->resetQuery()->orderBy('id', 'desc')->first();

		$this->assertSame(4, $row['id']);
	}

	public function testLast()
	{
		$table = 'authors';

		$authors = DB::table($table);
		$author = $authors->asObject()->last();

		$this->assertSame('Tante Ais', $author->name);

		// last after deleted
		$authors->useSoftDelete()->delete(4);

		$author = $authors->asObject()->last();

		$this->assertSame('Simbah Mas', $author->name);

		// get last with deleted
		$author = $authors->asObject()->withDeleted()->last();

		$this->assertSame('Tante Ais', $author->name);
	}

	public function testPaginate()
	{
		helper('url');
		$authors = DB::table('authors');
		$page_1  = $authors->paginate(2, $authors->getDBGroup(), 1);
		$page_2  = $authors->paginate(2, $authors->getDBGroup(), 2);

		$this->assertSame(count($page_1), count($page_2));

		$this->assertSame(1, $page_1[0]['id']);
		$this->assertSame(3, $page_2[0]['id']);
	}

	public function testSoftDelete()
	{
		$table = 'authors';

		$authors = DB::table($table);
		$authors->useSoftDelete(true, 'deleted_at');

		$authors->delete(1);

		$authors->find();
		$this->assertSame(3, $authors->countAllResults());

		$authors->findAll();
		$this->assertSame(3, $authors->countAllResults());

		$authors->withDeleted()->findAll();
		$this->assertSame(4, $authors->withDeleted()->countAllResults());

		$authors->onlyDeleted()->findAll();
		$this->assertSame(1, $authors->onlyDeleted()->countAllResults());

		
	}

	public function testFindAll()
	{
		$authors = DB::table('authors');
		$authors->findAll();

		$this->assertSame(4, $authors->countAllResults());
	}

	public function testFind()
	{
		$authors = DB::table('authors');

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
		$posts = DB::table('posts');
		$posts->belongsTo('authors');

		$postAuthor = $posts->with('authors')
							->asObject()
							->find(2);

		$this->assertSame('Tante Ais', $postAuthor->name);
	}

	public function testBelongsToCustom()
	{
		$posts = DB::table('posts');

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
		$authors = DB::table('authors');
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
		$authors = DB::table('authors');

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

		// filter for empty result
		$draftArticles = $authors->with($alias)
								  ->whereRelation($alias, ['status' => 'draft'])
								  ->find(2);

		$article_status = dot_array_search('*.article', [$draftArticles]);
		$this->assertEmpty($article_status);

		$draftArticles = $authors->with($alias)
								  ->whereRelation($alias, ['status' => 'publish'])
								  ->find(2);

		$article_status = dot_array_search('*.article', [$draftArticles]);
		$this->assertSame(1, count($article_status));

		// relation when parent is empty
		$authors->useSoftDelete()->delete([1,2,3,4]);
		

		$empty_results = $authors->with($alias)
									->whereRelation($alias, ['status' => 'publish'])
									->asObject()
									->findAll();

		$this->assertEmpty($empty_results);
	}

	public function testHelper()
	{
		$authors = DB::table('authors');
		$data    = $authors->find([2]);

		$options = array_key_value($data, ['id' => 'name, email'], [], ' | ');

		$this->assertEquals($options, [2 => "Budhe Ana | budhe@world.com"]);
	}
}
