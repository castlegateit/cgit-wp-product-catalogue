<?php

/**
 * Default product meta query
 *
 * This uses the cgit_product_meta_query filter in Cgit\ProductCatalogue to
 * assemble the default product meta query.
 */
add_filter('cgit_product_meta_query', function($meta_query, $args) {

    // The default meta query uses an 'AND' relationship. Set match_any to
    // true to use an 'OR' relationship.
    if ($args['match_any']) {
        $meta_query['relation'] = 'OR';
    }

    // Price restrictions (uses the discounted price, not the original)
    if ($args['min_price'] && $args['max_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => array($args['min_price'], $args['max_price']),
            'compare' => 'BETWEEN',
        );
    } elseif ($args['min_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => $args['min_price'],
            'compare' => '>=',
        );
    } elseif ($args['max_price']) {
        $meta_query[] = array(
            'key' => 'price',
            'type' => 'numeric',
            'value' => $args['max_price'],
            'compare' => '<=',
        );
    }

    // Price includes VAT
    if ($args['inc_vat']) {
        $meta_query[] = array(
            'key' => 'inc_vat',
            'value' => 1,
        );
    }

    // Featured products
    if ($args['featured']) {
        $meta_query[] = array(
            'key' => 'featured',
            'value' => 1,
        );
    }

    // Discounted products
    if ($args['discount']) {
        $meta_query[] = array(
            'key' => 'discount',
            'value' => array('amount', 'percent'),
            'compare' => 'IN',
        );
    }

    // Catalogue code
    if ($args['cat_code']) {
        $meta_query[] = array(
            'key' => 'cat_code',
            'value' => $args['cat_code'],
            'compare' => '=',
        );
    }

    // Products in stock (or N products in stock)
    if ($args['stock']) {
        if ($args > 1) {
            $meta_query[] = array(
                'key' => 'stock',
                'type' => 'numeric',
                'value' => $args['stock'],
                'compare' => '>',
            );
        } else {
            $meta_query[] = array(
                'key' => 'stock',
                'type' => 'numeric',
                'value' => 0,
                'compare' => '>',
            );
        }
    }

    return $meta_query;
}, 10, 2);
