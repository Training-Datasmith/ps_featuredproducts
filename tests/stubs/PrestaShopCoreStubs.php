<?php

namespace PrestaShop\PrestaShop\Core\Module;

interface WidgetInterface
{
}

namespace PrestaShop\PrestaShop\Core\Product\Search;

class ProductSearchContext
{
    public function __construct($context)
    {
    }
}

class ProductSearchQuery
{
    private $resultsPerPage;
    private $page;
    private $sortOrder;

    public function setResultsPerPage($n)
    {
        $this->resultsPerPage = $n;

        return $this;
    }

    public function setPage($page)
    {
        $this->page = $page;

        return $this;
    }

    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }

    public function getPage()
    {
        return $this->page;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}

class SortOrder
{
    private $entity;
    private $field;
    private $direction;

    public function __construct($entity, $field, $direction = 'asc')
    {
        $this->entity = $entity;
        $this->field = $field;
        $this->direction = strtolower($direction);
    }

    public static function random()
    {
        return new self('', '', 'random');
    }

    public function getEntity()
    {
        return $this->entity;
    }

    public function getField()
    {
        return $this->field;
    }

    public function getDirection()
    {
        return $this->direction;
    }
}

class ProductSearchResultStub
{
    private $products;

    public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function getProducts()
    {
        return $this->products;
    }
}

namespace PrestaShop\PrestaShop\Adapter\Category;

class CategoryProductSearchProvider
{
    public static $lastQuery;
    public static $productsToReturn = [['id_product' => 1]];

    public function __construct($translator, $category)
    {
    }

    public static function reset()
    {
        self::$lastQuery = null;
        self::$productsToReturn = [['id_product' => 1]];
    }

    public function runQuery($context, $query)
    {
        self::$lastQuery = $query;

        return new \PrestaShop\PrestaShop\Core\Product\Search\ProductSearchResultStub(self::$productsToReturn);
    }
}

namespace PrestaShop\PrestaShop\Adapter\Image;

class ImageRetriever
{
    public function __construct($link)
    {
    }
}

namespace PrestaShop\PrestaShop\Adapter\Product;

class PriceFormatter
{
}

class ProductColorsRetriever
{
}

namespace PrestaShop\PrestaShop\Core\Product;

class ProductListingPresenter
{
    public static $presentCount = 0;

    public function __construct()
    {
    }

    public function present($settings, $product, $language)
    {
        ++self::$presentCount;

        return ['id' => isset($product['id_product']) ? $product['id_product'] : 0];
    }
}

namespace PrestaShop\PrestaShop\Adapter\Presenter\Product;

class ProductListingPresenter
{
    public static $presentCount = 0;

    public function __construct()
    {
    }

    public function present($settings, $product, $language)
    {
        ++self::$presentCount;

        return ['id' => isset($product['id_product']) ? $product['id_product'] : 0];
    }
}
