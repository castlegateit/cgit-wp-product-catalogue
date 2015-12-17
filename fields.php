<?php

/**
 * Define fields
 *
 * Add custom fields to each product for price, discounts, images, and other
 * details. Products can have zero or more variants, with separate descriptions
 * and images.
 *
 * Currency values are stored as numbers. The currency symbol displayed in the
 * WP admin panel is set with the CGIT_PRODUCT_CURRENCY constant.
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
add_action('init', function() {

    // Location settings for all field groups
    $location = array(
        array(
            array(
                'param' => 'post_type',
                'operator' => '==',
                'value' => CGIT_PRODUCT_POST_TYPE,
            ),
        ),
    );

    // Fields
    $fields = array(
        'key' => 'cgit_product',
        'title' => 'Product details',
        'location' => $location,
        'position' => 'normal',
        'fields' => array(
            array(
                'label' => 'Price',
                'name' => 'price_original',
                'key' => 'price_original',
                'type' => 'number',
                'required' => true,
                'prepend' => CGIT_PRODUCT_CURRENCY,
                'wrapper' => array(
                    'width' => '50%',
                ),
            ),
            array(
                'label' => 'Inc. VAT?',
                'name' => 'inc_vat',
                'key' => 'inc_vat',
                'type' => 'true_false',
                'default_value' => '1',
                'message' => 'Price includes VAT',
                'wrapper' => array(
                    'width' => '25%',
                ),
            ),
            array(
                'label' => 'Featured?',
                'name' => 'featured',
                'key' => 'featured',
                'type' => 'true_false',
                'message' => 'This is a featured product',
                'wrapper' => array(
                    'width' => '25%',
                ),
            ),
            array(
                'label' => 'Discount',
                'name' => 'discount',
                'key' => 'discount',
                'type' => 'radio',
                'choices' => array(
                    'none' => 'None',
                    'amount' => 'Amount (' . CGIT_PRODUCT_CURRENCY . ')',
                    'percent' => 'Percent (%)',
                ),
            ),
            array(
                'label' => 'Discount amount',
                'name' => 'discount_amount',
                'key' => 'discount_amount',
                'type' => 'number',
                'prepend' => CGIT_PRODUCT_CURRENCY,
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'discount',
                            'operator' => '==',
                            'value' => 'amount',
                        ),
                    ),
                ),
            ),
            array(
                'label' => 'Discount percent',
                'name' => 'discount_percent',
                'key' => 'discount_percent',
                'type' => 'number',
                'append' => '%',
                'conditional_logic' => array(
                    array(
                        array(
                            'field' => 'discount',
                            'operator' => '==',
                            'value' => 'percent',
                        ),
                    ),
                ),
            ),
            array(
                'label' => 'Image gallery',
                'name' => 'gallery',
                'key' => 'gallery',
                'type' => 'gallery',
                'instructions' => 'Use the Featured Image field on the right of the screen to set the main image.',
            ),
            array(
                'label' => 'Catalogue number or code',
                'name' => 'cat_code',
                'key' => 'cat_code',
                'type' => 'text',
                'instructions' => 'A unique identifier, catalogue number, etc. (optional).',
                'wrapper' => array(
                    'width' => '50%',
                ),
            ),
            array(
                'label' => 'Number in stock',
                'name' => 'stock',
                'key' => 'stock',
                'type' => 'number',
                'instructions' => 'Number of items in stock (optional).',
                'wrapper' => array(
                    'width' => '50%',
                ),
            ),
        ),
    );

    // Variant fields
    $var_fields = array(
        'key' => 'cgit_product_variants',
        'title' => 'Product variants',
        'location' => $location,
        'position' => 'normal',
        'fields' => array(
            array(
                'label' => 'Variants',
                'name' => 'variants',
                'key' => 'variants',
                'type' => 'repeater',
                'button_label' => 'Add Variant',
                'layout' => 'block',
                'sub_fields' => array(
                    array(
                        'label' => 'Name',
                        'name' => 'variant_name',
                        'key' => 'variant_name',
                        'type' => 'text',
                        'required' => true,
                    ),
                    array(
                        'label' => 'Description',
                        'name' => 'variant_description',
                        'key' => 'variant_description',
                        'type' => 'textarea',
                        'rows' => 4,
                    ),
                    array(
                        'label' => 'Variant image gallery?',
                        'name' => 'variant_has_gallery',
                        'key' => 'variant_has_gallery',
                        'type' => 'true_false',
                        'message' => 'This product variant has its own images'
                    ),
                    array(
                        'label' => 'Image gallery',
                        'name' => 'variant_gallery',
                        'key' => 'variant_gallery',
                        'type' => 'gallery',
                        'conditional_logic' => array(
                            array(
                                array(
                                    'field' => 'variant_has_gallery',
                                    'operator' => '==',
                                    'value' => 1,
                                ),
                            ),
                        ),
                    ),
                    array(
                        'label' => 'Variant catalogue number or code',
                        'name' => 'variant_cat_code',
                        'key' => 'variant_cat_code',
                        'type' => 'text',
                    ),
                ),
            ),
        ),
    );

    // Related product fields
    $rel_fields = array(
        'key' => 'cgit_product_related',
        'title' => 'Related products',
        'location' => $location,
        'position' => 'normal',
        'fields' => array(
            array(
                'label' => 'Related products',
                'name' => 'related_products',
                'key' => 'related_products',
                'type' => 'post_object',
                'post_type' => array(
                    CGIT_PRODUCT_POST_TYPE,
                ),
                'allow_null' => 1,
                'multiple' => 1,
                'return_format' => 'object',
            ),
        ),
    );

    // Filters
    $fields = apply_filters('cgit_product_fields', $fields);
    $var_fields = apply_filters('cgit_product_variant_fields', $var_fields);
    $rel_fields = apply_filters('cgit_product_related_fields', $rel_fields);

    // Register fields
    acf_add_local_field_group($fields);
    acf_add_local_field_group($var_fields);
    acf_add_local_field_group($rel_fields);
});
