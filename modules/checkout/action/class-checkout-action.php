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
 * Checkout Action Class.
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

		add_action( 'wps_checkout_shipping', array( $this, 'callback_checkout_shipping' ), 10, 3 );
		add_action( 'wps_checkout_order_review', array( $this, 'callback_checkout_order_review' ), 10, 1 );
		add_action( 'wps_checkout_after_order_review', array( $this, 'callback_checkout_payment' ) );

		add_action( 'wp_ajax_load_edit_billing_address', array( $this, 'callback_load_edit_billing_address' ) );

		add_action( 'wp_ajax_wps_checkout_create_third_party', array( $this, 'callback_checkout_create_third' ) );
		add_action( 'wp_ajax_nopriv_wps_checkout_create_third_party', array( $this, 'callback_checkout_create_third' ) );

		add_action( 'wps_review_order_after_submit', array( $this, 'add_devis_button' ) );
		add_action( 'wps_review_order_after_submit', array( $this, 'add_place_order_button' ) );

		add_action( 'wp_ajax_wps_place_order', array( $this, 'callback_place_order' ) );
		add_action( 'wp_ajax_nopriv_wps_place_order', array( $this, 'callback_place_order' ) );
	}

	/**
	 * Ajoutes le bouton "Passer à la commande".
	 *
	 * @since 2.0.0
	 */
	public function callback_after_cart_table() {
		$link_checkout = Pages::g()->get_checkout_link();
		include( Template_Util::get_template_part( 'checkout', 'proceed-to-checkout-button' ) );
	}

	/**
	 * Affiches le formulaire pour l'adresse de livraison
	 *
	 * @since 2.0.0
	 *
	 * @param Third_Party_Model $third_party Les données du tier.
	 * @param Contact_Model     $contact     Les données du contact.
	 * @param Boolean           $force_edit  True pour forcer l'affichage la
	 * vue d'édition ou false.
	 */
	public function callback_checkout_shipping( $third_party, $contact, $force_edit = false ) {
		if ( null !== $contact->data['id'] && ! $force_edit ) {
			include( Template_Util::get_template_part( 'checkout', 'form-shipping' ) );
		} else {
			include( Template_Util::get_template_part( 'checkout', 'form-shipping-edit' ) );
		}
	}

	/**
	 * Le tableau récapitulatif de la commande
	 *
	 * @since 2.0.0
	 *
	 * @param  Proposal_Model $proposal Les données du devis.
	 */
	public function callback_checkout_order_review( $proposal ) {
		$cart_contents = Cart_Session::g()->cart_contents;

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

	/**
	 * Affiches les méthodes de paiement
	 *
	 * @since 2.0.0
	 */
	public function callback_checkout_payment() {
		$payment_methods = get_option( 'wps_payment_methods', Payment::g()->default_options );

		include( Template_Util::get_template_part( 'checkout', 'payment' ) );
	}

	/**
	 * Charges le template pour éditer l'adresse de livraison.
	 *
	 * @since 2.0.0
	 */
	public function callback_load_edit_billing_address() {
		check_ajax_referer( 'load_edit_billing_address' );

		$current_user = wp_get_current_user();

		if ( 0 !== $current_user->ID ) {
			$contact = Contact::g()->get( array(
				'search' => $current_user->user_email,
				'number' => 1,
			), true );

			$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );
		} else {
			wp_send_json_error();
		}

		ob_start();
		do_action( 'wps_checkout_shipping', $third_party, $contact, true );
		wp_send_json_success( array(
			'namespace'        => 'wpshopFrontend',
			'module'           => 'checkout',
			'callback_success' => 'loadedEditBillingAddress',
			'view'             => ob_get_clean(),
		) );
	}

	/**
	 * Créer le tier lors du tunnel de vente
	 *
	 * @since 2.0.0
	 */
	public function callback_checkout_create_third() {
		check_ajax_referer( 'callback_checkout_create_third' );

		$errors      = new \WP_Error();
		$posted_data = Checkout::g()->get_posted_data();

		Checkout::g()->validate_checkout( $posted_data, $errors );

		if ( 0 === count( $errors->error_data ) ) {
			if ( empty( $posted_data['third_party']['title'] ) || empty( $posted_data['contact']['lastname'] ) ) {
				$exploded_email = explode( '@', $posted_data['contact']['email'] );

				if ( empty( $posted_data['third_party']['title'] ) ) {
					$posted_data['third_party']['title'] = $exploded_email[0];
				}

				if ( empty( $posted_data['contact']['lastname'] ) ) {
					$posted_data['contact']['lastname'] = $exploded_email[0];
				}
			}

			$country = get_from_id( $posted_data['third_party']['country_id'] );

			$posted_data['third_party']['country_id'] = (int) $posted_data['third_party']['country_id'];
			$posted_data['third_party']['country']    = $country['label'];
			$posted_data['third_party']['phone']      = $posted_data['contact']['phone'];

			if ( ! is_user_logged_in() ) {
				$third_party = Third_Party::g()->update( $posted_data['third_party'] );
				do_action( 'wps_checkout_create_third_party', $third_party );

				$posted_data['contact']['login']          = sanitize_user( current( explode( '@', $posted_data['contact']['email'] ) ), true );
				$posted_data['contact']['password']       = wp_generate_password();
				$posted_data['contact']['third_party_id'] = $third_party->data['id'];

				$contact = Contact::g()->update( $posted_data['contact'] );

				$third_party->data['contact_ids'][] = $contact->data['id'];
				$thid_party                         = Third_Party::g()->update( $third_party->data );

				do_action( 'wps_checkout_create_contact', $contact );

				$signon_data = array(
					'user_login'    => $posted_data['contact']['login'],
					'user_password' => $posted_data['contact']['password'],
				);

				$user = wp_signon( $signon_data, is_ssl() );

				$key = get_password_reset_key( $user );

				$trackcode = get_user_meta( $contact->data['id'], 'p_user_registration_code', true );
				$track_url = get_option( 'siteurl' ) . '/wp-login.php?action=rp&key=' . $key . '&login=' . $posted_data['contact']['login'];

				Emails::g()->send_mail( $posted_data['contact']['email'], 'wps_email_customer_new_account', array_merge( $posted_data, array( 'url' => $track_url ) ) );
			} else {
				$current_user = wp_get_current_user();

				$contact = Contact::g()->get( array(
					'search' => $current_user->user_email,
					'number' => 1,
				), true );

				$third_party = Third_Party::g()->get( array( 'id' => $contact->data['third_party_id'] ), true );

				$posted_data['third_party']['id'] = $third_party->data['id'];

				$third_party = Third_Party::g()->update( $posted_data['third_party'] );
			}

			$type = ! empty( $_POST['type'] ) ? sanitize_text_field( $_POST['type'] ) : 'proposal';

			$proposal = Proposals::g()->get( array( 'schema' => true ), true );

			$last_ref = Proposals::g()->get_last_ref();
			$last_ref = empty( $last_ref ) ? 1 : $last_ref;
			$last_ref++;

			$proposal->data['title']     = 'PR' . sprintf( '%06d', $last_ref );
			$proposal->data['ref']       = sprintf( '%06d', $last_ref );
			$proposal->data['datec']     = current_time( 'mysql' );
			$proposal->data['parent_id'] = $third_party->data['id'];
			$proposal->data['author_id'] = $contact->data['id'];
			$proposal->data['status']    = 'publish';
			$proposal->data['lines']     = array();

			$total_ht  = 0;
			$total_ttc = 0;

			if ( ! empty( Cart_Session::g()->cart_contents ) ) {
				foreach ( Cart_Session::g()->cart_contents as $content ) {
					$proposal->data['lines'][] = $content;

					$total_ht  += $content['price'];
					$total_ttc += $content['price_ttc'];
				}
			}

			$proposal->data['total_ht']  = $total_ht;
			$proposal->data['total_ttc'] = $total_ttc;

			$proposal = Proposals::g()->update( $proposal->data );
			do_action( 'wps_checkout_create_proposal', $proposal );

			Cart_Session::g()->add_external_data( 'proposal_id', $proposal->data['id'] );
			Cart_Session::g()->update_session();

			wp_send_json_success( array(
				'namespace'        => 'wpshopFrontend',
				'module'           => 'checkout',
				'callback_success' => 'createdThirdSuccess',
				'redirect_url'     => Pages::g()->get_checkout_link() . '?step=2',
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

	public function add_devis_button() {
		include( Template_Util::get_template_part( 'checkout', 'devis-button' ) );

	}

	public function add_place_order_button() {
		if ( Settings::g()->dolibarr_is_active() ) {
			include( Template_Util::get_template_part( 'checkout', 'place-order-button' ) );
		}
	}

	/**
	 * Créer la commande et passe au paiement
	 *
	 * @since 2.0.0
	 */
	public function callback_place_order() {
		check_ajax_referer( 'callback_place_order' );

		do_action( 'wps_before_checkout_process' );

		do_action( 'wps_checkout_process' );

		$proposal                         = Proposals::g()->get( array( 'id' => Cart_Session::g()->external_data['proposal_id'] ), true );
		$proposal->data['payment_method'] = $_POST['type_payment'];

		$proposal = Proposals::g()->update( $proposal->data );

		do_action( 'wps_checkout_update_proposal', $proposal );

		if ( 'order' == $_POST['type'] ) {
			$order = apply_filters( 'wps_checkout_create_order', $proposal );
			Checkout::g()->process_order_payment( $order );
		} else {
			Cart_Session::g()->destroy();
			wp_send_json_success( array(
				'namespace'        => 'wpshopFrontend',
				'module'           => 'checkout',
				'callback_success' => 'redirect',
				'url'              => Pages::g()->get_valid_proposal_link() . '?proposal_id=' . $proposal->data['id'],
			) );
		}
	}
}

new Checkout_Action();
