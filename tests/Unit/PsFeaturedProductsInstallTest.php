<?php

class PsFeaturedProductsInstallTest extends PHPUnit_Framework_TestCase
{
    private static $expectedHooks = array(
        'actionProductAdd',
        'actionProductUpdate',
        'actionProductDelete',
        'displayHome',
        'displayOrderConfirmation2',
        'actionCategoryUpdate',
        'actionAdminGroupsControllerSaveAfter',
    );

    protected function setUp()
    {
        TestHarness::reset();
    }

    public function testInstallStoresDefaultsAndRegistersHooks()
    {
        $module = TestHarness::newModule();
        $this->assertTrue($module->install());
        $this->assertSame(8, (int) Configuration::get('HOME_FEATURED_NBR'));
        $this->assertSame(42, (int) Configuration::get('HOME_FEATURED_CAT'));
        $this->assertSame(false, (bool) Configuration::get('HOME_FEATURED_RANDOMIZE'));
        $this->assertHookMembership(ModuleStub::$registeredHooks);
    }

    public function testInstallReturnsFalseWhenParentInstallFails()
    {
        ModuleStub::$parentInstallResult = false;
        $module = TestHarness::newModule();
        $this->assertFalse($module->install());
        $this->assertSame(array(), ModuleStub::$registeredHooks);
    }

    /**
     * @dataProvider hookFailureProvider
     */
    public function testInstallReturnsFalseWhenHookRegistrationFails($hook)
    {
        ModuleStub::$hookFailOn = $hook;
        $module = TestHarness::newModule();
        $this->assertFalse($module->install());
    }

    public function testUninstallClearsModuleTemplateCache()
    {
        $module = TestHarness::newModule();
        $module->uninstall();
        $this->assertContains(
            'module:ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl',
            ModuleStub::$clearCacheTemplates
        );
    }

    public function hookFailureProvider()
    {
        $rows = array();
        foreach (self::$expectedHooks as $hook) {
            $rows[] = array($hook);
        }

        return $rows;
    }

    private function assertHookMembership(array $hooks)
    {
        $this->assertSame(count(self::$expectedHooks), count($hooks));
        $this->assertSame(count(self::$expectedHooks), count(array_unique($hooks)));
        foreach (self::$expectedHooks as $expected) {
            $this->assertContains($expected, $hooks);
        }
    }
}
