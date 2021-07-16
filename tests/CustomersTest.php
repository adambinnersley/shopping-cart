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
        $this->assertFalse($this->customers->addCustomer('my-email@email.com', 'my-PaSS#word1', 'my-PaSS#word1', ['title' => 'Mr', 'firstname' => 'Test', 'lastname' => 'User', 'add_1' => '1 Some Street', 'town' => 'London', 'postcode' => 'WE8 7TY', 'mobile' => '07900 100100'], ['firstname', 'lastname', 'add_1', 'town', 'postcode'], false, true)['error']);
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
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::getUserByEmail
     */
    public function testGetUserByEmail()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::listCustomers
     */
    public function testListCustomers()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::countCustomers
     */
    public function testCountCustomers()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::searchCustomers
     * @covers \ShoppingCart\Customers::formatAdditionalSQL
     */
    public function testSearchCustomers()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::countSearchResults
     * @covers \ShoppingCart\Customers::formatAdditionalSQL
     */
    public function testCountSearchResults()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::listCounties
     */
    public function testListCounties()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::addCustomer
     * @covers \ShoppingCart\Mailer::sendEmail
     * @covers \ShoppingCart\Mailer::htmlWrapper
     */
    public function testAddCustomer()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Customers::__construct
     * @covers \ShoppingCart\Customers::forgotPassword
     * @covers \ShoppingCart\Customers::addRequest
     * @covers \ShoppingCart\Mailer::sendEmail
     * @covers \ShoppingCart\Mailer::htmlWrapper
     */
    public function testForgotPassword()
    {
        $this->assertArrayHasKey('message', $this->customers->forgotPassword('my-email@email.com'));
        $this->assertTrue($this->customers->forgotPassword('this.should.not.exists@email.com')['error']);
        $this->assertTrue($this->customers->forgotPassword(1)['error']);
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
        $this->markTestIncomplete();
    }
}
