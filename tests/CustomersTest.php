<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Customers;

class CustomersTest extends SetUp
{
    protected $customers;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->customers = new Customers(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->customers = null;
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::login
     * @covers \ShoppingCart\Customers::addCustomer
     * @covers \ShoppingCart\Customers::updateOrders
     * @covers \ShoppingCart\Customers::updateNoCustomerOrders
     */
    public function testLogin()
    {
        $this->assertTrue($this->customers->login('noneexistant@email.com', 'my-password')['error']);
        $this->assertFalse($this->customers->addCustomer('my-email@email.com', 'my-PaSS#word1', 'my-PaSS#word1', ['title' => 'Mr', 'firstname' => 'Test', 'lastname' => 'User', 'add_1' => '1 Some Street', 'town' => 'London', 'county' => '46', 'postcode' => 'WE8 7TY', 'mobile' => '07900 100100'], ['firstname', 'lastname', 'add_1', 'town', 'postcode'], false, true)['error']);
        $login = $this->customers->login('my-email@email.com', 'my-PaSS#word1');
        $this->assertFalse($login['error']);
        $this->assertArrayHasKey('cookie_name', $login);
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::logout
     * @covers \ShoppingCart\Customers::updateOrders
     * @covers \ShoppingCart\Customers::updateNoCustomerOrders
     */
    public function testLogout()
    {
        $this->assertFalse($this->customers->logout('NotAProperHash'));
        $hash = self::$db->select('users_sessions', ['uid' => 2]);
        $this->assertTrue($this->customers->logout($hash['hash']));
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::getUserByEmail
     */
    public function testGetUserByEmail()
    {
        $this->assertFalse($this->customers->getUserByEmail('notanemail'));
        $this->assertFalse($this->customers->getUserByEmail('doesnotexist@yahoo.co.uk'));
        $userInfo = $this->customers->getUserByEmail('my-email@email.com');
        $this->assertArrayHasKey('firstname', $userInfo);
        $this->assertEquals('1 Some Street', $userInfo['add_1']);
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::listCustomers
     */
    public function testListCustomers()
    {
        $this->assertCount(2, $this->customers->listCustomers());
        $this->assertArrayHasKey('firstname', $this->customers->listCustomers(0, 50)[0]);
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::countCustomers
     */
    public function testCountCustomers()
    {
        $this->assertEquals(2, $this->customers->countCustomers());
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::searchCustomers
     * @covers \ShoppingCart\Customers::formatAdditionalSQL
     */
    public function testSearchCustomers()
    {
        $this->assertEmpty($this->customers->searchCustomers('hello', 0, 50));
        $results = $this->customers->searchCustomers('Some Street', 0, 50, ['last_login' => 'IS NULL']);
        $this->assertArrayHasKey('title', $results[0]);
        $this->assertCount(1, $results);
        $this->assertCount(1, $this->customers->searchCustomers('Test'));
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::countSearchResults
     * @covers \ShoppingCart\Customers::formatAdditionalSQL
     */
    public function testCountSearchResults()
    {
        $this->assertEquals(0, $this->customers->countSearchResults('hello'));
        $this->assertEquals(1, $this->customers->countSearchResults('test', ['last_login' => 'IS NULL']));
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::listCounties
     */
    public function testListCounties()
    {
        $this->assertGreaterThan(100, count($this->customers->listCounties()));
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::addCustomer
     * @covers \ShoppingCart\Mailer::sendEmail
     * @covers \ShoppingCart\Mailer::htmlWrapper
     */
    public function testAddCustomer()
    {
        $this->assertTrue($this->customers->addCustomer('my-email@email.com', 'my-PaSS#word1', 'my-PaSS#word1', ['title' => 'Mr', 'firstname' => 'Test', 'lastname' => 'User', 'add_1' => '1 Some Street', 'town' => 'London', 'county' => '46', 'postcode' => 'WE8 7TY', 'mobile' => '07900 100100'], ['firstname', 'lastname', 'add_1', 'town', 'postcode'], false, true)['error']);
        $this->assertFalse($this->customers->addCustomer('new-customer@email.com', 'my-PaSS#word1', 'my-PaSS#word1', ['title' => 'Mr', 'firstname' => 'Another', 'lastname' => 'Test', 'add_1' => '20 Some Avanue', 'town' => 'Leeds', 'county' => '46', 'postcode' => 'LS15 7TY', 'mobile' => '07900 111111'], ['firstname', 'lastname', 'add_1', 'town', 'postcode'], false, true)['error']);
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::forgotPassword
     * @covers \ShoppingCart\Customers::addRequest
     * @covers \ShoppingCart\Customers::resetPassword
     * @covers \ShoppingCart\Customers::getUserInfo
     * @covers \ShoppingCart\Customers::sendPasswordChangeEmail
     * @covers \ShoppingCart\Mailer::sendEmail
     * @covers \ShoppingCart\Mailer::htmlWrapper
     */
    public function testForgotResetPassword()
    {
        $this->assertArrayHasKey('message', $this->customers->forgotPassword('my-email@email.com'));
        $this->assertTrue($this->customers->forgotPassword('this.should.not.exists@email.com')['error']);
        $this->assertTrue($this->customers->forgotPassword(2)['error']);
        $this->customers->requestReset('my-email@email.com', false);
        $key = self::$db->fetchColumn('users_requests', ['uid' => 2], ['rkey']);
        $this->assertTrue($this->customers->resetPassword($key, 'my-NeW-PaSS#word1', 'doesnotmatch', NULL, false, false)['error']);
        $this->assertFalse($this->customers->resetPassword($key, 'my-NeW-PaSS#word1', 'my-NeW-PaSS#word1', NULL)['error']);
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::updateCustomer
     * @covers \ShoppingCart\Customers::getUserInfo
     * @covers \ShoppingCart\Customers::checkEmailExists
     */
    public function testUpdateCustomer()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::deleteCustomer
     */
    public function testDeleteCustomer()
    {
        $this->assertTrue($this->customers->deleteCustomer('NAN')['error']);
        $this->assertTrue($this->customers->deleteCustomer(99)['error']);
        $this->assertFalse($this->customers->deleteCustomer(2)['error']);
    }
}
