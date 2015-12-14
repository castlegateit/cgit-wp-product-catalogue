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

        <button>Search</button>

    </form>

</div>
