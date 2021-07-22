<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Product;

class ProductTest extends SetUp
{
    protected $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->product = new Product(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->product = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
