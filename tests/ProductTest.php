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
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::listProducts
     * @covers ShoppingCart\Category::addWhereIsActive
     */
    public function testListProduct()
    {
        $this->assertCount(2, $this->product->listProducts());
        $this->assertCount(2, $this->product->listProducts(false)); // all products
        $this->assertArrayHasKey('name', $this->product->listProducts()[0]);
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::countProducts
     * @covers ShoppingCart\Category::addWhereIsActive
     */
    public function testCountProducts()
    {
        $this->assertEquals(2, $this->product->countProducts());
        $this->assertEquals(2, $this->product->countProducts(false)); // all products
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::addProduct
     * @covers ShoppingCart\Product::getProductByCode
     * @covers ShoppingCart\Product::getProduct
     * @covers ShoppingCart\Product::addProductToCategory
     */
    public function testAddProduct()
    {
        $this->assertFalse($this->product->addProduct('New Product', 'SAMPLE', '<p>A brand new product</p>', '6.29', [1, 2], 1, 1, false, ['weight' => 1, 'homepage' => 1]));
        $this->assertTrue($this->product->addProduct('New Product', 'NEWPROD1', '<p>A brand new product</p>', '6.29', [1, 2], 1, 1, false, ['weight' => 1, 'homepage' => 1]));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::editProduct
     * @covers ShoppingCart\Product::updateProductCategory
     */
    public function testEditProduct()
    {
        $this->assertFalse($this->product->editProduct(3, false, ['active' => 0]));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::listProductCategories
     */
    public function testListProductCategories()
    {
        $this->assertFalse($this->product->listProductCategories(99));
        $this->assertCount(2, $this->product->listProductCategories(3));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::deleteProduct
     */
    public function testDeleteProduct()
    {
        $this->assertFalse($this->product->deleteProduct(99));
        $this->assertTrue($this->product->deleteProduct(3));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::getProductsInCategory
     * @covers ShoppingCart\Product::buildProductArray
     * @covers ShoppingCart\Product::buildProduct
     * @covers ShoppingCart\Product::getProductByURL
     * @covers ShoppingCart\Product::getProduct
     */
    public function testGetProductsInCategory()
    {
        $this->assertFalse($this->product->getProductsInCategory(99));
        $this->assertCount(1, $this->product->getProductsInCategory(1));
    }
}
