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
 * @version 1.1.0
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<?php

if( $terms && ! is_wp_error( $terms ) ) { ?>

	<div class="mnm_filter_button_group" style="display:none" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">

		<p><?php _e( 'Filter by:', 'wc-mnm-filter' ); ?></p>

  		<ul class="mnm_filters">

  		<li><button class="selected" data-filter="*"><?php esc_html_e( 'Show all', 'wc-mnm-filter' ); ?></button></li>

		<?php

		foreach( $terms as $term ) {
			printf( '<li><button data-filter="%s">%s</button></li>', $term->slug, $term->name ); 
		}

		?>

		</ul>

	</div>
<?php
}

