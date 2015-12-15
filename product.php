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

        // Check post is a product
        if ($obj->post_type != CGIT_PRODUCT_POST_TYPE) {
            trigger_error('Post ' . $id . ' is not a product', E_USER_WARNING);
        }

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
        );

        foreach ($fields as $field) {
            $property = 'product_' . $field;
            $this->$property = get_field($field, $id);
        }

        // Add related products
        $this->product_related = get_field('related_products', $id);
    }

    /**
     * Related products
     *
     * The product_related property returns an array of WP_Post objects. This
     * method returns an array of Cgit\Product objects. This has to be a
     * separate method outside of the constructor to avoid infinite loops.
     */
    public function related() {
        $items = $this->product_related;
        $products = array();

        if (!$items) {
            return $products;
        }

        foreach ($items as $item) {
            $products[] = new Product($item->ID);
        }

        return $products;
    }
}
