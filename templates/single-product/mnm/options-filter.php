<?php
/**
 * Mix and Match Options Filter
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/mnm/options-filter.php.
 *
 * HOWEVER, on occasion WooCommerce Mix and Match will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  Kathy Darling
 * @package WooCommerce Mix and Match Filter/Templates
 * @since   1.0.0
 * @version 1.0.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<?php 

$terms = get_terms( 'product_cat', array( 'orderby' => 'name' ) );

if( $terms && ! is_wp_error( $terms ) ) {

	echo '<div class="mnm-filter-button-group" style="display:none">';

		echo '<p>' . __( 'Filter by:', 'wc-mnm-filter' ) . '</p>';

  		echo '<button class="selected" data-filter="*">' . __( 'Show all', 'wc-mnm-filter' ) . '</button>';

		foreach( $terms as $term ) {
			printf( '<button data-filter="%s">%s</button>', $term->slug, $term->name ); 
		}

	echo '</div>';

}

