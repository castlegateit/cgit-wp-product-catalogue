<?php

namespace Cgit;

/**
 * Product
 *
 * Similar to WP_Post, but with additional properties for the product details
 * and variants saved as custom fields via ACF.
 */
class Product
{

    /**
     * Constructor
     *
     * WP_Post cannot be extended, so this iterates over the WP_Post object for
     * the given ID and adds all its properties to the Product object. It then
     * adds the properties that are unique to the product post type.
     */
    public function __construct($id)
    {
        $obj = get_post($id);

        // Add WP_Post properties
        foreach ($obj as $property => $value) {
            $this->$property = $value;
        }

        // Assign values to properties
        $fields = array(
            'price',
            'price_original',
            'inc_vat',
            'featured',
            'discount',
            'discount_amount',
            'discount_percent',
            'gallery',
            'cat_code',
            'stock',
            'variants',
            'related',
        );

        foreach ($fields as $field) {
            $property = 'product_' . $field;
            $this->$property = get_field($field, $id);
        }
    }
}
