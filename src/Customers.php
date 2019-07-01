<?php

namespace ShoppingCart;

use DBAL\Database;
use DBAL\Modifiers\SafeString;
use DBAL\Modifiers\Modifier;
use UKCounties\Counties;
use Configuration\Config;
use Blocking\IPBlock;

class Customers extends \UserAuth\User{
    
    /**
     * This should be the config of the object
     * @var object 
     */
    public $config;
    
    /**
     * This is an object of Counties
     * @var object 
     */
    public $counties;
    
    /**
     *This is the object for the IP Address Blocking and information
     * @var object
     */
    protected $ip_address;


    /**
     * Constructor
     * @param Database $db This should be the database class instance
     * @aparm Config $config This should be an instance of the config class
     * @param string $language The current language that you want to use
     */
    public function __construct(Database $db, Config $config = NULL, $language = "en_GB") {
        parent::__construct($db, $language);
        $this->config = $config;
        $this->counties = new Counties();
        $this->ip_address = new IPBlock($this->db);
        $this->table_users = $this->config->table_users;
        $this->table_sessions = $this->config->table_users_sessions;
        $this->table_requests = $this->config->table_users_requests;
        $this->table_attempts = $this->config->table_users_attempts;
        $this->setLanguageArray(array_merge($this->getLanguageArray(), $this->config->getAll()));
    }
        
    /**
    * Gets public user data for a given email and returns an array, password is not returned
    * @param  stringt $email This should be the user ID of the person you are getting the information for
    * @return array|false If information exists for the user will return an array else will return false
    */
    public function getUserByEmail($email) {
        if(filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $data = $this->db->select($this->table_users, ['email' => filter_var($email, FILTER_SANITIZE_EMAIL)]);
            if(empty($data)) {
                return false;
            }
            $data['uid'] = $data['id'];
            unset($data['password']);
            return $data;
        }
        return false;
    }
    
    /**
     * List customers in the database
     * @param int $start This should be the start location for the results used for pagination
     * @param int $limit This should be the maximum number of results to show
     * @param array $additionalInfo Any additional information you want to limit the customers on
     * @return array|false IF any results exist they will be returned as an array else will return false
     */
    public function listCustomers($start = 0, $limit = 50, $additionalInfo = []) {
        $customers = $this->db->selectAll($this->table_users, $additionalInfo, '*', ['regtime' => 'DESC'], [intval($start) => intval($limit)]);
        if(is_array($customers)){
            foreach($customers as $i => $customer){
                if(isset($customer['county']) && is_numeric($customer['county'])){
                    $customers[$i]['county'] = $this->counties->getCountyName($customer['county']);
                }
            }
        }
        return $customers;
    }
    
    /**
     * Counts the total number of customers in the database
     * @param array $additionalInfo Any additional information you want to limit the count on
     * @return int This will be the number of customers
     */
    public function countCustomers($additionalInfo = []) {
        return $this->db->count($this->table_users, $additionalInfo);
    }
    
    /**
     * Search the customers database for a particular customer or their details
     * @param mixed $search This should be the search field
     * @param int $start The start location of the results used for pagination
     * @param int $limit This should be the maximum number of results to show
     * @param array $additionalInfo Any additional information you want to limit the search on
     * @return array|false If results exist will return an array else if no results exist will return false
     */
    public function searchCustomers($search, $start = 0, $limit = 50, $additionalInfo = []) {
        $sql = $this->formatAdditionalSQL($additionalInfo);
        return $this->db->query("SELECT * FROM `{$this->table_users}` WHERE `firstname` LIKE :SEARCH OR `lastname` LIKE :SEARCH OR `add_1` LIKE :SEARCH OR `add_2` LIKE :SEARCH OR `town` LIKE :SEARCH OR `postcode` LIKE :SEARCH OR `phone` LIKE :SEARCH OR `mobile` LIKE :SEARCH OR `email` LIKE :SEARCH{$sql['string']} ORDER BY `regtime` DESC LIMIT ".intval($start).", ".intval($limit).";", array_merge([':SEARCH' => '%'.$search.'%'], $sql['values']));
    }
    
