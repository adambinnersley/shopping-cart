<?php

namespace ShoppingCart;

use DBAL\Database;
use Configuration\Config;
use ShoppingCart\Currency;
use ShoppingCart\Modifiers\Cost;
use ShoppingCart\Modifiers\SQLBuilder;
use ShoppingCart\Mailer;
use Soundasleep\Html2Text;
use DateTime;
use DateTimeZone;

class Order extends Basket{
    
    protected $download;
    
    protected $hasDownload = false;

    public $status_list = [1 => 'Pending', 2 => 'Paid', 3 => 'Order Complete &amp; Dispatched', 4 => 'Order Cancelled', 5 => 'Order Refunded'];
    
    /**
     * Adds instance of the required classes and clears old orders
     * @param Database $db This should be an instance of the Database class
     * @param Config $config This should be an instance of the Config class
     * @param object|false $user This should be an instance of the user/customer class
     * @param object|false $product This should be an instance of the product class 
     */
    public function __construct(Database $db, Config $config, $user = false, $product = false) {
        parent::__construct($db, $config, $user, $product);
        $this->download = new Download($this->db, $this->config, $this, $product);
    }
    
    /**
     * Clears orders from the database which haven't been completed after allotted time 
     */
    public function clearIncompleteOrders($timeframe = '-1 month') {
        $date = new DateTime();
        $date->modify($timeframe);
        $this->db->delete($this->config->table_basket, ['cust_id' => 0, 'status' => 1, 'date' => ['<=', $date->format('Y-m-d H:i:s')]]);
        $this->db->update($this->config->table_basket, ['status' => 4, 'sessionid' => NULL], ['status' => 1, 'date' => ['<=', $date->format('Y-m-d H:i:s')]]);
    }
    
    /**
     * Clears old paid orders that have not yet been marked as complete and may have been missed 
     */
    public function completeOldPaidOrders($timeframe = '-1 month') {
        $date = new DateTime();
        $date->modify($timeframe);
        $this->db->update($this->config->table_basket, ['status' => 3], ['status' => 2, 'date' => ['<=', $date->format('Y-m-d H:i:s')]]);
    }
    
    /**
     * Returns an array of orders based on the given parameters
     * @param int $status This should be the status of the orders you wish to retrieve (set to 0 for all orders)
     * @param int $userID If you want to only get orders for a certain user set the users unique ID here (set to 0 for all users)
     * @param int $start This should be the start location within the results used for pagination
     * @param int $limit The maximum number of results to return
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false If any orders exist matching the parameters will return an array else will return false
     */
    public function fetchOrders($status = 2, $userID = 0, $start = 0, $limit = 50, array $additional = []) {
        if(intval($userID) !== 0) {
            $additional['customer_id'] = $userID;
        }
        if(intval($status) !== 0) {
            $additional['status'] = $status;
        }
        $extraSQL = SQLBuilder::createAdditionalString($additional);
        return $this->db->query("SELECT * FROM `{$this->config->table_basket}` as `bskt`, `{$this->config->table_users}` AS `usr` WHERE `bskt`.`customer_id` = `usr`.`id`".(strlen($extraSQL) >= 1 ? ' AND '.$extraSQL : '')." ORDER BY `date` DESC".($limit >= 1 ? " LIMIT ".intval($start).", ".intval($limit) : '').";", SQLBuilder::$values);
    }
    
