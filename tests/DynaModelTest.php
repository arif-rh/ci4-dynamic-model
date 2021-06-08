<?php namespace Arifrh\DynaModelTests;

use Arifrh\DynaModel\DB;
use Arifrh\DynaModelTests\DynaModelTestCase as TestCase;

final class DynaModelTest extends TestCase
{
	public function testInitFromClass():void
	{
		$authors = new \Arifrh\DynaModelTests\Models\AuthorModel();
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);
	}

	public function testInitDynamically():void
	{
		$table = 'authors';

		$authors = DB::table($table);
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);

		$this->assertSame($table, $authors->getTableName());
	}

	public function testSetPrimaryKey():void
	{
		$primaryKey = 'email';

		$authors = DB::table('authors', $primaryKey);

		$this->assertSame($primaryKey, $authors->getPrimaryKey());
	}

	public function testResetQuery():void
	{
		$comments = DB::table('comments');

		$comments->setOrderBy(['id' => 'asc']);
		$row = $comments->resetQuery()->setOrderBy(['id' => 'desc'])->first();

		$this->assertSame(4, $row['id']);
	}

	public function testLast():void
	{
		$table = 'authors';

		$authors = DB::table($table);
		$author  = $authors->last();

		$this->assertSame('Tante Ais', $author['name']);

		// last after deleted
		$authors->useSoftDelete()->delete(4);

		$author = $authors->last();

		$this->assertSame('Simbah Mas', $author['name']);

		// get last with deleted
		$authors->asObject()->withDeleted();
		$author = $authors->last();

		$this->assertSame('Tante Ais', $author->name);
	}

	public function testPaginate():void
	{
		helper('url');

		$authors = DB::table('authors');

		$page_1 = $authors->paginate(2, $authors->getDBGroup(), 1);
		$page_2 = $authors->paginate(2, $authors->getDBGroup(), 2);

		$this->assertCount(count($page_1), $page_2);

		$this->assertSame(1, $page_1[0]['id']);
		$this->assertSame(3, $page_2[0]['id']);
	}

	public function testSoftDelete():void
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

	public function testFindAll():void
	{
		$authors = DB::table('authors');
		$authors->findAll();

		$this->assertSame(4, $authors->countAllResults());
	}

	public function testFind():void
	{
		$authors = DB::table('authors');

		// an alias to findAll
		$authors->find();

		$this->assertSame(4, $authors->countAllResults());

		// find one key
		$authors->find(1, false);

		$this->assertSame(1, $authors->countAllResults());

		// find using array
		$authors->find([1, 2], false);

		$this->assertSame(2, $authors->countAllResults());
	}

	public function testBelongsTo():void
	{
		$posts = DB::table('posts');
		$posts->belongsTo('authors');

		$postAuthor = $posts->with('authors')
							->asObject()
							->find(2);

		$this->assertSame('Tante Ais', $postAuthor->name);
	}

	public function testBelongsToCustom():void
	{
		$posts = DB::table('posts');

		$posts->findAll();
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

		$this->assertCount(4, $postAuthor);

		$postAuthor = $posts->with($parentTable)
							->whereRelation($parentTable, ['active' => 0])
							->findAll();

		$this->assertCount(1, $postAuthor);

		// filter based on array conditions
		$postAuthor = $posts->with($parentTable)
							->whereRelation($parentTable, ['email' => ['pakdhe@world.com', 'budhe@world.com']])
							->findAll();

		$this->assertCount(3, $postAuthor);
	}

	public function testHasMany():void
	{
		$authors = DB::table('authors');
		$authors->hasMany('posts');

		$authorPosts = $authors->with('posts')->find(1);

		$this->assertCount(2, $authorPosts['posts']);

		$authorPosts = $authors->with('posts')
								->asObject()
								->find(1);

		$this->assertCount(2, $authorPosts->posts);
	}

	public function testHasManyCustom():void
	{
		$authors = DB::table('authors');

		// set relation with alias and ordering
		$alias = 'article';
		$authors->hasMany('posts', 'author_id', $alias, ['status' => 'asc']);

		$authorArticles = $authors->with($alias)
								  ->find([1]);

		$article_status = dot_array_search('*.article.0.status', $authorArticles);
		$this->assertSame('draft', $article_status);

		// set relation with alias and ordering
		$authors->hasMany('posts', 'author_id', $alias, ['status' => 'desc']);

		$authorArticles = $authors->with($alias)
								   ->find([1]);

		$article_status = dot_array_search('*.article.0.status', $authorArticles);
		$this->assertSame('publish', $article_status);

		// filtered based on publish status
		$publish_articles = $authors->with($alias)
									->whereRelation($alias, ['status' => 'publish'])
									->asObject()
									->findAll();

		$this->assertCount(4, $publish_articles);

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
		$this->assertCount(1, $article_status);

		// relation when parent is empty
		$authors->useSoftDelete()->delete([1, 2, 3, 4]);

		$empty_results = $authors->with($alias)
									->whereRelation($alias, ['status' => 'publish'])
									->asObject()
									->findAll();

		$this->assertEmpty($empty_results);
	}

	public function testHelper():void
	{
		$authors = DB::table('authors');
		$data    = $authors->find([2]);

		$options = array_key_value($data, ['id' => 'name, email'], [], ' | ');

		$this->assertEquals($options, [2 => 'Budhe Ana | budhe@world.com']);
	}

	public function testInsert():void
	{
		$authors = DB::table('authors');

		$data = [
			'name'   => 'Ira',
			'email'  => 'ira@mail.com',
			'active' => 0,
		];

		$insertID = $authors->insert($data);

		$inserted = $authors->find($insertID);
		unset($inserted['id']);
		unset($inserted['deleted_at']);

		$this->assertSame($data, $inserted);
	}

	public function testSave():void
	{
		$authors = DB::table('authors');

		$data = [
			'name'   => 'Ira',
			'email'  => 'ira@mail.com',
			'active' => 0,
		];

		$this->assertTrue($authors->save($data));

		$data['id']   = 5;
		$data['name'] = 'Ari';

		$this->assertTrue($authors->save($data));

		$author = $authors->find($data['id']);

		$this->assertSame($data['name'], $author['name']);
	}

	public function testAllowedFields():void
	{
		$posts = DB::table('posts');
		$posts->setAllowedFields(['title', 'author_id']);

		$data = [
			'title'     => 'Hello World!',
			'content'   => 'This content should not be saved',
			'author_id' => 1,
		];

		$posts->save($data);

		$post = $posts->where('title', 'Hello World!')->find();

		$this->assertNull($post[0]['content']);
	}

	public function testProtectedFields():void
	{
		$posts = DB::table('posts');
		$posts->setProtectedFields(['content']);

		$data = [
			'title'     => 'Hello World!',
			'content'   => 'This content should not be saved',
			'author_id' => 1,
			'status'    => 'draft',
		];

		$posts->save($data);

		$post = $posts->where('title', 'Hello World!')->find();

		$this->assertNull($post[0]['content']);
	}

	public function testTimestamps():void
	{
		$posts = DB::table('posts');
		$posts->useTimestamp();

		$data = [
			'title'     => 'Hello World!',
			'content'   => 'This content should not be saved',
			'author_id' => 1,
			'status'    => 'draft',
		];

		$posts->save($data);

		$post = $posts->where('title', 'Hello World!')->find();

		$this->assertNotNull($post[0]['created_at']);
		$this->assertNotNull($post[0]['updated_at']);
	}

	public function testFindBy()
	{
		$posts        = DB::table('posts');
		$publishPosts = $posts->findBy(['status' => 'publish']);

		$this->assertCount(4, $publishPosts);

		$posts        = DB::table('posts');
		$publishPosts = $posts->findBy([
			'status' => 'publish',
			'id'     => [
				1,
				2,
			],
		]);

		$this->assertCount(2, $publishPosts);
	}

	public function testUpdatAndFindOneBy()
	{
		$posts = DB::table('posts');
		$posts->updateBy(
			['status' => 'draft'],
			['title' => 'CI4 Dynamic Model is Awesome']
		);

		$publishPost = $posts->findOneBy(['title' => 'CI4 Dynamic Model is Awesome']);

		$this->assertSame('draft', $publishPost['status']);
	}

	public function testDeleteBy()
	{
		$posts = DB::table('posts');
		$posts->deleteBy([
			'status' => 'publish',
			'id'     => [
				3,
				4,
			],
		]);

		$publishPosts = $posts->findBy([
			'status' => 'publish',
			'id'     => [
				3,
				4,
			],
		]);

		$this->assertEmpty($publishPosts);
	}

	public function testSoftDeleteBy()
	{
		$posts = DB::table('posts');
		$posts->useSoftDelete();
		$posts->deleteBy([
			'status' => 'publish',
			'id'     => [2],
		]);

		$publishPosts = $posts->findBy([
			'status' => 'publish',
			'id'     => [2],
		]);

		$this->assertEmpty($publishPosts);

		$publishPosts = $posts->withDeleted()->findBy([
			'status' => 'publish',
			'id'     => [2],
		]);

		$this->assertCount(1, $publishPosts);
	}
}
