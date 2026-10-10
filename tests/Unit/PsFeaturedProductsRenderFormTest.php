<?php

class PsFeaturedProductsRenderFormTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
        Configuration::updateValue('PS_LANG_DEFAULT', '3');
        Configuration::updateValue('HOME_FEATURED_CAT', 2);
    }

    public function testRenderFormConfiguresHelperForm()
    {
        $module = TestHarness::newModule();
        $module->renderForm();
        $helper = HelperFormStub::$lastInstance;
        $this->assertFalse($helper->show_toolbar);
        $this->assertSame('submitHomeFeatured', $helper->submit_action);
        $this->assertSame(3, $helper->default_form_language);
        $form = $helper->lastFieldsForm[0]['form'];
        $names = array();
        foreach ($form['input'] as $input) {
            $names[] = $input['name'];
        }
        $this->assertContains('HOME_FEATURED_NBR', $names);
        $this->assertContains('HOME_FEATURED_CAT', $names);
        $this->assertContains('HOME_FEATURED_RANDOMIZE', $names);
        $this->assertSame('home_featured_category', $form['input'][1]['tree']['id']);
        $this->assertSame(1, $form['input'][2]['values'][0]['value']);
        $this->assertSame(0, $form['input'][2]['values'][1]['value']);
    }
}
