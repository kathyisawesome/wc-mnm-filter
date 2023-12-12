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
 * @version 2.0.2
 */
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ){
	exit;
}
?>
<?php

if ( $terms && ! is_wp_error( $terms ) ) { ?>

	<div class="mnm_filter_button_group" style="display:none" data-taxonomy="<?php echo esc_attr( $taxonomy ); ?>">

		<p><?php esc_html_e( 'Filter by:', 'wc-mnm-filter' ); ?></p>

  		<div class="mnm_filters">

  		<button class="selected<?php echo esc_attr( WC_MNM_Core_Compatibility::wp_theme_get_element_class_name( 'button' ) ); ?>" data-filter="*"><?php esc_html_e( 'Show all', 'wc-mnm-filter' ); ?></button>

		<?php

		foreach( $terms as $term ) {
			printf( '<button data-filter="%s" class="' . esc_attr( WC_MNM_Core_Compatibility::wp_theme_get_element_class_name( 'button' ) ) . '">%s</button>', esc_attr( $term->slug ), esc_html( $term->name ) ); 
		}

		?>

		</div>

	</div>
<?php
}

