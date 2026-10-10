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
        $expectedUnregistered = ['categoryUpdate'];
        $expectedRegistered = ['actionCategoryUpdate'];
        sort($expectedUnregistered);
        sort($expectedRegistered);
        $unregistered = $module->unregistered;
        $registered = $module->registered;
        sort($unregistered);
        sort($registered);
        $this->assertSame($expectedUnregistered, $unregistered);
        $this->assertSame($expectedRegistered, $registered);
    }

    public function testUpgrade212MigratesProductHooks()
    {
        $module = new UpgradeModuleDouble();
        $this->assertTrue(upgrade_module_2_1_2($module));
        $expectedUnregistered = ['addproduct', 'deleteproduct', 'updateproduct'];
        $expectedRegistered = ['actionProductAdd', 'actionProductDelete', 'actionProductUpdate'];
        sort($expectedUnregistered);
        sort($expectedRegistered);
        $unregistered = $module->unregistered;
        $registered = $module->registered;
        sort($unregistered);
        sort($registered);
        $this->assertSame($expectedUnregistered, $unregistered);
        $this->assertSame($expectedRegistered, $registered);
    }
}

class UpgradeModuleDouble
{
    public $registered = [];
    public $unregistered = [];

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
