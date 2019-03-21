<?php
/**
 * Gestion de Stripe.
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
 * Stripe Class.
 */
class Stripe extends \eoxia\Singleton_Util {
	/**
	 * Constructeur.
	 *
	 * @since 2.0.0
	 */
	protected function construct() {}

	/**
	 * Prépares la paiement stripe.
	 *
	 * @since 2.0.0
	 *
	 * @param Order $order Les données de la commande.
	 *
	 * @return array       L'ID de la session Stripe.
	 */
	public function process_payment( $order ) {
		$stripe_options = Payment::g()->get_payment_option( 'stripe' );

		\Stripe\Stripe::setApiKey( $stripe_options['secret_key'] );
		\Stripe\Stripe::setApiVersion( '2019-03-14; checkout_sessions_beta=v1' );

		$lines = array();

		if ( ! empty( $order->data['lines'] ) ) {
			foreach ( $order->data['lines'] as $line ) {
				$lines[] = array(
					'amount'   => (int) $line['price_ttc'] * 100,
					'quantity' => $line['qty'],
					'name'     => $line['libelle'],
					'currency' => 'eur',
				);
			}
		}

		$session = \Stripe\Checkout\Session::create( array(
			'success_url'          => Pages::g()->get_valid_checkout_link() . '?order_id=' . $order->data['id'],
			'cancel_url'           => site_url(),
			'payment_method_types' => array( 'card' ),
			'line_items'           => array( $lines ),
		) );

		$order->data['external_data']['stripe_session_id'] = $session->id;

		Doli_Order::g()->update( $order->data );

		return array(
			'id' => $session->id,
		);
	}
}

Stripe::g();
