<?php

/**
 * Add product query variables
 *
 * Custom query variables must be added to WP_Query before they can be accessed
 * by other functions and filters.
 */
add_filter('query_vars', function($vars) {
    return array_merge($vars, array_keys(cgit_product_default_args()));
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
    $args = cgit_product_default_args();

    // Get search parameters from query string
    foreach ($args as $key => $value) {
        $args[$key] = get_query_var($key, false);
    }

    // Convert search parameters into WP meta query
    $meta_query = cgit_product_meta_query($args);

    // Add meta query to main search query
    $query->set('meta_query', $meta_query);

    // Allow search to be ordered by price
    if (get_query_var('orderby') == 'price') {
        $query->set('orderby', 'meta_value_num');
        $query->set('meta_key', 'price');
    }

    // Allow separate product pagination. By default, the number of products
    // per page is taken from the WordPress settings.
    if (defined('CGIT_PRODUCT_PER_PAGE')) {
        $query->set('posts_per_page', CGIT_PRODUCT_PER_PAGE);
    }

    // Return query
    return $query;
}, 10);

/**
 * Default sort order and pagination
 *
 * This function sets the default sort order of products in the main product
 * archive. This does not affect product searches. The default order is:
 * featured first, then sorted alphabetically by name.
 */
add_filter('pre_get_posts', function($query) {

    // Filter only affects main product archive
    if (!is_search() && !is_post_type_archive(CGIT_PRODUCT_POST_TYPE)) {
        return;
    }

    // Set default sort order: featured first, then alphabetical
    $order = array(
        'meta_value_num' => 'DESC',
        'title' => 'ASC',
    );

    $query->set('orderby', $order);
    $query->set('meta_key', 'featured');

    return $query;
}, 20);
