<?php

namespace ShoppingCart\Delivery;

use ShoppingCart\Modifiers\Cost;
use DBAL\Database;
use Configuration\Config;

class Free implements DeliveryInterface
{
    protected $db;
    protected $config;
    
    protected $decimals;

    /**
     * Provides the instances of the objects needed
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the ShoppingCart config class
     */
    public function __construct(Database $db, Config $config, $decimals = 2)
    {
        $this->db = $db;
        $this->config = $config;
        $this->decimals = $decimals;
    }
    
    /**
     * Returns the delivery cost as the free amount
     * @return string This will be the value 0 represented as the local currency decimal
     */
    public function getDeliveryCost($item = '')
    {
        return Cost::priceUnits(0, $this->decimals);
    }
    
    /**
     * Added for compatibility (not used)
     * @return false
     */
    public function listDeliveryItems()
    {
        return false;
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
     * Added for compatibility (not used)
     * @return false
     */
    public function addDeliveryItem($info)
    {
        return false;
    }
    
    /**
     * Added for compatibility (not used)
     * @return false
     */
    public function editDeliveryItem($id, $info)
    {
        return false;
    }
    
    /**
     * Added for compatibility (not used)
     * @return false
     */
    public function deleteDeliveryItem($id)
    {
        return false;
    }
}
