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
		add_action( 'init', array( Cart_Shortcode::g(), 'callback_init' ) );

		add_action( 'wp_ajax_nopriv_add_to_cart', array( $this, 'ajax_add_to_cart' ) );
		add_action( 'wp_ajax_add_to_cart', array( $this, 'ajax_add_to_cart' ) );
	}

	public function ajax_add_to_cart() {
		check_ajax_referer( 'add_to_cart');

		$id = ! empty( $_POST['id'] ) ? (int) $_POST['id'] : 0;

		if ( empty( $id ) ) {
			wp_send_json_error();
		}

		$product = Product_Class::g()->get( array( 'id' => $id ), true );

		Cart_Class::g()->add_to_cart( $product );

		wp_send_json_success();
	}
}

new Cart_Action();
