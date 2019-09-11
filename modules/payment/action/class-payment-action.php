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
}

new Payment_Action();
