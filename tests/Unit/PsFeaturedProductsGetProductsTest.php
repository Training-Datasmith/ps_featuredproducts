<?php

class PsFeaturedProductsGetProductsTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
        TestHarness::reset();
    }

    public function testRandomizeUsesRandomSortOrder()
    {
        $result = $this->runGetProductsSubprocess('1.7.8.0', 'modern', '8', '4', '1');
        $this->assertSame('random', $result['sortDirection']);
        $this->assertSame('', $result['sortEntity']);
        $this->assertSame('', $result['sortField']);
    }

    public function testCategoryConstructorCastsConfigurationValue()
    {
        $result = $this->runGetProductsSubprocess('1.7.8.0', 'modern', '8', '12', '0');
        $this->assertSame(12, $result['categoryIds'][0]);
    }

    public function testLegacyPresenterAndPerProductAssemblyBelow175()
    {
        $result = $this->runGetProductsSubprocess('1.7.4.4', 'legacy-assembler', '8', '4', '0');
        $this->assertSame(0, $result['adapterPresenterCount']);
        $this->assertSame(1, $result['corePresenterCount']);
        $this->assertSame(0, $result['assembleProductsCalls']);
        $this->assertSame(1, $result['assembleProductCalls']);
    }

    private function runGetProductsSubprocess($psVersion, $assemblerMode, $nbr, $cat, $randomize)
    {
        $repoRoot = dirname(dirname(__DIR__));
        $script = $repoRoot . '/tests/subprocess-run-get-products.php';
        $args = array(
            escapeshellarg(PHP_BINARY),
            escapeshellarg($script),
            escapeshellarg($psVersion),
            escapeshellarg($assemblerMode),
            escapeshellarg($nbr),
            escapeshellarg($cat),
            escapeshellarg($randomize),
        );
        $command = implode(' ', $args);
        $json = shell_exec($command);
        $this->assertNotEmpty($json);

        return json_decode($json, true);
    }
}
