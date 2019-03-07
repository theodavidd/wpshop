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

		add_action( 'wps_checkout_shipping', array( $this, 'callback_checkout_shipping' ), 10, 2 );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_order_review' ), 10, 1 );
		add_action( 'wps_checkout_after_order_review', array( $this, 'callback_checkout_payment' ) );

		add_action( 'wp_ajax_wps_checkout_create_third_party', array( $this, 'callback_checkout_create_third' ) );
		add_action( 'wp_ajax_nopriv_wps_checkout_create_third_party', array( $this, 'callback_checkout_create_third' ) );

		add_action( 'wp_ajax_wps_place_order', array( $this, 'callback_place_order' ) );
		add_action( 'wp_ajax_nopriv_wps_place_order', array( $this, 'callback_place_order' ) );
	}

	public function callback_after_cart_table() {
		$link_checkout = Pages_Class::g()->get_checkout_link();
		include( Template_Util::get_template_part( 'checkout', 'proceed-to-checkout-button' ) );
	}

	public function callback_checkout_shipping( $third_party, $contact ) {

		include( Template_Util::get_template_part( 'checkout', 'form-shipping' ) );
	}

	public function callback_checkout_order_review( $proposal ) {
		$cart_contents = Class_Cart_Session::g()->cart_contents;

		$tva_lines = array();

		if ( ! empty( $proposal->data['lines'] ) ) {
			foreach ( $proposal->data['lines'] as $line ) {

				if ( empty( $tva_lines[ $line['tva_tx'] ] ) ) {
					$tva_lines[ $line['tva_tx'] ] = 0;
				}

				$tva_lines[ $line['tva_tx'] ] += $line['total_tva'];
			}
		}

		include( Template_Util::get_template_part( 'checkout', 'review-order' ) );
	}

	public function callback_checkout_payment() {
		$payment_methods = get_option( 'wps_payment_methods', Payment_Class::g()->default_options );

		include( Template_Util::get_template_part( 'checkout', 'payment' ) );
	}

	public function callback_checkout_create_third() {
		$errors      = new \WP_Error();
		$posted_data = Checkout_Class::g()->get_posted_data();

		Checkout_Class::g()->validate_checkout( $posted_data, $errors );

		if ( 0 === count( $errors->error_data ) ) {
			if ( empty( $posted_data['third_party']['title'] ) || empty( $posted_data['contact']['lastname'] ) ) {
				$exploded_email = explode( '@', $posted_data['contact']['email'] );

				if ( empty( $posted_data['third_party']['title'] ) ) {
					$posted_data['third_party']['title'] = $exploded_email[0];
				}

				if ( empty( $posted_data['contact']['lastname'] ) ) {
					$posted_data['contact']['lastname']  = $exploded_email[0];
				}
			}

			$posted_data['third_party']['country_id'] = (int) $posted_data['third_party']['country_id'];
			$posted_data['third_party']['country']    = get_from_code( $posted_data['third_party']['country_id'] );

			if ( ! is_user_logged_in() ) {
				$third_party = Third_Party_Class::g()->update( $posted_data['third_party'] );

				do_action( 'wps_checkout_create_third_party', $third_party );

				$posted_data['contact']['login']          = sanitize_user( current( explode( '@', $posted_data['contact']['email'] ) ), true );
				$posted_data['contact']['password']       = wp_generate_password();
				$posted_data['contact']['third_party_id'] = $third_party->data['id'];

				$contact = Contact_Class::g()->update( $posted_data['contact'] );

				$third_party->data['contact_ids'][] = $contact->data['id'];
				$thid_party = Third_Party_Class::g()->update( $third_party->data );

				do_action( 'wps_checkout_create_contact', $contact );

				$signon_data = array(
					'user_login'    => $posted_data['contact']['login'],
					'user_password' => $posted_data['contact']['password'],
				);

				wp_signon( $signon_data, is_ssl() );
			} else {
				$current_user = wp_get_current_user();

				$contact = Contact_Class::g()->get( array(
					'search' => $current_user->user_email,
					'number' => 1,
				), true );

				$third_party = Third_Party_Class::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );

				$posted_data['third_party']['id'] = $third_party->data['id'];

				$third_party = Third_Party_Class::g()->update( $posted_data['third_party'] );
			}

			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'proposal';

			$proposal = Proposals_Class::g()->get( array( 'schema' => true ), true );

			$last_ref = Proposals_Class::g()->get_last_ref();
			$last_ref = empty( $last_ref ) ? 1 : $last_ref;
			$last_ref++;

			$proposal->data['title']          = 'PR' . sprintf( "%06d", $last_ref );
			$proposal->data['ref']            = sprintf( "%06d", $last_ref );
			$proposal->data['datec']          = current_time( 'mysql' );
			$proposal->data['parent_id']      = $third_party->data['id'];
			$proposal->data['status']         = 'publish';
			$proposal->data['lines']          = array();

			$total_ht  = 0;
			$total_ttc = 0;

			if ( ! empty( Class_Cart_Session::g()->cart_contents ) ) {
				foreach ( Class_Cart_Session::g()->cart_contents as $content ) {
					$proposal->data['lines'][] = $content;

					$total_ht  += $content['price'];
					$total_ttc += $content['price_ttc'];
				}
			}

			$proposal->data['total_ht']  = $total_ht;
			$proposal->data['total_ttc'] = $total_ttc;

			$proposal = Proposals_Class::g()->update( $proposal->data );
			do_action( 'wps_checkout_create_proposal', $proposal );


			Class_Cart_Session::g()->add_external_data( 'proposal_id', $proposal->data['id'] );
			Class_Cart_Session::g()->update_session();

			wp_send_json_success( array(
				'namespace'        => 'wpshopFrontend',
				'module'           => 'checkout',
				'callback_success' => 'createdThirdSuccess',
				'redirect_url'     => Pages_Class::g()->get_checkout_link() . '?step=2',
			) );
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

	public function callback_place_order() {
		do_action( 'wps_before_checkout_process' );

		do_action( 'wps_checkout_process' );

		$proposal = Proposals_Class::g()->get( array( 'id' => Class_Cart_Session::g()->external_data['proposal_id'] ), true );
		$proposal->data['payment_method'] = $_POST['type_payment'];
		$proposal = Proposals_Class::g()->update( $proposal->data );

		do_action( 'wps_checkout_update_proposal', $proposal );

		if ( 'order' == $_POST['type'] ) {
			$order = apply_filters( 'wps_checkout_create_order', $proposal );
			Checkout_Class::g()->process_order_payment( $order );
		} else {
			Class_Cart_Session::g()->destroy();
			wp_send_json_success( array(
				'namespace'        => 'wpshopFrontend',
				'module'           => 'checkout',
				'callback_success' => 'redirect',
				'url'              => Pages_Class::g()->get_valid_proposal_link() . '?proposal_id=' . $proposal->data['id'],
			) );
		}
	}
}

new Checkout_Action();
