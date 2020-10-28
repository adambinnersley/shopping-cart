<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Gallery;

class GalleryTest extends SetUp
{
    protected $gallery;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->gallery = new Gallery($this->db, $this->config);
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
