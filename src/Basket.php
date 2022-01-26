<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Modifiers\Cost;
use DBAL\Modifiers\Modifier;
use Blocking\IPBlock;

class Basket
{
    protected $db;
    public $product;
    public $tax;
    public $voucher;
    public $user;
    protected $ip_address;

    protected $user_id;

    public $config;
    
    protected $totals;
    protected $products = [];
    protected $has_download = 0;
    
    protected $decimals;
    
    /**
     * Adds instance of the required classes and clears old orders
     * @param Database $db This should be an instance of the Database class
     * @param Config $config This should be an instance of the Config class
     * @param object|false $user This should be an instance of the user/customer class
     * @param object|false $product This should be an instance of the product class
     */
    public function __construct(Database $db, Config $config, $user = false, $product = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->product = (is_object($product) ? $product : new Product($this->db, $this->config));
        $this->tax = new Tax($this->db, $this->config);
        $this->voucher = new Voucher($this->db, $this->config, $this->product);
        $this->user = (is_object($user) ? $user : new Customers($this->db, $config));
        $this->user_id = $this->user->getUserID();
        $this->decimals = Currency::getCurrencyDecimals($this->config->currency);
        $this->ip_address = new IPBlock($this->db);
        if (!session_id()) {
            session_start();
        }
    }
    
    /**
     * Creates a unique order number
     * @return string A unique order number containing the date and time will be returned
     */
    protected function createOrderID()
    {
        return date('ymd') . '-' . date('His') . '-' . rand(1000, 9999);
    }
    
    /**
     * Adds the order information into the database
     * @param array $additional Additional fields to insert
     * @return boolean If the order is successfully inserted will return true else returns false
     */
    protected function createOrder($additional = [])
    {
        $this->updateTotals();
        return $this->db->insert($this->config->table_basket, array_merge(['customer_id' => ($this->user_id === 0 ? null : $this->user_id), 'order_no' => $this->createOrderID(), 'digital' => $this->has_download, 'subtotal' => $this->totals['subtotal'], 'discount' => $this->totals['discount'], 'total_tax' => $this->totals['tax'], 'delivery' => $this->totals['delivery'], 'cart_total' => $this->totals['total'], 'sessionid' => session_id(), 'ipaddress' => $this->ip_address->getUserIP()], $additional));
    }
    
    /**
     * Returns the basket information for the current users pending order or if given a selected order number
     * @param string $orderNo This should be the order number you want to get the order information for, leave blank for current users pending order
     * @param array $additional Addition where fields
     * @return array|false If the order exists an array will be returned else will return false
     */
    public function getBasket($orderNo = '', $additional = [])
    {
        if (empty(trim($orderNo))) {
            $where = ['status' => 1, 'customer_id' => ($this->user_id === 0 ? 'IS NULL' : $this->user_id), 'sessionid' => session_id()];
        } else {
            $where = ['order_no' => $orderNo];
        }
        
        $basketInfo = $this->db->select($this->config->table_basket, array_merge($where, $additional), '*', ['order_id' => 'DESC'], false);
        if (is_array($basketInfo)) {
            $this->getProducts($basketInfo['order_id'], $additional);
            $basketInfo['products'] = $this->products;
        }
        return $basketInfo;
    }
    
    /**
     * Updates the basket in the database
     * @param array $additional Additional where fields
     * @return boolean If the information is updated will return true else will return false
     */
    protected function updateBasket($additional = [])
    {
        $this->updateTotals();
        if (!empty($this->products)) {
            return $this->db->update($this->config->table_basket, ['digital' => $this->has_download, 'subtotal' => $this->totals['subtotal'], 'discount' => $this->totals['discount'], 'total_tax' => $this->totals['tax'], 'delivery' => $this->totals['delivery'], 'cart_total' => $this->totals['total']], array_merge(['customer_id' => ($this->user_id === 0 ? 'IS NULL' : $this->user_id), 'sessionid' => session_id(), 'status' => 1], $additional));
        }
        return $this->emptyBasket();
    }

    /**
     * Deletes the order from the database
     * @return boolean If the basket has successfully been deleted will return true else returns false
     */
    public function emptyBasket()
    {
        return $this->db->delete($this->config->table_basket, ['customer_id' => ($this->user_id === 0 ? 'IS NULL' : $this->user_id), 'sessionid' => session_id(), 'status' => 1], 1);
    }
    
