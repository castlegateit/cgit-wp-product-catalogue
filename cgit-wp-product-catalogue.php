<?php

/*

Plugin Name: Castlegate IT WP Product Catalogue
Plugin URI: http://github.com/castlegateit/cgit-wp-product-catalogue
Description: Flexible product catalogue plugin for WordPress.
Version: 1.0
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

/**
 * Constants
 *
 * These constants set the product post type, category, and tag names used to
 * store and query data in WordPress. They also define the currency symbol used
 * by the plugin. These can be overridden by defining the constants first in
 * wp-config.php.
 */
defined('CGIT_PRODUCT_POST_TYPE')
    || define('CGIT_PRODUCT_POST_TYPE', 'product');
defined('CGIT_PRODUCT_CATEGORY')
    || define('CGIT_PRODUCT_CATEGORY', 'product_category');
defined('CGIT_PRODUCT_TAG')
    || define('CGIT_PRODUCT_TAG', 'product_tag');
defined('CGIT_PRODUCT_CURRENCY')
    || define('CGIT_PRODUCT_CURRENCY', '&pound;');

/**
 * ACF is required
 */
register_activation_hook(__FILE__, function() {
    if (!function_exists('acf_add_local_field_group')) {
        $message = 'Plugin activation failed. The Product Catalogue plugin '
            . 'requires <a href="http://www.advancedcustomfields.com/">'
            . 'Advanced Custom Fields</a>.<br /><br /><a href="'
            . admin_url('/plugins.php') . '">Back to Plugins</a>';

        wp_die($message);
    }
});

/**
 * Includes
 */
include dirname(__FILE__) . '/post-type.php';
include dirname(__FILE__) . '/templates.php';
include dirname(__FILE__) . '/taxonomies.php';
include dirname(__FILE__) . '/fields.php';
include dirname(__FILE__) . '/prices.php';
include dirname(__FILE__) . '/product.php';
include dirname(__FILE__) . '/functions.php';
include dirname(__FILE__) . '/query.php';
include dirname(__FILE__) . '/form.php';
include dirname(__FILE__) . '/widget.php';
