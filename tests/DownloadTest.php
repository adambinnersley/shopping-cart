<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Download;
use ShoppingCart\Basket;
use ShoppingCart\Product;

class DownloadTest extends SetUp
{
    protected $download;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->download = new Download($this->db, $this->config, new Basket($this->db, $this->config), new Product($this->db, $this->config));
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->download = null;
    }
    
    public function testExample()
    {
        $this->markTestIncomplete();
    }
}
