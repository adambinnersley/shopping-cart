<?php

namespace ShoppingCart\Delivery;

use ShoppingCart\Modifiers\Cost;
use DBAL\Database;
use Configuration\Config;

class Weight implements DeliveryInterface
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
     * Returns the delivery cost based on the wight of the items
     * @param int|string $weight This should be the total weight of the order
     * @return int|string This will be the cost of delivery
     */
    public function getDeliveryCost($weight)
    {
        $deliveryInfo = $this->db->fetchColumn($this->config->table_delivery_weight, ['max_weight' => ['<=', $weight]], ['price'], 0, ['max_weight' => 'DESC'], 600);
        if ($deliveryInfo !== false) {
            return Cost::priceUnits($deliveryInfo, $this->decimals);
        }
        return Cost::priceUnits(0, $this->decimals);
    }
    
    /**
     * Returns all of the weights and costs
     * @return array Returns a list of all of the max_weigh and costs ordered by the max_weight
     */
    public function listDeliveryItems()
    {
        return $this->db->selectAll($this->config->table_delivery_weight, [], '*', ['max_weight' => 'DESC'], 0, 300);
    }
    
    /**
     * Gets delivery item information
     * @param int $id This is the unique id of the item
     */
    public function getDeliveryItem($id = 1)
    {
        return $this->db->select($this->config->table_delivery_weight, ['id' => $id], '*', [], 3600);
    }
    
    /**
     * Adds a delivery weight range to the database
     * @param array $info This should be the information array
     * @return boolean If the information is added to the database will return true else will return false
     */
    public function addDeliveryItem($info)
    {
        if (!$this->checkForConflicts($info['max_weight']) && is_array($info)) {
            $info['price'] = Cost::priceUnits($info['price'], $this->decimals);
            return $this->db->insert($this->config->table_delivery_weight, $info);
        }
        return false;
    }
    
    /**
     * Edits a weight amount in the database
     * @param int $weight_id This should be the unique weight id
     * @param array $info This should be the information array
     * @return boolean If the information is updated will return true else will return false
     */
    public function editDeliveryItem($weight_id, $info)
    {
        if (!$this->checkForConflicts($info['max_weight'], $weight_id) && is_array($info)) {
            $info['price'] = Cost::priceUnits($info['price'], $this->decimals);
            return $this->db->update($this->config->table_delivery_weight, $info, ['id' => $weight_id], 1);
        }
        return false;
    }
    
    /**
     * Deletes a weight parameter from the database
     * @param int $weight_id This should be the id for the item that you wish to delete
     * @return boolean If the item is deleted will return true else returns false
     */
    public function deleteDeliveryItem($weight_id)
    {
        return $this->db->delete($this->config->table_delivery_weight, ['id' => $weight_id]);
    }
    
    /**
     * Checks to see if any of the ranges will conflict with each other
     * @param int|string $weight This should be the maximum weight to check for identical value
     * @param int|false $weight_id If you are only updating the price make sure the current row is not check as the weight will be identical as it will always return true
     * @return boolean If there are any conflicting ranges will return true else will return false
     */
    protected function checkForConflicts($weight, $weight_id = false)
    {
        $where = [];
        $where['max_weight'] = $weight;
        if ($weight_id !== false && is_numeric($weight_id)) {
            $where['id'] = ['!=', $weight_id];
        }
        
        if ($this->db->select($this->config->table_delivery_weight, $where)) {
            return true;
        }
        return false;
    }
}
