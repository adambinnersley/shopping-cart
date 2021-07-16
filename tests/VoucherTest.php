<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Voucher;
use ShoppingCart\Product;

class VoucherTest extends SetUp
{
    protected $voucher;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->voucher = new Voucher(self::$db, self::$config, new Product(self::$db, self::$config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->voucher = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
