<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Basket;
use ShoppingCart\Customers;
use ShoppingCart\Product;

class BasketTest extends SetUp
{
    protected $basket;
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->basket = new Basket($this->db, $this->config, new Customers($this->db, $this->config), new Product($this->db, $this->config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->basket = null;
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::getProducts
     */
    public function testGetBasket()
    {
        $this->assertFalse($this->basket->getBasket());
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::emptyBasket
     */
    public function testEmptyBasket()
    {
        $this->assertFalse($this->basket->emptyBasket());
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemByCodeToBasket
     * @covers \ShoppingCart\Product::getProductByCode
     * @covers \ShoppingCart\Product::getProduct
     * @covers \ShoppingCart\Product::listProductCategories
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testAddItemByCodeToBasket()
    {
        $this->assertFalse($this->basket->addItemByCodeToBasket('non_existing_product'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testAddItemToBasket()
    {
        $this->assertFalse($this->basket->addItemToBasket(78));
        $this->assertFalse($this->basket->addItemToBasket('nan'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::removeItemFromBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::emptyBasket
     */
    public function testRemoveItemToBasket()
    {
        $this->assertFalse($this->basket->removeItemFromBasket('test'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testUpdateQuantityInBasket()
    {
        $this->markTestIncomplete();
    }
}
