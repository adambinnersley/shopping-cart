<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ImgUpload\ImageUpload;

class Gallery
{
    protected $db;
    protected $upload;
    public $config;
    
    /**
     * Constructor
     * @param Database $db This should be an instance of the database class
     */
    public function __construct(Database $db, Config $config)
    {
        $this->db = $db;
        $this->config = $config;
        $this->upload = new ImageUpload();
        $this->upload->thumbWidth = $this->config->gallery_thumb_width;
        $this->upload->setRootFolder(getcwd() . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR)
                     ->setImageFolder($this->config->gallery_image_folder)
                     ->setThumbFolder($this->config->gallery_thumbs_folder);
    }
    
    /**
     * Set the image location where the gallery images can be found
     * @param string $path This should be the path where the gallery folder can be located
     * @return $this
     */
    public function setImageLocation($path)
    {
        $this->config->gallery_image_folder = $path;
        $this->upload->setImageFolder($this->config->gallery_image_folder);
        return $this;
    }
    
    /**
     * Sets the location where the thumbnails for the gallery images can be located
     * @param string $path This should be the path where the gallery thumbnails folder can be located
     * @return $this
     */
    public function setThumbLocation($path)
    {
        $this->config->gallery_thumbs_folder = $path;
        $this->upload->setThumbFolder($this->config->gallery_thumbs_folder);
        return $this;
    }
    
    /**
     * Sets the maximum width of the of the thumbnails
     * @param int $width This should be the maximum number of pixels that the thumbnail should be set to
     * @return $this
     */
    public function setMaxThumbWidth($width)
    {
        if (is_numeric($width)) {
            $this->config->gallery_thumb_width = intval($width);
            $this->upload->thumbWidth = $this->config->gallery_thumb_width;
        }
        return $this;
    }
    
    /**
     * List all of the images in the gallery
     * @param array $where Parameters to search on
     * @return array|false Lists all of the images within the gallery database
     */
    public function listImages($where = [])
    {
        return $this->db->selectAll($this->config->table_gallery, $where, '*', [], 0, 86400);
    }
    
    /**
     * Gets gallery images that have a given product assigned
     * @param int $product_id This should be the product ID that you are searching for gallery images for
     * @return array|false If any gallery images exist for the product will return an array else will return false
     */
    public function getProductImages($product_id)
    {
        $images = $this->db->query("SELECT `gallery`.* FROM `{$this->config->table_gallery}` as `gallery`, `{$this->config->table_product_images}` as `ref` WHERE `gallery`.`img_id` = `ref`.`image_id` AND `ref`.`product_id` = ?;", [$product_id], 86400);
        if (!empty($images)) {
            return $images;
        }
        return false;
    }
    
    /**
     * Returns the number of gallery images linked with a given product
     * @param int $product_id This should be the unique product id that you want t look for gallery images linked to it
     * @return int Th total number of gallery images linked to the product will be returned
     */
    public function numProductImages($product_id)
    {
        return $this->db->count($this->config->table_product_images, ['product_id' => $product_id], 86400);
    }
    
    /**
     * Returns image information for a particular gallery image
     * @param int $image_id This should be the unique image id assigned in the database
     * @param array $where Additional values to search on
     * @return array|false If the image exists the information will be returned as an array else will return false
     */
    public function getImageInfo($image_id, $where = [])
    {
        return $this->db->select($this->config->table_gallery, array_merge(['img_id' => intval($image_id)], $where), '*', [], 86400);
    }
    
    /**
     * Gets the gallery image information from the unique name
     * @param string $name The name of the image
     * @return array|false If the image exists it will return an array else will return false
     */
    public function getImageInfoByName($name)
    {
        return $this->db->select($this->config->table_gallery, ['image' => $name]);
    }
    
    /**
     * Uploads an image or multiple image to the gallery
     * @param int $product_id This should be the product ID that you are uploading gallery images for
     * @param file $images This should be the submitted $_FILES information
     * @return boolean If the images are inserted will return true else return false
     * @codeCoverageIgnore
     */
    public function uploadGalleryImages($product_id, $images)
    {
        if ($images['name']) {
            foreach ($images as $image) {
                if (self::$upload->uploadImage($image)) {
                    $this->insertGalleryImage($image['name']);
                    $this->assignProductToImage($this->db->lastInsertId(), $product_id);
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Insert a gallery image into the database
     * @param string $name This should be the name of the image with the extension
     * @return boolean If successfully inserted will return true else will return false
     */
    public function insertGalleryImage($name)
    {
        if (!$this->getImageInfoByName($name)) {
            return $this->db->insert($this->config->table_gallery, ['image' => $name]);
        }
        return false;
    }
    
    /**
     * Add multiple products to a single image
     * @param int $image_id This should be the image ID
     * @param int $product_id This should be the ID of the product you want to display this image for
     * @return boolean If the product has been added will return true else will return false
     */
    public function assignProductToImage($image_id, $product_id)
    {
        if (is_numeric($product_id) && $this->getImageInfo($image_id)) {
            return $this->db->insert($this->config->table_product_images, ['product_id' => $product_id, 'image_id' => $image_id]);
        }
        return false;
    }
    
    /**
     * Remove a particular product from the image to stop it displaying in the gallery
     * @param int $image_id This should be the image ID
     * @param int $product_id This should be the ID of the product you want to display this image for
     * @return boolean If the product has been removed from the image will return true else will return false
     */
    public function removeProductFromImage($image_id, $product_id)
    {
        return $this->db->delete($this->config->table_product_images, ['product_id' => $product_id, 'image_id' => $image_id]);
    }
    
    /**
     * Deletes a given gallery item an removes the images from the server
     * @param int $image_id This should be the unique image ID of the gallery item you want to delete
     * @param array $where Additional parameters as an array
     * @return boolean If the item is successfully removed will return true else returns false
     */
    public function deleteImage($image_id, $where = [])
    {
        $imageInfo = $this->getImageInfo($image_id, $where);
        if (!empty($imageInfo)) {
            if (file_exists($this->config->gallery_image_folder . $imageInfo['image'])) {
                unlink($this->config->gallery_image_folder . $imageInfo['image']);
            }
            if (file_exists($this->config->gallery_thumbs_folder . $imageInfo['image'])) {
                unlink($this->config->gallery_thumbs_folder . $imageInfo['image']);
            }
            return $this->db->delete($this->config->table_gallery, array_merge(['img_id' => intval($image_id)], $where));
        }
        return false;
    }
}
