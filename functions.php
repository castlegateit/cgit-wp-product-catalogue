<?php

/**
 * Get single product
 *
 * Returns a Cgit\Product object for the given post ID.
 */
function cgit_product($id = null) {

    // If ID is not specified, use $post->ID
    if (is_null($id) && isset($GLOBALS['post'])) {
        $id = $GLOBALS['post']->ID;
    }

    return new Cgit\Product($id);
}

/**
 * Return default product search parameters
 *
 * The array of default product search parameters is used in several functions
 * and filters. It should be a constant, but this will not be possible until
 * PHP 7.
 */
function cgit_product_default_args() {
    return array(
        'match_any' => false,
        'min_price' => false,
        'max_price' => false,
        'inc_vat' => false,
        'featured' => false,
        'discount' => false,
        'cat_code' => false,
        'stock' => false,
    );
}

/**
 * Assemble product meta query
 *
 * Given an array that describes a search for products meeting particular
 * criteria, which is compatible with the array of options passed to
 * get_posts(), this returns a meta query array that can be used with
 * get_posts() or WP_Query.
 */
function cgit_product_meta_query($args) {

    // Default values
    $args = array_merge(cgit_product_default_args(), $args);

    // Meta query
    $meta_query = array();

    // The default meta query uses an 'AND' relationship. Set match_any to
    // true to use an 'OR' relationship.
    if ($args['match_any']) {
        $meta_query['relation'] = 'OR';
    }

    // Price restrictions (uses the discounted price, not the original)
    if ($args['min_price'] && $args['max_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => array($args['min_price'], $args['max_price']),
            'compare' => 'BETWEEN',
        );
    } elseif ($args['min_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => $args['min_price'],
            'compare' => '>=',
        );
    } elseif ($args['max_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => $args['max_price'],
            'compare' => '<=',
        );
    }

    // Price includes VAT
    if ($args['inc_vat']) {
        $meta_query[] = array(
            'key' => 'inc_vat',
            'value' => 1,
        );
    }

    // Featured products
    if ($args['featured']) {
        $meta_query[] = array(
            'key' => 'featured',
            'value' => 1,
        );
    }

    // Discounted products
    if ($args['discount']) {
        $meta_query[] = array(
            'key' => 'discount',
            'value' => array('amount', 'percent'),
            'compare' => 'IN',
        );
    }

    // Catalogue code
    if ($args['cat_code']) {
        $meta_query[] = array(
            'key' => 'cat_code',
            'value' => $args['cat_code'],
            'compare' => '=',
        );
    }

    // Products in stock (or N products in stock)
    if ($args['stock']) {
        if ($args > 1) {
            $meta_query[] = array(
                'key' => 'stock',
                'type' => 'numeric',
                'value' => $args['stock'],
                'compare' => '>',
            );
        } else {
            $meta_query[] = array(
                'key' => 'stock',
                'type' => 'numeric',
                'value' => 0,
                'compare' => '>',
            );
        }
    }

    return $meta_query;
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

    // Amend options for product type and fields
    $args['post_type'] = CGIT_PRODUCT_POST_TYPE;
    $args['meta_query'] = cgit_product_meta_query($args);

    if (isset($args) && $args['orderby'] == 'price') {
        $args['orderby'] = 'meta_value_num';
        $args['meta_key'] = 'price';
    }

    // Get posts
    $items = cgit_posts($args);

    // Generate list of product objects instead of default WP_Post objects
    $products = array();

    foreach ($items as $item) {
        $products[] = cgit_product($item->ID);
    }

    return $products;
}
