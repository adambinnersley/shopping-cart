<?php

namespace ShoppingCart;

use DBAL\Database;
use DBAL\Modifiers\Modifier;
use Configuration\Config;
use ImgUpload\ImageUpload;
use ShoppingCart\Modifiers\Cost;

class Product extends Category
{
    protected $review;
    protected $gallery;
    
    protected $imageUpload;
    protected $decimals;

    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be an instance of the config class
     * @param string $imageFolder This should be the location of the image folder
     * @param string|false $rootFolder This should be the document root folder
     */
    public function __construct(Database $db, Config $config, $imageFolder = '/images/products/', $rootFolder = false)
    {
        parent::__construct($db, $config);
        $this->review = new Review($db, $config, $this);
        $this->gallery = new Gallery($db, $config);
        $this->decimals = Currency::getCurrencyDecimals($this->config->currency);
        $this->imageUpload = new ImageUpload();
        $this->imageUpload->setRootFolder(is_string($rootFolder) && file_exists($rootFolder) ? $rootFolder : getcwd())
            ->setImageFolder($imageFolder)
            ->setThumbFolder('thumbs/');
        $this->imageUpload->createThumb = true;
    }
    
    /**
     * Returns an array of active products
     * @param boolean $active If you only want to retrieve active products set this to true else for all products should be false
     * @param int $start The start location for the records in the database used for pagination
     * @param int $limit The maximum number of results to return in the array
     * @param array $where Addition where fields
     * @return array|false If any products exists they will be returned as an array else will return false
     */
    public function listProducts($active = true, $start = 0, $limit = 50, $where = [])
    {
        return $this->db->selectAll($this->config->table_products, $this->addWhereIsActive($active, $where), '*', [], [$start => $limit], 86400);
    }
    
    /**
     * Count the total number of products in the database
     * @param boolean $active If you only wish to count the number of active products set this value to true else for all products set to false
     * @param array $where Addition where fields
     * @return int The total number of products in the database will be returned
     */
    public function countProducts($active = true, $where = [])
    {
        return $this->db->count($this->config->table_products, $this->addWhereIsActive($active, $where), 86400);
    }
    
    /**
     * Add a product to the database
     * @param string $name This should be the name of the product that you are adding
     * @param string $code Give the product a unique code of SKU to identify it
     * @param string $description Add a description to the product what is shown in the store
     * @param int|float $price This should be the RRP or price that you are charging for this product
     * @param int|array $category The category ID where this product is located
     * @param int $tax_id The Tax ID of the Tax band that is item has
     * @param int $active If the product should be set as active set to 1 else set to 0
     * @param array|false $image This should be the image to be associated with the product
     * @param array $additionalInfo Any additional information should be included as array items
     * @return boolean If the product is added successfully will return true else will return false
     */
    public function addProduct($name, $code, $description, $price, $category, $tax_id, $active = 1, $image = false, $additionalInfo = [])
    {
        if (!$this->getProductByCode($code, false)) {
            $additionalInfo['weight'] = number_format($additionalInfo['weight'], 3);
            $additionalInfo['sale_price'] = Modifier::setNullOnEmpty($additionalInfo['sale_price']);
            $additionalInfo['features'] = Modifier::setNullOnEmpty($additionalInfo['features']);
            $additionalInfo['requirements'] = Modifier::setNullOnEmpty($additionalInfo['requirements']);
            $additionalInfo['digital'] = Modifier::setZeroOnEmpty($additionalInfo['digital']);
            $additionalInfo['homepage'] = Modifier::setZeroOnEmpty($additionalInfo['homepage']);
            $insert = $this->db->insert($this->config->table_products, array_merge(['active' => intval($active), 'code' => $code, 'name' => $name, 'description' => Modifier::setNullOnEmpty($description), 'price' => Cost::priceUnits($price, $this->decimals), 'tax_id' => intval($tax_id)], $additionalInfo, $this->addImage($image)));
            $this->addProductToCategory($category, $this->db->lastInsertId());
            return ($insert ? true : false);
        }
        return false;
    }
    
