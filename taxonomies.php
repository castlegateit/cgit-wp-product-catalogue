<?php

/**
 * Register product taxonomies
 *
 * Products support categories and tags, using the slugs set by the
 * CGIT_PRODUCT_CATEGORY and CGIT_PRODUCT_TAG constants respectively. The
 * cgit_product_category and cgit_product_tag filers can be used to customize
 * the options passed to the register_taxonomy() function.
 */
add_action('init', function() {

    // Category options
    $cat_options = array(
        'labels' => array(
            'name' => 'Categories',
            'singular_name' => 'Category',
        ),
        'hierarchical' => true,
    );

    // Tag options
    $tag_options = array(
        'labels' => array(
            'name' => 'Tags',
            'singular_name' => 'Tag',
        ),
        'hierarchical' => false,
    );

    // Filters
    $cat_options = apply_filters('cgit_product_category', $cat_options);
    $tag_options = apply_filters('cgit_product_tag', $tag_options);

    // Register category taxonomy
    register_taxonomy(
        CGIT_PRODUCT_CATEGORY,
        CGIT_PRODUCT_POST_TYPE,
        $cat_options
    );

    // Register tag taxonomy
    register_taxonomy(
        CGIT_PRODUCT_TAG,
        CGIT_PRODUCT_POST_TYPE,
        $tag_options
    );
});