    /**
     * Returns a list of orders for a given status
     * @param int $status This should be the status of the orders that you wish to retrieve
     * @param int $start This should be the start location in the database
     * @param int $limit This should be the maximum number of records to display
     * @param array $additional Any additional items to contain the parameters to
     * @param boolean $count If you want to count the records rather than return set to true
     * @return array|false If any results exist with the given status will return an array of results else will return false
     */
    public function listOrders($status = 2, $start = 0, $limit = 50, array $additional = [], $count = false) {
        $extraSQL = SQLBuilder::createAdditionalString($additional);
        return $this->db->query("SELECT ".($count === false ? "*" : "count(`{$this->config->table_basket}`.`order_id`) as `count`")." FROM `{$this->config->table_basket}`, `{$this->config->table_users}` WHERE `{$this->config->table_basket}`.`customer_id` = `{$this->config->table_users}`.`id` AND `{$this->config->table_basket}`.`customer_id` != '0'".(is_numeric($status) ? " AND `{$this->config->table_basket}`.`status` = ".intval($status) : "").(strlen($extraSQL) >= 1 ? ' AND '.$extraSQL : '')." ORDER BY `date` DESC".($limit >= 1 ? " LIMIT ".intval($start).", ".intval($limit) : '').";", (!empty(SQLBuilder::$values) ? array_values(SQLBuilder::$values) : []));
    }
    
    /**
     * Returns the total number of orders with the given status
     * @param int $status This should be the status that you wish to retrieve the order for 
     * @param array $additional Any additional items to contain the parameters to
     * @return int This will be the number of orders matching the query
     */
    public function listOrdersCount($status, $additional = []) {
        return $this->listOrders($status, 0, 0, $additional, true)[0]['count'];
    }
    
    /**
     * Search the orders an customers database
     * @param string $search The search value that you are searching the orders for
     * @param int $start The start location in the database
     * @param int $limit The maximum number of results to show
     * @param int|false $status If the status value is set to a number it will only search for orders with that value else should be set to false
     * @param array $additional Add any additional parameters as an array here
     * @return array|false If any orders exist matching the criteria will return an array of results else will return false
     */
    public function searchOrders($search, $start = 0, $limit = 50, $status = false, array $additional = []) {
        $extraSQL = SQLBuilder::createAdditionalString($additional);
        return $this->db->query("SELECT * FROM `{$this->config->table_basket}`, `{$this->config->table_users}` WHERE (`{$this->config->table_basket}`.`customer_id` = `{$this->config->table_users}`.`id` AND `{$this->config->table_basket}`.`customer_id` != '0') AND `{$this->config->table_basket}`.`order_no` LIKE :SEARCH OR `{$this->config->table_users}`.`firstname` LIKE :SEARCH OR `{$this->config->table_users}`.`lastname` LIKE :SEARCH OR `{$this->config->table_users}`.`add_1` LIKE :SEARCH OR `{$this->config->table_users}`.`add_2` LIKE :SEARCH OR `{$this->config->table_users}`.`town` LIKE :SEARCH OR `{$this->config->table_users}`.`postcode` LIKE :SEARCH OR `{$this->config->table_users}`.`phone` LIKE :SEARCH OR `{$this->config->table_users}`.`mobile` LIKE :SEARCH OR `email` LIKE :SEARCH".(is_numeric($status) ? " AND `{$this->config->table_basket}`.`status` = ".intval($status) : "").(strlen($extraSQL) >= 1 ? ' AND '.$extraSQL : '')." ORDER BY `date` DESC".($limit >= 1 ? " LIMIT ".intval($start).", ".intval($limit) : '').";", array_merge(SQLBuilder::$values, [':SEARCH' => "%{$search}%"]));
    }
    
    /**
     * This will return the number of results for the given search query
     * @param string $search The search value that you are searching the orders for
     * @param int|false $status If the status value is set to a number it will only search for orders with that value else should be set to false
     * @return int
     */
    public function searchOrdersCount($search, $status = false, array $additional = []) {
        return count($this->searchOrders($search, 0, 0, $status, $additional));
    }
    
    /**
     * Returns an order by the incremental number within the database
     * @param int $orderID This should be the number of the order as in the auto-incremental ID column in the database
     * @param int|false $status If you wish to only get the order if it has a certain status set this here else set to false
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false If any orders exist with the id and status given it will be returned as an array else will return false
     */
    public function getOrderByID($orderID, $status = false, array $additional = []) {
        $additional['order_id'] = $orderID;
        if($status !== false) {
            $additional['status'] = intval($status);
        }
        return $this->getOrdersBy($additional, 1);
    }
    