    /**
     * Edit a product in the database
     * @param type $product_id This should be the unique product ID you are updating
     * @param array|false $image This should be the image to be associated with the product
     * @param array $additionalInfo Any additional information you are updating should be set as an array here
     * @return boolean If the information has successfully been updated will return true else returns false
     */
    public function editProduct($product_id, $image = false, $additionalInfo = [])
    {
        if (is_numeric($product_id) && Modifier::arrayMustContainFields(['weight', 'description', 'price'], $additionalInfo)) {
            $additionalInfo['weight'] = number_format($additionalInfo['weight'], 3);
            $additionalInfo['description'] = Modifier::setNullOnEmpty($additionalInfo['description']);
            $additionalInfo['sale_price'] = Modifier::setNullOnEmpty($additionalInfo['sale_price']);
            $additionalInfo['features'] = Modifier::setNullOnEmpty($additionalInfo['features']);
            $additionalInfo['requirements'] = Modifier::setNullOnEmpty($additionalInfo['requirements']);
            $additionalInfo['digital'] = Modifier::setZeroOnEmpty($additionalInfo['digital']);
            $additionalInfo['homepage'] = Modifier::setZeroOnEmpty($additionalInfo['homepage']);
            $additionalInfo['active'] = Modifier::setZeroOnEmpty($additionalInfo['active']);
            $this->updateProductCategory($additionalInfo['category'], $product_id);
            unset($additionalInfo['category']);
            return $this->db->update($this->config->table_products, array_merge($additionalInfo, $this->addImage($image)), ['product_id' => $product_id], 1);
        }
        return false;
    }
    
    /**
     * Adds an image for the product to the server and returns the variables for the database
     * @param array|false $image This should be the image information array
     * @return array An array will be returned
     */
    protected function addImage($image)
    {
        $additionalInfo = [];
        if (is_array($image) && $this->imageUpload->uploadImage($image)) {
            $additionalInfo['image'] = '/' . trim($this->imageUpload->getImageFolder(), '\/') . '/' . $image['name'];
            list($width, $height) = getimagesize($this->imageUpload->getRootFolder() . $this->imageUpload->getImageFolder() . $image['name']);
            $additionalInfo['width'] = $width;
            $additionalInfo['height'] = $height;
        }
        return $additionalInfo;
    }
    
    /**
     * Delete a product from the database
     * @param int $product_id This should be the unique product ID assigned in the database of the product you wish to delete
     * @param array $where Addition where fields
     * @return boolean If the product is successfully removed will return true else returns false
     */
    public function deleteProduct($product_id, array $where = [])
    {
        return $this->db->delete($this->config->table_products, array_merge(['product_id' => $product_id], $where));
    }
    
    /**
     * Returns an array of the categories that a product should be listed in
     * @param int $productID This should be the product id
     * @return array|false If any values exists an array will be returned else if no values exists false will be returned
     */
    public function listProductCategories($productID)
    {
        $getCategories = $this->db->selectAll($this->config->table_product_categories, ['product_id' => $productID], ['category_id'], [], 0, 86400);
        if (is_array($getCategories)) {
            $categories = [];
            foreach ($getCategories as $category) {
                $categories[] = $category['category_id'];
            }
            return $categories;
        }
        return false;
    }
    
    /**
     * Add a product to a category or categories
     * @param int|array $categories This should be an array of all of the categories that a product should be listed under
     * @param int $productID This should be the products ID that you are adding the category to
     * @param int $main If it is the main category set to 1 else set to 0
     * @return boolean Returns true on success else return false
     */
    protected function addProductToCategory($categories, $productID, $main = 1)
    {
        if (is_numeric($categories) && is_numeric($productID)) {
            return $this->db->insert($this->config->table_product_categories, ['product_id' => $productID, 'category_id' => $categories, 'main_category' => intval($main)]);
        } elseif (is_array($categories) && is_numeric($productID)) {
            foreach ($categories as $i => $category_id) {
                $this->addProductToCategory($category_id, $productID, ($i == 0 && $main == 1 ? 1 : 0));
            }
            return true;
        }
        return false;
    }
    
