<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Order;
use ShoppingCart\Product;

class OrderTest extends SetUp
{
    protected $order;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->order = new Order(self::$db, self::$config, false, new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::fetchOrders
     * @covers \ShoppingCart\Modifiers\SQLBuilder::createAdditionalString
     * @covers \ShoppingCart\Modifiers\SQLBuilder::formatValues
     */
    public function testFetchOrders()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::listOrders
     * @covers \ShoppingCart\Order::listOrdersCount
     * @covers \ShoppingCart\Download::__construct
     */
    public function testListOrders()
    {
        $this->markTestIncomplete();
    }
    
     /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::searchOrders
     * @covers \ShoppingCart\Order::searchOrdersCount
     * @covers \ShoppingCart\Modifiers\SQLBuilder::createAdditionalString
     * @covers \ShoppingCart\Modifiers\SQLBuilder::formatValues
     * @covers \ShoppingCart\Download::__construct
     */
    public function testSearchOrders()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::getOrderByID
     * @covers \ShoppingCart\Order::getOrderBy
     * @covers \ShoppingCart\Order::buildOrder
     * @covers \ShoppingCart\Order::orderStatus
     * @covers \ShoppingCart\Order::getDeliveryInfo
     * @covers \ShoppingCart\Order::getOrderProducts
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Download::__construct
     */
    public function testGetOrderByID()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::getOrderByOrderNo
     * @covers \ShoppingCart\Order::getOrderBy
     * @covers \ShoppingCart\Order::buildOrder
     * @covers \ShoppingCart\Order::orderStatus
     * @covers \ShoppingCart\Order::getDeliveryInfo
     * @covers \ShoppingCart\Order::getOrderProducts
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Download::__construct
     */
    public function testGetOrderByOrderNo()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::getOrdersByUser
     * @covers \ShoppingCart\Order::getUserOrder
     * @covers \ShoppingCart\Order::getOrderBy
     * @covers \ShoppingCart\Order::buildOrder
     * @covers \ShoppingCart\Order::orderStatus
     * @covers \ShoppingCart\Order::getDeliveryInfo
     * @covers \ShoppingCart\Order::getOrderProducts
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Download::__construct
     */
    public function testGetOrdersByUser()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::getOrderInformation
     * @covers \ShoppingCart\Order::getOrderByID
     * @covers \ShoppingCart\Order::getOrderBy
     * @covers \ShoppingCart\Order::buildOrder
     * @covers \ShoppingCart\Order::orderStatus
     * @covers \ShoppingCart\Order::getDeliveryInfo
     * @covers \ShoppingCart\Order::getOrderProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Download::__construct
     */
    public function testGetOrderInfo()
    {
        $this->markTestIncomplete();
    }
    
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::updateOrderInformation
     */
    public function testEditOrderInfo()
    {
        $this->markTestIncomplete();
    }
    
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::deleteOrderInformation
     */
    public function testDeleteOrderInfo()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::clearIncompleteOrders
     * @covers \ShoppingCart\Order::completeOldPaidOrders
     * @covers \ShoppingCart\Download::__construct
     */
    public function testClearOrders()
    {
        $this->assertEmpty($this->order->clearIncompleteOrders());
        $this->assertEmpty($this->order->completeOldPaidOrders());
    }
}
