<?php

/**
 * Sample search form
 *
 * This file provides a sample product search form that can be included in your
 * template as a function, shortcode, or widget. These should be considered
 * examples; you will probably need to write custom search forms to suit you
 * site and theme.
 */

namespace Cgit;

/**
 * Function to return basic search form
 */
function get_product_search_form() {
    include dirname(__FILE__) . '/views/search-form.php';
}

/**
 * Search form shortcode
 */
add_shortcode('product_search', 'get_product_search_form');

/**
 * Search widget
 */
class ProductSearchWidget extends \WP_Widget {

    /**
     * Register widget
     */
    function __construct() {
        parent::__construct(
            'cgit_product_search_widget',
            __('Product Search', 'text_domain')
        );
    }

    /**
     * Display widget content
     */
    public function widget($args, $instance) {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        echo get_product_search_form();
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     */
    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] :
            __('Product Search', 'text_domain');
        $id = $this->get_field_id('title');
        $name = $this->get_field_name('title');
        $label = __('Title:');
        $value = esc_attr($title);

        echo '<p><label for="' . $id . '">' . $label
            . '</label><input type="text" name="' . $name . '" id="'
            . $id . '" class="widefat" value="' . $value . '" /></p>';
    }

    /**
     * Save widget settings
     */
    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ?
            strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Cgit\ProductSearchWidget');
});
