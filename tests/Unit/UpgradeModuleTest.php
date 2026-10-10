<?php

class UpgradeModuleTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
    }

    public function testUpgrade211MigratesCategoryHook()
    {
        $module = new UpgradeModuleDouble();
        $this->assertTrue(upgrade_module_2_1_1($module));
        $this->assertContains('categoryUpdate', $module->unregistered);
        $this->assertContains('actionCategoryUpdate', $module->registered);
    }

    public function testUpgrade212MigratesProductHooks()
    {
        $module = new UpgradeModuleDouble();
        $this->assertTrue(upgrade_module_2_1_2($module));
        $this->assertContains('addproduct', $module->unregistered);
        $this->assertContains('updateproduct', $module->unregistered);
        $this->assertContains('deleteproduct', $module->unregistered);
        $this->assertContains('actionProductAdd', $module->registered);
        $this->assertContains('actionProductUpdate', $module->registered);
        $this->assertContains('actionProductDelete', $module->registered);
    }
}

class UpgradeModuleDouble
{
    public $registered = array();
    public $unregistered = array();

    public function registerHook($name)
    {
        $this->registered[] = $name;

        return true;
    }

    public function unregisterHook($name)
    {
        $this->unregistered[] = $name;

        return true;
    }
}

require_once dirname(dirname(__DIR__)) . '/upgrade/upgrade-2.1.1.php';
require_once dirname(dirname(__DIR__)) . '/upgrade/upgrade-2.1.2.php';
