<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Category;

class CategoryTest extends SetUp
{
    protected $category;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category($this->db, $this->config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->category = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
