<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Order;
use ShoppingCart\Product;

class OrderTest extends SetUp
{
    protected $order;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->order = new Order(self::$db, self::$config, false, new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