    /**
     * Returns an order by the unique order number
     * @param string $orderNo This should be the order number for the order you wish to retrieve
     * @param int|false $status If you wish to only get the order if it has a certain status set this here else set to false 
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false If an order exists with the given order number and status it will be returned as ana array else will return false
     */
    public function getOrderByOrderNo($orderNo, $status = false, array $additional = []) {
        $additional['order_no'] = $orderNo;
        if($status !== false) {
            $additional['status'] = intval($status);
        }
        return $this->getOrdersBy($additional, 1);
    }
    
    /**
     * Returns orders for a given user and status
     * @param int $userID This should be the user ID of the user you wish to retrieve the order for
     * @param int $status If you only wish to retrieve order with a certain status set this to th status number else set to false
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false If any order exist matching the given criteria will return an array of the orders else will return false
     */
    public function getOrdersByUser($userID, $status = 2, array $additional = []) {
        if(is_numeric($userID) && $userID > 0) {
            $additional['customer_id'] = $userID;
        }
        if($status !== false) {
            $additional['status'] = intval($status);
        }
        return $this->getOrdersBy($additional, 0);
    }
    
    /**
     * Returns a specific order for user
     * @param int $userID This should be the users ID
     * @param string $orderNo This should be the unique order number
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false Returns array if any orders returned else returns false
     */
    public function getUserOrder($userID, $orderNo, array $additional = []) {
        if(is_numeric($userID) && $userID > 0) {
            $additional['customer_id'] = $userID;
        }
        $additional['order_no'] = $orderNo;
        return $this->getOrdersBy($additional, 1);
    }
    
    /**
     * Searches for order information based on the given array of queries
     * @param array $where This should be the array of parameters you want to search the orders for
     * @param int $limit This should be the maximum number of rows to return
     * @return array|false If any orders exist matching the given parameters will return an array of the returns else will return false 
     */
    protected function getOrdersBy($where, $limit = 1) {
        $orderInfo = $this->db->selectAll($this->config->table_basket, $where, '*', ['date' => 'DESC'], $limit);
        if(is_array($orderInfo)) {
            if($limit === 1){
                return $this->buildOrder($orderInfo);
            }
            foreach($orderInfo as $i => $order) {
                $orderInfo[$i] = $this->buildOrder($order);
            }
        }
        return $orderInfo;
    }
    
    /**
     * Retrieves additional information for a given order
     * @param array $orderInfo This should be the current order information
     * @return array A full array of the order information will be returned 
     */
    protected function buildOrder($orderInfo) {
        $this->totals = [];
        $orderInfo['statustext'] = $this->orderStatus($orderInfo['status']);
        $orderInfo['user'] = $this->user->getUserInfo($orderInfo['customer_id']);
        $orderInfo['delivery_info'] = $this->getDeliveryInfo($orderInfo);
        $orderInfo['billing_info'] = $this->getDeliveryInfo($orderInfo, false);
        $this->getOrderProducts($this->getProducts($orderInfo['order_id']), $orderInfo['order_id'], $orderInfo);
        $orderInfo['products'] = isset($this->totals['product']) ? $this->totals['product'] : [];
        $orderInfo['numproducts'] = isset($this->totals['numproducts']) ? $this->totals['numproducts'] : 0;
        unset($this->totals, $this->products);
        return $orderInfo;
    }
    
    /**
     * Returns all of the order information a given order number or the current users order if order number not set
     * @param string $orderNo This should be the order number
     * @param array $additional Any additional items to contain the parameters to
     * @return array|false If the order exists an array will be returned else will return false
     */
    public function getOrderInformation($orderNo = '', array $additional = []) {
        $orderInfo = $this->getBasket($orderNo, $additional);
        if(is_array($orderInfo)){
            return $this->getOrderByID($orderInfo['order_id']);
        }
        return false;
    }
    
