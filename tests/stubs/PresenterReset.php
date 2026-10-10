<?php

class ProductListingPresenterStub
{
    public static function reset()
    {
        \PrestaShop\PrestaShop\Core\Product\ProductListingPresenter::$presentCount = 0;
        \PrestaShop\PrestaShop\Adapter\Presenter\Product\ProductListingPresenter::$presentCount = 0;
    }
}
