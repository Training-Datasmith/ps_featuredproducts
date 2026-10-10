<?php

require_once __DIR__ . '/bootstrap-stubs.php';

class TestHarness
{
    public static function ensureModuleLoaded()
    {
        if (!class_exists('Ps_FeaturedProducts', false)) {
            require_once dirname(__DIR__) . '/ps_featuredproducts.php';
        }
    }

    public static function reset()
    {
        Configuration::reset();
        Tools::reset();
        Context::reset();
        \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::reset();
        ProductAssemblerStub::reset();
        ProductListingPresenterStub::reset();
    }

    public static function newModule()
    {
        self::ensureModuleLoaded();

        return new Ps_FeaturedProducts();
    }

    public static function invokeGetProducts($module)
    {
        self::ensureModuleLoaded();
        $reflection = new ReflectionClass($module);
        $method = $reflection->getMethod('getProducts');
        $method->setAccessible(true);

        return $method->invoke($module);
    }
}
