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

use Cgit\Products\Loader;

define('CGIT_PRODUCT_CATALOGUE_PLUGIN_FILE', __FILE__);

require __DIR__ . '/src/autoload.php';
require __DIR__ . '/activation.php';
require __DIR__ . '/constants.php';

/**
 * Load plugin
 *
 * This uses the plugins_loaded action to control the order in which plugins are
 * loaded. Any plugins depending on this one can be added to the same action
 * with a priority value larger than 10.
 */
add_action('plugins_loaded', function() {
    require __DIR__ . '/functions.php';

    // Initialization
    Loader::getInstance();
}, 10);
