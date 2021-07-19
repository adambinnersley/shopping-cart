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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testGetVoucherByCode()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddVoucher()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::editVoucher
     */
    public function testEditVoucher()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::deleteVoucher
     */
    public function testDeleteVoucher()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::changeVoucherStatus
     * @covers \ShoppingCart\Voucher::editVoucher
     */
    public function testChangeVoucherStatus()
    {
        $this->markTestIncomplete();
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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addSelectedProductToVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByID
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddSelectedProductToVoucher()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::removeSelectedProductFromVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByID
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testRemoveSelectedProductFromVoucher()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Voucher::__construct
     * @covers \ShoppingCart\Voucher::addUsageToVoucher
     * @covers \ShoppingCart\Voucher::getVoucherByCode
     * @covers \ShoppingCart\Voucher::getVoucher
     */
    public function testAddUsageToVoucher()
    {
        $this->markTestIncomplete();
    }
}
