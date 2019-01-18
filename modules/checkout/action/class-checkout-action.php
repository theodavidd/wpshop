<?php
/**
 * Gestion des actions du tunnel de vente.
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
* Handle order
*/
class Checkout_Action {

	/**
	 * Constructeur pour la classe Class_Checkout_Action. Ajoutes les
	 * actions pour le tunnel de vente.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'init', array( Checkout_Shortcode::g(), 'callback_init' ) );

		add_action( 'wps_after_cart_table', array( $this, 'callback_after_cart_table' ) );

		add_action( 'wps_checkout_billing', array( $this, 'callback_checkout_billing' ) );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_order_review' ) );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_payment' ), 20 );

		add_action( 'wp_ajax_wps_place_order', array( $this, 'callback_place_order' ) );
	}

	public function callback_after_cart_table() {
		include( Template_Util::get_template_part( 'checkout', 'proceed-to-checkout-button' ) );
	}

	public function callback_checkout_billing() {
		include( Template_Util::get_template_part( 'checkout', 'form-billing' ) );
	}

	public function callback_checkout_order_review() {
		$cart = Cart_Class::g()->get_cart();

		include( Template_Util::get_template_part( 'checkout', 'review-order' ) );
	}

	public function callback_checkout_payment() {
		$cart = Cart_Class::g()->get_cart();

		include( Template_Util::get_template_part( 'checkout', 'payment' ) );
	}

	public function callback_place_order() {
		do_action( 'wps_before_checkout_process' );

		do_action( 'wps_checkout_process' );

		$errors      = new \WP_Error();
		$posted_data = Checkout_Class::g()->get_posted_data();

		echo '<pre>'; print_r( $_SESSION ); echo '</pre>';exit;

		Checkout_Class::g()->validate_checkout( $posted_data, $errors );

		if ( 0 === count( $errors->error_data ) ) {
			$third_party = Third_Party_Class::g()->save( $posted_data );
			$contact     = Contact_Class::g()->get( array( 'id' => end( $third_party->data['contact_ids'] ) ), true );

			Order_Class::g()->save( $third_party, $contact );

			do_action( 'wps_checkout_order_processed' );
		} else {
			ob_start();
			include( Template_Util::get_template_part( 'checkout', 'notice-error' ) );
			$template = ob_get_clean();


			wp_send_json_success( array(
				'namespace'        => 'wpshopFrontend',
				'module'           => 'checkout',
				'callback_success' => 'checkoutErrors',
				'errors'           => $errors,
				'template'         => $template,
			) );
		}

	}
}

new Checkout_Action();
