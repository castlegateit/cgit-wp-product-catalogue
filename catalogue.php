<?php

namespace Cgit;

/**
 * Product catalogue
 *
 * The product catalogue extends the Cgit\ProductUtil class, which provides
 * basic methods for rendering views and formatting currency values.
 */
class ProductCatalogue extends ProductUtil
{

    /**
     * Reference to the singleton instance of the class
     */
    private static $instance;

    /**
     * Default query parameters
     *
     * The array of default search parameters used in various functions and
     * filters.
     */
    public $queryArgs = array(
        'match_any' => false,
        'min_price' => false,
        'max_price' => false,
        'inc_vat' => false,
        'featured' => false,
        'discount' => false,
        'cat_code' => false,
        'stock' => false,
    );

    /**
     * Constructor
     *
     * Private constructor ...
     */
    private function __construct()
    {
        // Apply filters
        $this->queryArgs = apply_filters(
            'cgit_product_default_args',
            $this->queryArgs
        );

        // Register product query variables and set query parameters
        add_filter('query_vars', array($this, 'registerQueryVars'));
        add_filter('pre_get_posts', array($this, 'setQueryVars'));
    }

    /**
     * Return the singleton instance of the class
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * Assemble product meta query
     *
     * Given an array that describes a search for products meeting particular
     * criteria, which is compatible with the array of options passed to
     * get_posts(), this returns a meta query array that can be used with
     * get_posts() or WP_Query.
     */
    public function metaQuery($args)
    {
        // Default values
        $args = array_merge($this->queryArgs, $args);

        // Return filtered meta query
        return apply_filters('cgit_product_meta_query', array(), $args);
    }

    /**
     * Register product query variables
     *
     * Custom query variables must be added to WP_Query before they can be
     * accessed by other functions and filters.
     */
    public function registerQueryVars($vars)
    {
        return array_merge($vars, array_keys($this->queryArgs));
    }

    /**
     * Set product query parameters
     *
     * If searching the product post type, allow additional search parameters to
     * filter by product details.
     */
    public function setQueryVars($query)
    {
        // Filter should only affect product search
        if (
            !isset($query->query['post_type']) ||
            $query->query['post_type'] != CGIT_PRODUCT_POST_TYPE
        ) {
            return $query;
        }

        // Product search parameters and default values
        $args = $this->queryArgs;

        // Get search parameters from query string
        foreach ($args as $key => $value) {
            $args[$key] = get_query_var($key, false);
        }

        // Convert search parameters into WP meta query
        $meta_query = $this->metaQuery($args);

        // Add meta query to main search query
        $query->set('meta_query', $meta_query);

        // Set default search order (featured, then alphabetically by name)
        if (!get_query_var('orderby')) {
            $order = array(
                'meta_value_num' => 'DESC',
                'title' => 'ASC',
            );

            $query->set('orderby', $order);
            $query->set('meta_key', 'featured');
        }

        // Allow search to be ordered by price
        if (get_query_var('orderby') == 'price') {
            $query->set('orderby', 'meta_value_num');
            $query->set('meta_key', 'price');
        }

        // Allow separate product pagination. By default, the number of
        // products per page is taken from the WordPress settings.
        if (defined('CGIT_PRODUCT_PER_PAGE')) {
            $query->set('posts_per_page', CGIT_PRODUCT_PER_PAGE);
        }

        // Return query
        return $query;
    }

    /**
     * Get products
     *
     * This uses get_posts() to return an array of posts that use the product
     * post type, as set with the CGIT_PRODUCT_POST_TYPE constant. Any option
     * that could be used with get_posts() can be used here, except post_type.
     *
     * Additional arguments: min_price, max_price, inc_vat, featured, discount,
     * cat_code, and stock. You can also use the match_any option to use an 'OR'
     * relationship between meta queries instead of the default 'AND'
     * relationship.
     */
    public function products($args)
    {
        // Amend options for product type and fields
        $args['post_type'] = CGIT_PRODUCT_POST_TYPE;
        $args['meta_query'] = $this->metaQuery($args);

        if (isset($args['orderby']) && $args['orderby'] == 'price') {
            $args['orderby'] = 'meta_value_num';
            $args['meta_key'] = 'price';
        }

        // Get posts
        $items = get_posts($args);

        // Generate list of product objects instead of default WP_Post objects
        $products = array();

        foreach ($items as $item) {
            $products[] = new Product($item->ID);
        }

        return $products;
    }
}
