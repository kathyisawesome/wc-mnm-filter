<?php
/**
 * Plugin Name: WooCommerce Mix and Match - Filter by Terms
 * Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products
 * Description: Dynmamic Term Filtering for WooCommerce Mix and Match Products.
 * Version: 2.0.0
 * Author: Kathy Darling
 * Author URI: http://kathyisawesome.com/
 *
 * Text Domain: wc-mnm-filter
 * Domain Path: /languages/
 *
 * Requires at least: 5.8
 * Tested up to: 6.0
 *
 * WC requires at least: 7.0.0
 * WC tested up to: 7.5.0
 *
 * GitHub Plugin URI: https://github.com/wc-mnm-filter
 * Primary Branch: trunk
 * Release Asset: true
 *
 * Copyright: © 2023 Kathy Darling
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class WC_MNM_Filter {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	public static $version = '2.0.0';

	/**
	 * Required MNM Plugin version.
	 *
	 * @var string
	 */
	private static $required_mnm = '2.0.0';

	/**
	 * Product Taxonomies.
	 *
	 * @var array
	 */
	private static $product_taxonomies = array();

	/**
	 * Filter taxonomy.
	 *
	 * @var array
	 */
	private static $taxonomy = '';

	/**
	 * Fire in the hole!
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin' ), 20 );
	}

	/**
	 * Hooks.
	 */
	public static function load_plugin() {

		/*
		 * Admin.
		 */

		// Load translation files.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		if ( ! is_callable( array( 'WC_MNM_Compatibility', 'is_version_gte' ) ) || ! WC_MNM_Compatibility::is_version_gte( '2.0' ) ) {

			add_action( 'admin_notices', function() {
				if ( ! current_user_can( 'manage_options' ) ) {
					return;
				}
		
				// Translators: %1$s = Plugin name, %2$s = Plugin version, %2$s = Required Mix and Match Plugin version.
				$message = '<p>' . sprintf( esc_html__( 'To function properly, %1$s %2$s requires WooCommerce Mix and Match version %3$s', 'wc-mnm-filter' ), '<strong>' . esc_html( 'WooCommerce Mix and Match - Filter by Terms', 'wc-mnm-filter' ) . '</strong>', self::$version, self::$required_mnm ) . '</p>';

				echo '<div class="notice notice-error">' . wp_kses_post( $message ) . '</div>';
			} );

			return false;
		}

		// Add extra meta.
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'additional_container_option' ) , 7, 2 );

		// Add extra meta.
		add_action( 'wc_mnm_admin_product_options', array( __CLASS__, 'additional_container_option' ) , 7, 2 );
		
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_meta' ), 20 );

		// Display the filter and add term to product class.
		add_action( 'wc_mnm_content_loop', array( __CLASS__, 'add_filter_navigation' ), 5 );
		add_action( 'wc_mnm_before_child_items', array( __CLASS__, 'add_product_class_filter' ) );
		add_action( 'wc_mnm_after_child_items', array( __CLASS__, 'remove_product_class_filter' ) );

		// Add data to <form> for consumption by script.
		add_action( 'wc_mnm_container_data_attributes', array( __CLASS__, 'container_data_attributes' ), 10, 2 );
		
		// Register Scripts.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );

		// Display Scripts.
		add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'load_scripts' ) );
		add_action( 'woocommerce_grouped-mnm_add_to_cart', array( __CLASS__, 'load_scripts' ) );

		// QuickView support.
		add_action( 'wc_quick_view_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

		// Declare Features compatibility.
		add_action( 'before_woocommerce_init', array( __CLASS__, 'declare_features_compatibility' ) );

	}

	/*-----------------------------------------------------------------------------------*/
	/* Localization */
	/*-----------------------------------------------------------------------------------*/


	/**
	 * Make the plugin translation ready
	 *
	 * @return void
	 */
	public static function load_plugin_textdomain() {
		load_plugin_textdomain( 'wc-mnm-filter' , false , dirname( plugin_basename( __FILE__ ) ) .  '/languages/' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Admin */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Adds the container max weight option writepanel options.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function additional_container_option( $post_id, $mnm_product_object ) {

		$value = $mnm_product_object->get_meta( '_mnm_filter', true );

		// Previous version was strictly categories so convert.
		$value = $value === 'yes' ? 'product_cat' : $value;

		woocommerce_wp_select( array(
			'id'      => '_mnm_filter',
			'label'   => __( 'Filter container options by taxonomy', 'wc-mnm-filter' ),
			'options' => self::get_product_taxonomies(),
			'value'   => $value
		) );
	}

	/**
	 * Saves the new meta field.
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public static function process_meta( $product ) {

		if ( isset( $_POST[ '_mnm_filter' ] ) && array_key_exists( $_POST[ '_mnm_filter' ], self::get_product_taxonomies() ) ) {
			$product->update_meta_data( '_mnm_filter', sanitize_text_field( $_POST[ '_mnm_filter' ] ) );
		} else {
			$product->delete_meta_data( '_mnm_filter' );
		}
	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Maybe use the plugin's template version
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public static function add_filter_navigation( $product ) {

		if ( $product->is_type( 'mix-and-match' ) && ( $taxonomy = $product->get_meta( '_mnm_filter', true ) ) ) {

				if ( apply_filters( 'wc_mnm_filter_display_inline_styles', true, $product ) ) {

				?>
					<style type="text/css">
						.mnm_filter_button_group .mnm_filters {
							margin-bottom: 2em;
							padding: 0;
							list-style: none;
							overflow: hidden;
							width: 100%;
							display: flex;
							flex-wrap: wrap;
							justify-content: space-between;
							gap: .5em;
							gap: .5em 1em; /* row-gap column gap */
							row-gap: .5em;
							column-gap: 1em;
						}
						.mnm_filter_button_group .mnm_filters button {
							margin-left: 0 !important;
							margin-right: 0 !important;
							width: 5rem;
							flex-grow: 1;
							flex-shrink: 0;
							flex-basis: calc(100% / 3 - 1rem);
							padding: .5em;
							box-sizing: border-box;
							line-height: 1.2;
						}
						.mnm_filter_button_group .mnm_filters button.selected {
							background: black;
							color: white;
						}
						
						@media only screen and (min-width: 380px) {
							/* full-width layout adjustment */
							.summary-add-to-cart-form-mnm .mnm_filter_button_group .mnm_filters button {
								flex-basis: calc(100% / 6 - 1rem);
							}
						}
					</style>
					
				<?php

				}

				// Load the navigation template.
				wc_get_template(
					'single-product/mnm/options-filter.php',
					array(
						'container' => $product,
						'layout'    => $product->get_layout(),
						'taxonomy'	=> $taxonomy,
						'terms'     => get_terms( $taxonomy, array( 'orderby' => 'name' ) )
					),
					'',
					self::plugin_path() . '/templates/'
				);

		}
	}


	/**
	 * Add the woocommerce_post_class filter
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public static function add_product_class_filter( $product ) {
		$taxonomy = $product->get_meta( '_mnm_filter', true );

		if ( $taxonomy ) {
			self::$taxonomy = $taxonomy;
			add_filter( 'woocommerce_post_class', array( __CLASS__, 'term_classes' ), 10, 3 );
		}
	}

	/**
	 * Remove the woocommerce_post_class filter
	 *
	 * @param  WC_Product_Mix_and_Match  $product
	 */
	public static function remove_product_class_filter( $product ) {
		remove_filter( 'woocommerce_post_class', array( __CLASS__, 'term_classes' ), 10, 3 );
		self::$taxonomy = '';
	}

	/**
	 * Add attributes to the children's woocommerce_post_class
	 *
	 * @param array      $classes Array of CSS classes.
	 * @param WC_Product $product Product object.
	 * @return array
	 */
	public static function term_classes( $classes, $product ) {
		
		if ( self::$taxonomy ) {

			$new_classes = array();

			// Include attributes and any extra taxonomies only if enabled via the hook - this is a performance issue.
			if ( false === apply_filters( 'woocommerce_get_product_class_include_taxonomies', false ) ) {
				$taxonomies = self::get_product_taxonomies();
				$type       = 'variation' === $product->get_type() ? 'product_variation' : 'product';
	
				if ( is_object_in_taxonomy( $type, self::$taxonomy ) && ! in_array( self::$taxonomy, array( 'product_cat', 'product_tag' ), true ) ) {
					$new_classes = wc_get_product_taxonomy_class( (array) get_the_terms( $product->get_id(), self::$taxonomy ), self::$taxonomy );
				}
			}

			// If variation.
			if ( $product->get_parent_id() > 0 ) {

				$attributes = $product->get_attributes();

				// If it's a variation's attribute then use that.
				if ( array_key_exists( self::$taxonomy, $attributes ) ) {
					$new_classes = array( self::$taxonomy . '-'. strtolower( $product->get_attribute( self::$taxonomy ) ) );
				} else {
					// Else inherit the parent's terms.
					$new_classes = wc_get_product_taxonomy_class( (array) get_the_terms( $product->get_parent_id(), self::$taxonomy ), self::$taxonomy );
				}
			} 

			if ( ! empty( $new_classes ) ) {
				$classes = array_merge( $classes, $new_classes );
			}

		}

		return $classes;
	}


	/**
	 * Add some props to the form class for script data.
	 * 
	 * @since 2.1.0
	 *
	 * @param  array $attributes
	 * @param  obj WC_Product_Mix_and_Match $this
	 * @return array
	 */
	public static function container_data_attributes( $attributes, $product ) {
		$attributes['filter-columns']      = apply_filters( 'woocommerce_mnm_grid_layout_columns', 3, $product );
		$attributes['filter-multi_terms' ] = apply_filters( 'wc_mnm_filters_support_multi_term_filtering', false, $product );
		return $attributes;

	}
	
	
	/*-----------------------------------------------------------------------------------*/
	/* Scripts and Styles */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Register scripts
	 *
	 * @return void
	 */
	public static function register_scripts() {

		$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';

		$script_path    = 'assets/js/frontend/wc-mnm-filter' . $suffix . '.js';
		$script_url     = plugins_url( $script_path, __FILE__ );
		$script_version = WC_Mix_and_Match()->get_file_version( plugin_dir_path( __FILE__ ) . $script_path, self::$version );

		wp_register_script( 'wc-mnm-filter', $script_url, array( 'wc-add-to-cart-mnm' ), $script_version, true );

		$l10n = array( 
			'i18n_no_matches' => esc_html__( 'No matching products were found', 'wc-mnm-filter' ),
		);
		
		wp_localize_script( 'wc-mnm-filter', 'WC_MNM_FILTER_PARAMS', $l10n );
	}


	/**
	 * Load the script anywhere the MNN add to cart button is displayed
	 * @return void
	 */
	public static function load_scripts() {
		wp_enqueue_script( 'wc-mnm-filter' );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Core Compat */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Declare Features compatibility.
	 *
	 */
	public static function declare_features_compatibility() {

		if ( ! class_exists( 'Automattic\WooCommerce\Utilities\FeaturesUtil' ) ) {
			return;
		}

		// HPOS (Custom Order tables) compatibility.
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', plugin_basename( __FILE__ ), true );

		// Cart and Checkout Blocks.
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'cart_checkout_blocks', plugin_basename( __FILE__ ), true );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Helper Functions                                                                  */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Plugin URL.
	 *
	 * @return string
	 */
	public static function plugin_url() {
		return plugins_url( basename( plugin_dir_path( __FILE__ ) ), basename(__FILE__) );
	}

	/**
	 * Plugin path.
	 *
	 * @return string
	 */
	public static function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Fetch and stash the taxonomies for products.
	 *
	 * @param int $post_id
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function get_product_taxonomies() {

		if ( empty( self::$product_taxonomies ) ) {

			$args = array(
				'object_type' => array( 'product' ),
			);

			// Product Addons support.
			if ( class_exists( 'WC_Product_Addons' ) ) {
				$args['object_type'][] = 'global_product_addon';
			}
			
			$taxonomies = get_taxonomies( $args, 'object' );

			unset( $taxonomies['product_type'] );

			$taxonomies = wp_list_pluck( $taxonomies, 'label', 'name' );

			self::$product_taxonomies = array_merge( array( '' => __( 'No filter', 'wc-mnm-filter' ) ), $taxonomies );

		}

		return self::$product_taxonomies;

	}

}
WC_MNM_Filter::init();
