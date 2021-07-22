<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Order;
use ShoppingCart\Product;
use ShoppingCart\Customers;

class OrderTest extends SetUp
{
    protected $order;
    protected $customer;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = new Customers(self::$db, self::$config);
        $this->order = new Order(self::$db, self::$config, false, new Product(self::$db, self::$config));
        $this->customer->login('sample.name@emaple.com', 'password');
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
        $this->customer = null;
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::fetchOrders
     * @covers \ShoppingCart\Modifiers\SQLBuilder::createAdditionalString
     * @covers \ShoppingCart\Modifiers\SQLBuilder::formatValues
     */
    public function testFetchOrders()
    {
        $this->assertEmpty($this->order->fetchOrders());
        $this->order->addItemToBasket(1);
        /*$orders = $this->order->fetchOrders(1);
        $this->assertCount(1, $orders);
        $this->assertCount(1, $this->order->fetchOrders(1, 0, 0, 50, []));*/
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
     * @covers \ShoppingCart\Order::getOrdersBy
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
     * @covers \ShoppingCart\Order::getOrdersBy
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
     * @covers \ShoppingCart\Order::getOrdersBy
     * @covers \ShoppingCart\Order::buildOrder
     * @covers \ShoppingCart\Order::orderStatus
     * @covers \ShoppingCart\Order::getDeliveryInfo
     * @covers \ShoppingCart\Order::getOrderProducts
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Download::__construct
     */
    public function testGetOrdersByUser()
    {
        $this->assertFalse($this->order->getOrdersByUser(99));
        //$this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Order::__construct
     * @covers \ShoppingCart\Order::getOrderInformation
     * @covers \ShoppingCart\Order::getOrderByID
     * @covers \ShoppingCart\Order::getOrdersBy
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
