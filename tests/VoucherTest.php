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
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::listVouchers
     */
    public function testListVouchers()
    {
        $list = $this->voucher->listVouchers();
        $this->assertArrayHasKey('expire', $list[0]);
        $this->assertCount(2, $list);
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::getVoucherByID
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testGetVoucherByID()
    {
        $this->assertFalse($this->voucher->getVoucherByID(99));
        $this->assertArrayHasKey('code', $this->voucher->getVoucherByID(2));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testGetVoucherByCode()
    {
        $this->assertFalse($this->voucher->getVoucherByCode('NOTEXIST'));
        $this->assertArrayHasKey('expire', $this->voucher->getVoucherByCode('DISC10'));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddVoucher()
    {
        $this->assertFalse($this->voucher->addVoucher('FIXED1', ['amount' => '5.99'], '2030-12-31 23:59:59')); // Duplicate name
        $this->assertFalse($this->voucher->addVoucher('NEWVOUCHER', '5.99', '2030-12-31 23:59:59'));
        $this->assertTrue($this->voucher->addVoucher('NEWVOUCHER', ['percent' => '5'], '2030-12-31 23:59:59'));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::editVoucher
     */
    public function testEditVoucher()
    {
        $this->assertFalse($this->voucher->editVoucher(1, 'hello'));
        $this->assertFalse($this->voucher->editVoucher(99, ['percent' => '7.5', 'code' => 'DISC75']));
        $this->assertTrue($this->voucher->editVoucher(1, ['percent' => '7.5', 'code' => 'DISC75']));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::deleteVoucher
     */
    public function testDeleteVoucher()
    {
        $this->assertFalse($this->voucher->deleteVoucher(99));
        $this->assertTrue($this->voucher->deleteVoucher(3));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::changeVoucherStatus
     * @covers \ShoppingCart\Voucher::editVoucher
     */
    public function testChangeVoucherStatus()
    {
        $this->assertFalse($this->voucher->changeVoucherStatus(99));
        $this->assertFalse($this->voucher->changeVoucherStatus(1, 1));
        $this->assertTrue($this->voucher->changeVoucherStatus(1, 0));
        $this->assertTrue($this->voucher->changeVoucherStatus(1));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addSelectedProductToVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByID
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddSelectedProductToVoucher()
    {
        $this->assertFalse($this->voucher->addSelectedProductToVoucher(99, 1));
        $this->assertFalse($this->voucher->addSelectedProductToVoucher(1, 99));
        $this->assertFalse($this->voucher->addSelectedProductToVoucher(1, null));
        $this->assertTrue($this->voucher->addSelectedProductToVoucher(1, 1));
        $this->assertTrue($this->voucher->addSelectedProductToVoucher(1, [2]));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::getDiscountAmount
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     * @covers \ShoppingCart\Voucher::calculateProductDiscount
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     */
    public function testGetDiscountAmount()
    {
        $this->assertEquals('0.00', $this->voucher->getDiscountAmount('Hello', [1 => 1], '9.99'));
        $this->assertEquals('1.00', $this->voucher->getDiscountAmount('FIXED1', [1 => 1], '9.99'));
        $this->assertEquals('0.75', $this->voucher->getDiscountAmount('DISC75', [1 => 1], '9.99'));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::removeSelectedProductFromVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByID
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testRemoveSelectedProductFromVoucher()
    {
        $this->assertFalse($this->voucher->removeSelectedProductFromVoucher(2, 1));
        $this->assertTrue($this->voucher->removeSelectedProductFromVoucher(1, 1));
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addUsageToVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddUsageToVoucher()
    {
        $this->assertFalse($this->voucher->addUsageToVoucher('HELLO'));
        $this->assertTrue($this->voucher->addUsageToVoucher('FIXED1'));
    }
}