    /**
     * Update order information
     * @param int $orderID This Should be the ID of the order you are updating the information for
     * @param type $orderInfo This should be an array containing the information that you wish to update
     * @return boolean Returns true if successfully updated else returns false
     */
    public function updateOrderInformation($orderID, $orderInfo = []) {
        if(is_numeric($orderID) && !empty(array_filter($orderInfo))){
            return $this->db->update($this->config->table_basket, $orderInfo, ['order_id' => $orderID]);
        }
        return false;
    }
    
    /**
     * Deletes an order
     * @param int $orderID This should be the unique order id
     * @return boolean If the order has successfully been deleted will return true else returns false
     */
    public function deleteOrderInformation($orderID){
        if(is_numeric($orderID)){
            return $this->db->delete($this->config->table_basket, ['order_id' => $orderID]);
        }
        return false;
    }
    
    /**
     * Retrieves the orders in the basket and any information relating to it
     * @param array|false $products This should be an array of the items in the specified basket
     * @param int $order_id This should be the unique order_id for this basket
     * @param int status The current order status 
     */
    protected function getOrderProducts($products, $order_id, $orderInfo){
        if(is_array($products)){
            $this->totals['numproducts'] = 0;
            foreach($products as $i => $product){
                $this->totals['product'][$i] = $this->product->getProductByID($product['product_id']);
                $this->totals['product'][$i]['quantity'] = intval($product['quantity']);
                $this->totals['product'][$i]['price'] = Cost::priceUnits($this->product->getProductPrice($product['product_id']), $this->decimals);
                $this->totals['product'][$i]['tax'] = Cost::priceUnits($this->tax->calculateItemTax($this->totals['product'][$i]['tax_id'], $this->totals['product'][$i]['price']), $this->decimals);
                $this->totals['numproducts'] = intval($this->totals['numproducts']) + $this->totals['product'][$i]['quantity'];
                if($this->product->isProductDownload($product['product_id']) && ($orderInfo['status'] == 2 || $orderInfo['status'] == 3)){
                    $this->hasDownload = true;
                    if($this->download->getDownloadInformation($product['product_id'], $order_id) === false){
                        $this->download->addDownloadLink($orderInfo['user']['id'], $order_id, [$product['product_id'] => $this->totals['product'][$i]['quantity']], $orderInfo['user']['email']);
                    }
                    $this->getOrderDownloadInfo($i, $product['product_id'], $order_id);
                }
            }
        }
    }
    
    /**
     * Returns the delivery information for and order 
     * @param array $orderInfo This should be the order information
     * @param boolean $delivery Should be set to true (default) for the delivery address else set to false for the billing address 
     * @return array The designated delivery information will be returned for the order
     */
    protected function getDeliveryInfo($orderInfo, $delivery = true) {
        $type = ($delivery === true ? 'delivery' : 'billing');
        if(isset($orderInfo[$type.'_id']) && !is_null($orderInfo[$type.'_id']) && $orderInfo[$type.'_id'] >= 1) {
            return $this->user->getDeliveryAddress($orderInfo[$type.'_id'], $orderInfo['customer_id']);
        }
        return $this->user->getUserInfo(intval($orderInfo['customer_id']));
    }
    
    /**
     * Returns the download information for a given product and order
     * @param int $key This should be the key
     * @param int $product This should be the product ID that you are retrieving the download information for
     * @param int $order_id This should be the order number associated with the download link
     */
    protected function getOrderDownloadInfo($key, $product, $order_id) {
        $dlinfo = $this->download->getDownloadInformation($product, $order_id);
        $this->totals['product'][$key]['dlid'] = $dlinfo['dlid'];
        $this->totals['product'][$key]['link'] = $this->download->getDownloadLink($product, $order_id);
        $this->totals['product'][$key]['serials'] = $this->download->getDownloadSerials($product, $order_id);
        $this->totals['product'][$key]['attempts'] = $dlinfo['attempts'];
    }
    
    /**
     * Return the status text for the given status number
     * @param int $status This should be the status number that you wish to get the text for
     * @return string This will be the status text
     */
    public function orderStatus($status = 1) {
        return $this->status_list[intval($status)];
    }
    
