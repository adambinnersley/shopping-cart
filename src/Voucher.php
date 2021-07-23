<?php

namespace ShoppingCart;

use DBAL\Database;
use DBAL\Modifiers\Modifier;
use Configuration\Config;
use ShoppingCart\Modifiers\Cost;

class Voucher
{
    protected $db;
    protected $product;
    public $config;
    
    public $decimals;
    
    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the Config class
     * @param object|false $product This should be an instance of the Config class
     */
    public function __construct(Database $db, Config $config, $product = false)
    {
        $this->db = $db;
        $this->config = $config;
        $this->product = $product;
        $this->decimals = Currency::getCurrencyDecimals($this->config->currency);
    }
    
    /**
     * List all of the vouchers that are in the database
     * @return array|false If any vouchers exist they will be returned as a multidimensional array else if no vouchers exist false will be returned
     */
    public function listVouchers()
    {
        return $this->db->selectAll($this->config->table_voucher, [], '*', [], 0, 3600);
    }
    
    /**
     * Gets voucher information based on given information
     * @param array $where Array of parameters to search that database on
     * @param boolean $active Set this to true to only search for information from active vouchers
     * @return array|false If any vouchers exist the information will be returned as an array else if nothing exists will return false
     */
    protected function getVoucher($where, $active = true)
    {
        if ($active === true) {
            $where['active'] = 1;
            $where['expire'] = ['>=', date('Y-m-d H:i:s')];
        }
        if (is_array($where)) {
            $voucherInfo = $this->db->select($this->config->table_voucher, $where);
            if (isset($voucherInfo['selected_products']) && $voucherInfo['selected_products'] !== null) {
                $voucherInfo['selected_products'] = unserialize($voucherInfo['selected_products']);
            }
            if ((($voucherInfo['allowed'] === 0 || $voucherInfo['allowed'] > $voucherInfo['times_used']) && $active === true) || $active !== true) {
                return $voucherInfo;
            }
        }
        return false;
    }
    
    /**
     * Gets voucher information based on a given unique ID
     * @param int $voucher_id This should be the unique voucher ID given in the database
     * @param boolean $active If you only want active voucher information set this value to true else for all vouchers set to false
     * @return array|false If a voucher exists will return an array else will return false
     */
    public function getVoucherByID($voucher_id, $active = true)
    {
        if (is_numeric($voucher_id)) {
            return $this->getVoucher(['voucher_id' => $voucher_id], $active);
        }
        return false;
    }
    
    /**
     * Gets voucher information based on a the vouchers unique code
     * @param string $code This should be the vouchers unique code you are getting any voucher information for
     * @param boolean $active If you only want to look at active vouchers information set to true else for any set to false
     * @return array If any vouchers exist an array will be returned else false will be returned if no results
     */
    public function getVoucherByCode($code, $active = true)
    {
        return $this->getVoucher(['code' => $code], $active);
    }
    
    /**
     * Adds a voucher to the database
     * @param string $code This should be the unique voucher code
     * @param array $discount This should be the discount['amount'] or discount['percent']
     * @param date $expire A date of expiry for the voucher
     * @param int $active If the voucher should be set as active set this value to 1 else set to 0
     * @return boolean If the information is added to the database will return true else returns false
     */
    public function addVoucher($code, $discount, $expire, $active = 1, $additionalInfo = [])
    {
        if (!$this->getVoucherByCode($code) && is_array($discount) && (!empty(trim($discount['amount'])) || !empty(trim($discount['percent'])))) {
            return $this->db->insert($this->config->table_voucher, array_merge(['code' => $code, 'expire' => $expire, 'active' => intval($active), 'amount' => Modifier::setNullOnEmpty($discount['amount']), 'percent' => Modifier::setNullOnEmpty($discount['percent'])], $additionalInfo));
        }
        return false;
    }
    
    /**
     * Edits the voucher information in the database
     * @param int $voucher_id This should be the unique voucher ID
     * @param array $additionalInfo This should be the new voucher information with updates
     * @return boolean Will return true if successfully updated else will return false
     */
    public function editVoucher($voucher_id, $additionalInfo = [])
    {
        if (isset($additionalInfo['amount']) || isset($additionalInfo['percent'])) {
            $additionalInfo['amount'] = Modifier::setNullOnEmpty($additionalInfo['amount']);
            $additionalInfo['percent'] = Modifier::setNullOnEmpty($additionalInfo['percent']);
        }
        if (is_array($additionalInfo)) {
            return $this->db->update($this->config->table_voucher, $additionalInfo, ['voucher_id' => intval($voucher_id)], 1);
        }
        return false;
    }
    
    /**
     * Deletes a voucher from the database
     * @param int $voucher_id This should be the unique voucher ID assigned in the database
     * @return boolean If the voucher has successfully been deleted will return true else returns false
     */
    public function deleteVoucher($voucher_id)
    {
        return $this->db->delete($this->config->table_voucher, ['voucher_id' => intval($voucher_id)]);
    }
    
