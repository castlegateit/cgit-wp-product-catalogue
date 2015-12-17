<div class="<?= CGIT_PRODUCT_POST_TYPE ?>-search-form">

    <form action="/" method="get">

        <input type="hidden" name="post_type" value="<?= CGIT_PRODUCT_POST_TYPE ?>" />

        <p>
            <label for="product_keyword">Keywords</label>
            <input type="text" name="s" id="product_keyword" value="<?= get_query_var('s') ?>" />
        </p>

        <p>
            <label for="product_min_price">Minimum price</label>
            <?= CGIT_PRODUCT_CURRENCY ?> <input type="text" name="min_price" id="product_min_price" value="<?= get_query_var('min_price') ?>" />
        </p>

        <p>
            <label for="product_max_price">Maximum price</label>
            <?= CGIT_PRODUCT_CURRENCY ?> <input type="text" name="max_price" id="product_max_price" value="<?= get_query_var('max_price') ?>" />
        </p>

        <p>
            <label>
                <input type="checkbox" name="inc_vat" value="1" <?= get_query_var('inc_vat') ? 'checked' : '' ?> />
                Price includes VAT?
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="featured" value="1" <?= get_query_var('featured') ? 'checked' : '' ?> />
                Featured products
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="discount" value="1" <?= get_query_var('discount') ? 'checked' : '' ?> />
                Discounted products
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="stock" value="1" <?= get_query_var('stock') ? 'checked' : '' ?> />
                Products in stock
            </label>
        </p>

        <p>
            <label for="product_cat_code">Catalogue code</label>
            <input type="text" name="cat_code" id="product_cat_code" value="<?= get_query_var('cat_code') ?>" />
        </p>

        <?php if (count($cats) > 0): ?>
        <p>
            <?= $cat_tax->labels->name ?>:
            <?php foreach ($cats as $cat): ?>
                <label>
                    <input type="checkbox" name="<?= $cat->taxonomy ?>[]" value="<?= $cat->slug ?>" <?= in_array($cat->slug, get_query_var(CGIT_PRODUCT_CATEGORY)) ? 'checked' : '' ?> />
                    <?= $cat->name ?>
                </label>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>

        <?php if (count($tags) > 0): ?>
        <p>
            <?= $tag_tax->labels->name ?>:
            <?php foreach ($tags as $tag): ?>
                <label>
                    <input type="checkbox" name="<?= $tag->taxonomy ?>[]" value="<?= $tag->slug ?>" <?= in_array($cat->slug, get_query_var(CGIT_PRODUCT_TAG)) ? 'checked' : '' ?> />
                    <?= $tag->name ?>
                </label>
            <?php endforeach; ?>
        </p>
        <?php endif; ?>

        <button>Search</button>

    </form>

</div>