    /**
     * Change the status of a given order
     * @param int $order_id The incremental order ID that you are updating
     * @param int $new_status The new status to give to the order
     * @param boolean $setPaidDate If set to true will update the paid date/time to current date/time
     * @param boolean $sendEmail If you want to send the email set to true else set to false for no emails being sent
     * @return boolean If the status is updated will return true else will return false
     */
    public function changeOrderStatus($order_id, $new_status, $setPaidDate = false, $sendEmail = true) {
        if(is_numeric($new_status) && is_numeric($order_id)){
            $date = [];
            if($setPaidDate === true){
                $datetime = new DateTime();
                $datetime->setTimezone(new DateTimeZone($this->config->timezone));
                $date = ['payment_date' => $datetime->format('Y-m-d H:i:s')];
            }
            $this->db->update($this->config->table_basket, array_merge(['status' => intval($new_status)], $date), ['order_id' => $order_id], 1);
            $orderInfo = $this->getOrderByID($order_id);
            if($sendEmail === true && is_array($orderInfo)){
                $this->hasDownload === true && ($new_status == 2 || $new_status == 3) ? $this->download->sendDownloadLink($orderInfo['order_no']) : '';
                isset($this->orderEmailTypes($orderInfo)[$new_status]) ? $this->sendOrderEmail($orderInfo, $this->orderEmailTypes($orderInfo)[$new_status]['email'], $this->orderEmailTypes($orderInfo)[$new_status]['variables']) : '';
                isset($this->orderEmailTypes($orderInfo)[$new_status.'_office']) ? $this->sendOrderEmail($orderInfo, $this->orderEmailTypes($orderInfo)[$new_status.'_office']['email'], $this->orderEmailTypes($orderInfo)[$new_status.'_office']['variables'], false) : '';
            }
            return true;
        }
        return false;
    }
    
    /**
     * Send an order email
     * @param array $orderInfo This should be the order information
     * @param string $emailType This should be the string of the email type
     * @param array $variables This should be the variables to include in the email
     * @param boolean $toUser If the email is to be sent to the end user set to true else for the office email set to false
     * @return boolean Will return true if email is sent else will return false
     */
    public function sendOrderEmail($orderInfo, $emailType, $variables = [], $toUser = true) {
        $subject = sprintf($this->config->{"email_".strtolower($emailType)."_subject"}, $orderInfo['order_no'], date('d M Y', strtotime(isset($orderInfo['payment_date']) ? $orderInfo['payment_date'] : $orderInfo['date'])), $this->config->site_name);
        return Mailer::sendEmail(
            ($toUser === true ? $orderInfo['user']['email'] : $this->config->email_office_address),
            $subject,
            vsprintf($this->config->{"email_".strtolower($emailType)."_altbody"}, array_map(function($v){return Html2Text::convert($v, ['ignore_errors' => true]);}, $variables)),
            Mailer::htmlWrapper($this->config, vsprintf($this->config->{"email_".strtolower($emailType)."_body"}, $variables), $subject),
            $this->config->email_from_address,
            $this->config->email_from_name,
            '',
            ($emailType === 'order_confirm' ? [0 => [$this->createOrderPDF($orderInfo['order_id'], $orderInfo['customer_id'], false, false, true), 'Order'.$orderInfo['order_no'].'.pdf']] : [])
        );
    }
    
