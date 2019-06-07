<?php
/**
 * Gestion API.
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
 * API Action Class.
 */
class API_Action {

	/**
	 * Constructeur
	 *
	 * @since 2.0.0
	 */
	public function __construct() {
		add_action( 'rest_api_init', array( $this, 'callback_rest_api_init' ) );
	}

	/**
	 * Ajoutes la route pour PayPal.
	 *
	 * @since 2.0.0
	 */
	public function callback_rest_api_init() {
		register_rest_route( 'wpshop/v2', '/wps_gateway_paypal', array(
			'methods'  => array( 'GET', 'POST' ),
			'callback' => array( $this, 'callback_wps_gateway_paypal' ),
		) );

		register_rest_route( 'wpshop/v2', '/wps_gateway_stripe', array(
			'methods'  => array( 'GET', 'POST' ),
			'callback' => array( $this, 'callback_wps_gateway_stripe' ),
		) );
	}

	/**
	 * Gestion de la route Paypal.
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Request $request L'objet contenant les informations de la
	 * requête.
	 */
	public function callback_wps_gateway_paypal( $request ) {
		$data = $request->get_body_params();

		// translators: Paypal Gateway data: {json_data}.
		\eoxia\LOG_Util::log( sprintf( 'Paypal Gateway data: %s', json_encode( $data ) ), 'wpshop2' );

		$txn_id = get_post_meta( $data['custom'], 'payment_txn_id', true );

		if ( $txn_id !== $data['txn_id'] ) {
			update_post_meta( $data['custom'], 'payment_data', $data );
			update_post_meta( $data['custom'], 'payment_txn_id', $data['txn_id'] );
			update_post_meta( $data['custom'], 'payment_method', 'paypal' );

			do_action( 'wps_gateway_paypal', $data );
		}
	}

	/**
	 * Gestion de la route Stripe.
	 *
	 * @since 2.0.0
	 *
	 * @param  WP_Request $request L'objet contenant les informations de la
	 * requête.
	 */
	public function callback_wps_gateway_stripe( $request ) {
		$param = json_decode( $request->get_body(), true );

		// translators: Stripe Gateway data: {json_data}.
		\eoxia\LOG_Util::log( sprintf( 'Stripe Gateway data: %s', json_encode( $param ) ), 'wpshop2' );

		$order = Doli_Order::g()->get( array(
			'meta_key'     => '_external_data',
			'meta_compare' => 'LIKE',
			'meta_value'   => $param['data']['object']['id'],
		), true );

		if ( ! empty( $order ) ) {
			update_post_meta( $order->data['id'], 'payment_data', $param );
			update_post_meta( $order->data['id'], 'payment_txn_id', $param['data']['object']['id'] );
			update_post_meta( $order->data['id'], 'payment_method', 'stripe' );

			$param['custom'] = $order->data['id'];

			do_action( 'wps_gateway_stripe', $param );
		}
	}
}

new API_Action();
