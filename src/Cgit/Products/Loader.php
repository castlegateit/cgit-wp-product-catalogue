<?php

namespace Cgit\Products;

/**
 * Loader class
 *
 * Register the post types, templates, fields, query parameters, and widgets
 * required by the plugin and initialize the Catalogue instance.
 */
class Loader
{
    /**
     * Reference to the singleton instance of the class
     */
    private static $instance;

    /**
     * Constructor
     */
    private function __construct()
    {
        // Register post type and settings
        add_action('init', [$this, 'registerPostType']);
        add_filter('init', [$this, 'registerTaxonomies']);
        add_filter('init', [$this, 'registerFields']);

        // Edit search template
        add_filter('search_template', [$this, 'registerSearchTemplate']);
        add_filter('wp_title', [$this, 'fixSearchTitle'], 5, 3);

        // Apply price discounts on save
        add_action('acf/save_post', [$this, 'saveDiscountedPrice'], 20);

        // Set default product meta query
        add_filter('cgit_product_meta_query', [$this, 'setMetaQuery'], 10, 2);

        // Register widgets
        add_action('widgets_init', [$this, 'registerWidgets']);

        // Initialize catalogue
        Catalogue::getInstance();
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
     * Register product post type
     *
     * Products all the default WP fields types, including comments. Additional
     * fields are added with ACF (if available). The post type slug is set with
     * the CGIT_PRODUCT_POST_TYPE constant. The cgit_product_post_type filter
     * allows you to customize the options passed to the register_post_type()
     * function.
     */
    public function registerPostType()
    {
        // Labels
        $labels = [
            'name' => 'Products',
            'singular_name' => 'Product',
            'add_new_item' => 'Add New Product',
            'edit_item' => 'Edit Product',
            'new_item' => 'New Product',
            'view_item' => 'View Product',
            'search_items' => 'Search Products',
            'not_found' => 'No products found.',
            'not_found_in_trash' => 'No products found in Trash.',
        ];

        // Features
        $supports = [
            'title',
            'editor',
            'excerpt',
            'thumbnail',
            'comments', // use comments for reviews
        ];

        // Options
        $options = [
            'labels' => $labels,
            'supports' => $supports,
            'public' => true,
            'has_archive' => true,
            'menu_icon' => 'dashicons-cart',
        ];

        // Filters
        $options = apply_filters('cgit_product_post_type', $options);

        // Register post type
        register_post_type(CGIT_PRODUCT_POST_TYPE, $options);
    }

    /**
     * Allow separate product search template
     *
     * If the search is restricted to the product post type and a template
     * called search-product.php exists, use that template instead of search.php
     * for product searches.
     */
    public function registerSearchTemplate($templates = '')
    {
        if (get_query_var('post_type') != CGIT_PRODUCT_POST_TYPE) {
            return $templates;
        }

        $custom = locate_template('search-' . CGIT_PRODUCT_POST_TYPE . '.php');

        if ($custom) {
            return $custom;
        }

        return $templates;
    }

    /**
     * Fix empty title
     *
     * Product searches might not include a general search term, which means
     * they do not have a standard WP title. This function uses the generic
     * product post type name as the title instead.
     */
    public function fixSearchTitle($title, $sep, $location)
    {
        $is_product = is_search()
            && get_query_var('post_type') == CGIT_PRODUCT_POST_TYPE
            && !get_query_var('s');

        if (!$is_product) {
            return $title;
        }

        // Get post type label
        $obj = get_post_type_object(CGIT_PRODUCT_POST_TYPE);
        $name = $obj->labels->name;

        if ($location == 'right') {
            $title = $name . ' ' . $sep . ' ';
        } else {
            $title = ' ' . $sep . ' ' . $name;
        }

        return $title;
    }

    /**
     * Register product taxonomies
     *
     * Products support categories and tags, using the slugs set by the
     * CGIT_PRODUCT_CATEGORY and CGIT_PRODUCT_TAG constants respectively. The
     * cgit_product_category and cgit_product_tag filers can be used to
     * customize the options passed to the register_taxonomy() function.
     */
    public function registerTaxonomies()
    {
        // Category options
        $cat_options = [
            'labels' => [
                'name' => 'Categories',
                'singular_name' => 'Category',
            ],
            'hierarchical' => true,
        ];

        // Tag options
        $tag_options = [
            'labels' => [
                'name' => 'Tags',
                'singular_name' => 'Tag',
            ],
            'hierarchical' => false,
        ];

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
    }

    /**
     * Define fields
     *
     * Add custom fields to each product for price, discounts, images, and other
     * details. Products can have zero or more variants, with separate
     * descriptions and images.
     *
     * Currency values are stored as numbers. The currency symbol displayed in
     * the WP admin panel is set with the CGIT_PRODUCT_CURRENCY constant.
     *
     * There are three groups of fields: the main product fields, the product
     * variant fields, and the related product fields. The options passed to the
     * acf_add_local_field_group() function for each of these groups can be
     * customized with the cgit_product_fields, cgit_product_variant_fields, and
     * cgit_product_related_fields respectively.
     *
     * An additional field for the price with any discount applied is also saved
     * using the acf/save_post filter. This field is not accessible from the WP
     * admin panel.
     */
    public function registerFields()
    {
        // Location settings for all field groups
        $location = [
            [
                [
                    'param' => 'post_type',
                    'operator' => '==',
                    'value' => CGIT_PRODUCT_POST_TYPE,
                ],
            ],
        ];

        // Fields
        $fields = [
            'key' => 'cgit_product',
            'title' => 'Product details',
            'location' => $location,
            'position' => 'normal',
            'fields' => [
                [
                    'label' => 'Price',
                    'name' => 'price_original',
                    'key' => 'price_original',
                    'type' => 'number',
                    'required' => true,
                    'prepend' => CGIT_PRODUCT_CURRENCY,
                    'wrapper' => [
                        'width' => '50%',
                    ],
                ],
                [
                    'label' => 'Inc. VAT?',
                    'name' => 'inc_vat',
                    'key' => 'inc_vat',
                    'type' => 'true_false',
                    'default_value' => '1',
                    'message' => 'Price includes VAT',
                    'wrapper' => [
                        'width' => '25%',
                    ],
                ],
                [
                    'label' => 'Featured?',
                    'name' => 'featured',
                    'key' => 'featured',
                    'type' => 'true_false',
                    'message' => 'This is a featured product',
                    'wrapper' => [
                        'width' => '25%',
                    ],
                ],
                [
                    'label' => 'Discount',
                    'name' => 'discount',
                    'key' => 'discount',
                    'type' => 'radio',
                    'choices' => [
                        'none' => 'None',
                        'amount' => 'Amount (' . CGIT_PRODUCT_CURRENCY . ')',
                        'percent' => 'Percent (%)',
                    ],
                ],
                [
                    'label' => 'Discount amount',
                    'name' => 'discount_amount',
                    'key' => 'discount_amount',
                    'type' => 'number',
                    'prepend' => CGIT_PRODUCT_CURRENCY,
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'discount',
                                'operator' => '==',
                                'value' => 'amount',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Discount percent',
                    'name' => 'discount_percent',
                    'key' => 'discount_percent',
                    'type' => 'number',
                    'append' => '%',
                    'conditional_logic' => [
                        [
                            [
                                'field' => 'discount',
                                'operator' => '==',
                                'value' => 'percent',
                            ],
                        ],
                    ],
                ],
                [
                    'label' => 'Image gallery',
                    'name' => 'gallery',
                    'key' => 'gallery',
                    'type' => 'gallery',
                    'instructions' => 'Use the Featured Image field on the'
                        . ' right of the screen to set the main image.',
                ],
                [
                    'label' => 'Catalogue number or code',
                    'name' => 'cat_code',
                    'key' => 'cat_code',
                    'type' => 'text',
                    'instructions' => 'A unique identifier, catalogue number,'
                        . ' etc. (optional).',
                    'wrapper' => [
                        'width' => '50%',
                    ],
                ],
                [
                    'label' => 'Number in stock',
                    'name' => 'stock',
                    'key' => 'stock',
                    'type' => 'number',
                    'instructions' => 'Number of items in stock (optional).',
                    'wrapper' => [
                        'width' => '50%',
                    ],
                ],
            ],
        ];

        // Variant fields
        $var_fields = [
            'key' => 'cgit_product_variants',
            'title' => 'Product variants',
            'location' => $location,
            'position' => 'normal',
            'fields' => [
                [
                    'label' => 'Variants',
                    'name' => 'variants',
                    'key' => 'variants',
                    'type' => 'repeater',
                    'button_label' => 'Add Variant',
                    'layout' => 'block',
                    'sub_fields' => [
                        [
                            'label' => 'Name',
                            'name' => 'variant_name',
                            'key' => 'variant_name',
                            'type' => 'text',
                            'required' => true,
                        ],
                        [
                            'label' => 'Description',
                            'name' => 'variant_description',
                            'key' => 'variant_description',
                            'type' => 'textarea',
                            'rows' => 4,
                        ],
                        [
                            'label' => 'Variant image gallery?',
                            'name' => 'variant_has_gallery',
                            'key' => 'variant_has_gallery',
                            'type' => 'true_false',
                            'message' => 'This product variant has its own'
                                . ' images',
                        ],
                        [
                            'label' => 'Image gallery',
                            'name' => 'variant_gallery',
                            'key' => 'variant_gallery',
                            'type' => 'gallery',
                            'conditional_logic' => [
                                [
                                    [
                                        'field' => 'variant_has_gallery',
                                        'operator' => '==',
                                        'value' => 1,
                                    ],
                                ],
                            ],
                        ],
                        [
                            'label' => 'Variant catalogue number or code',
                            'name' => 'variant_cat_code',
                            'key' => 'variant_cat_code',
                            'type' => 'text',
                        ],
                    ],
                ],
            ],
        ];

        // Related product fields
        $rel_fields = [
            'key' => 'cgit_product_related',
            'title' => 'Related products',
            'location' => $location,
            'position' => 'normal',
            'fields' => [
                [
                    'label' => 'Related products',
                    'name' => 'related_products',
                    'key' => 'related_products',
                    'type' => 'post_object',
                    'post_type' => [
                        CGIT_PRODUCT_POST_TYPE,
                    ],
                    'allow_null' => 1,
                    'multiple' => 1,
                    'return_format' => 'object',
                ],
            ],
        ];

        // Filters
        $fields = apply_filters('cgit_product_fields', $fields);
        $var_fields = apply_filters('cgit_product_variant_fields', $var_fields);
        $rel_fields = apply_filters('cgit_product_related_fields', $rel_fields);

        // Register fields
        acf_add_local_field_group($fields);
        acf_add_local_field_group($var_fields);
        acf_add_local_field_group($rel_fields);
    }

    /**
     * Add discounted prices to database
     *
     * When saving ACF data, apply any discount to the price and save the
     * discounted value alongside the original value.
     */
    public function saveDiscountedPrice()
    {
        // Get ACF field values
        $fields = $_POST['acf'];

        // Get discount type
        $type = $fields['discount'];

        // Get discount values
        $original = (float) $fields['price_original'];
        $amount = (float) $fields['discount_amount'];
        $percent = (float) $fields['discount_percent'];
        $discount = 0;

        // Calculate discount
        if ($type == 'percent') {
            $discount = $original * ($percent / 100);
        } elseif ($type == 'amount') {
            $discount = $amount;
        }

        // Apply discount
        $price = $original - $discount;

        // Save price with discount applied
        update_field('price', $price, $post_id);
    }

    /**
     * Default product meta query
     *
     * This uses the cgit_product_meta_query filter in Cgit\ProductCatalogue to
     * assemble the default product meta query.
     */
    public function setMetaQuery($meta_query, $args)
    {
        // The default meta query uses an 'AND' relationship. Set match_any to
        // true to use an 'OR' relationship.
        if ($args['match_any']) {
            $meta_query['relation'] = 'OR';
        }

        // Price restrictions (uses the discounted price, not the original)
        if ($args['min_price'] && $args['max_price']) {
            $meta_query[] = [
                'key' => 'price',
                'type' => 'numeric',
                'value' => [$args['min_price'], $args['max_price']],
                'compare' => 'BETWEEN',
            ];
        } elseif ($args['min_price']) {
            $meta_query[] = [
                'key' => 'price',
                'type' => 'numeric',
                'value' => $args['min_price'],
                'compare' => '>=',
            ];
        } elseif ($args['max_price']) {
            $meta_query[] = [
                'key' => 'price',
                'type' => 'numeric',
                'value' => $args['max_price'],
                'compare' => '<=',
            ];
        }

        // Price includes VAT
        if ($args['inc_vat']) {
            $meta_query[] = [
                'key' => 'inc_vat',
                'value' => 1,
            ];
        }

        // Featured products
        if ($args['featured']) {
            $meta_query[] = [
                'key' => 'featured',
                'value' => 1,
            ];
        }

        // Discounted products
        if ($args['discount']) {
            $meta_query[] = [
                'key' => 'discount',
                'value' => ['amount', 'percent'],
                'compare' => 'IN',
            ];
        }

        // Catalogue code
        if ($args['cat_code']) {
            $meta_query[] = [
                'key' => 'cat_code',
                'value' => $args['cat_code'],
                'compare' => '=',
            ];
        }

        // Products in stock (or N products in stock)
        if ($args['stock']) {
            if ($args > 1) {
                $meta_query[] = [
                    'key' => 'stock',
                    'type' => 'numeric',
                    'value' => $args['stock'],
                    'compare' => '>',
                ];
            } else {
                $meta_query[] = [
                    'key' => 'stock',
                    'type' => 'numeric',
                    'value' => 0,
                    'compare' => '>',
                ];
            }
        }

        return $meta_query;
    }

    /**
     * Register search widget
     */
    public function registerWidgets()
    {
        register_widget('Cgit\Products\Widgets\Search');
    }
}
