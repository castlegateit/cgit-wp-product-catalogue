<?php

/**
 * Add discounted prices to database
 *
 * When saving ACF data, apply any discount to the price and save the discounted
 * value alongside the original value.
 */
add_action('acf/save_post', function($post_id) {

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
}, 20);
