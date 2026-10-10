<?php

class PsFeaturedProductsCacheHooksTest extends PHPUnit_Framework_TestCase
{
    private static $template = 'module:ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl';

    protected function setUp()
    {
        TestHarness::reset();
    }

    /**
     * @dataProvider cacheHookProvider
     */
    public function testCacheHooksClearModuleTemplate($method)
    {
        $module = TestHarness::newModule();
        $module->$method([]);
        $this->assertContains(self::$template, ModuleStub::$clearCacheTemplates);
    }

    public function testClearCacheOverrideUsesModuleTemplate()
    {
        $module = TestHarness::newModule();
        $module->_clearCache('*');
        $this->assertContains(self::$template, ModuleStub::$clearCacheTemplates);
    }

    public function cacheHookProvider()
    {
        return [
            ['hookActionProductAdd'],
            ['hookActionProductUpdate'],
            ['hookActionProductDelete'],
            ['hookActionCategoryUpdate'],
            ['hookActionAdminGroupsControllerSaveAfter'],
        ];
    }
}
