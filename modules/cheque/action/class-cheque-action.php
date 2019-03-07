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
class Cheque_Action {
	public function __construct() {
		add_action( 'wps_setting_payment_method_cheque', array( $this, 'callback_setting_payment_method' ), 10, 0 );
		add_action( 'admin_post_wps_update_method_payment_cheque', array( $this, 'update_method_payment_cheque' ) );
	}

	public function callback_setting_payment_method() {
		$cheque_options = Payment_Class::g()->get_payment_option( 'cheque' );

		\eoxia\View_Util::exec( 'wpshop', 'cheque', 'form-setting', array(
			'cheque_options' => $cheque_options,
		) );
	}

	public function update_method_payment_cheque() {
		if ( ! current_user_can( 'edit_themes' ) ) {
			wp_die();
		}

		$title       = ! empty( $_POST['title'] ) ? sanitize_text_field( $_POST['title'] ) : '';
		$description = ! empty( $_POST['description'] ) ? $_POST['description'] : '';

		$payment_methods_option = get_option( 'wps_payment_methods', Payment_Class::g()->default_options );

		$payment_methods_option['cheque']['title']       = $title;
		$payment_methods_option['cheque']['description'] = $description;

		update_option( 'wps_payment_methods', $payment_methods_option );

		set_transient( 'updated_wpshop_option_' . get_current_user_id(), __( 'Vos réglages ont été enregistrés.', 'wpshop' ), 30 );

		wp_redirect( admin_url( 'admin.php?page=wps-settings&tab=payment_method&section=cheque' ) );
	}
}

new Cheque_Action();
