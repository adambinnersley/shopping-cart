<?php
namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Basket;
use ShoppingCart\Customers;
use ShoppingCart\Product;

class BasketTest extends TestCase{
    protected $db;
    protected $config;
    protected $basket;
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     */
    protected function setUp(): void {
        $this->db = new Database($GLOBALS['hostname'], $GLOBALS['username'], $GLOBALS['password'], $GLOBALS['database']);
        if(!$this->db->isConnected()){
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        if(!$this->db->selectAll('store_config')){
            $this->db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/database_mysql.sql'));
            $this->db->query(file_get_contents(dirname(__FILE__).'/sample_data/data.sql'));
        }
        $config = new Config($this->db, 'store_config');
        $this->basket = new Basket($this->db, $config, new Customers($this->db), new Product($this->db, $config));
    }
    
    protected function tearDown(): void {
        $this->db = null;
        $this->basket = null;
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::getProducts
     */
    public function testGetBasket(){
        $this->assertFalse($this->basket->getBasket());
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::emptyBasket
     */
    public function testEmptyBasket(){
        $this->assertFalse($this->basket->emptyBasket());
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemByCodeToBasket
     * @covers \ShoppingCart\Product::getProductByCode
     * @covers \ShoppingCart\Product::getProduct
     * @covers \ShoppingCart\Product::listProductCategories
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testAddItemByCodeToBasket(){
        $this->assertFalse($this->basket->addItemByCodeToBasket('non_existing_product'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testAddItemToBasket(){
        $this->assertFalse($this->basket->addItemToBasket(78));
        $this->assertFalse($this->basket->addItemToBasket('nan'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::removeItemFromBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::emptyBasket
     */
    public function testRemoveItemToBasket(){
        $this->assertFalse($this->basket->removeItemFromBasket('test'));
//        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Basket::__construct
     * @covers \ShoppingCart\Basket::addItemToBasket
     * @covers \ShoppingCart\Basket::getProducts
     * @covers \ShoppingCart\Basket::getBasket
     * @covers \ShoppingCart\Basket::createOrder
     * @covers \ShoppingCart\Basket::updateBasket
     * @covers \ShoppingCart\Basket::updateTotals
     * @covers \ShoppingCart\Product::getProductByID
     * @covers \ShoppingCart\Product::getProductWeight
     * @covers \ShoppingCart\Product::getProductPrice
     * @covers \ShoppingCart\Product::isProductDownload
     * @covers \ShoppingCart\Tax::calculateItemTax
     * @covers \ShoppingCart\Modifiers\Cost::priceUnits
     * @covers \ShoppingCart\Delivery::__construct
     * @covers \ShoppingCart\Delivery::getDeliveryCost
     * @covers \ShoppingCart\Basket::updateBasket
     */
    public function testUpdateQuantityInBasket(){
        $this->markTestIncomplete();
    }
}
