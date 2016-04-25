<?php

/**
 * ACF is required
 */
register_activation_hook(CGIT_PRODUCT_CATALOGUE_PLUGIN_FILE, function() {
    if (!function_exists('acf_add_local_field_group')) {
        $message = 'Plugin activation failed. The Product Catalogue plugin '
            . 'requires <a href="http://www.advancedcustomfields.com/">'
            . 'Advanced Custom Fields</a>.<br /><br /><a href="'
            . admin_url('/plugins.php') . '">Back to Plugins</a>';

        wp_die($message);
    }
});
