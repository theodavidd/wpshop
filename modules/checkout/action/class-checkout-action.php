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

		add_action( 'wps_after_cart_table', array( $this, 'callback_after_cart_table' ), 20 );

		add_action( 'wps_checkout_shipping', array( $this, 'callback_checkout_shipping' ) );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_order_review' ) );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_payment' ), 20 );

		add_action( 'wp_ajax_wps_place_order', array( $this, 'callback_place_order' ) );
		add_action( 'wp_ajax_nopriv_wps_place_order', array( $this, 'callback_place_order' ) );
	}

	public function callback_after_cart_table() {
		$link_checkout = Pages_Class::g()->get_checkout_link();
		include( Template_Util::get_template_part( 'checkout', 'proceed-to-checkout-button' ) );
	}

	public function callback_checkout_shipping() {
		$current_user = wp_get_current_user();

		$third_party = Third_Party_Class::g()->get( array( 'schema' => true ), true );
		$contact = Third_Party_Class::g()->get( array( 'schema' => true ), true );

		if ( $current_user->ID != 0 ) {
			$third_party = Third_Party_Class::g()->get( array(
				'meta_key'   => 'email',
				'meta_value' => $current_user->user_email,
			), true );

			$contact = Contact_Class::g()->get( array(
				'search' => $current_user->user_email,
				'number' => 1,
			), true );
		}

		include( Template_Util::get_template_part( 'checkout', 'form-shipping' ) );
	}

	public function callback_checkout_order_review() {
		$cart_contents = Class_Cart_Session::g()->cart_contents;

		include( Template_Util::get_template_part( 'checkout', 'review-order' ) );
	}

	public function callback_checkout_payment() {
		$payment_methods = get_option( 'wps_payment_methods' );

		include( Template_Util::get_template_part( 'checkout', 'payment' ) );
	}

	public function callback_place_order() {
		do_action( 'wps_before_checkout_process' );

		do_action( 'wps_checkout_process' );

		$errors      = new \WP_Error();
		$posted_data = Checkout_Class::g()->get_posted_data();

		Checkout_Class::g()->validate_checkout( $posted_data, $errors );

		if ( 0 === count( $errors->error_data ) ) {
			if ( ! is_user_logged_in() ) {
				$data = Third_Party_Class::g()->save( $posted_data );

				$signon_data = array(
					'user_login'    => $data['contact']->data['login'],
					'user_password' => $data['contact']->data['password'],
				);

				// wp_signon( $signon_data, is_ssl() );
			} else {
				$current_user = wp_get_current_user();

				$third_party = Third_Party_Class::g()->get( array(
					'meta_key'   => 'email',
					'meta_value' => $current_user->user_email,
				), true );

				$contact = Contact_Class::g()->get( array(
					'search' => $current_user->user_email,
					'number' => 1,
				), true );

				$posted_data['third_party']['id']          = $third_party->data['id'];
				$posted_data['third_party']['external_id'] = $third_party->data['external_id'];

				$posted_data['contact']['id']          = $contact->data['id'];
				$posted_data['contact']['external_id'] = $contact->data['id'];
				$data = Third_Party_Class::g()->save( $posted_data );
			}

			$proposal = Proposals_Class::g()->save( $data['third_party'], $data['contact'] );
			$order    = apply_filters( 'wps_checkout_create_order', $proposal );
			Checkout_Class::g()->process_order_payment( $order );

			// do_action( 'wps_checkout_order_processed' );
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
