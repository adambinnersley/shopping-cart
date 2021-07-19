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
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::setImageLocation
     */
    public function testSetImageLocation()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::setThumbLocation
     */
    public function testSetThumbLocation()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::setMaxThumbWidth
     */
    public function testSetMaxThumbWidth()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::listImages
     */
    public function testListImages()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::getProductImages
     * @covers \ShoppingCart\Gallery::numProductImages
     */
    public function testGetProductImages()
    {
        $this->assertEquals(0, $this->gallery->numProductImages(145));
        $this->assertFalse($this->gallery->getProductImages(145));
        $this->assertEquals(3, $this->gallery->numProductImages(1));
        $this->assertArrayHasKey('image', $this->gallery->getProductImages(1)[0]);
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::getImageInfo
     */
    public function testGetImageInfo()
    {
        $imageInfo = $this->gallery->getImageInfo(2);
        $this->assertArrayHasKey('caption', $imageInfo);
        $this->assertEquals('Another image', $imageInfo['caption']);
        $this->assertFalse($this->gallery->getImageInfo(90));
        $this->assertArrayHasKey('caption', $this->gallery->getImageInfo(2, ['active' => 1]));
        $this->assertFalse($this->gallery->getImageInfo(2, ['active' => 0]));
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::getImageInfoByName
     */
    public function testGetImageInfoByName()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::insertGalleryImage
     */
    public function testInsertGalleryImage()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Gallery::__construct
     * @covers \ShoppingCart\Gallery::assignProductToImage
     */
    public function testAssignProductToImage()
    {
        $this->markTestIncomplete();
    }
}