    /**
     * Updates the product categories
     * @param int|array $categories This should either be the category id or the array of categories
     * @param int $productID This should be the product ID
     * @return boolean Returns true if no changes or updated successfully
     */
    protected function updateProductCategory($categories, $productID)
    {
        if (is_numeric($categories) && is_numeric($productID)) {
            return $this->updateProductCategory([$categories], $productID);
        } elseif (is_array($categories)) {
            $currentCategories = $this->listProductCategories($productID);
            $add = array_diff_assoc($categories, $currentCategories);
            if (!empty($add)) {
                $this->addProductToCategory($add, $productID, 0);
            }
            $remove = array_diff_assoc($currentCategories, $categories);
            if (!empty($remove)) {
                $this->deleteProductFromCategory($remove, $productID);
            }
            return $this->setMainProductCategory($categories[0], $productID);
        }
        return true;
    }
    
    /**
     * Deletes a product from a category
     * @param int|array $category This should be that category you are removing the product from
     * @param int $productID This should be the product ID
     * @return boolean If the product has been successfully removed will return true else return false
     */
    protected function deleteProductFromCategory($category, $productID)
    {
        if (is_numeric($category)) {
            return $this->db->delete($this->config->table_product_categories, ['product_id' => $productID, 'category_id' => $category], 1);
        } else {
            foreach ($category as $category_id) {
                $this->deleteProductFromCategory($category_id, $productID);
            }
            return true;
        }
        return false;
    }
    
    /**
     * Sets the main product category id
     * @param int $categoryID This should be the main category ID
     * @param int $productID This should be the product ID
     * @return boolean If successfully updated will return true else returns false
     */
    protected function setMainProductCategory($categoryID, $productID)
    {
        $this->db->update($this->config->table_product_categories, ['main_category' => 0], ['product_id' => $productID, 'category_id' => ['!=', $categoryID]]);
        return $this->db->update($this->config->table_product_categories, ['main_category' => 0], ['product_id' => $productID, 'category_id' => $categoryID]);
    }

    /**
     * Returns the product information based on the given parameters
     * @param array $where This should be the where parameters you wish to search the database for
     * @param boolean $active If you only wish to display active products set this to true else set to false for all products
     * @return array|false If a product exists will return the single products information as an array else will return false
     */
    protected function getProduct($where, $active = true)
    {
        if (is_array($where)) {
            $productInfo = $this->db->select($this->config->table_products, $this->addWhereIsActive($active, $where), '*', [], 86400);
            if (is_array($productInfo)) {
                $productInfo['related'] = $this->getRelatedProducts(is_null($productInfo['related']) ? false : unserialize($productInfo['related']));
                $productInfo['category_url'] = $this->getPrimaryCategoryURL($productInfo['product_id']);
                $productInfo['gallery_images'] = ($productInfo['noimages'] >= 1 ? $this->gallery->getProductImages($productInfo['product_id']) : false);
                if ($productInfo['num_reviews'] > 0) {
                    $productInfo['reviews'] = $this->review->getProductReviews($productInfo['product_id']);
                    $productInfo['reviewInfo'] = [
                        'numReviews' => $productInfo['num_reviews'],
                        'rating' => $productInfo['review_rating']
                    ];
                }
                return $productInfo;
            }
        }
        return false;
    }
    
    /**
     * Returns the product information for a given product ID
     * @param int $product_id This should be the products unique ID
     * @param boolean $active If you only wish to get the product if it is active set to true else for all products set to false
     * @param array $where Addition where fields
     * @return array|false If a product exists will return the single products information as an array else will return false
     */
    public function getProductByID($product_id, $active = true, $where = [])
    {
        return $this->getProduct(array_merge(['product_id' => $product_id], $where), $active);
    }
    
