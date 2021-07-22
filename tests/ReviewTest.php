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
        $this->review = new Review(self::$db, self::$config, new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->review = null;
    }
    
    /**
     * @covers ShoppingCart\Review::__construct
     * @covers ShoppingCart\Review::addProductReview
     * @covers ShoppingCart\Review::checkForReviewsByIP
     * @covers ShoppingCart\Review::checkIfCustomerReviewExists
     * @covers ShoppingCart\Product::getProductByID
     * @covers ShoppingCart\Product::getProduct
     * @covers ShoppingCart\Review::sendReviewEmail
     */
    public function testAddReview()
    {
        $this->assertFalse($this->review->addProductReview(100, 'No Product User', 'my-email@test.com', 'Great Product', 'This is some text about a great product', 5));
        $this->assertFalse($this->review->addProductReview(1, 'Test User', 'incorrectemail@test', 'Great Product', 'This is some text about a great product', 5));
        $this->assertTrue($this->review->addProductReview(1, 'Test User', 'my-email@test.com', 'Great Product', 'This is some text about a great product', 5));
        $this->assertFalse($this->review->addProductReview(1, 'Test User', 'my-email@test.com', 'Great Product', 'This is some text about a great product', 5));
    }
    
    /**
     * @covers ShoppingCart\Review::__construct
     * @covers ShoppingCart\Review::getReviewInfo
     */
    public function testGetReviewInfo()
    {
        $this->assertFalse($this->review->getReviewInfo(10));
        $this->assertArrayHasKey('review', $this->review->getReviewInfo(1));
    }
    
    /**
     * @covers ShoppingCart\Review::__construct
     * @covers ShoppingCart\Review::getReviews
     * @covers ShoppingCart\Review::countReviews
     * @covers ShoppingCart\Modifiers\SQLBuilder::createAdditionalString
     * @covers ShoppingCart\Modifiers\SQLBuilder::formatValues
     */
    public function testGetReviews()
    {
        $this->assertEquals(1, $this->review->countReviews());
        $this->assertEquals(1, $this->review->countReviews(['product' => 1]));
        $this->assertEquals(0, $this->review->countReviews(['product' => 2]));
        $this->assertArrayHasKey('review', $this->review->getReviews()[0]);
        $this->assertCount(1, $this->review->getReviews(false, 0, ['product' => 1]));
    }
}
