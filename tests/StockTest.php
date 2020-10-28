<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Stock;

class StockTest extends SetUp
{
    protected $stock;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->stock = new Stock($this->db, $this->config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->stock = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