    /**
     * Retrieves product information based on a given unique product code
     * @param string $product_code This should be the unique product SKU code
     * @param boolean $active If you only wish to get the product if it is active set to true else for all products set to false
     * @param array $where Addition where fields
     * @return array|false If a product exists will return the single products information as an array else will return false
     */
    public function getProductByCode($product_code, $active = true, $where = [])
    {
        return $this->getProduct(array_merge(['code' => $product_code], $where), $active);
    }

    /**
     * Retrieves product information based on a given unique product URL
     * @param string $product_url This should be the unique product URL
     * @param boolean $active If you only wish to get the product if it is active set to true else for all products set to false
     * @param array $where Addition where fields
     * @return array|false If a product exists will return the single products information as an array else will return false
     */
    public function getProductByURL($product_url, $active = true, $where = [])
    {
        return $this->getProduct(array_merge(['custom_url' => $product_url], $where), $active);
    }
    
    /**
     * Build all of the product information needed to display the product on its product page
     * @param string $url This should be the unique product URL
     * @param array $where Addition where fields
     * @return array|boolean If the product information has been retrieved from the URL will return an array of information else will return false
     */
    public function buildProduct($url, $where = [])
    {
        return $this->getProductByURL($url, true, $where);
    }
    
    /**
     * Checks to see if the product is a download item
     * @param int $product_id the product ID of the item you are checking if the item is a download item
     * @return boolean If the item is a download item will return true else returns false
     */
    public function isProductDownload($product_id)
    {
        $productInfo = $this->getProductByID($product_id);
        if ($productInfo['digital']) {
            return true;
        }
        return false;
    }
    
    /**
     * Returns the price f the given product
     * @param int $product_id This should be the product ID of the item you are getting the price for
     * @return string Will return the current price for the item and whether it is the sale price or normal price
     */
    public function getProductPrice($product_id)
    {
        $productInfo = $this->getProductByID($product_id);
        if (is_numeric($productInfo['sale_price'])) {
            return Cost::priceUnits($productInfo['sale_price'], $this->decimals);
        }
        return Cost::priceUnits($productInfo['price'], $this->decimals);
    }
    
    /**
     * Returns the weight for a given product
     * @param int $product_id This should be the product ID of the item you are getting the price for
     * @return int Will return the weight for the item
     */
    public function getProductWeight($product_id)
    {
        $productInfo = $this->getProductByID($product_id);
        return $productInfo['weight'];
    }
    
    /**
     * Returns the most popular products
     * @param int $limit This should be the number of results to display
     * @param string $findBy This should be how you wish to generate the popular items can either be 'sales' or 'views'
     * @param array $where Addition where fields
     * @return array|false If items exist an array will be returned else will return false
     */
    public function getPopularProducts($limit = 5, $findBy = 'sales', $where = [])
    {
        $products = $this->db->selectAll($this->config->table_products, $where, '*', [$findBy => 'DESC'], intval($limit), 86400);
        foreach ($products as $i => $product) {
            $products[$i] = $this->getProductByID($product['product_id']);
        }
        return $products;
    }
    
    /**
     * Gets a list of related items to the given product
     * @param array|null $items An array of the associated items
     * @return array|false If any related products exists they will be returned as an array if none exist false will be returned
     */
    protected function getRelatedProducts($items)
    {
        if (is_array($items)) {
            $related = [];
            foreach ($items as $i => $product) {
                $productInfo = $this->db->select($this->config->table_products, ['product_id' => $product, 'active' => 1]);
                if($productInfo) {
                    $related[$i] = $productInfo;
                    $related[$i]['url'] = '/store/' . $this->getPrimaryCategoryURL($productInfo['product_id']) . '/' . $productInfo['custom_url'];
                    $related[$i]['image'] = removeImageExtension($productInfo['image']);
                }
            }
            return $related;
        }
        return false;
    }
    
