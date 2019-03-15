<?php
/**
 * Gestion API.
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
		$data   = $request->get_body_params();
		$txn_id = get_post_meta( $data['custom'], 'payment_txn_id', true );

		if ( $txn_id !== $data['txn_id'] ) {
			update_post_meta( $data['custom'], 'payment_data', $data );
			update_post_meta( $data['custom'], 'payment_txn_id', $data['txn_id'] );
			update_post_meta( $data['custom'], 'payment_method', 'paypal' );

			do_action( 'wps_gateway_paypal', $data );
		}
	}
}

new API_Action();
