<?php
/**
 * Gestion des actions PayPal.
 *
 * @author    Eoxia <dev@eoxia.com>
 * @copyright (c) 2011-2019 Eoxia <dev@eoxia.com>.
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
 * PayPal Action Class.
 */
class PayPal_Action {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_setting_payment_method_paypal', array( $this, 'callback_setting_payment_method' ), 10, 0 );
		add_action( 'admin_post_wps_update_method_payment_paypal', array( $this, 'update_method_payment_paypal' ) );

		add_action( 'wps_gateway_paypal', array( $this, 'callback_wps_gateway_paypal' ) );
		add_action( 'wps_valid_paypal_standard_ipn_request', array( $this, 'callback_wps_valid_paypal_standard_ipn_request' ) );
	}

	/**
	 * Ajoutes la page pour configurer le paiement PayPal.
	 *
	 * @since 2.0.0
	 */
	public function callback_setting_payment_method() {
		$paypal_options = Payment::g()->get_payment_option( 'paypal' );
		\eoxia\View_Util::exec( 'wpshop', 'paypal', 'form-setting', array(
			'paypal_options' => $paypal_options,
		) );
	}

	/**
	 * Enregistres les configurations de PayPal en base de donnée.
	 *
	 * @since 2.0.0
	 */
	public function update_method_payment_paypal() {
		check_admin_referer( 'update_method_payment_paypal' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$title              = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description        = ! empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$paypal_email       = ! empty( $_POST['paypal_email'] ) ? sanitize_text_field( $_POST['paypal_email'] ) : '';
		$use_paypal_sandbox = ( isset( $_POST['use_paypal_sandbox'] ) && 'on' === $_POST['use_paypal_sandbox'] ) ? true : false;

		$payment_methods_option = get_option( 'wps_payment_methods', Payment::g()->default_options );

		$payment_methods_option['paypal']['title']              = $title;
		$payment_methods_option['paypal']['description']        = $description;
		$payment_methods_option['paypal']['paypal_email']       = $paypal_email;
		$payment_methods_option['paypal']['use_paypal_sandbox'] = $use_paypal_sandbox;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Your settings have been saved.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=paypal' ) );
	}

	/**
	 * Vérifie les données IPN reçu par PayPal.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $data Les données reçu par PayPal.
	 */
	public function callback_wps_gateway_paypal( $data ) {
		if ( ! empty( $data ) && $this->validate_ipn( $data ) ) { // WPCS: CSRF ok.
			$posted = wp_unslash( $data );
			do_action( 'wps_valid_paypal_standard_ipn_request', $posted );
		} else {
			wp_die( 'No IPN' );
		}
	}

	/**
	 * Appel la méthode selon le status du paiement.
	 *
	 * @since 2.0.0
	 *
	 * @param array $posted Les données reçu par PayPal vérifié.
	 */
	public function callback_wps_valid_paypal_standard_ipn_request( $posted ) {
		if ( method_exists( $this, 'payment_status_' . strtolower( $posted['payment_status'] ) ) ) {
			call_user_func( array( $this, 'payment_status_' . strtolower( $posted['payment_status'] ) ), $posted );
		}
	}

	/**
	 * Valides les données IPN.
	 *
	 * @since 2.0.0
	 *
	 * @param  array $data Les données reçu par PayPal.
	 * @return boolean     True si OK, sinon false.
	 */
	public function validate_ipn( $data ) {
		$paypal_options = Payment::g()->get_payment_option( 'paypal' );

		$validate_ipn        = wp_unslash( $data );
		$validate_ipn['cmd'] = '_notify-validate';

		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
		);

		$response = wp_safe_remote_post( $paypal_options['use_paypal_sandbox'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( $response['body'], 'VERIFIED' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Paiement OK
	 *
	 * @since 2.0.0
	 *
	 * @param  array $posted Les données reçu par PayPal vérifié.
	 */
	private function payment_status_completed( $posted ) {
		do_action( 'wps_payment_complete', $posted );
	}

	/**
	 * Paiement pas OK.
	 *
	 * @since 2.0.0
	 *
	 * @param array $posted Les données reçu par Paypal vérifié.
	 */
	private function payment_status_failed( $posted ) {
		do_action( 'wps_payment_failed', $posted );
	}
}

new Paypal_Action();
