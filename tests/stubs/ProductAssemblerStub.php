<?php

class ProductAssemblerStub
{
    public static $assembleProductsCalls = 0;
    public static $assembleProductCalls = 0;

    public static function reset()
    {
        self::$assembleProductsCalls = 0;
        self::$assembleProductCalls = 0;
    }
}

class ProductPresenterFactory
{
    public function __construct($context)
    {
    }

    public function getPresentationSettings()
    {
        return [];
    }
}

class ProductAssembler
{
    public function __construct($context)
    {
    }

    public function assembleProducts($products)
    {
        ++ProductAssemblerStub::$assembleProductsCalls;

        return $products;
    }

    public function assembleProduct($product)
    {
        ++ProductAssemblerStub::$assembleProductCalls;

        return $product;
    }
}
