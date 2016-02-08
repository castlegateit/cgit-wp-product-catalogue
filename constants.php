<?php

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
