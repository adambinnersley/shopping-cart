<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Download;
use ShoppingCart\Basket;
use ShoppingCart\Product;

class DownloadTest extends SetUp
{
    protected $download;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->download = new Download(self::$db, self::$config, new Basket(self::$db, self::$config), new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->download = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
