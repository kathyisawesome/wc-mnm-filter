# WooCommerce Mix and Match - Filter by Terms

### Quickstart

This is a developmental repo. Clone this repo and run `npm install && npm run build`   
OR    
[Download latest release](https://github.com/kathyisawesome/wc-mnm-filter/releases/latest/download/wc-mnm-filter.zip)


### What's This?

Experimental mini-extension for [WooCommerce Mix and Match](https://woocommerce.com/products/woocommerce-mix-and-match-products/) that allows a customer to filter products by any product taxonomy.

![A group of products shown changing depending on which category button is clicked](https://user-images.githubusercontent.com/507025/53804881-ff6f5080-3f8b-11e9-8d13-3207df6f3a75.gif)

### Multi-term versus Single-term filtering

By default, customers can only search by a single term at a time. 

If you'd prefer to select multiple terms to narrow their search. For example, Category A + Category B will display products in both, then you can add the following snippet to your child theme's `functions.php`.

```
add_filter( 'wc_mnm_filters_support_multi_term_filtering', '__return_true' );
```

### Important

1. This is proof of concept and not officially supported in any way.
2. Currently, only tested with grid layout.
3. Version 2.0 now requires Mix and Match 2.0.

### Automatic plugin updates

Plugin updates can be enabled by installing the [Git Updater](https://git-updater.com/) plugin.