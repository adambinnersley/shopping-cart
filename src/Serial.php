<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;

class Serial
{
    /**
     * This is an instance of the database class
     * @var object Database class object
     */
    protected $db;
    
    /**
     * This is an instance of the shopping cart config class
     * @var object Config class object
     */
    protected $config;

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the config class
     */
    public function __construct(Database $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * Generate a new serial number
     * @return string Returns the generated string
     */
    protected function generateSerial()
    {
        return str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT) . '-' . str_pad(rand(0, 9999), 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Add a new serial number to the database in conjunction with the email address
     * @param string $order_id This should be the order number which is associated with the purchase of this serial
     * @param string $email This should be the email address that should be used with the serial
     * @return boolean If the serial has been added will return true else returns false
     */
    public function addSerial($order_id, $email, $product_id)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL) && is_numeric($product_id) && is_numeric($order_id)) {
            return $this->db->insert($this->config->table_serials, ['serial' => $this->generateSerial(), 'email' => strtolower($email), 'product_id' => intval($product_id), 'order_id' => intval($order_id)]);
        }
        return false;
    }
    
    /**
     * Retrieves the serial numbers for a given order and product
     * @param string $order_id This should be the order number
     * @param int $product_id This should be the id of the product you are checking for serial numbers for
     * @return array|false If any serial numbers exist for the product in the order given will return an array else will return false
     */
    public function getSerials($order_id, $product_id)
    {
        if (is_numeric($product_id) && is_numeric($order_id)) {
            return $this->db->selectAll($this->config->table_serials, ['active' => 1, 'order_id' => $order_id, 'product_id' => $product_id], ['serial'], [], 0, 300);
        }
        return false;
    }
    
    /**
     * Checks to see if the serial number is valid
     * @param string $email The email address that should be used in conjunction with the serial
     * @param string $serial The serial number being used
     * @param int|false $product_id If you want to check the serial number against a specific product set the product id here else set to false
     * @return boolean If the serial is valid returns true else return false
     */
    public function checkUserSerial($email, $serial, $product_id = false)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $where = ['email' => strtolower($email), 'serial' => $serial];
            if (is_numeric($product_id)) {
                $where['product_id'] = intval($product_id);
            }
            return ($this->db->count($this->config->table_serials, $where, false) ? true : false);
        }
        return false;
    }
    
    /**
     * Disables a serial number in the database
     * @param string $serial_no This should be the serial number that you want to disable
     * @return boolean If the database has been updated will return true else returns false
     */
    public function disableSerial($serial_no)
    {
        return $this->db->update($this->config->table_serials, ['active' => 0], ['serial' => $serial_no]);
    }
    
    /**
     * Returns the total number of install that a serial number has had
     * @param string $serial This should be the seral number that you wish to check how many times it has been installed
     * @return int Returns the number of times the serial number has been used to install the item
     */
    public function getNumInstalls($serial)
    {
        return $this->db->count($this->config->table_attempts, ['serials' => $serial], false);
    }
}
