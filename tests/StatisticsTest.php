<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Statistics;

class StatisticsTest extends SetUp
{
    protected $stats;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->stats = new Statistics(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->stats = null;
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getSalesByMonth
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     *
     */
    public function testGetSalesByMonth()
    {
        $statistics = $this->stats->getSalesByMonth(6, 2021);
        $this->assertArrayHasKey('days', $statistics);
        $this->assertArrayHasKey('statistics', $statistics);
        $this->assertArrayHasKey(15, $statistics['days']);
        $this->assertArrayNotHasKey(32, $statistics['days']);
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getSalesByYear
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     */
    public function testGetSalesByYear()
    {
        $statistics = $this->stats->getSalesByYear(2019);
        $this->assertArrayHasKey('months', $statistics);
        $this->assertArrayHasKey('statistics', $statistics);
        $this->assertArrayHasKey(10, $statistics['months']);
        $this->assertArrayNotHasKey(15, $statistics['months']);
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getProductStatsBySales
     * @covers \ShoppingCart\Statistics::getProductStats
     */
    public function testGetProductStatsBySales()
    {
        $statistics = $this->stats->getProductStatsBySales();
        $this->assertCount(2, $statistics);
        $this->assertArrayHasKey('percentage', $statistics[0]);
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getProductStatsByViews
     * @covers \ShoppingCart\Statistics::getProductStats
     */
    public function testGetProductStatsByViews()
    {
        $statistics = $this->stats->getProductStatsByViews();
        $this->assertCount(2, $statistics);
        $this->assertArrayHasKey('percentage', $statistics[0]);
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getProductSalesByMonth
     */
    public function testGetProductSalesByMonth()
    {
        $statistics = $this->stats->getProductSalesByMonth(1, 6, 2021);
        $this->assertArrayHasKey('days', $statistics);
        $this->assertArrayHasKey('statistics', $statistics);
        $this->assertArrayHasKey(15, $statistics['days']);
        $this->assertArrayNotHasKey(32, $statistics['days']);
    }
    
    /**
     * @covers \ShoppingCart\Statistics::__construct
     * @covers \ShoppingCart\Currency::getCurrencyDecimals
     * @covers \ShoppingCart\Statistics::getProductSalesByYear
     */
    public function testGetProductSalesByYear()
    {
        $statistics = $this->stats->getProductSalesByYear(1, 2021);
        $this->assertArrayHasKey('months', $statistics);
        $this->assertArrayHasKey('statistics', $statistics);
        $this->assertArrayHasKey(10, $statistics['months']);
        $this->assertArrayNotHasKey(15, $statistics['months']);
    }
}
