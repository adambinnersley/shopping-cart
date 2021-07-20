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
        $this->assertFalse($this->serial->addSerial(1, 'testemailcom', 2));
        $this->assertTrue($this->serial->addSerial(1, 'test@email.com', 2));
        $this->assertArrayHasKey('serial', $this->serial->getSerials(1, 2)[0]);
    }
    
    /**
     * @covers \ShoppingCart\Serial::__construct
     * @covers \ShoppingCart\Serial::getSerials
     * @covers \ShoppingCart\Serial::checkUserSerial
     */
    public function testCheckUserSerial()
    {
        $serial = $this->serial->getSerials(1, 2)[0];
        $this->assertFalse($this->serial->checkUserSerial('notanemail', $serial));
        $this->assertFalse($this->serial->checkUserSerial('test@email.com', $serial, 9));
        $this->assertTrue($this->serial->checkUserSerial('test@email.com', $serial, 2));
    }
    
    public function testNumInstalls()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Serial::__construct
     * @covers \ShoppingCart\Serial::disableSerial
     */
    public function testDisableSerial()
    {
        $this->assertFalse($this->serial->disableSerial('234523-234234-2342323'));
        $this->assertTrue($this->serial->disableSerial($this->serial->getSerials(1, 2)[0]));
    }
}
