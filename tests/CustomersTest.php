<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Customers;

class CustomersTest extends SetUp
{
    protected $customers;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->customers = new Customers($this->db, $this->config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->customers = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
