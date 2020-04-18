<?php 

declare(strict_types=1);

use Arifrh\DynaModel\DB;
use Arifrh\DynaModelTests\DynaModelTestCase as TestCase;

class DBTest extends TestCase
{
    public function testDbtable()
    {
		$authors = DB::table('authors');
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);
	}
	
	public function testDbNonExistingTable()
    {
        $this->expectException(Arifrh\DynaModel\Exceptions\DBException::class);
        $categories = DB::table('categories');
    }
}
