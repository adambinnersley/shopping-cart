<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Delivery;

class DeliveryTest extends SetUp
{
    protected $delivery;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->delivery = new Delivery($this->db, $this->config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->delivery = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
