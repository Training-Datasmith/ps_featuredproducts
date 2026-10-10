<?php

class PsFeaturedProductsGetContentTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
        Configuration::updateValue('HOME_FEATURED_NBR', 8);
        Configuration::updateValue('HOME_FEATURED_CAT', 5);
        Configuration::updateValue('HOME_FEATURED_RANDOMIZE', false);
    }

    public function testGetContentWithoutSubmitDoesNotWriteConfiguration()
    {
        $module = TestHarness::newModule();
        $module->getContent();
        $this->assertSame(8, (int) Configuration::get('HOME_FEATURED_NBR'));
        $this->assertSame(5, (int) Configuration::get('HOME_FEATURED_CAT'));
        $this->assertSame(false, (bool) Configuration::get('HOME_FEATURED_RANDOMIZE'));
    }

    /**
     * @dataProvider invalidCountProvider
     */
    public function testInvalidProductCountIsRejected($count)
    {
        Tools::setValues(array(
            'submitHomeFeatured' => '1',
            'HOME_FEATURED_NBR' => $count,
            'HOME_FEATURED_CAT' => '6',
            'HOME_FEATURED_RANDOMIZE' => '0',
        ));
        $module = TestHarness::newModule();
        $output = $module->getContent();
        $this->assertContains('ERROR:', $output);
        $this->assertSame(8, (int) Configuration::get('HOME_FEATURED_NBR'));
    }

    public function testInvalidCategoryIsRejected()
    {
        Tools::setValues(array(
            'submitHomeFeatured' => '1',
            'HOME_FEATURED_NBR' => '4',
            'HOME_FEATURED_CAT' => '0',
            'HOME_FEATURED_RANDOMIZE' => '0',
        ));
        $module = TestHarness::newModule();
        $output = $module->getContent();
        $this->assertContains('ERROR:', $output);
        $this->assertSame(5, (int) Configuration::get('HOME_FEATURED_CAT'));
    }

    public function testInvalidRandomizeFlagIsRejected()
    {
        Tools::setValues(array(
            'submitHomeFeatured' => '1',
            'HOME_FEATURED_NBR' => '4',
            'HOME_FEATURED_CAT' => '6',
            'HOME_FEATURED_RANDOMIZE' => 'yes',
        ));
        $module = TestHarness::newModule();
        $output = $module->getContent();
        $this->assertContains('ERROR:', $output);
        $this->assertSame(false, (bool) Configuration::get('HOME_FEATURED_RANDOMIZE'));
    }

    public function testValidSubmitPersistsCastConfigurationValues()
    {
        Tools::setValues(array(
            'submitHomeFeatured' => '1',
            'HOME_FEATURED_NBR' => '4',
            'HOME_FEATURED_CAT' => '6',
            'HOME_FEATURED_RANDOMIZE' => '1',
        ));
        $module = TestHarness::newModule();
        $output = $module->getContent();
        $this->assertContains('OK:', $output);
        $this->assertSame(4, Configuration::get('HOME_FEATURED_NBR'));
        $this->assertSame(6, Configuration::get('HOME_FEATURED_CAT'));
        $this->assertSame(true, Configuration::get('HOME_FEATURED_RANDOMIZE'));
        $this->assertContains(
            'module:ps_featuredproducts/views/templates/hook/ps_featuredproducts.tpl',
            ModuleStub::$clearCacheTemplates
        );
    }

    public function testGetConfigFieldsValuesCastsStoredConfiguration()
    {
        Configuration::updateValue('HOME_FEATURED_NBR', '8');
        Configuration::updateValue('HOME_FEATURED_CAT', '5');
        Configuration::updateValue('HOME_FEATURED_RANDOMIZE', '0');
        $module = TestHarness::newModule();
        $values = $module->getConfigFieldsValues();
        $this->assertSame(8, $values['HOME_FEATURED_NBR']);
        $this->assertSame(5, $values['HOME_FEATURED_CAT']);
        $this->assertSame(false, $values['HOME_FEATURED_RANDOMIZE']);
    }

    public function invalidCountProvider()
    {
        return array(
            array('08'),
            array('4.2'),
            array('0'),
            array('-3'),
        );
    }
}
