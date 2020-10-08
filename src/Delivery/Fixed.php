<?php

namespace ShoppingCart\Delivery;

use ShoppingCart\Modifiers\Cost;
use DBAL\Database;
use Configuration\Config;

class Fixed implements DeliveryInterface
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
     * Returns the set delivery cost
     * @return int|string This will be the cost of delivery
     */
    public function getDeliveryCost($item = '')
    {
        $price = $this->db->fetchColumn($this->config->table_delivery_fixed_cost, [], ['cost']);
        if ($price === false) {
            $price = 0;
        }
        return Cost::priceUnits($price, $this->decimals);
    }
    
    /**
     * List delivery items array
     * @return false Nothing to return as fixed cost is used so returns false
     */
    public function listDeliveryItems()
    {
        return $this->db->select($this->config->table_delivery_fixed_cost);
    }
    
    /**
     * Gets delivery item information
     * @param int $id This is the unique id of the item
     */
    public function getDeliveryItem($id = 1)
    {
        return $this->listDeliveryItems();
    }

    /**
     * Adds the fixed delivery cost to the database
     * @param int|string $cost This should be the set delivery cost that you are adding to the database
     * @return boolean If the delivery cost is added will return true else returns false
     */
    public function addDeliveryItem($cost)
    {
        if ($this->db->count($this->config->table_delivery_fixed_cost) == 1) {
            return $this->editDeliveryItem(1, $cost);
        }
        return$this->db->insert($this->config->table_delivery_fixed_cost, ['cost' => Cost::priceUnits($cost, $this->decimals)]);
    }
    
    /**
     * Edits the set delivery cost
     * @param int|string $cost_id This should be the cost ID
     * @param int|string $cost This should be the new set delivery cost
     * @return boolean If the set delivery cost is updated will return true else will return false
     */
    public function editDeliveryItem($cost_id, $cost)
    {
        return $this->db->update($this->config->table_delivery_fixed_cost, ['cost' => Cost::priceUnits($cost, $this->decimals)]);
    }
    
    /**
     * Deletes the fixed delivery cost from the database
     * @param int $cost_id This should be the id in the database assigned to the fixed delivery cost
     * @return boolean If the set delivery cost has been removed will return true else will return false
     */
    public function deleteDeliveryItem($cost_id)
    {
        if (is_numeric($cost_id)) {
            return $this->db->delete($this->config->table_delivery_fixed_cost, ['id' => $cost_id]);
        }
        return false;
    }
}
