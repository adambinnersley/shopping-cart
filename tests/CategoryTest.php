<?php
namespace ShoppingCart\Tests;

use ShoppingCart\Category;

class CategoryTest extends SetUp
{
    protected $category;
    
    protected function setUp(): void
    {
        parent::setUp();
        $this->category = new Category(self::$db, self::$config);
    }
    
    protected function tearDown(): void
    {
        parent::tearDown();
        $this->category = null;
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::addCategory
     * @covers \ShoppingCart\Modifiers\URL::makeURI
     */
    public function testAddCategory()
    {
        $this->assertTrue($this->category->addCategory('My Test Category', 'Some Random Description', 'my-test-category', ['active' => 1]));
        $this->assertFalse($this->category->addCategory('Duplicate URL', 'Some Random Description', 'my-test-category'));
        $this->assertFalse($this->category->addCategory('', '', 'new-categroy'));
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::listCategories
     * @covers \ShoppingCart\Category::addWhereIsActive
     */
    public function testListCategories()
    {
        $this->assertArrayHasKey('url', $this->category->listCategories()[0]);
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::getCategoryInfo
     * @covers \ShoppingCart\Category::getCategoryByID
     * @covers \ShoppingCart\Category::getCategoryByURL
     */
    public function testGetCategoryInfo()
    {
        $this->assertArrayHasKey('description', $this->category->getCategoryByID(1));
        $this->assertArrayHasKey('url', $this->category->getCategoryByID(1)); // Should use the set variable
        $this->assertFalse($this->category->getCategoryByID('ShouldBeANumber'));
        $this->assertArrayHasKey('url', $this->category->getCategoryByURL('my-test-category'));
        $this->assertFalse($this->category->getCategoryByURL('this-doesnt-exist'));
        $this->assertFalse($this->category->getCategoryByURL('this-doesnt-exist', 'hello'));
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::editCategory
     */
    public function testEditCategory()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::deleteCategory
     * @covers \ShoppingCart\Category::numberOfProductsInCategory
     */
    public function testDeleteCategory()
    {
        $this->markTestIncomplete();
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::getCategoryURL
     * @covers \ShoppingCart\Category::getCategoryByID
     */
    public function testGetCategoryURL()
    {
        $this->markTestIncomplete();
    }
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::changeCategoryOrder
     */
    public function testChangeCategoryOrder()
    {
        $this->markTestIncomplete();
    }
}
