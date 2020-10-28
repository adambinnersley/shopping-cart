<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Review;
use ShoppingCart\Product;

class ReviewTest extends SetUp
{
    protected $review;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->review = new Review($this->db, $this->config, new Product($this->db, $this->config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->review = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
