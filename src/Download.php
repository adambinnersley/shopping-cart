<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use DateTime;

class Download
{
    protected $db;
    protected $order;
    protected $product;
    protected $serials;
    public $config;
    protected $dateTime;
    
    protected $serialFormat = '<br />Serial<br />%s';

    /**
     * Passes the required objects to the class
     * @param Database $db This should be an instance of the database class
     * @param Config $config This should be the Shopping cart config class
     * @param Order $basket This should be an instance of the basket class
     */
    public function __construct(Database $db, Config $config, $basket, $product)
    {
        $this->db = $db;
        $this->config = $config;
        $this->product = $product;
        $this->order = $basket;
        $this->serials = new Serial($this->db, $this->config);
    }
    
    /**
     * Creates a unique link to hide the original download location and track link usage
     * @param string $order_id This should be the unique order number
     * @param int $product_id This should be the product ID that you are creating a unique link for
     * @return string This is the unique string that will be generated
     */
    protected function createUniqueLink($order_id, $product_id)
    {
        $date = new DateTime();
        return hash('sha256', $order_id.$product_id.$date->format('Y-m-d H:i:s'));
    }
    
    /**
     * Add download link to the database if download items exist in the shopping cart
     * @param int $customer_id This should be the users unique ID
     * @param string $order_id This should be the order_no
     * @param array $products This should be an array of products that the user has ordered in order to create the download links for them
     * @param string $email This should be the customers email address
     * @return boolean If the information has successfully been inserted will return true else will return false
     */
    public function addDownloadLink($customer_id, $order_id, $products, $email)
    {
        if (is_array($products)) {
            $date = new DateTime();
            $date->modify("+{$this->config->download_link_expiry}");
            $productClass = (is_object($this->product) ? $this->product : new Product($this->db, $this->config));
            foreach ($products as $product_id => $quantity) {
                if ($productClass->isProductDownload($product_id)) {
                    $this->db->insert($this->config->table_downloads, ['customer_id' => $customer_id, 'order_id' => $order_id, 'product' => $product_id, 'expire' => $date->format('Y-m-d H:i:s'), 'link' => $this->createUniqueLink($order_id, $product_id)]);
                    $this->addDownloadSerials($product_id, $quantity, $order_id, $email);
                }
            }
            return true;
        }
        return false;
    }

    /**
     * Gets the download information for a given product and order so it can be retrieved in order history or the admin
     * @param int $product_id This should be the product ID
     * @param string $order_id This should be the order number
     * @return array|false If a link exists will return an array else will return false
     */
    public function getDownloadInformation($product_id, $order_id)
    {
        return $this->db->select($this->config->table_downloads, ['order_id' => $order_id, 'product' => $product_id]);
    }
    
    /**
     * Reset the number of attempts a user has taken at downloading an item
     * @param string $linkID This should be the unique link ID
     * @return boolean If the number of attempts has successfully been updated will return true else returns false
     */
    public function resetDownloadAttempts($linkID)
    {
        return $this->db->update($this->config->table_downloads, ['attempts' => 0], ['dlid' => $linkID], 1);
    }
    
    /**
     * Checks he given array of products to see if any of the product are download and if they are will return the HTML links
     * @param array $products This should be the products array as in the basket
     * @param int $order_id This should be the order that the link is associated with
     * @return string The HTML formatted link will be returned
     */
    protected function formatDownloadLinks($products, $order_id)
    {
        if (is_array($products)) {
            $downloadInfo = '';
            foreach ($products as $product) {
                if ($this->order->product->isProductDownload($product['product_id']) === true) {
                    $downloadInfo.= $this->formatProductLink($product, $order_id);
                }
            }
            return $downloadInfo;
        }
        return false;
    }
    
    /**
     * Returns a HTML formatted link
     * @param array $productInfo This should be the product information as an array
     * @param int $order_id This should be the order that the link is associated with
     * @return string The HTML formatted link will be displayed
     */
    protected function formatProductLink($productInfo, $order_id)
    {
        $serials = array_column($this->getDownloadSerials($productInfo['product_id'], $order_id), 'serial');
        return sprintf($this->config->download_product_format, $productInfo['name'], $this->getDownloadLink($productInfo['product_id'], $order_id), (is_array($serials) ? sprintf($this->config->download_product_serial_format, implode('<br />', $serials)) : ''));
    }
    
