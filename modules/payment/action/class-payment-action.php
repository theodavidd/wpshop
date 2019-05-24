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
 * Payment Action Class.
 */
class Payment_Action {

	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_setting_payment_method_payment_in_shop', array( $this, 'callback_setting_payment_in_shop_method' ), 10, 0 );
		add_action( 'wps_setting_payment_method_cheque', array( $this, 'callback_setting_cheque_method' ), 10, 0 );
		add_action( 'admin_post_wps_update_method_payment_payment_in_shop', array( $this, 'update_method_payment_in_shop' ) );
		add_action( 'admin_post_wps_update_method_payment_cheque', array( $this, 'update_method_cheque' ) );
	}

	/**
	 * Affiches la page pour configurer la méthode de paiement
	 * "Payer en boutique".
	 *
	 * @since 2.0.0
	 */
	public function callback_setting_payment_in_shop_method() {
		$payment_data = Payment::g()->get_payment_option( 'payment_in_shop' );
		$action       = 'wps_update_method_payment_payment_in_shop';
		$nonce        = 'update_method_payment_in_shop';

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'payment-method-single-form', array(
			'payment_data' => $payment_data,
			'action'       => $action,
			'nonce'        => $nonce,
		) );
	}

	/**
	 * Affiches la page pour configurer la méthode de paiement
	 * "Chèque"".
	 *
	 * @since 2.0.0
	 */
	public function callback_setting_cheque_method() {
		$payment_data = Payment::g()->get_payment_option( 'cheque' );
		$action       = 'wps_update_method_payment_cheque';
		$nonce        = 'update_method_cheque';

		\eoxia\View_Util::exec( 'wpshop', 'settings', 'payment-method-single-form', array(
			'payment_data' => $payment_data,
			'action'       => $action,
			'nonce'        => $nonce,
		) );
	}

	/**
	 * Met à jour les données pour la méthode de paiement "Payer en boutique".
	 *
	 * @todo: Doublon ???
	 *
	 * @since 2.0.0
	 */
	public function update_method_payment_in_shop() {
		check_admin_referer( 'update_method_payment_in_shop' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$title       = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description = ! empty( $_POST['description'] ) ? stripslashes( $_POST['description'] ) : '';

		$payment_methods_option = get_option( 'wps_payment_methods', Payment::g()->default_options );

		$payment_methods_option['payment_in_shop']['title']       = $title;
		$payment_methods_option['payment_in_shop']['description'] = $description;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Your settings have been saved.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=payment_in_shop' ) );
	}

	/**
	 * Met à jour les données pour la méthode de paiement "Cheque".
	 *
	 * @todo: Doublon ???
	 *
	 * @since 2.0.0
	 */
	public function update_method_cheque() {
		check_admin_referer( 'update_method_cheque' );

		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die();
		}

		$title       = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description = ! empty( $_POST['description'] ) ? stripslashes( $_POST['description'] ) : '';

		$payment_methods_option = get_option( 'wps_payment_methods', Payment::g()->default_options );

		$payment_methods_option['cheque']['title']       = $title;
		$payment_methods_option['cheque']['description'] = $description;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Your settings have been saved.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=cheque' ) );
	}
}

new Payment_Action();
