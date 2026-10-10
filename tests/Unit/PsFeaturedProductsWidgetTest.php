<?php

class PsFeaturedProductsWidgetTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
        Configuration::updateValue('HOME_FEATURED_NBR', 8);
        Configuration::updateValue('HOME_FEATURED_CAT', '4');
        Configuration::updateValue('HOME_FEATURED_RANDOMIZE', false);
    }

    public function testAllProductsLinkUsesStoredCategoryNotRequestOverride()
    {
        Tools::setValues(array('HOME_FEATURED_CAT' => '9'));
        \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::$productsToReturn = array(
            array('id_product' => 1),
        );
        $module = TestHarness::newModule();
        $variables = $module->getWidgetVariables('displayHome', array());
        $this->assertInternalType('array', $variables);
        $this->assertSame(4, CategoryStub::$constructedIds[0]);
        $this->assertSame(4, LinkStub::$categoryLinkIds[0]);
        $this->assertInternalType('string', $variables['allProductsLink']);
        $this->assertNotEmpty($variables['allProductsLink']);
    }

    public function testEmptyProductsReturnsFalse()
    {
        \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::$productsToReturn = array();
        $module = TestHarness::newModule();
        $this->assertFalse($module->getWidgetVariables('displayHome', array()));
        $this->assertFalse($module->renderWidget('displayHome', array()));
    }

    public function testAdapterPresenterBulkAssemblyForCurrentPsVersion()
    {
        \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::$productsToReturn = array(
            array('id_product' => 1),
            array('id_product' => 2),
        );
        $module = TestHarness::newModule();
        TestHarness::invokeGetProducts($module);
        $this->assertSame(1, ProductAssemblerStub::$assembleProductsCalls);
        $this->assertSame(0, ProductAssemblerStub::$assembleProductCalls);
        $this->assertSame(2, \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter::$presentCount);
        $this->assertSame(0, \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter::$presentCount);
    }
}
