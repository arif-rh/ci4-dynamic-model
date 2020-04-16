<?php 

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

/**
*  Unit Test for StarterTest Class
*
*  @author Arif RH
*/
final class DynaModelTest extends TestCase
{
  /**
   * set common properties that will be used in all unit test case
   */
	  protected $mock = null;

    /**
     * Setup inital action that will be used in all unit test case
     */
    public function setUp(): void
    {
        $this->mock = [];  // TO DO
    }

    public function tearDown(): void
    {
        $this->mock = null;
    }
}
