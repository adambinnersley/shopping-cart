<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Gallery;

class GalleryTest extends SetUp
{
    protected $gallery;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->gallery = new Gallery(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->gallery = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
