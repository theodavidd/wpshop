<?php
/**
 * Gestion des actions des commandes.
 *
 * Ajoutes une page "Orders" dans le menu de WordPress.
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
 * Action of Order module.
 */
class Doli_Payment_Action {

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'wps_synchro_invoice', array( $this, 'synchro_payment' ), 10, 2 );
	}

	public function synchro_payment( $wp_invoice, $doli_invoice ) {
		$doli_payments = Request_Util::get( 'invoices/' . $doli_invoice->id . '/payments' );
		if ( ! empty( $doli_payments ) ) {
			foreach ( $doli_payments as $doli_payment ) {
				$payment = Doli_Payment::g()->get( array(
					'post_title' => $doli_payment->ref,
				), true );

				if ( empty( $payment ) ) {
					$payment = Doli_Payment::g()->get( array( 'schema' => true ), true );
				}

				$payment->data['post_parent']  = (int) $wp_invoice['id'];
				$payment->data['title']        = $doli_payment->ref;
				$payment->data['status']       = 'publish';
				$payment->data['payment_type'] = $doli_payment->type;
				$payment->data['amount']       = $doli_payment->amount;
				$payment->data['date']         = $doli_payment->date;

				Doli_Payment::g()->update( $payment->data );
			}
		}
	}
}

new Doli_Payment_Action();
