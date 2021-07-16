<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Tax;

class TaxTest extends SetUp
{
    protected $tax;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->tax = new Tax(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tax = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
