<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Modifiers\Cost;

class Delivery{
    /**
     * Instance of the database class
     * @var object
     */
    protected $db;
    
    /**
     * Instance of the config class
     * @var object 
     */
    protected $config;
    
    /**
     * The number of decimals in the local currency
     * @var int
     */
    protected $decimals;

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the config class
     * @param int $decimals This should be the number of decimals for the local currency
     */
    public function __construct(Database $db, Config $config, $decimals = 2) {
        $this->db = $db;
        $this->config = $config;
        $this->decimals = $decimals;
    }
    
    /**
     * Returns the delivery cost based on the config settings and the items in the basket
     * @param array|false $orderInfo This should be all of the order information
     * @param int|string $total_cart_weight This should be the total weight of the items in the basket
     * @return int|string The total cost of delivery will be returned
     */
    public function getDeliveryCost($orderInfo, $total_cart_weight = 0) {
        $deliveryType = ucwords($this->config->delivery_type);
        $deliveryObject = "ShoppingCart\Delivery\\".$deliveryType;
        $delivery = new $deliveryObject($this->db, $this->config, $this->decimals);
        if($deliveryType === 'Free' || $deliveryType == 'Fixed') {return $delivery->getDeliveryCost();}
        elseif($deliveryType === 'Method' && is_array($orderInfo) && $orderInfo['delivery_method'] !== NULL) {return $delivery->getDeliveryCost($orderInfo['delivery_method']);}
        elseif($deliveryType === 'Value' && is_array($orderInfo)) {return $delivery->getDeliveryCost($orderInfo['cart_total']);}
        elseif($deliveryType === 'Weight') {return $delivery->getDeliveryCost($total_cart_weight);}
        else{return Cost::priceUnits(0, $this->decimals);}
    }
}
