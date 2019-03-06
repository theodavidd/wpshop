<?php
/**
 * Gestion des actions du panier.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2018 Eoxia <dev@eoxia.com>.
 *
 * @license   AGPLv3 <https://spdx.org/licenses/AGPL-3.0-or-later.html>
 *
 * @package   WPshop\Classes
 *
 * @since     2.0.0
 */

namespace wpshop;

defined( 'ABSPATH' ) || exit;

/**
 * Action of cart module.
 */
class Cart_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'init', array( Cart_Shortcode::g(), 'callback_init' ), 5 );

		// add_action( 'wps_after_cart_table', array( $this, 'callback_after_cart_table' ), 10 );
		add_action( 'wps_calculate_totals', array( $this, 'callback_calculate_totals' ) );

		add_action( 'wp_ajax_nopriv_add_to_cart', array( $this, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_add_to_cart', array( $this, 'ajax_add_to_cart' ) );

		add_action( 'wp_ajax_nopriv_delete_product_from_cart', array( $this, 'ajax_delete_product_from_cart' ) );
		add_action( 'wp_ajax_delete_product_from_cart', array( $this, 'ajax_delete_product_from_cart' ) );
	}

	public function callback_after_cart_table() {
		$total_price = Class_Cart_Session::g()->total_price_ttc;
		include( Template_Util::get_template_part( 'cart', 'cart-totals' ) );
	}

	public function callback_calculate_totals() {
		$price = 0;

		if ( ! empty( Class_Cart_Session::g()->cart_contents ) ) {
			foreach ( Class_Cart_Session::g()->cart_contents as $key => $line ) {
				$price += $line['price_ttc'] * $line['qty'];
			}
		}

		Class_Cart_Session::g()->total_price_ttc = $price;
	}

	public function ajax_add_to_cart() {
		check_ajax_referer( 'add_to_cart');

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$product = Product_Class::g()->get( array( 'id' => $id ), true );

		Cart_Class::g()->add_to_cart( $product );

		ob_start();
		include( Template_Util::get_template_part( 'cart', 'link-cart' ) );
		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'cart',
			'callback_success' => 'addedToCart',
			'view'             => ob_get_clean(),
		) );
	}

	public function ajax_delete_product_from_cart() {
		$key = isset( $_POST['key'] ) ? (int) $_POST['key'] : -1;

		if ( -1 != $key ) {
			Cart_Class::g()->delete_product( $key );
		}

		ob_start();
		$cart_contents = Class_Cart_Session::g()->cart_contents;

		if ( ! empty( $cart_contents ) ) {
			include( Template_Util::get_template_part( 'cart', 'cart' ) );
		} else {
			include( Template_Util::get_template_part( 'cart', 'empty-cart' ) );
		}

		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'cart',
			'callback_success' => 'deletedProdutFromCart',
			'view'             => ob_get_clean(),
		) );
	}
}

new Cart_Action();
