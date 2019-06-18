<?php

namespace ShoppingCart\Delivery;

use DBAL\Database;
use Configuration\Config;

interface DeliveryInterface {
    public function __construct(Database $db, Config $config);
    public function getDeliveryCost($item);
    public function listDeliveryItems();
    public function getDeliveryItem($id);
    public function addDeliveryItem($info);
    public function editDeliveryItem($id, $info);
    public function deleteDeliveryItem($id);
}