    /**
     * Counts the number of results for the given search
     * @param mixed $search This should be the search field
     * @param array $additionalInfo Any additional information you want to limit the search on
     * @return int The number of results will be returned
     */
    public function countSearchResults($search, $additionalInfo = []) {
        $sql = $this->formatAdditionalSQL($additionalInfo);
        $results = $this->db->query("SELECT COUNT(*) as `count` FROM `{$this->table_users}` WHERE `firstname` LIKE :SEARCH OR `lastname` LIKE :SEARCH OR `add_1` LIKE :SEARCH OR `add_2` LIKE :SEARCH OR `town` LIKE :SEARCH OR `postcode` LIKE :SEARCH OR `phone` LIKE :SEARCH OR `mobile` LIKE :SEARCH OR `email` LIKE :SEARCH{$sql['string']} ORDER BY `regtime` DESC;", array_merge([':SEARCH' => '%'.$search.'%'], $sql['values']));
        return $results[0]['count'];
    }
    
    /**
     * Format additional values for use in the queries
     * @param array $additionalInfo This should be the additional information submitted
     * @return array Returns the SQL array info include the 'string' and array 'values'
     */
    protected function formatAdditionalSQL($additionalInfo) {
        $sql = [];
        $sql['values'] = [];
        $sql['string'] = '';
        if(!empty($additionalInfo)){
            foreach($additionalInfo as $field => $value) {
                $fieldVal = SafeString::makeSafe($field);
                $sql['string'] .= " AND `{$fieldVal}` = :{$fieldVal}";
                $sql['values'][':'.$fieldVal] = $value;
            }
        }
        return $sql;
    }
    
    /**
     * Updates the number of orders in the users table of the of the database
     * @param int $userID This should be the users unique id
     * @param int $noOrders The total number of orders that the user has places
     * @return boolean If the information is successfully updated will return true else returns false
     */
    public function updateNoCustomerOrders($userID, $noOrders) {
        return $this->db->update($this->table_users, ['no_orders' => intval($noOrders)], ['id' => intval($userID)], 1);
    }
    
    /**
     * Returns a list of all of the available counties
     * @return array
     */
    public function listCounties(){
        return $this->counties->getCountiesByName();
    }
    
    /**
     * Returns a delivery address for the customer 
     * @param int $address_id This should be the unique delivery address id
     * @param int $customerID This should be the customer id to verify that the address belongs to that customer
     * @return array|false If the address exists for the given customer will return an array of details else will return false
     */
    public function getDeliveryAddress($address_id, $customerID) {
        $deliveryInfo = $this->db->select($this->config->table_delivery_address, ['id' => $address_id, 'customer_id' => $customerID]);
        if(is_array($deliveryInfo)){
            if(empty($deliveryInfo['title']) || empty($deliveryInfo['firstname']) || empty($deliveryInfo['lastname'])){
                $userInfo = $this->getUserInfo($customerID);
                $deliveryInfo['title'] = $userInfo['title'];
                $deliveryInfo['firstname'] = $userInfo['firstname'];
                $deliveryInfo['lastname'] = $userInfo['lastname'];
            }
            $deliveryInfo['county'] = $this->counties->getCountyName($deliveryInfo['county']);
        }
        return $deliveryInfo;
    }
    
    /**
     * Add a new delivery address for the customer
     * @param int $customerID This should be the customers ID
     * @param array $deliveryInfo This should be the updated delivery information as an array
     * @return int|boolean If the delivery information is inserted into the database will return the lastInsertID number to add to the order else if nothing has been added will return false
     */
    public function setDeliveryAddress($customerID, $deliveryInfo) {
        if($this->checkIfAddressExists($customerID, $deliveryInfo['add_1'], $deliveryInfo['postcode']) === false && $this->compareDeliveryToBillingAddress($customerID, $deliveryInfo) === false) {
            $userInfo = $this->getUserInfo(intval($customerID));
            $this->db->insert($this->config->table_delivery_address, ['customer_id' => intval($customerID), `title` => (!empty(trim($deliveryInfo['title'])) ? $deliveryInfo['title'] : $userInfo['title']), `firstname` => (!empty(trim($deliveryInfo['firstname'])) ? $deliveryInfo['firstname'] : $userInfo['firstname']), `lastname` => (!empty(trim($deliveryInfo['lastname'])) ? $deliveryInfo['lastname'] : $userInfo['lastname']), 'add_1' => $deliveryInfo['add_1'], 'add_2' => $deliveryInfo['add_2'], 'town' => $deliveryInfo['town'], 'county' => intval($deliveryInfo['county']), 'postcode' => strtoupper(Modifier::removeNoneAlphaNumeric($deliveryInfo['postcode']))]);
            return $this->db->update($this->config->table_basket, ['delivery_id' => $this->db->lastInsertID()], ['customer_id' => $customerID, 'sessionid' => session_id(), 'status' => 1], 1);
        }
        return false;
    }
    