    /**
     * Returns the email types and variables to include 
     * @param array $orderInfo This should be the order information
     * @return array Returns the email types and variables to include
     */
    protected function orderEmailTypes($orderInfo){
        return array(
            2 => ['email' => 'order_confirm', 'variables' => [$orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], $this->config->site_url, $this->config->order_history_url, $this->emailFormatProducts($orderInfo), $orderInfo['delivery_info']['add_1'], $orderInfo['delivery_info']['add_2'], $orderInfo['delivery_info']['town'], $orderInfo['delivery_info']['county'], $orderInfo['delivery_info']['postcode']]],
            3 => ['email' => 'dispatch', 'variables' => [$orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], date('d/m/Y', strtotime(isset($orderInfo['payment_date']) ? $orderInfo['payment_date'] : $orderInfo['date'])), $this->emailFormatProducts($orderInfo), $orderInfo['user']['add_1'], $orderInfo['delivery_info']['add_1'], $orderInfo['user']['add_2'], $orderInfo['delivery_info']['add_2'], $orderInfo['user']['town'], $orderInfo['delivery_info']['town'], $orderInfo['user']['county'], $orderInfo['delivery_info']['county'], $orderInfo['user']['postcode'], $orderInfo['delivery_info']['postcode']]],
            4 => ['email' => 'order_cancel', 'variables' => [$orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], $this->config->site_url, $this->config->order_history_url]],
            5 => ['email' => 'order_refund', 'variables' => [$orderInfo['user']['title'], $orderInfo['user']['lastname'], $orderInfo['order_no'], $this->config->site_url, $this->config->order_history_url]],
            '2_office' => ['email' => 'order_office', 'variables' => [$orderInfo['order_no'], date('d/m/Y H:i', strtotime(isset($orderInfo['payment_date']) ? $orderInfo['payment_date'] : $orderInfo['date'])), trim($orderInfo['user']['title'].' '.$orderInfo['user']['firstname'].' '.$orderInfo['user']['lastname']), Currency::getCurrencySymbol($this->config->currency), $orderInfo['cart_total'], $this->config->admin_url, $this->config->admin_order_url, $this->emailFormatProducts($orderInfo, false)]]
        );
    }
    
    /**
     * Produces a HTML table to insert into the order email
     * @param array $orderInfo This should be the order information
     * @param boolean $download If you want to include any download links and serials set to true else set to false
     * @param boolean $tangible If you only want to include tangible products set to true
     * @return string Returns the HTML table 
     */
    public function emailFormatProducts($orderInfo, $download = true, $tangible = false){
        $table = '<table width="100%" border="0" cellpadding="2" cellspacing="0">
<tr><th>Quantity</th><th>Name</th><th>Unit Price</th><th>Total Price</th></tr>'."\n\r";
        foreach($this->removeUnwantedItems($orderInfo['products'], $tangible) as $product){
            $table.= '<tr><td class="align-center" valign="middle">'.$product['quantity'].'</td><td class="align-center" valign="middle">'.$product['name'];
            if(isset($product['link']) && $download === true){$table.= '<br /><br /><strong>Download Link:</strong> <a href="'.$product['link'].'" target="_blank">'.$product['link'].'</a>';}
            if(isset($product['serials']) && $download === true){
                $table.= '<br /><br /><strong>Serial No(s):</strong>';
                foreach($product['serials'] as $serial){
                    $table.= $serial['serial'].'<br />';
                }
            }
            $table.= '</td><td class="align-center" valign="middle">'.Currency::getCurrencySymbol($this->config->currency).$product['price'].'</td><td class="align-center" valign="middle">'.Currency::getCurrencySymbol($this->config->currency).number_format(($product['quantity']*$product['price']), Currency::getCurrencyDecimals($this->config->currency), '.', '')."</td></tr>\n\r";
        }
        $table.= '</table>';
        return $table;
    }
    
    /**
     * Removes any items that you might not want to include in all email such as download items
     * @param array $products This should be the products in the cart
     * @param boolean $tangible If you want to remove any download items set to true else set to false (false = default)
     * @return array Returns the product array with any irrelevant items removed
     */
    protected function removeUnwantedItems($products, $tangible = false) {
        if($tangible === true){
            foreach($products as $i => $product){
                if($product['digital'] == 1){unset($products[$i]);}
            }
        }
        return $products;
    }

    /**
     * Creates a PDF invoice
     */
    public function createOrderPDF($orderID, $userID, $download = false, $print = false, $send = false) {
        $invoice = new Invoice($this->db, $this->config, $this);
        return $invoice->createInvoice($orderID, $userID, $download, $print, $send);
    }
}
