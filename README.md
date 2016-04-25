# Castlegate IT WP Product Catalogue #

The Castlegate IT WP Product Catalogue plugin provides a simple, searchable product catalogue for WordPress, using [Advanced Custom Fields](http://www.advancedcustomfields.com/). It supports taxonomies, featured products, discounts, image galleries, product variations, and searching by price range.

## Constants ##

The post type and taxonomy names are set with constants. These can be overridden to avoid naming conflicts by defining the constants earlier, e.g. in `wp-config.php`.

*   `CGIT_PRODUCT_POST_TYPE` is the post type name, default `product`.
*   `CGIT_PRODUCT_CATEGORY` is the category taxonomy name, default `product_category`.
*   `CGIT_PRODUCT_TAG` is the tag taxonomy name, default `product_tag`.

The currency symbol displayed in the WP admin interface is also set using the `CGIT_PRODUCT_CURRENCY` constant and can be overridden. The default value is `&pound;` Note that the symbol is only used in the admin panel; it is not stored in the database.

The number of products per page (in archives or searches) can be customized with `CGIT_PRODUCT_PER_PAGE`. By default, this constant is not defined and the number of products per page will be the same as the number of posts per page.

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

The main product listing `archive-product.php` will list featured products first, then all products in alphabetical order.

## Prices and discounts ##

The admin panel lets you enter the price, the discount type (none, an amount, or a percentage), and a numerical discount value. This original price is saved as `price_original`. When you update the product, the plugin will calculate the discounted price with any discounts applied and save it as `price`.

This field cannot be edited directly in WordPress, but will be updated every time the product is saved. When searching by price, it is this calculated `price` field that is used, not the original price.

## Searches and queries ##

The `$catalogue->metaQuery()` method converts `WP_Query`-like arguments related to products into a meta query that is actually compatible with `WP_Query`. This function is used internally to filter searches that are specifically restricted to the product post type, allowing the following query parameters:

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

~~~ php
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
~~~

Searching by custom taxonomy is supported natively by WordPress, using the taxonomy slug as the query parameter. Assuming that `CGIT_PRODUCT_CATEGORY` is `product_category`, you can could use `?product_category=foo` to search for a single category or `?product_category[]=foo&product_category[]=bar` to search for multiple categories.

## Templates ##

According to the [WordPress template hierarchy](https://developer.wordpress.org/themes/basics/template-hierarchy/), product searches will use the `search.php` template. This plugin adds the option of using a `search-product.php` template to customize the format of product searches.

The `$catalogue->render('search')` method returns the compiled output of the file `views/search.php` within the plugin directory. Future versions of the plugin may include more views that can be rendered with this method. Their default output can be considered an example form; you can create your own forms or use the `cgit_product_search_form` filter to customize this form to suit your site.

## Functions ##

`cgit_product_catalogue()` returns the single instance of the `Cgit\Products\Catalogue` object. This is mostly used internally to manage the product post type and associated queries. However, you may interact with it in templates to access the `$catalogue::formatCurrency($number, $after = false, $sep = '')` method, which formats numbers with two decimal places and the currency symbol set in `CGIT_PRODUCT_CURRENCY`. If `$after` is true, the symbol is placed after the number; `$sep` is always put between the number and the symbol.

`cgit_product($post_id)` returns a `Cgit\Products\Product` object, which is based on the default `WP_Post` object, but has additional properties for the various product details. If `$post_id` is not specified, the function uses the current post ID. This function is provided for convenience, so you don't have to write lots of `get_field()` calls.

`cgit_products($args)` works like `get_posts`, but allows more arguments (see Searches and queries above) and returns an array of `Cgit\Products\Product` objects instead of `WP_Post` objects. This could be used to return a list of featured products:

~~~ php
$featured = cgit_products(array(
    'featured' => true
));
~~~

## Filters ##

Various filters are available to edit the product post type and fields.

*   `cgit_product_post_type` filters the options passed to the `register_post_type()` function that defines the product post type.
*   `cgit_product_fields` filters the main product field options passed to ACF.
*   `cgit_product_variant_fields` filters the product variant field options passed to ACF.
*   `cgit_product_related_fields` filters the related product field options passed to ACF.
*   `cgit_product_category` filters the options passed to the `register_taxonomy()` function that defined the product category taxonomy.
*   `cgit_product_tag` filters the options passed to the `register_taxonomy()` function that defined the product tag taxonomy.
*   `cgit_product_search_form` filters the HTML of the default search form. You could use this to edit or replace the default search form.
*   `cgit_product_meta_query` is used in the `cgit_product_meta_query()` function that converts query parameters to WordPress meta queries. You can use this to extend the range of searchable fields.
*   `cgit_product_render_search` can be used to edit or replace the search form returned by `$catalogue->render('search')`.

## Requirements ##

Requires [Advanced Custom Fields](http://www.advancedcustomfields.com/). The plugin will fail to activate without this dependency.
