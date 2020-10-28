<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Statistics;

class StatisticsTest extends SetUp
{
    protected $stats;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->stats = new Statistics($this->db, $this->config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->stats = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
