# WooCommerce Mix and Match - Filter by Terms

### What's This?

Experimental mini-extension for [WooCommerce Mix and Match](https://woocommerce.com/products/woocommerce-mix-and-match-products/?aff=5151&cid=4951026) that allows a customer to filter products by any product taxonomy.

![A group of products shown changing depending on which category button is clicked](https://user-images.githubusercontent.com/507025/53804881-ff6f5080-3f8b-11e9-8d13-3207df6f3a75.gif)

### Multi-term versus Single-term filtering

By default, customers can select multiple terms to narrow their search. For example, Category A + Category B will display products in both. 

If you'd prefer to only search by a single term at a time, then you can add the following snippet to your child theme's `functions.php`.

```
add_filter( 'wc_mnm_filters_support_multi_term_filtering', '__return_false' );
```

### Important

1. This is proof of concept and not officially supported in any way.
2. Currently, only tested with grid layout.
3. Version 1.2 now requires Mix and Match 1.10.6.
