<?php

namespace Cgit;

/**
 * Common product catalogue utilities
 *
 * This class can be extended to provide useful common functions to the product
 * catalogue and related plugins.
 */
abstract class ProductUtil
{

    /**
     * Include path for views
     */
    public $viewPath;

    /**
     * Render views
     *
     * Returns the compiled PHP output of a file within the views directory. If
     * the file extension is missing, '.php' will be appended to the file name.
     * The contents of the cart are available to the view files as $cart.
     *
     * The output of can be modified using the cgit_product_render_{name}
     * filter, where the name is the view filename without the extension.
     */
    public function render($view)
    {
        $path = $this->viewPath;

        if (substr($view, -4) != '.php') {
            $view = $view . '.php';
        }

        // Set default view path
        if (!$path) {
            $path = dirname(__FILE__) . '/views';
        }

        if (substr($path, -1) != '/') {
            $path = $path . '/';
        }

        $file = $path . $view;
        $name = substr($view , 0, -4);
        $filter = 'cgit_product_render_' . $name;

        // Check view file exists
        if (!file_exists($file)) {
            return false;
        }

        ob_start();

        include $file;

        $output = ob_get_clean();
        $output = apply_filters($filter, $output);

        return $output;
    }

    /**
     * Format currency
     */
    public static function formatCurrency($num, $after = false, $sep = '')
    {
        $value = number_format($num, 2);
        $str = CGIT_PRODUCT_CURRENCY . $sep . $value;

        if ($after) {
            $str = $value . $sep . CGIT_PRODUCT_CURRENCY;
        }

        return $str;
    }
}
