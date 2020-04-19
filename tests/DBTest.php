<?php namespace Arifrh\DynaModelTests;

use Arifrh\DynaModel\DB;
use Arifrh\DynaModelTests\DynaModelTestCase as TestCase;

class DBTest extends TestCase
{
    /**
     * @return void
     */
    public function testDbtable()
    {
		$authors = DB::table('authors');
		$this->assertInstanceOf(\CodeIgniter\Model::class, $authors);
	}
    
    /**
     * @return void
     */
	public function testDbNonExistingTable()
    {
        $this->expectException(\Arifrh\DynaModel\Exceptions\DBException::class);
        $categories = DB::table('categories');
    }
}
