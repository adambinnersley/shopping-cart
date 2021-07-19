<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Serial;
use ShoppingCart\Order;
use ShoppingCart\Product;

class SerialTest extends SetUp
{
    protected $serial;
    protected $order;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->serial = new Serial(self::$db, self::$config);
        $this->order = new Order(self::$db, self::$config, false, new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->serial = null;
        $this->order = null;
    }
    
    /**
     * @covers \ShoppingCart\Serial::__construct
     * @covers \ShoppingCart\Serial::addSerial
     * @covers \ShoppingCart\Serial::generateSerial
     * @covers \ShoppingCart\Serial::getSerials
     */
    public function testAddSerial()
    {
        $this->order->addItemToBasket(2);
        $this->assertTrue($this->serial->addSerial(1, 'test@email.com', 2));
        $this->assertArrayHasKey('serial', $this->serial->getSerials(1, 2)[0]);
        //$this->markTestIncomplete();
    }
}
