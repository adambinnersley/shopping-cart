<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;

class Tax
{
    protected $db;
    public $config;
    
    /**
     * Constructor
     * @param Database $db This should be the database class instance
     */
    public function __construct(Database $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * List all of the tax percentage amounts in the database
     * @return array|false If tax amounts exist they will be returned as an array else if none exist false will be returned
     */
    public function listTaxAmounts()
    {
        return $this->db->selectAll($this->config->table_tax);
    }
    
    /**
     * Get the tax percentage information for a given tax ID
     * @param int $tax_id This should be the unique tax ID assigned in the database
     * @return array|false If the tax information exists for the tax ID an array will be returned else will return false
     */
    public function getTaxInformation($tax_id)
    {
        return $this->db->select($this->config->table_tax, ['tax_id' => intval($tax_id)]);
    }
    
    /**
     * Add Tax information and percentage to the database
     * @param float $percentage This should be the percentage you wish to add to the database
     * @param string $description A short description for the tax amount
     * @param array $additionalInfo Any additional information for the database can be added as an array here
     * @return boolean If the information is successfully inserted will return true else will return false
     */
    public function addTax($percentage, $description = null, $additionalInfo = [])
    {
        if (is_numeric($percentage) && is_array($additionalInfo)) {
            return $this->db->insert($this->config->table_tax, array_merge(['percent' => $percentage, 'details' => $description], array_filter($additionalInfo)));
        }
        return false;
    }
    
    /**
     * Update the tax information in the database
     * @param int $tax_id This should be the unique tax ID that is assigned in the database
     * @param array $updateInfo Any information that needs updating can be added as an array here
     * @return boolean If the information is update will return true else will return false
     */
    public function editTax($tax_id, $updateInfo = [])
    {
        if (is_numeric($tax_id) && is_array(array_filter($updateInfo))) {
            return $this->db->update($this->config->table_tax, $updateInfo, ['tax_id' => intval($tax_id)], 1);
        }
        return false;
    }
    
    /**
     * Delete a specific tax information row from the database
     * @param int $tax_id The tax_id of the tax information that you wish to delete
     * @return boolean If the information is deleted will return true else will return false
     */
    public function deleteTax($tax_id)
    {
        if (is_numeric($tax_id)) {
            return $this->db->delete($this->config->table_tax, ['tax_id' => intval($tax_id)]);
        }
        return false;
    }
    
    /**
     * Returns the amount of tax that will be paid on each item of a given value
     * @param int $tax_id This should be the unique tax id assigned to the item
     * @param int|float $price This should be the current price charged for the item
     * @return double Returns the amount of tax that is paid on the item
     */
    public function calculateItemTax($tax_id, $price)
    {
        $taxInfo = $this->getTaxInformation($tax_id);
        return floor((($price / (100 + $taxInfo['percent'])) * $taxInfo['percent']) * 100) / 100;
    }
}
