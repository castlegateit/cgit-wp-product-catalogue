<?php

/**
 * Register product post type
 *
 * Products all the default WP fields types, including comments. Additional
 * fields are added with ACF (if available). The post type slug is set with the
 * CGIT_PRODUCT_POST_TYPE constant. The cgit_product_post_type filter allows you
 * to customize the options passed to the register_post_type() function.
 */
add_action('init', function() {

    // Labels
    $labels = array(
        'name' => 'Products',
        'singular_name' => 'Product',
        'add_new_item' => 'Add New Product',
        'edit_item' => 'Edit Product',
        'new_item' => 'New Product',
        'view_item' => 'View Product',
        'search_items' => 'Search Products',
        'not_found' => 'No products found.',
        'not_found_in_trash' => 'No products found in Trash.',
    );

    // Features
    $supports = array(
        'title',
        'editor',
        'excerpt',
        'thumbnail',
        'comments', // use comments for reviews
    );

    // Options
    $options = array(
        'labels' => $labels,
        'supports' => $supports,
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-cart',
    );

    // Filters
    $options = apply_filters('cgit_product_post_type', $options);

    // Register post type
    register_post_type(CGIT_PRODUCT_POST_TYPE, $options);
});
