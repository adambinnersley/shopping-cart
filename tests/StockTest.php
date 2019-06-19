<?php
namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Stock;

class StockTest extends TestCase{
    protected $db;
    protected $stock;
    
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
        $this->stock = new Stock($this->db, new Config($this->db, 'store_config'));
    }
    
    protected function tearDown(): void {
        $this->db = null;
        $this->stock = null;
    }
    
    public function testExample(){
        $this->markTestIncomplete();
    }
}
