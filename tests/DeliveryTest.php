<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Delivery;
use ShoppingCart\Delivery\Free;
use ShoppingCart\Delivery\Fixed;
use ShoppingCart\Delivery\Method;
use ShoppingCart\Delivery\Value;
use ShoppingCart\Delivery\Weight;


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
     * @covers \ShoppingCart\Delivery\Free::listDeliveryItems
     * @covers \ShoppingCart\Delivery\Free::getDeliveryItem
     * @covers \ShoppingCart\Delivery\Free::addDeliveryItem
     * @covers \ShoppingCart\Delivery\Free::editDeliveryItem
     * @covers \ShoppingCart\Delivery\Free::deleteDeliveryItem
     */
    public function testFreeDelivery()
    {
        self::$config->delivery_type = 'Free';
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(9234423));
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(1, 150));
        $free = new Free(self::$db, self::$config);
        $this->assertFalse($free->listDeliveryItems());
        $this->assertFalse($free->getDeliveryItem());
        $this->assertFalse($free->getDeliveryItem(6));
        $this->assertFalse($free->addDeliveryItem(['description' => 'Hello', 'price' => '8.59']));
        $this->assertFalse($free->editDeliveryItem(1, ['description' => 'Test Change', 'price' => '8.59']));
        $this->assertFalse($free->deleteDeliveryItem(1));
        self::$config->delivery_type = '';
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(1, 150));
    }
    
    /**
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery\Fixed::__construct
     * @covers \ShoppingCart\Delivery\Fixed::getDeliveryCost
     * @covers \ShoppingCart\Delivery\Fixed::listDeliveryItems
     * @covers \ShoppingCart\Delivery\Fixed::getDeliveryItem
     * @covers \ShoppingCart\Delivery\Fixed::addDeliveryItem
     * @covers \ShoppingCart\Delivery\Fixed::editDeliveryItem
     * @covers \ShoppingCart\Delivery\Fixed::deleteDeliveryItem
     */
    public function testFixedDelivery()
    {
        self::$config->delivery_type = 'Fixed';
        $this->assertEquals('4.99', $this->delivery->getDeliveryCost([]));
        $fixed = new Fixed(self::$db, self::$config);
        $this->assertCount(2, $fixed->listDeliveryItems());
        $this->assertEquals('4.99', $fixed->getDeliveryItem()['cost']);
        $this->assertEquals('4.99', $fixed->getDeliveryItem(56)['cost']);
        $this->assertFalse($fixed->addDeliveryItem('4.99'));
        $this->assertTrue($fixed->addDeliveryItem('3.99'));
        $this->assertEquals('3.99', $fixed->getDeliveryItem()['cost']);
        $this->assertFalse($fixed->deleteDeliveryItem('nan'));
        $this->assertFalse($fixed->deleteDeliveryItem(2));
        $this->assertTrue($fixed->deleteDeliveryItem(1));
        $this->assertTrue($fixed->addDeliveryItem('2.99'));
        $this->assertEquals('2.99', $fixed->getDeliveryItem()['cost']);
    }
    
    /**
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery\Method::__construct
     * @covers \ShoppingCart\Delivery\Method::getDeliveryCost
     * @covers \ShoppingCart\Delivery\Method::listDeliveryItems
     * @covers \ShoppingCart\Delivery\Method::getDeliveryItem
     * @covers \ShoppingCart\Delivery\Method::addDeliveryItem
     * @covers \ShoppingCart\Delivery\Method::editDeliveryItem
     * @covers \ShoppingCart\Delivery\Method::deleteDeliveryItem
     */
    public function testMethodDelivery()
    {
        self::$config->delivery_type = 'Method';
        $this->assertEquals('3.99', $this->delivery->getDeliveryCost(['delivery_method' => 1]));
        $this->assertEquals('1.29', $this->delivery->getDeliveryCost(['delivery_method' => 2]));
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(18));
        $method = new Method(self::$db, self::$config);
        $items = $method->listDeliveryItems();
        $this->assertArrayHasKey('description', $items[0]);
        $this->assertCount(3, $items);
        $this->assertEquals('1st Class', $method->getDeliveryItem()['description']);
        $this->assertEquals('2nd Class', $method->getDeliveryItem(2)['description']);
        $this->assertFalse($method->addDeliveryItem(['exist' => 'jssdfisdf', 'price' => '3.45']));
        $this->assertTrue($method->addDeliveryItem(['description' => 'Air Mail', 'price' => '17.69']));
        $this->assertCount(4, $method->listDeliveryItems());
        $this->assertFalse($method->editDeliveryItem(6, []));
        $this->assertFalse($method->editDeliveryItem(6, ['price' => '5.67']));
        $this->assertFalse($method->editDeliveryItem('nan', ['price' => '5.67']));
        $this->assertTrue($method->editDeliveryItem(4, ['price' => '17.99']));
        $this->assertFalse($method->deleteDeliveryItem(7));
        $this->assertTrue($method->deleteDeliveryItem(4));
    }
    
    /**
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery\Value::__construct
     * @covers \ShoppingCart\Delivery\Value::getDeliveryCost
     * @covers \ShoppingCart\Delivery\Value::listDeliveryItems
     * @covers \ShoppingCart\Delivery\Value::getDeliveryItem
     * @covers \ShoppingCart\Delivery\Value::addDeliveryItem
     * @covers \ShoppingCart\Delivery\Value::editDeliveryItem
     * @covers \ShoppingCart\Delivery\Value::deleteDeliveryItem
     */
    public function testValueDeleivery()
    {
        self::$config->delivery_type = 'Value';
        $this->assertEquals('3.99', $this->delivery->getDeliveryCost(['cart_total' => '34.78']));
        $this->assertEquals('0.00', $this->delivery->getDeliveryCost(['cart_total' => '92.30']));
        $value = new Value(self::$db, self::$config);
        $this->assertCount(2, $value->listDeliveryItems());
        //$this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery\Weight::__construct
     * @covers \ShoppingCart\Delivery\Weight::getDeliveryCost
     * @covers \ShoppingCart\Delivery\Weight::listDeliveryItems
     * @covers \ShoppingCart\Delivery\Weight::getDeliveryItem
     * @covers \ShoppingCart\Delivery\Weight::addDeliveryItem
     * @covers \ShoppingCart\Delivery\Weight::editDeliveryItem
     * @covers \ShoppingCart\Delivery\Weight::deleteDeliveryItem
     */
    public function testWeightDelivery()
    {
        self::$config->delivery_type = 'Weight';
        $this->assertEquals('2.00', $this->delivery->getDeliveryCost([], '1.67'));
        $this->assertEquals('3.50', $this->delivery->getDeliveryCost([], '3.26'));
        $weight = new Weight(self::$db, self::$config);
        $this->assertArrayHasKey('max_weight', $weight->listDeliveryItems()[0]);
        //$this->markTestIncomplete();
    }
}
