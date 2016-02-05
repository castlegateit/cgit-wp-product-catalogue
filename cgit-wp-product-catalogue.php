<?php

/*

Plugin Name: Castlegate IT WP Product Catalogue
Plugin URI: http://github.com/castlegateit/cgit-wp-product-catalogue
Description: Flexible product catalogue plugin for WordPress.
Version: 1.2
Author: Castlegate IT
Author URI: http://www.castlegateit.co.uk/
License: MIT

*/

use Cgit\Products\Plugin;
use Cgit\Products\Catalogue;

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
 * Load plugin
 *
 * This uses the plugins_loaded action to control the order in which plugins are
 * loaded. Any plugins depending on this one can be added to the same action
 * with a priority value larger than 10.
 */
add_action('plugins_loaded', function() {
    require __DIR__ . '/src/autoload.php';
    require __DIR__ . '/activation.php';
    require __DIR__ . '/functions.php';

    // Initialization
    Plugin::getInstance();
    Catalogue::getInstance();
}, 10);
