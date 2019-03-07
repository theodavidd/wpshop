<?php
/**
 * Gestion de PayPal.
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
class Paypal_Class extends \eoxia\Singleton_Util {
	protected $request_url;

	protected function construct() {}

	public function process_payment( $order ) {
		$paypal_options = Payment_Class::g()->get_payment_option( 'paypal' );

		$this->request_url = $paypal_options['use_paypal_sandbox'] ? 'https://www.sandbox.paypal.com/cgi-bin/webscr?test_ipn=1&' : 'https://www.paypal.com/cgi-bin/webscr?';
		$paypal_args       = $this->get_paypal_args( $order );

		return array(
			'url' => $this->request_url . http_build_query( $paypal_args, '', '&' ),
		);
	}

	protected function get_paypal_args( $order ) {
		$paypal_args = apply_filters( 'wps_paypal_args', array_merge(
			$this->get_transaction_args( $order ),
			$this->get_line_item_args( $order )
		), $order );

		return $paypal_args;
	}

	protected function get_transaction_args( $order ) {
		$payment_methods_option = get_option( 'wps_payment_methods', array(
			'paypal' => array(),
			'cheque' => array(),
		) );

		$third_party = Third_Party_Class::g()->get( array( 'id' => $order->data['parent_id'] ), true );
		$contact     = Contact_Class::g()->get( array( 'id' => end( $third_party->data['contact_ids'] ) ), true );

		return array(
			'cmd'           => '_cart',
			'business'      => $payment_methods_option['paypal']['paypal_email'],
			'no_shipping'   => 0,
			'lc'            => 'fr_FR',
			'no_note'       => 0,
			'rm'            => 0,
			'currency_code' => 'EUR',
			'charset'       => 'utf-8',
			'upload'        => 1,
			'return'        => Pages_Class::g()->get_valid_checkout_link() . '?order_id=' . $order->data['id'],
			'notify_url'    => site_url( 'wp-json/wpshop/v2/wps_gateway_paypal' ),
			'cancel_return' => '',
			'invoice'       => $order->data['id'],
			'email'         => $contact->data['email'],
			'custom'        => $order->data['id'],
		);
	}

	protected function get_line_item_args( $order ) {
		$line_item_args = array();

		if ( ! empty( $order->data['lines'] ) ) {
			foreach ( $order->data['lines'] as $index => $line ) {
				$line_item_args['item_name_' . ( $index + 1 )] = $line['libelle'];
				$line_item_args['quantity_' . ( $index + 1 )] = $line['qty'];
				$line_item_args['amount_' . ( $index + 1 )] = number_format( $line['price_ttc'], 2 );
				$line_item_args['item_number_' . ( $index + 1 )] = $line['ref'];
			}
		}

		return $line_item_args;
	}


}

Paypal_Class::g();
