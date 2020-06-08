<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use Blocking\IPBlock;
use Blocking\BannedWords;
use ShoppingCart\Mailer;
use ShoppingCart\Modifiers\SQLBuilder;
use DateTime;
use DateTimeZone;

class Review{
    protected $db;
    protected $config;
    protected $product;
    
    protected $blocking;
    protected $ip;
    
    protected $count = [];
    
    /**
     * Constructor add an instance of the database class
     * @param Database $db This should be an instance of the database class
     * @param Config $config This sis an instance of the config class
     * @param Object This is an instance of the product class
     */
    public function __construct(Database $db, Config $config, $product) {
        $this->db = $db;
        $this->config = $config;
        $this->product = $product;
        $this->ip = new IPBlock($this->db);
        $this->blocking = new BannedWords($this->db);
    }
    
    /**
     * Select all of the reviews from the database
     * @param int|false $limit If you wish to only display a limited number of testimonials set this to the maximum number to display else set to false
     * @param int $start This should be the start location where to start displaying from (should be a multiple of the $limit integer)
     * @param array $where any parameters you want to search on
     * @return array|false If any reviews exist they should be returned as an array else false will be returned
     */
    public function getReviews($limit = false, $start = 0, $where = []) {
        $extraSQL = SQLBuilder::createAdditionalString($where);
        return $this->db->query("SELECT `reviews`.*, `product`.`name` as `product_name` FROM `{$this->config->table_review}` as `reviews`, `{$this->config->table_products}` as `product` WHERE `reviews`.`product` = `product`.`product_id`".(strlen($extraSQL) >= 1 ? ' AND '.$extraSQL : '')." ORDER BY `date` DESC".($limit !== false ? " LIMIT ".intval($start).", ".intval($limit) : "").";", array_merge((!empty($where) ? array_values($where) : []), SQLBuilder::$values));
    }
    
    /**
     * Count the number of total reviews for all products
     * @param array $where any parameters you want to search on
     * @return int The number of total reviews will be returned
     */
    public function countReviews($where = []) {
        return $this->db->count($this->config->table_review, $where);
    }

    /**
     * Returns all of the reviews for a given product ID
     * @param int $productID This should be the product ID you wish to get the Product reviews for
     * @return string Returns a array containing all of the reviews for the given product
     */
    public function getProductReviews($productID, $start = 0, $limit = 50) {
        return $this->db->selectAll($this->config->table_review, ['approved' => 1, 'product' => $productID], '*', ['date' => 'DESC'], [$start => $limit]);
    }
    
    /**
     * Add a product review into the database
     * @param int $productID This should be the product ID that you are adding the review for
     * @param string $name The user name who is submitting the review
     * @param string $email The email address of the user who is submitting the reviews
     * @param string $title The review title given by the user
     * @param string $review The product review from the user
     * @param int $rating The rating given by the user
     * @param int $type The type of review 1 = review, 2 = comment
     * @return boolean Returns true if review is inserted into database else returns false
     */
    public function addProductReview($productID, $name, $email, $title, $review, $rating = 5, $type = 1) {
        if($this->blocking->containsBlockedWord($review) || $this->blocking->containsBlockedWord($title) || $this->checkForReviewsByIP($this->ip->getUserIP()) >= 1) {$spam = 1;}else{$spam = 0;}
        if(!$this->checkIfCustomerReviewExists($productID, $email) && filter_var($email, FILTER_VALIDATE_EMAIL)) {
            if($this->db->insert($this->config->table_review, ['product' => $productID, 'type' => $type, 'rating' => $rating, 'name' => $name, 'email' => $email, 'title' => $title, 'review' => strip_tags($review, '<img>'), 'ipaddress' => $this->ip->getUserIP(), 'spam' => $spam])) {
                return $this->sendReviewEmail($productID);
            }
        }
        return false;
    }
    
    /**
     * Send an email to alert admin the review has been submitted
     * @return boolean Returns true if email successfully sent else returns false
     */
    protected function sendReviewEmail($productID) {
        if($this->config->send_review_email === 'true') {
            $productInfo = $this->product->getProductByID($productID);
            return Mailer::sendEmail(
                $this->config->email_office_address,
                sprintf($this->config->email_review_subject, $productInfo['name']),
                sprintf($this->config->email_review_altbody, $productInfo['name'], $this->config->site_url, $this->ip->getUserIP()),
                Mailer::htmlWrapper($this->config, sprintf($this->config->email_review_body, $productInfo['name'], $this->config->site_url, $this->ip->getUserIP()), $this->config->email_review_subject),
                $this->config->email_from_address,
                $this->config->email_from_name
            );
        }
        return true;
    }
    
