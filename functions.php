<?php

use Cgit\Products\Catalogue;
use Cgit\Products\Product;

/**
 * Get product catalogue
 *
 * Provides a function-based interface to the product catalogue class. Returns
 * the single instance of Cgit\ProductCatalogue.
 */
function cgit_product_catalogue() {
    return Catalogue::getInstance();
}

/**
 * Get single product
 *
 * Provides a function-based interface to individual products. Returns a
 * Cgit\Product object for the given post ID.
 */
function cgit_product($id = null) {

    // If ID is not specified, use $post->ID
    if (is_null($id) && isset($GLOBALS['post'])) {
        $id = $GLOBALS['post']->ID;
    }

    return new Product($id);
}

/**
 * Get multiple products
 *
 * Provides a function-based interface to product queries and searches. Returns
 * an array of Cgit\Product objects.
 */
function cgit_products($args) {
    $catalogue = Catalogue::getInstance();
    return $catalogue->products($args);
}