    /**
     * Returns the first category URL
     * @param int $product_id This should be the product ID that you are getting the main category for
     * @param int $main_category For initial search leave as 1 for the main category if not found will change to search for any category
     * @return string|false If The category exists the primary category URL will be returned false if no categories are assigned
     */
    protected function getPrimaryCategoryURL($product_id, $main_category = 1)
    {
        $category = $this->db->fetchColumn($this->config->table_product_categories, ['product_id' => $product_id, 'main_category' => $main_category], ['category_id']);
        if (is_numeric($category)) {
            return $this->getCategoryURL($category);
        } elseif ($main_category === 1) {
            return $this->getPrimaryCategoryURL($product_id, 0);
        }
        return false;
    }
    
    /**
     * Get all of the products in a given category based on the given parameters
     * @param int $category_id This should be the category ID that you are getting all the products within
     * @param string $orderBy How the products should be ordered can be on fields such as `sales`, `price`, `views`
     * @param string $orderDir The direction it should be ordered ASC OR DESC
     * @param int $limit The maximum number of results to show
     * @param int $start The start location for the database results (Used for pagination)
     * @param boolean $activeOnly If you only want to display active product this should be set to true else should be set to false
     * @return array|false Returns an array containing the products in a given category if any exist else will return false if none exist
     */
    public function getProductsInCategory($category_id, $orderBy = 'sales', $orderDir = 'DESC', $limit = 20, $start = 0, $activeOnly = true)
    {
        return $this->buildProductArray(
            $this->db->query("SELECT `products`.* FROM `{$this->config->table_products}` as `products`, `{$this->config->table_product_categories}` as `category` WHERE " . ($activeOnly === true ? "`products`.`active` = 1 AND " : "") . "`products`.`product_id` = `category`.`product_id` AND `category`.`category_id` = ? ORDER BY `{$orderBy}` {$orderDir}" . ($limit > 0 ? " LIMIT {$start}, {$limit}" : "") . ";", [$category_id], 86400)
        );
    }
    
    /**
     * Returns the products that should be featured on the homepage
     * @param string $orderBy How the products should be ordered can be on fields such as `sales`, `price`, `views`
     * @param string $orderDir The direction it should be ordered ASC OR DESC
     * @param int $limit The maximum number of results to show
     * @param int $start The start location for the database results (Used for pagination)
     * @param array $additionalInfo Any additional fields to add to the query
     * @return array|false Returns an array containing the products in a given category if any exist else will return false if none exist
     */
    public function getHomepageProducts($orderBy = 'sales', $orderDir = 'DESC', $limit = 20, $start = 0, array $additionalInfo = [])
    {
        return $this->buildProductArray(
            $this->db->selectAll($this->config->table_products, array_merge(['homepage' => 1, 'active' => 1], $additionalInfo), '*', [$orderBy => $orderDir], ($limit > 0 ? [$start => $limit] : 0), 86400)
        );
    }
    
    /**
     * Builds the array of products
     * @param array|false $products This should be an array of items if they exists else should be false
     * @return array|boolean Returns the array of items
     */
    protected function buildProductArray($products)
    {
        if (is_array($products) && !empty($products)) {
            foreach ($products as $i => $product) {
                $products[$i] = $this->buildProduct($product['custom_url']);
            }
            return $products;
        }
        return false;
    }
    
    /**
     * Counts the number of items in a category
     * @param int $category_id This should be the category ID that you want to count all the products within
     * @param boolean $activeOnly If you only want to display active product this should be set to true else should be set to false
     * @return int Returns the number of items in a category
     */
    public function countProductsInCategory($category_id, $activeOnly = true)
    {
        return count($this->getProductsInCategory($category_id, 'sales', 'DESC', 0, 0, $activeOnly));
    }
}
