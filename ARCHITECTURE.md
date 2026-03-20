# Architecture: ps_featuredproducts

## Purpose

A PrestaShop front-office module that displays a configurable selection of featured products
on the homepage. Products can be filtered from a specific category, with configurable
count and display options.

## Directory Structure

```
ps_featuredproducts.php   # Main module class (Ps_FeaturedProducts extends Module implements WidgetInterface)
views/templates/           # Smarty/Twig templates for product grid/list display
translations/              # Translation files
upgrade/                   # Database migration scripts
tests/                     # PHPStan and unit tests
```

## Key Design Decisions

Uses PrestaShop's Product Search API (`ProductSearchProvider`, `ProductSearchQuery`) to
query products from a configured category. Integrates `ImageRetriever`, `PriceFormatter`,
and `ProductColorsRetriever` for rich product card data. Implements `WidgetInterface` for
flexible placement in the theme.

## Extension Points

- Configure the source category and number of products in the module back-office settings.
- Override templates in the active theme's `modules/ps_featuredproducts/` directory for
  custom product card layout.
