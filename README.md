# Castlegate IT WP Product Catalogue #

The Castlegate IT WP Product Catalogue plugin provides a simple, searchable product catalogue for WordPress, using [Advanced Custom Fields](http://www.advancedcustomfields.com/). It supports taxonomies, featured products, discounts, image galleries, product variations, and searching by price range.

## Constants ##

The post type and taxonomy names are set with constants. These can be overridden to avoid naming conflicts by defining the constants earlier, e.g. in `wp-config.php`.

*   `CGIT_PRODUCT_POST_TYPE` is the post type name, default `product`.
*   `CGIT_PRODUCT_CATEGORY` is the category taxonomy name, default `product-category`.
*   `CGIT_PRODUCT_TAG` is the tag taxonomy name, default `product-tag`.

The currency symbol displayed in the WP admin interface is also set using the `CGIT_PRODUCT_CURRENCY` constant and can be overridden. The default value is `&pound;` Note that the symbol is only used in the admin panel; it is not stored in the database.

## Post type, fields, and taxonomies ##

Products exist as entries in the product post type. They support the same range of fields as regular posts or pages, including comments. They also include various custom fields, defined using ACF:

*   Price
*   Includes VAT?
*   Featured product?
*   Discount (with options for an amount or a percentage)
*   Image gallery
*   Catalogue code
*   Number in stock
*   Product variations, including name, description, and images
*   Related products

Two taxonomies have also been defined for the product post type: categories and tags. These should behave like their equivalents for the default post type.

## Prices and discounts ##

The admin panel lets you enter the price, the discount type (none, an amount, or a percentage), and a numerical discount value. This original price is saved as `price_original`. When you update the product, the plugin will calculate the discounted price with any discounts applied and save it as `price`.

This field cannot be edited directly in WordPress, but will be updated every time the product is saved. When searching by price, it is this calculated `price` field that is used, not the original price.

## Searches and queries ##

The plugin provides the function `get_product_meta_query()` that converts `WP_Query`-like arguments related to products into a meta query that is actually compatible with `WP_Query`. This function is used internally to filter searches that are specifically restricted to the product post type, allowing the following query parameters:

*   `min_price` minimum price (number)
*   `max_price` minimum price (number)
*   `inc_vat` price includes VAT (boolean)
*   `featured` featured product (boolean)
*   `discount` discounted product (boolean)
*   `cat_code` catalogue code (string)
*   `stock` in stock (boolean)

For example, a product search query string might look like `?post_type=product&max_price=20&featured=1`. These searches can be combined with the default WordPress search query string, e.g. `?s=example`.

As with `WP_Query` and `get_posts()`, you can use the `orderby` and `order` (`ASC` and `DESC`) options to set the order of the posts. You can also sort by price, using `?orderby=price`. See the [WordPress documentation](https://codex.wordpress.org/Template_Tags/get_posts) for the default options.

Therefore, you could use something like the following to allow quick sorting of products on a search or archive page:

    <?php

    $asc = add_query_arg('order', 'asc');
    $desc = add_query_arg('order', 'desc');

    ?>
    <p>
        <a href="<?= add_query_arg('orderby', 'title', $asc) ?>">Sort by name (ascending)</a>
        <a href="<?= add_query_arg('orderby', 'title', $desc) ?>">Sort by name (descending)</a>
        <a href="<?= add_query_arg('orderby', 'price', $asc) ?>">Sort by price (ascending)</a>
        <a href="<?= add_query_arg('orderby', 'price', $desc) ?>">Sort by price (descending)</a>
    </p>

## Templates ##

According to the [WordPress template hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/), product searches will use the `search.php` template. This plugin adds the option of using a `search-product.php` template to customize the format of product searches.

## Functions ##

`get_product($post_id)` returns a `Cgit\Product` object, which is based on the default `WP_Post` object, but has additional properties for the various product details. This is provided for convenience, so you don't have to write lots of `get_field()` calls.

`get_products($args)` works like `get_posts`, but allows more arguments (see Searches and queries above) and returns an array of `Cgit\Product` objects instead of `WP_Post` objects. This could be used to return a list of featured products:

    $featured = get_products(array(
        'featured' => true
    ));

## Filters ##

Various filters are available to edit the product post type and fields.

*   `cgit_product_post_type` filters the options passed to the `register_post_type()` function that defines the product post type.
*   `cgit_product_fields` filters the main product field options passed to ACF.
*   `cgit_product_variant_fields` filters the product variant field options passed to ACF.
*   `cgit_product_related_fields` filters the related product field options passed to ACF.
*   `cgit_product_category` filters the options passed to the `register_taxonomy()` function that defined the product category taxonomy.
*   `cgit_product_tag` filters the options passed to the `register_taxonomy()` function that defined the product tag taxonomy.

## Requirements ##

Requires [Advanced Custom Fields](http://www.advancedcustomfields.com/). The plugin will fail to activate without this dependency.
