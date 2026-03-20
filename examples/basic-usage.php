<?php

declare(strict_types=1);

/**
 * Example: Using ps_featuredproducts in a PrestaShop theme.
 *
 * The ps_featuredproducts module implements WidgetInterface and displays products
 * from a configured category on the homepage.
 *
 * In a Smarty theme template:
 *   {widget name="ps_featuredproducts"}
 *
 * The module reads its configuration (category ID, number of products, display options)
 * from the database. Configure it in:
 *   Back Office > Modules > Featured Products > Configure
 *
 * To place it in a custom position:
 *   {widget name="ps_featuredproducts" hook_name="displayHome"}
 */

// The module uses PrestaShop's Product Search API internally.
// No direct PHP usage is needed — it's entirely hook/widget driven.

// If you need to call the widget programmatically in a controller:
// $module = Module::getInstanceByName('ps_featuredproducts');
// if ($module && $module->active) {
//     echo $module->renderWidget('displayHome', ['hook_name' => 'displayHome']);
// }
