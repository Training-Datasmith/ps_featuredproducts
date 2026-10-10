<?php

putenv('PS_FEATURED_PS_VERSION=' . $argv[1]);
if (isset($argv[2]) && $argv[2] === 'legacy-assembler') {
    putenv('PS_FEATURED_BULK=0');
}
require dirname(__FILE__) . '/bootstrap-stubs.php';
require dirname(__DIR__) . '/ps_featuredproducts.php';

Configuration::updateValue('HOME_FEATURED_NBR', $argv[3]);
Configuration::updateValue('HOME_FEATURED_CAT', $argv[4]);
Configuration::updateValue('HOME_FEATURED_RANDOMIZE', $argv[5]);

$module = new Ps_FeaturedProducts();
$reflection = new ReflectionClass($module);
$method = $reflection->getMethod('getProducts');
$method->setAccessible(true);
$method->invoke($module);

$query = \PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider::$lastQuery;
$sort = $query->getSortOrder();

echo json_encode([
    'resultsPerPage' => $query->getResultsPerPage(),
    'page' => $query->getPage(),
    'sortEntity' => $sort->getEntity(),
    'sortField' => $sort->getField(),
    'sortDirection' => $sort->getDirection(),
    'categoryIds' => CategoryStub::$constructedIds,
    'corePresenterCount' => \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter::$presentCount,
    'adapterPresenterCount' => \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter::$presentCount,
    'assembleProductsCalls' => ProductAssemblerStub::$assembleProductsCalls,
    'assembleProductCalls' => ProductAssemblerStub::$assembleProductCalls,
]);