    /**
     * Edits a given delivery address information
     * @param int $customerID This should be the customers ID
     * @param int $deliveryID  This should be the unique delivery address ID
     * @param array $deliveryInfo This should be the updated delivery information as an array
     * @return boolean If the information is updated will return true else returns false
     */
    public function editDeliveryAddress($customerID, $deliveryID, $deliveryInfo) {
        if(Modifier::arrayMustContainFields(['add_1', 'town', 'postcode'], $deliveryInfo) && is_numeric($deliveryInfo['county'])) {
            $deliveryInfo['postcode'] = strtoupper(Modifier::removeNoneAlphaNumeric($deliveryInfo['postcode']));
            return $this->db->update($this->config->table_delivery_address, $deliveryInfo, ['id' => intval($deliveryID), 'customer_id' => intval($customerID)], 1);
        }
        return false;
    }
    
    /**
     * Deletes a given delivery address from the database
     * @param int $customerID This should be the customers ID
     * @param int $deliveryID This should be the unique delivery address ID
     * @return boolean If the information is deleted will return true else will return false
     */
    public function deleteDeliveryAddress($customerID, $deliveryID) {
        $this->db->delete($this->config->table_delivery_address, ['id' => intval($deliveryID), 'customer_id' => intval($customerID)], 1);
        return $this->db->update($this->config->table_basket, ['delivery_id' => 'NULL'], ['customer_id' => $customerID, 'sessionid' => session_id(), 'status' => 1], 1);
    }
    
    /**
     * Checks to see if the submitted delivery information is the same as the billing information before its inserted
     * @param int $customerID This should be the customers ID
     * @param array $deliveryInfo This should be the delivery information as an array
     * @return boolean If the information matches will return true else will return false
     */
    protected function compareDeliveryToBillingAddress($customerID, $deliveryInfo) {
        $billingInfo = $this->getUserInfo($customerID);
        if($deliveryInfo['add_1'] == $billingInfo['add_1'] && $deliveryInfo['postcode'] == $billingInfo['postcode']) {
            return true;
        }
        return false;
    }
    
    /**
     * Check to see if the same delivery address already exist in the database for this user
     * @param int $customerID This should be the customers ID
     * @param string $address This should be the first line of the address
     * @param string $postcode This should be the postcode given
     * @return array|false If the address already exist will return the information as an array else will return false
     */
    public function checkIfAddressExists($customerID, $address, $postcode) {
        return $this->db->select($this->config->table_delivery_address, ['customer_id' => $customerID, 'add_1' => ['LIKE', $address], 'postcode' => strtoupper($postcode)]);
    }
    
    /**
     * Select all of the delivery addresses added by the customer
     * @param int $customerID This should be the customers unique id
     * @return array|false If the customer has any delivery addresses added to the database they will be returned here else will return false
     */
    public function listCustomerDeliveryAddresses($customerID) {
        return $this->db->selectAll($this->config->table_delivery_address, ['customer_id' => $customerID]);
    }

    /**
     * Returns the user information for the user who is currently logged in
     * @param int|boolean $userID
     * @return mixed If the user is logged in will return their information else will return false
     */
    public function getUserInfo($userID = false) {
        $userInfo = parent::getUserInfo($userID);
        if(is_array($this->userInfo) && is_numeric($this->userInfo['county']) && !is_numeric($userID)) {
            $this->userInfo['county'] = $this->counties->getCountyName($this->userInfo['county']);
            return $this->userInfo;
        }
        elseif(is_array($userInfo)){
            $userInfo['county'] = $this->counties->getCountyName($userInfo['county']);
        }
        return $userInfo;
    }
    
