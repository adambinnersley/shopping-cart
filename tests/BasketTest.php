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
        $this->basket = new Basket(self::$db, self::$config, new Customers(self::$db, self::$config), new Product(self::$db, self::$config));
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
     * @covers \ShoppingCart\Basket::createOrderID
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
        $this->assertTrue($this->basket->addItemToBasket(1));
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
     * @covers \ShoppingCart\Basket::updateQuantityInBasket
     * @covers \ShoppingCart\Basket::removeItemFromBasket
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
        $this->assertEquals(1, $this->basket->getBasket()['products'][0]['quantity']);
        $this->assertTrue($this->basket->addItemToBasket(1, 3));
        $this->assertEquals(3, $this->basket->getBasket()['products'][0]['quantity']);
        $this->assertTrue($this->basket->updateQuantityInBasket(1, 2));
        $this->assertEquals(2, $this->basket->getBasket()['products'][0]['quantity']);
        $this->assertTrue($this->basket->updateQuantityInBasket(1, 0));
        $this->assertFalse($this->basket->getBasket());
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
        $this->assertTrue($this->basket->addItemToBasket(1, 3));
        $this->assertTrue($this->basket->addItemToBasket(2));
        $this->assertCount(2, $this->basket->getBasket()['products']);
        $this->assertTrue($this->basket->removeItemFromBasket(1));
        $this->assertCount(1, $this->basket->getBasket()['products']);
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addVoucherCode
     * @covers \ShoppingCart\Basket::updateVoucherCode
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     */
    public function testAddVoucher()
    {
        $this->assertTrue($this->basket->addItemToBasket(1));
        $this->assertFalse($this->basket->addVoucherCode(''));
        $this->assertTrue($this->basket->addVoucherCode('DISC10'));
        $this->assertTrue($this->basket->addVoucherCode(''));
    }
}
