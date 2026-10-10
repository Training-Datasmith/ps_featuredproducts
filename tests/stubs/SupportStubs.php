<?php

class ShopStub
{
    public function getCategory()
    {
        return ShopStub::$categoryId;
    }

    public static $categoryId = '42';
}

class LinkStub
{
    public static $categoryLinkIds = array();

    public static function reset()
    {
        self::$categoryLinkIds = array();
    }

    public function getCategoryLink($id)
    {
        self::$categoryLinkIds[] = $id;

        return 'category-link-' . $id;
    }

    public function getAdminLink($controller, $withToken = false)
    {
        return 'admin-link';
    }
}

class LanguageStub
{
    public $id = 1;
}

class CustomerStub
{
    public $id = 0;
}

class ControllerStub
{
    public function getLanguages()
    {
        return array();
    }
}

class TranslatorStub
{
}

class SmartyStub
{
    public $assigned = array();

    public function assign($vars)
    {
        $this->assigned = $vars;
    }

    public function createData($smarty)
    {
        return new SmartyDataStub();
    }
}

class SmartyDataStub
{
}

class Language
{
    public $id;

    public function __construct($id)
    {
        $this->id = (int) $id;
    }
}

class HelperFormStub
{
    public static $lastInstance;

    public $show_toolbar;
    public $table;
    public $default_form_language;
    public $allow_employee_form_lang;
    public $identifier;
    public $submit_action;
    public $currentIndex;
    public $token;
    public $tpl_vars;
    public $lastFieldsForm;

    public static function reset()
    {
        self::$lastInstance = null;
    }

    public function generateForm($fields)
    {
        $this->lastFieldsForm = $fields;

        return 'generated-form';
    }
}

class HelperForm extends HelperFormStub
{
    public function __construct()
    {
        HelperFormStub::$lastInstance = $this;
    }
}

class CategoryStub
{
    public static $constructedIds = array();

    public static function reset()
    {
        self::$constructedIds = array();
    }
}

class Category
{
    public function __construct($id)
    {
        CategoryStub::$constructedIds[] = $id;
    }
}
