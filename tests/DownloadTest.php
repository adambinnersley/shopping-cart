<?php

namespace ShoppingCart\Tests;

use ShoppingCart\Download;
use ShoppingCart\Basket;
use ShoppingCart\Product;

class DownloadTest extends SetUp
{
    protected $download;
    protected $basket;

    protected function setUp(): void
    {
        parent::setUp();
        $this->basket = new Basket(self::$db, self::$config);
        $this->download = new Download(self::$db, self::$config, $this->basket, new Product(self::$db, self::$config));
        $this->basket->addItemToBasket(1);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->download = null;
    }
    
    /**
     * @covers ShoppingCart\Download::__construct
     * @covers ShoppingCart\Download::addDownloadLink
     * @covers ShoppingCart\Download::createUniqueLink
     * @covers ShoppingCart\Download::addDownloadSerials
     * @covers ShoppingCart\Product::__construct
     * @covers ShoppingCart\Product::isProductDownload
     */
    public function testAddDownloadLink()
    {
        $orderID = $this->basket->getBasket()['order_id'];
        $this->assertFalse($this->download->addDownloadLink(1, $orderID, 1, 'myemail@example.com'));
        $this->assertFalse($this->download->addDownloadLink(1, $orderID, [1 => 1], 'myemail@example.com'));
        $this->assertTrue($this->download->addDownloadLink(1, $orderID, [2 => 1], 'myemail@example.com'));
    }
}
