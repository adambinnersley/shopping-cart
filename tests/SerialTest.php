<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Serial;

class SerialTest extends SetUp
{
    protected $serial;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->serial = new Serial(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->serial = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
