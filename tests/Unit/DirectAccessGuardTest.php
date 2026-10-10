<?php

class DirectAccessGuardTest extends PHPUnit_Framework_TestCase
{
    private static $guardFiles = array(
        'index.php',
        'translations/index.php',
        'views/index.php',
        'views/templates/index.php',
        'views/templates/hook/index.php',
        'tests/index.php',
        'tests/phpstan/index.php',
        'tests/Unit/index.php',
        'ps_featuredproducts.php',
        'upgrade/upgrade-2.1.1.php',
        'upgrade/upgrade-2.1.2.php',
    );

    /**
     * @dataProvider guardFileProvider
     */
    public function testGuardFileWithoutPsVersionDoesNotReachAfterSentinel($relativePath)
    {
        $repoRoot = dirname(dirname(__DIR__));
        $target = $repoRoot . '/' . $relativePath;
        $wrapper = $repoRoot . '/tests/include-wrapper.php';
        $php = PHP_BINARY;
        $command = escapeshellarg($php) . ' ' . escapeshellarg($wrapper) . ' ' . escapeshellarg($target);
        $output = shell_exec($command);
        $this->assertContains('PS_FEATUREDPRODUCTS_SENTINEL_BEFORE', $output);
        $this->assertNotContains('PS_FEATUREDPRODUCTS_SENTINEL_AFTER', $output);
    }

    /**
     * @dataProvider versionGuardProvider
     */
    public function testVersionGuardAllowsLoadWhenPsVersionDefined($relativePath, $symbolCheck)
    {
        $repoRoot = dirname(dirname(__DIR__));
        $target = $repoRoot . '/' . $relativePath;
        $script = $this->buildGuardBootstrapScript($target, $symbolCheck);
        $php = PHP_BINARY;
        $command = escapeshellarg($php) . ' ' . escapeshellarg($script);
        $output = shell_exec($command);
        $this->assertContains('PS_FEATUREDPRODUCTS_SENTINEL_BEFORE', $output);
        $this->assertContains('PS_FEATUREDPRODUCTS_SENTINEL_AFTER', $output);
        $this->assertContains('SYMBOL_OK', $output);
    }

    public function guardFileProvider()
    {
        $rows = array();
        foreach (self::$guardFiles as $path) {
            $rows[] = array($path);
        }

        return $rows;
    }

    public function versionGuardProvider()
    {
        return array(
            array('ps_featuredproducts.php', 'class_exists(\'Ps_FeaturedProducts\')'),
            array('upgrade/upgrade-2.1.1.php', 'function_exists(\'upgrade_module_2_1_1\')'),
            array('upgrade/upgrade-2.1.2.php', 'function_exists(\'upgrade_module_2_1_2\')'),
        );
    }

    private function buildGuardBootstrapScript($target, $symbolCheck)
    {
        $dir = sys_get_temp_dir() . '/ps-featured-guard-' . md5($target . $symbolCheck);
        if (!is_dir($dir)) {
            mkdir($dir, 0700, true);
        }
        $script = $dir . '/run.php';
        $repoRoot = dirname(dirname(__DIR__));
        $code = '<?php
define(\'_PS_VERSION_\', \'1.7.8.0\');
require ' . var_export($repoRoot . '/tests/bootstrap-stubs.php', true) . ';
fwrite(STDOUT, "PS_FEATUREDPRODUCTS_SENTINEL_BEFORE\n");
include ' . var_export($target, true) . ';
if (' . $symbolCheck . ') {
    fwrite(STDOUT, "SYMBOL_OK\n");
}
fwrite(STDOUT, "PS_FEATUREDPRODUCTS_SENTINEL_AFTER\n");
';
        file_put_contents($script, $code);

        return $script;
    }
}
