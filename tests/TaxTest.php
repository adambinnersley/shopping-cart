<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Tax;

class TaxTest extends SetUp
{
    protected $tax;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->tax = new Tax(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->tax = null;
    }
    
    /**
     * @covers \ShoppingCart\Tax::__construct
     * @covers \ShoppingCart\Tax::listTaxAmounts
     */
    public function testListTaxAmounts()
    {
        $taxes = $this->tax->listTaxAmounts();
        $this->assertCount(2, $taxes);
        $this->assertArrayHasKey('percent', $taxes[0]);
    }
    
    /**
     * @covers \ShoppingCart\Tax::__construct
     * @covers \ShoppingCart\Tax::getTaxInformation
     */
    public function testGetTaxInfo()
    {
        $this->assertFalse($this->tax->getTaxInformation('NAN'));
        $this->assertFalse($this->tax->getTaxInformation(99));
        $taxInfo = $this->tax->getTaxInformation(1);
        $this->assertArrayHasKey('percent', $taxInfo);
        $this->assertEquals('UK Standard VAT', $taxInfo['details']);
    }
    
    /**
     * @covers \ShoppingCart\Tax::__construct
     * @covers \ShoppingCart\Tax::addTax
     */
    public function testAddTax()
    {
        $this->assertFalse($this->tax->addTax('NAN', 'Some Description'));
        $this->assertTrue($this->tax->addTax('17.5', 'Some Description for 17.5'));
    }
    
    /**
     * @covers \ShoppingCart\Tax::__construct
     * @covers \ShoppingCart\Tax::editTax
     */
    public function testEditTax()
    {
        $this->assertFalse($this->tax->editTax('NAN'));
        $this->assertFalse($this->tax->editTax(3, []));
        $this->assertTrue($this->tax->editTax(3, ['percent' => 15]));
    }
    
    /**
     * @covers \ShoppingCart\Tax::__construct
     * @covers \ShoppingCart\Tax::deleteTax
     */
    public function testDeleteTax()
    {
        $this->assertFalse($this->tax->deleteTax('NAN'));
        $this->assertFalse($this->tax->deleteTax(99));
        $this->assertTrue($this->tax->deleteTax(3));
    }
}
