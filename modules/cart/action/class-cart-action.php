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
 * Cart Action Class.
 */
class Cart_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {

		add_action( 'init', array( Cart_Shortcode::g(), 'callback_init' ), 5 );

		add_action( 'wps_calculate_totals', array( $this, 'callback_calculate_totals' ) );

		add_action( 'wp_ajax_nopriv_add_to_cart', array( $this, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_add_to_cart', array( $this, 'ajax_add_to_cart' ) );

		add_action( 'wp_ajax_nopriv_wps_update_cart', array( $this, 'ajax_update_cart' ) );
		add_action( 'wp_ajax_wps_update_cart', array( $this, 'ajax_update_cart' ) );

		add_action( 'wp_ajax_nopriv_delete_product_from_cart', array( $this, 'ajax_delete_product_from_cart' ) );
		add_action( 'wp_ajax_delete_product_from_cart', array( $this, 'ajax_delete_product_from_cart' ) );
	}

	/**
	 * Ajoutes le total du panier.
	 *
	 * @since 2.0.0
	 *
	 * @todo doublon ?
	 * @return void
	 */
	public function callback_after_cart_table() {
		$total_price = Cart_Session::g()->total_price_ttc;
		include( Template_Util::get_template_part( 'cart', 'cart-totals' ) );
	}

	/**
	 * Calcul le total du panier.
	 *
	 * @since 2.0.0
	 */
	public function callback_calculate_totals() {
		$shipping_cost_option = get_option( 'wps_shipping_cost', Settings::g()->shipping_cost_default_settings );

		$price             = 0;
		$price_no_shipping = 0;
		$price_ttc         = 0;

		if ( ! empty( Cart_Session::g()->cart_contents ) ) {
			foreach ( Cart_Session::g()->cart_contents as $key => $line ) {
				$price     += $line['price'] * $line['qty'];
				$price_ttc += $line['price_ttc'] * $line['qty'];

				if ( $shipping_cost_option['shipping_product_id'] != $line['id'] ) {
					$price_no_shipping += $line['price'] * $line['qty'];
				}
			}
		}

		Cart_Session::g()->total_price             = $price;
		Cart_Session::g()->total_price_no_shipping = $price_no_shipping;
		Cart_Session::g()->total_price_ttc         = $price_ttc;
	}

	/**
	 * Action pour ajouter un produit dans le panier.
	 *
	 * @since 2.0.0
	 */
	public function ajax_add_to_cart() {
		check_ajax_referer( 'add_to_cart' );

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$product = Product::g()->get( array( 'id' => $id ), true );

		Cart::g()->add_to_cart( $product );

		ob_start();
		include( Template_Util::get_template_part( 'cart', 'link-cart' ) );
		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'cart',
			'callback_success' => 'addedToCart',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Action pour mêttre à jour le panier.
	 *
	 * @todo: Sécurisé les données du tableau produit
	 *
	 * @since 2.0.0
	 */
	public function ajax_update_cart() {
		check_ajax_referer( 'ajax_update_cart' );

		$products = ! empty( $_POST['products'] ) ? (array) $_POST['products'] : array();

		if ( empty( $products ) ) {
			wp_send_json_error();
		}

		if ( ! empty( $products ) ) {
			foreach ( $products as $key => $product ) {
				if ( isset ( $product['qty'] ) ) {
				$product['qty'] = (int) $product['qty'];
					if ( $product['qty'] <= 0 ) {
						Cart::g()->delete_product( $key );
					} else {
						Cart::g()->update_cart( $product );
					}
				}
			}
		}

		ob_start();
		echo do_shortcode( '[wps_cart]' );
		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'cart',
			'callback_success' => 'updatedCart',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Action pour supprimer un produit du panier.
	 *
	 * @since 2.0.0
	 */
	public function ajax_delete_product_from_cart() {
		check_ajax_referer( 'ajax_delete_product_from_cart' );

		$key = isset( $_POST['key'] ) ? (int) $_POST['key'] : -1;

		if ( -1 != $key ) {
			Cart::g()->delete_product( $key );
		}

		ob_start();
		echo do_shortcode( '[wps_cart]' );
		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'cart',
			'callback_success' => 'deletedProdutFromCart',
			'view'             => ob_get_clean(),
		) );
	}
}

new Cart_Action();
