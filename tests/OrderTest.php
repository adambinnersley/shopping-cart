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
        $this->order = new Order($this->db, $this->config, false, new Product($this->db, $this->config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->order = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
