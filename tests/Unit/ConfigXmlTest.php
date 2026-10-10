<?php

class ConfigXmlTest extends PHPUnit_Framework_TestCase
{
    public function testConfigXmlMatchesModuleTechnicalName()
    {
        $xml = simplexml_load_file(dirname(dirname(__DIR__)) . '/config.xml');
        $module = TestHarness::newModule();
        $this->assertSame('ps_featuredproducts', (string) $xml->name);
        $this->assertSame($module->name, (string) $xml->name);
    }
}
