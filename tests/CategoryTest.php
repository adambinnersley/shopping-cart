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
        $this->assertFalse($this->category->editCategory('NAN', ['description' => '<p>This should not change as the ID need to be a number</p>']));
        $this->assertFalse($this->category->editCategory(99, ['description' => '<p>This should not change as the ID shouldn\'t exist</p>']));
        $this->assertTrue($this->category->editCategory(3, ['description' => '<p>This will be updated</p>', 'url' => 'this will be reformatted']));
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::deleteCategory
     * @covers \ShoppingCart\Category::numberOfProductsInCategory
     */
    public function testDeleteCategory()
    {
        $this->assertFalse($this->category->deleteCategory('NAN'));
        $this->assertFalse($this->category->deleteCategory(99)); // None existant category
        $this->assertFalse($this->category->deleteCategory(1)); // Products in category
        $this->assertTrue($this->category->deleteCategory(3)); // Products in category
    }
    
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::getCategoryURL
     * @covers \ShoppingCart\Category::getCategoryByID
     */
    public function testGetCategoryURL()
    {
        $this->assertFalse($this->category->getCategoryURL(99));
        $this->assertEquals('first-category', $this->category->getCategoryURL(1));
    }
    /**
     * @covers \ShoppingCart\Category::__construct
     * @covers \ShoppingCart\Category::changeCategoryOrder
     */
    public function testChangeCategoryOrder()
    {
        $this->assertEquals(1, $this->category->listCategories()[0]['id']);
        $this->assertFalse($this->category->changeCategoryOrder(1));
        $this->assertTrue($this->category->changeCategoryOrder(1, 'down'));
        $this->assertEquals(2, $this->category->listCategories()[0]['id']);
        $this->assertTrue($this->category->changeCategoryOrder(1));
        $this->assertEquals(1, $this->category->listCategories()[0]['id']);
    }
}
