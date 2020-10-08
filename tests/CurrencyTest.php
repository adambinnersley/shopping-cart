<?php
namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use ShoppingCart\Currency;

class CurrencyTest extends TestCase
{
    
    /**
     * @covers \ShoppingCart\Currency::retrieveCurrencies
     * @covers \ShoppingCart\Currency::listCurrencyNames
     */
    public function testListCurrencyNames()
    {
        $this->assertContains('US Dollar', Currency::listCurrencyNames());
        $this->assertContains('Euro', Currency::listCurrencyNames());
        $this->assertGreaterThan(40, count(Currency::listCurrencyNames()));
        $this->assertNotContains('GBP', Currency::listCurrencyNames());
    }
    
    /**
     * @covers \ShoppingCart\Currency::retrieveCurrencies
     * @covers \ShoppingCart\Currency::listCurrencyCodes
     */
    public function testListCurrencyCodes()
    {
        $this->assertContains('GBP', Currency::listCurrencyCodes());
        $this->assertContains('USD', Currency::listCurrencyCodes());
        $this->assertGreaterThan(40, count(Currency::listCurrencyCodes()));
        $this->assertNotContains('US Dollar', Currency::listCurrencyCodes());
    }
    
    /**
     * @covers \ShoppingCart\Currency::retrieveCurrencies
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     */
    public function testGetCurrencyDecimals()
    {
        $this->assertEquals(2, Currency::getCurrencyDecimals('USD'));
        $this->assertEquals(0, Currency::getCurrencyDecimals('ALL'));
        $this->assertEquals(2, Currency::getCurrencyDecimals('HYU')); // Dafults to 2 if doesn't exist
    }
    
    /**
     * @covers \ShoppingCart\Currency::retrieveCurrencies
     * @covers \ShoppingCart\Currency::getCurrencyName
     */
    public function testGetCurrencyName()
    {
        $this->assertEquals('US Dollar', Currency::getCurrencyName('USD'));
        $this->assertEquals('British Pound Sterling', Currency::getCurrencyName('GBP'));
        $this->assertFalse(Currency::getCurrencyName('HYU'));
    }
    
    /**
     * @covers \ShoppingCart\Currency::retrieveCurrencies
     * @covers \ShoppingCart\Currency::getCurrencySymbol
     */
    public function testGetCurrencySymbol()
    {
        $this->assertEquals('£', Currency::getCurrencySymbol('GBP'));
        $this->assertEquals('$', Currency::getCurrencySymbol('USD'));
        $this->assertFalse(Currency::getCurrencySymbol('HYU'));
    }
}
