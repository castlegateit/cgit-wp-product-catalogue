<?php

/**
 * Get product catalogue
 *
 * Provides a function-based interface to the product catalogue class. Returns
 * the single instance of Cgit\ProductCatalogue.
 */
function cgit_product_catalogue() {
    return Cgit\ProductCatalogue::getInstance();
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

    return new Cgit\Product($id);
}

/**
 * Get multiple products
 *
 * This uses get_posts() to return an array of posts that use the product post
 * type, as set with the CGIT_PRODUCT_POST_TYPE constant. Any option that could
 * be used with get_posts() can be used here, except post_type.
 *
 * Additional arguments: min_price, max_price, inc_vat, featured, discount,
 * cat_code, and stock. You can also use the match_any option to use an 'OR'
 * relationship between meta queries instead of the default 'AND' relationship.
 */
function cgit_products($args) {
    $catalogue = Cgit\ProductCatalogue::getInstance();

    // Amend options for product type and fields
    $args['post_type'] = CGIT_PRODUCT_POST_TYPE;
    $args['meta_query'] = $catalogue->metaQuery($args);

    if (isset($args['orderby']) && $args['orderby'] == 'price') {
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'price';
    }

    // Get posts
    $items = get_posts($args);

    // Generate list of product objects instead of default WP_Post objects
    $products = array();

    foreach ($items as $item) {
        $products[] = new Cgit\Product($item->ID);
    }

    return $products;
}
