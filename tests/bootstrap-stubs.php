<?php

if (!defined('_PS_VERSION_')) {
    if (getenv('PS_FEATURED_PS_VERSION')) {
        define('_PS_VERSION_', getenv('PS_FEATURED_PS_VERSION'));
    } else {
        define('_PS_VERSION_', '1.7.8.0');
    }
}

require_once __DIR__ . '/stubs/Configuration.php';
require_once __DIR__ . '/stubs/Tools.php';
require_once __DIR__ . '/stubs/Validate.php';
require_once __DIR__ . '/stubs/Context.php';
require_once __DIR__ . '/stubs/SupportStubs.php';
require_once __DIR__ . '/stubs/ModuleStub.php';
require_once __DIR__ . '/stubs/PrestaShopCoreStubs.php';
require_once __DIR__ . '/stubs/PresenterReset.php';
if (getenv('PS_FEATURED_BULK') === '0') {
    require_once __DIR__ . '/stubs/ProductAssemblerLegacy.php';
} else {
    require_once __DIR__ . '/stubs/ProductAssemblerStub.php';
}
