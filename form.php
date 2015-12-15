<?php

/**
 * Function to return basic search form
 *
 * This function returns a sample product search form. This should be considered
 * an example; you will probably need to write custom search forms to suit your
 * site and theme. The `cgit_product_search_form` filters lets you edit or
 * replace the default search form.
 */
function cgit_product_search_form() {
    $cat_tax = get_taxonomy(CGIT_PRODUCT_CATEGORY);
    $tag_tax = get_taxonomy(CGIT_PRODUCT_TAG);
    $cats = get_terms(CGIT_PRODUCT_CATEGORY);
    $tags = get_terms(CGIT_PRODUCT_TAG);

    ob_start();

    include dirname(__FILE__) . '/views/search-form.php';

    $form = ob_get_clean();
    $form = apply_filters('cgit_product_search_form', $form);

    return $form;
}

/**
 * Search form shortcode
 *
 * Uses the cgit_product_search_form() function to embed the default search form
 * in the page content.
 */
add_shortcode('product_search', 'cgit_product_search_form');
