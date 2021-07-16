<?php

namespace ShoppingCart;

class Stock extends Product
{
    
    /**
     * Remove a given amount of items from the quantity of a certain product
     * @param int $product_id This should be the product ID you are removing an amount from
     * @param int $quantity This should be the number of items you wish to remove
     * @return boolean If the amount has been updated will return true else will return false
     */
    public function removeQuantity($product_id, $quantity = 1)
    {
        return $this->setQuantityInStock($product_id, intval($this->getStockQuantity($product_id) - $quantity));
    }
    
    /**
     * Add an amount of items to the quantity of a certain product
     * @param int $product_id This should be the product ID you are adding an amount to
     * @param int $quantity This should be the number of items you wish to add
     * @return boolean If the amount has been updated will return true else will return false
     */
    public function addQuantity($product_id, $quantity)
    {
        return $this->setQuantityInStock($product_id, intval($this->getStockQuantity($product_id) + $quantity));
    }
    
    /**
     * Updates the amount of quantity in stock of a given product to a given number
     * @param int $product_id This should be the product ID you are updating the amount for
     * @param int $no_items This should be the number of items have in stock
     * @return boolean If the amount has been updated will return true else will return false
     */
    public function setQuantityInStock($product_id, $no_items)
    {
        return $this->db->update($this->config->table_products, ['in_stock' => $no_items], ['product_id' => $product_id]);
    }
    
    /**
     * Returns the number of items in stock of a given product
     * @param int $product_id  This should be the product ID you are updating the amount for
     * @return int Will return the amount of items that are in stock for the given product
     */
    public function getStockQuantity($product_id)
    {
        $productInfo = $this->getProductByID($product_id);
        return $productInfo['in_stock'];
    }
    
    /**
     * Checks o see it an item is in stock or not
     * @param int $product_id  This should be the product ID you are checking if stock is available for
     * @return boolean If the item is in stock will return true else returns false
     */
    public function isInStock($product_id)
    {
        if ($this->getStockQuantity($product_id) >= 1) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns the products that meet the given criteria which should wither be in or out of stock
     * @param boolean $instock If this is set to true will return those items in stock else set to false for items out of stock
     * @param boolean $active If this is set to true will return only active items else will return both active and disabled products
     * @return array|false If any items exist they will be returned as an array else will return false
     */
    public function getItemsByStockLevel($instock = true, $active = true)
    {
        $where = [];
        $where['active'] = ($active === true ? 1 : 0);
        if ($instock === true) {
            $where['in_stock'] = ['>=' => 1];
        } else {
            $where['in_stock'] = 0;
        }
        return $this->db->selectAll($this->config->table_products, $where, '*', [], 0, false);
    }
}
