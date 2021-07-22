<?php

namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Invoice;
use ShoppingCart\Order;

class InvoiceTest extends SetUp
{
    protected $invoice;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->invoice = new Invoice(self::$db, self::$config, new Order(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->invoice = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