    /**
     * Retrieve the review information for a specific review
     * @param int $reviewID This should be the unique review id
     * @param array $additionalInfo Any additional information to limit the query
     * @return array|boolean Returns the review information if it exists else returns false
     */
    public function getReviewInfo($reviewID, $additionalInfo = []) {
        if(is_numeric($reviewID)){
            return $this->db->select($this->config->table_review, array_merge(['review_id' => $reviewID], $additionalInfo));
        }
        return false;
    }
    
    /**
     * Updates the review information
     * @param int $reviewID This should be the unique review id
     * @param array $additionalInfo Any additional information to limit the query
     * @return boolean Returns true on success of false on failure
     */
    public function updateProductReview($reviewID, $additionalInfo = []) {
        foreach($additionalInfo as $field => $value) {
            $additionalInfo[$field] = strip_tags($value, '<img>');
        }
        return $this->db->update($this->config->table_review, $additionalInfo, ['review_id' => $reviewID], 1);
    }
    
    /**
     * Delete a review from the database
     * @param inst $reviewID This should be the unique ID given to the review that you wish to delete
     * @return boolean If the review is successfully delete will return true else returns false
     */
    public function deleteProductReview($reviewID) {
        return $this->db->delete($this->config->table_review, ['review_id' => $reviewID], 1);
    }
    
    /**
     * Changes the status of a review to either publish or un-publish a review
     * @param int $reviewID This should be the unique review ID assigned in the database
     * @param int $status The new status that you wish to give to the review to publish set to 1 else set to 0
     * @return boolean If the review is successfully updated will return true else returns false
     */
    public function changeReviewStatus($reviewID, $status = 1) {
        if($this->db->update($this->config->table_review, ['approved' => $status], ['review_id' => $reviewID], 1)){
            $this->updateProductReviewInfo($this->getReviewInfo($reviewID)['product']);
            return true;
        }
        return false; 
    }
    
    /**
     * Update the number of reviews and rating for each product
     * @param int $productID This should be the product ID you are updating number of reviews for
     * @return boolean If successfully update will return true else returns false
     */
    public function updateProductReviewInfo($productID) {
        if(is_numeric($productID)){
            return $this->db->update($this->config->table_products, ['num_reviews' => $this->countProductReviews($productID), 'review_rating' => $this->getProductReviews($productID)]);
        }
        return false;
    }
    
    /**
     * Get the number of approved reviews for a given product
     * @param int $productID The product ID you wish to get the number of product reviews for
     * @return int Returns the number of reviews for the given product ID
     */
    public function countProductReviews($productID) {
        if(isset($this->count[$productID])){
            return intval($this->count[$productID]);
        }
        $this->count[$productID] = $this->db->count($this->config->table_review, ['approved' => 1, 'product' => $productID]);
        return intval($this->count[$productID]);
    }
    
    /**
     * Gets The product rating value out of 5
     * @param int $productID This should be the product ID you wish to get the rating of
     * @return string Returns the product rating out of 5
     */
    protected function getProductRating($productID) {
        $total = 0;
        foreach($this->db->selectAll($this->config->table_review, ['approved' => 1, 'product' => $productID]) as $review) {
            $total = $total + $review['rating'];
        }
        return number_format(($total / $this->countProductReviews($productID)), 1, '.', '');
    }
    
    /**
     * If ratings have been submitted to the given product ID this will return a summary of the ratings
     * @param int $productID This should be the product ID you wish to generate the Rating string for
     * @return string Returns the product review info in a formatted string 
     */
    public function productReviewInfo($productID) {
        if($this->countProductReviews($productID)) {
            $reviewInfo = [];
            $reviewInfo['numReviews'] = $this->countProductReviews($productID);
            $reviewInfo['rating'] = str_replace('.0', '', $this->getProductRating($productID));
            return $reviewInfo;
        }
        return false;
    }
    
    /**
     * Checks to see a if a customer review already exists for the given product
     * @param int $productID This should be the product ID of the product the review is for
     * @param string $email This should be the customers email address
     * @return int If review already exists for this user will return 1 else returns 0
     */
    protected function checkIfCustomerReviewExists($productID, $email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL) && is_numeric($productID)) {
            return $this->db->count($this->config->table_review, ['product' => $productID, 'email' => $email]);
        }
        return 0;
    }
    
    /**
     * Checks to see if any other reviews have been submitted by the same IP in the last 24 hours
     * @param string $ip This should be the IP address of the person who submitted the review
     * @return int Returns >= 1 if any reviews have been submitted by the same IP in the last 24 hours else will return 0
     */
    protected function checkForReviewsByIP($ip){
        if($ip){
            $datetime = new DateTime();
            $datetime->setTimezone(new DateTimeZone($this->config->timezone));
            $datetime->modify('-24 hours');
            return $this->db->count($this->config->table_review, ['ipaddress' => $ip, 'timestamp' => ['>=', $datetime->format('Y-m-d H:i:s')]]);
        }
        return 0;
    }
}
