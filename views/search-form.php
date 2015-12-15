<div class="<?= CGIT_PRODUCT_POST_TYPE ?>-search-form">

    <form action="/" method="get">

        <input type="hidden" name="post_type" value="<?= CGIT_PRODUCT_POST_TYPE ?>" />

        <p>
            <label for="product_keyword">Keywords</label>
            <input type="text" name="s" id="product_keyword" />
        </p>

        <p>
            <label for="product_min_price">Minimum price</label>
            <?= CGIT_PRODUCT_CURRENCY ?> <input type="text" name="min_price" id="product_min_price" />
        </p>

        <p>
            <label for="product_max_price">Maximum price</label>
            <?= CGIT_PRODUCT_CURRENCY ?> <input type="text" name="max_price" id="product_max_price" />
        </p>

        <p>
            <label>
                <input type="checkbox" name="inc_vat" value="1" />
                Price includes VAT?
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="featured" value="1" />
                Featured products
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="discount" value="1" />
                Discounted products
            </label>
        </p>

        <p>
            <label>
                <input type="checkbox" name="stock" value="1" />
                Products in stock
            </label>
        </p>

        <p>
            <label for="product_cat_code">Catalogue code</label>
            <input type="text" name="cat_code" id="product_cat_code" />
        </p>

        <p>
            <?= $cat_tax->labels->name ?>:
            <?php foreach ($cats as $cat): ?>
                <label>
                    <input type="checkbox" name="<?= $cat->taxonomy ?>[]" value="<?= $cat->slug ?>" />
                    <?= $cat->name ?>
                </label>
            <?php endforeach; ?>
        </p>

        <p>
            <?= $tag_tax->labels->name ?>:
            <?php foreach ($tags as $tag): ?>
                <label>
                    <input type="checkbox" name="<?= $tag->taxonomy ?>[]" value="<?= $tag->slug ?>" />
                    <?= $tag->name ?>
                </label>
            <?php endforeach; ?>
        </p>

        <button>Search</button>

    </form>

</div>
