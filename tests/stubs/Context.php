<?php

class Context
{
    private static $instance;

    public $shop;
    public $link;
    public $language;
    public $controller;
    public $customer;
    public $smarty;

    public function __construct()
    {
        $this->shop = new ShopStub();
        $this->link = new LinkStub();
        $this->language = new LanguageStub();
        $this->controller = new ControllerStub();
        $this->customer = new CustomerStub();
        $this->smarty = new SmartyStub();
    }

    public function getTranslator()
    {
        return new TranslatorStub();
    }

    public static function getContext()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public static function reset()
    {
        self::$instance = null;
        LinkStub::reset();
        CategoryStub::reset();
        \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::reset();
        ProductAssemblerStub::reset();
        ProductListingPresenterStub::reset();
        HelperFormStub::reset();
        ModuleStub::reset();
    }
}
