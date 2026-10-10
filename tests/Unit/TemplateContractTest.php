<?php

class TemplateContractTest extends PHPUnit_Framework_TestCase
{
    public function testHookTemplateReferencesWidgetVariables()
    {
        $contents = file_get_contents(
            dirname(dirname(__DIR__)) . '/views/templates/hook/ps_featuredproducts.tpl'
        );
        $this->assertContains('$products', $contents);
        $this->assertContains('$allProductsLink', $contents);
    }
}
