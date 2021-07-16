<?php

namespace ShoppingCart\Delivery;

use ShoppingCart\Modifiers\Cost;
use DBAL\Database;
use Configuration\Config;

class Value implements DeliveryInterface
{
    protected $db;
    protected $config;
    
    protected $decimals;

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the ShoppingCartzConfig class
     */
    public function __construct(Database $db, Config $config, $decimals = 2)
    {
        $this->db = $db;
        $this->config = $config;
        $this->decimals = $decimals;
    }
    
    /**
     * Returns the delivery cost based on the value of the order
     * @param int $price This should be value of the order
     * @return int|string This will be the cost of delivery
     */
    public function getDeliveryCost($price)
    {
        $cost = $this->db->fetchColumn($this->config->table_delivery_value, ['min_price' => ['>=', Cost::priceUnits($price, $this->decimals)], 'max_price' => ['<=', Cost::priceUnits($price, $this->decimals)]], ['price'], 0, [], 3600);
        if ($cost !== false) {
            return Cost::priceUnits($cost, $this->decimals);
        }
        return Cost::priceUnits(0, $this->decimals);
    }
    
    /**
     * Return a list of all of the value ranges
     * @return array Will return a list of all of the value ranges
     */
    public function listDeliveryItems()
    {
        return $this->db->selectAll($this->config->table_delivery_value, [], '*', [], 0, 300);
    }
    
    /**
     * Gets delivery item information
     * @param int $id This is the unique id of the item
     */
    public function getDeliveryItem($id = 1)
    {
        return $this->db->select($this->config->table_delivery_value, ['id' => $id], '*', [], 3600);
    }
    
    /**
     * Adds the delivery price based on the min and max order value
     * @param array $info This should be the information array
     * @return boolean If the price range has been added will return true else will return false
     */
    public function addDeliveryItem($info)
    {
        if (!$this->checkForConflicts($info['min_price'], $info['max_price']) && is_array($info)) {
            $info['price'] = Cost::priceUnits($info['price'], $this->decimals);
            return $this->db->insert($this->config->table_delivery_value, $info);
        }
        return false;
    }
    
    /**
     * Updates the delivery price range
     * @param int $cost_id This should be the unique id for the price range information that you are updating
     * @param array $info This should be the information array
     * @return boolean If the price range has been updated will return true else will return false
     */
    public function editDeliveryItem($cost_id, $info)
    {
        if (!$this->checkForConflicts($info['min_price'], $info['max_price'], $cost_id) && is_array($info)) {
            $info['price'] = Cost::priceUnits($info['price'], $this->decimals);
            return $this->db->update($this->config->table_delivery_value, $info, ['id' => $cost_id]);
        }
        return false;
    }
    
    /**
     * Deletes a given delivery cost price range
     * @param type $cost_id This should be the unique id for the price range information that you are deleting
     * @return boolean If the price range has been deleted will return true else will return false
     */
    public function deleteDeliveryItem($cost_id)
    {
        return $this->db->delete($this->config->table_delivery_value, ['id' => $cost_id]);
    }
    
    /**
     * Checks to see if any of the ranges will conflict with each other
     * @param int|string $min_price The minimum price for the price range
     * @param int|string $max_price The maximum price for the price range
     * @param int $cost_id If you are only updating the price make sure the current row is not check as the values will be identical and it will always return true
     * @return boolean If there are any conflicting ranges will return true else will return false
     */
    protected function checkForConflicts($min_price, $max_price, $cost_id = false)
    {
        foreach ($this->listDeliveryItems() as $range) {
            if (($min_price === $range['min_price'] || $min_price === $range['max_price'] || ($min_price >= $range['min_price'] && $min_price <= $range['max_price']) || $max_price === $range['min_price'] || $max_price === $range['max_price'] || ($max_price >= $range['min_price'] && $max_price <= $range['max_price'])) && $cost_id !== $range['id']) {
                return true;
            }
        }
        return false;
    }
}
