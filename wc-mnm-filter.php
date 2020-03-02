<?php
/*
* Plugin Name: WooCommerce Mix and Match: Isotope Filter
* Plugin URI: https://woocommerce.com/products/woocommerce-mix-and-match-products/
* Description: Dynmamic Category Filtering for WooCommerce Mix and Match Products.
* Version: 1.0.0.beta.1
* Author: Kathy Darling
* Author URI: http://kathyisawesome.com/
*
* Text Domain: wc-mnm-filter
* Domain Path: /languages/
*
* Requires at least: 4.9
* Tested up to: 5.1
*
* WC requires at least: 3.4
* WC tested up to: 3.4.5
*
* Copyright: © 2019 Kathy Darling
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
	public static $version = '1.0.0.beta.1';

	/**
	 * Min required MNM version.
	 *
	 * @var string
	 */
	public static $req_mnm_version = '1.9.0-beta';


	/**
	 * Fire in the hole!
	 */
	public static function init() {
		add_action( 'plugins_loaded', array( __CLASS__, 'load_plugin' ) );
	}

	/**
	 * Hooks.
	 */
	public static function load_plugin() {

		// Check dependencies.
		if ( ! function_exists( 'WC_Mix_and_Match' ) || version_compare( WC_Mix_and_Match()->version, self::$req_mnm_version ) < 0 ) {
			add_action( 'admin_notices', array( __CLASS__, 'version_notice' ) );
			return false;
		}

		/*
		 * Admin.
		 */

		// Load translation files.
		add_action( 'init', array( __CLASS__, 'load_plugin_textdomain' ) );

		// Add extra meta.
		add_action( 'woocommerce_mnm_product_options', array( __CLASS__, 'additional_container_option') , 7, 2 );
		add_action( 'woocommerce_admin_process_product_object', array( __CLASS__, 'process_meta' ), 20 );

		// Switch the quantity input.
		add_action( 'woocommerce_before_add_to_cart_form', array( __CLASS__, 'add_filter_navigation' ), 20 );

		// Register Scripts.
		add_action( 'wp_enqueue_scripts', array( __CLASS__, 'register_scripts' ) );

		// Display Scripts.
		add_action( 'woocommerce_mix-and-match_add_to_cart', array( __CLASS__, 'load_scripts' ) );

		// QuickView support.
		add_action( 'wc_quick_view_enqueue_scripts', array( __CLASS__, 'load_scripts' ) );

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
		woocommerce_wp_checkbox( array(
			'id'            => '_mnm_filter',
			'label'       => __( 'Add isotope filtering to container options', 'wc-mnm-filter' )
		) );
	}

	/**
	 * Saves the new meta field.
	 *
	 * @param  WC_Product_Mix_and_Match  $mnm_product_object
	 */
	public static function process_meta( $product ) {
		if( isset( $_POST[ '_mnm_filter' ] ) ) {
			$product->update_meta_data( '_mnm_filter', 'yes' );
		} else {
			$product->update_meta_data( '_mnm_filter', 'no' );
		}
	}


	/*-----------------------------------------------------------------------------------*/
	/* Front End Display */
	/*-----------------------------------------------------------------------------------*/

	/**
	 * Maybe use the plugin's template version
	 */
	public static function add_filter_navigation() {
		global $product;
		if( $product->is_type( 'mix-and-match' ) && 'yes' == $product->get_meta( '_mnm_filter', true, 'edit' ) ) {

				?>
				<style type="text/css">
					.mnm-filter-button-group {
						margin-bottom: 2em;
					} 
					.mnm-filter-button-group button {
						margin: 0 .5em .5em 0;
						padding: .25em .5em;
						background: gray;
						}

					.mnm-filter-button-group button.selected {
						background: black;
					}
				</style>
				
				<?php
				// Load the navigation template.
				wc_get_template(
					'single-product/mnm/options-filter.php',
					array(
						'container'	      => $product,
						'layout'  		  => $product->get_layout()
					),
					'',
					self::plugin_path() . '/templates/'
				);
		}
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
		wp_register_script( 'wc-mnm-filter', plugins_url( 'assets/js/wc-mnm-filter.js', __FILE__ ), array( 'wc-add-to-cart-mnm' ), self::$version, true );
	}


	/**
	 * Load the script anywhere the MNN add to cart button is displayed
	 * @return void
	 */
	public static function load_scripts(){
		global $product;
		
		wp_enqueue_script( 'wc-mnm-filter' );
		
		$l10n = array( 'columns' => apply_filters( 'woocommerce_mnm_grid_layout_columns', 3, $product ) );
		
		wp_localize_script( 'wc-mnm-filter', 'WC_MNM_FILTER_PARAMS', $l10n );
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

}
WC_MNM_Filter::init();