    /**
     * Changes an old order be be moved to the current basket to complete payment
     * @param string $orderNo This should be the order no string
     * @return boolean If updated will return true else returns false
     */
    public function changeCurrentBasket($orderNo)
    {
        if ($this->db->select($this->config->table_basket, ['customer_id' => $this->user_id, 'order_no' => $orderNo, 'status' => 1], '*', [], false)) {
            $this->db->update($this->config->table_basket, ['sessionid' => 'NULL'], ['customer_id' => $this->user_id, 'sessionid' => session_id(), 'status' => 1]);
            return $this->db->update($this->config->table_basket, ['sessionid' => session_id()], ['customer_id' => $this->user_id, 'order_no' => $orderNo, 'status' => 1]);
        }
        return false;
    }
    
    /**
     * Gets an array of products that the user has added to their basket
     * @param array $additional Additional where fields
     * @return array|false This should be an array featuring the item number and the quantity in the basket if any exist else will be false
     */
    protected function getProducts($orderID = '', $additional = [])
    {
        if (empty(trim($orderID)) || !is_numeric($orderID)) {
            $baskteInfo = $this->getBasket('', $additional);
            $orderID = ($baskteInfo !== false ? $baskteInfo['order_id'] : false);
        }
        if (!empty($this->products) && is_array($this->products)) {
            return $this->products;
        } elseif (is_numeric($orderID)) {
            $this->products = $this->db->selectAll($this->config->table_basket_products, ['order_id' => $orderID], '*', [], false);
            if (is_array($this->products)) {
                foreach ($this->products as $i => $product) {
                    $this->products[$i]['product_info'] = unserialize($product['product_info']);
                }
            }
            return $this->products;
        }
        return false;
    }

    /**
     * Adds an item by its SKU code to the basket
     * @param string $code This should be the unique code given to the product
     * @return boolean If the product has successfully been added will return true else will return false
     */
    public function addItemByCodeToBasket($code)
    {
        $productInfo = $this->product->getProductByCode($code);
        return $this->addItemToBasket($productInfo['product_id']);
    }
    
    /**
     * Adds an item to the basket by its product ID
     * @param int $product_id This should be the product ID
     * @param int $quantity This should be the number of products that you wish to add to the basket
     * @param boolean $update If you want to add quantity to existing items then set to true else to override set to false
     * @param array $additional Additional where fields
     * @return boolean If the basket has successfully been updated will return true else returns false
     */
    public function addItemToBasket($product_id, $quantity = 1, $update = true, $additional = [])
    {
        $this->getProducts('', $additional);
        $productInfo = $this->product->getProductByID($product_id);
        if (is_numeric($product_id) && is_numeric($quantity) && is_array($productInfo)) {
            $orderInfo = $this->getBasket('', $additional);
            if ($orderInfo === false) {
                $this->createOrder($additional);
                $orderInfo['order_id'] = $this->db->lastInsertId();
            }
            
            $match = false;
            if (!empty($this->products) && is_array($this->products)) {
                foreach ($this->products as $i => $product) {
                    if (intval($product['product_id']) === intval($product_id)) {
                        $this->products[$i]['quantity'] = intval($update !== true ? ($product['quantity'] + $quantity) : $quantity);
                        $this->db->update($this->config->table_basket_products, ['quantity' => $this->products[$i]['quantity']], ['product_id' => $product_id, 'order_id' => $orderInfo['order_id']], 1);
                        $match = true;
                    }
                }
            }
            if ($match !== true) {
                $this->db->insert($this->config->table_basket_products, ['order_id' => $orderInfo['order_id'], 'product_id' => $product_id, 'quantity' => $quantity, 'product_info' => serialize(['name' => $productInfo['name'], 'price' => $this->product->getProductPrice($product_id), 'tax_id' => $productInfo['tax_id']])]);
            }
            $this->products = [];
            $this->getProducts($orderInfo['order_id'], $additional);
        }
        return $this->updateBasket($additional);
    }
    
    /**
     * Remove a given item from the basket
     * @param int $product_id This should be the product ID
     * @param array $additional Additional where fields
     * @return boolean If the basket has successfully been updated will return true else returns false
     */
    public function removeItemFromBasket($product_id, $additional = [])
    {
        $orderInfo = $this->getBasket('', $additional);
        if (is_numeric($product_id)) {
            $this->db->delete($this->config->table_basket_products, ['product_id' => $product_id, 'order_id' => $orderInfo['order_id']]);
            unset($this->products);
        }
        $this->getProducts($orderInfo['order_id'], $additional);
        return $this->updateBasket($additional);
    }
    
