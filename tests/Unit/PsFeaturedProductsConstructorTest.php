<?php

class PsFeaturedProductsConstructorTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
    }

    public function testConstructorExposesTechnicalSurface()
    {
        $module = TestHarness::newModule();
        $this->assertSame('ps_featuredproducts', $module->name);
        $interfaces = class_implements($module);
        $this->assertTrue(isset($interfaces['PrestaShop\\PrestaShop\\Core\\Module\\WidgetInterface']));
        $reflection = new ReflectionClass($module);
        $property = $reflection->getProperty('templateFile');
        $property->setAccessible(true);
        $this->assertSame(
            'module:ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl',
            $property->getValue($module)
        );
    }
}
