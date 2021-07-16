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
     * @codeCoverageIgnore
     */
    public function listDeliveryItems()
    {
        return false;
    }
    
    /**
     * Gets delivery item information
     * @param int $id This is the unique id of the item
     * @return false;
     * @codeCoverageIgnore
     */
    public function getDeliveryItem($id = 1)
    {
        return $this->listDeliveryItems();
    }
    
    /**
     * Added for compatibility (not used)
     * @param mixed Can enter anything it won't be used anyway just added for compatibilty
     * @return false
     * @codeCoverageIgnore
     */
    public function addDeliveryItem($info)
    {
        return false;
    }
    
    /**
     * Added for compatibility (not used)
     * @param int $id The delivery ID
     * @param mixed $info Wont be used enter anything you want
     * @return false
     * @codeCoverageIgnore
     */
    public function editDeliveryItem($id, $info)
    {
        return false;
    }
    
    /**
     * Added for compatibility (not used)
     * @return false
     * @codeCoverageIgnore
     */
    public function deleteDeliveryItem($id)
    {
        return false;
    }
}