    /**
     * Add a customer to the database
     * @param string $email The email address for the account
     * @param string $password The password that the user has entered
     * @param string $confirm The password field repeated to make sure they match and are what the user wants
     * @param array $params An array containing the users entered parameters
     * @param array $required Enter an array containing what fields are required to fill out the form
     * @param boolean $sendmail If you want an email to be sent set to true (default) else set to false
     * @param boolean $login If you want the user to be logged in once created set to true else should be set to false
     * @return array Returns an array containing the error status and any message 
     */
    public function addCustomer($email, $password, $confirm, $params = [], $required = ['firstname', 'lastname', 'add_1', 'town', 'postcode'], $sendmail = true, $login = true) {
        $return = [];
        $return['error'] = true;
        if(!Modifier::arrayMustContainFields($required, $params) && (!Modifier::arrayMustContainFields(['phone'], $params) || !Modifier::arrayMustContainFields(['mobile'], $params))){
            $return['message'] = 'Please make sure all of the required fields have been entered and try again!';
            return $return;
        }
        $params['postcode'] = strtoupper(Modifier::removeNoneAlphaNumeric($params['postcode']));
        $params['ipaddress'] = $this->ip_address->getUserIP();
        $addUser = $this->register($email, $password, $confirm, $params, NULL, false);
        if($addUser['error'] === false && $sendmail === true) {
            $subject = sprintf($this->config->email_reg_subject, $this->config->site_name);
            $emailArray = array_merge([$this->config->site_name, $this->config->site_url, $this->config->login_url], $params);
            Mailer::sendEmail(
                $email,
                $subject,
                vsprintf($this->config->email_reg_altbody, $emailArray),
                Mailer::htmlWrapper($this->config, vsprintf($this->config->email_reg_body, $emailArray), $subject),
                $this->config->email_from_address,
                $this->config->email_from_name
            );
        }
        if($addUser['error'] === false && $login === true){
            $this->login($email, $password, true);
        }
        return $addUser;
    }
    
    /**
     * Updates customer information
     * @param int $userID This should be the ID of the user that the information is being updated
     * @param array $customerInfo This should be an array of the customer information that is getting updated
     * @param array $additionalInfo This should be any addition parameters to limit the update on
     * @return boolean If the customer information has successfully been updated will return true else return false
     */
    public function updateCustomer($userID, $customerInfo = [], $additionalInfo = []) {
        $return = [];
        $return['error'] = true;
        if($customerInfo['email']){
            $currentInfo = $this->getUserInfo($userID);
            if(filter_var(trim($customerInfo['email']), FILTER_VALIDATE_EMAIL) && (strtolower($currentInfo['email']) !== strtolower($customerInfo['email']) && !$this->checkEmailExists($customerInfo['email']))){
                $customerInfo['email'] = strtolower(trim($customerInfo['email']));
            }
            elseif(strtolower($currentInfo['email']) !== strtolower($customerInfo['email']) && !$this->checkEmailExists($customerInfo['email'])){
                $return['message'] = 'The email address you are attempting to use already belongs to another account!';
                return $return;
            }
            else{
                unset($customerInfo['email']);
            }
        }
        if($customerInfo['postcode']) {
            $customerInfo['postcode'] = strtoupper(Modifier::removeNoneAlphaNumeric($customerInfo['postcode']));
        }
        $return['error'] = $this->db->update($this->table_users, $customerInfo, array_filter(array_merge(['id' => $userID], $additionalInfo)), 1) ? false : true;
        $return['message'] = $return['error'] ? 'Please make sure the information has changed and try again!' : 'Account has been successfully updated';
        return $return;
    }
    
    /**
     * Deletes a given user
     * @param int $userID This should be the unique ID
     * @param array $additionalInfo This should be any other variables to constrain the delete
     * @return array Returns an array containing a message an if error is true or false
     */
    public function deleteCustomer($userID, $additionalInfo = []) {
        $return = [];
        $return['error'] = true;
        if(!$this->db->delete($this->table_users, array_filter(array_merge(['id' => $userID], $additionalInfo)), 1)) {
            $return['message'] = $this->lang["system_error"] . " #05";
            return $return;
        }
        $return['error'] = false;
        $return['message'] = $this->lang["account_deleted"];

        return $return;
    }
    