    /**
     * Adds a serial number to the database for each item purchased if required
     * @param int $product_id This should be the product id number
     * @param int $quantity The quantity of items the customer has purchased of the given product
     * @param int $order_id The order number to associate the serials with
     * @param string $email This should be the customers email address
     */
    public function addDownloadSerials($product_id, $quantity, $order_id, $email)
    {
        if ($this->config->download_require_serials) {
            for ($i = 1; $i <= $quantity; $i++) {
                $this->serials->addSerial($order_id, $email, $product_id);
            }
        }
    }
    
    /**
     * Retrieves the serial numbers for a given product and order if required
     * @param int $product_id This should be the product ID that you are retrieving the serials for
     * @param int $order_id This should be the order id
     * @return array|boolean If serials are available will return an array of the serials else returns false
     */
    public function getDownloadSerials($product_id, $order_id)
    {
        if ($this->config->download_require_serials) {
            return $this->serials->getSerials($order_id, $product_id);
        }
        return false;
    }
    
    /**
     * Returns the full URL for the download
     * @param int $product_id This should be the product ID that you are getting the URL for
     * @param string $order_id This should be the order that the link is associated with
     * @return string Will return the URL to enable the downloads to be tracked and downloaded
     */
    public function getDownloadLink($product_id, $order_id)
    {
        $uniqueLink = $this->getDownloadInformation($product_id, $order_id);
        return sprintf($this->config->download_link, $this->config->site_url, $product_id, $uniqueLink['link']);
    }
    
    /**
     * Sens the email with all of the download link information to the customer
     * @param string $order_id This should be the order id that you wish to send the details for
     * @return boolean If the email has been sent will return true else returns false
     */
    public function sendDownloadLink($order_id)
    {
        $orderInfo = $this->order->getOrderByOrderNo($order_id);
        $date = new DateTime();
        $date->modify("+{$this->config->download_link_expiry}");
        $subject = sprintf($this->config->email_download_subject, $order_id);
        return Mailer::sendEmail(
            $orderInfo['user']['email'],
            $subject,
            sprintf($this->config->email_download_altbody, $orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], date('jS F Y g:ia', strtotime($orderInfo['payment_date'] != null ? $orderInfo['payment_date'] : 'now')), $date->format('jS F Y'), $this->config->download_attempts, $this->formatDownloadLinks($orderInfo['products'], $orderInfo['order_id']), $this->config->site_name),
            Mailer::htmlWrapper($this->config, sprintf($this->config->email_download_body, $orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], date('jS F Y g:ia', strtotime($orderInfo['payment_date'] != null ? $orderInfo['payment_date'] : 'now')), $date->format('jS F Y'), $this->config->download_attempts, $this->formatDownloadLinks($orderInfo['products'], $orderInfo['order_id']), $this->config->site_name), $subject),
            $this->config->email_from_address,
            $this->config->email_from_name
        );
    }
    
    /**
     * Download an item from a given link
     * @param string $link This should be the unique link variable given by the user from the download link
     * @return false If the download link doesn't exist will return false but should be redirected to the download if it does exist
     */
    public function downloadItem($link)
    {
        $date = new DateTime();
        $linkInfo = $this->db->select($this->config->table_downloads, ['link' => $link, 'attempts' => ['<=', intval($this->config->download_attempts)], 'expire' => ['>=', $date->format('Y-m-d H:i:s')]]);
        if (is_array($linkInfo)) {
            $this->db->update($this->config->table_downloads, ['attempts' => intval($linkInfo['attempts'] + 1)], ['dlid' => $linkInfo['dlid']], 1);
            $productInfo = $this->order->product->getProductByID($linkInfo['product']);
            redirect($productInfo['digitalloc']);
        }
        return false;
    }
}
