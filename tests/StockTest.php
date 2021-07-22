<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Stock;

class StockTest extends SetUp
{
    protected $stock;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->stock = new Stock(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->stock = null;
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Stock::addQuantity
     * @covers ShoppingCart\Stock::setQuantityInStock
     * @covers ShoppingCart\Stock::getStockQuantity
     */
    public function testAddStock()
    {
        $this->assertFalse($this->stock->addQuantity(199, 100)); // Product shouldn't exist
        $this->assertEquals(0, $this->stock->getStockQuantity(1));
        $this->assertTrue($this->stock->addQuantity(1, 100));
        $this->assertEquals(100, $this->stock->getStockQuantity(1));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Stock::removeQuantity
     * @covers ShoppingCart\Stock::setQuantityInStock
     * @covers ShoppingCart\Stock::getStockQuantity
     */
    public function testRemoveStock()
    {
        $this->assertTrue($this->stock->removeQuantity(1));
        $this->assertEquals(99, $this->stock->getStockQuantity(1));
        $this->assertFalse($this->stock->removeQuantity(199));
        $this->assertFalse($this->stock->removeQuantity('nan'));
        $this->assertTrue($this->stock->removeQuantity(1, 2));
        $this->assertEquals(97, $this->stock->getStockQuantity(1));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Stock::isInStock
     * @covers ShoppingCart\Stock::getStockQuantity
     */
    public function testIsInStock()
    {
        $this->assertTrue($this->stock->isInStock(1));
        $this->assertFalse($this->stock->isInStock(199));
        $this->assertFalse($this->stock->isInStock(2));
    }
    
    /**
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Stock::getItemsByStockLevel
     */
    public function testGetItemsByStockLevel()
    {
        $this->assertArrayHasKey('code', $this->stock->getItemsByStockLevel()[0]);
        $this->assertArrayHasKey('code', $this->stock->getItemsByStockLevel(false)[0]);
        $this->assertFalse($this->stock->getItemsByStockLevel(false, false));
        $this->assertFalse($this->stock->getItemsByStockLevel(true, false));
    }
}
