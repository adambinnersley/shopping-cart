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
}
