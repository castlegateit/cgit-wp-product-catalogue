<?php

/**
 * Allow separate product search template
 *
 * If the search is restricted to the product post type and a template called
 * search-product.php exists, use that template instead of search.php for
 * product searches.
 */
add_filter('search_template', function($templates = '') {
    if (get_query_var('post_type') != CGIT_PRODUCT_POST_TYPE) {
        return $templates;
    }

    $custom = locate_template('search-' . CGIT_PRODUCT_POST_TYPE . '.php');

    if ($custom) {
        return $custom;
    }

    return $templates;
});

/**
 * Fix empty title
 *
 * Product searches might not include a general search term, which means they do
 * not have a standard WP title. This function uses the generic product post
 * type name as the title instead.
 */
add_filter('wp_title', function($title, $sep, $location) {
    $is_product = is_search()
        && get_query_var('post_type') == CGIT_PRODUCT_POST_TYPE
        && !get_query_var('s');

    if (!$is_product) {
        return $title;
    }

    // Get post type label
    $obj = get_post_type_object(CGIT_PRODUCT_POST_TYPE);
    $name = $obj->labels->name;

    if ($location == 'right') {
        $title = $name . ' ' . $sep . ' ';
    } else {
        $title = ' ' . $sep . ' ' . $name;
    }

    return $title;
}, 5, 3);
