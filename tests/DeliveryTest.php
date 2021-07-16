<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Delivery;

class DeliveryTest extends SetUp
{
    protected $delivery;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->delivery = new Delivery(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->delivery = null;
    }
    
    /**
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery\Free::__construct
     * @covers \ShoppingCart\Delivery\Free::getDeliveryCost
     */
    public function testFreeDelivery()
    {
        self::$config->delivery_type = 'Free';
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(9234423));
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(1, 150));
        self::$config->delivery_type = '';
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(1, 150));
    }
}
