<?php

/**
 * Add product query variables
 *
 * Custom query variables must be added to WP_Query before they can be accessed
 * by other functions and filters.
 */
add_filter('query_vars', function($vars) {
    return array_merge($vars, array_keys(get_product_default_args()));
});

/**
 * Product query parameters
 *
 * If searching the product post type, allow additional search parameters to
 * filter by product details.
 */
add_filter('pre_get_posts', function($query) {

    // Filter should only affect product search
    if (get_query_var('post_type') != CGIT_PRODUCT_POST_TYPE) {
        return;
    }

    // Product search parameters and default values
    $args = get_product_default_args();

    // Get search parameters from query string
    foreach ($args as $key => $value) {
        $args[$key] = get_query_var($key, false);
    }

    // Convert search parameters into WP meta query
    $meta_query = get_product_meta_query($args);

    // Add meta query to main search query
    $query->set('meta_query', $meta_query);

    // Allow search to be ordered by price
    if (get_query_var('orderby') == 'price') {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', 'price');
    }

    // Return query
    return $query;
});
