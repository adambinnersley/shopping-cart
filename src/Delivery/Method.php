<?php

namespace ShoppingCart\Delivery;

use DBAL\Database;
use DBAL\Modifiers\Modifier;
use ShoppingCart\Modifiers\Cost;
use Configuration\Config;

class Method implements DeliveryInterface
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
     * Returns the delivery cost for the delivery method given
     * @param int $method_id This should be the delivery method selected
     * @return int|string This will be the cost of delivery
     */
    public function getDeliveryCost($method_id)
    {
        return Cost::priceUnits($this->db->fetchColumn($this->config->table_delivery_methods, ['id' => $method_id], ['price'], 0, [], 3600), $this->decimals);
    }
    
    /**
     * Returns all of the delivery methods available in the database
     * @return array|false This will be an array of delivery methods
     */
    public function listDeliveryItems()
    {
        return $this->db->selectAll($this->config->table_delivery_methods, [], '*', [], 0, 300);
    }
    
    /**
     * Gets delivery item information
     * @param int $id This is the unique id of the item
     * @return array|false Returns delivery method item if it exists else returns false
     */
    public function getDeliveryItem($id = 1)
    {
        return $this->db->select($this->config->table_delivery_methods, ['id' => $id], '*', [], 3600);
    }
    
    /**
     * Add a new delivery method to the database
     * @param array $info This should be the information array
     * @return boolean If the method has been added to the database will return true else returns false
     */
    public function addDeliveryItem($info)
    {
        if(Modifier::arrayMustContainFields(['description', 'price'], $info)){
            $info['price'] = Cost::priceUnits($info['price'], $this->decimals);
            return $this->db->insert($this->config->table_delivery_methods, $info);
        }
        return false;
    }
    
    /**
     * Edit a current delivery method
     * @param int $method_id This should be the unique delivery method id assigned in the database
     * @param array $info This should be the information array
     * @return boolean If the method is updated will return true else returns false
     */
    public function editDeliveryItem($method_id, $info)
    {
        if (is_numeric($method_id) && is_array($info) && (array_key_exists('price', $info) || array_key_exists('description', $info))) {
            if(array_key_exists('price', $info)){$info['price'] = Cost::priceUnits($info['price'], $this->decimals);}
            return $this->db->update($this->config->table_delivery_methods, $info, ['id' => $method_id]);
        }
        return false;
    }
    
    /**
     * Delete a delivery method from the database
     * @param int $method_id This should be the unique delivery method id assigned in the database
     * @return boolean If the method has been deleted will return true else returns false
     */
    public function deleteDeliveryItem($method_id)
    {
        return $this->db->delete($this->config->table_delivery_methods, ['id' => $method_id]);
    }
}
