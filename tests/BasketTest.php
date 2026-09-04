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
     * @covers \ShoppingCart\Basket::getDeliveryCost
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
     * @covers \ShoppingCart\Basket::createOrderID
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Basket::getDeliveryCost
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
     * @covers \ShoppingCart\Basket::createOrderID
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Basket::getDeliveryCost
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
     * @covers \ShoppingCart\Basket::getDeliveryCost
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
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddVoucher()
    {
        $this->assertTrue($this->basket->addItemToBasket(1));
        $this->assertFalse($this->basket->addVoucherCode(''));
        $this->assertTrue($this->basket->addVoucherCode('DISC10'));
        $this->assertTrue($this->basket->addVoucherCode(''));
    }

    /**
     * A price the product cannot work out must not be billed as free.
     *
     * getProductPrice() returns false for an item it cannot price - a lesson
     * whose price band could not be resolved. Multiplied by a quantity that
     * false is 0, which is how order 205256 came to hold a cart total of 0.00
     * against two items snapshotted at 420.00 each.
     *
     * @covers \ShoppingCart\Basket::itemUnitPrice
     */
    public function testUnpriceableItemFallsBackToItsSnapshot()
    {
        $basket = new Basket(self::$db, self::$config, new Customers(self::$db, self::$config), new UnpriceableProduct(self::$db, self::$config));
        $line = ['product_id' => 1, 'quantity' => 2, 'product_info' => ['name' => 'Block Booking of 10 hours', 'price' => '420.00', 'tax_id' => 1]];

        $this->assertEquals('420.00', $this->callProtected($basket, 'itemUnitPrice', [$line]));
    }

    /**
     * With no snapshot either, it has to say so rather than answer zero.
     *
     * @covers \ShoppingCart\Basket::itemUnitPrice
     */
    public function testUnpriceableItemWithNoSnapshotReportsFailure()
    {
        $basket = new Basket(self::$db, self::$config, new Customers(self::$db, self::$config), new UnpriceableProduct(self::$db, self::$config));
        $line = ['product_id' => 1, 'quantity' => 1, 'product_info' => ['name' => 'Block Booking of 10 hours']];

        $this->assertFalse($this->callProtected($basket, 'itemUnitPrice', [$line]));
    }

    /**
     * A product that prices normally is untouched by the fallback.
     *
     * @covers \ShoppingCart\Basket::itemUnitPrice
     */
    public function testNormalItemUsesTheLivePrice()
    {
        $product = new Product(self::$db, self::$config);
        $basket = new Basket(self::$db, self::$config, new Customers(self::$db, self::$config), $product);
        $line = ['product_id' => 1, 'quantity' => 1, 'product_info' => ['price' => '999.99']];

        $this->assertEquals($product->getProductPrice(1), $this->callProtected($basket, 'itemUnitPrice', [$line]));
    }

    /**
     * A basket that could not be priced has to be reportable, so a caller can
     * refuse to charge instead of charging nothing.
     *
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Basket::hasPricingFailure
     */
    public function testPricingFailureIsFlaggedAndCleared()
    {
        $basket = new Basket(self::$db, self::$config, new Customers(self::$db, self::$config), new UnpriceableProduct(self::$db, self::$config));

        $this->setProtected($basket, 'products', [['product_id' => 1, 'quantity' => 1, 'product_info' => ['name' => 'no price anywhere']]]);
        $this->callProtected($basket, 'updateTotals');
        $this->assertTrue($basket->hasPricingFailure());

        $this->setProtected($basket, 'products', [['product_id' => 1, 'quantity' => 1, 'product_info' => ['price' => '420.00', 'tax_id' => 1]]]);
        $this->callProtected($basket, 'updateTotals');
        $this->assertFalse($basket->hasPricingFailure());
    }

    /**
     * Reach a protected method for the checks above.
     *
     * @param object $object The instance
     * @param string $method The method name
     * @param array $args The arguments
     * @return mixed
     */
    protected function callProtected($object, $method, $args = [])
    {
        $reflection = new \ReflectionMethod($object, $method);
        $reflection->setAccessible(true);
        return $reflection->invokeArgs($object, $args);
    }

    /**
     * Set a protected property for the checks above.
     *
     * @param object $object The instance
     * @param string $property The property name
     * @param mixed $value The value to set
     * @return void
     */
    protected function setProtected($object, $property, $value)
    {
        $reflection = new \ReflectionProperty($object, $property);
        $reflection->setAccessible(true);
        $reflection->setValue($object, $value);
    }
}

/**
 * A product that cannot price anything, standing in for a lesson whose price
 * band could not be resolved.
 */
class UnpriceableProduct extends Product
{
    public function getProductPrice($product_id)
    {
        return false;
    }
}
