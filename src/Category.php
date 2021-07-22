<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Modifiers\URL;

class Category
{
    protected $db;
    public $config;
    
    protected $categoryInfo = [];

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the ShoppingCartzConfig class
     */
    public function __construct(Database $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
    }
    
    /**
     * List all of the categories in the database
     * @param boolean $active If you only want to view the active categories set to true else set to false
     * @param array $where Addition where fields
     * @return array|false If any categories exist they will be returned as an array else will return false
     */
    public function listCategories($active = true, $where = [])
    {
        return $this->db->selectAll($this->config->table_categories, $this->addWhereIsActive($active, $where), '*', ['order' => 'ASC'], 0, 86400);
    }
    
    /**
     * Get category information from the database
     * @param array $where This should be an array with the values that are being searched
     * @return array|false If the query returns any results will return the category information as an array else will return false
     */
    protected function getCategoryInfo($where)
    {
        return $this->db->select($this->config->table_categories, $where, '*', [], 86400);
    }
    
    /**
     * Get a category based on the given ID
     * @param int $category_id This should be the category ID of the category information you wish to retrieve
     * @return array|false If category information exists the information array will be returned else will return false if nothing exists
     */
    public function getCategoryByID($category_id)
    {
        if (!empty($this->categoryInfo[$category_id])) {
            return $this->categoryInfo[$category_id];
        }
        if (is_numeric($category_id)) {
            $this->categoryInfo[$category_id] = $this->getCategoryInfo(['id' => $category_id]);
            return $this->categoryInfo[$category_id];
        }
        return false;
    }
    
    /**
     * Get the category information based on the given URL
     * @param string $category_url This should be the URL of the category that you wish to get the information for
     * @param array $where Addition where fields
     * @return array|false If the category exists the information will be returned as and array else will return false
     */
    public function getCategoryByURL($category_url, $where = [])
    {
        if (is_array($where)) {
            return $this->getCategoryInfo(array_merge(['url' => $category_url], $where));
        }
        return false;
    }
    
    /**
     * Add a category to the database
     * @param string $category_name This is the new categories name
     * @param string $description Any test that should be added to the category should be added here
     * @param string $url A unique URL should be added
     * @param array $additionalInfo Any additional information should be added as an array as field and value
     * @return boolean If the category is successfully inserted will return true else returns false
     */
    public function addCategory($category_name, $description, $url, $additionalInfo = [])
    {
        if (!empty(trim($category_name)) && !empty(trim($description)) && !$this->getCategoryByURL($url)) {
            return $this->db->insert($this->config->table_categories, array_merge(['name' => $category_name, 'description' => $description, 'url' => URL::makeURI($url)], $additionalInfo));
        }
        return false;
    }
    
    /**
     * Edit the category information in the database
     * @param int $category_id This should be the ID of the category that you are changing
     * @param array $editInfo This should be the fields and values of the updated information for the category
     * @return boolean If the information is successfully updated will return true else returns false
     */
    public function editCategory($category_id, $editInfo)
    {
        if (is_numeric($category_id)) {
            if (!empty($editInfo['url'])) {
                $editInfo['url'] = URL::makeURI($editInfo['url']);
            }
            return $this->db->update($this->config->table_categories, $editInfo, ['id' => intval($category_id)]);
        }
        return false;
    }
    
    /**
     * Deletes a category
     * @param int $category_id This should be the unique category id which is associated in the database
     * @param array $where Addition where fields
     * @return boolean If the category is deleted will return true else returns false
     */
    public function deleteCategory($category_id, $where = [])
    {
        if ($this->numberOfProductsInCategory($category_id) == 0) {
            return $this->db->delete($this->config->table_categories, array_merge(['id' => $category_id], $where));
        }
        return false;
    }
    
    /**
     * Returns the URL for a given category ID
     * @param int $category_id This should be the unique category ID
     * @return string|false If the category exists the unique URL will be returned else will return false
     */
    public function getCategoryURL($category_id)
    {
        $categoryInfo = $this->getCategoryByID($category_id);
        if (!empty($categoryInfo)) {
            return $categoryInfo['url'];
        }
        return false;
    }
    
    /**
     * Change the category display order
     * @param int $category_id This should be the category ID that you are changing the order for
     * @param string $direction The direction that you are moving the category i.e. 'up' or 'down'
     * @return boolean If the category order is updated will return true else returns false
     */
    public function changeCategoryOrder($category_id, $direction = 'up', $additional = [])
    {
        $catdetails = $this->db->select($this->config->table_categories, array_merge(['id' => $category_id], $additional), ['order'], [], false);
        $neworder = ($direction === 'up' ? ($catdetails['order'] - 1) : ($catdetails['order'] + 1));
        $new = $this->db->select($this->config->table_categories, array_merge(['order' => $neworder], $additional), ['id'], [], false);
        $this->db->update($this->config->table_categories, ['order' => $catdetails['order']], array_merge(['id' => $new['id']], $additional), 1);
        if ($neworder < 1) {
            $neworder = 1;
        }
        return $this->db->update($this->config->table_categories, ['order' => $neworder], array_merge(['id' => $category_id], $additional), 1);
    }
    
    /**
     * Returns the total number of products in a given category
     * @param int $category_id This should be the category ID that you wish to get the number of products for
     * @return int Will return the number of products in the category
     */
    protected function numberOfProductsInCategory($category_id)
    {
        return $this->db->count($this->config->table_product_categories, ['category_id' => intval($category_id)], 86400);
    }
    
    /**
     * Adds an additional key to the where array
     * @param boolean $active If you only want to view the active results set to true else set to false
     * @param array $where Addition where fields
     * @return array
     */
    protected function addWhereIsActive($active = true, $where = [])
    {
        if ($active !== false) {
            $where['active'] = 1;
        }
        return $where;
    }
}