    /**
     * Update the quantity of a given product in the basket
     * @param int $product_id This should be the product ID
     * @param int $quantity This should be the total quantity that you want in the cart of this item
     * @param array $additional Additional where fields
     * @return boolean If the basket has successfully been updated will return true else returns false
     */
    public function updateQuantityInBasket($product_id, $quantity, $additional = [])
    {
        if ($quantity >= 1) {
            return $this->addItemToBasket($product_id, $quantity, true, $additional);
        }
        return $this->removeItemFromBasket($product_id, $additional);
    }
    
    /**
     * Update the totals for all items in the basket including delivery and tax
     */
    protected function updateTotals()
    {
        $totaltax = 0;
        $totalweight = 0;
        $subtotal = 0;
        
        if (!empty($this->products) && is_array($this->products)) {
            foreach ($this->products as $product) {
                $productInfo = $this->product->getProductByID($product['product_id']);
                $totaltax = $totaltax + ($this->tax->calculateItemTax($productInfo['tax_id'], $this->product->getProductPrice($product['product_id'])) * $product['quantity']);
                $totalweight = $totalweight + ($this->product->getProductWeight($product['product_id']) * $product['quantity']);
                $subtotal = $subtotal + ($this->product->getProductPrice($product['product_id']) * $product['quantity']);
                if ($this->has_download == 0 && $this->product->isProductDownload($product['product_id'])) {
                    $this->has_download = 1;
                }
            }
        }
        $this->totals['subtotal'] = Cost::priceUnits(($subtotal - $totaltax), $this->decimals);
        $this->totals['discount'] = Cost::priceUnits(0, $this->decimals);
        $this->totals['tax'] = Cost::priceUnits($totaltax, $this->decimals);
        $this->totals['delivery'] = Cost::priceUnits($this->getDeliveryCost($totalweight), $this->decimals);
        $this->totals['total'] = Cost::priceUnits((($subtotal - $this->totals['discount']) + $this->totals['delivery']), $this->decimals);
    }
    
    /**
     * Return the delivery cost fro the current order
     * @param int|string Set the weight of the basket just incase delivery is returned based on the weight of items
     * @return int|string The cost of the delivery will be returned as a numeric value
     */
    protected function getDeliveryCost($basket_items_weight = 0)
    {
        $delivery = new Delivery($this->db, $this->config, $this->decimals);
        return $delivery->getDeliveryCost($this->getBasket(), $basket_items_weight);
    }
    
    /**
     * Returns the delivery address details based on if any information exists within the delivery details database
     * @param array $userInfo This should be all of the the users information as an array
     * @param int $delivery_id If the delivery ID exists within the order give this number here to try to retrieve the details
     * @return array Will return an array of the delivery details for the order
     */
    public function getDeliveryDetails($userInfo, $delivery_id)
    {
        if (is_numeric($delivery_id) && $delivery_id >= 1) {
            $deliveryInfo = $this->user->getDeliveryAddress($delivery_id, $userInfo['id']);
            if ($deliveryInfo !== false) {
                return $deliveryInfo;
            }
        }
        return $userInfo;
    }
    
    /**
     * Inserts a voucher code into the order, checks to see if the code is active first before adding else will insert NULL as the code so no discount is applied
     * @param string $code This should be the unique voucher code
     * @return boolean If the code is successfully update will return true else returns false if nothing has been updated
     */
    public function addVoucherCode($code)
    {
        return $this->updateVoucherCode($code);
    }
    
    /**
     * Inserts a voucher code into the order, checks to see if the code is active first before adding else will insert NULL as the code so no discount is applied
     * @param string $code This should be the unique voucher code
     * @return boolean If the code is successfully update will return true else returns false if nothing has been updated
     */
    public function updateVoucherCode($code)
    {
        $voucherInfo = $this->voucher->getVoucherByCode($code, true);
        return $this->db->update($this->config->table_basket, ['voucher' => Modifier::setNullOnEmpty($voucherInfo['code'])], ['customer_id' => ($this->user_id === 0 ? 'IS NULL' : $this->user_id), 'sessionid' => session_id(), 'status' => 1], 1);
    }
}