    /**
     * Change the voucher status between active and disabled
     * @param int $voucher_id This should be the unique voucher ID assigned in the database
     * @param int $status If you want to set the status to active set to 1 else to disable set to 0
     * @return boolean If the voucher has been updated will return true else returns false
     */
    public function changeVoucherStatus($voucher_id, $status = 1)
    {
        return $this->editVoucher($voucher_id, ['active' => $status]);
    }
    
    /**
     * Gets the discount amount applied to the order if a code has been entered
     * @param string $code This should be the voucher code that the discount should be applied
     * @param array $basket_products This should be an array of all of the product and quantities
     * @param int|string $basket_total This should be the basket total before any discount is applied
     * @return string The total discount amount will be applied
     */
    public function getDiscountAmount($code, $basket_products, $basket_total)
    {
        $codeInfo = $this->getVoucherByCode($code);
        if (is_array($codeInfo) && $codeInfo['selected_products'] === null) {
            if ($codeInfo['percent'] !== null) {
                return Cost::priceUnits(($basket_total * ($codeInfo['percent'] / 100)), $this->decimals);
            }
            return Cost::priceUnits($codeInfo['amount'], $this->decimals);
        }
        if (is_array($codeInfo['selected_products']) && is_array($basket_products)) {
            return $this->calculateProductDiscount($codeInfo, $basket_products, $basket_total);
        }
        return Cost::priceUnits(0, $this->decimals);
    }
    
    /**
     * Calculate the discount on vouchers that only allow selected products to be discounted
     * @param array $codeInfo This should be the voucher information that has been retrieved form the database
     * @param array $basket_products All of the items that are included in the basket should be includes as an array
     * @param int|string $basket_total This should be the basket_total before any discount
     * @return string The total amount of discount for the voucher will be returned
     */
    protected function calculateProductDiscount($codeInfo, $basket_products, $basket_total)
    {
        $usage = false;
        $discountablePrice = 0;
        foreach ($basket_products as $product_id => $quantity) {
            if (array_key_exists($product_id, $codeInfo['selected_products'])) {
                $usage = true;
                $productClass = (is_object($this->product) ? $this->product : new Product($this->db, $this->config));
                $discountablePrice = ($discountablePrice + ($productClass->getProductPrice($product_id) * $quantity));
            }
        }
        if ($usage === true && $codeInfo['percent'] !== null) {
            return Cost::priceUnits(($discountablePrice * ($codeInfo['percent'] / 100)), $this->decimals);
        } elseif ($usage === true && $codeInfo['amount'] !== null) {
            return Cost::priceUnits($codeInfo['amount'], $this->decimals);
        }
        return Cost::priceUnits(0, $this->decimals);
    }
    
    /**
     * Adds selected products to a voucher so that only selected products may be used with the voucher code
     * @param int $voucher_id This should be the unique voucher ID that you are adding the products to
     * @param int|array $product_id This should either be a single product ID or an array of product ID to add to the voucher
     * @return boolean If the products are added to the voucher it will return true else will return false
     */
    public function addSelectedProductToVoucher($voucher_id, $product_id)
    {
        if (is_numeric($voucher_id) && (is_numeric($product_id) || is_array($product_id))) {
            $voucherInfo = $this->getVoucherByID($voucher_id);
            if (is_array($product_id)) {
                foreach ($product_id as $product) {
                    if ($this->db->select($this->config->table_products, ['product_id' => $product])) {
                        $voucherInfo['selected_products'][$product] = 1;
                    }
                }
            } elseif ($this->db->select($this->config->table_products, ['product_id' => $product_id])) {
                $voucherInfo['selected_products'][$product_id] = 1;
            }
            return $this->db->update($this->config->table_voucher, ['selected_products' => (is_array($voucherInfo['selected_products']) ? serialize($voucherInfo['selected_products']) : null)], ['voucher_id' => $voucher_id]);
        }
        return false;
    }
    
    /**
     * Remove a selected product from being used with a voucher
     * @param int $voucher_id This should be the unique voucher ID
     * @param int $product_id This should be the product_id to remove as one of the products that the voucher code can be used with
     * @return boolean If the product has successfully been removed will return true else returns false
     */
    public function removeSelectedProductFromVoucher($voucher_id, $product_id)
    {
        if (is_numeric($voucher_id) && is_numeric($product_id)) {
            $voucherInfo = $this->getVoucherByID($voucher_id);
            unset($voucherInfo['selected_products'][$product_id]);
            if (!empty($voucherInfo['selected_products'])) {
                $products = serialize($voucherInfo['selected_products']);
            } else {
                $products = null;
            }
            return $this->db->update($this->config->table_voucher, ['selected_products' => $products], ['voucher_id' => $voucher_id]);
        }
        return false;
    }
    
    /**
     * Update the number of times that the voucher code has been used incase a limit has been set
     * @param string $voucher_code This should be the voucher code that has been used
     * @return boolean If the no. of times the voucher has been used field is updated will return true else returns false
     */
    public function addUsageToVoucher($voucher_code)
    {
        $voucherInfo = $this->getVoucherByCode($voucher_code);
        return $this->db->update($this->config->table_voucher, ['times_used' => intval($voucherInfo['times_used'] + 1)], ['code' => $voucher_code]);
    }
}
