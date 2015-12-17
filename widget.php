<?php

namespace Cgit;

/**
 * Search widget
 *
 * Uses the cgit_product_search_form() function to create a search form widget
 * that can be used in any dynamic widget area.
 */
class ProductSearchWidget extends \WP_Widget
{

    /**
     * Register widget
     */
    function __construct()
    {
        parent::__construct(
            'cgit_product_search_widget',
            __('Product Search', 'text_domain')
        );
    }

    /**
     * Display widget content
     */
    public function widget($args, $instance)
    {
        echo $args['before_widget'];

        if (!empty($instance['title'])) {
            echo $args['before_title']
                . apply_filters('widget_title', $instance['title'])
                . $args['after_title'];
        }

        echo cgit_product_search_form();
        echo $args['after_widget'];
    }

    /**
     * Display widget settings
     */
    public function form($instance)
    {
        $title = !empty($instance['title']) ? $instance['title'] :
            __('Product search', 'text_domain');
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
    public function update($new_instance, $old_instance)
    {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ?
            strip_tags($new_instance['title']) : '';

        return $instance;
    }
}

add_action('widgets_init', function() {
    register_widget('Cgit\ProductSearchWidget');
});
