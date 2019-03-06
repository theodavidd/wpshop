<?php
/**
 * Gestion des actions PayPal.
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
* Gestion de PayPal.
*/
class Paypal_Action {
	public function __construct() {
		add_action( 'wps_setting_payment_method_paypal', array( $this, 'callback_setting_payment_method' ), 10, 0 );
		add_action( 'admin_post_wps_update_method_payment_paypal', array( $this, 'update_method_payment_paypal' ) );

		add_action( 'wps_gateway_paypal', array( $this, 'callback_wps_gateway_paypal' ) );
		add_action( 'wps_valid_paypal_standard_ipn_request', array( $this, 'callback_wps_valid_paypal_standard_ipn_request' ) );
	}

	public function callback_setting_payment_method() {
		$paypal_options = Payment_Class::g()->get_payment_option( 'paypal' );
		\eoxia\View_Util::exec( 'wpshop', 'paypal', 'form-setting', array(
			'paypal_options' => $paypal_options,
		) );
	}

	public function update_method_payment_paypal() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_die();
		}

		$title              = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description        = ! empty( $_POST['description'] ) ? sanitize_text_field( $_POST['description'] ) : '';
		$paypal_email       = ! empty( $_POST['paypal_email'] ) ? sanitize_text_field( $_POST['paypal_email'] ) : '';
		$use_paypal_sandbox = ( isset( $_POST['use_paypal_sandbox'] ) && 'on' == $_POST['use_paypal_sandbox'] ) ? true : false;

		$payment_methods_option = get_option( 'wps_payment_methods', Payment_Class::g()->default_options );

		$payment_methods_option['paypal']['title']              = $title;
		$payment_methods_option['paypal']['description']        = $description;
		$payment_methods_option['paypal']['paypal_email']       = $paypal_email;
		$payment_methods_option['paypal']['use_paypal_sandbox'] = $use_paypal_sandbox;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Vos réglages ont été enregistrés.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=paypal' ) );
	}

	public function callback_wps_gateway_paypal( $data ) {
		if ( ! empty( $data ) && $this->validate_ipn( $data ) ) { // WPCS: CSRF ok.
			$posted = wp_unslash( $data );
			do_action( 'wps_valid_paypal_standard_ipn_request', $posted );
		} else {
			wp_die( 'No IPN' );
		}
	}

	public function callback_wps_valid_paypal_standard_ipn_request( $posted ) {
		$order = ! empty( $posted['custom'] ) ? Order_Class::g()->get( array( 'id' => (int) $posted['custom'] ), true ) : null;

		if ( $order ) {
			if ( method_exists( $this, 'payment_status_' . $posted['payment_status'] ) ) {
				call_user_func( array( $this, 'payment_status_' . $posted['payment_status'] ), $posted );
			}
		}
	}

	public function validate_ipn( $data ) {
		$validate_ipn        = wp_unslash( $data );
		$validate_ipn['cmd'] = '_notify-validate';

		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
		);

		$response = wp_safe_remote_post( 'https://www.sandbox.paypal.com/cgi-bin/webscr', $params );

		if ( ! is_wp_error( $response ) && $response['response']['code'] >= 200 && $response['response']['code'] < 300 && strstr( $response['body'], 'VERIFIED' ) ) {
			return true;
		}

		return false;
	}

	private function payment_status_completed( $posted ) {
		do_action( 'wps_payment_complete', $posted );
	}
}

new Paypal_Action();
