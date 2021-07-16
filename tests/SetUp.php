<?php

namespace ShoppingCart\Tests;

use PHPUnit\Framework\TestCase;
use DBAL\Database;
use Configuration\Config;

abstract class SetUp extends TestCase
{
    protected static $db;
    protected static $config;
    
    /**
     * This method is called before the first test of this test class is run.
     */
    public static function setUpBeforeClass(): void
    {
        self::$db = new Database(
            $GLOBALS['hostname'],
            $GLOBALS['username'],
            $GLOBALS['password'],
            $GLOBALS['database']
        );
        if (!self::$db->isConnected()) {
            $this->markTestSkipped(
                'No local database connection is available'
            );
        }
        self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/database/database_mysql.sql'));
        self::$db->query(file_get_contents(dirname(dirname(__FILE__)).'/vendor/adamb/blocking/files/database/database.sql'));
        self::$db->query(file_get_contents(dirname(__FILE__).'/sample_data/data.sql'));
        self::$config = new Config(self::$db, 'store_config');
    }

    /**
     * This method is called after the last test of this test class is run.
     */
    public static function tearDownAfterClass(): void
    {
        
        self::$db = null;
        self::$config = null;
    }
}