    /**
    * Changes a user's password
    * @param int $uid The users ID that you are changing the password for
    * @param string $currpass The current password of the user
    * @param string $newpass The new password
    * @param string $repeatnewpass A confirmation of the new password
    * @param string $captcha = NULL
    * @return array Return an array containing error status and message if 
    */
    public function changePassword($uid, $currpass, $newpass, $repeatnewpass, $captcha = NULL) {
        $change = parent::changePassword($uid, $currpass, $newpass, $repeatnewpass, $captcha);
        if($change['error'] === false){
            $this->sendPasswordChangeEmail($this->getUserInfo($uid));
        }
        return $change;
    }
    
    /**
     * Reset the password with a request key instead of the existing password 
     * @param string $key This should be the key requested via the forgot password form
     * @param string $newpass This should be the new password
     * @param string $repeatnewpass This should be a repeat of the new password
     * @param string $captcha = NULL If captcha exists set this here
     * @param boolean $sendmail If you want an email to be sent should be set to true (default) else set to false
     * @param boolean $login If you want user to be logged in after password reset set to true
     * @return array Return an array containing error status and message if 
     */
    public function resetPassword($key, $newpass, $repeatnewpass, $captcha = NULL, $sendmail = true, $login = true) {
        $userInfo = $this->getUserInfo($this->getRequest($key, "reset")['uid']);
        $return = $this->resetPass($key, $newpass, $repeatnewpass, $captcha);
        if($return['error'] === true){
            $this->db->update($this->table_users, ['require_pass' => 0], ['id' => $userInfo['id']]);
            return $return;
        }
        
        if($sendmail === true && is_array($userInfo)) {
            $this->sendPasswordChangeEmail($userInfo);
        }
        if($login === true){
            $this->login($userInfo['email'], $newpass, false);
        }
        return $return;
    }
    
    /**
     * Sends email confirmation that the password has changed
     * @param array $userInfo This should be the users information of the users that the password has changed
     */
    protected function sendPasswordChangeEmail($userInfo){
        $subject = sprintf($this->config->email_password_change_subject, $this->config->site_name);
        $emailInfo = array_merge([$this->config->site_name, $this->config->site_url], $userInfo);
        Mailer::sendEmail($userInfo['email'], $subject, vsprintf($this->config->email_password_change_altbody, $emailInfo), Mailer::htmlWrapper($this->config, vsprintf($this->config->email_password_change_body, $emailInfo), $subject), $this->config->email_from_address, $this->config->email_from_name);
    }
    
    /**
     * Forgotten password request
     * @param string $email This should be the email address of the account that is resetting the password
     * @return array Will return an array containing error statuses and any warning messages
     */
    public function forgotPassword($email) {
        return $this->requestReset($email, true);
    }
       
    /**
    * Creates an activation entry and sends email to user
    * @param int $uid
    * @param string $email
    * @param string $type
    * @param boolean|null $sendmail = NULL
    * @return boolean
    */
    protected function addRequest($uid, $email, $type, $sendmail) {
        $return = parent::addRequest($uid, $email, $type, false);
        if($return['error'] === false && $sendmail === true) {
            $mailsent = false;
            if($type == "activation") {
                $subject = sprintf($this->config->email_activation_subject, $this->config->site_name);
                $mailsent = Mailer::sendEmail($email, $subject, sprintf($this->config->email_activation_altbody, $this->config->site_url, $this->activation_page, $this->key), Mailer::htmlWrapper($this->config, sprintf($this->config->email_activation_body, $this->config->site_url, $this->activation_page, $this->key), $subject), $this->config->email_from_address, $this->config->email_from_name);
            }
            elseif($type == "reset"){
                $subject = sprintf($this->config->email_reset_subject, $this->config->site_name);
                $mailsent = Mailer::sendEmail($email, $subject, sprintf($this->config->email_reset_altbody, $this->config->site_url, $this->password_reset_page, $this->key),  Mailer::htmlWrapper($this->config, sprintf($this->config->email_reset_body, $this->config->site_url, $this->password_reset_page, $this->key), $subject), $this->config->email_from_address, $this->config->email_from_name);
            }
            if($mailsent !== true) {
                $this->deleteRequest($this->db->lastInsertId());
                $return['error'] = true;
                $return['message'] = $this->lang["system_error"] . " #06";
                return $return;
            }
        }
        return $return;
    }
}