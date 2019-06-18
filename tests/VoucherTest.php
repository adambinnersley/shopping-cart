<?php
namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Voucher;
use ShoppingCart\Product;

class VoucherTest extends TestCase{
    protected $db;
    protected $voucher;
    
    protected function setUp() {
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
        $this->voucher = new Voucher($this->db, $config, new Product($this->db, $config));
    }
    
    protected function tearDown() {
        $this->db = null;
        $this->voucher = null;
    }
    
    public function testExample(){
        $this->markTestIncomplete();
    }
}
